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
 * Chartered College of Teaching 2021 theme.
 *
 * @package    theme_charteredcollege
 * @copyright  2020 Kieran Briggs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

function theme_charteredcollege_get_main_scss_content($theme) {
    global $CFG;

    // Note, the following code is not fully used yet, only the hardcoded
    // pre and post scss files will be loaded, not any presets defined by
    // settings.

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    if ($filename == 'default.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');

    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_charteredcollege', 'preset', 0, '/', $filename))) {
        // This preset file was fetched from the file area for theme_snap and not theme_boost (see the line above).
        $scss .= $presetfile->get_content();
    } else {
        $scss = '@import "boost";';
    }

    // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.
    $pre = file_get_contents($CFG->dirroot . '/theme/charteredcollege/scss/pre.scss');
    // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.
    $post = file_get_contents($CFG->dirroot . '/theme/charteredcollege/scss/post.scss');

    // Combine them together.
    return $pre . "\n" . $scss . "\n" . $post;
}


/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return array
 */
function theme_charteredcollege_get_pre_scss($theme) {
    global $CFG;

   $scss = '';
    /*
       $settings['brand-primary'] = !empty($theme->settings->themecolor) ? $theme->settings->themecolor : '#3bcedb';
       $userfontsans  = $theme->settings->headingfont;
       if (empty($userfontsans) || in_array($userfontsans, ['Roboto', '"Roboto"'])) {
           $userfontsans = '';
       } else {
           $userfontsans .= ",";
       }
       $fallbacksans = 'Roboto, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
       $settings['font-family-feature'] = $userfontsans . $fallbacksans;

       $userfontserif = $theme->settings->seriffont;
       if (empty($userfontserif) || in_array($userfontserif, ['Georgia', '"Georgia"'])) {
           $userfontserif = '';
       } else {
           $userfontserif .= ",";
       }
       $fallbackserif = 'Georgia,"Times New Roman", Times, serif';
       $settings['font-family-serif'] = $userfontserif . $fallbackserif;

       if (!empty($theme->settings->customisenavbar)) {
           $settings['nav-bg'] = !empty($theme->settings->navbarbg) ? $theme->settings->navbarbg : '#ffffff';
           $settings['nav-color'] = !empty($theme->settings->navbarlink) ? $theme->settings->navbarlink : $settings['brand-primary'];
       }
       if (!empty($theme->settings->customisenavbutton)) {
           $settings['nav-button-bg'] = !empty($theme->settings->navbarbuttoncolor) ? $theme->settings->navbarbuttoncolor : "#ffffff";

           if (!empty($theme->settings->navbarbuttonlink)) {
               $settings['nav-button-color'] = $theme->settings->navbarbuttonlink;
           } else {
               $settings['nav-button-color'] = $settings['brand-primary'];
           }
       }
       if (!empty($theme->settings->customisecustommenu)) {
           if (!empty($theme->settings->custommenutext)) {
               $settings['custom-menu-text-color'] = $theme->settings->custommenutext;
           } else {
               $settings['custom-menu-text-color'] = $settings['brand-primary'];
           }
       }

       foreach ($settings as $key => $value) {
           $scss .= '$' . $key . ': ' . $value . ";\n";
       }
   */
    return $scss;
}