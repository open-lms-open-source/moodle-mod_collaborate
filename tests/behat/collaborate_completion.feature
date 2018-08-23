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
# @copyright  Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@mod @mod_collaborate
Feature: Set one action as a completion condition for a Collab activity
  In order to ensure students are participating on sessions
  As a teacher
  I need to set a minimum number of conditions to mark the Collab activity as completed

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

  Scenario: Automatic view completion
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    And I add a "Collaborate" to section "1" and I fill the form with:
      | Session name | Test collaborate |
      | completion   | 2 |
      | completionview | 1 |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test collaborate"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test collaborate"
    And I navigate to "Edit settings" in current page administration
    Then I should see "Completion options locked"

  @javascript @_switch_window
  Scenario: Edit settings on Collab are not completing activities automatically
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    And I add a "Collaborate" to section "1" and I fill the form with:
      | Session name | Test collaborate second |
      | completion   | 2 |
      | completionlaunch | 1 |
    And I follow "Test collaborate second"
    And I navigate to "Edit settings" in current page administration
    And I press "Save and display"
    And I follow "Test collaborate second"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    Then I should not see "Completion options locked"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test collaborate second"
    And I click on "Join session" "link"
    And I wait to be redirected
    And I change to main window
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test collaborate second"
    # This pause is a workaround till MDL-58053 is fixed.
    And I wait "3" seconds
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    Then I should see "Completion options locked"
