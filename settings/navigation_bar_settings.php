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

$charteredcollegesettings = new admin_settingpage('themecharteredcollegetopbar', get_string('customtopbar', 'theme_charteredcollege'));

$checked = '1';
$unchecked = '0';

// Use custom colors for navigation bar at top of the screen.
$name = 'theme_charteredcollege/customisenavbar';
$title = new lang_string('customisenavbar', 'theme_charteredcollege');
$description = '';
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$setting->set_updatedcallback('theme_reset_all_caches');
$charteredcollegesettings->add($setting);

// Custom background color of nav bar at top of screen.
$name = 'theme_charteredcollege/navbarbg';
$title = new lang_string('navbarbg', 'theme_charteredcollege');
$description = '';
$default = '#ffffff';
$previewconfig = null;
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
$setting->set_updatedcallback('theme_reset_all_caches');
$charteredcollegesettings->add($setting);

// Color of links in nav bar at top of the screen.
$name = 'theme_charteredcollege/navbarlink';
$title = new lang_string('navbarlink', 'theme_charteredcollege');
$description = '';
$default = '#ff7f41'; // Blackboard Open LMS orange.
$previewconfig = null;
$setting = new \theme_charteredcollege\admin_setting_configcolorwithcontrast(
    \theme_charteredcollege\admin_setting_configcolorwithcontrast::NAVIGATION_BAR, $name, $title, $description, $default, $previewconfig);
$setting->set_updatedcallback('theme_reset_all_caches');
$charteredcollegesettings->add($setting);

// Use custom colors for My Courses button at top of the screen.
$name = 'theme_charteredcollege/customisenavbutton';
$title = new lang_string('customisenavbutton', 'theme_charteredcollege');
$description = '';
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$setting->set_updatedcallback('theme_reset_all_caches');
$charteredcollegesettings->add($setting);

// Color of My Courses link background in nav bar at the top of the screen.
$name = 'theme_charteredcollege/navbarbuttoncolor';
$title = new lang_string('navbarbuttoncolor', 'theme_charteredcollege');
$description = '';
$default = '#ffffff';
$previewconfig = null;
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
$setting->set_updatedcallback('theme_reset_all_caches');
$charteredcollegesettings->add($setting);

// Color of My Courses link text in nav bar at the top of the screen.
$name = 'theme_charteredcollege/navbarbuttonlink';
$title = new lang_string('navbarbuttonlink', 'theme_charteredcollege');
$description = '';
$default = '#ff7f41'; // Blackboard Open LMS orange.
$previewconfig = null;
$setting = new \theme_charteredcollege\admin_setting_configcolorwithcontrast(
    \theme_charteredcollege\admin_setting_configcolorwithcontrast::NAVIGATION_BAR_BUTTON,
    $name, $title, $description, $default, $previewconfig);
$setting->set_updatedcallback('theme_reset_all_caches');
$charteredcollegesettings->add($setting);

// Use custom color for text in the custom menu.
$name = 'theme_charteredcollege/customisecustommenu';
$title = new lang_string('customisecustommenu', 'theme_charteredcollege');
$description = '';
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$setting->set_updatedcallback('theme_reset_all_caches');
$charteredcollegesettings->add($setting);

// Color of the text in the custom menu.
$name = 'theme_charteredcollege/custommenutext';
$title = new lang_string('custommenutext', 'theme_charteredcollege');
$description = '';
$default = '#ffffff'; // Blackboard Open LMS orange.
$previewconfig = null;
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
$setting->set_updatedcallback('theme_reset_all_caches');
$charteredcollegesettings->add($setting);

$settings->add($charteredcollegesettings);
