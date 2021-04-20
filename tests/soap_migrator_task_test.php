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
}