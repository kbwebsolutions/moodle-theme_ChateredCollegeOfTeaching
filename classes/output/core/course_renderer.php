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
 * charteredcollege course renderer.
 * Overrides core course renderer.
 *
 * @package   theme_charteredcollege
 * @copyright Copyright (c) 2015 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_charteredcollege\output\core;

defined('MOODLE_INTERNAL') || die();

use cm_info;
use context_course;
use context_module;
use html_writer;
use moodle_url;
use coursecat;
use stdClass;
use theme_charteredcollege\activity;
use theme_charteredcollege\activity_meta;

require_once($CFG->dirroot . "/mod/book/locallib.php");
require_once($CFG->libdir . "/gradelib.php");
require_once($CFG->dirroot . '/course/renderer.php');

class course_renderer extends \core_course_renderer {
    /**
     * override course render for course module list items
     * add additional classes to list item (see $modclass)
     *
     * @author: SL / GT
     * @param stdClass $course
     * @param \completion_info $completioninfo
     * @param cm_info $mod
     * @param int|null $sectionreturn
     * @param array $displayoptions
     * @return String
     */
    public function course_section_cm_list_item($course,
    &$completioninfo,
    cm_info $mod,
    $sectionreturn,
    $displayoptions = array()
    ) {
        $output = '';
        if ($modulehtml = $this->course_section_cm($course, $completioninfo, $mod, $sectionreturn, $displayoptions)) {
            list($charteredcollegemodtype, $extension) = $this->get_mod_type($mod);

            if ($mod->modname === 'resource') {
                // Default for resources/attatchments e.g. pdf, doc, etc.
                $modclasses = array('charteredcollege-resource', 'charteredcollege-mime-'.$extension, 'charteredcollege-resource-long');
                if (in_array($extension, $this->charteredcollege_multimedia())) {
                    $modclasses[] = 'js-charteredcollege-media';
                }
                // For images we overwrite with the native class.
                if ($this->is_image_mod($mod)) {
                    $modclasses = array('charteredcollege-native-image', 'charteredcollege-image', 'charteredcollege-mime-'.$extension);
                }
            } else if ($mod->modname === 'folder' && !$mod->url) {
                // Folder mod set to display on page.
                $modclasses = array('charteredcollege-activity', 'charteredcollege_extended_resource');
            }  else if ($mod->modname === 'folder') {
                $modclasses = array('charteredcollege-extended-resource');
            }  else if ($mod->modname === 'url') {
                $modclasses = array('charteredcollege-extended-resource');
            }  else if (plugin_supports('mod', $mod->modname, FEATURE_MOD_ARCHETYPE) === MOD_ARCHETYPE_RESOURCE) {
                $modclasses = array('charteredcollege-resource');
                if ($mod->modname !== 'label') {
                    $modclasses = array('charteredcollege-resource', 'charteredcollege-resource-long');
                }
            } else if ($mod->modname === 'scorm') {
                $modclasses = array('charteredcollege-resource', 'charteredcollege-resource-long');
            } else if ($mod->modname !== 'label') {
                $modclasses = array('charteredcollege-activity');
            }

            // Special classes for native html elements.
            if (in_array($mod->modname, ['page', 'book'])) {
                $modclasses = array('charteredcollege-native', 'charteredcollege-mime-'.$mod->modname);
                $attr['aria-expanded'] = "false";
            } else if ($modurl = $mod->url) {
                // For charteredcollege cards, js uses this to make the whole card clickable.
                if ($mod->uservisible) {
                    $attr['data-href'] = $modurl;
                }
            }

            // Is this mod draft?
            // We don't need visibleold as a condition here since it can affect a
            // module merged from a course to another, and the draft class won't be applied.
            // The "Not published to students" message won't be displayed next to the course module so teachers do not
            // realize that the content is not available to students.

            if (!$mod->visible) {
                $modclasses [] = 'draft';
            }

            // Is this mod stealth?
            if ($mod->is_stealth()) {
                $modclasses [] = 'stealth';
            }

            $canviewhidden = has_capability('moodle/course:viewhiddenactivities', $mod->context);
            // If the module isn't available, or we are a teacher (can view hidden activities) then get availability
            // info.
            $availabilityinfo = '';
            if (!$mod->available || $canviewhidden) {
                $availabilityinfo = $this->course_section_cm_availability($mod, $displayoptions);
            }

            if ($availabilityinfo !== '' && !$mod->uservisible || $canviewhidden) {
                $modclasses [] = 'conditional';
            }
            if (!$mod->available && !$mod->uservisible) {
                $modclasses [] = 'unavailable';
            }
            // TODO - can we add completion data.
            if (has_any_capability(['moodle/course:update', 'moodle/course:manageactivities'], $mod->context)) {
                $modclasses [] = 'charteredcollege-can-edit';
            }
            if (has_capability('moodle/course:viewhiddenactivities', $mod->context)) {
                $modclasses [] = 'charteredcollege-can-view-hidden';
            }

            $modclasses [] = 'charteredcollege-asset'; // Added to stop conflicts in flexpage.
            $modclasses [] = 'activity'; // Moodle needs this for drag n drop.
            $modclasses [] = $mod->modname;
            $modclasses [] = "modtype_$mod->modname";
            $modclasses [] = $mod->extraclasses;

            $attr['data-type'] = $charteredcollegemodtype;
            $attr['class'] = implode(' ', $modclasses);
            $attr['id'] = 'module-' . $mod->id;
            $attr['data-modcontext'] = $mod->context->id;

            $output .= html_writer::tag('li', $modulehtml, $attr);
        }
        return $output;
    }


