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

$charteredcollegesettings = new admin_settingpage('themecharteredcollegepersonalmenu', get_string('personalmenu', 'theme_charteredcollege'));

// Personal menu show course grade in cards.
$name = 'theme_charteredcollege/showcoursegradepersonalmenu';
$title = new lang_string('showcoursegradepersonalmenu', 'theme_charteredcollege');
$description = new lang_string('showcoursegradepersonalmenudesc', 'theme_charteredcollege');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$charteredcollegesettings->add($setting);

// Personal menu deadlines on/off.
$name = 'theme_charteredcollege/deadlinestoggle';
$title = new lang_string('deadlinestoggle', 'theme_charteredcollege');
$description = new lang_string('deadlinestoggledesc', 'theme_charteredcollege');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$charteredcollegesettings->add($setting);

// Personal menu recent feedback & grading on/off.
$name = 'theme_charteredcollege/feedbacktoggle';
$title = new lang_string('feedbacktoggle', 'theme_charteredcollege');
$description = new lang_string('feedbacktoggledesc', 'theme_charteredcollege');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$charteredcollegesettings->add($setting);

// Personal menu messages on/off.
$name = 'theme_charteredcollege/messagestoggle';
$title = new lang_string('messagestoggle', 'theme_charteredcollege');
$description = new lang_string('messagestoggledesc', 'theme_charteredcollege');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$charteredcollegesettings->add($setting);

// Personal menu forum posts on/off.
$name = 'theme_charteredcollege/forumpoststoggle';
$title = new lang_string('forumpoststoggle', 'theme_charteredcollege');
$description = new lang_string('forumpoststoggledesc', 'theme_charteredcollege');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$charteredcollegesettings->add($setting);

// Personal menu display on login on/off.
$name = 'theme_charteredcollege/personalmenulogintoggle';
$title = new lang_string('personalmenulogintoggle', 'theme_charteredcollege');
$description = new lang_string('personalmenulogintoggledesc', 'theme_charteredcollege');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$charteredcollegesettings->add($setting);

// Enable advanced PM feeds.
$name = 'theme_charteredcollege/personalmenuadvancedfeedsenable';
$title = new lang_string('personalmenuadvancedfeedsenable', 'theme_charteredcollege');
$description = new lang_string('personalmenuadvancedfeedsenabledesc', 'theme_charteredcollege');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/personalmenuadvancedfeedsperpage';
$title = new lang_string('personalmenuadvancedfeedsperpage', 'theme_charteredcollege');
$description = new lang_string('personalmenuadvancedfeedsperpagedesc', 'theme_charteredcollege');
$default = '3';
$pmfeedperpagechoices = [
    '3' => '3',
    '4' => '4',
    '5' => '5',
    '6' => '6',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $pmfeedperpagechoices);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/personalmenuadvancedfeedslifetime';
$title = new lang_string('personalmenuadvancedfeedslifetime', 'theme_charteredcollege');
$description = new lang_string('personalmenuadvancedfeedslifetimedesc', 'theme_charteredcollege');
$default = 30 * MINSECS;
$setting = new admin_setting_configduration($name, $title, $description, $default, MINSECS);
$charteredcollegesettings->add($setting);

$settings->add($charteredcollegesettings);

// Advanced feeds hidden settings.
$dependency = 'theme_charteredcollege/personalmenuadvancedfeedsenable';
// Only show per page option if advanced feeds are enabled.
$tohide     = 'theme_charteredcollege/personalmenuadvancedfeedsperpage';
$settings->hide_if($tohide, $dependency, 'notchecked');
// Only show life time if advanced feeds are enabled.
$tohide     = 'theme_charteredcollege/personalmenuadvancedfeedslifetime';
$settings->hide_if($tohide, $dependency, 'notchecked');


