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
 * Privacy implementation for the charteredcollege theme.
 *
 * @package    theme_charteredcollege
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2018 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_charteredcollege\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\metadata\provider as metadata_provider;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\plugin\provider as request_provider;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy implementation for the charteredcollege theme.
 *
 * @package    theme_charteredcollege
 * @copyright  Copyright (c) 2018 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements metadata_provider, request_provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $sql = "SELECT cx.id
                  FROM {context} cx
                  JOIN {theme_cct_course_favorites} tscf ON tscf.userid = cx.instanceid AND cx.contextlevel = :userctxlevel
                 WHERE tscf.userid = :userid
              GROUP BY cx.id";

        $params = [
            'userctxlevel' => CONTEXT_USER,
            'userid' => $userid,
        ];

        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        $context = \context_user::instance($userid);

        if (!in_array($context->id, $contextlist->get_contextids())) {
            return;
        }

        $sql = "SELECT tscf.id, c.id AS courseid, c.fullname, tscf.timefavorited
                  FROM {theme_cct_course_favorites} tscf
                  JOIN {course} c ON c.id = tscf.courseid
                 WHERE tscf.userid = :userid";

        $favorites = $DB->get_recordset_sql($sql, ['userid' => $userid]);
        $data = [];
        foreach ($favorites as $favorite) {
            $data[] = (object) [
                'course'        => format_string($favorite->fullname, true),
                'user'          => transform::user($userid),
                'timefavorited' => transform::datetime($favorite->timefavorited),
            ];

            $subcontext = ['theme_charteredcollege-course-favorites'];
        }

        writer::with_context($context)->export_data($subcontext, (object) ['favorites' => $data]);
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (empty($context)) {
            return;
        }
        if ($context->contextlevel != CONTEXT_USER) {
            return;
        }

        $DB->delete_records('theme_cct_course_favorites', ['userid' => $context->instanceid]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }
        $user = $contextlist->get_user();
        $context = \context_user::instance($user->id);

        $contextids = $contextlist->get_contextids();
        if (!in_array($context->id, $contextids)) {
            return;
        }

        $DB->delete_records('theme_cct_course_favorites', ['userid' => $user->id]);
    }

    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $fields = [
            'userid' => 'privacy:metadata:theme_cct_course_favorites:userid',
            'courseid' => 'privacy:metadata:theme_cct_course_favorites:courseid',
            'timefavorited' => 'privacy:metadata:theme_cct_course_favorites:timefavorited',
        ];
        $summary = 'privacy:metadata:theme_cct_course_favorites';
        $collection->add_database_table('theme_cct_course_favorites', $fields, $summary);

        return $collection;
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_course) {
            return;
        }

        $params = [
            'courseid' => $context->instanceid
        ];

        $sql = "SELECT DISTINCT(tscf.userid) as userid
                  FROM {theme_cct_course_favorites} tscf
                 WHERE tscf.courseid = :courseid";

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $context = $userlist->get_context();

        if (!$context instanceof \context_course) {
            return;
        }

        list($userinsql, $userinparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
        $sql = "userid $userinsql AND courseid = :courseid";
        $params = array_merge($userinparams, ['courseid' => $context->instanceid]);

        $DB->delete_records_select('theme_cct_course_favorites', $sql, $params);
    }
}