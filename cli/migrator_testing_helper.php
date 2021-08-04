<?php
// This file is part of Moodle - http://moodle.org/
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
 * Help QA to test some features related to SOAP migration.
 *
 * @package    mod_collaborate
 * @copyright  Copyright (c) 2021 OpenLMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

use mod_collaborate\local;
global $DB;

// CLI options.
list($options, $unrecognized) = cli_get_params(
    [
        'help'     => false,
        'test'     => false,
        'reset'    => false,
        'server'   => null,
        'key'      => null,
        'secret'   => null,
    ],
    [
        'h' => 'help',
        't' => 'test',
        'r' => 'reset'
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if (!empty($options['help'])) {
    echo "Testing helper for migration feature. Testing purpose only

Options:
-h, --help               Print out this help.
-t, --test               Test the service is reachable.
-r, --reset              Remove migration settings from the database.
--server=servervalue     Optional. Provide a server URL.
--key=keyvalue           Optional. Provide a key.
--secret=secretvalue     Optional. Provide the secret for the given key.

Example:
$ /usr/bin/php mod/collaborate/cli/migrator_testing_helper.php " . PHP_EOL;

    die;
}

if (empty($options['server'])) {
    $options['server'] = 'https://citc-olms.bbcollabcloud.com/collab/api/csa';
}

if (empty($options['key'])) {
    $options['key'] = 'OLMS-TEST-USER-1';
}

if (empty($options['secret'])) {
    $options['secret'] = 'password';
}

if (!empty($options['test'])) {
    echo '[INFO] Proceeding to test connectivity' . PHP_EOL;
    set_config('restserver', $options['server'], 'collaborate');
    set_config('restkey', $options['key'], 'collaborate');
    set_config('restsecret', $options['secret'], 'collaborate');
    set_config('migrationstatus', 1, 'collaborate');
    $api = local::get_api(false, null);
    $result = $api->set_migration_accesstoken(true);
    if (!empty($result)) {
        echo '[INFO] Service reachable. token generated: ' . $result->access_token . PHP_EOL;
        die;
    }
}
if (!empty($options['reset'])) {
    echo '[INFO] Proceeding to delete migrationstatus from plugin config' . PHP_EOL;
    $parameters = [
        'plugin' => 'collaborate',
        'name' => 'migrationstatus'
    ];
    $delrecord = $DB->get_record('config_plugins', $parameters);
    if (!empty($delrecord)) {
        $DB->delete_records('config_plugins', ['id' => $delrecord->id]);
        echo '[INFO] migrationstatus successfully deleted from plugin config' . PHP_EOL;
        die;
    }
    $parameterstwo = [
        'plugin' => 'collaborate',
        'name' => 'migrationoffset'
    ];
    $delrecordtwo = $DB->get_record('config_plugins', $parameterstwo);
    if (!empty($delrecordtwo)) {
        $DB->delete_records('config_plugins', ['id' => $delrecordtwo->id]);
        echo '[INFO] migrationoffset successfully deleted from plugin config' . PHP_EOL;
        die;
    }
}

echo '[INFO] Script execution finished without actions'. PHP_EOL;