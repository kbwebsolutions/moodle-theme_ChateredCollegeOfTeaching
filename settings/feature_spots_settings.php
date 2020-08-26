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

$charteredcollegesettings = new admin_settingpage('themecharteredcollegefeaturespots', get_string('featurespots', 'theme_charteredcollege'));

// Feature spots settings.
// Feature spot instructions.
$name = 'theme_charteredcollege/fs_instructions';
$heading = new lang_string('featurespots', 'theme_charteredcollege');
$description = get_string('featurespotshelp', 'theme_charteredcollege');
$setting = new admin_setting_heading($name, $heading, $description);
$charteredcollegesettings->add($setting);

// Feature spots heading.
$name = 'theme_charteredcollege/fs_heading';
$title = new lang_string('featurespotsheading', 'theme_charteredcollege');
$description = '';
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW, 50);
$charteredcollegesettings->add($setting);

// Feature spot images.
$name = 'theme_charteredcollege/fs_one_image';
$title = new lang_string('featureoneimage', 'theme_charteredcollege');
$opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.svg'));
$setting = new admin_setting_configstoredfile($name, $title, $description, 'fs_one_image', 0, $opts);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/fs_two_image';
$title = new lang_string('featuretwoimage', 'theme_charteredcollege');
$opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.svg'));
$setting = new admin_setting_configstoredfile($name, $title, $description, 'fs_two_image', 0, $opts);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/fs_three_image';
$title = new lang_string('featurethreeimage', 'theme_charteredcollege');
$opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.svg'));
$setting = new admin_setting_configstoredfile($name, $title, $description, 'fs_three_image', 0, $opts);
$charteredcollegesettings->add($setting);

// Feature spot titles.
$name = 'theme_charteredcollege/fs_one_title';
$title = new lang_string('featureonetitle', 'theme_charteredcollege');
$description = '';
$setting = new admin_setting_configtext($name, $title, $description, '');
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/fs_two_title';
$title = new lang_string('featuretwotitle', 'theme_charteredcollege');
$setting = new admin_setting_configtext($name, $title, $description, '');
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/fs_three_title';
$title = new lang_string('featurethreetitle', 'theme_charteredcollege');
$setting = new admin_setting_configtext($name, $title, $description, '');
$charteredcollegesettings->add($setting);

// Feature spot title links.

$name = 'theme_charteredcollege/fs_one_title_link';
$title = new lang_string('featureonetitlelink', 'theme_charteredcollege');
$description = new lang_string('featuretitlelinkdesc', 'theme_charteredcollege');
$linkvalidation = '/(https|http)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?|^\/|^\s*$/';
$setting = new admin_setting_configtext($name, $title, $description, '', $linkvalidation);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/fs_two_title_link';
$title = new lang_string('featuretwotitlelink', 'theme_charteredcollege');
$description = new lang_string('featuretitlelinkdesc', 'theme_charteredcollege');
$setting = new admin_setting_configtext($name, $title, $description, '', $linkvalidation);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/fs_three_title_link';
$title = new lang_string('featurethreetitlelink', 'theme_charteredcollege');
$description = new lang_string('featuretitlelinkdesc', 'theme_charteredcollege');
$setting = new admin_setting_configtext($name, $title, $description, '', $linkvalidation);
$charteredcollegesettings->add($setting);

// Feature spot title checkbox new window links.

$name = 'theme_charteredcollege/fs_one_title_link_cb';
$title = new lang_string('featureonetitlecb', 'theme_charteredcollege');
$description = new lang_string('featuretitlecbdesc', 'theme_charteredcollege');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/fs_two_title_link_cb';
$title = new lang_string('featuretwotitlecb', 'theme_charteredcollege');
$description = new lang_string('featuretitlecbdesc', 'theme_charteredcollege');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/fs_three_title_link_cb';
$title = new lang_string('featurethreetitlecb', 'theme_charteredcollege');
$description = new lang_string('featuretitlecbdesc', 'theme_charteredcollege');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$charteredcollegesettings->add($setting);

// Feature spot text.
$name = 'theme_charteredcollege/fs_one_text';
$description = '';
$title = new lang_string('featureonetext', 'theme_charteredcollege');
$setting = new admin_setting_configtextarea($name, $title, $description, '');
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/fs_two_text';
$title = new lang_string('featuretwotext', 'theme_charteredcollege');
$setting = new admin_setting_configtextarea($name, $title, $description, '');
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/fs_three_text';
$title = new lang_string('featurethreetext', 'theme_charteredcollege');
$setting = new admin_setting_configtextarea($name, $title, $description, '');
$charteredcollegesettings->add($setting);

$settings->add($charteredcollegesettings);
