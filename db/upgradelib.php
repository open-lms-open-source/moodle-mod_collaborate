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

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade library for collaborate.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class collaborate_update_manager {
    /**
     * Migrates instanceids to sessionlinkids.
     * Note, we do not create the group sessions + links here as that could be too expensive time wise - we can do those
     * on the fly instead.
     * @throws coding_exception
     */
    public function migrate_recording_info_instanceid_to_sessionlink() {
        global $DB;

        $instances = $DB->get_records('collaborate', null, '', 'id, sessionid');
        // Migrate old recording_info recordings instance ids to use session link records.
        $rs = $DB->get_records('collaborate_recording_info');
        if (PHPUNIT_TEST) {
            $progress = new \core\progress\none();
        } else {
            $progress = new \core\progress\display();
        }
        $progress->start_progress('Migrating recording info to use session link table', count($rs));
        $p = 0;
        $missinginstances = [];
        foreach ($rs as $row) {
            $p++;
            $progress->progress($p);

            if (empty($row->sessionlinkid)) {
                // Migrate to session link.
                if (!isset($instances[$row->instanceid])) {
                    // We can't use debugging or throw an error here.
                    // This is happening because a collaborate instance that had recordings has been deleted.
                    // Delete the recording view data for this missing instance.
                    $DB->delete_records('collaborate_recording_info', ['id' => $row->id]);

                    if (!in_array($row->instanceid, $missinginstances)) {
                        // Only warn about instance missing once.
                        mtrace('Instance does not exist - '.$row->instanceid);
                        $missinginstances[] = $row->instanceid;
                    }

                    continue;
                }
                $instance = $instances[$row->instanceid];
                $slrow = $DB->get_record('collaborate_sessionlink', ['sessionid' => $instance->sessionid]);
                if (!$slrow) {
                    $DB->insert_record('collaborate_sessionlink', (object) [
                        'collaborateid' => $row->instanceid,
                        'sessionid' => $instance->sessionid
                    ]);
                    $slrow = $DB->get_record('collaborate_sessionlink', ['sessionid' => $instance->sessionid]);
                    if (!$slrow) {
                        throw new coding_exception('Failed to create session link record', var_export($slrow, true));
                    }
                }
                $row->sessionlinkid = $slrow->id;
                $updateok = $DB->update_record('collaborate_recording_info', $row);
                if (!$updateok) {
                    throw new coding_exception('Failed link collaborate recording info (id = '.$row->id.') to ' .
                        'session link record', var_export($slrow, true));
                }
            }
        }
        $progress->end_progress();
    }
}
