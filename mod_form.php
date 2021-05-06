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
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

use mod_collaborate\local;
use mod_collaborate\migration_status;

/**
 * Module instance settings form
 *
 * @package    mod_collaborate
 * @copyright  Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_collaborate_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG, $PAGE;

        $mform = $this->_form;

        // Show notification if REST migration is on course.
        migration_status::get_instance()->show_migration_notification(
            get_string('migrationoncourseerror:creation', 'mod_collaborate'));

        local::require_configured();

        $mform->addElement('hidden', 'sessionid');
        $mform->setType('sessionid', PARAM_INT);
        $mform->addElement('hidden', 'sessionuid');
        $mform->setType('sessionuid', PARAM_ALPHANUMEXT);

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
        $tzone = self::get_validated_time_zone();
        $tzonestr = ' (' . get_string('timezone', 'mod_collaborate', $tzone).')';

        // Start Time.
        $mform->addElement('date_time_selector', 'timestart', get_string('sessionstart', 'mod_collaborate').$tzonestr);
        $mform->setDefault('timestart', $time);
        $mform->addElement('static', 'sessionstarthelp', '', get_string('sessionstarthelp', 'mod_collaborate'));

        $options = [
            (HOURSECS * 0.5)        => get_string('minutes', 'mod_collaborate', '30'),
            HOURSECS                => get_string('hour', 'mod_collaborate'),
            (HOURSECS * 1.5)        => get_string('hourminutes', 'mod_collaborate', (object) ['hours' => 1, 'minutes' => 30]),
            (HOURSECS * 2)          => get_string('hours', 'mod_collaborate', 2),
            (HOURSECS * 2.5)        => get_string('hoursminutes', 'mod_collaborate', (object) ['hours' => 2, 'minutes' => 30]),
            local::DURATIONOFCOURSE => get_string('openended', 'mod_collaborate')
        ];
        $mform->addElement('select', 'duration', get_string('duration', 'mod_collaborate'), $options);
        $mform->setDefault('duration', HOURSECS);

        // Guest access enabled yes / no.
        $mform->addElement('checkbox', 'guestaccessenabled',
                get_string('guestaccessenabled', 'mod_collaborate'), '', array('group' => 1), array(0, 1));

        // Guest role.
        $options = [
            'pa'  => get_string('participant', 'mod_collaborate'),
            'pr'  => get_string('presenter', 'mod_collaborate'),
            'mo'  => get_string('moderator', 'mod_collaborate')
        ];
        $mform->addElement('select', 'guestrole', get_string('guestrole', 'mod_collaborate'), $options);
        $mform->setDefault('guestrole', 'pr');
        $mform->disabledIf('guestrole', 'guestaccessenabled');

        // Instructor Settings.
        $mform->addElement('header', 'instructorsettings', get_string('instructorsettings',  'mod_collaborate'));

        $instructortoggle = get_config('collaborate', 'instructorsettingstoggle');
        $mform->addElement('hidden', 'instructorsettingstoggle', $instructortoggle);
        $mform->setType('instructorsettingstoggle', PARAM_INT);

        $mform->addElement('static', 'instructorsettings:allow', '', get_string('instructorsettings:allow', 'mod_collaborate'));
        $mform->addHelpButton('instructorsettings:allow', 'instructorsettings:allow', 'mod_collaborate');

        $mform->addElement('advcheckbox', 'canpostmessages',
            get_string('canpostmessages', 'mod_collaborate'), '', array('group' => 1), array(0, 1));
        $mform->setDefault('canpostmessages', get_config('collaborate', 'canpostmessages'));
        $mform->disabledIf('canpostmessages', 'instructorsettingstoggle', 0);

        $mform->addElement('advcheckbox', 'canannotatewhiteboard',
            get_string('canannotatewhiteboard', 'mod_collaborate'), '', array('group' => 1), array(0, 1));
        $mform->setDefault('canannotatewhiteboard', get_config('collaborate', 'canannotatewhiteboard'));
        $mform->disabledIf('canannotatewhiteboard', 'instructorsettingstoggle', 0);

        $mform->addElement('advcheckbox', 'cansharevideo',
            get_string('cansharevideo', 'mod_collaborate'), '', array('group' => 1), array(0, 1));
        $mform->setDefault('cansharevideo', get_config('collaborate', 'cansharevideo'));
        $mform->disabledIf('cansharevideo', 'instructorsettingstoggle', 0);

        $mform->addElement('advcheckbox', 'canshareaudio',
            get_string('canshareaudio', 'mod_collaborate'), '', array('group' => 1), array(0, 1));
        $mform->setDefault('canshareaudio', get_config('collaborate', 'canshareaudio'));
        $mform->disabledIf('canshareaudio', 'instructorsettingstoggle', 0);

        $mform->addElement('advcheckbox', 'candownloadrecordings',
            get_string('candownloadrecordings', 'mod_collaborate'), '', array('group' => 1), array(0, 1));
        $mform->setDefault('candownloadrecordings', get_config('collaborate', 'candownloadrecordings'));
        $mform->disabledIf('candownloadrecordings', 'instructorsettingstoggle', 0);

        $mform->addElement('static', 'instructorsettings:largesession', '',
            get_string('instructorsettings:largesession', 'mod_collaborate'));
        $mform->addHelpButton('instructorsettings:largesession',
            'instructorsettings:largesession', 'mod_collaborate');

        $mform->addElement('advcheckbox', 'largesessionenable',
            get_string('largesessionenable', 'mod_collaborate'), '', array('group' => 1), array(0, 1));
        $mform->setDefault('largesessionenable', 0);
        $mform->disabledIf('largesessionenable', 'instructorsettingstoggle', 0);
        $mform->disabledIf('largesessionenable', 'groupmode', 'neq', NOGROUPS);

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();
        /** @var MoodleQuickForm_modgrade $modgrade */
        $modgrade = $mform->getElement('grade');
        if (empty($modgrade->isupdate)) {
            $mform->setDefault('grade[modgrade_type]', 'none');
        }

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();

        $migrationstatus = migration_status::get_instance()->get_migration_status();
        $PAGE->requires->js_call_amd('mod_collaborate/settings', 'uiinit', ['migrationstatus' => $migrationstatus]);
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

    /**
     * Determines if the given time zones are valid.
     *
     * @return string $tzone
     * @throws \Exception
     */
    public static function get_validated_time_zone() {
        global $USER;
        $tzones = core_date::get_list_of_timezones();
        if (isset($tzones[$USER->timezone])) {
            $tzone = $tzones[$USER->timezone];
        } else {
            $defaulttz = core_date::get_server_timezone();
            if (isset($tzones[$defaulttz])) {
                 // Great, moodle has a textual representation of this timezone that we can use.
                 $tzone = $tzones[$defaulttz];
            } else {
                 // We can't find this timezone in the list of moodle timezones, so let's just use it as is.
                 throw new \moodle_exception('error:invalidservertimezone', 'collaborate');
            }
        }
        return $tzone;
    }

    public function set_data($defaultvalues) {
        if (is_object($defaultvalues)) {
            $defaultvalues = (array)$defaultvalues;
        }
        $groupmode = $defaultvalues['groupmode'];
        $largesession = array_key_exists('largesessionenable', $defaultvalues) && $defaultvalues['largesessionenable'];
        if ($groupmode != NOGROUPS) {
            $defaultvalues['largesessionenable'] = 0;
        }
        if ($largesession) {
            $defaultvalues['guestrole'] = 'pa';
        }
        parent::set_data($defaultvalues);
    }
    /**
     * Perform minimal validation on the settings form
     * @param array $data
     * @param array $files
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data['guestaccessenabled']) && !empty($data['largesessionenable']) &&
            $data['guestaccessenabled'] == 1 && $data['largesessionenable'] == 1) {
            if ($data['guestrole'] != 'pa') {
                $errors['guestrole'] = get_string('rolenotavailableforlargesession', 'collaborate');
            }
        }

        return $errors;
    }
}
