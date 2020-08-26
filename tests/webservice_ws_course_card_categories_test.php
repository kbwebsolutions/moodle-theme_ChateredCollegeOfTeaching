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

defined('MOODLE_INTERNAL') || die();

use theme_charteredcollege\webservice\ws_course_card;

/**
 * Test course card web service
 * @author    diego casas <diego.casas@blackboard.com>
 * @copyright Copyright (c) 2019 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_charteredcollege_ws_course_card_categories_test extends \advanced_testcase {

    public function test_service_parameters() {
        $params = \theme_charteredcollege\webservice\ws_course_cards_categories::service_parameters();
        $this->assertTrue($params instanceof external_function_parameters);
    }

    public function test_service_returns() {
        $returns = \theme_charteredcollege\webservice\ws_course_cards_categories::service_returns();
        $this->assertTrue($returns instanceof external_multiple_structure);
    }

    public function test_service() {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $sturole = $DB->get_record('role', array('shortname' => 'student'));

        // Create course.
        $course = $this->getDataGenerator()->create_course();
        // Enrol student.
        $this->getDataGenerator()->enrol_user($user->id,
            $course->id,
            $sturole->id);

        // Create course.
        $course = $this->getDataGenerator()->create_course([
            'startdate' => '1252304000',
            'enddate' => '1262304000'
        ]);
        // Enrol student.
        $this->getDataGenerator()->enrol_user($user->id,
            $course->id,
            $sturole->id);

        // Create course.
        $course = $this->getDataGenerator()->create_course([
            'startdate' => '1352304000',
            'enddate' => '1362304000'
        ]);
        // Enrol student.
        $this->getDataGenerator()->enrol_user($user->id,
            $course->id,
            $sturole->id);

        // Create course.
        $course = $this->getDataGenerator()->create_course([
            'startdate' => '1452304000',
            'enddate' => '1462304000'
        ]);
        // Enrol student.
        $this->getDataGenerator()->enrol_user($user->id,
            $course->id,
            $sturole->id);

        $this->setUser($user);

        $serviceresult = \theme_charteredcollege\webservice\ws_course_cards_categories::service('');

        $this->assertTrue(is_array($serviceresult));

        $this->assertCount(4, $serviceresult);

    }
}
