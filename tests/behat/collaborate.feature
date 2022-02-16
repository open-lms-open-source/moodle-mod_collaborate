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
# Behat feature for Collab instances.
#
# @package    mod_collaborate
# @author     Guy Thomas
# @copyright  Copyright (c) 2017 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@mod @mod_collaborate
Feature: Collaborate instances can be created by teachers and joined by students.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student1@example.com |
      | student3 | Student   | 3        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
    And the following "groups" exist:
      | name    | course  | idnumber |
      | Group 1 | C1      | G1       |
      | Group 2 | C1      | G2       |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | student2 | G1    |
      | student2 | G2    |

  Scenario: Collaborate instance can be created with various durations.
    Given I log in as "teacher1"
    # Test 30 minutes duration
    And the following "activity" exists:
      | activity        | collaborate           |
      | course          | C1                    |
      | section         | 1                     |
      | name            | Test collab 30 mins   |
      | duration        | 1800                  |
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test collab 30 mins"
    And I should see Collaborate time span of "30 minutes"
    # Test 1 hour duration
    And the following "activity" exists:
      | activity        | collaborate           |
      | course          | C1                    |
      | section         | 1                     |
      | name            | Test collab 1 hour    |
      | duration        | 3600                  |
    And I am on "Course 1" course homepage
    And I follow "Test collab 1 hour"
    And I should see Collaborate time span of "1 hour"
    # Test duration of course
    And the following "activity" exists:
      | activity        | collaborate                  |
      | course          | C1                           |
      | section         | 1                            |
      | name            | Test collab duration course  |
      | duration        | 9999                         |
    And I am on "Course 1" course homepage
    And I follow "Test collab duration course"
    And I should see Collaborate time span of "duration of course"

  Scenario: Collaborate instance with group mode enabled shows appropriate options for joining session.
    Given I log in as "teacher1"
    And the following "activity" exists:
      | activity        | collaborate           |
      | course          | C1                    |
      | section         | 1                     |
      | name            | Test collab           |
      | groupmode       | 1                     |
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test collab"
    And I should see "No group" in the ".mod-collaborate-group-selector" "css_element"
    And I should see "Group 1" in the ".mod-collaborate-group-selector" "css_element"
    And I should see "Group 2" in the ".mod-collaborate-group-selector" "css_element"
    And ".mod-collaborate-group-selector input[value=\"Join session\"]" "css_element" should exist
    # Note, if you run this scenario with an @javascript tag it breaks on the redirect after pressing "Join session".
    And I press "Join session"
    And I should see "Joined a fake session for the collaborate instance"
    And I log out
    # Log in as student and make sure student doesn't see any group selectors if they are only in one group.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test collab"
    And ".mod-collaborate-group-selector" "css_element" should not exist
    And I should see "Join session" in the "a.btn-success" "css_element"
    And I follow "Join session"
    And I should see "Joined a fake session for group \"Group 1\""
    And I log out
    # Log in as student and make sure student sees group selectors if they are in more than one group.
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test collab"
    And ".mod-collaborate-group-selector" "css_element" should exist
    And I should not see "No group" in the ".mod-collaborate-group-selector" "css_element"
    And I should see "Group 1" in the ".mod-collaborate-group-selector" "css_element"
    And I should see "Group 2" in the ".mod-collaborate-group-selector" "css_element"
    And ".mod-collaborate-group-selector input[value=\"Join session\"]" "css_element" should exist
    And I press "Join session"
    And I should see "Joined a fake session for group \"Group 1\""
    And I am on "Course 1" course homepage
    And I follow "Test collab"
    And I set the field "group" to "Group 2"
    And I press "Join session"
    And I should see "Joined a fake session for group \"Group 2\""
    And I log out
    # Log in as student who isn't in any groups and make sure they join the main session.
    And I log in as "student3"
    And I am on "Course 1" course homepage
    And I follow "Test collab"
    And ".mod-collaborate-group-selector" "css_element" should not exist
    And I should see "Join session" in the "a.btn-success" "css_element"
    When I follow "Join session"
    Then I should see "Joined a fake session for the collaborate instance"

  Scenario: Collaborate - deleting a group removes the group from the list of available groups when joining a session.
    Given I log in as "teacher1"
    And the following "activity" exists:
      | activity        | collaborate                  |
      | course          | C1                           |
      | section         | 1                            |
      | name            | Test collab                  |
      | groupmode       | 1                            |
    And I am on "Course 1" course homepage with editing mode on
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test collab"
    And ".mod-collaborate-group-selector" "css_element" should exist
    And I should see "Group 1" in the ".mod-collaborate-group-selector" "css_element"
    And I should see "Group 2" in the ".mod-collaborate-group-selector" "css_element"
    And the group "Group 1" is deleted
    And I reload the page
    # Student will no longer see any options as they are only in 1 group.
    And ".mod-collaborate-group-selector" "css_element" should not exist
    And I follow "Join session"
    And I should see "Joined a fake session for group \"Group 2\""

  Scenario: Collaborate - duplicating an instance makes groups available post duplication.
    Given I log in as "teacher1"
    And the following "activity" exists:
      | activity        | collaborate                  |
      | course          | C1                           |
      | section         | 1                            |
      | name            | Test collab                  |
      | groupmode       | 1                            |
    And I am on "Course 1" course homepage with editing mode on
    And I duplicate "Test collab" activity
    # Make sure duplicated collaborate works.
    And I follow "Test collab (copy)"
    And ".mod-collaborate-group-selector" "css_element" should exist
    And I should see "No group" in the ".mod-collaborate-group-selector" "css_element"
    And I should see "Group 1" in the ".mod-collaborate-group-selector" "css_element"
    And I should see "Group 2" in the ".mod-collaborate-group-selector" "css_element"
    And ".mod-collaborate-group-selector input[value=\"Join session\"]" "css_element" should exist
    And I press "Join session"
    And I should see "Joined a fake session for the collaborate instance"
    And I log out
    # Log in as student and make sure student doesn't see any group selectors if they are only in one group.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test collab (copy)"
    And ".mod-collaborate-group-selector" "css_element" should not exist
    And I should see "Join session" in the "a.btn-success" "css_element"
    And I follow "Join session"
    And I should see "Joined a fake session for group \"Group 1\""
    And I log out
    # Log in as student and make sure student sees group selectors if they are in more than one group.
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test collab (copy)"
    And ".mod-collaborate-group-selector" "css_element" should exist
    And I should not see "No group" in the ".mod-collaborate-group-selector" "css_element"
    And I should see "Group 1" in the ".mod-collaborate-group-selector" "css_element"
    And I should see "Group 2" in the ".mod-collaborate-group-selector" "css_element"
    And ".mod-collaborate-group-selector input[value=\"Join session\"]" "css_element" should exist
    And I press "Join session"
    And I should see "Joined a fake session for group \"Group 1\""
    And I am on "Course 1" course homepage
    And I follow "Test collab (copy)"
    And I set the field "group" to "Group 2"
    When I press "Join session"
    Then I should see "Joined a fake session for group \"Group 2\""

  Scenario: Collaborate instance with group mode enabled and guest access should display nav tabs for teachers.
    Given I log in as "teacher1"
    And the following "activity" exists:
      | activity                 | collaborate         |
      | course                   | C1                  |
      | section                  | 1                   |
      | name                     | Test collab         |
      | groupmode                | 1                   |
      | guestaccessenabled       | 1                   |
      | guestrole                | pr                  |
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test collab"
    And "#maintab" "css_element" should exist
    And "#guesttab" "css_element" should exist
    And I follow "Guest links"
    And I should see "No group"
    And I should see "Group 1"
    And I should see "Group 2"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test collab"
    And "#maintab" "css_element" should not exist
    And "#guesttab" "css_element" should not exist

  Scenario: Collaborate instance can be created with default instructor settings and edit the settings.
    Given the following config values are set as admin:
      | instructorsettingstoggle | 1 | collaborate |
      | canpostmessages          | 0 | collaborate |
      | canannotatewhiteboard    | 0 | collaborate |
      | cansharevideo            | 0 | collaborate |
      | canshareaudio            | 0 | collaborate |
      | candownloadrecordings    | 0 | collaborate |
    And I log in as "teacher1"
    And the following "activity" exists:
      | activity                 | collaborate                      |
      | course                   | C1                               |
      | section                  | 1                                |
      | name                     | Test collab Instructor settings  |
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test collab Instructor settings"
    And I click on "#region-main-box .action-menu-trigger .dropdown .dropdown-toggle" "css_element"
    And I click on "Edit settings" "link"
    And I should see "Instructor Settings"
    And I should see "Post messages"
    And I should see "Annotate on the whiteboard"
    And I should see "Share video feed"
    And I should see "Share audio feed"
    And I should see "Download recordings"
    And I should see "Enable sessions to allocate up to 500 participants"
    And I set the following fields to these values:
      | Post messages                                       | 1 |
      | Annotate on the whiteboard                          | 1 |
      | Share video feed                                    | 1 |
      | Share audio feed                                    | 1 |
      | Download recordings                                 | 1 |
      | Enable sessions to allocate up to 500 participants  | 1 |
    And I should see "Post messages"
    And I click on "Save and display" "button"
    And I click on "#region-main-box .action-menu-trigger .dropdown .dropdown-toggle" "css_element"
    And I click on "Edit settings" "link"
    Then the following fields match these values:
      | Post messages                                       | 1 |
      | Annotate on the whiteboard                          | 1 |
      | Share video feed                                    | 1 |
      | Share audio feed                                    | 1 |
      | Download recordings                                 | 1 |
      | Enable sessions to allocate up to 500 participants  | 1 |

  Scenario Outline: Collaborate instance enables large sessions only when there are no groups on common module settings.
    Given the following config values are set as admin:
      | instructorsettingstoggle | 1 | collaborate |
      | canpostmessages          | 0 | collaborate |
      | canannotatewhiteboard    | 0 | collaborate |
      | canannotatewhiteboard    | 0 | collaborate |
      | cansharevideo            | 0 | collaborate |
      | canshareaudio            | 0 | collaborate |
      | candownloadrecordings    | 0 | collaborate |
    And I log in as "teacher1"
    And the following "activity" exists:
      | activity                 | collaborate                      |
      | course                   | C1                               |
      | section                  | 1                                |
      | name                     | Test collab Instructor settings  |
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test collab Instructor settings"
    And I click on "#region-main-box .action-menu-trigger .dropdown .dropdown-toggle" "css_element"
    And I click on "Edit settings" "link"
    And I set the following fields to these values:
      | Post messages                                       | 1 |
      | Annotate on the whiteboard                          | 1 |
      | Share video feed                                    | 1 |
      | Share audio feed                                    | 1 |
      | Download recordings                                 | 1 |
      | Enable sessions to allocate up to 500 participants  | 1 |
      | Group mode                                          | <groupmode> |
    And I click on "Save and display" "button"
    And I click on "#region-main-box .action-menu-trigger .dropdown .dropdown-toggle" "css_element"
    And I click on "Edit settings" "link"
    Then the following fields match these values:
      | Post messages                                       | 1 |
      | Annotate on the whiteboard                          | 1 |
      | Share video feed                                    | 1 |
      | Share audio feed                                    | 1 |
      | Download recordings                                 | 1 |
      | Enable sessions to allocate up to 500 participants  | <enabled> |

    Examples:
      | groupmode       | enabled |
      | No groups       | 1       |
      | Separate groups | 0       |
      | Visible groups  | 0       |

  @javascript
  Scenario: Collaborate large sessions option message is shown accordingly.
    Given the following config values are set as admin:
      | instructorsettingstoggle | 1 | collaborate |
      | canpostmessages          | 0 | collaborate |
      | canannotatewhiteboard    | 0 | collaborate |
      | canannotatewhiteboard    | 0 | collaborate |
      | cansharevideo            | 0 | collaborate |
      | canshareaudio            | 0 | collaborate |
      | candownloadrecordings    | 0 | collaborate |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Collaborate" to section "1" and I fill the form with:
      | Session name | Test collab Instructor settings |
    And I follow "Test collab Instructor settings"
    And I click on "#region-main-box .action-menu-trigger .dropdown .dropdown-toggle" "css_element"
    And I click on "Edit settings" "link"
    And I expand all fieldsets
    And I set the following fields to these values:
      | Group mode | Separate groups |
    Then I should see "This option is not available for groups"
    Then I set the following fields to these values:
      | Group mode | No groups |
    Then I should not see "This option is not available for groups"

  Scenario: Collaborate large sessions can be created with guest access.
    Given I log in as "teacher1"
    And the following "activity" exists:
      | activity                 | collaborate                      |
      | course                   | C1                               |
      | section                  | 1                                |
      | name                     | Test collab large guests         |
      | guestaccessenabled       | 1                                |
      | guestrole                | pa                               |
      | largesessionenable       | 1                                |
    And I am on "Course 1" course homepage with editing mode on
    # Yep, it was added.
    And I follow "Test collab large guests"
    And I click on "#region-main-box .action-menu-trigger .dropdown .dropdown-toggle" "css_element"
    And I click on "Edit settings" "link"
    And I expand all fieldsets
    And I set the following fields to these values:
      | Collaborate guest role | Presenter |
    And I click on "Save and display" "button"
    Then I should see "In large scale sessions, guests must be participants"

  Scenario: Collaborate instance with hide duration view configuration enable does not show the duration.
    Given I log in as "teacher1"
    And the following "activity" exists:
      | activity                 | collaborate         |
      | course                   | C1                  |
      | section                  | 1                   |
      | name                     | Test collab         |
      | hideduration             | 1                   |
      | duration                 | 9999                |
    And I am on "Course 1" course homepage with editing mode on
    And I should not see "(Duration of course)"
    And I follow "Test collab"
    And I should not see "(Duration of course)"

  Scenario: Collaborate instance with hide duration view configuration disable show the duration.
    Given I log in as "teacher1"
    And the following "activity" exists:
      | activity                 | collaborate         |
      | course                   | C1                  |
      | section                  | 1                   |
      | name                     | Test collab         |
      | hideduration             | 0                   |
      | duration                 | 9999                |
    And I am on "Course 1" course homepage with editing mode on
    And I should see "(Duration of course)"
    And I follow "Test collab"
    And I should see "(Duration of course)"

  Scenario: Collaborate instance with override group mode setting ON will not allow you choose Groups Mode.
    Given the following config values are set as admin:
      | overridegroupmode | 1 | collaborate |
    And I log in as "teacher1"
    And the following "activity" exists:
      | activity                 | collaborate         |
      | course                   | C1                  |
      | section                  | 1                   |
      | name                     | Test collab         |
      | duration                 | 9999                |
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test collab"
    And I click on "#region-main-box .action-menu-trigger .dropdown .dropdown-toggle" "css_element"
    And I click on "Edit settings" "link"
    And I expand all fieldsets
    And I should not see "Separate groups" in the "#id_groupmode" "css_element"
    And I should not see "Visible groups" in the "#id_groupmode" "css_element"
    Then I should see "No groups" in the "#id_groupmode" "css_element"

  Scenario: Collaborate instance with override group mode setting OFF will allow you choose Groups Mode.
    Given the following config values are set as admin:
      | overridegroupmode | 0 | collaborate |
    And I log in as "teacher1"
    And the following "activity" exists:
      | activity                 | collaborate         |
      | course                   | C1                  |
      | section                  | 1                   |
      | name                     | Test collab         |
      | duration                 | 9999                |
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test collab"
    And I click on "#region-main-box .action-menu-trigger .dropdown .dropdown-toggle" "css_element"
    And I click on "Edit settings" "link"
    And I expand all fieldsets
    And I should see "Separate groups" in the "#id_groupmode" "css_element"
    And I should see "Visible groups" in the "#id_groupmode" "css_element"
    Then I should see "No groups" in the "#id_groupmode" "css_element"
