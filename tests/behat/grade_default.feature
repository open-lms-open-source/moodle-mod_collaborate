@mod @mod_collaborate
Feature: Have a sensible default grade type when creating a Collaborate instance
  In order to quickly add Collaborate instances
  As a teacher
  I want the grade type set to "None"

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  Scenario: Teacher can create a Collaborate instance that has grade type default to None
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Collaborate" to section "1" and I fill the form with:
      | Session name | Test collab |
    And I follow "Test collab"
    And I navigate to "Edit settings" node in "Collaborate administration"
    Then the field "grade[modgrade_type]" matches value "None"