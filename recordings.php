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
 * Recording redirect script. Handles Collaborate recording views and downloads and fires events.
 *
 * @package    mod_collaborate
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_collaborate\event\recording_downloaded;
use mod_collaborate\event\recording_viewed;
use mod_collaborate\recording_counter;

require_once(__DIR__ .'/../../config.php');

$instanceid = required_param('c', PARAM_INT);
$urlencoded = required_param('url', PARAM_TEXT);
$action = required_param('t', PARAM_INT);
$recordingid = required_param('rid', PARAM_INT);

$collab = $DB->get_record('collaborate', ['id' => $instanceid], '*', MUST_EXIST);
$course = $DB->get_record('course', ['id' => $collab->course], '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('collaborate', $collab->id, $course->id, false, MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/collaborate:participate', $context);
require_sesskey();

$url = urldecode($urlencoded);

$record = ['instanceid' => $collab->id, 'recordingid' => $recordingid, 'action' => $action];
$DB->insert_record('collaborate_recording_info', (object) $record);

$data = [
    'contextid' => $context->id,
    'objectid' => $collab->id,
    'other' => [
        'recordingid' => $recordingid,
    ],
];

// Create the appropriate event based on view or recording and trigger.
if ($action == recording_counter::DOWNLOAD) {
    $event = recording_downloaded::create($data);
} else if ($action == recording_counter::VIEW) {
    $event = recording_viewed::create($data);
} else {
    throw new coding_exception('Only view or download is allowed for type');
}
$event->trigger();

// Delete the cached recording counts.
cache::make('mod_collaborate', 'recordingcounts')->delete($collab->id);

redirect($url);
