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
 * Recording controller.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\controller;

use mod_collaborate\event\recording_downloaded;
use mod_collaborate\event\recording_viewed;
use mod_collaborate\recording_counter;
use mod_collaborate\local;
use core\output\notification;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/controller_abstract.php');

class recordings_controller extends controller_abstract {

    /**
     * @var \cm_info
     */
    protected $cm;

    /**
     * @var \stdClass|bool
     */
    protected $course;

    /**
     * @var \stdClass|bool
     */
    protected $collaborate;

    public function init() {
        global $PAGE;

        $this->set_properties();

        $PAGE->set_url(new \moodle_url('/mod/collaborate/recordings.php', ['id' => $this->cm->id, 'action' => $this->action]));

        parent::init();

        require_once(__DIR__.'/../../lib.php');
    }

    /**
     * Set class properties from params.
     *
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    protected function set_properties() {
        global $DB;

        $c  = required_param('c', PARAM_INT);  // Collaborate instance ID.

        $this->collaborate = $DB->get_record('collaborate', array('id' => $c), '*', MUST_EXIST);
        $this->course = $DB->get_record('course', array('id' => $this->collaborate->course), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance(
                'collaborate', $this->collaborate->id, $this->course->id, false, MUST_EXIST
        );
        $modinfo = get_fast_modinfo($this->course);
        $this->cm = $modinfo->get_cm($cm->id);
    }

    public function require_capability() {
        require_login($this->course, true, $this->cm);

        $context = \context_module::instance($this->cm->id);
        require_capability('mod/collaborate:participate', $context);
        require_sesskey();
    }

    /**
     * View or download a recording based on params.
     * @param bool $view
     * @throws \coding_exception
     */
    private function view_or_download($view = true) {
        global $DB;

        if ($view) {
            $actioncheck = 'view';
            $disposition = 'launch';
        } else {
            $actioncheck = 'download';
            $disposition = 'download';
        }

        $urlencoded = required_param('url', PARAM_TEXT);
        $action = required_param('action', PARAM_ALPHA);
        $recordingid = required_param('rid', PARAM_ALPHANUMEXT);
        $sessionlinkid = required_param('sessionlinkid', PARAM_INT);
        $context = \context_module::instance($this->cm->id);

        $url = urldecode($urlencoded);

        $data = [
            'contextid' => $context->id,
            'objectid' => $this->collaborate->id,
            'other' => [
                'recordingid' => $recordingid,
            ],
        ];

        // Create the appropriate event based on view or recording.
        if ($action === $actioncheck) {
            if ($actioncheck === recording_counter::VIEW) {
                $event = recording_viewed::create($data);
            } else {
                $event = recording_downloaded::create($data);
            }
        } else {
            throw new \coding_exception('Only action of type ' . $actioncheck . ' is allowed for type, action of type ' .
                $action . ' provided');
        }

        // Insert a record to the collab recording info table.
        if ($action === 'view') {
            $actionint = recording_counter::VIEW;
        } else {
            $actionint = recording_counter::DOWNLOAD;
        }

        $record = ['instanceid' => $this->collaborate->id, 'sessionlinkid' => $sessionlinkid,
                'recordingid' => $recordingid, 'action' => $actionint];
        $DB->insert_record('collaborate_recording_info', (object) $record);

        // Trigger the event.
        $event->trigger();

        // Delete the cached recording counts.
        \cache::make('mod_collaborate', 'recordingcounts')->delete($this->collaborate->id);

        if (!empty($this->collaborate->sessionuid)) {
            $api = local::get_api();
            $url = $api->get_recording_url($recordingid, $disposition);
            redirect($url);
        } else {
            redirect($url);
        }
    }

    /**
     * View collaborate recording.
     *
     * @return void
     */
    public function view_action() {
        $this->view_or_download();
    }

    /**
     * Download collaborate recording.
     *
     * @return void
     */
    public function download_action() {
        $this->view_or_download(false);
    }

    /**
     * Delete confirmation action.
     */
    public function delete_action() {
        global $PAGE, $OUTPUT;

        $recordingid = required_param('rid', PARAM_ALPHANUMEXT);
        $recordingname = required_param('rname', PARAM_TEXT);

        // Set up the page header.
        $PAGE->set_title(format_string($this->collaborate->name));
        $PAGE->set_heading(format_string($this->course->fullname));

        $baseparams = [
            'c' => $this->cm->instance,
            'rid' => $recordingid,
            'rname' => $recordingname,
            'sesskey' => sesskey()
        ];

        $PAGE->set_url('/mod/collaborate/recordings.php', $baseparams + ['action' => 'delete']);

        echo $OUTPUT->header();
        $continueparams = $baseparams + ['action' => 'delete_confirmation'];
        $continueurl = new \moodle_url('/mod/collaborate/recordings.php', $continueparams);
        $cancelurl = new \moodle_url('/mod/collaborate/view.php', ['id' => $this->cm->id]);

        $confmsg = get_string('deleterecordingconfirmation', 'mod_collaborate', format_string($recordingname));
        echo $OUTPUT->confirm($confmsg, $continueurl, $cancelurl);
        echo $OUTPUT->footer();
    }

    /**
     * Delete confirmation action.
     */
    public function delete_confirmation_action() {
        global $PAGE;

        $recordingid = required_param('rid', PARAM_ALPHANUMEXT);
        $recordingname = required_param('rname', PARAM_TEXT);

        // Set up the page header.
        $PAGE->set_title(format_string($this->collaborate->name));
        $PAGE->set_heading(format_string($this->course->fullname));
        $PAGE->set_url('/mod/collaborate/recordings.php', array(
            'c' => $this->cm->id,
            'action' => 'delete_confirmation',
            'rid' => $recordingid,
            'rname' => $recordingname
        ));

        local::delete_recording($recordingid, $recordingname, $this->cm);

        $message = get_string('recordingdeleted', 'mod_collaborate', format_string($recordingname));
        $redirecturl = new \moodle_url('/mod/collaborate/view.php', ['id' => $this->cm->id]);

        redirect($redirecturl, $message, null, notification::NOTIFY_SUCCESS);
    }
}
