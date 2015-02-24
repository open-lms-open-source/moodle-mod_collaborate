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

require_once(__DIR__.'/../../lib.php');

/**
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_service {

    /**
     * @var \mod_collaborate_renderer
     */
    protected $renderer;

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
     * Cosntructor.
     *
     * @param \stdClass $collaborate
     * @param \stdClass $cm
     * @param \context_module $context
     * @param \mod_collaborate_renderer $renderer
     */
    public function __construct(\stdClass $collaborate,
                                \cm_info $cm,
                                \context_module $context,
                                \mod_collaborate_renderer $renderer) {
        $this->collaborate = $collaborate;
        $this->cm = $cm;
        $this->context = $context;
        $this->renderer = $renderer;
        $this->course = $cm->get_course();
    }

    /**
     * Handle view action.
     *
     * @return string
     * @throws \coding_exception
     */
    public function handle_view() {
        $event = course_module_viewed::create(array(
            'objectid' => $this->cm->instance,
            'context' => $this->context,
        ));
        $event->add_record_snapshot('course', $this->course);
        $event->add_record_snapshot($this->cm->modname, $this->collaborate);
        $event->trigger();

        if ($this->collaborate->sessionid === null
            && has_capability('mod/collaborate:addinstance', $this->context)) {
                collaborate_update_instance($this->collaborate);
        }

        // Completion tracking on view.
        $completion=new \completion_info($this->course);
        $completion->set_module_viewed($this->cm);

        return $this->renderer->view_action($this->collaborate, $this->cm);
    }

}
