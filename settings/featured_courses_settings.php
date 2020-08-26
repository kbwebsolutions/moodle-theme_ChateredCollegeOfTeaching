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

defined('MOODLE_INTERNAL') || die;// Main settings.

use theme_charteredcollege\admin_setting_configcourseid;
$charteredcollegesettings = new admin_settingpage('themecharteredcollegefeaturedcourses', get_string('featuredcourses', 'theme_charteredcollege'));

// Featured courses instructions.
$name = 'theme_charteredcollege/fc_instructions';
$heading = '';
$description = get_string('featuredcourseshelp', 'theme_charteredcollege');
$setting = new admin_setting_heading($name, $heading, $description);
$charteredcollegesettings->add($setting);

// Featured courses heading.
$name = 'theme_charteredcollege/fc_heading';
$title = new lang_string('featuredcoursesheading', 'theme_charteredcollege');
$description = '';
$default = new lang_string('featuredcourses', 'theme_charteredcollege');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_RAW_TRIMMED, 50);
$charteredcollegesettings->add($setting);

// Featured courses.
$name = 'theme_charteredcollege/fc_one';
$title = new lang_string('featuredcourseone', 'theme_charteredcollege');
$description = '';
$default = '0';
$setting = new admin_setting_configcourseid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/fc_two';
$title = new lang_string('featuredcoursetwo', 'theme_charteredcollege');
$setting = new admin_setting_configcourseid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/fc_three';
$title = new lang_string('featuredcoursethree', 'theme_charteredcollege');
$setting = new admin_setting_configcourseid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/fc_four';
$title = new lang_string('featuredcoursefour', 'theme_charteredcollege');
$setting = new admin_setting_configcourseid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/fc_five';
$title = new lang_string('featuredcoursefive', 'theme_charteredcollege');
$setting = new admin_setting_configcourseid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/fc_six';
$title = new lang_string('featuredcoursesix', 'theme_charteredcollege');
$setting = new admin_setting_configcourseid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/fc_seven';
$title = new lang_string('featuredcourseseven', 'theme_charteredcollege');
$setting = new admin_setting_configcourseid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/fc_eight';
$title = new lang_string('featuredcourseeight', 'theme_charteredcollege');
$setting = new admin_setting_configcourseid($name, $title, $description, $default, PARAM_RAW_TRIMMED);
$charteredcollegesettings->add($setting);

// Browse all courses link.
$name = 'theme_charteredcollege/fc_browse_all';
$title = new lang_string('featuredcoursesbrowseall', 'theme_charteredcollege');
$description = new lang_string('featuredcoursesbrowsealldesc', 'theme_charteredcollege');
$checked = '1';
$unchecked = '0';
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$charteredcollegesettings->add($setting);

$settings->add($charteredcollegesettings);
