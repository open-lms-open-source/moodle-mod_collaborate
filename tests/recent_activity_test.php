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
 * Unit tests for the recording_counter class.
 *
 * @package    mod_collaborate
 * @author     David Castro
 * @copyright  Copyright (c) 2020 Open LMS. (http://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

use mod_collaborate\event\session_launched;

/**
 * Unit tests for the recording_counter class.
 *
 * @package    mod_collaborate
 * @copyright  Copyright (c) 2020 Open LMS. (http://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_collaborate_recent_activity_testcase extends advanced_testcase {
    /**
     * Recent activity in Collab only works if standard log is enabled.
     * @throws coding_exception
     */
    public function test_recent_activity_enable_disable() {
        global $DB;

        $this->resetAfterTest();
        $this->preventResetByRollback(); // Logging waits till the transaction gets committed.

        // Enable standard log.
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        set_config('buffersize', 0, 'logstore_standard');

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_user();
        $collab = $this->getDataGenerator()->create_module('collaborate', ['course' => $course->id]);
        $this->setUser($teacher);
        $now = time();
        $oneweekago = $now - WEEKSECS;
        $collabcontext = context_module::instance($collab->cmid);
        $event = session_launched::create([
            'objectid' => $collab->id,
            'context' => $collabcontext,
            'other' => ['session' => '22222222dwqdw']
        ]);
        $event->trigger();

        // Default behavior, recent activity is enabled.
        $activities = [];
        $index = 0;
        collaborate_get_recent_mod_activity($activities, $index, $oneweekago, $course->id, $collab->cmid);
        $this->assertCount(1, $activities);
        $this->assertEquals(1, $index);

        // New Behavior, recent activity can be disabled.
        set_config('disablerecentactivity', 1, 'collaborate');
        $activities = [];
        $index = 0;
        collaborate_get_recent_mod_activity($activities, $index, $oneweekago, $course->id, $collab->cmid);
        $this->assertEmpty($activities);
        $this->assertEquals(0, $index);
    }
}
