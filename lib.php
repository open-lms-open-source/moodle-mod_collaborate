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
 * Library of interface functions and constants for module collaborate
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 *
 * All the collaborate specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_collaborate\soap\api;
use mod_collaborate\soap\generated\SetHtmlSession;
use mod_collaborate\soap\generated\UpdateHtmlSessionDetails;
use mod_collaborate\soap\generated\RemoveHtmlSession;
use mod_collaborate\soap\generated\SuccessResponse;
use mod_collaborate\local;
use mod_collaborate\logging\constants;


/* Moodle core API */

/**
 * Returns the information on whether the module supports a feature
 *
 * See {@link plugin_supports()} for more info.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function collaborate_supports($feature) {

    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the collaborate into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $collaborate Submitted data from the form in mod_form.php
 * @param mod_collaborate_mod_form $mform The form instance itself (if needed)
 * @return int The id of the newly inserted collaborate record
 */
function collaborate_add_instance(stdClass $collaborate, mod_collaborate_mod_form $mform = null) {
    global $DB, $COURSE;

    $data = clone($collaborate);
    $data->timeend = local::timeend_from_duration($data->timestart, $data->duration);
    $sessionid = local::api_create_session($data, $COURSE);

    $collaborate->timecreated = time();
    $collaborate->timestart = $data->timestart;
    $collaborate->timeend = $data->timeend;
    $collaborate->sessionid = $sessionid;
    $collaborate->id = $DB->insert_record('collaborate', $collaborate);

    collaborate_grade_item_update($collaborate);

    local::update_calendar($collaborate);

    return $collaborate->id;
}

/**
 * Updates an instance of the collaborate in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $collaborate An object from the form in mod_form.php
 * @param mod_collaborate_mod_form $mform The form instance itself (if needed)
 * @return boolean Success/Fail
 */
function collaborate_update_instance(stdClass $collaborate, mod_collaborate_mod_form $mform = null) {
    global $DB, $COURSE;

    $htmlsession = local::el_update_html_session($collaborate, $collaborate->course);
    $api = api::get_api();
    if ($htmlsession instanceof SetHtmlSession) {
        $collaborate->timeend = local::timeend_from_duration($collaborate->timestart, $collaborate->duration);
        $collaborate->sessionid = local::api_create_session($collaborate, $COURSE);
        $result = $api->SetHtmlSession($htmlsession);
    } else if ($htmlsession instanceof UpdateHtmlSessionDetails) {
        $result = $api->UpdateHtmlSession($htmlsession);
    } else {
        $msg = 'el_update_html_session returned an unexpected object. ';
        $msg .= 'Should have been either mod_collaborate\\soap\\generated\\SetHtmlSession OR ';
        $msg .= 'mod_collaborate\\soap\\generated\\UpdateHtmlSessionDetails. ';
        $msg .= 'Returned: '.var_export($htmlsession, true);
        throw new coding_exception($msg);
    }

    // Re-get html session from result. This means that we are guaranteeing our
    // module data is exactly the same as what is on the collaborate end!
    $htmlsessioncollection = $result->getHtmlSession();
    $htmlsession = $htmlsessioncollection[0];

    if (empty($collaborate->id)) {
        $collaborate->id = $collaborate->instance;
    }

    $collaborate->timemodified = time();
    $collaborate->timestart = $htmlsession->getStartTime()->getTimestamp();
    $collaborate->timeend = $htmlsession->getEndTime()->getTimestamp();

    if (empty($collaborate->guestaccessenabled)) {
        // This is necessary as an unchecked check box just removes the property instead of setting it to 0.
        $collaborate->guestaccessenabled = 0;
    }

    $result = $DB->update_record('collaborate', $collaborate);

    collaborate_grade_item_update($collaborate);

    local::update_calendar($collaborate);

    return $result;
}

/**
 * Removes an instance of the collaborate from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function collaborate_delete_instance($id) {
    global $DB;

    if (! $collaborate = $DB->get_record('collaborate', array('id' => $id))) {
        return false;
    }

    // API request deletion.
    $api = api::get_api();
    $api->set_silent(true);

    $params = new RemoveHtmlSession($collaborate->sessionid);
    try {
        $result = $api->RemoveHtmlSession($params);
    } catch (Exception $e) {
        $result = false;
    }
    if ($result === null) {
        // TODO: Warning - this is a bodge fix! - the wsdl2phpgenerator has set up this class so that it is expecting
        // a Success Response object but we are actually getting back a RemoveSessionSuccessResponse element in the
        // xml and as a result of that we end up with a 'null' object.
        $xml = $api->__getLastResponse();
        if (preg_match('/<success[^>]*>true<\/success>/', $xml)) {
            // Manually create the response object!
            $result = new SuccessResponse(true);
        } else {
            $result = false;
        }
    }

    if (!$result || !$result->getSuccess()) {
        $api->process_error(
            'error:failedtodeletesession', constants::SEV_WARNING
        );
    }

    // Delete main record.
    $DB->delete_records('collaborate', array('id' => $collaborate->id));

    // Delete the recording counts info.
    $DB->delete_records('collaborate_recording_info', ['id' => $collaborate->id]);

    // Delete the cached recording counts.
    cache::make('mod_collaborate', 'recordingcounts')->delete($collab->id);

    collaborate_grade_item_delete($collaborate);

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 *
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param stdClass $course The course record
 * @param stdClass $user The user record
 * @param cm_info|stdClass $mod The course module info object or record
 * @param stdClass $collaborate The collaborate instance record
 * @return stdClass|null
 */
function collaborate_user_outline($course, $user, $mod, $collaborate) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}


/* Gradebook API */

