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
 * End point for fake guest urls.
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../../config.php'); // @codingStandardsIgnoreLine Ignore require login check.

defined('BEHAT_SITE_RUNNING') || die();

$sessionid = required_param('id', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
global $DB, $PAGE, $OUTPUT;

$sessionlink = $DB->get_record('collaborate_sessionlink', ['sessionid' => $sessionid]);

$PAGE->set_url(new moodle_url('/mod/collaborate/tests/fixtures/fakeurl.php', ['id' => 1, 'userid' => 802000]));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Fake join meeting endpoint');
$PAGE->set_pagelayout('incourse');

echo $OUTPUT->header();

$OUTPUT->heading ('Fake join meeting endpoint');

if (!empty($sessionlink->groupid)) {
    $group = $DB->get_record('groups', ['id' => $sessionlink->groupid]);
    echo ('<p>Joined a fake session for group "'.$group->name.'"</p>');
} else {
    echo ('<p>Joined a fake session for the collaborate instance</p>');
}

echo $OUTPUT->footer();
