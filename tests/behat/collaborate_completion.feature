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
# Behat feature for Collaborate grade default
#
# @author     Rafael Monterroza
# @package    mod_collaborate
# @copyright  Copyright (c) 2016 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@mod @mod_collaborate
Feature: Set and validate activity completion options for a Collaborate activity
  In order to ensure that students are participating on Collaborate sessions
  As a teacher
  I need to set and validate activity completion options to mark the Collaborate activity as completed

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | First | teacher1@example.com |
      | student1 | Student | First | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  Scenario: Student must view the Collaborate activity to lock the Completion options
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    And the following "activity" exists:
      | activity        | collaborate           |
      | course          | C1                    |
      | section         | 1                     |
      | name            | Test collaborate      |
      | completion      | 2                     |
      | completionview  | 1                     |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test collaborate"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test collaborate"
    And I navigate to "Settings" in current page administration
    Then I should see "Completion options locked"

  @javascript @_switch_window
  Scenario: Student must launch the Collaborate activity to lock the Completion options
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    And the following "activity" exists:
      | activity         | collaborate             |
      | course           | C1                      |
      | section          | 1                       |
      | name             | Test collaborate second |
      | completion       | 2                       |
      | completionlaunch | 1                       |
    And I am on "Course 1" course homepage with editing mode on
    And I click on "span[data-value='Test collaborate second'] .stretched-link" "css_element"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    Then I should not see "Completion options locked"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test collaborate second"
    And I click on "Join session" "link"
    And I click on "button[data-action='newwindow']" "css_element"
    And I switch to the main window
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on ".activityname" "css_element"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    Then I should see "Completion options locked"