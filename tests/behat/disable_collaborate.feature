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
# Disable collaborate when a REST migration is on course.
#
# @package    mod_collaborate
# @author     Juan Ibarra
# @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@mod @mod_collaborate
Feature: Collaborate instances cannot be created when REST migration is on course.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Collaborate" to section "1" and I fill the form with:
      | Session name | Test collab  |
    Then I log out

  @javascript
  Scenario Outline: Collaborate instance cannot be created when REST migration is on course.
    Given the following config values are set as admin:
      | migrationstatus | <Status> | collaborate |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Collaborate" to section "1"
    Then I should see "Edition of collaborate sessions is disabled. Migration to REST in course"
    And "#page-mod-collaborate-mod section#region-main div[role='main']" "css_element" should not be visible
    Examples:
      | Status |
      | 1      |
      | 2      |
      | 3      |
      | 4      |

  @javascript
  Scenario: Collaborate instance can be created when REST migration is finished.
    Given the following config values are set as admin:
      | migrationstatus | 5 | collaborate |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Collaborate" to section "1"
    And I set the field "Session name" to "Test collab"
    Then I should not see "Edition of collaborate sessions is disabled. Migration to REST in course"
    And "#page-mod-collaborate-mod section#region-main div[role='main']" "css_element" should be visible

  @javascript
  Scenario Outline: Collaborate instance cannot be managed when REST migration is on course.
    Given the following config values are set as admin:
      | migrationstatus | <Status> | collaborate |
    And I log in as "teacher1"
    Then I am on "Course 1" course homepage
    And I follow "Test collab"
    Then I should see "Management of collaborate sessions is disabled. Migration to REST in course"
    And "#page-mod-collaborate-view section#region-main div[role='main'] div.container" "css_element" should not be visible
    Examples:
      | Status |
      | 1      |
      | 2      |
      | 3      |
      | 4      |

  @javascript
  Scenario: Collaborate instance can be managed when REST migration is finished.
    Given the following config values are set as admin:
      | migrationstatus | 5 | collaborate |
    And I log in as "teacher1"
    Then I am on "Course 1" course homepage
    And I follow "Test collab"
    Then I should not see "Management of collaborate sessions is disabled. Migration to REST in course"
    And "#page-mod-collaborate-view section#region-main div[role='main'] div.container" "css_element" should be visible
