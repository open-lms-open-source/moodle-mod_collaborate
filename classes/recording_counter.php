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
     * @const int
     */
    const VIEW = 1;

    /**
     * @var \cm_info
     */
    private $cm;

    /**
     * @var \mod_collaborate\soap\generated\HtmlSessionRecordingResponse[]
     */
    private $recordings = [];

    /**
     * @var \moodle_database|null
     */
    private $db;

    /**
     * recording_counter constructor.
     * @param \cm_info $cm
     * @param \mod_collaborate\soap\generated\HtmlSessionRecordingResponse[] $recordings
     * @param \moodle_database|null $db
     * @param \cache|null $cache
     * @throws \coding_exception
     */
    public function __construct($cm, $recordings, \moodle_database $db = null, \cache $cache = null) {
        global $DB;

        $this->cm = $cm;
        $this->recordings = $recordings;

        if (is_null($db)) {
            $db = $DB;
        }
        $this->db = $db;

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
        $counts = $this->cache->get($this->cm->instance);
        if (empty($counts) or (count($counts) != $numrecordings)) {
            // Miss on the cache, query for the counts.
            $counts = $this->query_counts();

            // Set the cache with the results.
            $this->cache->set($this->cm->instance, $counts);
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
            'instanceid' => $this->cm->instance,
        ];

        $sql = <<<EOL
  SELECT recordingid, action, COUNT(action) numactions
    FROM {collaborate_recording_info}
   WHERE instanceid = :instanceid
GROUP BY instanceid, recordingid, action
EOL;

        $rs = $this->db->get_recordset_sql($sql, $params);
        if (!$rs->valid()) {
            return $recordingcounts;
        }

        foreach ($rs as $recordingid => $event) {
            if (empty($recordingcounts[$recordingid])) {
                $recordingcounts[$recordingid] = new recording_counts($recordingid);
            }
            if ($event->action == self::VIEW) {
                $recordingcounts[$recordingid]->views = $event->numactions;
            }
        }

        $rs->close();

        return $recordingcounts;
    }
}
