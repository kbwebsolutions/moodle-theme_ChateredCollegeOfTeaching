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
 * @copyright Copyright (c) 2015 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/log', 'core/ajax', 'core/str', 'core/templates', 'core/notification',
    'theme_charteredcollege/util', 'theme_charteredcollege/ajax_notification', 'theme_charteredcollege/footer_alert',
    'core/event', 'core/fragment'],
    function($, log, ajax, str, templates, notification, util, ajaxNotify, footerAlert, Event, fragment) {

        var self = this;

        /**
         * Items being moved - actual dom elements.
         * @type {array}
         */
        var movingObjects = [];

        /**
         * Item being moved - actual dom element.
         * @type {object}
         */
        var movingObject;

        /**
         * @type {boolean}
         */
        var ajaxing = false;

        var ajaxTracker;

        /**
         * Sections that are being retrieved by the API.
         * @type {Array}
         */
        var sectionsProcess = [];

        /**
         * Module html caching.
         * @type {object|null}
         */
        var moduleCache = null;

        /**
         * Progress caching.
         * @type {Array|null}
         */
        var progressCache = null;

        /**
         * Sets observers for the TOC elements.
         */
        var setTocObservers = function () {
            if (self.courseConfig.format == 'weeks' || self.courseConfig.format == 'topics') {
                $('#course-toc .chapter-title').click(function(e) {
                    var link = $(e.target);
                    var section = link.attr('href');
                    if (typeof section != 'undefined' && section.length > 0) {
                        getSection(section.split('#section-')[1], 0);
                    }
                });

                $('#toc-searchables li a').click(function(e) {
                    var link = $(e.target);
                    var urlParams = link.attr('href').split("&"),
                        section = urlParams[0],
                        mod = urlParams[1] || null;
                    section = section.split('#section-')[1];
                    getSection(section, mod);
                });
            }
        };

        /**
         * Sets observers for the navigation arrows.
         */
        var setNavigationFooterObservers = function () {
            if (self.courseConfig.format == 'weeks' || self.courseConfig.format == 'topics') {
                $('.section_footer .next_section, .section_footer .icon-arrow-right, ' +
                    '.section_footer .previous_section, .section_footer .icon-arrow-left').click(function(e) {
                    var link = $(e.target);
                    var section = link.attr('section-number');
                    if(typeof section !== 'undefined' && section.length > 0) {
                        getSection(section, 0);
                    }
                });

                $('.section_footer .text').click(function (e) {
                    var node = $(e.target);
                    var section = node.find('.nav_guide').attr('section-number');
                    if(typeof section !== 'undefined' && section.length > 0) {
                        getSection(section, 0);
                    }
                });
            }
        };

        /**
         * Scroll to a mod via search.
         * @param {string} modid
         */
        var scrollToModule = function(modid) {
            // Sometimes we have a hash, sometimes we don't.
            // Strip hash then add just in case.
            $('#toc-search-results').html('');
            var targmod = $("#" + modid.replace('#', ''));
            // http://stackoverflow.com/questions/6677035/jquery-scroll-to-element
            util.scrollToElement(targmod);

            var searchpin = $("#searchpin");
            if (!searchpin.length) {
                searchpin = $('<i id="searchpin"></i>');
            }

            $(targmod).find('.instancename').prepend(searchpin);
            $(targmod).attr('tabindex', '-1').focus();
            $('#course-toc').removeClass('state-visible');
        };

        /**
         * Update moving message.
         */
        var updateMovingMessage = function() {
            var title;
            if (movingObjects.length === 1) {
                var assetname = $(movingObjects[0]).find('.charteredcollege-asset-link .instancename').html();
                assetname = assetname || M.util.get_string('pluginname', 'label', assetname);
                title = M.util.get_string('moving', 'theme_charteredcollege', assetname);
            } else {
                title = M.util.get_string('movingcount', 'theme_charteredcollege', movingObjects.length);
            }
            footerAlert.setTitle(title);
        };

        /**
         * Updates the drop zone with a descriptive text.
         * @param sectionName
         */
        var updateSectionDropMsg = function (sectionName) {
            if (typeof movingObjects !== 'undefined' && movingObjects.length > 0) {
                $('.section-drop').each(function() {
                    var sectionDropMsg = M.util.get_string('movingdropsectionhelp', 'theme_charteredcollege',
                        {moving: sectionName, before: $(this).data('title')}
                    );
                    $(this).html(sectionDropMsg);
                });
                footerAlert.setSrNotice(M.util.get_string('movingstartedhelp', 'theme_charteredcollege', sectionName));

            }
        };

        /**
         * Gets a specific section for the current course and if an activity module is passed sets focus on it.
         * @param section
         * @param mod
         */
        var getSection = function (section, mod) {
            var node = $('#section-' + section);
            if (node.length == 0 && sectionsProcess.indexOf(section) == -1) {
                sectionsProcess.push(section);
                var params = {courseid: self.courseConfig.id, section: section};
                $('.sk-fading-circle').show();
                // We need to prevent the DOM to show the default section.
                $('.course-content .' + self.courseConfig.format + ' li[id^="section-"]').hide();
                fragment.loadFragment('theme_charteredcollege', 'section', self.courseConfig.contextid, params).done(function(html, js) {
                    var node = $(html);
                    renderSection(section, node, mod, js);

                    var folders = node.find('li.charteredcollege-activity.modtype_folder');
                    $.each(folders, function (index, folder) {
                        var content = $(folder).find('div.contentwithoutlink div.charteredcollege-assettype');
                        if (content.length > 0) {
                            if ($(folder).find('div.activityinstance div.charteredcollege-header-card .asset-type').length == 0) {
                                var folderAssetTypeHeader = $(folder).find('div.activityinstance div.charteredcollege-header-card');
                                content.prependTo(folderAssetTypeHeader);
                            }
                        }
                    });
                    // Modify Folder tree activity with inline content to have a H3 tag and have the same behavior that the
                    // folder with content in a separate page has.
                    $('#section-' + section + ' li.charteredcollege-activity.modtype_folder div[id^="folder_tree"] ' +
                        '> ul > li > div > span.fp-filename').each(function () {
                        $(this).replaceWith("<h3>"+$(this).text()+"</h3>");
                    });
                });
            }
        };

        /**
         * This functions inserts a section node to the DOM.
         * @param section
         * @param html
         * @param mod
         */
        var renderSection = function(section, html, mod, js) {
            var anchor = $('.course-content .' + self.courseConfig.format);
            var existingSections = [];
            anchor.find('li[id^=section-]').each(function() {
                existingSections.push(parseInt($(this).attr('id').split('section-')[1]));
            });
            var tempnode = $('<div></div>');
            templates.replaceNodeContents(tempnode, html, '');
            // Append resource card fadeout to content resource card.
            tempnode.find('.charteredcollege-resource-long .contentafterlink .charteredcollege-resource-card-fadeout').each(function() {
                $(this).appendTo($(this).prevAll('.charteredcollege-resource-long .contentafterlink .no-overflow'));
            });
            // Remove from Dom the completion tracking when it is disabled for an activity.
            tempnode.find('.charteredcollege-header-card .charteredcollege-header-card-icons .disabled-charteredcollege-asset-completion-tracking').remove();
            if (existingSections.length > 0) {
                var closest = existingSections.reduce(function(prev, curr) {
                    return (Math.abs(curr - section) < Math.abs(prev - section) ? curr : prev);
                });

                if (closest > section) {
                    anchor.find('#section-' + closest).before(tempnode.find('li[id^="section-"]'));

                } else {
                    anchor.find('#section-' + closest).after(tempnode.find('li[id^="section-"]'));

                }
            } else {
                $('.sk-fading-circle').after(tempnode);
            }
            templates.runTemplateJS(js);

            // Hide loading animation.
            $('.sk-fading-circle').hide();
            // Notify filters about the new section.
            Event.notifyFilterContentUpdated($('.course-content .' + self.courseConfig.format));
            var sections = anchor.find('li[id^="section-"]');
            // When not present the section, the first one will be shown as default, remove all classes to prevent that.
            sections.removeClass('state-visible');
            var id = '#section-' + section;
            $(id).addClass('state-visible');
            if (self.courseConfig.toctype == 'top' && self.courseConfig.format == 'topics' && section > 0) {
                var title = $(id).find('.sectionname').html();
                var elements = $('.chapter-title');
                var tmpid = 0;
                // Find the right toc element.
                $.each(elements, function(key, element) {
                    if ($(element).attr('href').split('#section-')[1] == section) {
                        tmpid = key;
                    }
                });
                $(id).find('.sectionname').html(title);
                $(id).find('.sectionnumber').html(tmpid + '.');
            }
            // Leave all course sections as they were.
            sections.show();
            $(id).find('.section_footer .next_section, .section_footer .icon-arrow-right, ' +
                '.section_footer .previous_section, .section_footer .icon-arrow-left').click(function(e) {
                var link = $(e.target);
                var section = link.attr('section-number');
                if(typeof section !== 'undefined' && section.length > 0) {
                    getSection(section, 0);
                }
            });

            $(id).find('.section_footer .text').click(function (e) {
                var node = $(e.target);
                var section = node.find('.nav_guide').attr('section-number');
                if(typeof section !== 'undefined' && section.length > 0) {
                    getSection(section, 0);
                }
            });

            // Set observer for mod chooser.
            $(id + ' .section-modchooser-link').click(function() {
                // Grab the section number from the button.
                var sectionNum = $(this).attr('data-section');
                $('.charteredcollege-modchooser-addlink').each(function() {
                    // Update section in mod link to current section.
                    var newLink = this.href.replace(/(section=)[0-9]+/ig, '$1' + sectionNum);
                    $(this).attr('href', newLink);
                });
            });

            // If a module id has been passed as parameter, set focus.
            if (mod != 0 && typeof mod !== 'undefined') {
                scrollToModule(mod);
            }
            var sectionName = $('#region-main .section-moving').find('.sectionname').text();
            if (typeof sectionName !== 'undefined' && sectionName.length > 0) {
                updateSectionDropMsg(sectionName);
            }

            var movingId = $('#region-main .section-moving').attr('id');
            if (typeof movingId !== 'undefined' && movingId.length > 0) {
                $('#section-' + (parseInt(movingId.split('section-')[1]) + 1) +
                    ' .charteredcollege-drop.section-drop').removeClass('partial-render');
            }

            $('#course-toc #chapters li').removeClass('charteredcollege-visible-section');
            // Set link as current.
            $('#course-toc .chapter-title[href="#section-'+ section +'"]').parent('li').addClass('charteredcollege-visible-section');
            $(id).find('ul.section').append(
                '<li class="charteredcollege-drop asset-drop">' +
                '<div class="asset-wrapper">' +
                '<a href="#">' +
                M.util.get_string('movehere', 'theme_charteredcollege') +
                '</a>' +
                '</div>' +
                '</li>');
        };



    return {
        init: function(courseLib) {

            self.courseConfig = courseLib.courseConfig;

            /**
             * AJAX tracker class - for tracking chained AJAX requests (prevents behat intermittent faults).
             * Also, sets and unsets ajax classes on trigger element / child of trigger if specified.
             */
            var AjaxTracker = function() {

                var triggersByKey = {};

                /**
                 * Starts tracking.
                 * @param {string} jsPendingKey
                 * @param {domElement} trigger
                 * @param {string} subSelector
                 * @returns {boolean}
                 */
                this.start = function(jsPendingKey, trigger, subSelector) {
                    if (this.ajaxing(jsPendingKey)) {
                        log.debug('Skipping ajax request for ' + jsPendingKey + ', AJAX already in progress');
                        return false;
                    }
                    M.util.js_pending(jsPendingKey);
                    triggersByKey[jsPendingKey] = {trigger: trigger, subSelector: subSelector};
                    if (trigger) {
                        if (subSelector) {
                            $(trigger).find(subSelector).addClass('ajaxing');
                        } else {
                            $(trigger).addClass('ajaxing');
                        }
                    }
                    return true;
                };

                /**
                 * Is there an AJAX request in progress.
                 * @param {string} jsPendingKey
                 * @returns {boolean}
                 */
                this.ajaxing = function(jsPendingKey) {
                    return M.util.pending_js.indexOf(jsPendingKey) > -1;
                };

                /**
                 * Completes tracking.
                 * @param {string} jsPendingKey
                 */
                this.complete = function(jsPendingKey) {
                    var trigger, subSelector;
                    if (triggersByKey[jsPendingKey]) {
                        trigger = triggersByKey[jsPendingKey].trigger;
                        subSelector = triggersByKey[jsPendingKey].subSelector;
                    }
                    if (trigger) {
                        if (subSelector) {
                            $(trigger).find(subSelector).removeClass('ajaxing');
                        } else {
                            $(trigger).removeClass('ajaxing');
                        }
                    }
                    delete triggersByKey[jsPendingKey];
                    M.util.js_complete(jsPendingKey);
                };
            };

            ajaxTracker = new AjaxTracker();

            /**
             * Get the section number from a section element.
             * @param {jQuery|object} el
             * @returns {number}
             */
            var sectionNumber = function(el) {
                if (self.courseConfig.partialrender) {
                    return (parseInt($(el).attr('id').split('section-')[1]));
                } else {
                    return (parseInt($(el).attr('id').replace('section-', '')));
                }
            };

            /**
             * Get the section number for an element within a section.
             * @param {object} el
             * @returns {number}
             */
            var parentSectionNumber = function(el) {
                return sectionNumber($(el).parents('li.section.main')[0]);
            };

            /**
             * Moving has stopped, clean up.
             */
            var stopMoving = function() {
                $('body').removeClass('charteredcollege-move-inprogress');
                $('body').removeClass('charteredcollege-move-section');
                $('body').removeClass('charteredcollege-move-asset');
                footerAlert.hideAndReset();
                $('.section-moving').removeClass('section-moving');
                $('.asset-moving').removeClass('asset-moving');
                $('.charteredcollege-asset a').removeAttr('tabindex');
                $('.charteredcollege-asset button').removeAttr('disabled');
                $('.js-charteredcollege-asset-move').removeAttr('checked');
                movingObjects = [];
                if (self.courseConfig.partialrender) {
                    $('.charteredcollege-drop.section-drop').addClass('partial-render');
                }
            };

            /**
             * Move fail - sad face :(.
             */
            var moveFailed = function() {
                var actname = $(movingObject).find('.instancename').html();

                footerAlert.removeAjaxLoading();
                footerAlert.setTitle(M.util.get_string('movefailed', 'theme_charteredcollege', actname));
                // Stop moving in 2 seconds so that the user has time to see the failed moving notice.
                window.setTimeout(function() {
                    // Don't pass in target, we want to abort the move!
                    stopMoving(false);
                }, 2000);
            };

            /**
             * Remove moving object from moving objects array.
             * @param {object} obj
             */
            var removeMovingObject = function(obj) {
                var index = movingObjects.indexOf(obj);
                if (index > -1) {
                    movingObjects.splice(index, 1);
                }
                updateMovingMessage();
            };

            /**
             * General move request
             *
             * @param {object}   params
             * @param {function} onSuccess
             * @param {bool}     finalItem
             */
            var ajaxReqMoveGeneral = function(params, onSuccess, finalItem) {
                if (ajaxing) {
                    // Request already made.
                    log.debug('Skipping ajax request, one already in progress');
                    return;
                }

                // Add spinner.
                footerAlert.addAjaxLoading();

                // Set common params.
                params.sesskey = M.cfg.sesskey;
                params.courseId = courseLib.courseConfig.id;
                params.field = 'move';

                log.debug('Making course/rest.php request', params);
                var req = $.ajax({
                    type: "POST",
                    async: true,
                    data: params,
                    url: M.cfg.wwwroot + courseLib.courseConfig.ajaxurl
                });
                req.done(function(data) {
                    ajaxNotify.ifErrorShowBestMsg(data).done(function(errorShown) {
                        if (errorShown) {
                            log.debug('Ajax request fail');
                            moveFailed();
                            return;
                        } else {
                            // No errors, call success callback and stop moving if necessary.
                            log.debug('Ajax request successful');
                            if (onSuccess) {
                                onSuccess();
                            }
                            if (finalItem) {
                                if (params.class === 'resource') {
                                    // Only stop moving for resources, sections handle this later once the TOC is reloaded.
                                    stopMoving();
                                }
                            }
                        }
                    });
                });
                req.fail(function() {
                    moveFailed();
                });

                if (finalItem) {
                    req.always(function() {
                        ajaxing = false;
                        footerAlert.removeAjaxLoading();
                    });
                }
            };

            /**
             * Get section title.
             * @param {integer} section
             * @returns {*|jQuery}
             */
            var getSectionTitle = function(section) {
                // Get title from TOC.
                if (self.courseConfig.partialrender) {
                    return  $('#course-toc #chapters > li a[href="#section-' + section + '"]').text();
                } else {
                    return $('#chapters li:nth-of-type(' + (section + 1) + ') .chapter-title').html();
                }
            };

            /**
             * Update next / previous links.
             * @param {string} selector
             * @return {promise}
             */
            var updateSectionNavigation = function(selector) {
                var dfd = $.Deferred();
                var sections, totalSectionCount;
                if (!selector) {
                    if (self.courseConfig.partialrender) {
                        selector = '#course-toc #chapters > li a';
                    } else {
                        selector = '#region-main .course-content > ul li.section';
                    }
                    sections = $(selector);
                    totalSectionCount = sections.length;
                } else {
                    sections = $(selector);
                    if (self.courseConfig.partialrender) {
                        var allSections = $('#course-toc #chapters > li a');
                    } else {
                        var allSections = $('#region-main .course-content > ul li.section');
                    }
                    totalSectionCount = allSections.length;
                }

                var completed = 0;
                $.each(sections, function(idx, el) {
                    if (self.courseConfig.partialrender) {
                        var href = $(el).attr('href');
                        var sectionNum;
                        if (typeof href !== typeof undefined && href !== false) {
                            sectionNum = parseInt($(el).attr('href').split('#section-')[1]);
                        } else {
                            sectionNum = parseInt($(el).attr('id').split('section-')[1]);
                        }
                    } else {
                        var sectionNum = sectionNumber(el);
                    }
                    var previousSection = sectionNum - 1;
                    var nextSection = sectionNum + 1;
                    var previous = false;
                    var next = false;
                    var hidden, extraclasses;
                    if (previousSection > -1) {
                        if (self.courseConfig.partialrender) {
                            hidden = $('#section-' + previousSection).hasClass('draft');
                        } else {
                            hidden = $('#section-' + previousSection).hasClass('hidden');
                        }
                        extraclasses = hidden ? ' dimmed_text' : '';
                        previous = {
                            section: previousSection,
                            title: getSectionTitle(previousSection),
                            classes: extraclasses
                        };
                    }
                    if (nextSection < totalSectionCount) {
                        if (self.courseConfig.partialrender) {
                            hidden = $('#section-' + nextSection).hasClass('draft');
                        } else {
                            hidden = $('#section-' + nextSection).hasClass('hidden');
                        }
                        extraclasses = hidden ? ' dimmed_text' : '';
                        next = {
                            section: nextSection,
                            title: getSectionTitle(nextSection),
                            classes: extraclasses
                        };
                    }
                    var navigation = {
                        previous: previous,
                        next: next
                    };
                    templates.render('theme_charteredcollege/course_section_navigation', navigation)
                        .done(function(result) {
                            var target = $('#section-' + sectionNum + ' .section_footer');
                            if (target.length > 0) {
                                target.replaceWith(result);
                            }
                            completed++;
                            if (completed === sections.length) {
                                dfd.resolve();
                            }
                        });

                });
                return dfd.promise();
            };

            /**
             * Calculates how the sections are ordered after moving.
             * @param sections
             * @param oldIndex
             * @param newIndex
             * @returns {array}
             */
            var calculateSections = function (sections, oldIndex, newIndex) {
                if (newIndex >= sections.length) {
                    var k = newIndex - sections.length + 1;
                    while (k--) {
                        sections.push(undefined);
                    }
                }
                sections.splice(newIndex, 0, sections.splice(oldIndex, 1)[0]);
                return sections;
            };

            /**
             * Update sections.
             */
            var updateSections = function(current, target, predeleteSections, deletedSection) {
                if (courseLib.courseConfig.partialrender) {
                    var loadedSections = [];
                    var sections = [];
                    if (current != 0 && target != 0) {
                        $.each($('#course-toc #chapters > li a'), function (idx, obj) {
                            sections.push($(obj).attr('href').split('#section-')[1]);
                        });
                        var newOrder = calculateSections(sections, current, target);
                    } else {
                        sections = predeleteSections;
                        predeleteSections.splice(deletedSection, 1);
                        var newOrder = predeleteSections;
                    }
                    $.each($('#region-main .course-content > ul li.section'), function(idx, obj) {
                        var value = $(obj).attr('id').split('section-')[1];
                        var key = newOrder.indexOf(value);
                        var chapterTitle = getSectionTitle(key);
                        var fullTitle = chapterTitle;
                        $(obj).attr('id', 'section-' + key);
                        if (self.courseConfig.toctype == 'top' && self.courseConfig.format == 'topics' && key > 0) {
                            fullTitle = `<span class='sectionnumber'> ${key}.</span>${chapterTitle}`;
                        }
                        $('#section-' + key + ' .content .sectionname').html(fullTitle);
                        loadedSections.push(key);
                        // Uodate the attribute.
                        $(obj).find('a.section-modchooser-link').attr('data-section', key);
                    });
                    sectionsProcess = loadedSections;
                } else {
                    // Renumber section ids, rename section titles.
                    $.each($('#region-main .course-content > ul li.section'), function(idx, obj) {
                        $(obj).attr('id', 'section-' + idx);
                        // Get title from TOC (note that its idx + 1 because first entry is
                        // introduction.
                        var chapterTitle = getSectionTitle(idx);
                        // Update section title with corresponding TOC title - this is necessary
                        // for weekly topic courses where the section title needs to stay the
                        // same as the TOC.
                        var fullTitle = chapterTitle;
                        if (self.courseConfig.toctype == 'top' && self.courseConfig.format == 'topics' && idx > 0) {
                            fullTitle = `<span class='sectionnumber'></span>${chapterTitle}`;
                        }
                        $('#section-' + idx + ' .content .sectionname').html(fullTitle);
                        // Update section data attribute to reflect new section idx.
                        $(this).find('a.section-modchooser-link').attr('data-section', idx);
                    });
                }

                updateSectionNavigation().done(function() {
                    if (courseLib.courseConfig.partialrender) {
                        setCourseSectionObervers();
                    }
                });
            };

            /**
             * Delete section dialog and confirm function.
             * @param {object} e
             * @param {object} el
             */
            var sectionDelete = function(e, el) {
                e.preventDefault();
                var sectionNum = parentSectionNumber(el);
                var section = $('#section-' + sectionNum);
                var sectionName = section.find('.sectionname').text();

                /**
                 * Delete section.
                 */
                var doDelete = function() {

                    if (!ajaxTracker.start('section_delete', el)) {
                        // Already in progress.
                        return;
                    }

                    var delProgress = M.util.get_string('deletingsection', 'theme_charteredcollege', sectionName);

                    footerAlert.addAjaxLoading('');
                    footerAlert.show();
                    footerAlert.setTitle(delProgress);

                    var params = {
                        courseshortname: courseLib.courseConfig.shortname,
                        action: 'delete',
                        sectionnumber: sectionNum,
                        value: 1,
                        loadmodules: true,
                    };

                    log.debug('Making course/rest.php section delete request', params);

                    // Make ajax call.
                    var ajaxPromises = ajax.call([
                        {
                            methodname: 'theme_charteredcollege_course_sections',
                            args: params
                        }
                    ], true, true);
                    var sections = [];
                    $.each($('#course-toc #chapters > li a'), function (idx, obj) {
                        sections.push($(obj).attr('href').split('#section-')[1]);
                    });
                    // Handle ajax promises.
                    ajaxPromises[0]
                        .done(function(response) {
                            // Update TOC.
                            templates.render('theme_charteredcollege/course_toc', response.toc)
                                .done(function(result) {
                                    $('#course-toc').html($(result).html());
                                    $(document).trigger('charteredcollegeTOCReplaced');
                                    // Remove section from DOM.
                                    section.remove();
                                    updateSections(0, 0, sections, sectionNum);
                                    // Current section no longer exists so change location to previous section.
                                    if (self.courseConfig.partialrender) {
                                        var chapters = $('.chapter-title');
                                        var ids = [];
                                        $.each(chapters, function (key, element) {
                                            ids.push($(element).attr('href').split('#section-')[1]);
                                        });
                                        var closest = ids.reduce(function(prev, curr) {
                                            return (Math.abs(curr - sectionNum) < Math.abs(prev - sectionNum) ? curr : prev);
                                        });
                                        location.hash = 'section-' + closest;
                                        if ($('li#section-' + closest).length == 1) {
                                            courseLib.showSection();
                                        } else {
                                            getSection(closest, 0);
                                        }
                                    } else {
                                        if (sectionNum >= $('.course-content > ul li.section').length) {
                                            location.hash = 'section-' + (sectionNum - 1);
                                        }
                                        courseLib.showSection();
                                    }
                                    // We can't complete the action in the 'always' section because we want it to
                                    // definitely be called after the section is removed from the DOM.
                                    ajaxTracker.complete('section_delete');
                                })
                                .always(function() {
                                    // Allow another request now this has finished.
                                    footerAlert.hideAndReset();
                                })
                                .fail(function() {
                                    ajaxTracker.complete('section_delete');
                                });
                        })
                        .fail(function(response) {
                            ajaxNotify.ifErrorShowBestMsg(response);
                            footerAlert.hideAndReset();
                            // Allow another request now this has finished.
                            ajaxTracker.complete('section_delete');
                        });
                };

                var delTitle = M.util.get_string('confirm', 'moodle');
                var delConf = M.util.get_string('confirmdeletesection', 'moodle', sectionName);
                var ok = M.util.get_string('deletesectionconfirm', 'theme_charteredcollege');
                var cancel = M.util.get_string('cancel', 'moodle');
                notification.confirm(delTitle, delConf, ok, cancel, doDelete);
            };

            /**
             * Generic action handler for all asset actions.
             * @param {event} e
             * @param {domNode} triggerEl
             */
            var assetAction = function(e, triggerEl) {
                e.preventDefault();

                var assetEl = $($(triggerEl).parents('.charteredcollege-asset')[0]),
                    cmid = Number(assetEl[0].id.replace('module-', '')),
                    instanceName = assetEl.find('.instancename').text().trim(),
                    action = $(triggerEl).data('action'),
                    errActionKey = '',
                    errMessageKey = '',
                    errAction = '',
                    errMessage = '',
                    jsPendingKey = 'asset_' + action;

                if (ajaxTracker.ajaxing(jsPendingKey)) {
                    // Already in progress.
                    // We check this because we don't want to show the confirmation dialog when in progress.
                    return;
                }

                var actionAJAX = function() {
                    if (!ajaxTracker.start(jsPendingKey, assetEl, '.charteredcollege-edit-asset-more')) {
                        // Request already made.
                        return;
                    }

                    var params = {
                        'action': action,
                        'sectionreturn': 0,
                        'id': cmid
                    };

                    ajax.call([
                        {
                            methodname: 'core_course_edit_module',
                            args: params
                        }
                    ], true, true)[0]
                        .done(function(response) {
                            ajaxNotify.ifErrorShowBestMsg(response, errAction, errMessage).done(function(errorShown) {
                                ajaxTracker.complete(jsPendingKey);
                                if (errorShown) {
                                    log.debug('Ajax request fail');
                                    return;
                                } else {
                                    log.debug('Ajax request successful');

                                    // Reset module cache.
                                    moduleCache = null;
                                    progressCache = null;
                                    if (action === 'delete') {
                                        // Remove asset from DOM.
                                        assetEl.remove();
                                        // Remove asset searchable.
                                        $('#toc-searchables li[data-id="' + cmid + '"]').remove();
                                    } else if (action === 'show') {
                                        assetEl.removeClass('draft');
                                    } else if (action === 'hide') {
                                        assetEl.addClass('draft');
                                    } else if (action === 'duplicate') {
                                        assetEl.replaceWith(response);
                                    }
                                }
                            });
                        })
                        .fail(function(response) {
                            ajaxNotify.ifErrorShowBestMsg(response, errAction, errMessage).done(function() {
                                ajaxTracker.complete(jsPendingKey);
                            });
                        })
                        .always(function() {
                            footerAlert.hideAndReset();
                        });
                };

                /**
                 * Get error strings incase of AJAX failure.
                 * @returns {*|Promise}
                 */
                var getErrorStrings = function() {
                    if (action === 'duplicate') {
                        errActionKey = 'action:duplicateasset';
                        errMessageKey = 'error:failedtoduplicateasset';
                    } else if (action === 'show' || action === 'hide') {
                        errActionKey = 'action:changeassetvisibility';
                        errMessageKey = 'error:failedtochangeassetvisibility';
                    } else if (action === 'delete') {
                        errActionKey = 'action:deleteasset';
                        errMessageKey = 'error:failedtodeleteasset';
                    }
                    return str.get_strings([
                        {key: errActionKey, component: 'theme_charteredcollege'},
                        {key: errMessageKey, component: 'theme_charteredcollege'}
                    ]);
                };

                getErrorStrings().then(function(strings) {
                    errAction = strings[0];
                    errMessage = strings[0];
                    if (action === 'delete') {
                        // Create confirmation strings.
                        var delConf = '',
                            plugindata = {
                                type: M.util.get_string('pluginname', assetEl.attr('class').match(/modtype_([^\s]*)/)[1])
                            };
                        if (instanceName !== '') {
                            plugindata.name = instanceName;
                            delConf = M.util.get_string('deletechecktypename', 'moodle', plugindata);
                        } else {
                            delConf = M.util.get_string('deletechecktype', 'moodle', plugindata);
                        }

                        var delTitle = M.util.get_string('confirm', 'moodle');
                        var ok = M.util.get_string('deleteassetconfirm', 'theme_charteredcollege', plugindata.type);
                        var cancel = M.util.get_string('cancel', 'moodle');
                        notification.confirm(delTitle, delConf, ok, cancel, actionAJAX);
                    } else {
                        actionAJAX();
                    }
                });
            };

            /**
             * Ajax request to move asset to target.
             * @param {object} target
             */
            var ajaxReqMoveAsset = function(target) {
                var params = {};

                log.debug('Move objects', movingObjects);

                // Prepare request parameters
                params.class = 'resource';

                updateMovingMessage();

                movingObject = movingObjects.shift();

                params.id = Number(movingObject.id.replace('module-', ''));

                if (target && !$(target).hasClass('charteredcollege-drop')) {
                    params.beforeId = Number($(target)[0].id.replace('module-', ''));
                } else {
                    params.beforeId = 0;
                }

                if (document.body.id === "page-site-index") {
                    params.sectionId = 1;
                } else {
                    if (target) {
                        params.sectionId = parentSectionNumber(target);
                    } else {
                        params.sectionId = parentSectionNumber(movingObject);
                    }
                }

                if (movingObjects.length > 0) {
                    ajaxReqMoveGeneral(params, function() {
                        $(target).before($(movingObject));
                        // recurse
                        ajaxReqMoveAsset(target);
                    }, false);
                } else {
                    ajaxReqMoveGeneral(params, function() {
                        $(target).before($(movingObject));
                    }, true);
                }

            };

            /**
             * Ajax request to move section to target.
             * @param {str|object} dropzone
             */
            var ajaxReqMoveSection = function(dropzone) {
                var domTargetSection = parentSectionNumber(dropzone);
                var currentSection = sectionNumber(movingObjects[0]);
                var targetSection = currentSection < domTargetSection ?
                        domTargetSection - 1 :
                        domTargetSection;

                var params = {
                    "class": 'section',
                    id: currentSection,
                    value: targetSection
                };

                ajaxReqMoveGeneral(params, function() {

                    // Update TOC chapters.
                    ajax.call([
                        {
                            methodname: 'theme_charteredcollege_course_toc_chapters',
                            args: {
                                courseshortname: courseLib.courseConfig.shortname
                            },
                            done: function(response) {
                                // Update TOC.
                                templates.render('theme_charteredcollege/course_toc_chapters', response.chapters)
                                    .done(function(result) {
                                        // Update chapters.
                                        $('#chapters').replaceWith(result);

                                        // Move current section before target section.
                                        $('#section-' + domTargetSection).before($('#section-' + currentSection));

                                        // Update section ids, next previous links, etc.
                                        updateSections(currentSection, targetSection, [], null);

                                        // Navigate to section in its new location.
                                        location.hash = 'section-' + targetSection;
                                        courseLib.showSection();

                                        // Finally, we have finished moving the section!
                                        stopMoving();
                                    });
                            },
                            fail: function(response) {
                                ajaxNotify.ifErrorShowBestMsg(response);
                                stopMoving();
                            }
                        }
                    ], true, true);

                }, true);
            };

            /**
             * Listen for edit action clicks, hide, show, duplicate, etc..
             */
            var assetEditListeners = function() {
                var actionSelectors = '.charteredcollege-asset-actions .js_charteredcollege_hide, ';
                actionSelectors += '.charteredcollege-asset-actions .js_charteredcollege_show, ';
                actionSelectors += '.charteredcollege-asset-actions .js_charteredcollege_delete, ';
                actionSelectors += '.charteredcollege-asset-actions .js_charteredcollege_duplicate';

                $(document).on('click', actionSelectors, function(e) {
                    assetAction(e, this);
                });
            };

            /**
             * Generic section action handler.
             *
             * @param {string} action visibility, highlight
             * @param {null|function} onComplete for when completed.
             */
            var sectionActionListener = function(action, onComplete) {

                $('#region-main').on('click', '.charteredcollege-section-editing.actions .charteredcollege-' + action, function(e) {

                    e.stopPropagation();
                    e.preventDefault();

                    var trigger = this;

                    /**
                     * Invalid section action exception.
                     *
                     * @param {string} action
                     */
                    var InvalidActionException = function(action) {
                        this.message = 'Invalid section action: ' + action;
                        this.name = 'invalidActionException';
                    };

                    // Check action is valid.
                    var validactions = ['visibility', 'highlight'];
                    if (validactions.indexOf(action) === -1) {
                        throw new InvalidActionException(action);
                    }

                    if (!ajaxTracker.start('section_' + action, trigger)) {
                        // Request already in progress.
                        return;
                    }

                    // For toggling visibility.
                    var toggle, loadModules = true;
                    if (action === 'visibility') {
                        toggle = $(this).hasClass('charteredcollege-hide') ? 0 : 1;
                        if (moduleCache && moduleCache.length > 0 && progressCache && progressCache.length > 0) {
                            loadModules = false;
                        }
                    } else {
                        // For toggling highlight/mark as current.
                        toggle = $(this).attr('aria-pressed') === 'true' ? 0 : 1;
                    }
                    var sectionNumber = parentSectionNumber(this);
                    var sectionActionsSelector = '#section-' + sectionNumber + ' .charteredcollege-section-editing';
                    var actionSelector = sectionActionsSelector + ' .charteredcollege-' + action;

                    // Make ajax call.
                    var ajaxPromises = ajax.call([
                        {
                            methodname: 'theme_charteredcollege_course_sections',
                            args : {
                                courseshortname: courseLib.courseConfig.shortname,
                                action: action,
                                sectionnumber: sectionNumber,
                                value: toggle,
                                loadmodules: loadModules,
                            }
                        }
                    ], true, true);

                    // Handle ajax promises.
                    ajaxPromises[0]
                    .fail(function(response) {
                        var errMessage, errAction;
                        if (action === 'visibility') {
                            errMessage = M.util.get_string('error:failedtochangesectionvisibility', 'theme_charteredcollege');
                            errAction = M.util.get_string('action:changesectionvisibility', 'theme_charteredcollege');
                        } else {
                            errMessage = M.util.get_string('error:failedtohighlightsection', 'theme_charteredcollege');
                            errAction = M.util.get_string('action:highlightsectionvisibility', 'theme_charteredcollege');
                        }
                        ajaxNotify.ifErrorShowBestMsg(response, errAction, errMessage).done(function() {
                            // Allow another request now this has finished.
                            ajaxTracker.complete('section_' + action);
                        });
                    }).always(function() {
                        $(trigger).removeClass('ajaxing');
                    }).done(function(response) {
                        // Update section action and then reload TOC.
                        return templates.render('theme_charteredcollege/course_action_section', response.actionmodel)
                        .then(function(result) {
                            $(actionSelector).replaceWith(result);
                            $(actionSelector).focus();
                            // Update TOC.
                            if (!loadModules) {
                                if (moduleCache && moduleCache.length > 0 && response.toc.modules.length === 0) {
                                    // Modules not loaded on request. Replacing them on the toc.
                                    response.toc.modules = moduleCache;
                                }

                                if (progressCache && progressCache.length > 0) {
                                    var progressCacheCopy = progressCache.slice(0);
                                    $.each(response.toc.chapters.chapters, function(index) {
                                        response.toc.chapters.chapters[index].progress = progressCacheCopy.shift();
                                    });
                                }
                            }

                            if (loadModules) {
                                // Caching modules for future use.
                                moduleCache = response.toc.modules;

                                // Caching progress for future use.
                                progressCache = [];
                                $.each(response.toc.chapters.chapters, function(index, value) {
                                    progressCache.push(value.progress);
                                });
                            }

                            return templates.render('theme_charteredcollege/course_toc', response.toc);
                        }).then(function(result) {
                            $('#course-toc').html($(result).html());
                            $(document).trigger('charteredcollegeTOCReplaced');
                            if (onComplete && typeof (onComplete) === 'function') {
                                var completion = onComplete(sectionNumber, toggle);
                                if (self.courseConfig.partialrender) {
                                    if (typeof onComplete === 'function') {
                                        ajaxTracker.complete('section_' + action);
                                    }
                                } else {
                                    if (completion && typeof (completion.always) === 'function') {
                                        // Callback returns a promise, js no longer running.
                                        completion.always(
                                            function() {
                                                // Allow another request now this has finished.
                                                ajaxTracker.complete('section_' + action);
                                            }
                                        );
                                    } else {
                                        // Callback does not return a promise, js no longer running.
                                        // Allow another request now this has finished.
                                        ajaxTracker.complete('section_' + action);
                                    }
                                }
                            } else {
                                // Allow another request now this has finished.
                                ajaxTracker.complete('section_' + action);
                            }
                        });
                    });
                });
            };

            /**
             * Highlight section on click.
             */
            var highlightSectionListener = function() {
                sectionActionListener('highlight', function(sectionNumber) {
                    $('#section-' + sectionNumber).toggleClass('current');

                    // Reset sections which are not highlighted.
                    var $notCurrent = $('li.section.main')
                    .not('#section-' + sectionNumber)
                    .not('#section-0').removeClass("current");

                    $notCurrent.each(function() {
                        var highlighter = $(this).find('.charteredcollege-highlight');
                        var sectionNumber = parentSectionNumber(highlighter);
                        var newLink = $(highlighter).attr('href').replace(/(marker=)[0-9]+/ig, '$1' + sectionNumber);
                        $(highlighter).attr('href', newLink).attr('aria-pressed', 'false');
                    });
                });
            };

            /**
             * Delete section on click.
             */
            var deleteSectionListener = function() {
                $(document).on('click', '.charteredcollege-section-editing.actions .charteredcollege-delete', function(e) {
                    sectionDelete(e, this);
                });
            };

            /**
             * Toggle section visibility on click.
             */
            var toggleSectionListener = function() {
                /**
                 * Toggle hidden class and update section navigation.
                 * @param {number} sectionNumber
                 * @param {boolean} toggle
                 * @returns {Promise}
                 */
                var manageHiddenClass = function(sectionNumber, toggle) {
                    if (toggle === 0) {
                        $('#section-' + sectionNumber).addClass('hidden');
                    } else {
                        $('#section-' + sectionNumber).removeClass('hidden');
                        $('#section-' + sectionNumber + ' .charteredcollege-activity .charteredcollege-stealth-tag').remove();
                        $('#section-' + sectionNumber + ' .charteredcollege-activity').removeClass('stealth');
                    }

                    // Update the section navigation either side of the current section.
                    var selectors = [
                        '#section-' + (sectionNumber - 1),
                        '#section-' + (sectionNumber + 1)
                    ];
                    var selector = selectors.join(',');
                    return updateSectionNavigation(selector);
                };
                sectionActionListener('visibility', manageHiddenClass);
            };

            /**
             * Show footer alert for moving.
             */
            var footerAlertShowMove = function() {
                footerAlert.show(function(e) {
                    e.preventDefault();
                    stopMoving();
                });
            };

            /**
             * When section move link is clicked, get the data we need and start the move.
             */
            var moveSectionListener = function() {
                // Listen clicks on move links.
                $("#region-main").on('click', '.charteredcollege-section-editing.actions .charteredcollege-move', function(e) {
                    e.stopPropagation();
                    e.preventDefault();

                    $('body').addClass('charteredcollege-move-inprogress');
                    footerAlertShowMove();

                    // Moving a section.
                    var sectionNumber = parentSectionNumber(this);
                    log.debug('Section is', sectionNumber);
                    var section = $('#section-' + sectionNumber);
                    var sectionName = section.find('.sectionname').text();

                    log.debug('Moving this section', sectionName);
                    movingObjects = [section];
                    // This should never happen, but just in case...
                    $('.section-moving').removeClass('section-moving');
                    section.addClass('section-moving');
                    $('a[href="#section-' + sectionNumber + '"]').parent('li').addClass('section-moving');
                    $('body').addClass('charteredcollege-move-section');
                    if (self.courseConfig.partialrender) {
                        $('#section-' + (sectionNumber + 1) + ' .charteredcollege-drop.section-drop').removeClass('partial-render');
                    }
                    var title = M.util.get_string('moving', 'theme_charteredcollege', sectionName);
                    footerAlert.setTitle(title);
                    updateSectionDropMsg(sectionName);
                });
            };

            /**
             * Add drop zones at the end of sections.
             */
            var addAfterDrops = function() {
                if (document.body.id === "page-site-index") {
                    $('#region-main .sitetopic ul.section').append(
                        '<li class="charteredcollege-drop asset-drop">' +
                        '<div class="asset-wrapper">' +
                        '<a href="#">' +
                        M.util.get_string('movehere', 'theme_charteredcollege') +
                        '</a>' +
                        '</div>' +
                        '</li>');
                } else {
                    $('li.section .content ul.section').append(
                        '<li class="charteredcollege-drop asset-drop">' +
                        '<div class="asset-wrapper">' +
                        '<a href="#">' +
                        M.util.get_string('movehere', 'theme_charteredcollege') +
                        '</a>' +
                        '</div>' +
                        '</li>');
                }
            };

            /**
             * Add listener for move checkbox.
             */
            var assetMoveListener = function() {
                $("#region-main").on('change', '.js-charteredcollege-asset-move', function(e) {
                    e.stopPropagation();

                    var asset = $(this).parents('.charteredcollege-asset')[0];

                    // Make sure after drop is at the end of section.
                    var section = $(asset).parents('ul.section')[0];
                    var afterdrop = $(section).find('li.charteredcollege-drop.asset-drop');
                    $(section).append(afterdrop);

                    if (movingObjects.length === 0) {
                        // Moving asset - activity or resource.
                        // Initiate move.
                        var assetname = $(asset).find('.charteredcollege-asset-link .instancename').html();

                        log.debug('Moving this asset', assetname);

                        var classes = $(asset).attr('class'),
                            regex = /(?=charteredcollege-mime)([a-z0-9\-]*)/;
                        var assetclasses = regex.exec(classes);
                        classes = '';
                        if (assetclasses) {
                            classes = assetclasses.join(' ');
                        }
                        log.debug('Moving this class', classes);
                        $(asset).addClass('asset-moving');
                        $('.charteredcollege-asset button').attr('disabled','disabled');
                        $(asset).find('button').removeAttr('disabled');
                        $('.charteredcollege-asset .charteredcollege-asset-content a').attr('tabindex','-1');
                        $(asset).find('a').removeAttr('tabindex');

                        $(asset).find('.js-charteredcollege-asset-move').prop('checked', 'checked');

                        $('body').addClass('charteredcollege-move-inprogress');
                        $('body').addClass('charteredcollege-move-asset');

                    }

                    if ($(this).prop('checked')) {
                        // Add asset to moving array.
                        movingObjects.push(asset);
                        $(asset).find('a').removeAttr('tabindex');
                        $(asset).find('button').removeAttr('disabled');
                        $(asset).addClass('asset-moving');
                    } else {
                        // Remove from moving array.
                        removeMovingObject(asset);
                        // Remove moving class
                        $(asset).find('.charteredcollege-asset-content a').attr('tabindex','-1');
                        $(asset).find('button').attr('disabled','disabled');
                        $(asset).removeClass('asset-moving');
                        if (movingObjects.length === 0) {
                            // Nothing is ticked for moving, cancel the move.
                            stopMoving();
                        }
                    }
                    footerAlertShowMove();
                    updateMovingMessage();
                });
            };

            /**
             * When an asset or drop zone is clicked, execute move.
             */
            var movePlaceListener = function() {
                $(document).on('click', '.charteredcollege-move-note, .charteredcollege-drop', function(e) {
                    log.debug('charteredcollege drop clicked', e);
                    if (movingObjects) {
                        e.stopPropagation();
                        e.preventDefault();
                        if ($('body').hasClass('charteredcollege-move-section')) {
                            ajaxReqMoveSection(this);
                        } else {
                            var target;
                            if ($(this).hasClass('charteredcollege-drop')) {
                                target = this;
                            } else {
                                target = $(this).closest('.charteredcollege-asset');
                            }
                            ajaxReqMoveAsset(target);
                        }
                    }
                });
            };

            /**
             * Set observers for TOC and navigation buttons in the footer.
             */
            var setCourseSectionObervers = function () {
                setTocObservers();
                setNavigationFooterObservers();
            };

            /**
             * Add listeners.
             */
            var addListeners = function() {
                moveSectionListener();
                toggleSectionListener();
                highlightSectionListener();
                deleteSectionListener();
                assetMoveListener();
                movePlaceListener();
                assetEditListeners();
                addAfterDrops();
                if (courseLib.courseConfig.partialrender) {
                    setCourseSectionObervers();
                }
                $('body').addClass('charteredcollege-course-listening');
            };

            /**
             * Override core functions.
             */
            var overrideCore = function() {
                // Check M.course exists (doesn't exist in social format).
                if (M.course && M.course.resource_toolbox) {
                    /* eslint-disable camelcase */
                    M.course.resource_toolbox.handle_resource_dim = function(button, activity, action) {
                        return (action === 'hide') ? 0 : 1;
                    };
                    /* eslint-enable camelcase */
                }
            };

            /**
             * Make an Ajax request for caching the TOC so it's not so expensive to hide and show sections.
             */
            var cacheTOC = function() {
                if ($('.charteredcollege-section-editing.actions').length === 0) {
                    // Only cache the TOC if there are sections.
                    return;
                }

                var action = 'toc';

                var trigger = $('#region-main');

                if (!ajaxTracker.start('section_' + action, trigger)) {
                    // Request already in progress.
                    return;
                }

                // Make ajax call.
                var ajaxPromises = ajax.call([
                    {
                        methodname: 'theme_charteredcollege_course_sections',
                        args : {
                            courseshortname: courseLib.courseConfig.shortname,
                            action: action,
                            sectionnumber: 0,
                            value: 0,
                            loadmodules: 0,
                        }
                    }
                ], true, true);

                // Handle ajax promises.
                ajaxPromises[0]
                .fail(function(response) {
                    var errMessage, errAction;
                    errMessage = M.util.get_string('error:failedtotoc', 'theme_charteredcollege');
                    errAction = M.util.get_string('action:sectiontoc', 'theme_charteredcollege');
                    ajaxNotify.ifErrorShowBestMsg(response, errAction, errMessage).done(function() {
                        // Allow another request now this has finished.
                        ajaxTracker.complete('section_' + action);
                    });
                }).always(function() {
                    $(trigger).removeClass('ajaxing');
                }).done(function(response) {
                    // Caching modules for future use.
                    moduleCache = response.toc.modules;

                    // Caching progress for future use.
                    progressCache = [];
                    $.each(response.toc.chapters.chapters, function(index, value) {
                        progressCache.push(value.progress);
                    });

                    ajaxTracker.complete('section_' + action);
                });
            };

            /**
             * Initialise script.
             */
            var initialise = function() {
                // Add listeners.
                addListeners();

                // Cache TOC.
                cacheTOC();

                // Override core functions
                util.whenTrue(function() {
                    return M.course && M.course.init_section_toolbox;
                }, function() {
overrideCore();
}, true);

            };
            initialise();
        },

        /**
         * Exposed function that renders a specific course section and sets focus on an activity module.
         * @param section
         * @param mod
         */
        renderAndFocusSection: function(section, mod) {
            getSection(section, mod);
        },

        setTocObserver: function() {
            setTocObservers();
        }
    };

});
