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
 * Recording redirect script. Handles Collaborate recording views and downloads and fires events.
 *
 * @package    mod_collaborate
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use mod_collaborate\controller\recordings_controller;

require_once(__DIR__.'/../../config.php'); // @codingStandardsIgnoreLine Ignore require login check, handled elsewhere.
require_once(__DIR__.'/lib.php');

$action = optional_param('action', 'view', PARAM_ALPHAEXT);

$vc = new recordings_controller($action);
if ($action === 'download') {
    $vc->download_action();
} else if ($action === 'view') {
    $vc->view_action();
} else if ($action === 'delete') {
    $vc->delete_action();
} else if ($action === 'delete_confirmation') {
    $vc->delete_confirmation_action();
} else {
    print_error('error:unknownaction', 'mod_collaborate');
}


