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
# Tests for settings link.
#
# @package    theme_charteredcollege
# @copyright Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later


@theme @theme_charteredcollege
Feature: When the moodle theme is set to charteredcollege, and global search is enabled, users should see a global search interface.

  Background:
    Given the following config values are set as admin:
      | enableglobalsearch | true |

  @javascript
  Scenario: Non-logged in user sees search interface.
    Given I am on site homepage
    Then ".charteredcollege-login-button" "css_element" should exist
    Then ".search-input-form" "css_element" should exist
    And the following config values are set as admin:
        | enableglobalsearch | |
    And I reload the page
    Then ".search-input-form" "css_element" should not exist

  @javascript
  Scenario: Logged in user sees search interface.
    Given I log in as "admin"
    Then ".search-input-form" "css_element" should exist
    And the following config values are set as admin:
        | enableglobalsearch | |
    And I reload the page
    Then ".search-input-form" "css_element" should not exist

