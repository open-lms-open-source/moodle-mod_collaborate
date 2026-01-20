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
 * Unit tests for rendering user_show_download_recordings.
 *
 * @package    mod_collaborate
 * @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate;
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/mod/collaborate/renderer.php');

/**
 * Unit tests for rendering user_show_download_recordings.
 *
 * @package    mod_collaborate
 * @author     Diego Monroy
 * @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recording_download_test extends \advanced_testcase {
    public function setUp(): void {
        $this->resetAfterTest();
    }

    public function test_show_recording_sessions() {
        global $DB, $PAGE;

        // Set config download recordings to true.
        set_config('candownloadrecordings', 1, 'collaborate');
        $candownloadrecordings = get_config('collaborate', 'candownloadrecordings');
        $this->assertEquals(1, $candownloadrecordings);

        $output = $PAGE->get_renderer('mod_collaborate');

        // Assert there are no records yet.
        $this->assertEquals(0, $DB->count_records('collaborate'));

        // Create course.
        $course = $this->getDataGenerator()->create_course();

        /** @var mod_collaborate_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_collaborate');
        $this->assertInstanceOf('mod_collaborate_generator', $generator);
        $this->assertEquals('collaborate', $generator->get_modulename());

        // Test general use and some defaults.
        $generator->create_instance(['course' => $course->id]);
        $collab = $generator->create_instance(['course' => $course->id]);
        $cm = get_coursemodule_from_instance('collaborate', $collab->id);
        $this->assertEquals($collab->id, $cm->instance);
        $this->assertEquals('collaborate', $cm->modname);
        $this->assertEquals($course->id, $cm->course);

        // Get context.
        $pagecontext = \context_module::instance($cm->id);

        // Show or hide download records.
        $showdownload = $output->user_show_download_recordings($candownloadrecordings, $pagecontext);

        // Assertion user can not download record.
        $this->assertFalse($showdownload);

        // Change to permit list role.
        $this->setAdminUser();

        // Show or hide download #2 records.
        $showdownload2 = $output->user_show_download_recordings($candownloadrecordings, $pagecontext);

        // Assertion user can download record.
        $this->assertTrue($showdownload2);
    }

    public function test_hide_recording_sessions() {
        global $DB, $PAGE;

        // Set config download recordings to false.
        set_config('candownloadrecordings', 0, 'collaborate');
        $candownloadrecordings = get_config('collaborate', 'candownloadrecordings');
        $this->assertEquals(0, $candownloadrecordings);

        $output = $PAGE->get_renderer('mod_collaborate');

        // Assert there are no records yet.
        $this->assertEquals(0, $DB->count_records('collaborate'));

        // Create course.
        $course = $this->getDataGenerator()->create_course();

        /** @var mod_collaborate_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_collaborate');
        $this->assertInstanceOf('mod_collaborate_generator', $generator);
        $this->assertEquals('collaborate', $generator->get_modulename());

        // Test general use and some defaults.
        $generator->create_instance(['course' => $course->id]);
        $collab = $generator->create_instance(['course' => $course->id]);
        $cm = get_coursemodule_from_instance('collaborate', $collab->id);
        $this->assertEquals($collab->id, $cm->instance);
        $this->assertEquals('collaborate', $cm->modname);
        $this->assertEquals($course->id, $cm->course);

        // Get context.
        $pagecontext = \context_module::instance($cm->id);

        // Show or hide download records.
        $showdownload = $output->user_show_download_recordings($candownloadrecordings, $pagecontext);

        // Assertion user can not download record.
        $this->assertFalse($showdownload);

        // Change to permit list role.
        $this->setAdminUser();

        // Show or hide download #2 records.
        $showdownload2 = $output->user_show_download_recordings($candownloadrecordings, $pagecontext);

        // Assertion user can not download record.
        $this->assertFalse($showdownload2);
    }
}
