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
# Tests course heading links back to course.
#
# @package    theme_charteredcollege
# @copyright Copyright (c) 2015 Blackboard Inc. (http://www.blackboard.com)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later


@theme @theme_charteredcollege @theme_charteredcollege_course
Feature: When the moodle theme is set to charteredcollege, users can link back to the course main page by clicking the page heading.

  Background:
  Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario: Teacher can navigate back to course main page from editing topics section.
    Given I log in as "teacher1"
    And I am on the course main page for "C1"
    And I follow "Edit section"
    # Note the double space between "of" and "General" below is necessary.
    And I should see "Summary of  General"
    And I follow the page heading course link
    Then I should see "Contents"
    And I should see "Introduction"
    # Check that the site page heading does not have a link back
    And I am on site homepage
    Then I cannot follow the page heading
