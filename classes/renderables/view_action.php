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
 * View action
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\renderables;

defined('MOODLE_INTERNAL') || die();

class view_action implements \renderable{

    /**
     * @var \stdClass
     */
    protected $collaborate;

    /**
     * @var \cm_info
     */
    protected $cm;

    /**
     * @var \context_module
     */
    protected $context;

    /**
     * @var bool
     */
    protected $canadd;

    /**
     * @var bool
     */
    protected $canmoderate;

    /**
     * @var bool
     */
    protected $canparticipate;

    public function __construct($collaborate, $cm) {
        $this->collaborate = $collaborate;
        $this->cm = $cm;
        $this->context = \context_module::instance($cm->id);
        $this->canadd = has_capability('mod/collaborate:addinstance', $this->context);
        $this->canmoderate = has_capability('mod/collaborate:moderate', $this->context);
        $this->canparticipate = has_capability('mod/collaborate:participate', $this->context);
    }

    /**
     * @return \stdClass
     */
    public function get_collaborate() {
        return $this->collaborate;
    }

    /**
     * @return \cm_info
     */
    public function get_cm() {
        return $this->cm;
    }

    /**
     * @return \context_module
     */
    public function get_context() {
        return $this->context;
    }

    /**
     * @return bool
     */
    public function get_canadd() {
        return $this->canadd;
    }

    /**
     * @return bool
     */
    public function get_canmoderate() {
        return $this->canmoderate;
    }

    /**
     * @return mixed
     */
    public function get_canparticipate() {
        return $this->canparticipate;
    }

    /**
     * Get guest url if appropriate.
     * Return empty string if it should not be viewed.
     *
     * @return string $url
     */
    public function get_guest_url() {
        $canview = $this->canadd || $this->canmoderate;
        $guestaccessallowed = !empty($this->collaborate->guestaccessenabled);
        if ($canview && $guestaccessallowed && !empty($this->collaborate->guesturl)) {
            return $this->collaborate->guesturl;
        } else {
            return '';
        }
    }

}
