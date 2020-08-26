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
# Tests for site policy redirects.
#
# @package    theme_charteredcollege
# @copyright  Copyright (c) 2017 Blackboard Inc.
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_charteredcollege
Feature: As an authenticated non-admin user, opening the charteredcollege personal menu redirects to the site policy acceptance
  page when not previously accepted.

  Background:
    Given the following config values are set as admin:
      | defaulthomepage | 0                           |
      | sitepolicy      | http://somesitepolicy.local |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |

  @javascript
  Scenario: Login redirects to site policy page appropriately when personal menu set to show on login.
    Accepting the site policy prevents redirect on next login.
    Given I log in as "student1"
    And I have been redirected to the site policy page
    And I press "Yes"
    And I log out
    When I log in as "student1"
    Then I am currently on the default site home page

  @javascript
  Scenario: Login redirects to site policy page appropriately when personal menu set to not show on login.
    Accepting the site policy prevents redirect next time personal menu is opened.
    Given the following config values are set as admin:
      | personalmenulogintoggle | 0 | theme_charteredcollege |
    And I log in as "student1"
    Then I have been redirected to the site policy page
    And I press "Yes"
    And I log out
    Then I log in as "student1"
    And I am currently on the default site home page
    When I open the personal menu
    And I wait until the page is ready
    Then I should not see "You must agree to this policy to continue using this site. Do you agree?"

  @javascript
  Scenario: Opening personal menu does not redirect when logged in as admin user.
    Given I log in as "admin"
    Then I am currently on the default site home page
