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
 * View services
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\service;

defined('MOODLE_INTERNAL') || die();

use mod_collaborate\soap\api;
use mod_collaborate\soap\generated\HtmlAttendee;
use mod_collaborate\soap\generated\UpdateHtmlSessionAttendee;
use mod_collaborate\event;
use mod_collaborate\logging;
use mod_collaborate\sessionlink;
use mod_collaborate\service\base_visit_service;

require_once(__DIR__.'/../../lib.php');

/**
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class forward_service extends base_visit_service {

    /**
     * @var int
     */
    protected $id;

    /**
     * @var api
     */
    protected $api;

    /**
     * Constructor
     *
     * @param \stdClass $collaborate
     * @param \cm_info $cm
     * @param \stdClass $user;
     * @throws \coding_exception
     * @throws \require_login_exception
     */
    public function __construct(\stdClass $collaborate,
                                \cm_info $cm,
                                \stdClass $user) {

        parent::__construct($collaborate, $cm, $user);

        $this->api = api::get_api();
    }

    /**
     * Get url
     * @throws \coding_exception
     */
    public function handle_forward() {
        if (!confirm_sesskey()) {
            throw new \moodle_exception('confirmsesskeybad', 'error');
        }

        // If a collaborate session hasn't been created yet and we can moderate or add, then create it now.
        $this->moderator_ensure_session();

        return $this->forward();
    }

    /**
     * Log event.
     *
     * @throws \coding_exception
     */
    protected function log_viewed_event() {
        $event = event\session_launched::create(array(
            'objectid' => $this->cm->instance,
            'context' => $this->context,
            'other' => ['session' => $this->collaborate->sessionid],
        ));
        $event->add_record_snapshot('course', $this->course);
        $event->add_record_snapshot($this->cm->modname, $this->collaborate);
        $event->trigger();
    }

    /**
     * Update attendee for a specific session.
     * @param int $sessionid
     *
     * @throws \coding_exception
     */
    protected function api_update_attendee($sessionid) {
        if (has_capability('mod/collaborate:moderate', $this->context)) {
            $role = 'moderator';
        } else if (has_capability('mod/collaborate:participate', $this->context)) {
            $role = 'participant';
        } else {
            return new \moodle_url('/mod/collaborate/view.php', ['id' => $this->cm->id]);
        }

        $attendee = new HtmlAttendee($this->user->id, $role);
        $attendee->setDisplayName(\core_text::substr(fullname($this->user), 0, 80));
        $avatar = new \user_picture($this->user);
        // Note, we get the avatar url for the site instance and don't use the $PAGE object so that this function is
        // unit testable.
        $page = new \moodle_page();
        $page->set_context(\context_system::instance());
        $avatarurl = $avatar->get_url($page);
        $attendee->setAvatarUrl(new \SoapVar('<ns1:avatarUrl><![CDATA['.$avatarurl.']]></ns1:avatarUrl>', XSD_ANYXML));

        $satts = new UpdateHtmlSessionAttendee($sessionid, $attendee);
        $satts->setLocale(current_language());
        $result = $this->api->UpdateHtmlSessionAttendee($satts);

        if (!$result || !method_exists($result, 'getUrl')) {
            return false;
        }
        $url = $result->getUrl();
        return ($url);
    }

    /**
     * Get url for forwarding to meeting room.
     *
     * @throws \coding_exception
     * @return bool|string
     */
    protected function forward() {
        global $PAGE, $USER;

        $context = \context_course::instance($this->course->id);
        $aag = has_capability('moodle/site:accessallgroups', $context);
        if ($aag) {
            $groups = groups_get_all_groups($this->cm->get_course()->id);
        } else {
            $groups = groups_get_all_groups($this->cm->get_course()->id, $USER->id);
        }

        $groupid = optional_param('group', -1, PARAM_INT);
        $group = false;

        $groupsession = $this->cm->groupmode > NOGROUPS && !($aag && $groupid === 0);

        if ($groupsession) {
            if (count($groups) === 1) {
                $group = reset($groups);
            } else if (count($groups) > 1) {
                if (isset($groups[$groupid])) {
                    $group = $groups[$groupid];
                } else {
                    throw new \coding_exception('Request for invalid group id', $groupid);
                }
            }
        }

        if ($group) {
            $sessionlink = sessionlink::get_group_session_link($this->collaborate, $group->id);
            $sessionid = $sessionlink->sessionid;
        } else {
            $sessionid = $this->collaborate->sessionid;
        }

        $PAGE->set_url('/mod/collaborate/view.php', array(
            'id' => $this->cm->id,
            'action'    => 'view'
        ));

        $this->log_viewed_event();
        $url = $this->api_update_attendee($sessionid);

        if (empty($url)) {
            $this->api->process_error(
                'error:failedtocreateurl', logging\constants::SEV_CRITICAL
            );
            return false;
        } else {

            if (!empty($USER->id) && $this->collaborate->completionlaunch) {
                // Completion tracking on forward.
                $completion = new \completion_info($this->course);
                $completion->update_state($this->cm, COMPLETION_COMPLETE, $USER->id);
            }
            return $url;
        }
    }
}
