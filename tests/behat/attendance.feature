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
# Behat feature for Collab grade default
#
# @author     Rafael Monterroza
# @package    mod_collaborate
# @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@mod @mod_collaborate
Feature: Set one action as a completion condition for a Collab activity
  In order to ensure students are participating on sessions
  As a teacher
  I need to set a minimum number of conditions to mark the Collab activity as completed

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | First    | teacher1@example.com |
      | student1 | Student   | First    | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  Scenario: All data displayed in attendance feature is visible only for teachers
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Collaborate" to section "1" and I fill the form with:
      | Session name | Test collaborate |
    And the following fake attendees exist for session "Test collaborate":
      | id | username  | joined     | left        | role        |
      | 1  | teacher1  | +1 minutes | +20 minutes | moderator   |
      | 2  | student1  | +2 minutes | +15 minutes | participant |
    And I log out
    # Test student cannot see attendance.
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test collaborate"
    And I should not see "Attendee"
    And I should not see "Joined at"
    And I should not see "Online time"
    And I should not see "Attendance"
    And I log out
    # Test teacher is able to see attendance.
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test collaborate"
    And I should see "Joined at"
    And I should see "Online time"
    And I should see "Attendee"
    And I should see "19 mins" in the ".collab-attendance-table" "css_element"
    And I should see "13 mins" in the ".collab-attendance-table" "css_element"
    And I should see "Attendance"

  Scenario: Duplicated activity must not inherit attendance from original.
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Collaborate" to section "1" and I fill the form with:
      | Session name | Testing collaborate |
    And the following fake attendees exist for session "Testing collaborate":
      | id | username  | joined     | left        | role        |
      | 1  | teacher1  | +1 minutes | +23 minutes | moderator   |
      | 2  | student1  | +1 minutes | +21 minutes | participant |
    # Test teacher is able to see attendance.
    And I follow "Testing collaborate"
    And I should see "Attendee"
    And I should see "Joined at"
    And I should see "Online time"
    And I follow "Course 1"
    And I duplicate "Testing collaborate" activity editing the new copy with:
      | Session name | Testing collaborate 2 |
    # Test duplicated did not inherit attendance data.
    And I follow "Testing collaborate 2"
    And I should not see "Attendee"
    And I should not see "Joined at"
    Then I should not see "Online time"