/**
 * Is a given scale used by the instance of collaborate?
 *
 * This function returns if a scale is being used by one collaborate
 * if it has support for grading and scales.
 *
 * @param int $collaborateid ID of an instance of this module
 * @param int $scaleid ID of the scale
 * @return bool true if the scale is used by the given collaborate instance
 */
function collaborate_scale_used($collaborateid, $scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('collaborate', array('id' => $collaborateid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of collaborate.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param int $scaleid ID of the scale
 * @return boolean true if the scale is used by any collaborate instance
 */
function collaborate_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('collaborate', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the given collaborate instance
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $collaborate instance object with extra cmidnumber and modname property
 * @param bool $reset reset grades in the gradebook
 * @return void
 */
function collaborate_grade_item_update(stdClass $collaborate, $reset=false) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $item = array();
    $item['itemname'] = clean_param($collaborate->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;

    if ($collaborate->grade > 0) {
        $item['gradetype'] = GRADE_TYPE_VALUE;
        $item['grademax']  = $collaborate->grade;
        $item['grademin']  = 0;
    } else if ($collaborate->grade < 0) {
        $item['gradetype'] = GRADE_TYPE_SCALE;
        $item['scaleid']   = -$collaborate->grade;
    } else {
        $item['gradetype'] = GRADE_TYPE_NONE;
    }

    if ($reset) {
        $item['reset'] = true;
    }

    grade_update('mod/collaborate', $collaborate->course, 'mod', 'collaborate',
            $collaborate->id, 0, null, $item);
}

/**
 * Delete grade item for given collaborate instance
 *
 * @param stdClass $collaborate instance object
 * @return grade_item
 */
function collaborate_grade_item_delete($collaborate) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('mod/collaborate', $collaborate->course, 'mod', 'collaborate',
            $collaborate->id, 0, null, array('deleted' => 1));
}

/* File API */

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function collaborate_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * File browsing support for collaborate file areas
 *
 * @package mod_collaborate
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function collaborate_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the collaborate file areas
 *
 * @package mod_collaborate
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the collaborate's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 */
function collaborate_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options=array()) {
    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);

    send_file_not_found();
}

/**
 * Collaborate course module info - meeting times.
 *
 * @param cm_info $cm
 */
function collaborate_cm_info_view(cm_info $cm) {
    global $PAGE;
    $renderer = $PAGE->get_renderer('mod_collaborate');
    $times = local::get_times($cm->instance);
    $o = html_writer::tag('span', $renderer->meeting_times($times), ['class' => 'label label-info']);
    $cm->set_after_link($o);
}

/**
 * Print recent activity from all collaborate instances in a given course
 *
 * This is used by course/recent.php
 * @param stdClass $activity
 * @param int $courseid
 * @param bool $detail
 * @param array $modnames
 */
function collaborate_print_recent_mod_activity($activity, $courseid, $detail, $modnames) {
    global $PAGE;
    $renderer = $PAGE->get_renderer('collaborate');
    echo $renderer->recent_activity($activity, $courseid, $detail, $modnames);
}

/**
 * Returns all collaborate instances since a given time.
 *
 * @param array $activities The activity information is returned in this array
 * @param int $index The current index in the activities array
 * @param int $timestart The earliest activity to show
 * @param int $courseid Limit the search to this course
 * @param int $cmid The course module id
 * @param int $userid Optional user id
 * @param int $groupid Optional group id
 * @return void
 */
function collaborate_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid,
                                             $cmid, $userid=0, $groupid=0) {
    global $DB;

    $logmanger = get_log_manager();
    $readers = $logmanger->get_readers('\core\log\sql_reader');
    $reader = reset($readers);
    if (empty($reader)) {
        return; // No log reader found.
    }

    $modinfo = get_fast_modinfo($courseid);
    $cminfo = $modinfo->get_cm($cmid);
    $cmcontext = context_module::instance($cmid);

    $select = "courseid = :courseid AND eventname = :eventname AND objectid = :objectid AND timecreated > :since";
    $params = array(
        'since' => $timestart,
        'objectid'     => $cminfo->instance,
        'courseid'     => $courseid,
        'eventname'    => '\mod_collaborate\event\session_launched'
    );

    if (!empty($userid)) {
        $select .= ' AND userid = :userid';
        $params['userid'] = $userid;
    }

    $events = $reader->get_events_select($select, $params, 'timecreated DESC', 0, 999);

    if (empty($userid)) {
        $userfields = user_picture::fields('u', null);
        list($esql, $params) = get_enrolled_sql($cmcontext, '', 0, true);
        $sql = "SELECT $userfields
                  FROM {user} u
                  JOIN ($esql) e
                    ON e.id = u.id";
        $users = $DB->get_records_sql($sql, $params);
    } else {
        $users = [$userid => $DB->get_record('user', ['id' => $userid])];
    }

    foreach ($events as $event) {
        $eventdata = $event->get_data();

        $user = false;
        if (isset($users[$eventdata['userid']])) {
            $user = $users[$eventdata['userid']];
        } else {
            // User not enrolled, if not for specific group then just get user.
            if (empty($groupid)) {
                $userfields = user_picture::fields('', null);
                $user = $DB->get_record('user', ['id' => $eventdata['userid']], $userfields);
            }
        }

        if (empty($user)) {
            continue;
        }

        $viewfullnames   = has_capability('moodle/site:viewfullnames', $cmcontext);

        $activity = new stdClass();
        $activity->type         = 'collaborate';
        $activity->cmid         = $cmid;
        $activity->name         = format_string($cminfo->name, true);
        $activity->sectionnum   = $cminfo->sectionnum;
        $activity->timestamp    = $eventdata['timecreated'];
        $activity->user         = $user;
        $activity->user->fullname = fullname($user, $viewfullnames);
        $activity->grade        = null;
        $activities[$index++]   = $activity;
    }

}
