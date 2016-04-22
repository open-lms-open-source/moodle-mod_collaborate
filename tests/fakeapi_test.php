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
use mod_collaborate\soap\generated\HtmlAttendee;
use mod_collaborate\soap\generated\UpdateHtmlSessionDetails;
use mod_collaborate\soap\generated\RemoveHtmlSession;
use mod_collaborate\soap\generated\SuccessResponse;
use mod_collaborate\soap\generated\ServerConfiguration;
use mod_collaborate\soap\generated\ServerConfigurationResponse;
use mod_collaborate\soap\generated\BuildHtmlSessionUrl;
use mod_collaborate\local;

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

 }
