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
 * Course card renderable
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_charteredcollege\renderables;

use theme_charteredcollege\services\course;
use theme_charteredcollege\local;

defined('MOODLE_INTERNAL') || die();

class course_card implements \renderable {

    /**
     * @var \stdClass $course
     */
    private $course;

    /**
     * @var course service
     */
    private $service;

    /**
     * @var course_card $model
     * (should be set to json encoded version of $this);
     */
    public $model;

    /**
     * @var int $courseid
     */
    public $courseid;

    /**
     * @var string $fullname
     */
    public $fullname;

    /**
     * @var string $shortname
     */
    public $shortname;

    /**
     * @var bool
     */
    public $favorited;

    /**
     * @var array
     */
    public $visibleavatars;

    /**
     * @var array
     */
    public $hiddenavatars;

    /**
     * @var int
     */
    public $hiddenavatarcount;

    /**
     * @var bool
     */
    public $showextralink = false;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $imagecss;

    /**
     * @var string
     */
    public $lazyloadimageurl;

    /**
     * @var bool
     */
    public $archived = false;

    /**
     * @var bool
     */
    public $published = true;

    /**
     * @var string
     */
    public $toggletitle = '';

    /**
     * @var int
     */
    public $category = null;

    /**
     * @var string
     */
    public $feedbackurl;

    /**
     * @var int
     */
    private $contextid;

    /**
     * @param \stdClass $course
     * @param course | null $service
     */
    public function __construct($course, course $service = null) {
        $this->course = $course;
        $this->courseid = $this->course->id;
        $this->contextid = \context_course::instance($this->courseid)->id;
        $this->service = $service ? : course::service();
        $this->apply_properties();
        $this->model = $this;
    }

    /**
     * Set props.
     */
    private function apply_properties() {
        $this->category = $this->course->category;
        $this->url = new \moodle_url('/course/view.php', ['id' => $this->courseid]) . '';
        $this->feedbackurl = new \moodle_url('/grade/report/user/index.php', ['id' => $this->courseid]) . '';
        $this->shortname = $this->course->shortname;
        $this->fullname = format_string($this->course->fullname);
        $this->published = (bool)$this->course->visible;
        $this->favorited = $this->service->favorited($this->courseid);
        $togglestrkey = !$this->favorited ? 'favorite' : 'favorited';
        $this->toggletitle = get_string($togglestrkey, 'theme_charteredcollege', $this->fullname);
        $this->apply_contact_avatars();
        $this->apply_image_css();
    }

    /**
     * Set image css for course card (cover image, etc).
     */
    private function apply_image_css() {
        static $count = 0;
        $count++;
        $bgcolor = local::get_course_color($this->courseid);
        $this->imagecss = "background-image: $bgcolor;";
        // Only immediately load images for the first 12 cards.
        /** @var \cache_application $bgcache */
        $bgcache = \cache::make('theme_charteredcollege', 'course_card_bg_image');
        $bgimage = $bgcache->get($this->contextid);
        if ($bgimage === false) {
            $bgurl = local::course_card_image_url($this->courseid);
            if ($bgurl instanceof \moodle_url) {
                $bgimage = $bgurl->out();
            } else {
                $bgimage = '';
            }
            $bgcache->set($this->contextid, $bgimage);
        }
        $isajax = defined('AJAX_SCRIPT') && AJAX_SCRIPT;
        if (!empty($bgimage)) {
            if (!$isajax && $count <= 12) {
                $this->imagecss = "background-image: url($bgimage);";
            } else {
                $this->lazyloadimageurl = $bgimage;
            }
        }
    }

    /**
     * Set course contact avatars;
     */
    private function apply_contact_avatars() {
        global $DB, $OUTPUT;
        /** @var \cache_application $avatarcache */
        $avatarcache = \cache::make('theme_charteredcollege', 'course_card_teacher_avatar');
        $avatars = $avatarcache->get($this->contextid);
        if ($avatars !== false) {
            $this->disperse_avatars($avatars);
            return;
        }
        $clist = new \core_course_list_element($this->course);
        $teachers = $clist->get_course_contacts();
        $avatars = [];
        $blankavatars = [];

        /** @var \cache_application $indexcache */
        $indexcache = \cache::make('theme_charteredcollege', 'course_card_teacher_avatar_index');
        $userctxidx = $indexcache->get('idx');
        if (!$userctxidx) {
            $userctxidx = [];
        }

        if (!empty($teachers)) {
            foreach ($teachers as $teacher) {
                $teacherids[] = $teacher['user']->id;
            }
            $teacherusers = $DB->get_records_list('user', 'id', $teacherids);

            foreach ($teachers as $teacher) {
                if (!isset($teacherusers[$teacher['user']->id])) {
                    continue;
                }

                // Updating avatar cache binary index.
                $userid = $teacher['user']->id;
                if (empty($userctxidx[$userid])) {
                    $userctxidx[$userid] = [];
                }
                $userctxidx[$userid][$this->contextid] = true;

                $teacheruser = $teacherusers [$userid];
                $userpicture = new \user_picture($teacheruser);
                $userpicture->link = false;
                $userpicture->size = 100;
                $teacherpicture = $OUTPUT->render($userpicture);

                if (stripos($teacherpicture, 'defaultuserpic') === false) {
                    $avatars[] = $teacherpicture;
                } else {
                    $blankavatars[] = $teacherpicture;
                }
            }
        }

        $avatars = array_merge($avatars, $blankavatars);

        if (!empty($avatars)) {
            // Cached value of avatar array for the course.
            $avatarcache->set($this->contextid, $avatars);
        } else {
            $avatarcache->delete($this->contextid);
        }
        if (!empty($userctxidx)) {
            // Context ID + User ID index for handling unenrolments and deletions.
            $indexcache->set('idx', $userctxidx);
        } else {
            $indexcache->delete('idx');
        }

        $this->disperse_avatars($avatars);
    }

    /**
     * Sets avatars in specific arrays to be visualized accordingly.
     * @param [] $avatars
     */
    private function disperse_avatars($avatars) {
        // Let's put the interesting avatars first!
        if (count($avatars) > 5) {
            // Show 4 avatars and link to show more.
            $this->visibleavatars = array_slice($avatars, 0, 4);
            $this->hiddenavatars = array_slice($avatars, 4);
            $this->showextralink = true;
        } else {
            $this->visibleavatars = $avatars;
            $this->hiddenavatars = [];
        }
        $this->hiddenavatarcount = count($this->hiddenavatars);
    }

    /**
     * This magic method is here purely so that doing strval($coursecard->model) yields a json encoded version of the
     * object that can be used in a template.
     * @return string
     */
    public function __toString() {
        unset($this->model);
        $retval = json_encode($this);
        $this->model = $this;
        return $retval;
    }
}
