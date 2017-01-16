# This file is part of Moodle - http://moodle.org/
#
# Moodle is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Moodle is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
#
# Behat feature for Collab recordings.
#
# @package    mod_collaborate
# @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@mod @mod_collaborate
Feature: Recordings are listed and can be deleted from collaborate sessions.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student        |

  Scenario: Teacher creates a collaborate instance, adds recordings and can view / delete them. Student cannot delete
    recordings.
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Collaborate" to section "1" and I fill the form with:
      | Session name | Test collab |
    And the following fake recordings exist for session "Test collab":
      | id | name       | starttime | endtime  |
      | 1  |Recording1  | +1 hours  | +2 hours |
      | 2  |Recording2  | +3 hours  | +4 hours |
    And I follow "Test collab"
    And I should see "Recordings" in the "h3" "css_element"
    And I should see "Recording1" in the ".collab-recording-list" "css_element"
    And I should see "Recording2" in the ".collab-recording-list" "css_element"
    And I click on ".mod-collaborate-delete[alt='Delete recording \"Recording1\"']" "css_element"
    And I should see "Are you sure you want to delete the recording entitled \"Recording1\"?"
    # Test canceling delete recording.
    And I press "Cancel"
    And I should see "Recording1" in the ".collab-recording-list" "css_element"
    And I should see "Recording2" in the ".collab-recording-list" "css_element"
    # Test student cannot delete recordings.
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And ".mod-collaborate-delete[alt='Delete recording \"Recording1\"']" "css_element" should not exist
    And I log out
    # Log back in as teacher and test delete recording options visible.
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test collab"
    # Test delete recording.
    And I click on ".mod-collaborate-delete[alt='Delete recording \"Recording1\"']" "css_element"
    And I should see "Are you sure you want to delete the recording entitled \"Recording1\"?"
    And I press "Continue"
    And I should see "The recording entitled \"Recording1\" has been deleted."
    And I should see "Recordings" in the "h3" "css_element"
    And I should not see "Recording1" in the ".collab-recording-list" "css_element"
    And I should see "Recording2" in the ".collab-recording-list" "css_element"
    # Test deleting final recording.
    And I click on ".mod-collaborate-delete[alt='Delete recording \"Recording2\"']" "css_element"
    And I should see "Are you sure you want to delete the recording entitled \"Recording2\"?"
    When I press "Continue"
    Then I should see "The recording entitled \"Recording2\" has been deleted."
    # The recordings header should be gone.
    And "h3" "css_element" should not exist
    # There should be no recordings listed at all.
    And ".collab-recording-list" "css_element" should not exist
