@mod @mod_collaborate
Feature: Guest access can be provided for a Collaborate session
  In order to allow guest access
  As a teacher
  I need to be able to set guest access for the session, set the guest access role for the session and copy the guest access link

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student        |
      | student2 | C1 | student        |

  Scenario: Teacher can enable and disable guest access for a Collaborate instance
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Collaborate" to section "1" and I fill the form with:
      | Session name | Test collab |
      | Allow Collaborate guest access | 1 |
    And I follow "Test collab"
    And I follow "Guest links"
    Then I should see "Main session"
    And "#guestlink" "css_element" should exist
    And I click on "Copy link" "button"
    Then I should see "Link copied to clipboard."
    And I navigate to "Edit settings" in current page administration
    And I set the field "Allow Collaborate guest access" to "0"
    And I press "Save and display"
    Then I should not see "Collaborate guest link"
    And "#guestlink" "css_element" should not exist

  Scenario: Teacher can create Collaborate instance with Collaborate guest role default of "Presenter"
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Collaborate" to section "1" and I fill the form with:
      | Session name | Test collab 2 |
      | Allow Collaborate guest access | 1 |
    And I follow "Test collab"
    And I navigate to "Edit settings" in current page administration
    Then the field "Collaborate guest role" matches value "Presenter"

  Scenario: Teacher select Collaborate guest role for a Collaborate instance
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Collaborate" to section "1" and I fill the form with:
      | Session name | Test collab 2 |
      | Allow Collaborate guest access | 1 |
      | Collaborate guest role         | Participant |
    And I follow "Test collab"
    And I navigate to "Edit settings" in current page administration
    Then the field "Collaborate guest role" matches value "Participant"

  Scenario: Groups guest access are created depending on the group mode
    And the following "groups" exist:
      | name    | course  | idnumber |
      | Group 1 | C1      | G1       |
      | Group 2 | C1      | G2       |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | student2 | G2    |
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Collaborate" to section "1" and I fill the form with:
      | Session name                   | Test collab     |
      | Allow Collaborate guest access | 1               |
      | Group mode                     | Separate groups |
    And I follow "Test collab"
    And I follow "Guest links"
    Then I should see "Main session"
    Then I should see "Group 1"
    Then I should see "Group 2"
    And I follow "Course 1"
    And I add a "Collaborate" to section "1" and I fill the form with:
      | Session name                   | Testing collab  |
      | Allow Collaborate guest access | 1               |
      | Group mode                     | No groups       |
    And I follow "Testing collab"
    And I follow "Guest links"
    Then I should see "Main session"
    Then I should not see "Group 1"
    Then I should not see "Group 2"

  Scenario: Guest can not access to a Collaborate instance and an error message should be displayed
    Given I log in as "teacher1"
      And I am on "Course 1" course homepage with editing mode on
      And I add a "Collaborate" to section "1" and I fill the form with:
        | Session name | Test collab 3      |
        | Duration     | Duration of course |
      And I follow "Test collab 3"
     Then I follow "Switch role to..." in the user menu
      And I press "Guest"
      And I should see "Sorry, guests are not allowed to take part in discussions"