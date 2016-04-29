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
 * Recording count helper class.
 *
 * @package    mod_collaborate
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate;

use core\log\sql_reader;
use mod_collaborate\renderables\recording_counts;

defined('MOODLE_INTERNAL') || die();

/**
 * Recording count helper class.
 *
 * @package    mod_collaborate
 * @copyright  Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recording_counter {

    /**
     * @var \cm_info
     */
    private $cm;

    /**
     * @var \mod_collaborate\soap\generated\HtmlSessionRecordingResponse[]
     */
    private $recordings = [];

    /**
     * @var sql_reader
     */
    private $reader;

    /**
     * recording_counter constructor.
     * @param \cm_info $cm
     * @param \mod_collaborate\soap\generated\HtmlSessionRecordingResponse[] $recordings
     * @param sql_reader|null $reader
     * @param \cache|null $cache
     * @throws \coding_exception
     */
    public function __construct($cm, $recordings, sql_reader $reader = null, \cache $cache = null) {
        $this->cm = $cm;
        $this->recordings = $recordings;

        if (is_null($reader)) {
            /** @var \core\log\manager $logmanager */
            $logmanager = get_log_manager();
            $readers = $logmanager->get_readers('core\\log\\sql_reader');

            do {
                $reader = reset($readers);
            } while (!($reader instanceof \logstore_standard\log\store) and !empty($reader));
        }
        if (!$reader instanceof \logstore_standard\log\store) {
            throw new \coding_exception('Standard log store must be enabled and used');
        }
        $this->reader = $reader;

        if (is_null($cache)) {
            $cache = \cache::make('mod_collaborate', 'recordingcounts');
        }
        $this->cache = $cache;
    }

    /**
     * @return recording_counts[]
     */
    public function get_recording_counts() {
        $numrecordings = count($this->recordings);
        // Try the cache first.
        $counts = $this->cache->get($this->cm->id);
        if (empty($counts) or (count($counts) != $numrecordings)) {
            // Miss on the cache, query for the counts.
            $counts = $this->query_counts();

            // Set the cache with the results.
            $this->cache->set($this->cm->id, $counts);
        }
        return $counts;
    }

    /**
     * @return recording_counts[]
     * @throws \coding_exception
     */
    protected function query_counts() {
        $recordingcounts = [];

        // Initialize a model for each recording.
        foreach ($this->recordings as $recording) {
            $recordingid = $recording->getRecordingId();
            $recordingcounts[$recordingid] = new recording_counts($recordingid);
        }

        $params = [
            'course' => $this->cm->course,
            'cmid'   => $this->cm->id,
            'component' => 'mod_collaborate',
            'target' => 'recording',
            'viewaction' => 'viewed',
            'downloadaction' => 'downloaded'
        ];
        $where = 'courseid = :course AND component = :component AND contextinstanceid = :cmid AND target = :target'
            . 'AND (action = :downloadaction OR action = :viewaction)';

        $events = $this->reader->get_events_select($where, $params, '', 0, 0);

        foreach ($events as $event) {
            $eventdata = $event->get_data();
            $recordingid = $eventdata['other']['recordingid'];

            if (empty($recordingid)) {
                continue;
            }
            if (empty($recordingcounts[$recordingid])) {
                $recordingcounts[$recordingid] = new recording_counts($recordingid);
            }
            if ($eventdata['action'] == $params['viewaction']) {
                $recordingcounts[$recordingid]->views++;
            } else if ($eventdata['action'] == $params['downloadaction']) {
                $recordingcounts[$recordingid]->downloads++;
            }
        }

        return $recordingcounts;
    }
}
