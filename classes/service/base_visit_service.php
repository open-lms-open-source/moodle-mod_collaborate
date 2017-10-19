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
 * Visit service - abstract class used for any service involved in visiting the session - i.e. viewing or forwarding.
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\service;

defined('MOODLE_INTERNAL') || die();

use mod_collaborate\local;

require_once(__DIR__.'/../../lib.php');

/**
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_visit_service {

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
    protected $collaborate;


    /**
     * @var \context_module
     */
    protected $context;

    /**
     * @var \stdClass
     */
    protected $user;


    /**
     * Constructor.
     *
     * @param \stdClass $collaborate
     * @param \cm_info $cm
     * @param \stdClass $user
     */
    public function __construct(\stdClass $collaborate,
                                \cm_info $cm,
                                \stdClass $user) {

        $this->collaborate = $collaborate;
        $this->cm = $cm;
        $this->context = \context_module::instance($this->cm->id);
        $this->course = $cm->get_course();
        $this->user = $user;
    }

    /**
     * Ensure a session exists for moderators.
     *
     * @throws \coding_exception
     */
    protected function moderator_ensure_session() {

        if ($this->collaborate->sessionid === null && $this->collaborate->sessionuid === null) {
            $sessionidkey = local::api_is_legacy() ? 'sessionid' : 'sessionuid';
        } else {
            return;
        }

        if ($this->collaborate->$sessionidkey === null) {
            $canmoderate = has_capability('mod/collaborate:moderate', $this->context);
            $canadd = has_capability('mod/collaborate:addinstance', $this->context);
            $capscreate = ($canmoderate || $canadd);
            if ($capscreate) {
                collaborate_update_instance($this->collaborate);
            }
        }
    }
}
