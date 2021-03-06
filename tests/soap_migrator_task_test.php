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
use mod_collaborate\testables\sessionlink;

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

    public function test_execute_task_update_sessions_completed() {
        global $DB;

        $sessionids = $this->create_migration_data();
        // All sessions have been migrated.
        $DB->insert_records('collaborate_migration', $sessionids);

        $task = new soap_migrator_task();
        $task->execute();

        // Migration completed.
        $this->assertEmpty($DB->get_records('collaborate', ['sessionuid' => null]));
        $this->assertEmpty($DB->get_records('collaborate_sessionlink', ['sessionuid' => null]));
        $this->assertEquals(5, get_config('collaborate', 'migrationstatus'));
        $migrationdata = $DB->get_records_sql('SELECT sessionid, sessionuid FROM {collaborate_migration}');
        $updatedrecords = $DB->get_records_sql('SELECT * FROM {collaborate_sessionlink} WHERE sessionuid iS NOT NULL');
        $this->assertNotEmpty($migrationdata);

        $migrationdata = $this->find_migration_discrepancies();
        $this->assertEmpty($migrationdata);
    }

    public function test_execute_task_update_sessions_not_completed() {
        global $DB;

        $sessionids = $this->create_migration_data();
        // Not all sessions have been migrated.
        $session1 = array_shift($sessionids);
        $session2 = array_pop($sessionids);
        $DB->insert_records('collaborate_migration', $sessionids);

        $task = new soap_migrator_task();
        $task->execute();

        // Migration not completed.
        $this->assertNotEmpty($DB->get_records('collaborate', ['sessionuid' => null]));
        $this->assertNotEmpty($DB->get_records('collaborate_sessionlink', ['sessionuid' => null]));
        // Verify status as not completed.
        $this->assertEquals(6, get_config('collaborate', 'migrationstatus'));

        $migrationdata = $this->find_migration_discrepancies();
        $this->assertCount(2, $migrationdata);
        // We are missing 2 records, verify which ones by id.
        $this->assertArrayHasKey($session1->sessionid, $migrationdata);
        $this->assertArrayHasKey($session2->sessionid, $migrationdata);
    }

    public function create_migration_data () {
        global $DB;
        set_config('restserver', 'serverexample', 'collaborate');
        set_config('restkey', 'keyexample', 'collaborate');
        set_config('restsecret', 'secretexample', 'collaborate');
        set_config('migrationstatus', soap_migrator_task::STATUS_COLLECTED, 'collaborate');

        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $group1 = $gen->create_group(array('courseid' => $course->id, 'name' => 'group1'));
        $group2 = $gen->create_group(array('courseid' => $course->id, 'name' => 'group2'));

        $modgen = $gen->get_plugin_generator('mod_collaborate');
        $collabdata = (object) [
            'course'    => $course->id,
            'groupmode' => SEPARATEGROUPS
        ];
        $collaborate = $modgen->create_instance($collabdata);
        $linkscreated = sessionlink::apply_session_links($collaborate);
        $this->assertTrue($linkscreated);
        $collaborate = $DB->get_record('collaborate', ['id' => $collaborate->id]);
        $this->assertNotEmpty($collaborate->sessionid);
        $sessionlink = $DB->get_record('collaborate_sessionlink',
            ['collaborateid' => $collaborate->id, 'groupid' => null]
        );
        $this->assertEquals($collaborate->sessionid, $sessionlink->sessionid);
        $gplink1 = sessionlink::get_group_session_link($collaborate, $group1->id);
        $this->assertNotNull($gplink1);
        $gplink2 = sessionlink::get_group_session_link($collaborate, $group2->id);
        $this->assertNotNull($gplink2);

        $this->assertCount(1, $DB->get_records('collaborate'));
        // Sessionlink will hold an additional record for the main activity.
        $this->assertCount(3, $DB->get_records('collaborate_sessionlink'));
        $collabdata = (object) [
            'course'    => $course->id,
        ];
        for ($i = 1; $i <= 9; $i++) {
            $collaborate = $modgen->create_instance($collabdata);
            $this->assertTrue(sessionlink::apply_session_links($collaborate));
        }
        $this->assertCount(10, $DB->get_records('collaborate'));
        // 3 records for the first activity and then 9 more.
        $this->assertCount(12, $DB->get_records('collaborate_sessionlink'));

        // Get all sessionids and create a new fake sessionuid.
        return $DB->get_records_sql('
            SELECT sessionid, CONCAT(sessionid, "uid") AS sessionuid
              FROM {collaborate_sessionlink}');
    }

    public function find_migration_discrepancies() {
        global $DB;
        $migrationdata = $DB->get_records_sql('SELECT sessionid, sessionuid FROM {collaborate_migration}');
        $updatedrecords = $DB->get_records_sql('SELECT sessionid, sessionuid FROM {collaborate_sessionlink}');
        $this->assertNotEmpty($migrationdata);
        // Verify data has been properly handled. If there are missing records, return them.
        foreach ($migrationdata as $migratedrecord) {
            if ($migratedrecord->sessionuid == $updatedrecords[$migratedrecord->sessionid]->sessionuid) {
                unset($updatedrecords[$migratedrecord->sessionid]);
            }
        }
        return $updatedrecords;
    }
}