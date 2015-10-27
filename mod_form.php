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
 * The main collaborate configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

use mod_collaborate\local;

/**
 * Module instance settings form
 *
 * @package    mod_collaborate
 * @copyright  2015 Moodle Rooms www.moodlerooms.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_collaborate_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {

        global $USER, $CFG;

        $mform = $this->_form;

        local::require_configured();

        $mform->addElement('hidden', 'sessionid');
        $mform->setType('sessionid', PARAM_INT);

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('collaboratename', 'collaborate'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Adding the standard "intro" and "introformat" fields.
        $this->standard_intro_elements();

        $time = time();

        // Round time up if necessary.
        $minutes = date('i', $time);
        $rminutes = 0; // New minutes to use in rounding.
        if ($minutes >= 45) {
            $time = strtotime('+1 hour', $time);
        } else if ($minutes >= 15) {
            $rminutes = 30;
        }
        $time = mktime(date('H', $time), $rminutes, 0, date('n'), date('j'), date('Y'));


        // Get timezone to show against start time label.
        $tzones = core_date::get_list_of_timezones();
        if (isset($tzones[$USER->timezone])) {
            $tzone = $tzones[$USER->timezone];
        } else {
            $defaulttz = date_default_timezone_get();
            if (isset($tzones[$defaulttz])) {
                // Great, moodle has a textual representation of this timezone that we can use.
                $tzone = $tzones[$defaulttz];
            } else {
                // We can't find this timezone in the list of moodle timezones, so let's just use it as is.
                $tzone = $defaulttz;
            }
        }
        $tzonestr = ' (' . get_string('timezone', 'mod_collaborate', $tzone).')';

        // Start Time.
        $mform->addElement('date_time_selector', 'timestart', get_string('sessionstart', 'mod_collaborate').$tzonestr);
        $mform->setDefault('timestart', $time);
        $mform->addElement('static', 'sessionstarthelp', '', get_string('sessionstarthelp', 'mod_collaborate'));

        $options = [
            (HOURSECS * 0.5)  => get_string('minutes', 'mod_collaborate', '30'),
            HOURSECS          => get_string('hour', 'mod_collaborate'),
            (HOURSECS * 1.5)  => get_string('hourminutes', 'mod_collaborate', (object) ['hours' => 1, 'minutes' => 30]),
            (HOURSECS * 2)    => get_string('hours', 'mod_collaborate', 2),
            (HOURSECS * 2.5)  => get_string('hoursminutes', 'mod_collaborate', (object) ['hours' => 2, 'minutes' => 30]),
            9999              => get_string('openended', 'mod_collaborate')
        ];
        $mform->addElement('select', 'duration', get_string('duration', 'mod_collaborate'), $options);
        $mform->setDefault('duration', HOURSECS);

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }

    /**
     * Add any custom completion rules to the form.
     *
     * @return array Contains the names of the added form elements
     */
    public function add_completion_rules() {
        $mform =& $this->_form;

        $mform->addElement('checkbox', 'completionlaunch', '', get_string('completionlaunch', 'collaborate'));
        return array('completionlaunch');
    }

    /**
     * Determines if completion is enabled for this module.
     *
     * @param array $data
     * @return bool
     */
    public function completion_rule_enabled($data) {
        return !empty($data['completionlaunch']);
    }
}
