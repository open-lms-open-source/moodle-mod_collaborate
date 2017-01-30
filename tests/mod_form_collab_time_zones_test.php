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
 * Tests for collab form.
 * @author    Jonathan Garcia Gomez <jonathan.garcia@blackboard.com>
 * @copyright Copyright (c) 2016 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/mod/collaborate/mod_form.php');

class mod_form_collab_time_zones_testcase extends advanced_testcase {

    public function test_get_validated_time_zones() {
        global $DB;
        $this->resetAfterTest(true);

        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);
        $this->setUser($teacher);

        $coretzones = core_date::get_list_of_timezones();
        $tzone = mod_collaborate_mod_form::get_validated_time_zone();
        $this->assertEquals(get_user_timezone(), $tzone);
        $teacher->timezone = 'Indian/Mayotte';
        $DB->update_record('user', $teacher);
        $this->setUser($teacher);
        $tzone = mod_collaborate_mod_form::get_validated_time_zone();
        $this->assertNotEquals($coretzones[core_date::get_server_timezone()], $tzone);
        $this->assertArrayHasKey($tzone, $coretzones);
        $this->assertEquals(get_user_timezone(), $tzone);
        $teacher->timezone = '';
        $DB->update_record('user', $teacher);
        $this->setUser($teacher);
        $tzone = mod_collaborate_mod_form::get_validated_time_zone();
        $this->assertEquals($coretzones[core_date::get_server_timezone()], $tzone);
        $this->assertArrayHasKey($tzone, $coretzones);
    }
}
