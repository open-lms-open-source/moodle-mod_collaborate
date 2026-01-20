<?php
// This file is part of Moodle - https://moodle.org
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Ad-hoc task to migrate SOAP clients.
 *
 * @package    mod_collaborate
 * @copyright  Copyright (c) 2021 Open LMS.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\task;

use core\task\adhoc_task;
use mod_collaborate\local;
use mod_collaborate\exceptions\soap_migration_exception;

class soap_migrator_task extends adhoc_task {

    const STATUS_IDLE = 1;
    const STATUS_LAUNCHED = 2;
    const STATUS_READY = 3; // Data ready to be collected.
    const STATUS_COLLECTED = 4; // Data was stored in our table.
    const STATUS_MIGRATED = 5; // Migration process finished.
    const STATUS_INCOMPLETE = 6; // Migration data left sessions with no sessionuid.

    private $soapconfig = false;
    private $restconfig = false;

    /**
     * Runs the task for migrating SOAP sessions.
     */
    public function execute() {
        // Create the config value that stores the status.
        $config = get_config('collaborate');
        $this->soapconfig = !empty($config->server) && !empty($config->username) && !empty($config->password) ? (object) [
            'server'   => $config->server,
            'username' => $config->username,
            'password' => $config->password,
        ] : false;

        $this->restconfig = !empty($config->restserver) && !empty($config->restkey) && !empty($config->restsecret) ? (object) [
            'restserver'   => $config->restserver,
            'restkey' => $config->restkey,
            'restsecret' => $config->restsecret,
        ] : false;

        $testsoapcredentials = $this->soapconfig ? local::api_verified(true, $this->soapconfig) : false;
        $testrestcredentials = $this->restconfig ? local::api_verified(true, $this->restconfig) : false;

        if (!isset($config->migrationstatus)) { // We need to do it just once.
            // Copy SOAP credentials into REST credentials if REST credentials doesn't work.
            if ($testrestcredentials && !$testsoapcredentials) {
                // If both credentials works, it means is a migration on an existing REST user.
                $this->log_migration_entry('REST Credentials in use, migration not required.');
                return;
            }

            if (!$testrestcredentials) {
                $this->set_rest_credentials($config->server, $config->username, $config->password);
            }

            set_config('migrationstatus', self::STATUS_IDLE, 'collaborate');
            set_config('migrationoffset', 0, 'collaborate');
            set_config('migrationtimestamp', time(), 'collaborate');
        }

        // Launch Migration.
        $this->launch_soap_migration();

        // Request the status.
        $this->check_migration_status();

        // Fetch and store data.
        $this->fetch_migration_data();

        // Populate table.
        $this->update_sessions();
    }

    /**
     * Configures REST API with given credentials.
     * @return
     */
    private function set_rest_credentials($server, $username, $password) {
        if (!empty($server) && !empty($username) && !empty($password)) {
            $restserver = $this->resolve_rest_server($server);
            set_config('restserver', $restserver, 'collaborate');
            set_config('restkey', $username, 'collaborate');
            set_config('restsecret', $password, 'collaborate');
            return;
        }
        // Should not happen but...
        throw new soap_migration_exception('Credentials must not be empty');
    }

    /**
     * Resolves the REST server given SOAP server.
     * @param $soapserver string
     * @return string
     */
    private function resolve_rest_server($soapserver) {
        switch ($soapserver) {
            case 'https://sas.elluminate.com/site/external/adapter/default/v3/webservice.event':
            case 'https://ultra-us-prod-cusa.bbcollab.cloud/site/external/adapter/default/v3/webservice.event':
            case 'https://us-sas.bbcollab.com/site/external/adapter/default/v3/webservice.event':
                $restserver = 'https://us.bbcollab.com/collab/api/csa'; // US.
                break;
            case 'https://eu-sas.bbcollab.com/site/external/adapter/default/v3/webservice.event':
            case 'https://eu1.bbcollab.com/site/external/adapter/default/v3/webservice.event':
            case 'https://ultra-eu-prod-cusa.bbcollab.cloud/site/external/adapter/default/v3/webservice.event':
                $restserver = 'https://eu.bbcollab.com/collab/api/csa'; // EU.
                break;
            case 'https://ultra-au-prod-cusa.bbcollab.cloud/site/external/adapter/default/v3/webservice.event':
                $restserver = 'https://au.bbcollab.com/collab/api/csa'; // AU.
                break;
            case 'https://ultra-ca-prod-cusa.bbcollab.cloud/site/external/adapter/default/v3/webservice.event':
                $restserver = 'https://ca.bbcollab.com/collab/api/csa'; // CA.
                break;
            default:
                $restserver = 'https://citc-olms.bbcollabcloud.com/collab/api/csa'; // Default.
                break;
        }
        return $restserver;
    }

    private function launch_soap_migration() {
        $current = get_config('collaborate', 'migrationstatus');

        if ($current == self::STATUS_IDLE) {
            try {
                $api = local::get_api(false, null);
                $api->launch_soap_migration();
                set_config('migrationstatus', self::STATUS_LAUNCHED, 'collaborate');
                $this->log_migration_entry('Migration launched successfully');
            } catch (\Exception $e) {
                throw new soap_migration_exception('Migration could not be launched');
            }
        }
    }

