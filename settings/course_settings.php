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

$charteredcollegesettings = new admin_settingpage('themecharteredcollegecoursedisplay', get_string('coursedisplay', 'theme_charteredcollege'));

// Course toc display options.
$name = 'theme_charteredcollege/leftnav';
$title = new lang_string('leftnav', 'theme_charteredcollege');
$list = get_string('list', 'theme_charteredcollege');
$top = get_string('top', 'theme_charteredcollege');
$radios = array('list' => $list, 'top' => $top);
$default = 'list';
$description = new lang_string('leftnavdesc', 'theme_charteredcollege');
$setting = new admin_setting_configradiobuttons($name, $title, $description, $default, $radios);
$charteredcollegesettings->add($setting);

// Resource display options.
$name = 'theme_charteredcollege/resourcedisplay';
$title = new lang_string('resourcedisplay', 'theme_charteredcollege');
$card = new lang_string('card', 'theme_charteredcollege');
$list = new lang_string('list', 'theme_charteredcollege');
$radios = array('list' => $list, 'card' => $card);
$default = 'card';
$description = get_string('resourcedisplayhelp', 'theme_charteredcollege');
$setting = new admin_setting_configradiobuttons($name, $title, $description, $default, $radios);
$charteredcollegesettings->add($setting);

// Which Module metadata field to display in activity cards
$name = 'theme_charteredcollege/modulemetafield';
$title = new lang_string('modulemetafielddisplay', 'theme_charteredcollege');
$description = new lang_string('modulemetafielddescription', 'theme_charteredcollege');
$default = "";
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW_TRIMMED, 50);
$charteredcollegesettings->add($setting);

// Resource and URL description display options.
$name = 'theme_charteredcollege/displaydescription';
$title = new lang_string('displaydescription', 'theme_charteredcollege');
$default = $unchecked;
$description = new lang_string('displaydescriptionhelp', 'theme_charteredcollege');
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$charteredcollegesettings->add($setting);

// Course footer on/off.
$name = 'theme_charteredcollege/coursefootertoggle';
$title = new lang_string('coursefootertoggle', 'theme_charteredcollege');
$description = new lang_string('coursefootertoggledesc', 'theme_charteredcollege');
$default = $checked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$charteredcollegesettings->add($setting);

// Course web service for sections.
$name = 'theme_charteredcollege/coursepartialrender';
$title = new lang_string('coursepartialrender', 'theme_charteredcollege');
$description = new lang_string('coursepartialrenderdesc', 'theme_charteredcollege');
$default = $unchecked;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, $checked, $unchecked);
$charteredcollegesettings->add($setting);

// Lazy loading for pages.
$name = 'theme_charteredcollege/lazyload_mod_page';
$title = get_string('lazyload_mod_page', 'theme_charteredcollege');
$description = get_string('lazyload_mod_page_description', 'theme_charteredcollege');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$charteredcollegesettings->add($setting);

// Choose design for pages.
$name = 'theme_charteredcollege/design_mod_page';
$title = get_string('design_mod_page', 'theme_charteredcollege');
$description = get_string('design_mod_page_description', 'theme_charteredcollege');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$charteredcollegesettings->add($setting);

$settings->add($charteredcollegesettings);