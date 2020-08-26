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
# Tests for courses in the charteredcollege personal menu.
#
# @package    theme_charteredcollege
# @copyright  Copyright (c) 2015 Blackboard Inc. (http://www.blackboard.com)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_charteredcollege @theme_charteredcollege_personalmenu
Feature: When the moodle theme is set to charteredcollege, students and teachers can open a personal menu with a list of courses
  they are enrolled in to give them easy access.

  Background:
    Given the following "courses" exist:
      | fullname        | shortname | category | groupmode | visible |
      | Course 1        | C1        | 0        | 1         | 1       |
      | Course Hidden   | Hidden    | 0        | 1         | 0       |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | user1    | User      | 1        | user1@example.com    |
      | student2 | Student   | 2        | student2@example.com |

  @javascript
  Scenario: Enrolled courses display and are navigable, hidden courses with teacher roles need expanding first
    Given the following "course enrolments" exist:
      | user  | course | role    |
      | user1 | C1     | student |
      | user1 | Hidden | teacher |
    And I log in as "user1"
    And I open the personal menu
    Then I should see "Course 1"
    And I should not see "Courses you are enrolled in will be shown here"
    And I should not see "Course Hidden"
    And I follow "Hidden courses"
    And I should see "Course Hidden"
    When I am on "Course 1" course homepage
    Then I should see "Course 1"
    And I should see "Introduction" in the "#chapters" "css_element"

  @javascript
  Scenario: User with no course enrolments sees a message
    Given I log in as "student2"
    When I open the personal menu
    Then I should see "Courses you are enrolled in will be shown here"
    And I should not see "Hidden courses"

  @javascript
  Scenario: A student with only hidden courses sees the no courses message
    Given the following "course enrolments" exist:
      | user     | course | role    |
      | student2 | Hidden | student |
    And I log in as "student2"
    When I open the personal menu
    Then I should see "Courses you are enrolled in will be shown here"
    And I should not see "Course Hidden"
    And I should not see "Hidden Courses"

  @javascript
  Scenario: Student with only expired course enrolments sees a message
    Given the following "course enrolments" exist:
      | user     | course | role    | timeend    |
      | student2 | Hidden | student | 1466172659 |
    And I log in as "student2"
    When I open the personal menu
    Then I should see "Courses you are enrolled in will be shown here"
    And I should not see "Course Hidden"
    And I should not see "Hidden courses"

  @javascript
  Scenario: Teacher with only expired course enrolments sees a message
    Given the following "users" exist:
      | username | firstname | lastname | email                 |
      | teacher1 | Teacher   | 1        | teacher1@example.com  |
    And the following "course enrolments" exist:
      | user     | course | role    | timeend    |
      | teacher1 | Hidden | teacher | 1466172659 |
    And I log in as "teacher1"
    When I open the personal menu
    Then I should see "Courses you are enrolled in will be shown here"
    And I should not see "Course Hidden"
    And I should not see "Hidden courses"

  @javascript
  Scenario: A teacher with only hidden courses sees them without having to expand
    Given the following "users" exist:
      | username | firstname | lastname | email                 |
      | teacher1 | Teacher   | 1        | teacher1@example.com  |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | teacher1 | Hidden | teacher |
    And I log in as "teacher1"
    When I open the personal menu
    Then I should see "Course Hidden"
    And I should not see "Courses you are enrolled in will be shown here"
    And I should not see "Hidden Courses"

  @javascript
  Scenario: Opening / closing the expander works
    Given the following "users" exist:
      | username | firstname | lastname | email                 |
      | teacher1 | Teacher   | 1        | teacher1@example.com  |
    And the following "courses" exist:
      | fullname        | shortname | category | groupmode | visible |
      | Course Hidden 2 | Hidden2   | 0        | 1         | 0       |
    And the following "course enrolments" exist:
      | user     | course  | role    |
      | teacher1 | C1      | teacher |
      | teacher1 | Hidden  | teacher |
      | teacher1 | Hidden2 | teacher |
    And I log in as "teacher1"
    When I open the personal menu
    Then I should see "Course 1"
    And I should not see "Courses you are enrolled in will be shown here"
    And I should not see "Course Hidden"
    And I should not see "Course Hidden 2"
    And I follow "Hidden courses"
    And I should see "Course Hidden"
    And I should see "Course Hidden 2"
    # Sadly, the following pause is necessary as rapid clicks of the "Hidden courses" link are not registered.
    And I wait "1" seconds
    And I follow "Hidden courses"
    And I should not see "Course Hidden"
    And I should not see "Course Hidden 2"

