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
# Tests for availability of course tools section.
#
# @package   theme_charteredcollege
# @copyright Copyright (c) 2016 Blackboard Inc.
# @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_charteredcollege @theme_charteredcollege_course
Feature: When the moodle theme is set to charteredcollege, a course tools section is available.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario: Course tools link does not show for unsupported formats.
    Given the course format for "C1" is set to "social"
    When I log in as "student1"
    And I am on the course main page for "C1"
    Then "a[href=\"#coursetools\"]" "css_element" should not exist

  @javascript
  Scenario Outline: Course tools link functions for supported formats.
    Given the course format for "C1" is set to "<format>"
    And completion tracking is "<completionenabled>" for course "C1"
    And I set the following system permissions of "Student" role:
      | capability            | permission            |
      | gradereport/overview:view | <gradebookaccessible> |
    When I log in as "student1"
    And I am on the course main page for "C1"
    And I click on "a[href=\"#coursetools\"]" "css_element"
    Then I should see "Course Dashboard" in the "#coursetools" "css_element"
    And "#charteredcollege-student-dashboard" "css_element" should exist
    And ".charteredcollege-student-dashboard-progress" "css_element" <seecompletion> exist
    And ".charteredcollege-student-dashboard-grade" "css_element" <seegrade> exist
    Examples:
      | format | completionenabled | gradebookaccessible | seecompletion | seegrade   |
      | topics | Enabled           | Allow               | should        | should     |
      | topics | Disabled          | Prohibit            | should not    | should not |
      | topics | Enabled           | Prohibit            | should        | should not |
      | topics | Disabled          | Allow               | should not    | should     |
      | weeks  | Enabled           | Allow               | should        | should     |
      | weeks  | Disabled          | Prohibit            | should not    | should not |
      | weeks  | Enabled           | Prohibit            | should        | should not |
      | weeks  | Disabled          | Allow               | should not    | should     |

  @javascript
  Scenario Outline: Course tools show automatically for single activity format.
    Given the course format for "C1" is set to "singleactivity" with the following settings:
      | name      | activitytype |
      | value     | forum        |
    And the following "activities" exist:
      | activity | course | idnumber | name            | intro           | section |
      | forum    | C1     | forum1   | Test forum      | Test forum      | 1       |
    And completion tracking is "<completionenabled>" for course "C1"
    And I set the following system permissions of "Student" role:
      | capability                | permission            |
      | gradereport/overview:view | <gradebookaccessible> |
    When I log in as "student1"
    And I am on the course main page for "C1"
    # Note we have to call this step twice because for some reason it doesn't automatically go to the module page the
    # first time - that's a core issue though.
    And I am on the course main page for "C1"
    Then I should see "Course Dashboard" in the "#coursetools" "css_element"
    And "#charteredcollege-student-dashboard" "css_element" should exist
    And ".charteredcollege-student-dashboard-progress" "css_element" <seecompletion> exist
    And ".charteredcollege-student-dashboard-grade" "css_element" <seegrade> exist
    Examples:
      | completionenabled | gradebookaccessible | seecompletion | seegrade   |
      | Enabled           | Allow               | should        | should     |
      | Disabled          | Prohibit            | should not    | should not |

  @javascript
  Scenario Outline: Course tools show automatically for single activity format set to hsuforum of types general / single.
    Given I am using Blackboard Open LMS
    And the course format for "C1" is set to "singleactivity" with the following settings:
      | name      | activitytype |
      | value     | hsuforum        |
    And the following "activities" exist:
      | activity    | course | idnumber | name          | intro           | section | type   |
      | hsuforum    | C1     | forum1   | Test hsuforum | Test hsuforum   | 1       | <type> |
    And completion tracking is "<completionenabled>" for course "C1"
    And I set the following system permissions of "Student" role:
      | capability                | permission            |
      | gradereport/overview:view | <gradebookaccessible> |
    When I log in as "student1"
    And I am on the course main page for "C1"
    # Note we have to call this step twice because for some reason it doesn't automatically go to the module page the
    # first time - that's a core issue though.
    And I am on the course main page for "C1"
    Then I should see "Course Dashboard" in the "#coursetools" "css_element"
    And "#charteredcollege-student-dashboard" "css_element" should exist
    And ".charteredcollege-student-dashboard-progress" "css_element" <seecompletion> exist
    And ".charteredcollege-student-dashboard-grade" "css_element" <seegrade> exist
    Examples:
      | type    | completionenabled | gradebookaccessible | seecompletion | seegrade   |
      | general | Enabled           | Allow               | should        | should     |
      | general | Disabled          | Prohibit            | should not    | should not |
      | single  | Enabled           | Allow               | should        | should     |
      | single  | Disabled          | Prohibit            | should not    | should not |

  @javascript
  Scenario: Grade and progress are shown to students only when allowed by settings.
    Given completion tracking is "Disabled" for course "C1"
    And I set the following system permissions of "Student" role:
      | capability                | permission            |
      | gradereport/overview:view | Prohibit              |
    When I log in as "student1"
    And I am on the course main page for "C1"
    And I click on "a[href=\"#coursetools\"]" "css_element"
    And ".charteredcollege-student-dashboard-progress" "css_element" should not exist
    And ".charteredcollege-student-dashboard-grade" "css_element" should not exist
    Given completion tracking is "Enabled" for course "C1"
    And I set the following system permissions of "Student" role:
      | capability                | permission            |
      | gradereport/overview:view | Allow                 |
    And I am on the course main page for "C1"
    And I click on "a[href=\"#coursetools\"]" "css_element"
    And ".charteredcollege-student-dashboard-progress" "css_element" should exist
    And ".charteredcollege-student-dashboard-grade" "css_element" should exist
    Given the following config values are set as admin:
      | showcoursegradepersonalmenu | 0 | theme_charteredcollege |
    And I reload the page
    And I am on the course main page for "C1"
    And I click on "a[href=\"#coursetools\"]" "css_element"
    And ".charteredcollege-student-dashboard-progress" "css_element" should exist
    And ".charteredcollege-student-dashboard-grade" "css_element" should exist

  @javascript
  Scenario: Course tools includes ally course lti report.
    Given I am using Blackboard Open LMS
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    And I click on "a[href=\"#coursetools\"]" "css_element"
    And "#coursetools a[href*=\"report/allylti/launch.php?reporttype=course\"]" "css_element" should exist
    And I log out
    And I log in as "student1"
    And I am on the course main page for "C1"
    And I click on "a[href=\"#coursetools\"]" "css_element"
    And "#coursetools a[href*=\"report/allylti/launch.php?reporttype=course\"]" "css_element" should not exist
