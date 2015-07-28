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

require_once(__DIR__.'/../../lib.php');

/**
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class forward_service {

    /**
     * @var int
     */
    protected $id;

    /**
     * @var \cm_info
     */
    protected $cm;

    /**
     * @var \stdClass
     */
    protected $course;

    /**
     * @var \stdClass
     */
    protected $context;

    /**
     * @var \stdClass
     */
    protected $collaborate;

    /**
     * @var api
     */
    protected $api;

    /**
     * @var \stdClass
     */
    protected $user;


    /**
     * Constructor
     *
     * @param \stdClass $collaborate
     * @param \cm_info $cm
     * @param \context_module $context;
     * @param \stdClass $user;
     * @throws \coding_exception
     * @throws \require_login_exception
     */
    public function __construct(\stdClass $collaborate, \cm_info $cm, \context_module $context, \stdClass $user) {
        global $DB;

        $this->collaborate = $collaborate;
        $this->course = $cm->get_course();
        $this->cm = $cm;
        $this->id = $cm->instance;
        $this->context = $context;
        $this->user = $user;

        $this->collaborate = $DB->get_record('collaborate', array('id' => $this->id), '*', MUST_EXIST);

        $this->api = api::get_api();
    }

    /**
     * Get url
     * @throws \coding_exception
     */
    public function handle_forward() {
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
     * Update attendee.
     *
     * @throws \coding_exception
     */
    protected function api_update_attendee() {
        $sessionid = $this->collaborate->sessionid;
        if (has_capability('mod/collaborate:moderate', $this->context)) {
            $role = 'moderator';
        } else if (has_capability('mod/collaborate:participate', $this->context)) {
            $role = 'participant';
        } else {
            return new \moodle_url('/mod/collaborate/view.php', ['id' => $this->cm->id]);
        }

        $attendee = new HtmlAttendee($this->user->id, $role);
        $attendee->setDisplayName(substr(fullname($this->user),0,80));
        $avatar = new \user_picture($this->user);
        // Note, we get the avatar url for the site instance and don't use the $PAGE object so that this function is
        // unit testable.
        $page = new \moodle_page();
        $page->set_context(\context_system::instance());
        $avatarurl = $avatar->get_url($page);
        $attendee->setAvatarUrl(new \SoapVar('<ns1:avatarUrl><![CDATA['.$avatarurl.']]></ns1:avatarUrl>', XSD_ANYXML));

        $satts = new UpdateHtmlSessionAttendee($sessionid, $attendee);
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

        $PAGE->set_url('/mod/collaborate/view.php', array(
            'id' => $this->cm->id,
            'action'    => 'view'
        ));

        $this->log_viewed_event();
        $url = $this->api_update_attendee();

        if (empty($url)) {
            $this->api->process_error(
                'error:failedtocreateurl', logging\constants::SEV_CRITICAL
            );
            return false;
        } else {

            if (!empty($USER->id)
                && $this->collaborate->completionlaunch
            ) {
                // Completion tracking on forward.
                $completion=new \completion_info($this->course);
                $completion->update_state($this->cm, COMPLETION_COMPLETE, $USER->id);

            }
            return $url;
        }
    }
}
