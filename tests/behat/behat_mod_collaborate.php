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
 * Steps definitions for Collaborate module.
 *
 * @package   mod_collaborate
 * @category  test
 * @copyright Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;
use mod_collaborate\soap\fakeapi;

class behat_mod_collaborate extends behat_base {

    /**
     * Returns to Moodle tab after Joining collab session.
     * @Given /^I change to main window$/
     */
    public function i_change_to_main_window() {
        $session = $this->getSession();
        $mainwindow = $session->getWindowName();
        $session->switchToWindow($mainwindow);
    }

    /**
     * Deletes a group by name
     * @Given /^the group "([^"]*)" is deleted$/
     * @param $groupname
     */
    public function delete_group_by_name($groupname) {
        global $DB;
        $group = $DB->get_record('groups', ['name' => $groupname]);
        groups_delete_group($group);
    }

    /**
     * @Given /^I check the "([^"]*)" meeting group radio button$/
     * https://github.com/Behat/MinkExtension/issues/166
     */
    public function i_check_the_radio_button($labeltext) {
        $page = $this->getSession()->getPage();
        $radiobutton = $page->find('xpath', '//input[@data-group-name="'.$labeltext.'"]');
        if ($radiobutton) {
            $select = $radiobutton->getAttribute('name');
            $option = $radiobutton->getAttribute('value');
            $page->selectFieldOption($select, $option);
            return;
        }

        throw new \Exception("Radio button with label {$labeltext} not found");
    }

    /**
     * Creates fake recordings for testing purposes.
     * @param string $sessionname
     * @param TableNode $data
     * @Given /^the following fake recordings exist for session "(?P<element_string>(?:[^"]|\\")*)":$/
     */
    public function the_following_fake_recordings_exist($sessionname, TableNode $data) {
        global $DB;
        $sessionrow = $DB->get_record('collaborate', ['name' => $sessionname]);
        $sessionid = $sessionrow->sessionid;
        $api = fakeapi::get_api();
        $table = $data->getHash();
        foreach ($table as $rkey => $row) {

            if (isset($row['starttime'])) {
                $dti = new \DateTimeImmutable($row['starttime']);
            } else {
                $dti = new \DateTimeImmutable('+1 hours');
            }
            $starttime = $dti->format(\DateTime::ATOM);

            if (isset($row['endtime'])) {
                $dti = new \DateTimeImmutable($row['endtime']);
            } else {
                $dti = new \DateTimeImmutable('+1 hours');
            }
            $endtime = $dti->format(\DateTime::ATOM);

            $rowdefaults = [
                'id' => null,
                'starttime' => $starttime,
                'endtime' => $endtime,
                'name' => null
            ];

            $row = (object) array_replace($rowdefaults, $row);
            $trimname = trim($row->name);

            if (empty($trimname)) {
                $row->name = null;
            }

            $api->add_test_recording(
                $sessionid, $row->id, $row->starttime, $row->endtime, $row->name
            );
        }
    }
}
