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
# Tests for personal menu display on initial login.
#
# @package    theme_charteredcollege
# @author     2016 Guy Thomas <osdev@blackboard.com>
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_charteredcollege @theme_charteredcollege_personalmenu
Feature: When the moodle theme is set to charteredcollege,
          users can open and close the personal menu,
          and optionally open the personal menu on login

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And I am on site homepage

  @javascript
  Scenario: User opens and closes login menu using call-to-action button on site homepage
    Given I click on "#page-mast .js-charteredcollege-pm-trigger" "css_element"
    Then ".charteredcollege-pm-login-form" "css_element" should be visible
    And I follow "Close"
    Then ".charteredcollege-pm-login-form" "css_element" should not be visible

  @javascript
  Scenario: User logs in and does not see the personal menu, if option turned off
    Given the following config values are set as admin:
      | personalmenulogintoggle | 0 | theme_charteredcollege |
    Given I follow "Log in"
    And I set the field "username" to "teacher1"
    And I set the field "password" to "teacher1"
    And I press "Log in"
    Then "#charteredcollege-pm" "css_element" should not be visible

  @javascript
  Scenario: User logs in as guest, no personal menu or login dropdown visible
    Given I follow "Log in"
    And I set the field "username" to "guest"
    And I set the field "password" to "guest"
    And I press "Log in"
    Then "#charteredcollege-pm" "css_element" should not be visible
    And "#username" "css_element" should not be visible
    And "#password" "css_element" should not be visible

  @javascript
  Scenario: User logs in and sees the personal menu, then closes it and re-opens without changing section
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |
    And I follow "Log in"
    And I set the field "username" to "teacher1"
    And I set the field "password" to "teacher1"
    And I press "Log in"
    Then "#charteredcollege-pm" "css_element" should be visible
    And I am on "Course 1" course homepage
    And I follow "Introduction"
    And "#section-0" "css_element" should be visible
    And I follow "Topic 1"
    And "#section-1" "css_element" should be visible
    And I follow "My Courses"
    Then "#charteredcollege-pm" "css_element" should be visible
    And I follow "Close"
    And "#charteredcollege-pm" "css_element" should not be visible
    And "#section-1" "css_element" should be visible
    And "#section-0" "css_element" should not be visible

  @javascript
  Scenario: User logs in and sees the personal menu on site homepage, if that setting used
    Given the following config values are set as admin:
      | defaulthomepage | 0 |
    And I follow "Log in"
    And I set the field "username" to "teacher1"
    And I set the field "password" to "teacher1"
    And I press "Log in"
    Then "#charteredcollege-pm" "css_element" should be visible
    And I follow "Close"
    Then "#page-site-index #page-header" "css_element" should be visible

  @javascript
  Scenario: User accesses a course and is prompted to log in, does not see personal menu
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |
    And I am on homepage
    When I follow "Courses"
    And I am on "Course 1" course homepage
    # The above will trigger a redirect to the login page.
    And I wait until ".charteredcollege-log-in-loading-spinner" "css_element" is not visible
    And I set the field "username" to "teacher1"
    And I set the field "password" to "teacher1"
    And I press "Log in"
    Then "#charteredcollege-pm" "css_element" should not be visible
    And "#section-0" "css_element" should be visible
    And I am on site homepage
    And "#charteredcollege-pm" "css_element" should not be visible
    And I am on homepage
    And "#charteredcollege-pm" "css_element" should not be visible
