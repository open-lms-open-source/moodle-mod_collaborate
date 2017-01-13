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
 * Recording deleted event.
 *
 * @package    mod_collaborate
 * @author     Guy Thomas
 * @copyright  Copyright (c) 2017 Blackboard Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\event;

use core\event\base;

defined('MOODLE_INTERNAL') || die();

/**
 * Recording deleted event.
 *
 * @package    mod_collaborate
 * @author     Guy Thomas
 * @copyright  Copyright (c) 2017 Blackboard Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recording_deleted extends base {

    protected function init() {
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'collaborate';
    }

    public function get_description() {
        return "The user with id '$this->userid' deleted the recording with id '".$this->other['recordingid'].
        "' for the Collab with course module id '$this->contextinstanceid'.";
    }

    public static function get_name() {
        return get_string('eventrecordingdeleted', 'mod_collaborate');
    }

    public function get_url() {
        $delurl = new moodle_url('/mod/collaborate/recordings.php', array(
            'c' => $this->contextinstanceid,
            'action' => 'delete_confirmation',
            'rid' => $this->other['recordingid'],
            'rname' => $this->other['recordingname']
        ));
        return $delurl;
    }

    public static function get_other_mapping() {
        return false;
    }

    public static function get_objectid_mapping() {
        return ['db' => 'collaborate', 'restore' => 'collaborate'];
    }
}
