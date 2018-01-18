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
 * Renderable for joining meetings
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\renderables;

defined('MOODLE_INTERNAL') || die();

use mod_collaborate\local;
use mod_collaborate\traits\exportable;

class meetingstatus implements \renderable, \templatable{

    use exportable;

    /**
     * @var str
     */
    public $meetingtimes;

    /**
     * @var boolean|\stdClass
     */
    public $statusunrestored = false;

    /**
     * @var boolean
     */
    public $statusjoinmeeting = false;

    /**
     * @var boolean|\stdClass
     */
    public $statuswarnnoguest = false;

    /**
     * @var boolean|\stdClass
     */
    public $statusmeetingtimepassed = false;

    /**
     * @var boolean
     */
    public $statuslistgroups = false;

    /**
     * @var \moodle_url|null to be used for link or form action
     */
    public $fwdurl = null;

    /**
     * @var bool access all groups
     */
    public $aag = false;

    /**
     * @var \stdClass[]
     */
    public $groups;


    public function __construct($times,
                                view_action $viewaction,
                                $allowguestaccess = false) {

        global $PAGE, $USER, $COURSE;

        $collaborate = $viewaction->get_collaborate();
        $cm = $viewaction->get_cm();
        $canmoderate = $viewaction->get_canmoderate();
        $canparticipate = $viewaction->get_canparticipate();
        $unrestored = $collaborate->sessionid === null && $collaborate->sessionuid === null && $canparticipate;

        /** @var mod_collaborate_renderer $output */
        $output = $PAGE->get_renderer('mod_collaborate');
        // This should be migrated to a renderable and template at some point.
        $this->meetingtimes = $output->meeting_times($times);

        $params = ['action' => 'forward', 'id' => $cm->id, 'sesskey' => sesskey()];
        $this->fwdurl = new \moodle_url('view.php', $params);

        $boundarytime = local::boundary_time() * 60;

        if (time() < $times->end) {
            if (time() > ($times->start - $boundarytime)) {

                $showunrestored = !$canmoderate && $canparticipate && $unrestored;

                if ($showunrestored) {
                    $this->statusunrestored = (object) ['message' => get_string('unrestored', 'collaborate')];
                } else if ($canmoderate || $canparticipate) {
                    $this->statusjoinmeeting = true;
                    $forcedgrps = $COURSE->groupmodeforce && $COURSE->groupmode;
                    if ($cm->groupmode > NOGROUPS || $forcedgrps) {
                        $this->aag = has_capability('moodle/site:accessallgroups', $cm->context);
                        if ($this->aag) {
                            $this->groups = groups_get_all_groups($cm->get_course()->id);
                        } else {
                            $this->groups = groups_get_all_groups($cm->get_course()->id, $USER->id);
                            if (!empty($this->groups)) {
                                reset($this->groups)->checked = "checked=\"checked\"";
                            }
                        }
                    }
                    if (count($this->groups) > 1 || $this->aag) {
                        $this->statuslistgroups = true;
                    }
                } else if (!$allowguestaccess) {
                    $this->statuswarnnoguest = (object) ['message' => get_string('noguestentry', 'collaborate')];
                }
            }
            // Note: it is intentional that users don't see any message indicating that the meeting is scheduled to occur.
            // This can already be inferred by the title and times.
        } else {
            $this->statusmeetingtimepassed = (object) [
                'message' => get_string('meetingtimepast', 'mod_collaborate', $times)
            ];
        }
    }

}
