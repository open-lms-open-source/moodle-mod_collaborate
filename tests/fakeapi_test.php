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

use mod_collaborate\soap\fakeapi;
use mod_collaborate\soap\generated\SetHtmlSession;
use mod_collaborate\soap\generated\ListHtmlSession;
use mod_collaborate\soap\generated\HtmlSessionCollection;
use mod_collaborate\soap\generated\HtmlAttendee;
use mod_collaborate\soap\generated\UpdateHtmlSessionDetails;
use mod_collaborate\soap\generated\RemoveHtmlSession;
use mod_collaborate\soap\generated\SuccessResponse;
use mod_collaborate\soap\generated\ServerConfiguration;
use mod_collaborate\soap\generated\ServerConfigurationResponse;
use mod_collaborate\soap\generated\BuildHtmlSessionUrl;
use mod_collaborate\soap\generated\UpdateHtmlSessionAttendee;
use mod_collaborate\soap\generated\HtmlSessionRecording;
use mod_collaborate\soap\generated\RemoveHtmlSessionRecording;
use mod_collaborate\local;
use mod_collaborate\logging\constants;

class mod_collaborate_fakeapi_testcase extends advanced_testcase {

    /**
     * @param DateTime $a
     * @param DateTime $b
     */
    protected function assert_dates_equal(DateTime $a, DateTime $b) {
        $this->assertEquals($a->getTimestamp(), $b->getTimestamp());
        $tzonea = $a->getTimezone()->getName();
        $tzoneb = $b->getTimezone()->getName();
        $tzonea = $tzonea == 'Z' ? 'UTC' : $tzonea;
        $tzoneb = $tzoneb == 'Z' ? 'UTC' : $tzoneb;
        $this->assertEquals($tzonea, $tzoneb);
    }

    public function test_sethtmlsession() {
        $this->resetAfterTest();
        $api = fakeapi::get_api();
        list ($timestart, $timeend) = local::get_apitimes(time(), 30);
        $userid = 100;
        $htmlattendee = new HtmlAttendee($userid, 'Presenter');
        $htmlsession = new SetHtmlSession('Test Session', $timestart, $timeend, $userid);
        $htmlsession->setHtmlAttendees([$htmlattendee]);
        $htmlsessioncollection = $api->SetHtmlSession($htmlsession);
        $newhtmlsession = $htmlsessioncollection->getHtmlSession()[0];

        $this->assertEquals('Test Session', $newhtmlsession->getName());
        $this->assert_dates_equal($timestart, $newhtmlsession->getStartTime());
        $this->assert_dates_equal($timeend, $newhtmlsession->getEndTime());
        $setuserid = $newhtmlsession->getHtmlAttendees()[0]->getUserId();
        $this->assertEquals($userid, $setuserid);
    }

    public function test_listhtmlsession() {
        $this->resetAfterTest();
        $api = fakeapi::get_api();

        list ($timestart, $timeend) = local::get_apitimes(time(), 30);
        $userid = 100;
        $htmlattendee = new HtmlAttendee($userid, 'Presenter');
        $htmlsession = new SetHtmlSession('Test Session', $timestart, $timeend, $userid);
        $htmlsession->setHtmlAttendees([$htmlattendee]);
        $htmlsessioncollection = $api->SetHtmlSession($htmlsession);
        $newhtmlsession = $htmlsessioncollection->getHtmlSession()[0];

        $params = new ListHtmlSession();
        $params->setSessionId($newhtmlsession->getSessionId());
        $sessioncollection = $api->ListHtmlSession($params);
        $session = $sessioncollection->getHtmlSession()[0];
        $this->assertEquals('Test Session', $session->getName());
    }

