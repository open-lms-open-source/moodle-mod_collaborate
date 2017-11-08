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

namespace mod_collaborate\iface;

defined('MOODLE_INTERNAL') || die();

use stdClass;

/**
 * API session interface.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

interface api_session {

    /**
     * Create a session based on $collaborate data for specific $course
     *
     * @param stdClass $collaborate - collaborate instance data or record.
     * @param stdClass $sessionlink
     * @param stdClass|null $course
     * @return string sessionid
     * @throws \moodle_exception
     */
    public function create_session(stdClass $collaborate, stdClass $sessionlink, stdClass $course = null);

    /**
     * Updates the collaborate instance record's time and end dates with those returned in the API call response
     * object.
     * @param stdClass $collaborate
     * @param stdClass | \mod_collaborate\soap\generated\HtmlSession $respobj
     * @return mixed
     */
    public function update_collaborate_instance_record(stdClass $collaborate, $respobj);

    /**
     * Update a session based on $collaborate data for specific $course
     * @param stdClass $collaborate - collaborate instance data or record.
     * @param stdClass $sessionlink
     * @param stdClass|null $course
     * @return int
     * @throws \dml_exception
     * @throws \moodle_exception
     * @throws coding_exception
     */
    public function update_session(stdClass $collaborate, stdClass $sessionlink, stdClass $course = null);

    /**
     * Delete a specific session.
     * @param string $sessionid
     * @return mixed
     */
    public function delete_session($sessionid);

    /**
     * Get a guest url for a specific session.
     * @param $sessionid
     * @return mixed
     */
    public function guest_url($sessionid);
}
