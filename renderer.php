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
use mod_collaborate\renderables\copyablelink;
use mod_collaborate\renderables\meetingstatus;
use mod_collaborate\renderables\recording;
use mod_collaborate\renderables\recording_counts;
use mod_collaborate\recording_counter;
use mod_collaborate\local;
use mod_collaborate\sessionlink;

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
     * Render meeting status.
     * @param meetingstatus $meetingstatus
     * @return bool|string
     * @throws moodle_exception
     */
    public function render_meetingstatus(meetingstatus $meetingstatus) {
        return $this->render_from_template('collaborate/meetingstatus', $meetingstatus->export_for_template($this));
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
        $startyear = userdate($times->start, '%y');
        $endyear = userdate($times->end, '%y');
        $startmonth = userdate($times->start, '%m');
        $endmonth = userdate($times->end, '%m');

        $openended = local::timeend_open_ended($times->end);

        $startiscurrentyear = $startyear === userdate(time(), '%y');
        if ($startiscurrentyear && ($openended  || $startyear === $endyear)) {
            $datesstr = $this->datetime($times->start);
        } else {
            $visualstart = userdate($times->start, get_string('strftimedatetime', 'langconfig'));
            $datesstr = $this->datetime($times->start, $visualstart);
        }

        if ($startday === $endday && $startmonth === $endmonth && $startyear === $endyear) {
            $datesstr .= ' - '.$this->datetime($times->end, $endtime);
        } else {
            if ($openended) {
                $datesstr .= ' ('.get_string('openended', 'mod_collaborate').')';
            } else {
                if ($startyear === $endyear) {
                    $datesstr .= ' - ' . $this->datetime($times->end);
                } else {
                    $visualend = userdate($times->end, get_string('strftimedatetime', 'langconfig'));
                    $datesstr .= ' - ' . $this->datetime($times->end, $visualend);
                }
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
            $apistatus = $OUTPUT->notification(get_string('connectionverified', 'collaborate'), 'success');
        } else {
            $apistatus = $OUTPUT->notification(get_string('connectionfailed', 'collaborate'), 'error');
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
     * @param view_action $viewaction
     * @return string
     * @throws coding_exception
     */
    public function render_view_action(view_action $viewaction) {
        global $OUTPUT;

        $collaborate = $viewaction->get_collaborate();
        $cm = $viewaction->get_cm();
        $canmoderate = $viewaction->get_canmoderate();
        $canparticipate = $viewaction->get_canparticipate();

        $o = '<h2 class="activity-title">'.format_string($collaborate->name).'</h2>';
        $times = local::get_times($collaborate);

        $meetingstatus = new meetingstatus($times, $viewaction);
        $o .= $this->render($meetingstatus);

        // Conditions to show the intro can change to look for own settings or whatever.
        if (!empty($collaborate->intro)) {
            $o .= '<hr />';
            $o .= $OUTPUT->box(
                format_module_intro('collaborate', $collaborate, $cm->id),
                'generalbox mod_introbox', 'collaborateintro'
            );
            $o .= '<hr />';
        }

        // Guest url.
        $guesturl = $viewaction->get_guest_url();
        if ($guesturl) {
            $clink = new copyablelink(get_string('guestlink', 'mod_collaborate'), 'guestlink', $guesturl);
            $o .= $this->render($clink);
        }

        // Recordings.
        if ($canparticipate) {
            if (empty($collaborate->sessionuid) && !empty($collaborate->sessionid)) {
                $api = local::get_api(false, null, 'soap');
            } else {
                $api = local::get_api();
            }
            $sessionrecordings = $api->get_recordings($collaborate, $cm, $canmoderate);
            if (!empty($sessionrecordings)) {
                $o .= $this->render_recordings($collaborate, $sessionrecordings, $cm, $canmoderate);
            }
        }

        return $o;
    }

    /**
     * Render recordings.
     *
     * @param stdClass $collaborate
     * @param recording[][] $sessionrecordings
     * @param \cm_info $cm
     * @param boolean $canmoderate
     * @return string
     */
    public function render_recordings(stdClass $collaborate, array $sessionrecordings, $cm, $canmoderate) {
        $allrecordings = [];
        foreach ($sessionrecordings as $recordings) {
            $allrecordings = array_merge($allrecordings, $recordings);
        }
        if (empty($allrecordings)) {
            return '';
        }

        $header = get_string('recordings', 'mod_collaborate');
        $output = "<h3>$header</h3>";
        $output .= '<ul class="collab-recording-list">';
        $viewstr = get_string('viewrec', 'collaborate');
        $downloadstr = get_string('downloadrec', 'collaborate');

        $sessionfield = local::select_sessionid_or_sessionuid($collaborate);
        $mainsessionid = $collaborate->$sessionfield;
        $sessiontitles = sessionlink::get_titles_by_sessionids(array_keys($sessionrecordings), $mainsessionid, $sessionfield);

        $output .= '<hr />';

        foreach ($sessiontitles as $sessionid => $sessiontitle) {
            if ($sessionfield === 'sessionid') {
                // Remove prefix char (we needed it to sort the titles but we don't now!).
                $sessionid = str_replace('_', '', $sessionid);
            }

            if (empty($sessionrecordings[$sessionid])) {
                continue;
            }

            $recordings = $sessionrecordings[$sessionid];

            if ($sessionfield === 'sessionid') {
                $sessionlinkrow = sessionlink::get_session_link_row_by_sessionid($sessionid);
            } else {
                $sessionlinkrow = sessionlink::get_session_link_row_by_sessionuid($sessionid);
            }

            if (!$sessionlinkrow) {
                throw new coding_exception('Unable to get session link row for sessionid '.$sessionid);
            }

            // Only segregate by titles if there are multiple sessions per this instance.
            $output .= '<h4>' . $sessiontitle . '</h4>';

            foreach ($recordings as $recording) {

                $name = $recording->name;
                if (preg_match('/^recording_\d+$/', $name)) {
                    $name = str_replace('recording_', '', get_string('recording', 'collaborate', $name));
                }
                $datetimestart = userdate($recording->starttime);
                $duration = format_time($recording->duration);

                $params = ['c' => $cm->instance, 'action' => 'view', 'rid' => $recording->id,
                    'url' => urlencode($recording->viewurl), 'sesskey' => sesskey(),
                    'sessionlinkid' => $sessionlinkrow->id
                ];

                $viewurl = new moodle_url('/mod/collaborate/recordings.php', $params);

                $output .= '<li class="collab-recording-list-item">';
                $output .= '<a alt="' . s($viewstr) . '" href="' . $viewurl . '" target="_blank">' .
                    format_string($name) . '</a> ';
                $output .= '[' . $duration . ']';

                if (!empty($recording->downloadurl)) {
                    $params = ['c' => $cm->instance, 'action' => 'download', 'rid' => $recording->id,
                        'url' => urlencode($recording->downloadurl), 'sesskey' => sesskey(),
                        'sessionlinkid' => $sessionlinkrow->id
                    ];
                    $dlurl = new moodle_url('/mod/collaborate/recordings.php', $params);
                    $output .= '<a aria-label="' . s($downloadstr) . '" title="'.s($downloadstr).'"'.
                        '" class="mod-collaborate-download" href="' . $dlurl . '" target="_blank" role="button"></a>';
                }

                if (has_capability('mod/collaborate:deleterecordings', $cm->context)) {
                    $params = ['c' => $cm->instance, 'action' => 'view', 'rid' => $recording->id,
                        'url' => urlencode($recording->viewurl), 'sesskey' => sesskey(),
                        'sessionlinkid' => $sessionlinkrow->id, 'action' => 'delete', 'rname' => $name
                    ];
                    $deleteurl = new moodle_url('/mod/collaborate/recordings.php', $params);

                    $deldesc = s(get_string('deleterecording', 'mod_collaborate', $name));
                    $output .= '<a aria-label="' . $deldesc . '" title="' . $deldesc .
                        '" class="mod-collaborate-delete" href="' . $deleteurl . '" role="button"></a>';
                }

                $output .= '<br>' . $datetimestart . '<br>';
                if (!empty($recording->count) && $canmoderate) {
                    $output .= $this->render($recording->count);
                }
                $output .= '</li>';
            }
        }
        $output .= '</ul>';
        return $output;
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

    /**
     * Render recent activity
     * This code is copied
     *
     * @author: Guy Thomas
     * @param $activity
     * @param $courseid
     * @param $detail
     * @param $modnames
     * @return string
     */
    public function recent_activity($activity, $courseid, $detail, $modnames) {
        global $CFG, $OUTPUT;

        $o = '';
        $o .= '<table border="0" cellpadding="3" cellspacing="0" class="collaborate-recent">';

        $o .= '<tr><td class="userpicture" valign="top">';
        $o .= $OUTPUT->user_picture($activity->user);
        $o .= '</td><td>';

        if ($detail) {
            $modname = $modnames[$activity->type];
            $o .= '<div class="title">';
            $o .= '<img src="' . $OUTPUT->image_url('icon', 'collaborate') . '" '.
                'class="icon" alt="' . $modname . '">';
            $o .= '<a href="' . $CFG->wwwroot . '/mod/collaborate/view.php?id=' . $activity->cmid . '">';
            $o .= $activity->name;
            $o .= '</a>';
            $o .= '</div>';
        }

        $o .= '<div class="user">';
        $o .= "<a href=\"$CFG->wwwroot/user/view.php?id={$activity->user->id}&amp;course=$courseid\">";
        $o .= "{$activity->user->fullname}</a>  - " . userdate($activity->timestamp);
        $o .= '</div>';

        $o .= '</td></tr></table>';
        return $o;
    }

    /**
     * @param $url
     * @return bool|string
     * @throws moodle_exception
     */
    public function render_copyablelink(copyablelink $clink) {
        return $this->render_from_template('collaborate/copyablelink', $clink);
    }

    /**
     * API diagnostics - status + msg templates.
     *
     * @return string
     */
    public function api_diagnostics() {
        global $OUTPUT;

        $o = '<div id="api_diag">';
        $o .= '<div class="noticetemplate_problem">'.$OUTPUT->notification('', 'error').'</div>';
        $o .= '<div class="noticetemplate_success">'.$OUTPUT->notification('', 'success').'</div>';
        $o .= '<div class="noticetemplate_message">'.$OUTPUT->notification('', 'info').'</div>';
        $o .= '<div class="api-connection-status"></div>';
        $o .= '</div>';
        return $o;
    }

    /**
     * @param recording_counts $counts
     * @return string
     */
    public function render_recording_counts(recording_counts $counts) {
        if (isset($counts->downloads) && $counts->candownload) {
            return get_string('recordingcountsincdownloads', 'mod_collaborate', $counts);
        } else {
            return get_string('recordingcounts', 'mod_collaborate', $counts);
        }

    }
}
