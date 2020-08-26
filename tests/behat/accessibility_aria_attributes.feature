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
# Tests for Calendar's anchors aria-label attribute
#
# @package    theme_charteredcollege
# @autor      Oscar Nadjar
# @copyright  Copyright (c) 2019 Blackboard Inc. (http://www.blackboard.com)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later


@theme @theme_charteredcollege @theme_charteredcollege_ax
Feature: Elements for charteredcollege should have the proper aria attributes.

  Background:
    Given the following config values are set as admin:
      | enableglobalsearch | true |
    Given the following "courses" exist:
      | fullname | shortname | category | format | enablecompletion |
      | Course 1 | C1        | 0        | topics | 1                |
      | Course 2 | C2        | 0        | topics | 1                |
      | Course 3 | C3        | 0        | topics | 1                |
      | Course 4 | C4        | 0        | topics | 1                |
      | Course 5 | C5        | 0        | topics | 1                |
      | Course 6 | C6        | 0        | topics | 1                |
      | Course 7 | C7        | 0        | topics | 1                |
      | Course 8 | C8        | 0        | topics | 1                |
      | Course 9 | C9        | 0        | topics | 1                |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | course               | idnumber | name             | intro                         | section | assignsubmission_onlinetext_enabled | completion | completionview |
      | assign   | C1                   | assign1  | Test assignment1 | Test assignment description 1 | 1       | 1                                   | 1          | 0              |
      | assign   | Acceptance test site | assign1  | Test assignment1 | Test assignment description 1 | 1       | 1                                   | 0          | 0              |

  @javascript
  Scenario: All calendar's anchors must contain the aria-label attribute
    Given I log in as "teacher1"
    And I am on the course main page for "C1"
    And I follow "Topic 1"
    Then "#section-1" "css_element" should exist
    And I click on ".charteredcollege-asset .charteredcollege-edit-asset" "css_element"
    And the "aria-label" attribute of "#id_allowsubmissionsfromdate_calendar" "css_element" should contain "Calendar"
    And the "aria-label" attribute of "#id_cutoffdate_calendar" "css_element" should contain "Calendar"
    And the "aria-label" attribute of "#id_gradingduedate_calendar" "css_element" should contain "Calendar"
    And the "aria-label" attribute of "#id_duedate_calendar" "css_element" should contain "Calendar"

  @javascript
  Scenario: Elements in front page must contain the correct attributes
    Given I log in as "admin"
    And the following config values are set as admin:
    | linkadmincategories | 0 |
    And I am on site homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I expand "Site administration" node
    And I expand "Appearance" node
    And I expand "Themes" node
    And I follow "charteredcollege"
    And I click on "form#adminsettings div.settingsform div.row ul#charteredcollege-admin-tabs li:nth-child(5)" "css_element"
    And I set the field with xpath "//div[@class='form-text defaultsnext']//input[@id='id_s_theme_charteredcollege_fc_one']" to "1"
    And I set the field with xpath "//div[@class='form-text defaultsnext']//input[@id='id_s_theme_charteredcollege_fc_two']" to "2"
    And I set the field with xpath "//div[@class='form-text defaultsnext']//input[@id='id_s_theme_charteredcollege_fc_three']" to "3"
    And I set the field with xpath "//div[@class='form-text defaultsnext']//input[@id='id_s_theme_charteredcollege_fc_four']" to "4"
    And I set the field with xpath "//*[@id='id_s_theme_charteredcollege_fc_browse_all']" to "1"
    And I press "Save changes"
    And I am on site homepage
    And the "aria-label" attribute of "div.search-input-wrapper.nav-link div" "css_element" should contain "Search"
    And the "aria-label" attribute of "div#charteredcollege-featured-courses p.text-center a" "css_element" should contain "Browse all courses"
    And the "id" attribute of "div#charteredcollege-featured-courses p.text-center a" "css_element" should contain "browse-all-courses-featured-courses"

  @javascript
  Scenario: Elements in personal menu must contain the correct attributes
    Given I log in as "admin"
    And I am on site homepage
    And I open the personal menu
    # New ID's for personal menu elements are for the most used elements. This ID's are being established in accessibility.js AMD file.
    And the "id" attribute of "a.charteredcollege-personal-menu-more small#charteredcollege-pm-deadline" "css_element" should contain "charteredcollege-pm-deadline"
    And the "id" attribute of "a.charteredcollege-personal-menu-more small#charteredcollege-pm-feedback" "css_element" should contain "charteredcollege-pm-feedback"
    And the "id" attribute of "a.charteredcollege-personal-menu-more small#charteredcollege-pm-messages" "css_element" should contain "charteredcollege-pm-messages"
    And the "id" attribute of "a.charteredcollege-personal-menu-more small#charteredcollege-pm-forum-posts" "css_element" should contain "charteredcollege-pm-forum-posts"
    And the "id" attribute of "div.charteredcollege-pm-user a#charteredcollege-pm-user-profile" "css_element" should contain "charteredcollege-pm-user-profile"
    And the "id" attribute of "div.charteredcollege-pm-user div#charteredcollege-pm-header-quicklinks a#charteredcollege-pm-profile" "css_element" should contain "charteredcollege-pm-profile"
    And the "id" attribute of "div.charteredcollege-pm-user div#charteredcollege-pm-header-quicklinks a#charteredcollege-pm-dashboard" "css_element" should contain "charteredcollege-pm-dashboard"
    And the "id" attribute of "div.charteredcollege-pm-user div#charteredcollege-pm-header-quicklinks a#charteredcollege-pm-preferences" "css_element" should contain "charteredcollege-pm-preferences"
    And the "id" attribute of "div.charteredcollege-pm-user div#charteredcollege-pm-header-quicklinks a#charteredcollege-pm-grades" "css_element" should contain "charteredcollege-pm-grades"

  @javascript
  Scenario: Elements in course main view must contain the correct attributes
    Given I log in as "admin"
    And I am on the course main page for "C1"
    And the "id" attribute of "div.toc-footer a#charteredcollege-new-section" "css_element" should contain "charteredcollege-new-section"
    And the "id" attribute of "div.toc-footer a#charteredcollege-course-tools" "css_element" should contain "charteredcollege-course-tools"

  @javascript
  Scenario: Elements in course dashboard must contain the correct attributes
    Given I log in as "admin"
    And I am on the course main page for "C1"
    And I click on "#charteredcollege-course-wrapper .toc-footer a:nth-child(2)" "css_element"
    And the "id" attribute of "div#coursetools-list a#ct-course-settings" "css_element" should contain "ct-course-settings"
    And the "id" attribute of "div#coursetools-list a#ct-open-grader" "css_element" should contain "ct-open-grader"
    And the "id" attribute of "div#coursetools-list a#ct-course-gradebook" "css_element" should contain "ct-course-gradebook"
    And the "id" attribute of "div#coursetools-list a#ct-participants-number" "css_element" should contain "ct-participants-number"
    And the "id" attribute of "div#coursetools-list a#ct-open-reports" "css_element" should contain "ct-open-reports"
    And the "id" attribute of "div#coursetools-list a#ct-pld" "css_element" should contain "ct-pld"
    And the "id" attribute of "div#coursetools-list a#ct-competencies" "css_element" should contain "ct-competencies"
    And the "id" attribute of "div#coursetools-list a#ct-badges" "css_element" should contain "ct-badges"

    @javascript
    Scenario: When creating a new activity in charteredcollege, the mod chooser should have a specific ID
      Given I log in as "admin"
      And I am on the course main page for "C1"
      And the "id" attribute of "//li[@id='section-0']//div[@class='content']//div[@class='col-sm-6 charteredcollege-modchooser']" "xpath_element" should contain "charteredcollege-create-activity"
