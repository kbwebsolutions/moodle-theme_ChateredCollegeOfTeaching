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
 * Web components loader.
 *
 * @package   theme_charteredcollege
 * @author    David Castro <osdev@blackboard.com>
 * @copyright Copyright (c) 2019 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define([],
    function() {
        return {
            /**
             * Initializes this module.
             * @param componentPaths
             */
            init: function(componentPaths) {
                var compPathsObject = JSON.parse(componentPaths);
                if (compPathsObject !== {}) {
                    // Require all paths.
                    require.config({
                        enforceDefine: false,
                        paths: compPathsObject
                    });

                    var requires = Object.keys(compPathsObject);
                    require(requires, function() {});
                }
            }
        };
    });