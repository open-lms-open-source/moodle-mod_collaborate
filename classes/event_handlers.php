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
 * Event handlers.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate;
use core\event\group_deleted;
use core\event\group_updated;

defined('MOODLE_INTERNAL') || die();

class event_handlers {

    /**
     * @param group_deleted $event
     */
    public static function group_deleted(group_deleted $event) {
        $groupid = $event->objectid;
        sessionlink::delete_sessions_for_group($groupid);
    }

    /**
     * @param group_updated $event
     */
    public static function group_updated(group_updated $event) {
        $groupid = $event->objectid;
        sessionlink::update_sessions_for_group($groupid);
    }
}
