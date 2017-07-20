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
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user | course | role           |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student        |
      | student2 | C1 | student        |

  Scenario: Teacher creates a collaborate instance, adds recordings and can view / delete them. Student cannot delete
  recordings.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Collaborate" to section "1" and I fill the form with:
      | Session name | Test collab |
    And the following fake recordings exist for session "Test collab":
      | id | name       | starttime | endtime  |
      | 1  |Recording1  | +1 hours  | +2 hours |
      | 2  |Recording2  | +3 hours  | +4 hours |
    And I follow "Test collab"
    And I should see "Recordings" in the "#page h3" "css_element"
    And I should see "Recording1" in the ".collab-recording-list" "css_element"
    And I should see "Recording2" in the ".collab-recording-list" "css_element"
    And I click on ".mod-collaborate-delete[title='Delete recording entitled \"Recording1\"']" "css_element"
    And I should see "Are you sure you want to delete the recording entitled \"Recording1\"?"
    # Test canceling delete recording.
    And I press "Cancel"
    And I should see "Recording1" in the ".collab-recording-list" "css_element"
    And I should see "Recording2" in the ".collab-recording-list" "css_element"
    # Test student cannot delete recordings.
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test collab"
    And I should see "Recordings" in the "#page h3" "css_element"
    And I should see "Recording1" in the ".collab-recording-list" "css_element"
    And I should see "Recording2" in the ".collab-recording-list" "css_element"
    And ".mod-collaborate-delete[title='Delete recording entitled \"Recording1\"']" "css_element" should not exist
    And I log out
    # Log back in as teacher and test delete recording options visible.
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test collab"
    # Test delete recording.
    And I click on ".mod-collaborate-delete[title='Delete recording entitled \"Recording1\"']" "css_element"
    And I should see "Are you sure you want to delete the recording entitled \"Recording1\"?"
    And I press "Continue"
    And I should see "The recording entitled \"Recording1\" has been deleted."
    And I should see "Recordings" in the "#page h3" "css_element"
    And I should not see "Recording1" in the ".collab-recording-list" "css_element"
    And I should see "Recording2" in the ".collab-recording-list" "css_element"
    # Test deleting final recording.
    And I click on ".mod-collaborate-delete[title='Delete recording entitled \"Recording2\"']" "css_element"
    And I should see "Are you sure you want to delete the recording entitled \"Recording2\"?"
    When I press "Continue"
    Then I should see "The recording entitled \"Recording2\" has been deleted."
    # The recordings header should be gone.
    And "#page h3" "css_element" should not exist
    # There should be no recordings listed at all.
    And ".collab-recording-list" "css_element" should not exist

  Scenario: When groups are added to course, recordings are made and viewed accordingly.
    Given the following "groups" exist:
      | name    | course  | idnumber |
      | Group 1 | C1      | G1       |
      | Group 2 | C1      | G2       |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | student2 | G1    |
      | student2 | G2    |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Collaborate" to section "1" and I fill the form with:
      | Session name | Test collab |
    And the following fake recordings exist for session "Test collab":
      | id | name                 | starttime | endtime  | group   |
      | 1  |Recording1            | +1 hours  | +2 hours |         |
      | 2  |Recording for group 1 | +3 hours  | +4 hours | Group 1 |
      | 3  |Recording for group 2 | +4 hours  | +5 hours | Group 2 |
    And I follow "Test collab"
    # Teacher can see all recordings partitioned by a recording heading of instance or group name.
    And I should see "Recordings" in the "#page h3" "css_element"
    And recording "Recording1" should exist under heading "Test collab"
    And recording "Recording for group 1" should exist under heading "Group Group 1"
    And recording "Recording for group 2" should exist under heading "Group Group 2"
    And I log out
    # Student in 1 group sees recording list without recording heading.
    And I log in as "student1"
    And I follow "Test collab"
    And I should see "Recordings" in the "#page h3" "css_element"
    And recording "Recording1" should exist under heading "Test collab"
    And recording "Recording for group 1" should exist under heading "Group Group 1"
    And recording heading "Group Group 2" should not exist
    And I should see "Recording for group 1" in the ".collab-recording-list" "css_element"
    And I log out
    # Student in 2 groups sees recording list with recording heading.
    And I log in as "student2"
    And I follow "Test collab"
    And I should see "Recordings" in the "#page h3" "css_element"
    And recording "Recording1" should exist under heading "Test collab"
    And recording "Recording for group 1" should exist under heading "Group Group 1"
    And recording "Recording for group 2" should exist under heading "Group Group 2"
    And I log out
    # Test deleting recordings.
    And I log in as "teacher1"
    And I follow "Test collab"
    And I click on ".mod-collaborate-delete[title='Delete recording entitled \"Recording1\"']" "css_element"
    And I should see "Are you sure you want to delete the recording entitled \"Recording1\"?"
    # Test canceling delete recording.
    And I press "Cancel"
    And recording "Recording1" should exist under heading "Test collab"
    And recording "Recording for group 1" should exist under heading "Group Group 1"
    And recording "Recording for group 2" should exist under heading "Group Group 2"
    # Test student cannot delete recordings.
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test collab"
    And ".mod-collaborate-delete[title='Delete recording entitled \"Recording for group 1\"']" "css_element" should not exist
    And I log out
    # Log back in as teacher and test delete recording options visible.
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test collab"
    # Test delete recording.
    And I click on ".mod-collaborate-delete[title='Delete recording entitled \"Recording1\"']" "css_element"
    And I should see "Are you sure you want to delete the recording entitled \"Recording1\"?"
    And I press "Continue"
    And I should see "The recording entitled \"Recording1\" has been deleted."
    And I should see "Recordings" in the "#page h3" "css_element"
    And recording heading "Test collab" should not exist
    And I should not see "Recording1" in the ".collab-recording-list" "css_element"
    And recording "Recording for group 1" should exist under heading "Group Group 1"
    And recording "Recording for group 2" should exist under heading "Group Group 2"
    # Test deleting final recordings.
    And I click on ".mod-collaborate-delete[title='Delete recording entitled \"Recording for group 1\"']" "css_element"
    And I should see "Are you sure you want to delete the recording entitled \"Recording for group 1\"?"
    When I press "Continue"
    Then I should see "The recording entitled \"Recording for group 1\" has been deleted."
    And I should see "Recording for group 2"
    And I click on ".mod-collaborate-delete[title='Delete recording entitled \"Recording for group 2\"']" "css_element"
    And I should see "Are you sure you want to delete the recording entitled \"Recording for group 2\"?"
    When I press "Continue"
    Then I should see "The recording entitled \"Recording for group 2\" has been deleted."
    # The recordings header should be gone.
    And "#page h3" "css_element" should not exist
    # There should be no recordings listed at all.
    And ".collab-recording-list" "css_element" should not exist
