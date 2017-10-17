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
 * Test REST API.
 *
 * @package   mod_collaborate
 * @category  phpunit
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG, $DB;

use mod_collaborate\rest\api;

class mod_collaborate_rest_api_testcase extends advanced_testcase {

    public function test_configured() {

        $config = (object) [];
        $this->assertFalse(api::configured($config));

        $config->restserver = 'http://someserver.com';
        $this->assertFalse(api::configured($config));

        $config->restkey = 'somekey';
        $this->assertFalse(api::configured($config));

        $config->restsecret = 'somesecret';
        $this->assertTrue(api::configured($config));
    }
}
