<?php
// This file is part of Moodle - http://moodle.org
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Ad-hoc task to migrate SOAP clients.
 *
 * @package    mod_collaborate
 * @copyright  Copyright (c) 2021 Open LMS.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\task;

defined('MOODLE_INTERNAL') || die();

use core\task\adhoc_task,
    mod_collaborate\local,
    mod_collaborate\rest\api as restapi;

class soap_migrator_task extends adhoc_task {

    const STATUS_IDLE = 1;
    const STATUS_LAUNCHED = 2;
    const STATUS_READY = 3; // Data ready to be collected.
    const STATUS_COLLECTED = 4; // Data was stored in our table.
    const STATUS_MIGRATED = 5; // Migration process finished.

    /**
     * Runs the task for migrating SOAP sessions.
     */
    public function execute() {
        // Create the config value that stores the status.
        $config = get_config('collaborate');
        if (!isset($config->migrationstatus)) { // We need to do it just once.
            set_config('migrationstatus', self::STATUS_IDLE, 'collaborate');
        }

        // Copy SOAP credentials into REST credentials.
        if (empty($config->restserver) && empty($config->restkey) && empty($config->restsecret)) {
            $this->set_rest_credentials($config->server, $config->username, $config->password);
        }

        // Launch Migration.
        $this->launch_soap_migration();

        // Request the status.

        // Fetch and store data.

        // Populate table.

        // Set new credentials given.
    }

    /**
     * Configures REST API with given credentials.
     * @return
     */
    private function set_rest_credentials($server, $username, $password) {
        if (!empty($server) && !empty($username) && !empty($password)) {
            set_config('restserver', $server, 'collaborate');
            set_config('restkey', $username, 'collaborate');
            set_config('restsecret', $password, 'collaborate');
            return;
        }
        // Should not happen but...
        throw new \coding_exception('Credentials must not be empty');
    }

    private function launch_soap_migration() {
        $current = get_config('collaborate', 'migrationstatus');

        if ($current == self::STATUS_IDLE) {
            $api = local::get_api(false, null);
            try {
                $api->launch_soap_migration();
                set_config('migrationstatus', self::STATUS_LAUNCHED, 'collaborate');
                $this->log_migration_entry('Migration launched successfully');
            } catch (\Exception $e) {
                throw new \coding_exception('Migration could not be launched');
            }
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
}