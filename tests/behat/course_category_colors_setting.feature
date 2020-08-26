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
# Tests for setting colors per category.
#
# @package    theme_charteredcollege
# @copyright Copyright (c) 2018 Blackboard Inc.
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later


@theme @theme_charteredcollege @theme_charteredcollege_color_check @theme_charteredcollege_course
Feature: When the moodle theme is set to charteredcollege, sets a color per category.

  Background:

    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And I create the following course categories:
      | id | name   | category | idnumber | description |
      |  5 | Cat 5  |     0    |   CAT5   |   Test      |
      | 10 | Cat 10 |   CAT5   |   CAT10  |   Test      |
      | 20 | Cat 20 |   CAT10  |   CAT20  |   Test      |
      | 30 | Cat 30 |   CAT30  |   CAT30  |   Test      |
    And the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
      | Course 2 | C2        | CAT20    | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | admin    | C2     | editingteacher |
      | teacher1 | C2     | editingteacher |
      | student1 | C2     | student        |
    And the following config values are set as admin:
      | category_color | {"5":"#00FF00","30":"#FF0000"} | theme_charteredcollege |
    And the following config values are set as admin:
      | allowcategorythemes | true       |

  @javascript
  Scenario: Load all classes in each category hierarchy.
    Given I log in as "admin"
    And I follow "Browse all courses"
    And I purge charteredcollege caches
    And I wait until the page is ready
    And I check body for classes "theme-charteredcollege"
    And I follow "Miscellaneous"
    And I check body for classes "theme-charteredcollege,category-1"
    And I follow "Courses"
    And I follow "Browse all courses"
    And I follow "Cat 5"
    And I check body for classes "theme-charteredcollege,category-5"
    And I follow "Cat 10"
    And I check body for classes "theme-charteredcollege,category-5,category-10"
    And I follow "Cat 20"
    And I check body for classes "theme-charteredcollege,category-5,category-10,category-20"

  @javascript
  Scenario: Check category colors in hierarchy.
    Given I log in as "admin"
    And I follow "Browse all courses"
    And I purge charteredcollege caches
    And I wait until the page is ready
    And I follow "Cat 5"
    And I check element "a.btn.btn-secondary" with color "#00FF00"
    And I follow "Cat 10"
    And I check element "a.btn.btn-secondary" with color "#00FF00"
    And I follow "Cat 20"
    And I check element "a.btn.btn-secondary" with color "#00FF00"
    And I follow "Courses"
    And I follow "Browse all courses"
    And I follow "Cat 30"
    And I check element "a.btn.btn-secondary" with color "#FF0000"

  @javascript
  Scenario: Check category colors from nearest parent in hierarchy.
    Given the following config values are set as admin:
      | category_color | {"5":"#00FF00","10":"#FF0000"} | theme_charteredcollege |
    And I log in as "admin"
    And I follow "Browse all courses"
    And I purge charteredcollege caches
    And I wait until the page is ready
    And I follow "Cat 5"
    And I check element "a.btn.btn-secondary" with color "#00FF00"
    And I follow "Cat 10"
    And I check element "a.btn.btn-secondary" with color "#FF0000"
    And I follow "Cat 20"
    And I check element "a.btn.btn-secondary" with color "#FF0000"
    And I follow "Courses"
    And I follow "Browse all courses"
    And I follow "Cat 30"
    And I check element "a.btn.btn-secondary" with color "#ff7f41"

  @javascript
  Scenario: Check category course color from nearest parent in hierarchy for teacher and student.
    Given I log in as "admin"
    And I follow "Browse all courses"
    And I purge charteredcollege caches
    Then I log out
    Then I log in as "teacher1"
    # And I am on the course with shortname "C2" <- Removed this - this custom step is not part of charteredcollege!
    And I am on the course main page for "C2"
    And I check element "a" with color "#00FF00"
    And I follow "Create learning activity"
    And I follow "Resources"
    # The tabs color is by design 8% darker than the category color.
    And I check element "#resources-tab" with property "background-color" = "#00D600"
    And I follow "Help guide"
    And I check element "#help-guide-tab" with property "background-color" = "#00D600"
    And I follow "Activities"
    And I check element "#activites-tab" with property "background-color" = "#00D600"
    And I click on "//div[@id='modchooser-accessible-tab']//button[@class='close']" "xpath_element"
    Then I log out
    And I log in as "student1"
    # And I am on the course with shortname "C2"
    And I am on the course main page for "C2"
    And I check element "a" with color "#00FF00"
