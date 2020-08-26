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
 * @copyright Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * charteredcollege Personal menu.
 */
define(['jquery', 'core/log', 'core/yui', 'theme_charteredcollege/pm_course_cards', 'theme_charteredcollege/util', 'theme_charteredcollege/ajax_notification'],
    function($, log, Y, courseCards, util, ajaxNotify) {

        /**
         * Personal Menu (courses menu).
         * @constructor
         */
        var PersonalMenu = function() {

            var self = this;

            var redirectToSitePolicy = false;

            /**
             * Add deadlines, messages, grades & grading,  async'ly to the personal menu
             *
             */
            this.update = function() {

                // If site policy needs acceptance, then don't update, just redirect to site policy!
                if (redirectToSitePolicy) {
                    var redirect = M.cfg.wwwroot + '/user/policy.php';
                    window.location = redirect;
                    return;
                }

                // Update course cards with info.
                courseCards.reqCourseInfo(courseCards.getCourseIds());


                $('#charteredcollege-pm').focus();

                /**
                 * Load ajax info into personal menu.
                 * @param {string} type
                 */
                var loadAjaxInfo = function(type) {
                    // Target for data to be displayed on screen.
                    var container = $('#charteredcollege-personal-menu-' + type);
                    if ($(container).length) {
                        var cacheKey = M.cfg.sesskey + 'personal-menu-' + type;
                        try {
                            // Display old content while waiting
                            if (util.supportsSessionStorage() && window.sessionStorage[cacheKey]) {
                                log.info('using locally stored ' + type);
                                var html = window.sessionStorage[cacheKey];
                                $(container).html(html);
                            }
                            log.info('fetching ' + type);
                            $.ajax({
                                type: "GET",
                                async: true,
                                url: M.cfg.wwwroot + '/theme/charteredcollege/rest.php?action=get_' + type + '&contextid=' + M.cfg.context,
                                success: function(data) {
                                    ajaxNotify.ifErrorShowBestMsg(data).done(function(errorShown) {
                                        if (errorShown) {
                                            return;
                                        } else {
                                            // No errors, update sesion storage.
                                            log.info('fetched ' + type);
                                            if (util.supportsSessionStorage() && typeof (data.html) != 'undefined') {
                                                window.sessionStorage[cacheKey] = data.html;
                                            }
                                            // Note: we can't use .data because that does not manipulate the dom, we need the data
                                            // attribute populated immediately so things like behat can utilise it.
                                            // .data just sets the value in memory, not the dom.
                                            $(container).attr('data-content-loaded', '1');
                                            $(container).html(data.html);
                                        }
                                    });
                                }
                            });
                        } catch (err) {
                            sessionStorage.clear();
                            log.error(err);
                        }
                    }
                };

                loadAjaxInfo('deadlines');
                loadAjaxInfo('graded');
                loadAjaxInfo('grading');
                loadAjaxInfo('messages');
                loadAjaxInfo('forumposts');

                $(document).trigger('charteredcollegeUpdatePersonalMenu');

                // Triggering event for external PM open listeners.
                // Try/catch added to ensure browser compatibility for webcomponents.
                try {
                    document.dispatchEvent(new Event('charteredcollegePersonalMenuOpen'));
                } catch(error) {
                    log.error(error.message);
                    try {
                        var evt = document.createEvent("Event");
                        evt.initEvent('charteredcollegePersonalMenuOpen', true, true);
                        document.dispatchEvent(evt);
                    } catch(err) {
                        log.error(err.message);
                    }
                }
            };

            /**
             * Apply listeners for personal menu in mobile mode.
             */
            var mobilePersonalMenuListeners = function() {
                /**
                 * Get section left position and height.
                 * @param {string} href
                 * @returns {Object.<string, number>}
                 */
                var getSectionCoords = function(href) {
                    var sections = $("#charteredcollege-pm-content section");
                    var sectionWidth = $(sections).outerWidth();
                    var section = $(href);
                    var selector = "#charteredcollege-pm-updates section > div, #charteredcollege-pm-updates section > charteredcollege-feed > div";
                    var targetSection = $(selector).index(section) + 1;
                    var position = sectionWidth * targetSection;
                    var sectionHeight = $(href).outerHeight() + 200;

                    // Course lists is at position 0.
                    if (href == '#charteredcollege-pm-courses') {
                        position = 0;
                    }

                    // Set the window height.
                    var winHeight = $(window).height();
                    if (sectionHeight < winHeight) {
                        sectionHeight = winHeight;
                    }
                    return {left: position, height: sectionHeight};
                };
                // Personal menu small screen behaviour corrections on resize.
                $(window).on('resize', function() {
                    if (window.innerWidth >= 992) {
                        // If equal or larger than Bootstrap 992 large breakpoint, clear left positions of sections.
                        $('#charteredcollege-pm-content').removeAttr('style');

                        if ($("#charteredcollege-pm-courses div#charteredcollege-personal-menu-intelliboard").length > 0) {
                            var section = $('div#charteredcollege-personal-menu-intelliboard').closest('section');
                            section.prependTo('#charteredcollege-pm-updates');
                        }
                        if ($("#charteredcollege-pm-courses div#charteredcollege-personal-menu-intellicart").length > 0) {
                            var section = $('div#charteredcollege-personal-menu-intellicart').closest('section');
                            section.prependTo('#charteredcollege-pm-updates');
                        }
                        return;
                    }
                    var activeLink = $('#charteredcollege-pm-mobilemenu a.state-active');
                    if (!activeLink || !activeLink.length) {
                        return;
                    }

                    if ($("#charteredcollege-pm-updates div#charteredcollege-personal-menu-intelliboard").length > 0) {
                        var section = $('div#charteredcollege-personal-menu-intelliboard').closest('section');
                        section.appendTo('section#charteredcollege-pm-courses');
                    }
                    if ($("#charteredcollege-pm-updates div#charteredcollege-personal-menu-intellicart").length > 0) {
                        var section = $('div#charteredcollege-personal-menu-intellicart').closest('section');
                        section.appendTo('section#charteredcollege-pm-courses');
                    }

                    var href = activeLink.attr('href');
                    var posHeight = getSectionCoords(href);

                    $('#charteredcollege-pm-content').css('left', '-' + posHeight.left + 'px');
                    $('#charteredcollege-pm-content').css('height', posHeight.height + 'px');
                });
                // Personal menu small screen behaviour.
                $(document).on("click", '#charteredcollege-pm-mobilemenu a', function(e) {
                    var href = this.getAttribute('href');
                    var posHeight = getSectionCoords(href);

                    $("html, body").animate({scrollTop: 0}, 0);
                    $('#charteredcollege-pm-content').animate({
                            left: '-' + posHeight.left + 'px',
                            height: posHeight.height + 'px'
                        }, "700", "swing",
                        function() {
                            // Animation complete.
                        });
                    $('#charteredcollege-pm-mobilemenu a').removeClass('state-active');
                    $(this).addClass('state-active');
                    e.preventDefault();
                });
            };

            /**
             * Apply personal menu listeners.
             */
            var applyListeners = function() {
                // On clicking personal menu trigger.
                $(document).on("click", ".js-charteredcollege-pm-trigger", function(event) {
                    $("html, body").animate({scrollTop: 0}, 0);
                    $('body').toggleClass('charteredcollege-pm-open');
                    if ($('.charteredcollege-pm-open #charteredcollege-pm').is(':visible')) {
                        self.update();
                    }
                    event.preventDefault();

                    // Intelliboard and intellicart should be displayed under the courses on a small screen.
                    if (window.innerWidth < 992) {
                        if ($("#charteredcollege-pm-updates div#charteredcollege-personal-menu-intelliboard").length > 0) {
                            var section = $('div#charteredcollege-personal-menu-intelliboard').closest('section');
                            section.appendTo('section#charteredcollege-pm-courses');
                        }
                        if ($("#charteredcollege-pm-updates div#charteredcollege-personal-menu-intellicart").length > 0) {
                            var section = $('div#charteredcollege-personal-menu-intellicart').closest('section');
                            section.appendTo('section#charteredcollege-pm-courses');
                        }
                    }

                    // If there is a message drawer, dispatch event to avoid opening both elements.
                    if ($('.message-app.main').length === 0) {
                        document.dispatchEvent(new Event("messages-drawer:pm-toggle"));
                    }
                });

                mobilePersonalMenuListeners();
            };

            /**
             * Initialising function.
             * @param {boolean} sitePolicyAcceptReqd
             */
            this.init = function(sitePolicyAcceptReqd) {
                redirectToSitePolicy = sitePolicyAcceptReqd;
                applyListeners();
                if (!redirectToSitePolicy) {
                    courseCards.init();
                }
            };
        };

        return new PersonalMenu();
    }
);
