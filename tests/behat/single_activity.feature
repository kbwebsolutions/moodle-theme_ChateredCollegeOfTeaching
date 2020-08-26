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
# Tests that Single Activity format remains usable in charteredcollege
#
# @package   theme_charteredcollege
# @author    Sam Chaffee
# @copyright Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
# @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_charteredcollege
Feature: Use the Single Activity format in charteredcollege
  In order to use the Single Activity format in charteredcollege
  As a teacher
  I need to be able to create a course using Single Activity format without errors in charteredcollege

  Background:
  Given the following "categories" exist:
      | name     | category | idnumber |
      | Test Cat | 0        | TESTCAT  |

  @javascript
  Scenario: Admin creates a single activity course with a Glossary
    Given I log in as "admin"
    And I click on "Browse all courses" "link"
    And I click on "Add a new course" "link"
    And I set the following fields to these values:
      | Course full name | Test single activity in charteredcollege|
      | Course short name | TSAIS |
      | Course ID number | TC101 |
      | Course summary | This course has been created by automated tests. |
      | Format  | Single activity format |
    And I wait until "#id_activitytype" "css_element" is visible
    And I set the following fields to these values:
      | Type of activity | Glossary |
    And I press "Save and display"
    And I press "Proceed to course content"
    And I set the following fields to these values:
      | Name | Glossary test |
      | Description | G1     |
    And I press "Save and display"
    Then I should see "Glossary test"
