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
 * The mod_collaborate session launched event.
 *
 * @package    mod_collaborate
 * @copyright  Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\event;

use core\event\base;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_collaborate session launched event class.
 *
 * @property-read array $other {
 *      Extra information about event properties.
 *
 *      - int session: Session id of the Collab activity.
 * }
 *
 * @package    mod_collaborate
 * @since      Moodle 2.8
 * @copyright  Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class session_launched extends base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'collaborate';
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' launched the session with id '".$this->other['session']."' for the Collab with " .
            "course module id '$this->contextinstanceid'.";
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventsessionlaunched', 'mod_collaborate');
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/collaborate/view.php', array('id' => $this->contextinstanceid, 'action' => 'forward'));
    }

    /**
     * Replace add_to_log() statement.
     *
     * @return array of parameters to be passed to legacy add_to_log() function.
     */
    protected function get_legacy_logdata() {
        return array($this->courseid, 'collaborate', 'launch', 'view.php?id=' . $this->contextinstanceid,
                $this->other['session']);
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (empty($this->other['session'])) {
            throw new \coding_exception('The \'session\' value must be set in other.');
        }
    }

    /**
     * @return bool
     */
    public static function get_other_mapping() {
        return false;
    }

    /**
     * @return array
     */
    public static function get_objectid_mapping() {
        return ['db' => 'collaborate', 'restore' => 'collaborate'];
    }
}
