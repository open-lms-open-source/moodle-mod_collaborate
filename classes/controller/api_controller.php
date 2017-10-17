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
 * View controller.
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\controller;

defined('MOODLE_INTERNAL') || die();

use mod_collaborate\service\testapi_service;


require_once(__DIR__.'/controller_abstract.php');

class api_controller extends controller_abstract {

    public function init() {
        global $PAGE;

        parent::init();

        // Set up the page header.
        $PAGE->set_context(\context_system::instance());
        $PAGE->set_title(get_string('apidiagnostics', 'mod_collaborate'));
        $PAGE->set_heading(get_string('apidiagnostics', 'mod_collaborate'));
        $PAGE->set_url('/mod/collaborate/testapi.php');

        $renderer = $PAGE->get_renderer('mod_collaborate');

        $server = optional_param('server', false, PARAM_URL);
        $username  = optional_param('username', false, PARAM_ALPHANUMEXT);
        $password  = optional_param('password', false, PARAM_RAW);
        $key = optional_param('restkey', false, PARAM_RAW);
        $secret = optional_param('restsecret', false, PARAM_RAW);

        $this->testapiservice = new testapi_service($renderer, $server, $username, $password, $key, $secret);
    }

    /**
     * Do any security checks needed for the passed action
     *
     * @param string $action
     */
    public function require_capability() {
        require_login();
        require_capability('moodle/site:config', \context_system::instance());
    }

    /**
     * View collaborate instasnce.
     *
     * @return json_response
     */
    public function test_action() {
        return $this->testapiservice->handle_testapi();
    }

}
