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
# Tests for navigation between activities.
#
# @package    theme_charteredcollege
# @author     2017 Jun Pataleta <jun@moodle.com>
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_charteredcollege @theme_charteredcollege_course
Feature: Activity navigation in charteredcollege theme
  In order to quickly switch between activities
  As a user
  I need to use the activity navigation controls in activities

  Background:
    Given the following config values are set as admin:
      | allowstealth | 1    |
      | theme        | charteredcollege |
    Given the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | student1  | Student    | 1         | student1@example.com  |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
      | Course 2 | C2        | topics |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | student1  | C1      | student         |
      | teacher1  | C1      | editingteacher  |
      | student1  | C2      | student         |
    And the following "activities" exist:
      | activity   | name         | intro                       | course | idnumber  | section |
      | assign     | Assignment 1 | Test assignment description | C1     | assign1   | 0       |
      | book       | Book 1       | Test book description       | C1     | book1     | 0       |
      | chat       | Chat 1       | Test chat description       | C1     | chat1     | 0       |
      | choice     | Choice 1     | Test choice description     | C1     | choice1   | 1       |
      | data       | Database 1   | Test database description   | C1     | data1     | 1       |
      | feedback   | Feedback 1   | Test feedback description   | C1     | feedback1 | 1       |
      | folder     | Folder 1     | Test folder description     | C1     | folder1   | 2       |
      | forum      | Forum 1      | Test forum description      | C1     | forum1    | 2       |
      | glossary   | Glossary 1   | Test glossary description   | C1     | glossary1 | 2       |
      | imscp      | Imscp 1      | Test imscp description      | C1     | imscp1    | 3       |
      | label      | Label 1      | Test label description      | C1     | label1    | 3       |
      | lesson     | Lesson 1     | Test lesson description     | C1     | lesson1   | 3       |
      | lti        | Lti 1        | Test lti description        | C1     | lti1      | 4       |
      | page       | Page 1       | Test page description       | C1     | page1     | 4       |
      | quiz       | Quiz 1       | Test quiz description       | C1     | quiz1     | 4       |
      | resource   | Resource 1   | Test resource description   | C1     | resource1 | 5       |
      | scorm      | Scorm 1      | Test scorm description      | C1     | scorm1    | 5       |
      | survey     | Survey 1     | Test survey description     | C1     | survey1   | 5       |
      | url        | Url 1        | Test url description        | C1     | url1      | 6       |
      | wiki       | Wiki 1       | Test wiki description       | C1     | wiki1     | 6       |
      | workshop   | Workshop 1   | Test workshop description   | C1     | workshop1 | 6       |
      | assign     | Assignment 1 | Test assignment description | C2     | assign21  | 0       |
    And I log in as "admin"
    And I reset session storage
    And I am on "Course 1" course homepage with editing mode on
    # Stealth activity.
    And I follow "Topic 2"
    And I follow "Edit \"Forum 1\""
    And I expand all fieldsets
    And I set the field "Visibility" to "Make available but not shown on course page"
    And I press "Save and return to course"
    # Hidden activity.
    And I click on ".charteredcollege-activity[data-type='Glossary'] span.charteredcollege-edit-asset-more" "css_element"
    And I click on ".charteredcollege-activity[data-type='Glossary'] a.js_charteredcollege_hide" "css_element"
    # Hidden section.
    And I follow "Topic 5"
    And I wait until the page is ready
    And I click on "#section-5 a.charteredcollege-visibility.charteredcollege-hide" "css_element"
    # Set up book.
    And I follow "Introduction"
    And I click on "//h3/a/p[contains(text(),'Book 1')]" "xpath_element"
    And I should see "Add new chapter"
    And I set the following fields to these values:
      | Chapter title | Chapter 1                             |
      | Content       | In the beginning... blah, blah, blah. |
    And I press "Save changes"
    And I log out
  @javascript
  Scenario: Step through activities in the course as a teacher.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on "//h3/a/p[contains(text(),'Assignment 1')]" "xpath_element"
    # The first activity won't have the previous activity link.
    Then "#prev-activity-link" "css_element" should not exist
    And I should see "Book 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Book 1')]" "xpath_element"
    And I should see "Assignment" in the "#prev-activity-link" "css_element"
    And I should see "Chat 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Chat 1')]" "xpath_element"
    And I should see "Book 1" in the "#prev-activity-link" "css_element"
    And I should see "Choice 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Choice 1')]" "xpath_element"
    And I should see "Chat 1" in the "#prev-activity-link" "css_element"
    And I should see "Database 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Database 1')]" "xpath_element"
    And I should see "Choice 1" in the "#prev-activity-link" "css_element"
    And I should see "Feedback 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Feedback 1')]" "xpath_element"
    And I should see "Database 1" in the "#prev-activity-link" "css_element"
    # The next link will be Folder 1 because Forum 1 is in stealth mode.
    And I should see "Folder 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Folder 1')]" "xpath_element"
    And I should see "Feedback 1" in the "#prev-activity-link" "css_element"
    # Hidden activity will have a '(hidden)' text within the activity link.
    And I should see "Glossary 1 (hidden)" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Glossary 1 (hidden)')]" "xpath_element"
    And I should see "Folder 1" in the "#prev-activity-link" "css_element"
    And I should see "Imscp 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Imscp 1')]" "xpath_element"
    And I should see "Glossary 1" in the "#prev-activity-link" "css_element"
    # The next link will be Lesson 1 because Label 1 doesn't have a view URL.
    And I should see "Lesson 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Lesson 1')]" "xpath_element"
    And I should see "Imscp 1" in the "#prev-activity-link" "css_element"
    And I should see "Lti 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Lti 1')]" "xpath_element"
    And I should see "Lesson 1" in the "#prev-activity-link" "css_element"
    And I should see "Page 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Page 1')]" "xpath_element"
    And I should see "Lti 1" in the "#prev-activity-link" "css_element"
    And I should see "Quiz 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Quiz 1')]" "xpath_element"
    And I should see "Page 1" in the "#prev-activity-link" "css_element"
    # Hidden sections will have the activities render with the '(hidden)' text.
    And I should see "Resource 1 (hidden)" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Resource 1 (hidden)')]" "xpath_element"
    And I should see "Quiz 1" in the "#prev-activity-link" "css_element"
    And I should see "Scorm 1 (hidden)" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Scorm 1 (hidden)')]" "xpath_element"
    And I should see "Resource 1 (hidden)" in the "#prev-activity-link" "css_element"
    And I should see "Survey 1 (hidden)" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Survey 1 (hidden)')]" "xpath_element"
    And I should see "Scorm 1 (hidden)" in the "#prev-activity-link" "css_element"
    And I should see "Url 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Url 1')]" "xpath_element"
    And I should see "Survey 1 (hidden)" in the "#prev-activity-link" "css_element"
    And I should see "Wiki 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Wiki 1')]" "xpath_element"
    And I should see "Url 1" in the "#prev-activity-link" "css_element"
    And I should see "Workshop 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Workshop 1')]" "xpath_element"
    And I should see "Wiki 1" in the "#prev-activity-link" "css_element"
    # The last activity won't have the next activity link.
    And "#next-activity-link" "css_element" should not exist
  @javascript
  Scenario: Step through activities in the course as a student.
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    When I click on "//h3/a/p[contains(text(),'Assignment 1')]" "xpath_element"
    # The first activity won't have the previous activity link.
    Then "#prev-activity-link" "css_element" should not exist
    And I should see "Book 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Book 1')]" "xpath_element"
    And I should see "Assignment" in the "#prev-activity-link" "css_element"
    And I should see "Chat 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Chat 1')]" "xpath_element"
    And I should see "Book 1" in the "#prev-activity-link" "css_element"
    And I should see "Choice 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Choice 1')]" "xpath_element"
    And I should see "Chat 1" in the "#prev-activity-link" "css_element"
    And I should see "Database 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Database 1')]" "xpath_element"
    And I should see "Choice 1" in the "#prev-activity-link" "css_element"
    And I should see "Feedback 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Feedback 1')]" "xpath_element"
    And I should see "Database 1" in the "#prev-activity-link" "css_element"
    # The next link will be Folder 1 because Forum 1 is in stealth mode.
    And I should see "Folder 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Folder 1')]" "xpath_element"
    And I should see "Feedback 1" in the "#prev-activity-link" "css_element"
    # The next link will be Imscp 1 because hidden activities are not shown to students.
    And I should see "Imscp 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Imscp 1')]" "xpath_element"
    And I should see "Folder 1" in the "#prev-activity-link" "css_element"
    # The next link will be Lesson 1 because Label 1 doesn't have a view URL.
    And I should see "Lesson 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Lesson 1')]" "xpath_element"
    And I should see "Imscp 1" in the "#prev-activity-link" "css_element"
    And I should see "Lti 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Lti 1')]" "xpath_element"
    And I should see "Lesson 1" in the "#prev-activity-link" "css_element"
    And I should see "Page 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Page 1')]" "xpath_element"
    And I should see "Lti 1" in the "#prev-activity-link" "css_element"
    And I should see "Quiz 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Quiz 1')]" "xpath_element"
    And I should see "Page 1" in the "#prev-activity-link" "css_element"
    # Hidden sections will have the activities hidden so the links won't be available to students.
    And I should see "Url 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Url 1')]" "xpath_element"
    And I should see "Quiz 1" in the "#prev-activity-link" "css_element"
    And I should see "Wiki 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Wiki 1')]" "xpath_element"
    And I should see "Url 1" in the "#prev-activity-link" "css_element"
    And I should see "Workshop 1" in the "#next-activity-link" "css_element"
    And I click on "//div/a[contains(text(),'Workshop 1')]" "xpath_element"
    And I should see "Wiki 1" in the "#prev-activity-link" "css_element"
    # The last activity won't have the next activity link.
    And "#next-activity-link" "css_element" should not exist
  @javascript
  Scenario: Jump to another activity as a teacher
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage
    When I click on "//h3/a/p[contains(text(),'Assignment 1')]" "xpath_element"
    Then "Jump to..." "field" should exist
    # The current activity will not be listed.
    And the "Jump to..." select box should not contain "Assignment 1"
    # Stealth activities will not be listed.
    And the "Jump to..." select box should not contain "Forum 1"
    # Resources without view URL (e.g. labels) will not be listed.
    And the "Jump to..." select box should not contain "Label 1"
    # Check drop down menu contents.
    And the "Jump to..." select box should contain "Book 1"
    And the "Jump to..." select box should contain "Chat 1"
    And the "Jump to..." select box should contain "Choice 1"
    And the "Jump to..." select box should contain "Database 1"
    And the "Jump to..." select box should contain "Feedback 1"
    And the "Jump to..." select box should contain "Folder 1"
    And the "Jump to..." select box should contain "Imscp 1"
    And the "Jump to..." select box should contain "Lesson 1"
    And the "Jump to..." select box should contain "Lti 1"
    And the "Jump to..." select box should contain "Page 1"
    And the "Jump to..." select box should contain "Quiz 1"
    And the "Jump to..." select box should contain "Url 1"
    And the "Jump to..." select box should contain "Wiki 1"
    And the "Jump to..." select box should contain "Workshop 1"
    # Hidden activities will be rendered with a '(hidden)' text.
    And the "Jump to..." select box should contain "Glossary 1 (hidden)"
    # Activities in hidden sections will be rendered with a '(hidden)' text.
    And the "Jump to..." select box should contain "Resource 1 (hidden)"
    And the "Jump to..." select box should contain "Scorm 1 (hidden)"
    And the "Jump to..." select box should contain "Survey 1 (hidden)"
    # Jump to an activity somewhere in the middle.
    When I select "Page 1" from the "Jump to..." singleselect
    Then I should see "Page 1"
    And I should see "Lti 1" in the "#prev-activity-link" "css_element"
    And I should see "Quiz 1" in the "#next-activity-link" "css_element"
    # Jump to the first activity.
    And I select "Assignment 1" from the "Jump to..." singleselect
    And I should see "Book 1" in the "#next-activity-link" "css_element"
    But "#prev-activity-link" "css_element" should not exist
    # Jump to the last activity.
    And I select "Workshop 1" from the "Jump to..." singleselect
    And I should see "Wiki 1" in the "#prev-activity-link" "css_element"
    But "#next-activity-link" "css_element" should not exist
    # Jump to a hidden activity.
    And I select "Glossary 1" from the "Jump to..." singleselect
    And I should see "Folder 1" in the "#prev-activity-link" "css_element"
    And I should see "Imscp 1" in the "#next-activity-link" "css_element"
  @javascript
  Scenario: Jump to another activity as a student
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I click on "//h3/a/p[contains(text(),'Assignment 1')]" "xpath_element"
    And "Jump to..." "field" should exist
    # The current activity will not be listed.
    And the "Jump to..." select box should not contain "Assignment 1"
    # Stealth activities will not be listed for students.
    And the "Jump to..." select box should not contain "Forum 1"
    # Resources without view URL (e.g. labels) will not be listed.
    And the "Jump to..." select box should not contain "Label 1"
    # Hidden activities will not be listed for students.
    And the "Jump to..." select box should not contain "Glossary 1"
    # Activities in hidden sections will not be listed for students.
    And the "Jump to..." select box should not contain "Resource 1"
    And the "Jump to..." select box should not contain "Scorm 1"
    And the "Jump to..." select box should not contain "Survey 1"
    # Only activities visible to students will be listed.
    And the "Jump to..." select box should contain "Book 1"
    And the "Jump to..." select box should contain "Chat 1"
    And the "Jump to..." select box should contain "Choice 1"
    And the "Jump to..." select box should contain "Database 1"
    And the "Jump to..." select box should contain "Feedback 1"
    And the "Jump to..." select box should contain "Folder 1"
    And the "Jump to..." select box should contain "Imscp 1"
    And the "Jump to..." select box should contain "Lesson 1"
    And the "Jump to..." select box should contain "Lti 1"
    And the "Jump to..." select box should contain "Page 1"
    And the "Jump to..." select box should contain "Quiz 1"
    And the "Jump to..." select box should contain "Url 1"
    And the "Jump to..." select box should contain "Wiki 1"
    And the "Jump to..." select box should contain "Workshop 1"
    # Jump to an activity somewhere in the middle.
    When I select "Page 1" from the "Jump to..." singleselect
    Then I should see "Page 1"
    And I should see "Lti 1" in the "#prev-activity-link" "css_element"
    And I should see "Quiz 1" in the "#next-activity-link" "css_element"
    # Jump to the first activity.
    And I select "Assignment 1" from the "Jump to..." singleselect
    And I should see "Book 1" in the "#next-activity-link" "css_element"
    But "#prev-activity-link" "css_element" should not exist
    # Jump to the last activity.
    And I select "Workshop 1" from the "Jump to..." singleselect
    And I should see "Wiki 1" in the "#prev-activity-link" "css_element"
    But "#next-activity-link" "css_element" should not exist
  @javascript
  Scenario: Open an activity in a course that only has a single activity
    Given I log in as "student1"
    And I am on "Course 2" course homepage
    And I click on "//h3/a/p[contains(text(),'Assignment 1')]" "xpath_element"
    Then "#prev-activity-link" "css_element" should not exist
    And "#next-activity-link" "css_element" should not exist
    And "Jump to..." "field" should not exist
  @javascript
  Scenario: Shouldn't be able jump to another activity on quiz attempt.
    Given the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
    And quiz "Quiz 1" contains the following questions:
      | question | page | maxmark |
      | TF1      | 1    |         |
      | TF2      | 1    | 3.0     |
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I click on "Topic 4" "text"
    And I click on "//h3/a/p[contains(text(),'Quiz 1')]" "xpath_element"
    And "#prev-activity-link" "css_element" should be visible
    And "#next-activity-link" "css_element" should be visible
    And "Jump to..." "field" should be visible
    And I click on "Attempt quiz now" "text"
    And "#prev-activity-link" "css_element" should not be visible
    And "#next-activity-link" "css_element" should not be visible
    And "Jump to..." "field" should not be visible