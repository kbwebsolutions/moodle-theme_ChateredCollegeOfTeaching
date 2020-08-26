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
# Tests for cover image uploading.
#
# @package    theme_charteredcollege
# @copyright  Copyright (c) 2016 Blackboard Ltd.
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_charteredcollege
Feature: When the moodle theme is set to charteredcollege, ajax failures due to log outs / expired sessions are reported correctly
  as session issues.

  Background:
    Given the following config values are set as admin:
      | defaulthomepage | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher  | Test      | teacher  | teacher@example.com  |
    And the following config values are set as admin:
      | personalmenuadvancedfeedsenable | 0 | theme_charteredcollege |

  @javascript
  Scenario: Logged in user get's login status warning when logged out if personal menu is opened.
    Given I log in as "teacher"
    And I am on site homepage
    And I log out via a separate window
    And I open the personal menu
    Then I should see "You are logged out"
    When I press "Continue"
    Then "body#page-login-index" "css_element" should exist

  @javascript
  Scenario: Teacher get's login status warning when trying to manage sections if logged out.
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    # Test logout msg when changing section visibility
    And I log in as "teacher"
    And I am on the course main page for "C1"
    When I follow "Topic 2"
    Then "#section-2" "css_element" should exist
    And I log out via a separate window
    When I click on "#section-2 .charteredcollege-visibility.charteredcollege-hide" "css_element"
    Then "body#page-login-index" "css_element" should exist
    # Test logout msg when highlighting section
    And I log in as "teacher"
    And I am on the course main page for "C1"
    When I follow "Topic 2"
    Then "#section-2" "css_element" should exist
    And I log out via a separate window
    And I click on "#section-2 .charteredcollege-highlight" "css_element"
    Then "body#page-login-index" "css_element" should exist
    # Test logout msg when moving section
    And I log in as "teacher"
    And I am on the course main page for "C1"
    When I follow "Topic 2"
    And I follow "Move \"Topic 2\""
    Then I should see "Moving \"Untitled Topic\"" in the "#charteredcollege-footer-alert" "css_element"
    And I follow "Topic 4"
    And I log out via a separate window
    When I follow "Place section \"Untitled Topic\" before section \"Topic 4\""
    Then I should see "You are logged out"

  @javascript
  Scenario: Teacher get's login status warning when trying to manage assets if logged out.
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "activities" exist:
      | activity | course | idnumber | name            | intro           | section | assignsubmission_onlinetext_enabled |
      | assign   | C1     | assign1  | Test assignment | Test assignment | 1       | 1                                   |
    # Test logout msg when changing asset visibility
    Given I log in as "teacher"
    And I am on the course main page for "C1"
    When I follow "Topic 1"
    And I click on ".charteredcollege-activity[data-type='Assignment'] span.charteredcollege-edit-asset-more" "css_element"
    And I log out via a separate window
    When I click on ".charteredcollege-activity[data-type='Assignment'] a.js_charteredcollege_hide" "css_element"
    And I wait until the page is ready
    # Test logout msg when attempting to duplicate asset
    Given I log in as "teacher"
    And I am on the course main page for "C1"
    When I follow "Topic 1"
    And I click on ".charteredcollege-activity[data-type='Assignment'] span.charteredcollege-edit-asset-more" "css_element"
    And I log out via a separate window
    And I wait until the page is ready
    When I click on ".charteredcollege-activity[data-type='Assignment'] a.js_charteredcollege_duplicate" "css_element"
    Then "body#page-login-index" "css_element" should exist
    # Test logout msg when attempting to move asset
    Given I log in as "teacher"
    And I am on the course main page for "C1"
    And I follow "Topic 1"
    Then "#section-1" "css_element" should exist
    And I click on ".charteredcollege-activity.modtype_assign .charteredcollege-asset-move img[title='Move \"Test assignment\"']" "css_element"
    Then I should see "Moving \"Test assignment\""
    And I log out via a separate window
    When I click on "li#section-1 li.charteredcollege-drop.asset-drop div.asset-wrapper" "css_element"
    Then I should see "You are logged out"

  @javascript
  Scenario: User account is created with force password change on login enabled, personal menu shows a warning when
    opened with a link to change the users password.
    Given force password change is assigned to user "teacher"
    When I log in as "teacher"
    Then I should see "You must change your password to proceed."
    # Opening the personal menu should trigger an AJAX error which is displayed within the menu.
    And I open the personal menu
    Then I should see "You must change your password before using the personal menu." in the "#charteredcollege-pm-content .force-pwd-warning" "css_element"
