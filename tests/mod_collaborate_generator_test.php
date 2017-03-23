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
 * Collab generator tests.
 *
 * @package    mod_collaborate
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
use mod_collaborate\local;

/**
 * Collab generator tests.
 *
 * @package    mod_collaborate
 * @copyright  Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_collaborate_generator_testcase extends advanced_testcase {
    public function test_generator() {
        global $DB;

        $this->resetAfterTest(true);

        $this->assertEquals(0, $DB->count_records('collaborate'));

        $course = $this->getDataGenerator()->create_course();

        /** @var mod_collaborate_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_collaborate');
        $this->assertInstanceOf('mod_collaborate_generator', $generator);
        $this->assertEquals('collaborate', $generator->get_modulename());

        // Test general use and some defaults.
        $generator->create_instance(['course' => $course->id]);
        $collab = $generator->create_instance(['course' => $course->id]);
        $this->assertEquals(2, $DB->count_records('collaborate'));
        $this->assertNotEmpty($collab->sessionid);
        $this->assertEquals(100, $collab->grade);
        $this->assertEquals('pr', $collab->guestrole);
        $this->assertEmpty($collab->guesturl);

        $cm = get_coursemodule_from_instance('collaborate', $collab->id);
        $this->assertEquals($collab->id, $cm->instance);
        $this->assertEquals('collaborate', $cm->modname);
        $this->assertEquals($course->id, $cm->course);

        // Test non-default timestart.
        $time = strtotime('Yesterday midnight');
        $collab = $generator->create_instance(['course' => $course->id, 'timestart' => $time]);
        $this->assertEquals($time, $collab->timestart);
        $this->assertEquals($time + HOURSECS, $collab->timeend);

        // Test non-default duration.
        $time = time();
        $collab = $generator->create_instance(['course' => $course->id, 'duration' => HOURSECS * 2, 'timestart' => $time]);
        $this->assertEquals($time + HOURSECS * 2, $collab->timeend);

        // Test non-default grade.
        $collab = $generator->create_instance(['course' => $course->id, 'grade' => 86]);
        $this->assertEquals(86, $collab->grade);

        // Test non-default guest role.
        $collab = $generator->create_instance(['course' => $course->id, 'guestrole' => 'pa']);
        $this->assertEquals('pa', $collab->guestrole);

        // Test valid guest URL.
        $url = new moodle_url('/mod/collaborate/tests/fixtures/fakeurl.php');
        $collab = $generator->create_instance(['course' => $course->id, 'guestaccessenabled' => 1, 'guestrole' => 'pa',
            'guesturl' => $url->out()]);
        $this->assertEquals('', $collab->guesturl);
        $guesturl = local::guest_url($collab);
        $collab->guesturl = $guesturl;
        $this->assertContains($url->out(), $collab->guesturl);
    }
}