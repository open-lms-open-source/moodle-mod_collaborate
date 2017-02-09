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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../db/upgradelib.php');

/**
 * Test for upgrade library.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class  mod_collaborate_upgradelib_testcase extends advanced_testcase {

    /**
     * Create sequential id starting from 1000 to keep it in it's own range.
     * @return int
     */
    private function make_collaborate_id() {
        static $id = 1000;
        $id ++;
        return ($id);
    }

    /**
     * Create sequential id starting from 2000 to keep it in it's own range.
     * @return int
     */
    private function make_recording_id() {
        static $id = 2000;
        $id ++;
        return ($id);
    }

    private function make_session_id() {
        static $id = 3000;
        $id++;
        return ($id);
    }

    /**
     * Note: This test is only going to be relevant for 2 versions (including this one) at the most.
     * It should be removed at some point as it will provide no benefit once customers have upgraded.
     * @throws coding_exception
     */
    public function test_migrate_recording_info_instanceid_to_sessionlink() {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        // Create courses.
        $courses = [];
        for ($c = 0; $c < 10; $c++) {
            $courses[$c] = $generator->create_course();
        }

        // Create collaborate instances.
        foreach ($courses as $course) {
            /** @var mod_collaborate_generator $collabgen */
            $collabgen = $generator->get_plugin_generator('mod_collaborate');
            $collabgen->create_instance((object)[
                'id'            => $this->make_collaborate_id(),
                'course'        => $course->id,
                'sessionid'     => $this->make_session_id(),
                'legacytesting' => true // Create the record without session link table.
            ]);
        }
        $collabs = $DB->get_records('collaborate');

        // Create recording view records.
        foreach ($collabs as $collab) {
            for ($r = 0; $r < 10; $r++) {
                $DB->insert_record('collaborate_recording_info', (object)[
                    'instanceid' => $collab->id,
                    'recordingid' => $this->make_recording_id(),
                    'action' => mod_collaborate\recording_counter::VIEW
                ]);
            }
        }

        // Delete first collab record - simulate customer deleting instance but recording data remaining.
        $deletedcollab = reset($collabs);
        $DB->delete_records('collaborate', ['id' => $deletedcollab->id]);
        $collabs = $DB->get_records('collaborate');
        $this->expectOutputRegex('/Instance does not exist - '.$deletedcollab->id.'/');

        // Run upgrade migration script.
        $upgradelib = new collaborate_update_manager();
        $upgradelib->migrate_recording_info_instanceid_to_sessionlink();

        // Assert upgrade script has populated session link tables and recording info table has correct sessionlinkid.
        $sessionlinks = $DB->get_records('collaborate_sessionlink');
        $this->assertCount(count($collabs), $sessionlinks);

        $recordingviews = $DB->get_records('collaborate_recording_info');
        foreach ($recordingviews as $recordingview) {
            $this->assertNotEmpty($sessionlinks[$recordingview->sessionlinkid]);
            $sessionlink = $sessionlinks[$recordingview->sessionlinkid];
            $collab = $collabs[$recordingview->instanceid];
            $this->assertEquals($collab->sessionid, $sessionlink->sessionid);
        }
    }
}
