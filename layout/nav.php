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
 * Layout - nav.
 * This layout is based on a Moodle site index.php file but has been adapted to show news items in a different
 * way.
 *
 * @package   theme_charteredcollege
 * @copyright Copyright (c) 2015 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use theme_charteredcollege\renderables\settings_link;
use theme_charteredcollege\renderables\genius_dashboard_link;

?>
<header id='mr-nav' class='clearfix moodle-has-zindex'>
<div id="charteredcollege-header">
<?php
$sitefullname = format_string($SITE->fullname);
$attrs = array(
    'aria-label' => get_string('home', 'theme_charteredcollege'),
    'id' => 'charteredcollege-home',
    'title' => $sitefullname,
);

if (!empty($PAGE->theme->settings->logo)) {
    $sitefullname = '<span class="sr-only">'.format_string($SITE->fullname).'</span>';
    $attrs['class'] = 'logo';
}

echo html_writer::link($CFG->wwwroot, $sitefullname, $attrs);
?>

<div class="pull-right js-only">
    <?php
    if (class_exists('local_geniusws\navigation')) {
        $bblink = new genius_dashboard_link();
        echo $OUTPUT->render($bblink);
    }

    echo $OUTPUT->personal_menu_trigger();
    echo $OUTPUT->render_message_icon();
   // echo $OUTPUT->render_notification_popups();
    if(isloggedin()) {
        $logoutpix = $OUTPUT->image_url('logout', 'theme');
        $logouturl = $CFG->wwwroot.'/login/logout.php?sesskey='.sesskey();
        $logouticon = '<img id="mypd-logout-icon" class="svg-icon mypd-logout-icon" title="' .$logoutpix. '" alt="' .$logoutpix. '" src="' .$logoutpix. '">';
        echo html_writer::link($logouturl, $logouticon);
    }
    $settingslink = new settings_link();
    echo $OUTPUT->render($settingslink);
    echo '<span class="hidden-md-down">';
    echo core_renderer::search_box();
    echo '</span>';
    ?>
</div>
</div>
<?php
$custommenu = $OUTPUT->custom_menu();

/* Moodle custom menu. */
if (!empty($custommenu)) {
    echo '<div id="charteredcollege-custom-menu">';
    echo $custommenu;
    echo '</div>';
}
?>
</header>

<?php
echo $OUTPUT->personal_menu();
