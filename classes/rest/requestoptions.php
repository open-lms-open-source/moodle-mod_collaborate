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
 * Request options.
 * @author    Guy Thomas <osdev@blackboard.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_collaborate\rest;

defined('MOODLE_INTERNAL') || die();

class requestoptions {

    /**
     * @var string
     */
    public $bodyjson;

    /**
     * @var array
     */
    public $queryparams;

    /**
     * @var array
     */
    public $postfields;

    /**
     * @var array
     */
    public $pathparams;

    /**
     * requestoptions constructor.
     * @param string $bodyjson
     * @param array $queryparams
     * @param array $postfields
     */
    public function __construct($bodyjson = '', array $pathparams = [], array $queryparams = [], array $postfields = []) {
        $this->bodyjson = $bodyjson;
        $this->pathparams = $pathparams;
        $this->queryparams = $queryparams;
        $this->postfields = $postfields;
    }
}
