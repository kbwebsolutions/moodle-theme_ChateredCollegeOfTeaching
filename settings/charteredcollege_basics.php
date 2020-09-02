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

$charteredcollegesettings = new admin_settingpage('themecharteredcollegebranding', get_string('basics', 'theme_charteredcollege'));

if (!during_initial_install() && !empty(get_site()->fullname)) {
    // Site name setting.
    $name = 'fullname';
    $title = new lang_string('fullname', 'theme_charteredcollege');
    $description = new lang_string('fullnamedesc', 'theme_charteredcollege');
    $description = '';
    $setting = new admin_setting_sitesettext($name, $title, $description, null);
    $charteredcollegesettings->add($setting);
}

// Main theme colour setting.
$name = 'theme_charteredcollege/themecolor';
$title = new lang_string('themecolor', 'theme_charteredcollege');
$description = '';
$default = '#1b2342'; // Blackboard Open LMS orange.
$previewconfig = null;
$setting = new \theme_charteredcollege\admin_setting_configcolorwithcontrast(
    \theme_charteredcollege\admin_setting_configcolorwithcontrast::BASICS, $name, $title, $description, $default, $previewconfig);
$setting->set_updatedcallback('theme_reset_all_caches');
$charteredcollegesettings->add($setting);

// Site description setting.
$name = 'theme_charteredcollege/subtitle';
$title = new lang_string('sitedescription', 'theme_charteredcollege');
$description = new lang_string('subtitle_desc', 'theme_charteredcollege');
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW_TRIMMED, 50);
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/imagesheading';
$title = new lang_string('images', 'theme_charteredcollege');
$description = '';
$setting = new admin_setting_heading($name, $title, $description);
$charteredcollegesettings->add($setting);

 // Logo file setting.
$name = 'theme_charteredcollege/logo';
$title = new lang_string('logo', 'theme_charteredcollege');
$description = new lang_string('logodesc', 'theme_charteredcollege');
$opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'));
$setting = new admin_setting_configstoredfile($name, $title, $description, 'logo', 0, $opts);
$setting->set_updatedcallback('theme_reset_all_caches');
$charteredcollegesettings->add($setting);


// Favicon file setting.
$name = 'theme_charteredcollege/favicon';
$title = new lang_string('favicon', 'theme_charteredcollege');
$description = new lang_string('favicondesc', 'theme_charteredcollege');
$opts = array('accepted_types' => array('.ico', '.png', '.gif'));
$setting = new admin_setting_configstoredfile($name, $title, $description, 'favicon', 0, $opts);
$setting->set_updatedcallback('theme_reset_all_caches');
$charteredcollegesettings->add($setting);

$name = 'theme_charteredcollege/footerheading';
$title = new lang_string('footnote', 'theme_charteredcollege');
$description = '';
$setting = new admin_setting_heading($name, $title, $description);
$charteredcollegesettings->add($setting);

// Custom footer setting.
$name = 'theme_charteredcollege/footnote';
$title = new lang_string('footnote', 'theme_charteredcollege');
$description = new lang_string('footnotedesc', 'theme_charteredcollege');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$charteredcollegesettings->add($setting);

// Advanced branding heading.
$name = 'theme_charteredcollege/advancedbrandingheading';
$title = new lang_string('advancedbrandingheading', 'theme_charteredcollege');
$description = new lang_string('advancedbrandingheadingdesc', 'theme_charteredcollege');
$setting = new admin_setting_heading($name, $title, $description);
$charteredcollegesettings->add($setting);

// Heading font setting.
$name = 'theme_charteredcollege/headingfont';
$title = new lang_string('headingfont', 'theme_charteredcollege');
$description = new lang_string('headingfont_desc', 'theme_charteredcollege');
$default = '"museo-sans"';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$charteredcollegesettings->add($setting);

// Serif font setting.
$name = 'theme_charteredcollege/seriffont';
$title = new lang_string('seriffont', 'theme_charteredcollege');
$description = new lang_string('seriffont_desc', 'theme_charteredcollege');
$default = '"Georgia"';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$charteredcollegesettings->add($setting);

// Custom CSS file.
$name = 'theme_charteredcollege/customcss';
$title = new lang_string('customcss', 'theme_charteredcollege');
$description = new lang_string('customcssdesc', 'theme_charteredcollege');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$charteredcollegesettings->add($setting);

// Alternative login Settings.
$name = 'theme_charteredcollege/alternativeloginoptionsheading';
$title = new lang_string('alternativeloginoptions', 'theme_charteredcollege');
$description = '';
$setting = new admin_setting_heading($name, $title, $description);
$charteredcollegesettings->add($setting);

// Enable login options display.
$name = 'theme_charteredcollege/enabledlogin';
$title = new lang_string('enabledlogin', 'theme_charteredcollege');
$description = new lang_string('enabledlogindesc', 'theme_charteredcollege');
$default = '0';
$enabledloginchoices = [
    \theme_charteredcollege\output\core_renderer::ENABLED_LOGIN_BOTH        => new lang_string('bothlogin', 'theme_charteredcollege'),
    \theme_charteredcollege\output\core_renderer::ENABLED_LOGIN_MOODLE      => new lang_string('moodlelogin', 'theme_charteredcollege'),
    \theme_charteredcollege\output\core_renderer::ENABLED_LOGIN_ALTERNATIVE => new lang_string('alternativelogin', 'theme_charteredcollege')
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $enabledloginchoices);
$charteredcollegesettings->add($setting);

// Enabled login options order.
$name = 'theme_charteredcollege/enabledloginorder';
$title = new lang_string('enabledloginorder', 'theme_charteredcollege');
$description = new lang_string('enabledloginorderdesc', 'theme_charteredcollege');
$default = '0';
$enabledloginchoices = [
    \theme_charteredcollege\output\core_renderer::ORDER_LOGIN_MOODLE_FIRST      => new lang_string('moodleloginfirst', 'theme_charteredcollege'),
    \theme_charteredcollege\output\core_renderer::ORDER_LOGIN_ALTERNATIVE_FIRST => new lang_string('alternativeloginfirst', 'theme_charteredcollege')
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $enabledloginchoices);
$charteredcollegesettings->add($setting);

$settings->add($charteredcollegesettings);
