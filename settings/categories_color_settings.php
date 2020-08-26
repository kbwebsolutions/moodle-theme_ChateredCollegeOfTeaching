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
 * charteredcollege settings.
 *
 * @package   theme_charteredcollege
 * @copyright Copyright (c) 2018 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$charteredcollegesettings = new admin_settingpage('themecharteredcollegecolorcategories', get_string('category_color', 'theme_charteredcollege'));

$name = 'theme_charteredcollege/categorycorlor';

$heading = new lang_string('category_color', 'theme_charteredcollege');
$description = new lang_string('category_color_description', 'theme_charteredcollege');
$setting = new admin_setting_heading($name, $heading, $description);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/category_color_palette';
$title = get_string('category_color_palette', 'theme_charteredcollege');
$description = get_string('category_color_palette_description', 'theme_charteredcollege');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/category_color';
$title = get_string('jsontext', 'theme_charteredcollege');
$description = get_string('jsontextdescription', 'theme_charteredcollege');
$default = '';
$setting = new \theme_charteredcollege\admin_setting_configcolorcategory($name, $title, $description, $default);
$charteredcollegesettings->add($setting);
$setting->set_updatedcallback('theme_reset_all_caches');
$charteredcollegesettings->add($setting);

$settings->add($charteredcollegesettings);