    public function test_updatehtmlsession() {
        $this->resetAfterTest();
        $api = fakeapi::get_api();
        list ($timestart, $timeend) = local::get_apitimes(time(), 30);
        $userid = 100;
        $htmlattendee = new HtmlAttendee($userid, 'Presenter');
        $htmlsession = new SetHtmlSession('Test Session', $timestart, $timeend, $userid);
        $htmlsession->setHtmlAttendees([$htmlattendee]);
        $htmlsessioncollection = $api->SetHtmlSession($htmlsession);
        $htmlsession = $htmlsessioncollection->getHtmlSession()[0];
        $sessionid = $htmlsession->getSessionId();
        $updatehtmlsession = new UpdateHtmlSessionDetails($sessionid);
        $updatehtmlsession->setName('Updated Test Session');
        $htmlsessioncollection = $api->UpdateHtmlSession($updatehtmlsession);
        $updatedhtmlsession = $htmlsessioncollection->getHtmlSession()[0];
        $this->assertEquals('Updated Test Session', $updatedhtmlsession->getName());

        // Make sure original unchanged properties are still present and the same as they were when first set.
        $this->assert_dates_equal($timestart, $updatedhtmlsession->getStartTime());
        $this->assert_dates_equal($timeend, $updatedhtmlsession->getEndTime());
        $useridinupdated = $updatedhtmlsession->getHtmlAttendees()[0]->getUserId();
        $this->assertEquals($userid, $useridinupdated);
    }

    public function test_removehtmlsession() {
        $this->resetAfterTest();

        $api = fakeapi::get_api();
        list ($timestart, $timeend) = local::get_apitimes(time(), 30);
        $userid = 100;
        $htmlattendee = new HtmlAttendee($userid, 'Presenter');
        $htmlsession = new SetHtmlSession('Test Session', $timestart, $timeend, $userid);
        $htmlsession->setHtmlAttendees([$htmlattendee]);
        $htmlsessioncollection = $api->SetHtmlSession($htmlsession);
        $newhtmlsession = $htmlsessioncollection->getHtmlSession()[0];
        $removesession = new RemoveHtmlSession($newhtmlsession->getSessionId());
        $success = $api->RemoveHtmlSession($removesession);
        $this->assertTrue($success->getSuccess());
    }

    public function test_serverconfiguration() {
        $this->resetAfterTest();

        $api = fakeapi::get_api();
        $config = $api->GetServerConfiguration(new ServerConfiguration());
        $resp = $config->getServerConfigurationResponse()[0];
        $this->assertEquals(15, $resp->getBoundaryTime());
        $this->assertEquals(50, $resp->getMaxAvailableCameras());
        $this->assertEquals(50, $resp->getMaxAvailableTalkers());
        $this->assertFalse($resp->getMayUseSecureSignOn());
        $this->assertFalse($resp->getMayUseTelephony());
        $this->assertFalse($resp->getMustReserveSeats());
        $this->assertFalse($resp->getRaiseHandOnEnter());
    }

    public function test_buildsessionurl() {
        $this->resetAfterTest();

        $api = fakeapi::get_api();
        list ($timestart, $timeend) = local::get_apitimes(time(), 30);
        $userid = 100;
        $htmlattendee = new HtmlAttendee($userid, 'Presenter');
        $htmlsession = new SetHtmlSession('Test Session', $timestart, $timeend, $userid);
        $htmlsession->setHtmlAttendees([$htmlattendee]);
        $htmlsessioncollection = $api->SetHtmlSession($htmlsession);
        $newhtmlsession = $htmlsessioncollection->getHtmlSession()[0];
        // Test guest url.
        $guesturl = $api->BuildHtmlSessionUrl(new BuildHtmlSessionUrl($newhtmlsession->getSessionId()));
        $this->assertContains('&mode=guest', $guesturl->getUrl());
        // Test user url.
        $param = new BuildHtmlSessionUrl($newhtmlsession->getSessionId());
        $param->setUserId(100);
        $userurl = $api->BuildHtmlSessionUrl($param);
        $this->assertNotContains('&mode=guest', $userurl->getUrl());
    }

    public function test_updatehtmlsessionattendee() {
        $this->resetAfterTest();

        $api = fakeapi::get_api();
        $userid = 100;
        $sessionid = 2;
        $htmlattendee = new HtmlAttendee($userid, 'Presenter');
        $updateattendee = new UpdateHtmlSessionAttendee($sessionid, $htmlattendee);
        $result = $api->UpdateHtmlSessionAttendee($updateattendee);
        $this->assertContains('id=2&userid=100', $result->getUrl());
    }

