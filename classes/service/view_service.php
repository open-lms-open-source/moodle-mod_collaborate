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

use mod_collaborate\event\course_module_viewed;
use mod_collaborate\service\base_visit_service;
use mod_collaborate\soap\api;
use mod_collaborate\local;

require_once(__DIR__.'/../../lib.php');

/**
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_service extends base_visit_service {

    /**
     * @var \mod_collaborate_renderer
     */
    protected $renderer;

    /**
     * Cosntructor.
     *
     * @param \stdClass $collaborate
     * @param \cm_info  $cm
     * @param \stdClass $user
     * @param \mod_collaborate_renderer $renderer
     */
    public function __construct(\stdClass $collaborate,
                                \cm_info $cm,
                                \stdClass $user) {

        global $PAGE;

        // Force general render target in case this is called via cli or via ajax.
        $this->renderer = $PAGE->get_renderer('mod_collaborate', null, RENDERER_TARGET_GENERAL);

        parent::__construct($collaborate, $cm, $user);
    }

    /**
     * Handle view action.
     *
     * @return string
     * @throws \coding_exception
     */
    public function handle_view() {
        global $PAGE;
        $PAGE->requires->js_call_amd('mod_collaborate/collaborate', 'init');

        $event = course_module_viewed::create(array(
            'objectid' => $this->cm->instance,
            'context' => $this->context,
        ));
        $event->add_record_snapshot('course', $this->course);
        $event->add_record_snapshot($this->cm->modname, $this->collaborate);
        $event->trigger();

        // If a collaborate session hasn't been created yet and we can moderate or add, then create it now.
        $this->moderator_ensure_session();

        // Completion tracking on view.
        $completion = new \completion_info($this->course);
        $completion->set_module_viewed($this->cm, $this->user->id);

        // Apply guest url to collaborate property.
        $this->apply_guest_url();

        return $this->renderer->view_action($this->collaborate, $this->cm);
    }

    /**
     * Apply guest url to collaborate property.
     */
    protected function apply_guest_url() {
        $url = local::guest_url($this->collaborate);
        $this->collaborate->guesturl = $url;
    }

}
