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
# Test auto-expand area with error in settings page for modules
#
# @package   theme_charteredcollege
# @copyright Copyright (c) 2018 Blackboard Inc. (http://www.blackboard.com)
# @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later


@theme @theme_charteredcollege
Feature: When setting an erroneous field in settings, charteredcollege auto-expands area.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | category | groupmode | enablecompletion |
      | Course 1 | C1        | topics | 0        | 1         | 1                |
    Then I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Assignment One    |
      | Description     | Test description  |
    And I log out

  @javascript
  Scenario: Go to Assignment settings page and put wrong max grade.
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Topic 1"
    And I click on ".charteredcollege-activity[data-type='Assignment']  a.charteredcollege-edit-asset" "css_element"
    And I expand all fieldsets
    And I set the field "id_grade_modgrade_point" to "150"
    Then I press "Save and display"
    And I wait until the page is ready
    And I should see "Invalid grade value. This must be an integer between 1 and 100"

  @javascript
  Scenario: Go to Assignment settings page and put text on Grade to pass input.
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Topic 1"
    And I click on ".charteredcollege-activity[data-type='Assignment']  a.charteredcollege-edit-asset" "css_element"
    And I expand all fieldsets
    And I set the field "id_gradepass" to "text"
    Then I press "Save and display"
    And I wait until the page is ready
    And I should see "You must enter a number here"

  @javascript
  Scenario: Go to Assignment settings page and put Cut-off date before allow submissions from.
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Topic 1"
    And I click on ".charteredcollege-activity[data-type='Assignment']  a.charteredcollege-edit-asset" "css_element"
    And I expand all fieldsets
    And I set the field "id_cutoffdate_enabled" to "1"
    And I set the field "id_cutoffdate_year" to "1980"
    Then I press "Save and display"
    And I wait until the page is ready
    And I should see "Cut-off date cannot be earlier than the allow submissions from date."

  @javascript
  Scenario: Go to Assignment settings page and put Grading date before allow submissions from.
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Topic 1"
    And I click on ".charteredcollege-activity[data-type='Assignment']  a.charteredcollege-edit-asset" "css_element"
    And I expand all fieldsets
    And I set the field "id_gradingduedate_enabled" to "1"
    And I set the field "id_gradingduedate_year" to "1980"
    Then I press "Save and display"
    And I wait until the page is ready
    And I should see "Remind me to grade by date cannot be earlier than the due date"