    public function test_listhtmlsessionrecording() {
        $this->resetAfterTest();

        $api = fakeapi::get_api();
        $sessionid = 100;
        // Add two test recordings.
        $api->add_test_recording($sessionid);
        $api->add_test_recording($sessionid);
        $recording = new HtmlSessionRecording();
        $recording->setSessionId($sessionid);
        $result = $api->ListHtmlSessionRecording($recording);
        $recordings = $result->getHtmlSessionRecordingResponse();
        $this->assertCount(2, $recordings);

        $recordingurl = $recordings[0]->getRecordingUrl();
        $this->assertContains('original_media_url=', $recordingurl);
        $querystring = parse_url($recordingurl, PHP_URL_QUERY);
        $params = [];
        parse_str($querystring, $params);
        $this->assertNotEmpty($params['original_media_url']);
        $orginalmediadecoded = urldecode($params['original_media_url']);
        $this->assertNotEmpty($orginalmediadecoded);
        $this->assertEquals('Recording 1', $recordings[0]->getDisplayName());

        $recordingurl = $recordings[1]->getRecordingUrl();
        $this->assertContains('original_media_url=', $recordingurl);
        $querystring = parse_url($recordingurl, PHP_URL_QUERY);
        $params = [];
        parse_str($querystring, $params);
        $this->assertNotEmpty($params['original_media_url']);
        $orginalmediadecoded = urldecode($params['original_media_url']);
        $this->assertNotEmpty($orginalmediadecoded);
        $this->assertEquals('Recording 2', $recordings[1]->getDisplayName());

        $recording = new HtmlSessionRecording();
        $recording->setSessionId(101);
        $noresults = $api->ListHtmlSessionRecording($recording);
        $this->assertEmpty($noresults->getHtmlSessionRecordingResponse());
    }

    public function test_removehtmlsessionrecording() {
        $this->resetAfterTest();

        $api = fakeapi::get_api();
        $sessionid = 100;
        // Add two test recordings.
        $rec1 = $api->add_test_recording($sessionid);
        $rec2 = $api->add_test_recording($sessionid);
        $recording = new HtmlSessionRecording();
        $recording->setSessionId($sessionid);
        $result = $api->ListHtmlSessionRecording($recording);
        $recordings = $result->getHtmlSessionRecordingResponse();
        $this->assertCount(2, $recordings);

        // Delete the first recording.
        $removerecording = new RemoveHtmlSessionRecording($rec1->getRecordingId());
        $api->RemoveHtmlSessionRecording($removerecording);

        // Make sure recordings list only contains 1 recording and that it has the undeleted items id.
        $result = $api->ListHtmlSessionRecording($recording);
        $recordings = $result->getHtmlSessionRecordingResponse();
        $this->assertCount(1, $recordings);
        $this->assertEquals($rec2->getRecordingId(), $recordings[0]->getRecordingId());
    }

    public function test_logging() {
        global $DB;
        $this->resetAfterTest();

        set_config('logrange', constants::RANGE_MEDIUM, 'collaborate');

        // Need to reset the api to build a new logger.
        $api = fakeapi::get_api(true);
        $api->set_silent(true);

        // Make sure there are no logs.
        $this->assertEquals(0, $DB->count_records('collaborate_log'));

        // Make an error to log that won't record.
        $api->process_error('error:serviceunreachable', constants::SEV_INFO);
        $this->assertEquals(0, $DB->count_records('collaborate_log'));

        // Make an error to actually log.
        $api->process_error('error:serviceunreachable', constants::SEV_ERROR);

        // Make sure there is 1 entry.
        $logs = $DB->get_records('collaborate_log');
        $this->assertCount(1, $logs);

        // Check the log.
        $log = array_pop($logs);
        $this->assertEquals(get_string('error:serviceunreachable', 'mod_collaborate'), $log->message);
        $this->assertEquals(\Psr\Log\LogLevel::ERROR, $log->level);
    }
}
