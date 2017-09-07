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
 * Prints a particular instance of collaborate
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package   mod_collaborate
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_collaborate\controller\view_controller;

require_once(__DIR__.'/../../config.php'); // @codingStandardsIgnoreLine Ignore require login check, handled elsewhere.
require_once(__DIR__.'/lib.php');

$action = optional_param('action', 'view', PARAM_ALPHA);

$vc = new view_controller($action);
if ($action == 'view') {
    $vc->view_action();
} else if ($action == 'forward') {
    $vc->forward_action();
} else {
    print_error('error:unknownaction', 'mod_collaborate');
}