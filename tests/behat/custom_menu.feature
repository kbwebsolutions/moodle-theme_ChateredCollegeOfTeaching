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
# along with Moodle. If not, see <http://www.gnu.org/licenses/>.
#
# Tests for toggle course section visibility in non edit mode in charteredcollege.
#
# @package    theme_charteredcollege
# @author     Rafael Becerra rafael.becerrarodriguez@blackboard.com
# @copyright  Copyright (c) 2019 Blackboard Inc. (http://www.blackboard.com)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_charteredcollege @charteredcollege_custom_menu @theme_charteredcollege_color_check
Feature: When the Moodle theme is set to charteredcollege, custom menu should exist for the site.

  Background:
    Given the following config values are set as admin:
      | linkadmincategories | 0 |
    And I log in as "admin"
    And I am on site homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I expand "Site administration" node
    And I expand "Appearance" node
    And I expand "Themes" node
    And I follow "Theme settings"
    And I set the text field  "Custom menu items" with multi-line text:
      """
      Moodle community|https://moodle.org
      -Moodle free support|https://moodle.org/support
      -Moodle Docs|https://docs.moodle.org|Moodle Docs
      -###
      -Moodle development|https://moodle.org/development
      Moodle.com|https://moodle.com/
      """
    And I click on "Save changes" "button"
    And I log out

  @javascript
  Scenario: Custom menu should exists with the necessary items.
    Given I log in as "teacher1"
    And I am on site homepage
    And I should see "Moodle community"
    And I should see "Moodle.com"
    # Submenu will be shown when the dropdown is clicked.
    And I click on "//header[@id='mr-nav']//div[@id='charteredcollege-custom-menu']//ul[@id='charteredcollege-navbar-content']//li[@class='nav-item dropdown']//a[@class='nav-link dropdown-toggle']" "xpath_element"
    And I should see "Moodle free support"
    And I should see "Moodle Docs"
    And I should see "Moodle development"
    # Check that ### works as a divider
    And "//header[@id='mr-nav']//div[@id='charteredcollege-custom-menu']//ul[@id='charteredcollege-navbar-content']//li[@class='nav-item dropdown show']//div[@class='dropdown-menu show']//div[@class='dropdown-divider']" "xpath_element" should exist

  @javascript
  Scenario: Check custom menu background and text color.
    Given I log in as "admin"
    # Check that the custom menu has the same color as the site color.
    And I check element ".theme-charteredcollege header#mr-nav #charteredcollege-custom-menu nav.navbar" with property "background-color" = "#FF7F41"
    # Check that the text color have white color as a default color.
    And I check element ".theme-charteredcollege header#mr-nav #charteredcollege-custom-menu nav.navbar ul#charteredcollege-navbar-content li.nav-item a" with color "#FFFFFF"

  @javascript
  Scenario: Custom menu should exists in the footer for small screen sizes.
  # Tablet size
    Given I change window size to "768x456"
    And I log in as "Admin"
    And I am on site homepage
    And I should not see "Moodle community" in the "//header[@id='mr-nav']//div[@id='charteredcollege-custom-menu']" "xpath_element"
    And I should see "Moodle community" in the "//footer[@id='moodle-footer']//div[@id='charteredcollege-custom-menu']" "xpath_element"

  @javascript
  Scenario: Custom menu should exists in the header for window full width.
  # 28" size monitor
    Given I change window size to "2518x456"
    And I log in as "Admin"
    And I am on site homepage
    And I should see "Moodle community" in the "//header[@id='mr-nav']//div[@id='charteredcollege-custom-menu']" "xpath_element"
    And I should not see "Moodle community" in the "//footer[@id='moodle-footer']//div[@id='charteredcollege-custom-menu']" "xpath_element"
