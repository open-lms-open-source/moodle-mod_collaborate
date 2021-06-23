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

use mod_collaborate\task\soap_migrator_task;

/**
 * Test SOAP migrator task.
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2021 Open LMS.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class soap_migrator_task_test extends advanced_testcase {
    protected function setUp() :void {
        $this->resetAfterTest();
    }

    public function test_execute_task_without_credentials() {
        global $DB;

        $task = new soap_migrator_task();
        $count = $DB->count_records('task_adhoc');
        $this->assertEquals(0, $count);
        $this->expectExceptionMessage('Credentials must not be empty');
        // It should fail after executing the task without credentials and the task should queue itself.
        $task->execute();
        $count = $DB->count_records('task_adhoc');
        $this->assertEquals(1, $count);
    }

    public function test_execute_task_launch_migration() {
        global $DB;
        set_config('restserver', 'serverexample', 'collaborate');
        set_config('restkey', 'keyexample', 'collaborate');
        set_config('restsecret', 'secretexample', 'collaborate');
        set_config('migrationstatus', soap_migrator_task::STATUS_IDLE, 'collaborate');

        $task = new soap_migrator_task();
        $count = $DB->count_records('task_adhoc');
        $this->assertEquals(0, $count);
        $this->expectExceptionMessage('Migration could not be launched');
        // Should fail because REST API has wrong credentials and the task should queue itself.
        $task->execute();
        $count = $DB->count_records('task_adhoc');
        $this->assertEquals(1, $count);
    }

    public function test_execute_task_check_migration_status() {
        global $DB;
        set_config('restserver', 'serverexample', 'collaborate');
        set_config('restkey', 'keyexample', 'collaborate');
        set_config('restsecret', 'secretexample', 'collaborate');
        set_config('migrationstatus', soap_migrator_task::STATUS_LAUNCHED, 'collaborate');

        $task = new soap_migrator_task();
        $count = $DB->count_records('task_adhoc');
        $this->assertEquals(0, $count);
        $this->expectExceptionMessage('Curl options should be defined');
        // Should fail because REST API has wrong credentials and the task should queue itself.
        $task->execute();
        $count = $DB->count_records('task_adhoc');
        $this->assertEquals(1, $count);
    }

    public function test_execute_task_fetch_migration_data() {
        global $DB;
        set_config('restserver', 'serverexample', 'collaborate');
        set_config('restkey', 'keyexample', 'collaborate');
        set_config('restsecret', 'secretexample', 'collaborate');
        set_config('migrationstatus', soap_migrator_task::STATUS_READY, 'collaborate');

        $task = new soap_migrator_task();
        $count = $DB->count_records('task_adhoc');
        $this->assertEquals(0, $count);
        $this->expectExceptionMessage('Data collection has not finished');
        // Should fail because REST API has wrong credentials and the task should queue itself.
        $task->execute();
        $count = $DB->count_records('task_adhoc');
        $this->assertEquals(1, $count);
    }

    public function test_handle_migration_records() {
        global $DB;
        $countrecords = $DB->count_records('collaborate_migration');
        $this->assertEquals(0, $countrecords);
        $dataarray = array();
        $migrationobj = new stdClass();
        $migrationobj->sId = 1010;
        $migrationobj->sUid = 'D969D7DA5DB9127BF592533D479DE59F';
        array_push($dataarray, $migrationobj);
        $migrationobjtwo = new stdClass();
        $migrationobjtwo->sId = 1011;
        $migrationobjtwo->sUid = 'B04AD515B4EDF360DB96B8441052D57A';
        array_push($dataarray, $migrationobjtwo);
        $migrationobjthree = new stdClass();
        $migrationobjthree->sId = 1012;
        $migrationobjthree->sUid = '33EDD3A4DE31FE9E961636B31D8562AB';
        array_push($dataarray, $migrationobjthree);
        $task = new soap_migrator_task();
        $task->handle_migration_records($dataarray);
        $countrecords = $DB->count_records('collaborate_migration');
        $this->assertEquals(3, $countrecords);
    }
}