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
 * Http code validation error
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\rest;

defined('MOODLE_INTERNAL') || die();

use mod_collaborate\logging\constants;

class http_validation_code_error {
    /**
     * @var string
     */
    public $errorcode;

    /**
     * To be inserted into error string.
     * @var string
     */
    public $a;

    /**
     * Additional debug info to be used on error.
     * @var array
     */
    public $debuginfo = [];

    /**
     * @var int
     */
    public $severity;

    public function __construct($errorcode, $a = '', $severity = constants::SEV_CRITICAL, array $debuginfo = null) {
        $this->errorcode = $errorcode;
        $this->a = $a;
        $this->severity = $severity;
        $this->debuginfo = $debuginfo;
    }
}
