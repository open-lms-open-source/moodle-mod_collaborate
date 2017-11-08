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
 * Tests for local library.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2016 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_collaborate\testables\local;
use mod_collaborate\soap\fakeapi;
use mod_collaborate\soap\generated\HtmlSession;
use mod_collaborate\soap\generated\HtmlSessionRecording;
use mod_collaborate\recording_counter;
use mod_collaborate\sessionlink;
use mod_collaborate\event\recording_viewed;

defined('MOODLE_INTERNAL') || die();

class mod_collaborate_local_testcase extends advanced_testcase {

    public function test_servertime_to_utc() {

        $this->resetAfterTest();

        date_default_timezone_set('America/Los_Angeles');

        $time11 = strtotime('2016-12-31 23:00:00');
        $time0 = strtotime('2017-01-01 00:00:00');

        $format = 'Y-m-d H:i:s';

        $pmtoutc = date($format, local::servertime_to_utc($time11));
        $amtoutc = date($format, local::servertime_to_utc($time0));

        // Make sure conversion to utc is as expected:
        $this->assertEquals('2017-01-01 07:00:00', $pmtoutc); // PM LA converts to next day AM.
        $this->assertEquals('2017-01-01 08:00:00', $amtoutc);
    }

    public function test_servertime_to_utc_dst() {

        $this->resetAfterTest();

        date_default_timezone_set('America/Los_Angeles');

        $format = 'Y-m-d H:i:s';

        // Test DST clocks forward.
        $time0 = strtotime('2017-03-12 00:00:00');
        $time1 = strtotime('2017-03-12 01:00:00');
        $time2 = strtotime('2017-03-12 02:00:00');
        $time3 = strtotime('2017-03-12 03:00:00');
        $time4 = strtotime('2017-03-12 04:00:00');

        $am0toutc = date($format, local::servertime_to_utc($time0));
        $am1toutc = date($format, local::servertime_to_utc($time1));
        $am2toutc = date($format, local::servertime_to_utc($time2));
        $am3toutc = date($format, local::servertime_to_utc($time3));
        $am4toutc = date($format, local::servertime_to_utc($time4));

        // Make sure conversion to utc is as expected.
        $this->assertEquals('2017-03-12 08:00:00', $am0toutc);
        $this->assertEquals('2017-03-12 09:00:00', $am1toutc);
        $this->assertEquals('2017-03-12 10:00:00', $am2toutc);
        $this->assertEquals('2017-03-12 10:00:00', $am3toutc); // Day light saving.
        $this->assertEquals('2017-03-12 11:00:00', $am4toutc);

        // Test DST clocks back.
        $time0 = strtotime('2017-11-05 00:00:00');
        $time1 = strtotime('2017-11-05 01:00:00');
        $time2 = strtotime('2017-11-05 02:00:00');
        $time3 = strtotime('2017-11-05 03:00:00');
        $time4 = strtotime('2017-11-05 04:00:00');

        $am0toutc = date($format, local::servertime_to_utc($time0));
        $am1toutc = date($format, local::servertime_to_utc($time1));
        $am2toutc = date($format, local::servertime_to_utc($time2));
        $am3toutc = date($format, local::servertime_to_utc($time3));
        $am4toutc = date($format, local::servertime_to_utc($time4));

        // Make sure conversion to utc is as expected.
        $this->assertEquals('2017-11-05 07:00:00', $am0toutc);
        $this->assertEquals('2017-11-05 08:00:00', $am1toutc);
        $this->assertEquals('2017-11-05 10:00:00', $am2toutc); // Day light saving.
        $this->assertEquals('2017-11-05 11:00:00', $am3toutc);
        $this->assertEquals('2017-11-05 12:00:00', $am4toutc);
    }

    public function test_delete_recording() {
        global $DB;

        $this->resetAfterTest();
        $sink = $this->redirectEvents();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_user();

        // Enrol user to created course.
        $editteacherrole = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $editteacherrole);

