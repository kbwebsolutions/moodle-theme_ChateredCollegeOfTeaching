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
# Tests for toggle course section visibility in non edit mode in charteredcollege.
#
# @package    theme_charteredcollege
# @author     Sebastian Gracia sebastian.gracia@blackboard.com
# @copyright  Copyright (c) 2019 Blackboard Inc. (http://www.blackboard.com)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_charteredcollege @theme_charteredcollege_activity_title_visibility
Feature: Title of Page, Book and Label activities should not
  be display at the top when added to a course

  Background:
    Given the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | student1  | Student    | 1         | student1@example.com  |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | student1  | C1      | student         |
      | teacher1  | C1      | editingteacher  |
    And the following "activities" exist:
      | activity   | name         | intro                       | course | idnumber  | section |
      | book       | TestB 1      | Test book description       | C1     | book1     | 0       |
      | page       | TestP 1      | Test page description       | C1     | page1     | 0       |
      | label      | TestL 1      | Test label description      | C1     | label1    | 0       |
    And I log in as "admin"
    And the following config values are set as admin:
      | resourcedisplay | card | theme_charteredcollege |
    And I log out

  @javascript
  Scenario: The user should not see book title when visiting the course homepage, after a book
    activity was added.
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I should not see "Book"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I should not see "Book"

  @javascript
  Scenario: The user should not see page title when visiting the course homepage, after a page
    activity was added.
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I should not see "Page"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I should not see "Page"

  @javascript
  Scenario: The user should not see label title when visiting the course homepage, after a label
    activity was added.
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I should not see "Label"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I should not see "Label"
