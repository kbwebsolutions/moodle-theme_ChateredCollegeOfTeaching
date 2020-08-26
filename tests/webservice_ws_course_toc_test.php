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

use theme_charteredcollege\webservice\ws_course_sections;

/**
 * Test course toc
 * @author    Sebastian Gracia <sebastian.gracia@blackboard.com>
 * @copyright Copyright (c) 2020 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_charteredcollege_ws_course_roc extends \advanced_testcase {

    public function test_service_parameters() {
        $params = \theme_charteredcollege\webservice\ws_course_sections::service_parameters();
        $this->assertTrue($params instanceof external_function_parameters);
    }

    public function test_service_returns() {
        $returns = \theme_charteredcollege\webservice\ws_course_sections::service_returns();
        $this->assertTrue($returns instanceof external_single_structure);
    }

    public function test_service() {

        $this->resetAfterTest();
        global $OUTPUT;

        $shortname = 'test';
        $action = 'toc';

        // Create course.
        $course = $this->getDataGenerator()->create_course(['shortname' => $shortname]);

        $nullformat = null;
        $loadmodules = true;
        $toc = new \theme_charteredcollege\renderables\course_toc($course, $nullformat, $loadmodules);

        $expected = [
            'toc' => $toc->export_for_template($OUTPUT)
        ];

        $serviceresult = \theme_charteredcollege\webservice\ws_course_sections::service($course->shortname, $action, 0, 0, 0);

        $this->assertTrue(is_array($serviceresult));
        $this->assertEquals($expected['toc']->footer, $serviceresult['toc']->footer);
        $this->assertEquals($expected['toc']->chapters, $serviceresult['toc']->chapters);
    }
}