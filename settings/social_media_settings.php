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

use theme_charteredcollege\admin_setting_configurl;

$charteredcollegesettings = new admin_settingpage('themecharteredcollegesocialmedia', get_string('socialmedia', 'theme_charteredcollege'));

    // Social media.
    $name = 'theme_charteredcollege/facebook';
    $title = new lang_string('facebook', 'theme_charteredcollege');
    $description = new lang_string('facebookdesc', 'theme_charteredcollege');
    $default = '';
    $setting = new admin_setting_configurl($name, $title, $description, $default);
    $charteredcollegesettings->add($setting);

    $name = 'theme_charteredcollege/twitter';
    $title = new lang_string('twitter', 'theme_charteredcollege');
    $description = new lang_string('twitterdesc', 'theme_charteredcollege');
    $default = '';
    $setting = new admin_setting_configurl($name, $title, $description, $default);
    $charteredcollegesettings->add($setting);

    $name = 'theme_charteredcollege/linkedin';
    $title = new lang_string('linkedin', 'theme_charteredcollege');
    $description = new lang_string('linkedindesc', 'theme_charteredcollege');
    $default = '';
    $setting = new admin_setting_configurl($name, $title, $description, $default);
    $charteredcollegesettings->add($setting);

    $name = 'theme_charteredcollege/youtube';
    $title = new lang_string('youtube', 'theme_charteredcollege');
    $description = new lang_string('youtubedesc', 'theme_charteredcollege');
    $default = '';
    $setting = new admin_setting_configurl($name, $title, $description, $default);
    $charteredcollegesettings->add($setting);

    $name = 'theme_charteredcollege/instagram';
    $title = new lang_string('instagram', 'theme_charteredcollege');
    $description = new lang_string('instagramdesc', 'theme_charteredcollege');
    $default = '';
    $setting = new admin_setting_configurl($name, $title, $description, $default);
    $charteredcollegesettings->add($setting);

    $settings->add($charteredcollegesettings);
