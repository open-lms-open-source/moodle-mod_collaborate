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

namespace mod_collaborate\testables;

use mod_collaborate\traits\exportable;
use mod_collaborate\traits\testable_class;

defined('MOODLE_INTERNAL') || die();

/**
 * Testable exportable trait
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2016 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class trait_exportable {
    use exportable;
    use testable_class;

    /**
     * Test array
     * @var array
     */
    public $arr;

    public function __construct() {
        $this->arr = [
            300 => (object) ['url' => new \moodle_url('/profile.php'), 'name' => 'profile'],
            400 => (object) ['url' => new \moodle_url('/my'), 'name' => 'dashboard']
        ];
    }
}