    /**
     * Renders HTML to show course module availability information
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_availability(cm_info $mod, $displayoptions = array()) {
        // If we have available info, always spit it out.
        if (!$mod->uservisible && !empty($mod->availableinfo)) {
            $availinfo = $mod->availableinfo;
        } else {
            $ci = new \core_availability\info_module($mod);
            $availinfo = $ci->get_full_information();
        }

        if ($availinfo) {
            $formattedinfo = \core_availability\info::format_info(
                $availinfo, $mod->get_course());
            return $formattedinfo;
        }

        return '';
    }

    /**
     * Renders HTML to display one course module in a course section
     *
     * This includes link, content, availability, completion info and additional information
     * that module type wants to display (i.e. number of unread forum posts)
     *
     * This function calls:
     * {@link core_course_renderer::course_section_cm_name()}
     * {@link cm_info::get_after_link()}
     * {@link core_course_renderer::course_section_cm_text()}
     * {@link core_course_renderer::course_section_cm_availability()}
     * {@link core_course_renderer::course_section_cm_completion()}
     * {@link course_get_cm_edit_actions()}
     * {@link core_course_renderer::course_section_cm_edit_actions()}
     *
     * @param \stdClass $course
     * @param \completion_info $completioninfo
     * @param \cm_info $mod
     * @param int|null $sectionreturn
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm($course, &$completioninfo, cm_info $mod, $sectionreturn, $displayoptions = array()) {

        global $COURSE, $PAGE, $OUTPUT;

        $output = '';
        // We return empty string (because course module will not be displayed at all)
        // when
        // 1) The activity is not visible to users
        // and also
        // 2) The 'availableinfo' is empty, i.e. the activity was
        // hidden in a way that leaves no info, such as using the
        // eye icon.
        if (!$mod->uservisible
            && (empty($mod->availableinfo))) {
            return $output;
        }
        if (!$mod->is_visible_on_course_page()) {
            return $output;
        }

        $output .= '<div class="asset-wrapper">';

        // Drop section notice.
        if (has_capability('moodle/course:update', $mod->context)) {
            $output .= '<a class="charteredcollege-move-note" href="#">'.get_string('movehere', 'theme_charteredcollege').'</a>';
        }
        // Start the div for the activity content.
        $output .= "<div class='activityinstance'>";
        // Display the link to the module (or do nothing if module has no url).
        $cmname = $this->course_section_cm_name($mod, $displayoptions);
        $assetlink = '';
        $assettype = '';

        if (!empty($cmname)) {
            // Activity/resource type.
            $charteredcollegemodtype = $this->get_mod_type($mod)[0];
            $pagecurrentstr = get_string('modulename', 'mod_page');
            $bookcurrentstr = get_string('modulename', 'mod_book');
            if (strcmp($charteredcollegemodtype, $pagecurrentstr) == 0 || strcmp($charteredcollegemodtype, $bookcurrentstr) == 0) {
                $charteredcollegemodtype = '';
            }
            $assettype = '<div class="charteredcollege-assettype">'.$charteredcollegemodtype.'</div>';
            // Asset link.
            $assetlink .= '<h3 class="charteredcollege-asset-link">'.$cmname.'</h3>';
        }
        // Meta data field.
        if (!empty($PAGE->theme->settings->modulemetafield)) {
            $metafield = $PAGE->theme->settings->modulemetafield;
            $metadata = $this->get_extra_mod_data($mod, $metafield);
        }


        // Asset content.
        $contentpart = $this->course_section_cm_text($mod, $displayoptions);

        // Asset metadata - groups, completion etc.
        // Due date, feedback available and all the nice charteredcollege things.
        $charteredcollegecompletionmeta = '';
        $charteredcollegecompletiondata = $this->module_meta_html($mod);
        if ($charteredcollegecompletiondata) {
            $charteredcollegecompletionmeta = '<div class="charteredcollege-completion-meta">'.$charteredcollegecompletiondata.'</div>';
        }

        // Completion tracking.
        $completiontracking = '<div class="charteredcollege-asset-completion-tracking">';
        $completiontracking .= $this->course_section_cm_completion($course, $completioninfo, $mod, $displayoptions);
        $completiontracking .= '</div>';

        // Add specific class if the completion tracking is disabled for an activity.
        $completion = $completioninfo->is_enabled($mod);
        if ($completion == COMPLETION_TRACKING_NONE) {
            $completiontracking = '<div class="disabled-charteredcollege-asset-completion-tracking">';
            $completiontracking .= $this->course_section_cm_completion($course, $completioninfo, $mod, $displayoptions);
            $completiontracking .= '</div>';
        }

        // Draft & Stealth tags.
        $stealthtag = '';
        $drafttag = '';
        if ($mod->is_stealth()) {
            // Stealth tag.
            $stealthtag = '<div class="charteredcollege-stealth-tag">'.get_string('hiddenoncoursepage', 'moodle').'</div>';
        } else {
            // Draft status - always output, shown via css of parent.
            $drafttag = '<div class="charteredcollege-draft-tag">'.get_string('draft', 'theme_charteredcollege').'</div>';
        }

        // Group.
        $groupmeta = '';
        // Resources cannot have groups/groupings.
        if ($mod->modname !== 'resource') {
            $canmanagegroups = has_capability('moodle/course:managegroups', context_course::instance($mod->course));
            if ($canmanagegroups && $mod->effectivegroupmode != NOGROUPS) {
                if ($mod->effectivegroupmode == VISIBLEGROUPS) {
                    $groupinfo = get_string('groupsvisible');
                } else if ($mod->effectivegroupmode == SEPARATEGROUPS) {
                    $groupinfo = get_string('groupsseparate');
                }
                $groupmeta .= '<div class="charteredcollege-group-tag">'.$groupinfo.'</div>';
            }

            // This will show a grouping (group of groups) name against a module if one has been assigned to the module instance.
            if ($canmanagegroups && !empty($mod->groupingid)) {
                // Grouping label.
                $groupings = groups_get_all_groupings($mod->course);
                $groupmeta .= '<div class="charteredcollege-grouping-tag">'.format_string($groupings[$mod->groupingid]->name).'</div>';
            }
        }

        $canviewhidden = has_capability('moodle/course:viewhiddenactivities', $mod->context);
        // If the module isn't available, or we are a teacher (can view hidden activities) then get availability
        // info. Restrictions will appear on click over a lock image inside the activity header.
        $coursetoolsicon = '';
        if (!$mod->available || $canviewhidden) {
            $availabilityinfo = $this->course_section_cm_availability($mod, $displayoptions);
            if ($availabilityinfo) {
                $restrictionsource = '<img title="" id="charteredcollege-restriction-icon" aria-hidden="true" class="svg-icon" src="';
                $restrictionsource .= $this->output->image_url('lock', 'theme').'"/>';
                $coursetoolsicon = html_writer::tag('a', $restrictionsource, [
                    'tabindex' => '0',
                    'class' => 'charteredcollege-conditional-tag',
                    'role' => 'button',
                    'data-toggle' => 'popover',
                    'data-trigger' => 'focus',
                    'data-placement' => 'right',
                    'id' => 'charteredcollege-restriction',
                    'data-html' => 'true',
                    'clickable' => 'true',
                    'data-content' => $availabilityinfo
                ]);
            }
        }

        // Add draft, contitional.
        $assetmeta = $stealthtag.$drafttag;

        // Build output.
        $postcontent = '<div class="charteredcollege-asset-meta" data-cmid="'.$mod->id.'">'.$assetmeta.$mod->afterlink.'</div>';
        $content = '<div class="charteredcollege-asset-content">'.$assetlink.$postcontent.$metadata.$contentpart.$charteredcollegecompletionmeta.$groupmeta.'</div>';
        $cardicons = '<div class="charteredcollege-header-card-icons">'.$completiontracking.$coursetoolsicon.'</div>';
        $output .= '<div class="charteredcollege-header-card">'.$assettype.$cardicons.'</div>'.$content;

        // Bail at this point if we aren't using a supported format. (Folder view is only partially supported).
        $supported = ['topics', 'weeks', 'site'];
        if (!in_array($COURSE->format, $supported)) {
            return parent::course_section_cm($course, $completioninfo, $mod, $sectionreturn, $displayoptions).$assetmeta;
        }

        // Build up edit actions.
        $actions = '';
        $actionsadvanced = array();
        $coursecontext = context_course::instance($mod->course);
        $modcontext = context_module::instance($mod->id);
        $baseurl = new moodle_url('/course/mod.php', array('sesskey' => sesskey()));

        $str = get_strings(array('delete', 'move', 'duplicate', 'hide', 'show', 'roles'), 'moodle');
        // TODO - add charteredcollege strings here.

        // Move, Edit, Delete.
        if (has_capability('moodle/course:manageactivities', $modcontext)) {
            $movealt = s(get_string('move', 'theme_charteredcollege', $mod->get_formatted_name()));
            $moveicon = '<img title="'.$movealt.'" aria-hidden="true" class="svg-icon" src="';
            $moveicon .= $this->output->image_url('move', 'theme').'"/>';
            $editalt = s(get_string('edit', 'theme_charteredcollege', $mod->get_formatted_name()));
            $editicon = '<img title="'.$editalt.'" alt="'.$editalt.'" class="svg-icon" src="';
            $editicon .= $this->output->image_url('edit', 'theme').'"/>';
            $actions .= '<input id="charteredcollege-move-mod-'.$mod->id.'" class="js-charteredcollege-asset-move sr-only" type="checkbox">';
            $actions .= '<label class="charteredcollege-asset-move" for="charteredcollege-move-mod-'.$mod->id.'">';
            $actions .= '<span class="sr-only">'.$movealt.'</span>'.$moveicon.'</label>';
            $actions .= '<a class="charteredcollege-edit-asset" href="'.new moodle_url($baseurl, array('update' => $mod->id)).'">';
            $actions .= $editicon.'</a>';
            $actionsadvanced[] = '<a href="'.new moodle_url($baseurl, array('delete' => $mod->id)).
                '" data-action="delete" class="js_charteredcollege_delete dropdown-item">'.$str->delete.'</a>';
        }

        // Hide/Show.
        // Not output for stealth activites.
        if (has_capability('moodle/course:activityvisibility', $modcontext) && !$mod->is_stealth()) {
            $actions .= '<input class="sr-only" type="checkbox">';
            $hideaction = '<a href="'.new moodle_url($baseurl, array('hide' => $mod->id));
            $hideaction .= '" data-action="hide" class="dropdown-item editing_hide js_charteredcollege_hide">'.$str->hide.'</a>';
            $actionsadvanced[] = $hideaction;
            $showaction = '<a href="'.new moodle_url($baseurl, array('show' => $mod->id));
            $showaction .= '" data-action="show" class="dropdown-item editing_show js_charteredcollege_show">'.$str->show.'</a>';
            $actionsadvanced[] = $showaction;
        }

        // Duplicate.
        $dupecaps = array('moodle/backup:backuptargetimport', 'moodle/restore:restoretargetimport');
        if (has_all_capabilities($dupecaps, $coursecontext) &&
            plugin_supports('mod', $mod->modname, FEATURE_BACKUP_MOODLE2) &&
            plugin_supports('mod', $mod->modname, 'duplicate', true)) {
            $actionsadvanced[] = "<a href='".new moodle_url($baseurl, array('duplicate' => $mod->id)).
                "' data-action='duplicate' class='dropdown-item js_charteredcollege_duplicate'>$str->duplicate</a>";
        }

        // Asign roles.
        if (has_capability('moodle/role:assign', $modcontext)) {
            $actionsadvanced[] = "<a class='dropdown-item' href='".
                new moodle_url('/admin/roles/assign.php', array('contextid' => $modcontext->id)).
                "'>$str->roles</a>";
        }

        // Give local plugins a chance to add icons.
        $localplugins = array();
        foreach (get_plugin_list_with_function('local', 'extend_module_editing_buttons') as $function) {
            $localplugins = array_merge($localplugins, $function($mod));
        }

        foreach (get_plugin_list_with_function('block', 'extend_module_editing_buttons') as $function) {
            $localplugins = array_merge($localplugins, $function($mod));
        }

        // TODO - pld string is far too long....
        $locallinks = '';
        foreach ($localplugins as $localplugin) {
            $url = $localplugin->url;
            $text = $localplugin->text;
            $class = 'dropdown-item ' . $localplugin->attributes['class'];
            $actionsadvanced[] = "<a href='$url' class='$class'>$text</a>";
        }

        $advancedactions = '';
        if (!empty($actionsadvanced)) {
            $moreicon = "<img title='".get_string('more', 'theme_charteredcollege')."' alt='".get_string('more', 'theme_charteredcollege').
                    "' class='svg-icon' src='".$this->output->image_url('more', 'theme')."'/>";
            $advancedactions = '<div class="dropdown charteredcollege-edit-more-dropdown">';
            $advancedactions .= '<span class="dropdown-toggle charteredcollege-edit-asset-more" ';
            $advancedactions .= 'data-toggle="dropdown" data-boundary="window" data-offset="-10,12"';
            $advancedactions .= 'aria-expanded="false" aria-haspopup="true">'.$moreicon.'</span>';
            $advancedactions .= '<div class="dropdown-menu" role="menu">';
            foreach ($actionsadvanced as $action) {
                $advancedactions .= "$action";
            }
            $advancedactions .= "</div></div>";
        }
        $output .= "</div>"; // Close .activityinstance.

        // Add actions menu.
        if ($actions) {
            $output .= "<div class='js-only charteredcollege-asset-actions' role='region' aria-label='actions'>";
            $output .= $actions.$advancedactions;
            $output .= "</div>";
        }
        $output .= "</div>"; // Close .asset-wrapper.
        return $output;
    }

    /**
     * Renders html to display the module content on the course page (i.e. text of the labels)
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_text(cm_info $mod, $displayoptions = array()) {
        $output = '';
        if (!$mod->uservisible && empty($mod->availableinfo)) {
            // Nothing to be displayed to the user.
            return $output;
        }

        // Get custom module content for charteredcollege, or get modules own content.
        $modmethod = 'mod_'.$mod->modname.'_html';
        if ($this->is_image_mod($mod)) {
            $content = $this->mod_image_html($mod);
        } else if (method_exists($this,  $modmethod )) {
            $content = call_user_func(array($this, $modmethod), $mod);
        } else {
            $content = $mod->get_formatted_content(array('overflowdiv' => false, 'noclean' => true));
        }

        $accesstext = '';
        $textclasses = '';
        if ($mod->uservisible) {
            $conditionalhidden = $this->is_cm_conditionally_hidden($mod);
            $accessiblebutdim = (!$mod->visible || $conditionalhidden) &&
            has_capability('moodle/course:viewhiddenactivities',
            context_course::instance($mod->course));
            if ($accessiblebutdim) {
                if ($conditionalhidden) {
                    $textclasses .= ' conditionalhidden';
                }
                // Show accessibility note only if user can access the module himself.
                $accesstext = get_accesshide(get_string('hiddenfromstudents').':'. $mod->modfullname);
            }
        }
        if ($mod->url) {
            if ($content) {
                // Add extra content to create fadeout to resource cards activities and SCORM package.
                if ((plugin_supports('mod', $mod->modname, FEATURE_MOD_ARCHETYPE) === MOD_ARCHETYPE_RESOURCE) ||
                    $mod->modname === 'scorm') {
                        $fadeoutcard = "<p class='charteredcollege-resource-card-fadeout'>&nbsp;</p>";
                        $content .= $fadeoutcard;
                }
                // If specified, display extra content after link.
                $output = html_writer::tag('div', $content, array('class' => trim('contentafterlink ' . $textclasses)));
            }
        } else {
            $charteredcollegemodtype = $this->get_mod_type($mod)[0];
            // Label title should not be displayed in the activity card header.
            $labelcurrentstr = get_string('modulename', 'mod_label');
            if (strcmp($charteredcollegemodtype, $labelcurrentstr) == 0) {
                $charteredcollegemodtype = '';
            }
            $assettype = '<div class="charteredcollege-assettype">'.$charteredcollegemodtype.'</div>';

            // No link, so display only content.
            $output = html_writer::tag('div', $assettype . $accesstext . $content,
                array('class' => 'contentwithoutlink ' . $textclasses));
        }
        return $output;
    }

    /*
    ***** charteredcollege SPECIFIC DISPLAY OF RESOURCES *******
    */

