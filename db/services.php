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
 * Services
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'theme_charteredcollege_course_card' => [
        'classname'     => 'theme_charteredcollege\\webservice\\ws_course_card',
        'methodname'    => 'service',
        'description'   => 'Course card renderable data',
        'type'          => 'read',
        'ajax'          => true,
        'loginrequired' => true
    ],
    'theme_charteredcollege_cover_image' => [
        'classname'     => 'theme_charteredcollege\\webservice\\ws_cover_image',
        'methodname'    => 'service',
        'description'   => 'Cover image modifier',
        'type'          => 'write',
        'ajax'          => true,
        'loginrequired' => true
    ],
    'theme_charteredcollege_course_completion' => [
        'classname'     => 'theme_charteredcollege\\webservice\\ws_course_completion',
        'methodname'    => 'service',
        'description'   => 'Course completion updater',
        'type'          => 'write',
        'ajax'          => true,
        'loginrequired' => true
    ],
    'theme_charteredcollege_course_toc_chapters' => [
        'classname'     => 'theme_charteredcollege\\webservice\\ws_course_toc_chapters',
        'methodname'    => 'service',
        'description'   => 'Get course TOC chapters',
        'type'          => 'read',
        'ajax'          => true,
        'loginrequired' => true
    ],
    'theme_charteredcollege_course_sections' => [
        'classname'     => 'theme_charteredcollege\\webservice\\ws_course_sections',
        'methodname'    => 'service',
        'description'   => 'Manage course sections',
        'type'          => 'write',
        'ajax'          => true,
        'loginrequired' => true
    ],
    'theme_charteredcollege_course_module_completion' => [
        'classname'     => 'theme_charteredcollege\\webservice\\ws_course_module_completion',
        'methodname'    => 'service',
        'description'   => 'Course module completion',
        'type'          => 'write',
        'ajax'          => true,
        'loginrequired' => true
    ],
    'theme_charteredcollege_course_cards_categories' => [
        'classname'     => 'theme_charteredcollege\\webservice\\ws_course_cards_categories',
        'methodname'    => 'service',
        'description'   => 'Course cards sorting categories',
        'type'          => 'read',
        'ajax'          => true,
        'loginrequired' => true
    ],
    'theme_charteredcollege_course_cards_data' => [
        'classname'     => 'theme_charteredcollege\\webservice\\ws_course_cards_data',
        'methodname'    => 'service',
        'description'   => 'Course card renderable data for web components',
        'type'          => 'read',
        'ajax'          => true,
        'loginrequired' => true
    ],
    'theme_charteredcollege_feed' => [
        'classname'     => 'theme_charteredcollege\\webservice\\ws_feed',
        'methodname'    => 'service',
        'description'   => 'Personal Menu Feed',
        'type'          => 'read',
        'ajax'          => true,
        'loginrequired' => true
    ]
];

