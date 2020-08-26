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
use theme_charteredcollege\admin_setting_configradiobuttons;

$charteredcollegesettings = new admin_settingpage('themecharteredcollegecoverdisplay', get_string('coverdisplay', 'theme_charteredcollege'));

$name = 'theme_charteredcollege/cover_image';
$heading = new lang_string('poster', 'theme_charteredcollege');
$description = '';
$setting = new admin_setting_heading($name, $heading, $description);
$charteredcollegesettings->add($setting);

// Cover image file setting.
$name = 'theme_charteredcollege/poster';
$title = new lang_string('poster', 'theme_charteredcollege');
$description = new lang_string('posterdesc', 'theme_charteredcollege');
$opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.svg'));
$setting = new admin_setting_configstoredfile($name, $title, $description, 'poster', 0, $opts);
$setting->set_updatedcallback('theme_charteredcollege_process_site_coverimage');
$charteredcollegesettings->add($setting);

// Cover carousel.
$name = 'theme_charteredcollege/cover_carousel_heading';
$heading = new lang_string('covercarousel', 'theme_charteredcollege');
$description = new lang_string('covercarouseldescription', 'theme_charteredcollege');
$setting = new admin_setting_heading($name, $heading, $description);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/cover_carousel';
$title = new lang_string('covercarouselon', 'theme_charteredcollege');
$description = '';
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/slide_one_image';
$title = new lang_string('coverimage', 'theme_charteredcollege');
$description = '';
$opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.svg'));
$setting = new admin_setting_configstoredfile($name, $title, $description, 'slide_one_image', 0, $opts);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/slide_two_image';
$title = new lang_string('coverimage', 'theme_charteredcollege');
$description = '';
$opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.svg'));
$setting = new admin_setting_configstoredfile($name, $title, $description, 'slide_two_image', 0, $opts);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/slide_three_image';
$title = new lang_string('coverimage', 'theme_charteredcollege');
$description = '';
$opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.svg'));
$setting = new admin_setting_configstoredfile($name, $title, $description, 'slide_three_image', 0, $opts);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/slide_one_title';
$title = new lang_string('title', 'theme_charteredcollege');
$description = '';
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/slide_two_title';
$title = new lang_string('title', 'theme_charteredcollege');
$description = '';
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/slide_three_title';
$title = new lang_string('title', 'theme_charteredcollege');
$description = '';
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/slide_one_subtitle';
$title = new lang_string('subtitle', 'theme_charteredcollege');
$description = '';
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/slide_two_subtitle';
$title = new lang_string('subtitle', 'theme_charteredcollege');
$description = '';
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/slide_three_subtitle';
$title = new lang_string('subtitle', 'theme_charteredcollege');
$description = '';
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$charteredcollegesettings->add($setting);

$settings->add($charteredcollegesettings);