        /** @var mod_collaborate_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_collaborate');
        $generator->create_instance(['course' => $course->id]);
        $collab = $generator->create_instance(['course' => $course->id]);
        $cm = get_coursemodule_from_instance('collaborate', $collab->id);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($cm->id);

        $api = fakeapi::get_api();
        $sessionid = $collab->sessionid;

        // Add two test recordings.
        $rec1 = $api->add_test_recording($sessionid);
        $api->add_test_recording($sessionid);

        // Assert 2 recordings.
        $recording = new HtmlSessionRecording();
        $recording->setSessionId($sessionid);
        $result = $api->get_recordings($collab, $cm, true);
        $recordings = $result[$sessionid];
        $this->assertCount(2, $recordings);

        // Insert a record to the collab recording info table.
        $record = [
            'instanceid'  => $collab->id,
            'recordingid' => $rec1->getRecordingId(),
            'action'      => mod_collaborate\recording_counter::VIEW
        ];
        // Delete the cached recording counts.
        \cache::make('mod_collaborate', 'recordingcounts')->delete($collab->id);

        $DB->insert_record('collaborate_recording_info', (object) $record);
        $recordinghelper = new recording_counter($cm, $recordings, null, null);
        $counts = $recordinghelper->get_recording_counts();

        // Assert first recording is viewed once.
        $this->assertEquals(1, reset($counts)->views);

        // Assert that there are 2 recording counts.
        $this->assertCount(2, $counts);

        // Delete recording.
        $this->setUser($teacher);
        local::delete_recording($rec1->getRecordingId(), $rec1->getDisplayName(), $cm);

        // Assert 1 recording.
        $result = $api->get_recordings($collab, $cm, true);
        $recordings = $result[$sessionid];
        $this->assertCount(1, $recordings);

        // Assert recording_deleted event triggered.
        $events = $sink->get_events();
        $event = end($events);
        $this->assertInstanceOf('mod_collaborate\\event\\recording_deleted', $event);

        // Assert recording count data has disappeared for $rec1.
        $recordinghelper = new recording_counter($cm, $recordings, null, null);
        $counts = $recordinghelper->get_recording_counts();
        $this->assertCount(1, $counts);
        $rowcount = $DB->count_records('collaborate_recording_info', ['recordingid' => $rec1->getRecordingId()]);
        $this->assertEquals(0, $rowcount);
    }

    public function test_delete_recording_without_capability() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_user();

        // Enrol student.
        $studentrole = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole);

        /** @var mod_collaborate_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_collaborate');
        $generator->create_instance(['course' => $course->id]);
        $collab = $generator->create_instance(['course' => $course->id]);
        $cm = get_coursemodule_from_instance('collaborate', $collab->id);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($cm->id);

        $api = fakeapi::get_api();
        $sessionid = $collab->sessionid;

        // Add test recordings.
        $rec = $api->add_test_recording($sessionid);
        $api->add_test_recording($sessionid);

        // Assert delete recording fails with capability failure.
        $this->setUser($student);
        $this->setExpectedException('required_capability_exception');
        local::delete_recording($rec->getRecordingId(), $rec->getDisplayName(), $cm);
    }

    public function test_update_collaborate_instance_record() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        /** @var mod_collaborate_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_collaborate');
        $collaborate = $generator->create_instance(['course' => $course->id]);
        $newstart = strtotime('+1 years');
        $newduration = 6 * HOURSECS;
        list($starttime, $endtime) = local::get_apitimes($newstart, $newduration);

        $htmlsession = new HtmlSession(
            $collaborate->sessionid, $collaborate->name, $collaborate->intro, $starttime, $endtime, 10, false, false, '', '',
            false, false, false, false, false, [], 0, false
        );

        // Test that new times affect collaborate record.
        $instanceok = local::get_api()->update_collaborate_instance_record($collaborate,  $htmlsession);
        $this->assertTrue($instanceok);

        $modifiedcollab = $DB->get_record('collaborate', ['id' => $collaborate->id]);
        $this->assertEquals($newstart, $modifiedcollab->timestart);
        $this->assertEquals($newstart + $newduration, $modifiedcollab->timeend);
        $this->assertEquals($newduration, $modifiedcollab->duration);
    }

    public function test_api_update_session() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        /** @var mod_collaborate_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_collaborate');
        $collaborate = $generator->create_instance(['course' => $course->id]);
        // Get main session link (groupid is null).
        $sessionlink = sessionlink::get_group_session_link($collaborate, null);

        $newstart = strtotime('+1 years');
        $newduration = 6 * HOURSECS;
        $collaborate->timestart = $newstart;
        $collaborate->timeend = $newstart + $newduration;
        $collaborate->duration = $newduration;

        local::get_api()->update_session($collaborate, $sessionlink, $course);

        $modifiedcollab = $DB->get_record('collaborate', ['id' => $collaborate->id]);
        $this->assertEquals($newstart, $modifiedcollab->timestart);
        $this->assertEquals($newstart + $newduration, $modifiedcollab->timeend);
        $this->assertEquals($newduration, $modifiedcollab->duration);
    }

    public function test_configured() {
        $config = (object) [];
        $this->assertFalse(local::configured($config));
        $config->server = 'http://someserver';
        $config->username = 'myuser';
        $config->password = 'mypassword';
        $this->assertTrue(local::configured($config));
        $config = (object) [];
        $config->restserver = 'http://somerestserver';
        $config->restkey = 'somerestkey';
        $config->restsecret = 'somerestsecret';
        $this->assertTrue(local::configured($config));
    }

    public function test_select_api() {
        $expected = 'mod_collaborate\testable_api';
        $api = phpunit_util::call_internal_method(null, 'select_api', [null], 'mod_collaborate\local');
        $this->assertEquals($expected, $api);
    }

    public function test_get_api() {
        $soap = local::get_api(false, null, 'soap');
        $this->assertTrue($soap instanceof fakeapi);
        $rest = local::get_api(false, null, 'rest');
        $this->assertTrue($rest instanceof mod_collaborate\rest\api);
        $testable = local::get_api(false, null, 'testable');
        $this->assertTrue($testable instanceof mod_collaborate\testable_api);
    }

    public function test_legacy_record() {
        $legacy = (object) ['sessionid' => 1];
        $this->assertTrue(local::legacy_record($legacy));
        $notlegacy = (object) ['sessionuid' => 'abcd'];
        $this->assertFalse(local::legacy_record($notlegacy));
    }

    public function test_prepare_sessionids_for_query() {
        $record = (object) ['sessionid' => 1234, 'sessionuid' => 'ABCD'];
        $expected = clone $record;
        local::prepare_sessionids_for_query($record);
        $this->assertEquals($expected, $record);
        $record = (object) ['sessionid' => 1234, 'sessionuid' => ''];
        local::prepare_sessionids_for_query($record);
        $this->assertEquals(null, $record->sessionuid);
        $this->assertEquals(1234, $record->sessionid);
        $record = (object) ['sessionid' => 0, 'sessionuid' => 'ABCD'];
        local::prepare_sessionids_for_query($record);
        $this->assertEquals('ABCD', $record->sessionuid);
        $this->assertEquals(null, $record->sessionid);
    }
}
