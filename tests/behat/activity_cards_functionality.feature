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
# Test to check that no multimedia files appears at a card description content.
#
# @package    theme_charteredcollege
# @author     Rafael Becerra rafael.becerrarodriguez@blackboard.com
# @copyright  Copyright (c) 2019 Blackboard Inc. (http://www.blackboard.com)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_charteredcollege @theme_charteredcollege_activity_cards
Feature: Check functionality in activity cards.
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
      | teacher1  | C1      | editingteacher  |
      | student1  | C1      | student         |

  @javascript
  Scenario: Add an image to an activity card, student and teacher should not see the image in the content.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Folder" to section "1" and I fill the form with:
      | Name         | Test Page        |
      | Description | <p>Test Content</p><img src="https://download.moodle.org/unittest/test.jpg" alt="test image" width="200" height="150" class="img-responsive atto_image_button_text-bottom"> |
    And I am on "Course 1" course homepage
    And I follow "Topic 1"
    Then I click on "//a[@class='charteredcollege-edit-asset']" "xpath_element"
    And I wait until the page is ready
    And I set the following fields to these values:
      | Display description on course page | 1 |
    And I press "Save and return to course"
    And "img.img-responsive atto_image_button_text-bottom" "css_element" should not exist
    And I log out
    Given I log in as "student1"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Topic 1"
    And "img.img-responsive atto_image_button_text-bottom" "css_element" should not exist

  @javascript
  Scenario Outline: Add an image to an activity card, student and teacher should see the image in the content, when activity display is set as list in charteredcollege settings.
    Given I log in as "admin"
    And the following config values are set as admin:
      | resourcedisplay | <Option> | theme_charteredcollege |
    And I log out
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Folder" to section "1" and I fill the form with:
      | Name         | Test Page        |
      | Description | <p>Test Content</p><img src="https://download.moodle.org/unittest/test.jpg" alt="test image" width="200" height="150" class="img-responsive atto_image_button_text-bottom"> |
    And I am on "Course 1" course homepage
    And I follow "Topic 1"
    Then I click on "//a[@class='charteredcollege-edit-asset']" "xpath_element"
    And I wait until the page is ready
    And I set the following fields to these values:
      | Display description on course page | 1 |
    And I press "Save and return to course"
    And "img.img-responsive.atto_image_button_text-bottom" "css_element" should exist
    And I log out
    Given I log in as "student1"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Topic 1"
    And "img.img-responsive.atto_image_button_text-bottom" "css_element" should exist
    Examples:
      | Option     |
      | 1          |

  @javascript
  Scenario Outline: For activity cards, folder activity should always display "Folder" activity type when content is displayed inline or not.
    Given I log in as "admin"
    And the following config values are set as admin:
      | resourcedisplay | <Option> | theme_charteredcollege |
    And I log out
    Given I log in as "teacher1"
    # in the following setting, display 0 = "On a separate page", 1 = "Inline on a course page".
    And the following "activities" exist:
      | activity | name               | intro                   | course | idnumber | display | showexpanded |
      | folder   | Test folder name 1 | Test folder description | C1     | folder1  | 1       | 1            |
      | folder   | Test folder name 2 | Test folder description | C1     | folder2  | 0       | 1            |
    And I am on "Course 1" course homepage with editing mode on
    Then "li.charteredcollege-resource-long.modtype_folder div.charteredcollege-header-card div.charteredcollege-assettype" "css_element" should exist
    Then "li.charteredcollege-activity.modtype_folder div.charteredcollege-header-card div.charteredcollege-assettype" "css_element" should exist
    Examples:
      | Option   |
      | card     |
      | list     |

  @javascript
  Scenario Outline: For activity cards, when content is displayed inline the tree needs to start with a H3 tag
    Given I log in as "admin"
    And the following config values are set as admin:
      | resourcedisplay | <Option> | theme_charteredcollege |
    And I log out
    Given I log in as "teacher1"
    # in the following setting, display 0 = "On a separate page", 1 = "Inline on a course page".
    And the following "activities" exist:
      | activity | name               | intro                   | course | idnumber | display | showexpanded |
      | folder   | Test folder name 1 | Test folder description | C1     | folder1  | 1       | 1            |
    And I am on "Course 1" course homepage
    And I wait "1" seconds
    Then "li.charteredcollege-activity.modtype_folder div[id^='folder_tree'] #ygtvcontentel1 > div > h3" "css_element" should exist
    Examples:
      | Option   |
      | card     |
      | list     |

  @javascript
  Scenario: For activity cards, when the activity is a lesson the card should not display feedback link.
    Given I skip because "This is failing randomly while accessing the activity as a student, to be fixed in INT-15909"
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I add a "Lesson" to section "0" and I fill the form with:
      | Name | Test lesson |
      | Description | Test lesson description |
    And I click on "//h3/a/p[contains(text(),'Test lesson')]" "xpath_element"
    And I follow "Add a question page"
    And I set the field "Select a question type" to "Short answer"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Short answer question |
      | Page contents | Paper is made from trees. |
      | id_answer_editor_0 | True |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | False |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I log out
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I click on "//h3/a[@class='mod-link']" "xpath_element"
    And I set the following fields to these values:
      | id_answer | True |
    And I press "Submit"
    And I press "Continue"
    And I am on "Course 1" course homepage
    Then I should not see "Feedback available"


