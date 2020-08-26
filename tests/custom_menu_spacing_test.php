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
 * Custom menu spacing tests for charteredcollege.
 *
 * @package   theme_charteredcollege
 * @copyright Copyright (c) 2019 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_charteredcollege\output\core_renderer;

defined('MOODLE_INTERNAL') || die();

/**
 * Custom menu spacing tests for charteredcollege.
 *
 * @package   theme_charteredcollege
 * @copyright Copyright (c) 2019 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_charteredcollege_custom_menu_spacing extends advanced_testcase {
    /**
     * Setup for each test.
     */
    protected function setUp() {
        global $CFG;
        $CFG->theme = 'charteredcollege';
        $this->resetAfterTest(true);
    }

    /**
     * Reviews if spacer exists or not for a given url.
     *
     * @param $url
     * @param bool $exists
     */
    private function review_if_spacer_exists($url, $exists = true) {
        global $PAGE, $CFG;

        $this->resetAfterTest(true);
        $spacer = '';
        if ($exists) {
            $CFG->custommenuitems = 'Blackboard
-Blackboard|http://blackboard.com';
            $spacer = '<div class="charteredcollege-custom-menu-spacer"></div>';

            $css = '#page-content .block_settings.state-visible div.card-body {margin-top: 3em;}';
            $css .= '#page-admin-purgecaches #notice, #notice.charteredcollege-continue-cancel {margin-top: 1.2em;}';
            $spacer .= "<style> {$css} </style>";
        }

        $PAGE->set_url($url);
        /** @var core_renderer $renderer */
        $renderer = $PAGE->get_renderer('theme_charteredcollege', 'core', RENDERER_TARGET_GENERAL);

        $html = $renderer->custom_menu_spacer();
        $this->assertEquals($spacer, $html, 'Custom menu spacer not found in page: ' . $url);
    }

    public function test_spacing_skipped() {
        $this->review_if_spacer_exists('/index.php', false);
    }

    public function test_spacing_applied() {
        $this->review_if_spacer_exists('/index.php');
    }
}