    /**
     * Get module type
     * Note, if module is a resource, get the actual file type
     *
     * @author Guy Thomas
     * @date 2014-06-16
     * @param cm_info $mod
     * @return stdClass | string
     */
    protected function get_mod_type(cm_info $mod) {
        if ($mod->modname === 'resource') {
            // Get file type from icon
            // (note, I also tried this using a combo of substr and strpos and preg_match was much faster!)
            $matches = array();
            preg_match ('#/(\w+)-#', $mod->icon, $matches);
            $filetype = $matches[1];
            $ext = $filetype;
            $extension = array(
                'powerpoint' => 'ppt',
                'document' => 'doc',
                'spreadsheet' => 'xls',
                'archive' => 'zip',
                'pdf' => 'pdf',
                'image' => get_string('image', 'theme_charteredcollege'),
            );
            if (in_array($filetype, array_keys($extension))) {
                $filetype = $extension[$filetype];
            }
            return [$filetype, $ext];
        } else {
            return [$mod->modfullname, null];
        }
    }

    /**
     * Is this an image module
     * @param cm_info $mod
     * @return bool
     */
    protected function is_image_mod(cm_info $mod) {
        if ($mod->modname == 'resource') {
            $matches = array();
            preg_match ('#/(\w+)-#', $mod->icon, $matches);
            $filetype = $matches[1];
            $extension = array('jpg', 'jpeg', 'png', 'gif', 'svg', 'image');
            if (in_array($filetype, $extension)) {
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * Submission call to action.
     *
     * @param cm_info $mod
     * @param activity_meta $meta
     * @return string
     * @throws \coding_exception
     */
    public static function submission_cta(cm_info $mod, activity_meta $meta) {
        global $CFG;

        if (empty($meta->submissionnotrequired)) {
            $url = $CFG->wwwroot.'/mod/'.$mod->modname.'/view.php?id='.$mod->id;

            if ($meta->submitted) {
                if (empty($meta->timesubmitted)) {
                    $submittedonstr = '';
                } else {
                    $submittedonstr = ' '.userdate($meta->timesubmitted, get_string('strftimedate', 'langconfig'));
                }
                $message = $meta->submittedstr.$submittedonstr;
            } else {
                $warningstr = $meta->draft ? $meta->draftstr : $meta->notsubmittedstr;
                $warningstr = $meta->reopened ? $meta->reopenedstr : $warningstr;
                $message = $warningstr;
            }
            return html_writer::link($url, $message);
        }
        return '';
    }
    /**
     * Get extra custom field data for modules and return content
     */
    protected function get_extra_mod_data($mod, $field) {
        global $DB;
        $module = $mod->id;
        $sql = "SELECT instanceid AS Module, data, name, shortname
                FROM {local_metadata} As md
                JOIN {local_metadata_field} AS category ON category.id = md.fieldid
                WHERE instanceid = :mod AND shortname = :field";

        $metafields = $DB->get_record_sql($sql, ['mod' => $module, 'field' => $field]);

        if (empty($metafields->data)) {
            return " ";
        } else {
            return '<div class = "charteredcollege_module_metadata">' . $metafields->name . ': ' . $metafields->data . '</div>';
        }


    }
    /**
     * Get the module meta data for a specific module.
     *
     * @param cm_info $mod
     * @return string
     */
    protected function module_meta_html(cm_info $mod) {

        global $COURSE, $OUTPUT;

        $content = '';

        if (is_guest(context_course::instance($COURSE->id))) {
            return '';
        }

        // Do we have an activity function for this module for returning meta data?
        // @todo - check module lib.php for a meta function (won't work for core mods but will for ours if we wish).
        $meta = activity::module_meta($mod);
        if (!$meta->is_set(true)) {
            // Can't get meta data for this module.
            return '';
        }

        if ($meta->isteacher) {
            // Teacher - useful teacher meta data.
            $engagementmeta = array();

            // Below, !== false means we get 0 out of x submissions.
            if (!$meta->submissionnotrequired && $meta->numsubmissions !== false) {
                $engagementmeta[] = get_string('xofy'.$meta->submitstrkey, 'theme_charteredcollege',
                    (object) array(
                        'completed' => $meta->numsubmissions,
                        'participants' => \theme_charteredcollege\local::course_participant_count($COURSE->id, $mod->modname)
                    )
                );
            }

            if ($meta->numrequiregrading) {
                $engagementmeta[] = get_string('xungraded', 'theme_charteredcollege', $meta->numrequiregrading);
            }
            if (!empty($engagementmeta)) {
                $engagementstr = implode(', ', $engagementmeta);

                $params = array(
                    'action' => 'grading',
                    'id' => $mod->id,
                    'tsort' => 'timesubmitted',
                    'filter' => 'require_grading'
                );
                $url = new moodle_url("/mod/{$mod->modname}/view.php", $params);

                $content .= html_writer::link($url, $engagementstr);
            }
            $suspended = \theme_charteredcollege\local::suspended_participant_count($COURSE->id, $mod->id);
            if ($suspended) {
                $content .= html_writer::tag('p', get_string("quizattemptswarn", "theme_charteredcollege"));
            }

        } else {
            // Feedback meta.
            if (!empty($meta->grade)) {
                // Note - the link that a module takes you to would be better off defined by a function in
                // theme/charteredcollege/activity - for now its just hard coded.
                $url = new \moodle_url('/grade/report/user/index.php', ['id' => $COURSE->id]);
                if (in_array($mod->modname, ['quiz', 'assign'])) {
                    $url = new \moodle_url('/mod/'.$mod->modname.'/view.php?id='.$mod->id);
                }
                $feedbackavailable = get_string('feedbackavailable', 'theme_charteredcollege');
                if ($mod->modname != 'lesson') {
                    $content .= html_writer::link($url, $feedbackavailable);
                }
            }

            // If submissions are not allowed, return the content.
            if (!empty($meta->timeopen) && $meta->timeopen > time()) {
                // TODO - spit out a 'submissions allowed from' tag.
                return $content;
            }
            // @codingStandardsIgnoreLine
            /* @var cm_info $mod */
            $content .= self::submission_cta($mod, $meta);
        }

        // Activity due date.
        if (!empty($meta->extension) || !empty($meta->timeclose)) {
            $dateformat = get_string('strftimedate', 'langconfig');
            if (!empty($meta->extension)) {
                $field = 'extension';
            } else if (!empty($meta->timeclose)) {
                $field = 'timeclose';
            }
            $labeltext = get_string('due', 'theme_charteredcollege', userdate($meta->$field, $dateformat));
            $pastdue = $meta->$field < time();
            $url = new \moodle_url("/mod/{$mod->modname}/view.php", ['id' => $mod->id]);
            $dateclass = $pastdue ? 'tag-danger' : 'tag-success';
            $content .= html_writer::link($url, $labeltext,
                    [
                        'class' => 'charteredcollege-due-date tag '.$dateclass,
                        'data-from-cache' => $meta->timesfromcache ? 1 : 0
                    ]);
        }

        return $content;
    }


    /**
     * Get resource module image html
     *
     * @param stdClass $mod
     * @return string
     */
    protected function mod_image_html($mod) {
        global $OUTPUT;
        if (!$mod->uservisible) {
                return "";
        }

        $fs = get_file_storage();
        $context = \context_module::instance($mod->id);
        // TODO: this is not very efficient!!
        $files = $fs->get_area_files($context->id, 'mod_resource', 'content', 0, 'sortorder DESC, id ASC', false);
        if (count($files) > 0) {
            foreach ($files as $file) {
                $imgsrc = \moodle_url::make_pluginfile_url(
                        $file->get_contextid(),
                        $file->get_component(),
                        $file->get_filearea(),
                        $file->get_itemid(),
                        $file->get_filepath(),
                        $file->get_filename()
                );
            }
        }

        $summary = '';
        $summary = $mod->get_formatted_content(array('overflowdiv' => false, 'noclean' => true));
        $modname = format_string($mod->name);
        $img = format_text('<img src="' .$imgsrc. '" alt="' .$modname. '"/>');
        $icon = '<img title="' .get_string('vieworiginalimage', 'theme_charteredcollege'). '"
                alt="' .get_string('vieworiginalimage', 'theme_charteredcollege'). '"
                src="' .$OUTPUT->image_url('arrow-expand', 'theme'). '">';
        $imglink = '<a class="charteredcollege-expand-link" href="' .$imgsrc. '" target="_blank">' .$icon. '</a>';

        $output = '<figure class="charteredcollege-resource-figure figure">'
                    .$img.$imglink.
                    '<figcaption class="charteredcollege-resource-figure-caption figure-caption">'
                        .$modname.$summary.
                    '</figcaption>
                </figure>';

        return $output;
    }

    /**
     * Get page module html
     * @param cm_info $mod
     * @return string
     */
    protected function mod_page_html(cm_info $mod) {
        if (!$mod->uservisible) {
            return "";
        }

        $page = \theme_charteredcollege\local::get_page_mod($mod);

        $imgarr = \theme_charteredcollege\local::extract_first_image($page->content);

        $thumbnail = '';
        if ($imgarr) {
            $img = html_writer::img($imgarr['src'], $imgarr['alt']);
            $thumbnail = "<div class=summary-figure>$img</div>";
        }

        $readmore = get_string('readmore', 'theme_charteredcollege');
        $close = get_string('closebuttontitle', 'moodle');

        $content = '';
        $contentloaded = 0;
        if (empty(get_config('theme_charteredcollege', 'lazyload_mod_page'))) {
            // Identify content elements which should force an AJAX lazy load.
            $elcontentblist = ['iframe', 'video', 'object', 'embed', 'model-viewer'];
            $content = $page->content;
            $lazyload = false;
            foreach ($elcontentblist as $el) {
                if (stripos($content, '<'.$el) !== false) {
                    $content = ''; // Don't include the content as it is likely to slow the page load down considerably.
                    $lazyload = true;
                }
            }
            $contentloaded = !$lazyload ? 1 : 0;
        }

        $pagenewwindow = get_config('theme_charteredcollege', 'design_mod_page');
        // Check for mod page design setting to open the content inline on the same page or in another window.
        if ($pagenewwindow) {
            $pslinkclass = 'btn btn-secondary pagemod-readmore';
        } else {
            $pslinkclass = 'btn btn-secondary';
        }
        $pmcontextattribute = 'data-pagemodcontext="'.$mod->context->id.'"';

        $o = "
        {$thumbnail}
        <div class='summary-text'>
            {$page->summary}
            <p><a class='$pslinkclass' title='{$mod->name}' href='{$mod->url}' $pmcontextattribute>{$readmore}</a></p>
        </div>

        <div class=pagemod-content tabindex='-1' data-content-loaded={$contentloaded}>
            {$content}
            <div class='d-block'><hr><a  class='charteredcollege-action-icon charteredcollege-icon-close' href='#'>
            <small>$close</small></a></div>
        </div>";

        return $o;
    }

    protected function mod_book_html($mod) {
        if (!$mod->uservisible) {
            return "";
        }
        global $DB;

        $cm = get_coursemodule_from_id('book', $mod->id, 0, false, MUST_EXIST);
        $book = $DB->get_record('book', array('id' => $cm->instance), '*', MUST_EXIST);
        $chapters = book_preload_chapters($book);

        if ($book->intro) {
            $context = context_module::instance($mod->id);
            $content = file_rewrite_pluginfile_urls($book->intro, 'pluginfile.php', $context->id, 'mod_book', 'intro', null);
            $formatoptions = new stdClass;
            $formatoptions->noclean = true;
            $formatoptions->overflowdiv = true;
            $formatoptions->context = $context;
            $content = format_text($content, $book->introformat, $formatoptions);
            $o = '<div class="summary-text row">';
            $o .= '<div class="col-sm-6">' .$content. '</div>';
            $o .= '<div class="col-sm-6">' .$this->book_get_toc($chapters, $book, $cm) . '</div>';
            $o .= '</div>';
            return $o;
        }
        return $this->book_get_toc($chapters, $book, $cm);
    }

    /**
     * Simplified book toc Get assignment module html (includes meta data);
     *
     * Based on the function of same name in mod/book/localib.php
     * @param $mod
     * @return string
     */
    public function book_get_toc($chapters, $book, $cm) {
        $context = context_module::instance($cm->id);

        switch ($book->numbering) {
            case BOOK_NUM_BULLETS :
                $numclass = 'list-bullets';
                break;
            case BOOK_NUM_INDENTED:
                $numclass = 'list-indented';
                break;
            case BOOK_NUM_NONE:
                $numclass = 'list-none';
                break;
            case BOOK_NUM_NUMBERS :
            default :
                $numclass = 'list-numbers';
        }

        $toc = "<h4>".get_string('chapters', 'theme_charteredcollege')."</h4>";
        $toc .= '<ol class="bookmod-chapters '.$numclass.'">';
        $closemeflag = false; // Control for indented lists.
        $chapterlist = '';
        foreach ($chapters as $ch) {
            $title = trim(format_string($ch->title, true, array('context' => $context)));
            if (!$ch->hidden) {
                if ($closemeflag && !$ch->parent) {
                    $chapterlist .= "</ul></li>";
                    $closemeflag = false;
                }
                $chapterlist .= "<li>";
                $chapterlist .= html_writer::link(new moodle_url('/mod/book/view.php',
                    array('id' => $cm->id, 'chapterid' => $ch->id)), $title, array());
                if ($ch->subchapters) {
                    $chapterlist .= "<ul>";
                    $closemeflag = true;
                } else {
                    $chapterlist .= "</li>";
                }
            }
        }
        $toc .= $chapterlist.'</ol>';
        return $toc;
    }

    /**
     * Every mime type we consider to be multimedia.
     * @return array
     */
    protected function charteredcollege_multimedia() {
        return ['mp3', 'wav', 'audio', 'mov', 'wmv', 'video', 'mpeg', 'avi', 'quicktime', 'flash'];
    }

    /**
     * Renders html to display a name with the link to the course module on a course page
     *
     * If module is unavailable for user but still needs to be displayed
     * in the list, just the name is returned without a link
     *
     * Note, that for course modules that never have separate pages (i.e. labels)
     * this function return an empty string
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_name(cm_info $mod, $displayoptions = array()) {

        $output = '';

        // Nothing to be displayed to the user.
        if (!$mod->uservisible && empty($mod->availableinfo)) {
            return $output;
        }

        // Is this for labels or something with no other page url to point to?
        $url = $mod->url;
        if (!$url) {
            return $output;
        }

        // Get asset name.
        $instancename = $mod->get_formatted_name();
        $groupinglabel = $mod->get_grouping_label();

        $target = '';

        $activityimg = "<img class='iconlarge activityicon' alt='' role='presentation'  src='".$mod->get_icon_url()."' />";

        // Multimedia mods we want to open in the same window.
        $charteredcollegemultimedia = $this->charteredcollege_multimedia();

        $resourcedisplay = get_config('theme_charteredcollege', 'resourcedisplay');
        $displaydescription = get_config('theme_charteredcollege', 'displaydescription');
        if ($mod->modname === 'resource') {
            $extension = $this->get_mod_type($mod)[1];
            if (in_array($extension, $charteredcollegemultimedia) ) {
                // For multimedia we need to handle the popup setting.
                // If popup add a redirect param to prevent the intermediate page.
                if ($mod->onclick) {
                    $url .= "&amp;redirect=1";
                }
            } else {
                if ($resourcedisplay == 'card' && $displaydescription) {
                    $url .= "&amp;forceview=1";
                } else {
                    $url .= "&amp;redirect=1";
                    $target = "target='_blank'";
                }
            }
        }
        if ($mod->modname === 'url') {
            if ($resourcedisplay == 'card' && $displaydescription) {
                // Set the url to forceview 1 to see intermediate page with description.
                $url .= "&amp;forceview=1";
            } else {
                $url .= "&amp;redirect=1";
                $target = "target='_blank'";
            }
        }

        if ($mod->uservisible) {
            $output .= "<a $target class='mod-link' href='$url'>$activityimg<p class='instancename'>$instancename</p></a>";
            $output .= $groupinglabel;
        } else {
            // We may be displaying this just in order to show information
            // about visibility, without the actual link ($mod->uservisible).
            $output .= "<div>$activityimg $instancename</div> $groupinglabel";
        }

        return $output;
    }

    /**
     * Wrapper around course_get_cm_edit_actions
     *
     * @param cm_info $mod The module
     * @param int $sr The section to link back to (used for creating the links)
     * @return array Of action_link or pix_icon objects
     */
    protected function course_get_cm_edit_actions(cm_info $mod, $sr = null) {
        $actions = course_get_cm_edit_actions($mod, -1, $sr);
        $actions = array_filter($actions, function($action) {
            return !($action instanceof \action_menu_filler);
        });
        $rename = core_course_inplace_editable($mod, $mod->indent, $sr);
        $edittitle = get_string('edittitle');
        $rename = str_replace('</a>', "$edittitle</a>", $rename);
        $actions['edit-rename'] = $rename;

        return $actions;
    }

    /**
     * Return move notice.
     * @return bool|string
     * @throws moodle_exception
     */
    public function charteredcollege_footer_alert() {
        global $OUTPUT;
        return $OUTPUT->render_from_template('theme_charteredcollege/footer_alert', null);
    }

    /**
     * Generates a notification if course format is not topics or weeks the user is editing and is a teacher/mananger.
     *
     * @return string
     * @throws \coding_exception
     */
    public function course_format_warning() {
        global $COURSE, $PAGE, $OUTPUT;

        $format = $COURSE->format;
        if (in_array($format, ['weeks', 'topics'])) {
            return '';
        }

        if (!$PAGE->user_is_editing()) {
            return '';
        }

        if (!has_capability('moodle/course:manageactivities', context_course::instance($COURSE->id))) {
            return '';
        }

        $url = new moodle_url('/course/edit.php', ['id' => $COURSE->id]);
        return $OUTPUT->notification(get_string('courseformatnotification', 'theme_charteredcollege', $url->out()));
    }

    /**
     * Renders html to display a course search form.
     *
     * @param string $value default value to populate the search field
     * @param string $format display format - 'plain' (default), 'short' or 'navbar'
     * @return string
     */
    public function course_search_form($value = '', $format = 'plain') {
        static $count = 0;
        $formid = 'coursesearch';
        if ((++$count) > 1) {
            $formid .= $count;
        }

        switch ($format) {
            case 'navbar' :
                $formid = 'coursesearchnavbar';
                $inputid = 'navsearchbox';
                $inputsize = 20;
                break;
            case 'short' :
                $inputid = 'shortsearchbox';
                $inputsize = 12;
                break;
            default :
                $inputid = 'coursesearchbox';
                $inputsize = 30;
        }

        $data = (object) [
            'searchurl' => (new moodle_url('/course/search.php'))->out(false),
            'id' => $formid,
            'inputid' => $inputid,
            'inputsize' => $inputsize,
            'value' => $value
        ];

        return $this->render_from_template('theme_charteredcollege/course_search_form', $data);
    }

    /**
     * Renders HTML to display particular course category - list of it's subcategories and courses
     *
     * Invoked from /course/index.php
     *
     * @param int|stdClass|coursecat $category
     */
    public function course_category($category) {
        global $CFG;
        $basecategory = \core_course_category::get(0);
        $coursecat = \core_course_category::get(is_object($category) ? $category->id : $category);
        $site = get_site();
        $output = '';
        $categoryselector = '';
        // NOTE - we output manage catagory button in the layout file in charteredcollege.

        if (!$coursecat->id) {
            if (\core_course_category::is_simple_site() == 1) {
                // There exists only one category in the system, do not display link to it.
                $coursecat = \core_course_category::get_default();
                $strfulllistofcourses = get_string('fulllistofcourses');
                $this->page->set_title("$site->shortname: $strfulllistofcourses");
            } else {
                $strcategories = get_string('categories');
                $this->page->set_title("$site->shortname: $strcategories");
            }
        } else {
            $title = $site->shortname;
            if ($basecategory->get_children_count() > 1) {
                $title .= ": ". $coursecat->get_formatted_name();
            }
            $this->page->set_title($title);

            // Print the category selector.
            if ($basecategory->get_children_count() > 1) {
                $select = new \single_select(new moodle_url('/course/index.php'), 'categoryid',
                        \core_course_category::make_categories_list(), $coursecat->id, null, 'switchcategory');
                $select->set_label(get_string('category').':');
                $categoryselector .= $this->render($select);
            }
        }
        $output .= '<div class="row">';
        $output .= '<div class="col-sm-4">';
        // Add course search form.
        $output .= $this->course_search_form();
        $output .= '</div>';
        // Add cat select box if available.
        $output .= '<div class="col-sm-8 text-right">';
        $output .= $categoryselector;
        $output .= '</div>';

        $chelper = new \coursecat_helper();
        // Prepare parameters for courses and categories lists in the tree.
        $atts = ['class' => 'category-browse category-browse-'.$coursecat->id];
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_AUTO)->set_attributes($atts);

        $coursedisplayoptions = array();
        $catdisplayoptions = array();
        $browse = optional_param('browse', null, PARAM_ALPHA);
        $perpage = optional_param('perpage', $CFG->coursesperpage, PARAM_INT);
        $page = optional_param('page', 0, PARAM_INT);
        $baseurl = new moodle_url('/course/index.php');
        if ($coursecat->id) {
            $baseurl->param('categoryid', $coursecat->id);
        }
        if ($perpage != $CFG->coursesperpage) {
            $baseurl->param('perpage', $perpage);
        }
        $coursedisplayoptions['limit'] = $perpage;
        $catdisplayoptions['limit'] = $perpage;
        if ($browse === 'courses' || !$coursecat->has_children()) {
            $coursedisplayoptions['offset'] = $page * $perpage;
            $coursedisplayoptions['paginationurl'] = new moodle_url($baseurl, array('browse' => 'courses'));
            $catdisplayoptions['nodisplay'] = true;
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'categories'));
            $catdisplayoptions['viewmoretext'] = new \lang_string('viewallsubcategories');
        } else if ($browse === 'categories' || !$coursecat->has_courses()) {
            $coursedisplayoptions['nodisplay'] = true;
            $catdisplayoptions['offset'] = $page * $perpage;
            $catdisplayoptions['paginationurl'] = new moodle_url($baseurl, array('browse' => 'categories'));
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'courses'));
            $coursedisplayoptions['viewmoretext'] = new \lang_string('viewallcourses');
        } else {
            // We have a category that has both subcategories and courses, display pagination separately.
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'courses', 'page' => 1));
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'categories', 'page' => 1));
        }
        $chelper->set_courses_display_options($coursedisplayoptions)->set_categories_display_options($catdisplayoptions);

        // Display course category tree.
        $output .= $this->coursecat_tree($chelper, $coursecat);

        // Add action buttons.
        $context = get_category_or_system_context($coursecat->id);
        if (has_capability('moodle/course:create', $context)) {
            // Print link to create a new course, for the 1st available category.
            if ($coursecat->id) {
                $url = new moodle_url('/course/edit.php', ['category' => $coursecat->id, 'returnto' => 'category']);
            } else {
                $url = new moodle_url('/course/edit.php', ['category' => $CFG->defaultrequestcategory, 'returnto' => 'topcat']);
            }
            $output .= '<div class="add-course-btn-container"><a class="btn btn-secondary" href="'.$url.'">'.
                get_string('addnewcourse', 'moodle').'</a></div>';
        }

        $output .= $this->container_start('buttons');
        ob_start();
        if (\core_course_category::is_simple_site() == 1) {
            print_course_request_buttons(\context_system::instance());
        } else {
            print_course_request_buttons($context);
        }
        $output .= ob_get_contents();
        ob_end_clean();
        $output .= $this->container_end();

        return $output;
    }

    /**
     * Prints a course footer with course contacts, course description and recent updates.
     *
     * @return string
     */

    public function course_footer() {
        global $DB, $COURSE, $CFG, $PAGE;

        // Check toggle switch.
        if (empty($PAGE->theme->settings->coursefootertoggle)) {
            return false;
        }

        $context = context_course::instance($COURSE->id);
        $courseteachers = '';
        $coursesummary = '';

        $clist = new \core_course_list_element($COURSE);
        $teachers = $clist->get_course_contacts();

        if (!empty($teachers)) {
            // Get all teacher user records in one go.
            $teacherids = array();
            foreach ($teachers as $teacher) {
                $teacherids[] = $teacher['user']->id;
            }
            $teacherusers = $DB->get_records_list('user', 'id', $teacherids);

            // Course contacts.
            $courseteachers .= '<h5>'.get_string('coursecontacts', 'theme_charteredcollege').'</h5>';
            foreach ($teachers as $teacher) {
                if (!isset($teacherusers[$teacher['user']->id])) {
                    continue;
                }
                $teacheruser = $teacherusers [$teacher['user']->id];
                $courseteachers .= $this->print_teacher_profile($teacheruser);
            }
        }
        // If user can edit add link to manage users.
        if (has_capability('moodle/course:enrolreview', $context)) {
            if (empty($courseteachers)) {
                $courseteachers = "<h5>".get_string('coursecontacts', 'theme_charteredcollege')."</h5>";
            }
            $courseteachers .= '<br><a id="enrolled-users" class="btn btn-outline-secondary btn-sm"
                href="'.$CFG->wwwroot.'/user/index.php?id='.$COURSE->id.'">'.get_string('enrolledusers', 'enrol').'</a>';
        }

        // Course cummary.
        if (!empty($COURSE->summary)) {
            $coursesummary = '<h5>'.get_string('aboutcourse', 'theme_charteredcollege').'</h5>';
            $formatoptions = new stdClass;
            $formatoptions->noclean = true;
            $formatoptions->overflowdiv = true;
            $formatoptions->context = $context;
            $coursesummarycontent = file_rewrite_pluginfile_urls($COURSE->summary,
                'pluginfile.php', $context->id, 'course', 'summary', null);
            $coursesummarycontent = format_text($coursesummarycontent, $COURSE->summaryformat, $formatoptions);
            $coursesummary .= '<div id="charteredcollege-course-footer-summary">'.$coursesummarycontent.'</div>';
        }

        // If able to edit add link to edit summary.
        if (has_capability('moodle/course:update', $context)) {
            if (empty($coursesummary)) {
                $coursesummary = '<h5>'.get_string('aboutcourse', 'theme_charteredcollege').'</h5>';
            }
            $coursesummary .= '<br><a id="edit-summary" class="btn btn-outline-secondary btn-sm"
            href="'.$CFG->wwwroot.'/course/edit.php?id='.$COURSE->id.'#id_descriptionhdr">'.get_string('editsummary').'</a>';
        }

        // Get recent activities on mods in the course.
        $courserecentactivities = $this->get_mod_recent_activity($context);
        $courserecentactivity = '';
        if ($courserecentactivities) {
            $courserecentactivity = '<h5>'.get_string('recentactivity').'</h5>';
            if (!empty($courserecentactivities)) {
                $courserecentactivity .= $courserecentactivities;
            }
        }
        // If user can edit add link to moodle recent activity stuff.
        if (has_capability('moodle/course:update', $context)) {
            if (empty($courserecentactivities)) {
                $courserecentactivity = '<h5>'.get_string('recentactivity').'</h5>';
                $courserecentactivity .= get_string('norecentactivity');
            }
            $courserecentactivity .= '<div class="col-xs-12 clearfix"><a href="'.$CFG->wwwroot.'/course/recent.php?id='
                .$COURSE->id.'">'.get_string('showmore', 'form').'</a></div>';
        }

        if (!empty($courserecentactivity)) {
            $columns[] = $courserecentactivity;
        }
        if (!empty($courseteachers)) {
            $columns[] = $courseteachers;
        }
        if (!empty($coursesummary)) {
            $columns[] = $coursesummary;
        }

        $output = '';
        if (empty($columns)) {
            return $output;
        } else {
            $output .= '<div class="row">';
            $output .= '<div class="col-lg-3 col-md-4"><div id="charteredcollege-course-footer-contacts">'.$courseteachers.'</div></div>';
            $output .= '<div class="col-lg-9 col-md-8"><div id="charteredcollege-course-footer-about">'.$coursesummary.'</div></div>';
            $output .= '<div class="col-sm-12"><div id="charteredcollege-course-footer-recent-activity">'.$courserecentactivity.'</div></div>';
            $output .= '</div>';
        }
        return $output;
    }

    /**
     * Print teacher profile
     * Prints a media object with the techers photo, name (links to profile) and desctiption.
     *
     * @param stdClass $user
     * @return string
     */
    public function print_teacher_profile($user) {
        global $CFG, $OUTPUT, $USER;

        $userpicture = new \user_picture($user);
        $userpicture->link = false;
        $userpicture->alttext = false;
        $userpicture->size = 100;
        $picture = $this->render($userpicture);

        $fullname = '<a href="' .$CFG->wwwroot. '/user/profile.php?id=' .$user->id. '">'.format_string(fullname($user)).'</a>';
        $data = (object) [
            'image' => $picture,
            'content' => $fullname
        ];
        if ($USER->id != $user->id) {
            $messageicon = '<img class="svg-icon" alt="" role="presentation" src="'
                .$OUTPUT->image_url('messages', 'theme').' ">';
            $message = '<br><small><a href="'.$CFG->wwwroot.
                '/message/index.php?id='.$user->id.'">message'.$messageicon.'</a></small>';
            $data->content .= $message;
        }

        return $this->render_from_template('theme_charteredcollege/media_object', $data);
    }

    /**
     * Print recent activites for a course
     *
     * @param stdClass $context
     * @return string
     */
    public function get_mod_recent_activity($context) {
        global $COURSE, $OUTPUT;
        $viewfullnames = has_capability('moodle/site:viewfullnames', $context);
        $recentactivity = array();
        $timestart = time() - (86400 * 7); // Only show last 7 days activity.
        if (optional_param('testing', false, PARAM_BOOL)) {
            $timestart = time() - (86400 * 3000); // 3000 days ago for testing.
        }
        $modinfo = get_fast_modinfo($COURSE);
        $usedmodules = $modinfo->get_used_module_names();
        // Don't show activity for folder mod.
        unset($usedmodules['folder']);
        if (empty($usedmodules)) {
            // No used modules so return null string.
            return '';
        }
        foreach ($usedmodules as $modname => $modfullname) {
            // Each module gets it's own logs and prints them.
            ob_start();
            $hascontent = component_callback('mod_'. $modname, 'print_recent_activity',
                    array($COURSE, $viewfullnames, $timestart), false);
            if ($hascontent) {
                $content = ob_get_contents();
                if (!empty($content)) {
                    $recentactivity[$modname] = $content;
                }
            }
            ob_end_clean();
        }

        $output = '';
        if (!empty($recentactivity)) {
            foreach ($recentactivity as $modname => $moduleactivity) {
                // Get mod icon, empty alt as title already there.
                $img = html_writer::tag('img', '', array(
                    'src' => $OUTPUT->image_url('icon', $modname),
                    'alt' => '',
                ));

                // Create media object for module activity.
                $data = (object) [
                    'image' => $img,
                    'content' => $moduleactivity,
                    'class' => $modname
                ];
                $output .= $this->render_from_template('theme_charteredcollege/media_object', $data);
            }
        }
        return $output;
    }
}
