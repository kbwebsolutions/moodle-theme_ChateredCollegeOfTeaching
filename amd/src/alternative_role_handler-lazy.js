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
 * @author    David Castro <david.castro@blackboard.com>
 * @copyright Copyright (c) 2018 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module theme_charteredcollege/alternative_role_handler-lazy
 */
define(['jquery', 'core/templates'],
    function($, templates) {
        return {
            /**
             * Initialising function.
             */
            init: function(courseId) {
                var returnURL = encodeURIComponent(window.location.href.replace(M.cfg.wwwroot, ''));

                templates.render('theme_charteredcollege/return_to_normal_role', {
                    switchroleurl: M.cfg.wwwroot + '/course/switchrole.php?'
                        + 'id=' + courseId
                        + '&sesskey=' + M.cfg.sesskey
                        + '&switchrole=0'
                        + '&returnurl=' + returnURL
                }).then(function(html) {
                    $('#admin-menu-trigger').parent().append(html);
                });
            }
        };
    }
);