    private function check_migration_status() {
        $current = get_config('collaborate', 'migrationstatus');
        if ($current == self::STATUS_LAUNCHED) {
            $api = local::get_api(false, null);
            $result = $api->check_soap_migration_status();
            if ($result == 'FINISHED') {
                set_config('migrationstatus', self::STATUS_READY, 'collaborate');
                $this->log_migration_entry('Migration data is ready to be collected');
            } else {
                throw new soap_migration_exception('Data is not ready yet, re-scheduling the task. Result: ' . $result);
            }
        }
    }

    private function fetch_migration_data() {
        $current = get_config('collaborate', 'migrationstatus');

        if ($current == self::STATUS_READY) {

            try {
                $api = local::get_api(false, null);
                $limit = 900;
                if (!empty($CFG->mod_collaborate_migration_data_limit) &&
                        is_numeric($CFG->mod_collaborate_migration_data_limit) &&
                            $CFG->mod_collaborate_migration_data_limit <= 1000) {
                    $limit = $CFG->mod_collaborate_migration_data_limit;
                }
                $offset = get_config('collaborate', 'migrationoffset');
                $this->log_migration_entry('Requesting data with offset: ' . $offset);
                $result = $api->collect_soap_migration_data($limit, $offset);
                if (!empty($result)) {
                    $this->log_migration_entry('Data received');
                    $this->handle_migration_records($result);
                    set_config('migrationoffset', $offset + $limit, 'collaborate');
                    throw new soap_migration_exception('Re-scheduling task on purpose');
                }
                set_config('migrationstatus', self::STATUS_COLLECTED, 'collaborate');
            } catch (\Exception $e) {
                $message = $e->getMessage();
                throw new soap_migration_exception('Data collection has not finished. Hint: ' . $message);
            }
        }
    }

    /**
     * Necessary because Moodle does not allow uppercase in table columns and the API responds with uppercase.
     */
    public function handle_migration_records($dataobjects) {
        global $DB;
        if (!is_array($dataobjects) && !($dataobjects instanceof Traversable)) {
            throw new \core\exception\coding_exception('records passed are non-traversable object');
        }

        foreach ($dataobjects as $dataobject) {
            if (!is_array($dataobject) && !is_object($dataobject)) {
                throw new \core\exception\coding_exception('record passed is invalid');
            }
            $dataobject->sessionid = $dataobject->sId;
            $dataobject->sessionuid = $dataobject->sUid;
            unset($dataobject->sId);
            unset($dataobject->sUid);
            $DB->insert_record('collaborate_migration', $dataobject, false);
        }
    }

    /**
     * Validates we're not running a Unit test when doing mtrace. Tests are marked risky when they print info.
     */
    private function log_migration_entry($message) {
        if (!local::duringtesting()) {
            mtrace($message);
        }
    }

    /**
     * Updates all references for SOAP sessionid records to use the new sessionuuid obtained from Collab
     */

    private function update_sessions() {
        global $DB;
        $current = get_config('collaborate', 'migrationstatus');

        if ($current == self::STATUS_COLLECTED) {
            try {
                $transaction = $DB->start_delegated_transaction();

                if ($DB->get_dbfamily() === 'mysql') {
                    $DB->execute('
                                UPDATE {collaborate_sessionlink} csl
                                  JOIN {collaborate_migration} cm ON csl.sessionid = cm.sessionid
                                   SET csl.sessionuid = cm.sessionuid
                                 WHERE csl.sessionuid IS NULL');

                    $DB->execute('
                                UPDATE {collaborate} c
                                  JOIN {collaborate_migration} cm ON c.sessionid = cm.sessionid
                                   SET c.sessionuid = cm.sessionuid
                                 WHERE c.sessionuid IS NULL');
                } else {
                    $DB->execute('
                                UPDATE {collaborate_sessionlink} csl
                                   SET sessionuid = cm.sessionuid
                                  FROM {collaborate_migration} cm
                                 WHERE csl.sessionuid IS NULL
                                   AND csl.sessionid = cm.sessionid');
                    $DB->execute('
                                UPDATE {collaborate} c
                                   SET sessionuid = cm.sessionuid
                                  FROM {collaborate_migration} cm
                                 WHERE c.sessionuid IS NULL
                                   AND c.sessionid = cm.sessionid');
                }

                $transaction->allow_commit();
                $this->log_migration_entry('Collaborate session records have been updated.');
            } catch (\core\exception\moodle_exception $e) {
                throw new soap_migration_exception("An error occurred while updating collaborate session records: "
                    . $e->getMessage());
            }

            $selection = 'sessionid IS NOT NULL AND sessionuid IS NULL';
            if ($DB->count_records_select('collaborate', $selection) ||
                $DB->count_records_select('collaborate_sessionlink', $selection)) {
                set_config('migrationstatus', self::STATUS_INCOMPLETE, 'collaborate');
            } else {
                set_config('migrationstatus', self::STATUS_MIGRATED, 'collaborate');
            }

            if (!local::api_verified(true, $this->restconfig)) {
                $newusername = get_config('collaborate', 'newrestkey');
                $newpassword = get_config('collaborate', 'newrestsecret');
                set_config('restkey', $newusername, 'collaborate');
                set_config('restsecret', $newpassword, 'collaborate');
            }
        }
    }
}
