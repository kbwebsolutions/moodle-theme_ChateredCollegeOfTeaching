/**
 * This file is part of Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   theme_charteredcollege
 * @author    Oscar Nadjar oscar.nadjar@blackboard.com
 * @copyright Copyright (c) 2019 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * JS to add a text on the block_calendar_month and block_calendar_upcoming
 * warning that all course events will be loaded only on the core calendar,
 * this only when the $CFG->calendar_adminseesall is true.
 */
define(['jquery', 'core/str'],
    function($, str) {
        return {
            init: function () {

                str.get_strings([
                    {key : 'admineventwarning', component : 'theme_charteredcollege'},
                    {key : 'gotocalendarcharteredcollege', component : 'theme_charteredcollege'},
                ]).done(function(stringsjs) {
                    // Add a warning text about the load of event from courses
                    $( ".block_calendar_upcoming .gotocal a").before(
                        "<span>"+stringsjs[0]+" </span>");
                    $( ".block_calendar_upcoming .gotocal a").text(stringsjs[1]);
                    $( ".block_calendar_upcoming .gotocal ").addClass("alert alert-warning alert-block");
                    $( ".block_calendar_month .card-body" ).append(
                        "<div class='alleventswarning alert alert-warning alert-block'><span>"+stringsjs[0]+
                        "<a href='" + location.origin + "/calendar/view.php'> "+ stringsjs[1] + "</a></span></div>");
                });
            }
        };
    });

