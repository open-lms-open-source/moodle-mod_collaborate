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
# @author     Jonathan Garcia jonathan.garcia@openlms.net
# @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@mod @mod_collaborate
Feature: Collaborate migration status message changes
  @javascript
  Scenario Outline: Not recognized configuration values have no impact on migration status messages.
    Given I log in as "admin"
    And the following config values are set as admin:
      |      config     |    value    |   plugin   |
      | migrationstatus |   <value>   | collaborate|
    And I navigate to "Plugins > Activity modules > Collaborate Ultra" in site administration
    And I should not see "Migration from SOAP to REST has not been initiated."
    And I should not see "Migration from SOAP to REST is currently in progress."
    And I should not see "Migration from SOAP to REST has been completed successfully."
    Examples:
      |   value  |
      |     0    |
      |     6    |
      |     7    |
      |    test  |

  @javascript
  Scenario Outline: Migrations status message changes when configuration values are updated.
    Given I log in as "admin"
    And the following config values are set as admin:
      |      config     |     value   |   plugin   |
      | migrationstatus |   <value>   | collaborate|
    And I navigate to "Plugins > Activity modules > Collaborate Ultra" in site administration
    And I should see "<message>"
    Examples:
      | value |                      message                                       |
      |   1   | Migration from SOAP to REST has been scheduled, but not initiated. |
      |   2   | Migration from SOAP to REST is currently in progress.              |
      |   3   | Migration from SOAP to REST is currently in progress.              |
      |   4   | Migration from SOAP to REST is currently in progress.              |
      |   5   | Migration from SOAP to REST has been completed successfully.       |

  @javascript
  Scenario: The migration button is disabled once the ad hoc task has been added.
    Given I log in as "admin"
    And I navigate to "Plugins > Activity modules > Collaborate Ultra" in site administration
    And I click on "Migrate to REST API" "button"
    And I click on "Continue" "button"
    Then I should see "Migration execution has been scheduled."
    And the following config values are set as admin:
      |      config     |     value   |   plugin   |
      | migrationstatus |   1         | collaborate|
    And I reload the page
    Then the "disabled" attribute of ".restapisettings input[type='button']" "css_element" should be set
