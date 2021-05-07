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
 * @package   mod_collaborate
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_collaborate\task\soap_migrator_task;

require_once(__DIR__.'/../../config.php');

require_login();
$context = context_system::instance();
$url = new moodle_url('/mod/collaborate/restmigration.php');
$PAGE->set_url($url);
$PAGE->set_context($context);
require_capability('moodle/site:config', $context);

$confirm = optional_param('confirm', null, PARAM_BOOL);

$settingurl = new \moodle_url('/admin/settings.php', ['section' => 'modsettingcollaborate']);
$migrationtask = \core\task\manager::get_adhoc_tasks('\\mod_collaborate\\task\\soap_migrator_task');
if (!empty($migrationtask)) {
    redirect($settingurl);
}

if ($confirm !== null && confirm_sesskey()) {
    $migrationtask = new soap_migrator_task();
    \core\task\manager::queue_adhoc_task($migrationtask, true);
    redirect($settingurl, get_string('soapmigrationmessage', 'mod_collaborate'));
}

echo $OUTPUT->header();
$confirmstring = get_string('soapmigrationconfirmation', 'mod_collaborate');
$confirmurl = new \moodle_url($PAGE->url, ['confirm' => 1]);
$cancelurl = new \moodle_url($PAGE->url);
echo $OUTPUT->confirm($confirmstring, $confirmurl, $cancelurl);
echo $OUTPUT->footer();
