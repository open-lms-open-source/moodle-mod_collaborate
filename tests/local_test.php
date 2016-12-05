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

}
