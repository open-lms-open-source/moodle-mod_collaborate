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
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student        |

  Scenario: Teacher can enable and disable guest access for a Collaborate instance
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Collaborate" to section "1" and I fill the form with:
      | Session name | Test collab |
      | Allow Guest Access | 1 |
    And I follow "Test collab"
    Then I should see "Guest Link"
    And "#guestlink" "css_element" should exist
    And I click on "Copy link" "button"
    Then I should see "Link copied to clipboard."
    And I navigate to "Edit settings" node in "Collaborate administration"
    And I set the field "Allow Guest Access" to "0"
    And I press "Save and display"
    Then I should not see "Guest Link"
    And "#guestlink" "css_element" should not exist

  Scenario: Teacher can create Collaborate instance with Guest Role default of "Presenter"
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Collaborate" to section "1" and I fill the form with:
      | Session name | Test collab 2 |
      | Allow Guest Access | 1 |
    And I follow "Test collab"
    And I navigate to "Edit settings" node in "Collaborate administration"
    Then the field "Guest Role" matches value "Presenter"

  Scenario: Teacher select Guest Role for a Collaborate instance
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Collaborate" to section "1" and I fill the form with:
      | Session name | Test collab 2 |
      | Allow Guest Access | 1 |
      | Guest Role         | Participant |
    And I follow "Test collab"
    And I navigate to "Edit settings" node in "Collaborate administration"
    Then the field "Guest Role" matches value "Participant"
