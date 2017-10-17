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
 * Test jwthelper.
 *
 * @package   mod_collaborate
 * @category  phpunit
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG, $DB;

use mod_collaborate\rest\jwthelper;

class mod_collaborate_jwthelper_testcase extends advanced_testcase {
    public function test_get_token() {
        $token = jwthelper::get_token('mykey', 'mysecret');
        $this->assertNotEmpty($token);
        $this->assertContains('.', $token);
        $parts = explode('.', $token);
        $json1 = json_decode(base64_decode($parts[0]));
        $this->assertEquals('JWT', $json1->typ);
        $this->assertEquals('HS256', $json1->alg);
        $json2 = json_decode(base64_decode($parts[1]));
        $this->assertEquals('mykey', $json2->iss);
        $this->assertEquals('mykey', $json2->sub);
        $this->assertNotEmpty($json2->exp);
        $this->assertRegExp('/\d+/', strval($json2->exp));
        $this->assertNotEmpty($parts[2]);
    }
}
