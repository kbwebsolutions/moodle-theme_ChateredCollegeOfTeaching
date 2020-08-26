<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Recycle bin course section tests.
 *
 * @package    theme_charteredcollege
 * @copyright  Copyright (c) 2019 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
use theme_charteredcollege\services\course;

/**
 * Recycle bin course section tests.
 *
 * @package    theme_charteredcollege
 * @copyright  Copyright (c) 2019 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_charteredcollege_course_module_recyclebin_tests extends advanced_testcase {

    /**
     * @var stdClass $course
     */
    protected $course;

    /**
     * @var stdClass the assignment record
     */
    protected $assign;

    /**
     * @var course
     */
    protected $courseservice;

    /**
     * Setup for each test.
     */
    protected function setUp() {
        global $CFG;

        $CFG->theme = 'charteredcollege';
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // We want the course bin to be enabled.
        set_config('coursebinenable', 1, 'tool_recyclebin');

        $this->course = $this->getDataGenerator()->create_course(
            array('numsections' => 3, 'format' => 'weeks'),
            array('createsections' => true));
        $this->assign = $this->getDataGenerator()->create_module('assign',
            array('course' => $this->course, 'section' => 1));

        $this->courseservice = course::service();
    }

    /**
     * Check that our hook is called when an activity is deleted.
     */
    public function test_pre_course_module_delete_hook() {
        global $DB;

        $this->assertEquals(1, $DB->count_records('course_modules',
            ['course' => $this->course->id, 'deletioninprogress' => 0]));
        $this->courseservice->delete_section($this->course->shortname, 1);
        $this->assertEquals(1, $DB->count_records('course_modules',
            ['course' => $this->course->id, 'deletioninprogress' => 1]));
    }
}
