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
 * View controller.
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\controller;

defined('MOODLE_INTERNAL') || die();

use mod_collaborate\service\view_service;
use mod_collaborate\service\forward_service;


require_once(__DIR__.'/controller_abstract.php');

class view_controller extends controller_abstract {

    /**
     * @var \stdClass
     */
    protected $cm;

    /**
     * @var \stdClass|bool
     */
    protected $course;

    /**
     * @var \stdClass|bool
     */
    protected $collaborate;


    public function init() {
        global $PAGE;

        $this->set_properties();

        $PAGE->set_url(new \moodle_url('/mod/collaborate/view.php', ['id' => $this->cm->id, 'action' => $this->action]));

        parent::init();

        require_once(__DIR__.'/../../lib.php');

    }

    /**
     * Set class properties from params.
     *
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    protected function set_properties() {
        global $DB;

        // Set class properties from params.
        $id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
        $n  = optional_param('n', 0, PARAM_INT);  // collaborate instance ID.

        if ($id) {
            $cm         = get_coursemodule_from_id('collaborate', $id, 0, false, MUST_EXIST);
            $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
            $collaborate  = $DB->get_record('collaborate', array('id' => $cm->instance), '*', MUST_EXIST);
        } else if ($n) {
            $collaborate  = $DB->get_record('collaborate', array('id' => $n), '*', MUST_EXIST);
            $course     = $DB->get_record('course', array('id' => $collaborate->course), '*', MUST_EXIST);
            $cm         = get_coursemodule_from_instance('collaborate', $collaborate->id, $course->id, false, MUST_EXIST);
        } else {
            print_error('error:invalidmoduleid', 'mod_collaborate');
        }

        $this->cm = $cm;
        $this->course = $course;
        $this->collaborate = $collaborate;
    }

    /**
     * Do any security checks needed for the passed action
     *
     */
    public function require_capability() {
        require_login($this->course, true, $this->cm);
    }

    /**
     * View collaborate instance.
     *
     * @return void
     */
    public function view_action() {
        global $PAGE, $USER, $OUTPUT;

        $viewservice = new view_service($this->collaborate, $PAGE->cm, $USER);

        // Set up the page header.
        $PAGE->set_title(format_string($this->collaborate->name));
        $PAGE->set_heading(format_string($this->course->fullname));
        $PAGE->set_url('/mod/collaborate/view.php', array(
            'id' => $PAGE->cm->id,
            'action'    => 'view'
        ));

        // We get the content of the page before we output the header - otherwise set_module_viewed does not work.
        $view = $viewservice->handle_view();
        echo $OUTPUT->header();
        echo $view;
        echo $OUTPUT->footer();
    }

    /**
     * Forward to collaborate meeting session.
     */
    public function forward_action() {
        global $PAGE, $USER;

        $forwardservice = new forward_service($this->collaborate, $PAGE->cm, $USER);

        $PAGE->set_cacheable(false);
        $url = $forwardservice->handle_forward();
        if ($url) {
            redirect($url);
        }
    }
}
