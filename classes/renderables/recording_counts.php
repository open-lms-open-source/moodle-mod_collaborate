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
 * Simple recording count renderable model.
 *
 * @package    mod_collaborate
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\renderables;

defined('MOODLE_INTERNAL') || die();

/**
 * Simple recording count renderable model.
 *
 * @package    mod_collaborate
 * @copyright  Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recording_counts implements \renderable {
    /**
     * @var int
     */
    public $recordingid;

    /**
     * @var int
     */
    public $views = 0;

    /**
     * @var int
     */
    public $downloads = 0;

    /**
     * @var bool
     */
    public $candownload = false;

    /**
     * recording_counts constructor.
     * @param int $recordingid
     * @param bool $candownload
     */
    public function __construct($recordingid, $candownload = false) {
        $this->candownload = $candownload;
        $this->recordingid = $recordingid;
    }
}
