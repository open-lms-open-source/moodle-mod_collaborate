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
 * Renderer
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/calendar/lib.php');

use mod_collaborate\renderables\view_action;
use mod_collaborate\local;

class mod_collaborate_renderer extends plugin_renderer_base {

    /**
     * HTML5 time element.
     *
     * @param $time
     * @param bool $userdate
     * @return string
     */
    public function datetime($time, $visualtime = null) {
        if (is_string($time) && strval(intval($time)) === $time) {
            $time = intval($time);
        }
        if (!is_int($time)) {
            $time = strtotime($time);
        }

        if ($visualtime === null) {
            // Note the calendar_day_representation function automatically adjusts to take into account user timezone.
            $visualtime = calendar_day_representation($time);
            $visualtime .= ' ' . calendar_time_representation($time);
        }

        return html_writer::tag('time', $visualtime, array(
            'datetime' => date('c', $time))
        );
    }


    /**
     * Display meeting status, times, join link, etc..
     *
     * @param $times
     * @param $cm
     * @param bool $canmoderate
     * @param bool $canparticipate
     * @return string
     */
    public function meeting_status($times, $cm, $canmoderate = false, $canparticipate = false, $unrestored = false) {
        global $OUTPUT;

        $o = '<div class = "path-mod-collaborate__meetingstatus">';
        $o .= '<div class = "path-mod-collaborate__meetingstatus_times">'.$this->meeting_times($times).'</div>';

        $boundarytime = local::boundary_time() * 60;

        if (time() < $times->end) {
            if (time() > ($times->start - $boundarytime)) {
                if ($canparticipate && $unrestored) {
                    $o .= $OUTPUT->notification(get_string('unrestored', 'collaborate'));
                } else if ($canmoderate || $canparticipate) {
                    $url = new moodle_url('view.php', ['action' => 'forward', 'id' => $cm->id]);
                    $o .= html_writer::link($url, get_string('meetingtimejoin', 'mod_collaborate', $times), [
                        'class' => 'btn btn-success',
                        'target' => '_blank'
                    ]);
                } else {
                    $o .= $OUTPUT->notification(get_string('noguestentry', 'collaborate'));
                }
            }
            // Note: it is intentional that users don't see any message indicating that the meeting is scheduled to occur.
            // This can already be inferred by the title and times.
        } else {
            $o .= '<p>'.get_string('meetingtimepast', 'mod_collaborate', $times).'</p>';
        }

        $o .= '</div>';
        return $o;
    }

    /**
     * Meeting times.
     *
     * @param $times
     * @return string
     */
    public function meeting_times($times) {

        $startday = calendar_day_representation($times->start);
        $endday = calendar_day_representation($times->end);
        $endtime = calendar_time_representation($times->end);

        $datesstr = $this->datetime($times->start);

        if ($startday == $endday) {
            $datesstr .= ' - '.$this->datetime($times->end, $endtime);
        } else {
            if (date('Y-m-d', $times->end) != '3000-01-01') {
                $datesstr .= ' - ' . $this->datetime($times->end);
            } else {
                $datesstr .= ' ('.get_string('openended', 'mod_collaborate').')';
            }
        }

        return $datesstr;
    }

    /**
     * Connection verified status.
     *
     * @param bool $verified
     * @return string
     */
    public function connection_verified($verified = false) {
        global $OUTPUT;

        if ($verified) {
            $apistatus = $OUTPUT->notification(get_string('connectionverified', 'collaborate'), 'notifysuccess');
        } else {
            $apistatus = $OUTPUT->notification(get_string('connectionfailed', 'collaborate'), 'notifyproblem');
        }

        $o = $OUTPUT->header();
        $o .= $apistatus;
        $o .= $OUTPUT->close_window_button(get_string('exitapidiagnostics', 'mod_collaborate'));
        $o .= $OUTPUT->footer();

        return $o;
    }

    /**
     * View action.
     *
     * @param stdClass $collaborate
     * @param stdClass $cm
     * @return string
     * @throws coding_exception
     */
    public function view_action($collaborate, $cm) {
        $actionview = new view_action($collaborate, $cm);
        return $this->render_view_action($actionview);
    }

    /**
     * @param view_action $actionview
     * @return string
     * @throws coding_exception
     */
    public function render_view_action(view_action $viewaction) {
        global $OUTPUT;
        $o = $OUTPUT->header();
        $collaborate = $viewaction->get_collaborate();
        $cm = $viewaction->get_cm();
        $canmoderate = $viewaction->get_canmoderate();
        $canparticipate = $viewaction->get_canparticipate();
        $unrestored = $collaborate->sessionid == null && $canparticipate;

        $o .= '<h2 class="activity-title">'.format_string($collaborate->name).'</h2>';
        $times = local::get_times($collaborate, true);
        $o .= self::meeting_status($times, $cm, $canmoderate, $canparticipate, $unrestored);

        $o .= '<hr />';

        // Conditions to show the intro can change to look for own settings or whatever.
        if ($collaborate->intro) {
            $o .= $OUTPUT->box(
                format_module_intro('collaborate', $collaborate, $cm->id),
                'generalbox mod_introbox', 'collaborateintro'
            );
        }

        $o .= $OUTPUT->footer();
        return $o;
    }

    /**
     * Render table of collaborate instances.
     *
     * @param $course
     * @param $strname
     * @return string
     */
    public function render_instance_table($course, $strname) {
        $usesections = course_format_uses_sections($course->format);

        $table = new html_table();
        $table->attributes['class'] = 'generaltable mod_index';

        if ($usesections) {
            $strsectionname = get_string('sectionname', 'format_'.$course->format);
            $table->head  = array ($strsectionname, $strname);
            $table->align = array ('center', 'left');
        } else {
            $table->head  = array ($strname);
            $table->align = array ('left');
        }

        $modinfo = get_fast_modinfo($course);
        $currentsection = '';
        foreach ($modinfo->instances['collaborate'] as $cm) {
            $row = array();
            if ($usesections) {
                if ($cm->sectionnum !== $currentsection) {
                    if ($cm->sectionnum) {
                        $row[] = get_section_name($course, $cm->sectionnum);
                    } else {
                        $row[] = '';
                    }
                    if ($currentsection !== '') {
                        $table->data[] = 'hr';
                    }
                    $currentsection = $cm->sectionnum;
                } else {
                    $row[] = '';
                }
            }

            $class = $cm->visible ? null : array('class' => 'dimmed');

            $row[] = html_writer::link(new moodle_url('view.php', array('id' => $cm->id)),
                $cm->get_formatted_name(), $class);
            $table->data[] = $row;
        }
        return html_writer::table($table);
    }
}
