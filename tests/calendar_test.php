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
 * Test Collaborate interaction with Moodle calendar.
 *
 * @package   mod_collaborate
 * @category  phpunit
 * @copyright Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG, $DB;

use mod_collaborate\local;
require_once(__DIR__.'/../../../local/mr/bootstrap.php');
require_once($CFG->dirroot.'/calendar/lib.php');

class mod_collaborate_calendar_testcase extends advanced_testcase {

    private $user;

    protected function setUp() {
        global $USER;
        // The user we are going to test this on.
        $this->setAdminUser();
        $this->user = $USER;
    }

    public function test_collabcalendar_duration_onehour() {
        global $DB;

        // This scenario is for creating a collaborate session with duration set to 1 hour.

        $this->resetAfterTest(true);

        $course1 = $this->getDataGenerator()->create_course();
        $duration = 3600; // Setting duration of the activity to one hour.
        $now = time();

        // Create a collaborate activity.
        $collabactivity = new stdClass();
        $collabactivity->name = 'Task One';
        $collabactivity->intro = 'Description of task one';
        $collabactivity->introformat = 1;
        $collabactivity->course = $course1->id;
        $collabactivity->timecreated = $now;
        $collabactivity->timestart = $collabactivity->timecreated;
        $collabactivity->duration = $duration;

        $finishingtime = local::timeend_from_duration($collabactivity->timestart, $duration);
        $collabactivity->timeend = $finishingtime;
        $this->assertEquals(($collabactivity->timestart + intval($duration)), $collabactivity->timeend);
        $collabactivity->id = 10;

        // Once the activity it's ready, create the calendar event.
        local::update_calendar($collabactivity);

        // Capture the calendar event created.
        $params = array('modulename' => 'collaborate', 'instance' => $collabactivity->id);
        $eventid = $DB->get_field('event', 'id', $params);
        $calendarevent = \calendar_event::load($eventid);

        // Check that the calendar event got the correct properties.
        $this->assertEquals($eventid, $calendarevent->id);
        $this->assertEquals($collabactivity->name, $calendarevent->name);
        $this->assertEquals($course1->id, $calendarevent->courseid);
        $this->assertEquals('collaborate', $calendarevent->modulename);
        $this->assertEquals($collabactivity->id, $calendarevent->instance);
        $this->assertEquals($collabactivity->timestart, $calendarevent->timestart);
        $this->assertEquals($duration, $calendarevent->timeduration);
    }

    public function test_collabcalendar_duration_durationofcourse() {
        global $DB;

        // This scenario is for creating a collaborate session with duration set to "duration of course".

        $this->resetAfterTest(true);

        $course2 = $this->getDataGenerator()->create_course();
        $duration = 9999; // Numeric value for "Duration of course" set on the collab form.
        $now = time();

        // Create another activity.
        $collabactivity = new stdClass();
        $collabactivity->name = 'Task Two';
        $collabactivity->intro = 'Description of task two';
        $collabactivity->introformat = 1;
        $collabactivity->course = $course2->id;
        $collabactivity->timecreated = $now;
        $collabactivity->timestart = $collabactivity->timecreated;
        $collabactivity->duration = $duration;

        $finishingtime = local::timeend_from_duration($collabactivity->timestart, $duration);
        $collabactivity->timeend = $finishingtime;
        $this->assertNotEquals(($collabactivity->timestart + intval($duration)), $collabactivity->timeend);
        $durationofcoursetimestamp = strtotime('3000-01-01 00:00');
        $this->assertEquals($durationofcoursetimestamp, $finishingtime);
        $collabactivity->id = 20;

        // Create another calendar event.
        local::update_calendar($collabactivity);

        // Capture the calendar event created.
        $params = array('modulename' => 'collaborate', 'instance' => $collabactivity->id);
        $eventid = $DB->get_field('event', 'id', $params);
        $calendarevent = \calendar_event::load($eventid);

        // Check that the calendar event got the correct properties.
        $this->assertEquals($eventid, $calendarevent->id);
        $this->assertEquals($collabactivity->name, $calendarevent->name);
        $this->assertEquals($course2->id, $calendarevent->courseid);
        $this->assertEquals('collaborate', $calendarevent->modulename);
        $this->assertEquals($collabactivity->id, $calendarevent->instance);
        $this->assertEquals($collabactivity->timestart, $calendarevent->timestart);
        $this->assertNotEquals($duration, $calendarevent->timeduration);
        $this->assertEquals(0, $calendarevent->timeduration);
    }
}