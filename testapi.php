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
 * Tests the global collaborate api settings.
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use mod_collaborate\controller\api_controller;

// @codingStandardsIgnoreStart
if ((!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'], 'XMLHttpRequest') === 0)) {
    define('AJAX_SCRIPT', true);
    define('NO_DEBUG_DISPLAY', true);
}
// @codingStandardsIgnoreEnd

require_once(__DIR__.'/../../config.php'); // @codingStandardsIgnoreLine Ignore require login check, handled elsewhere.
require_once(__DIR__.'/lib.php');

global $PAGE;

$apic = new api_controller('test');

echo $apic->test_action();