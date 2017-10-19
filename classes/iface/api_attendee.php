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
 * API attendee interface.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

interface api_attendee {

    /**
     * Update attendee for a specific session.
     * @param string $sessionuid
     * @param string $userid
     * @param string $avatarurl
     * @param string $displayname
     * @param string $role
     *
     * @throws \coding_exception
     *
     * @return $string url
     */
    public function update_attendee($sessionuid, $userid, $avatarurl, $displayname, $role);
}
