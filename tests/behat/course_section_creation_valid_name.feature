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
# Tests deleting sections in charteredcollege.
#
# @package    theme_charteredcollege
# @author     Juan Ibarra <juan.ibarra@blackboard.com>
# @copyright  2019 Blackboard Ltd
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_charteredcollege @theme_charteredcollege_course @theme_charteredcollege_course_section
Feature: When the moodle theme is set to charteredcollege, section names should not be empty.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | course | idnumber | name             | intro                         | section | assignsubmission_onlinetext_enabled |
      | assign   | C1     | assign1  | Test assignment1 | Test assignment description 1 | 1       | 1                                   |
      | assign   | C1     | assign2  | Test assignment2 | Test assignment description 2 | 2       | 1                                   |

  @javascript
  Scenario Outline: When editing a section name, the name should be at least one character.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_charteredcollege |
    And I log out
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    And I follow "Topic 1"
    And I click on "#section-1 .edit-summary" "css_element"
    #"Only spaces" name not allowed
    And I set the section name to "  "
    And I press "Save changes"
    And "body#page-course-editsection" "css_element" should exist
    #Names with leading spaces are allowed
    And I set the section name to "  Topic one"
    And I press "Save changes"
    And I wait until the page is ready
    And "body#page-course-editsection" "css_element" should not exist
    #Names with trailing spaces are allowed too
    And I click on "#section-1 .edit-summary" "css_element"
    And I set the section name to "Topic one   "
    And I press "Save changes"
    And I wait until the page is ready
    And "body#page-course-editsection" "css_element" should not exist
    #Valid name is allowed
    And I click on "#section-1 .edit-summary" "css_element"
    Then I set the section name to "Topic one"
    And I press "Save changes"
    And I wait until the page is ready
    And "body#page-course-editsection" "css_element" should not exist
  Examples:
    | Option     |
    | 0          |
    | 1          |

  @javascript
  Scenario Outline: When creating a section, the name should be at least one character.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_charteredcollege |
    And I log out
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    And I click on "#charteredcollege-new-section" "css_element"
    #"Only spaces" name not allowed
    And I set the field with xpath "//input[@id=\"newsection\"]" to " "
    And I press "Create section"
    And "section#charteredcollege-add-new-section" "css_element" should be visible
    #Names with leading spaces are allowed
    And I set the field with xpath "//input[@id=\"newsection\"]" to "  New section"
    And I press "Create section"
    And I wait until the page is ready
    And "section#charteredcollege-add-new-section" "css_element" should not be visible
    #Names with trailing spaces are allowed
    And I click on "#charteredcollege-new-section" "css_element"
    And I set the field with xpath "//input[@id=\"newsection\"]" to "New section    "
    And I press "Create section"
    And I wait until the page is ready
    And "section#charteredcollege-add-new-section" "css_element" should not be visible
    #Valid name is allowed
    And I click on "#charteredcollege-new-section" "css_element"
    And I set the field with xpath "//input[@id=\"newsection\"]" to "New section"
    And I press "Create section"
    And I wait until the page is ready
    And "section#charteredcollege-add-new-section" "css_element" should not be visible
    And I follow "New section"
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario Outline: When creating a section in weekly format, the name should be at least one character.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_charteredcollege |
    And I am on the course main page for "C1"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And the field "id_format" matches value "Topics format"
    And I set the following fields to these values:
      | id_startdate_day | 1 |
      | id_startdate_month | January |
      | id_startdate_year | 2020 |
      | id_format | Weekly format |
      | id_enddate_enabled | 0 |
    And I press "Save and display"
    And I should see "1 January - 7 January"
    And I log out
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    And I follow "1 January - 7 January"
    And I click on "#section-1 .edit-summary" "css_element"
    #"Only spaces" name not allowed
    And I set the field "Custom" to "1"
    And I set the section name to "  "
    And I press "Save changes"
    And "body#page-course-editsection" "css_element" should exist
    #Names with leading spaces allowed
    And I set the section name to "  Topic one"
    And I press "Save changes"
    And I should see "Topic one"
    #Names with trailing spaces allowed
    And I follow "8 January - 14 January"
    And I click on "#section-2 .edit-summary" "css_element"
    And I set the section name to "Topic two   "
    And I press "Save changes"
    And I should see "Topic two"
    Examples:
      | Option     |
      | 0          |
      | 1          |
