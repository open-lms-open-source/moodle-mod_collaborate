<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace mod_collaborate\iface;

use stdClass;
use cm_info;

/**
 * API Recordings interface.
 *
 * @author    Guy Thomas
 * @copyright Copyright (c) 2017 Open LMS
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package mod_collaborate
 */
interface api_recordings {

    /**
     * @param stdClass $collaborate
     * @param cm_info $cm
     * @param bool $canmoderate
     * @return mixed
     */
    public function get_recordings(stdClass $collaborate, cm_info $cm, $canmoderate = false);

    /**
     * @param string $recordingid
     * @return mixed
     */
    public function delete_recording($recordingid);
}
