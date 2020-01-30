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
 * Privacy subsystem implementation for mod_exammanagement.
 *
 * @package    mod_exammanagement
 * @copyright  coactum GmbH 2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\privacy;

use \core_privacy\local\request\userlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\approved_userlist;
use \core_privacy\local\request\deletion_criteria;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\helper as request_helper;
use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;

defined('MOODLE_INTERNAL') || die();

/**
 * Based on the implementation of the privacy subsystem plugin provider for the forum activity module.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This plugin has data.
    \core_privacy\local\metadata\provider,

    // This plugin currently implements the original plugin\provider interface.
    \core_privacy\local\request\plugin\provider,

    // This plugin is capable of determining which users have data within it.
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection     $items The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $items) : collection {

        // The table 'exammanagement' does not store any specific user data. It only stores general information about the exam like exam time, rooms, gradingscale and so on.
        $items->add_database_table('exammanagement', [
        ], 'privacy:metadata:exammanagement');
        
        // The table 'exammanagement_participants' stores the personal data of all participants added to any exam.
        $items->add_database_table('exammanagement_participants', [
            'exammanagement' => 'privacy:metadata:exammanagement_participants:exammanagement',
            'courseid' => 'privacy:metadata:exammanagement_participants:courseid',
            'categoryid' => 'privacy:metadata:exammanagement_participants:categoryid',
            'moodleuserid' => 'privacy:metadata:exammanagement_participants:moodleuserid',
            'login' => 'privacy:metadata:exammanagement_participants:login',
            'firstname' => 'privacy:metadata:exammanagement_participants:firstname',
            'lastname' => 'privacy:metadata:exammanagement_participants:lastname',
            'email' => 'privacy:metadata:exammanagement_participants:email',
            'headerid' => 'privacy:metadata:exammanagement_participants:headerid',
            'roomid' => 'privacy:metadata:exammanagement_participants:roomid',
            'roomname' => 'privacy:metadata:exammanagement_participants:roomname',
            'place' => 'privacy:metadata:exammanagement_participants:place',
            'exampoints' => 'privacy:metadata:exammanagement_participants:exampoints',
            'examstate' => 'privacy:metadata:exammanagement_participants:examstate',
            'timeresultsentered' => 'privacy:metadata:exammanagement_participants:timeresultsentered',
            'bonus' => 'privacy:metadata:exammanagement_participants:bonus',
        ], 'privacy:metadata:exammanagement_participants');

        // The table 'exammanagement_temp_part' stores data of all potential exam participants that are temporary saved. This potential participants can not always be mapped to a moodle user and will be deleted once a day via a sheduled task, so no export is possible and no further deletion is needed.
        $items->add_database_table('exammanagement_temp_part', [
            'exammanagement' => 'privacy:metadata:exammanagement_participants:exammanagement',
            'courseid' => 'privacy:metadata:exammanagement_participants:courseid',
            'categoryid' => 'privacy:metadata:exammanagement_participants:categoryid',
            'identifier' => 'privacy:metadata:exammanagement_temp_part:identifier',
            'line' => 'privacy:metadata:exammanagement_temp_part:line',
        ], 'privacy:metadata:exammanagement_temp_part');
        
        // The table 'exammanagement_rooms' stores all available exam rooms. If a user has created a custom exam room it is stored here.
        $items->add_database_table('exammanagement_rooms', [
            'roomid' => 'privacy:metadata:exammanagement_rooms:roomid',
            'name' => 'privacy:metadata:exammanagement_rooms:name',
            'description' => 'privacy:metadata:exammanagement_rooms:description',
            'seatingplan' => 'privacy:metadata:exammanagement_rooms:seatingplan',
            'places' => 'privacy:metadata:exammanagement_rooms:places',
            'type' => 'privacy:metadata:exammanagement_rooms:type',
            'moodleuserid' => 'privacy:metadata:exammanagement_rooms:moodleuserid',
            'misc' => 'privacy:metadata:exammanagement_rooms:misc',
        ], 'privacy:metadata:exammanagement_rooms');

        // The exammanagement uses the messages subsystem that saves personal data
        $items->add_subsystem_link('core_message', [], 'privacy:metadata:core_message');

        // There are no user preferences in the exammanagement.

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * In this case of all exammanagements where a user is exam participant.
     *
     * @param   int         $userid     The user to search.
     * @return  contextlist $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : \core_privacy\local\request\contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();

        $params = [
            'modname'       => 'exammanagement',
            'contextlevel'  => CONTEXT_MODULE,
            'userid'        => $userid,
        ];

        // Where user is participant.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {exammanagement} e ON e.id = cm.instance
                  JOIN {exammanagement_participants} p ON p.exammanagement = e.id
                  WHERE p.moodleuserid = :userid
        ";

        $contextlist->add_from_sql($sql, $params);

        // Where user is teacher??.

        $event = \mod_exammanagement\event\log_variable::create(['other' => 'get_contexts_for_userid: ' . 'userid' . $userid  . 'contextlist' .json_encode($contextlist)]);
        $event->trigger();

        return $contextlist;
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!is_a($context, \context_module::class)) {
            return;
        }

        $params = [
            'instanceid'    => $context->instanceid,
            'modulename'    => 'exammanagement',
        ];

        // Participants
        $sql = "SELECT p.moodleuserid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {exammanagement} e ON e.id = cm.instance
                  JOIN {exammanagement_participants} p ON p.exammanagement = e.id
                 WHERE cm.id = :instanceid";
        $userlist->add_from_sql('userid', $sql, $params);

        $event = \mod_exammanagement\event\log_variable::create(['other' => 'get_users_in_context: ' . json_encode($userlist)]);
        $event->trigger();

        // Teachers??

    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $event = \mod_exammanagement\event\log_variable::create(['other' => 'export_user_data: ' . json_encode($contextlist)]);
        $event->trigger();

        if (empty($contextlist)) {
            return;
        }

        $user = $contextlist->get_user();
        $userid = $user->id;

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        // debug via event?
        
        // Digested forums.
        $sql = "SELECT
                    c.id AS contextid,
                    p.moodleuserid AS moodlesuerid
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid
                  JOIN {exammanagement} e ON e.id = cm.instance
                  JOIN {exammanagement_participants} p ON p.exammanagement = e.id
                 WHERE (
                    p.moodleuserid = :userid AND
                    c.id {$contextsql}
                )
        ";
        $params = $contextparams;
        $params['userid'] = $userid;
        $exammanagements = $DB->get_records_sql_menu($sql, $params);


        $event = \mod_exammanagement\event\log_variable::create(['other' => 'hallo']);
        $event->trigger();

        foreach ($exammanagements as $exammanagement) {

            $concept = format_string($exammanagement->name);
            $path = array_merge([get_string('modulename', 'mod_exammanagement'), $concept . " ({$exammanagement->id})"]);

            // If we've moved to a new glossary, then write the last glossary data and reinit the glossary data array.
            if (!is_null($lastid)) {
                if ($lastid != $exammanagement->id) {
                    if (!empty($exammanagementdata)) {
                        $context = \context_module::instance($lastid);
                        self::export_exammanagement_data_for_user($exammanagementdata, $context, [], $user);
                        //self::export_participants_data_for_user($participantsdata, $context, [], $user);
                        $exammanagementdata = [];
                    }
                }
            }
            $lastid = $exammanagement->id;
            $context = \context_module::instance($lastid);

            $exammanagementdata['exammanagement'][] = [
                'id'       => $exammanagement->id,
                'name'    => $exammanagement->name,
                'timecreated'   => \core_privacy\local\request\transform::datetime($exammanagement->timecreated),
                'timemodified'  => \core_privacy\local\request\transform::datetime($exammanagement->timemodified)
            ];
        }

        $exammanagements->close();

        // The data for the last activity won't have been written yet, so make sure to write it now!
        if (!empty($exammanagementdata)) {
            $context = \context_module::instance($lastid);
            self::export_exammanagement_data_for_user($exammanagementdata, $context, [], $user);
        }
    }

    /**
     * Export all data in the post.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   \context    $context The instance of the forum context.
     * @param   array       $postarea The subcontext of the parent.
     * @param   \stdClass   $post The post structure and all of its children
     */
    protected static function export_exammanagement_data_for_user(array $glossarydata, \context_module $context,array $subcontext, \stdClass $user) {
        // Fetch the generic module data for the glossary.
        $contextdata = helper::get_context_data($context, $user);
        // Merge with glossary data and write it.
        $contextdata = (object)array_merge((array)$contextdata, $glossarydata);
        writer::with_context($context)->export_data($subcontext, $contextdata);
        // Write generic module intro files.
        helper::export_context_files($context, $user);
    }

    /**
     * Export all data in the post.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   \context    $context The instance of the forum context.
     * @param   array       $postarea The subcontext of the parent.
     * @param   \stdClass   $post The post structure and all of its children
     */
    protected static function export_participants_data_for_user(int $userid, \context $context, $postarea, $post) {
        
    }



    /**
     * Delete all data for all users in the specified context.
     *
     * @param   context                 $context   The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        // Check that this is a context_module.
        if (!$context instanceof \context_module) {
            return;
        }

        // Get the course module.
        if (!$cm = get_coursemodule_from_id('forum', $context->instanceid)) {
            return;
        }

        $forumid = $cm->instance;

        $DB->delete_records('forum_track_prefs', ['forumid' => $forumid]);
        $DB->delete_records('forum_subscriptions', ['forum' => $forumid]);
        $DB->delete_records('forum_read', ['forumid' => $forumid]);
        $DB->delete_records('forum_digests', ['forum' => $forumid]);

        // Delete all discussion items.
        $DB->delete_records_select(
            'forum_queue',
            "discussionid IN (SELECT id FROM {forum_discussions} WHERE forum = :forum)",
            [
                'forum' => $forumid,
            ]
        );

        $DB->delete_records_select(
            'forum_posts',
            "discussion IN (SELECT id FROM {forum_discussions} WHERE forum = :forum)",
            [
                'forum' => $forumid,
            ]
        );

        $DB->delete_records('forum_discussion_subs', ['forum' => $forumid]);
        $DB->delete_records('forum_discussions', ['forum' => $forumid]);

        // Delete all files from the posts.
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'mod_forum', 'post');
        $fs->delete_area_files($context->id, 'mod_forum', 'attachment');

        // Delete all ratings in the context.
        \core_rating\privacy\provider::delete_ratings($context, 'mod_forum', 'post');

        // Delete all Tags.
        \core_tag\privacy\provider::delete_item_tags($context, 'mod_forum', 'forum_posts');
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $user = $contextlist->get_user();
        $userid = $user->id;
        foreach ($contextlist as $context) {
            // Get the course module.
            $cm = $DB->get_record('course_modules', ['id' => $context->instanceid]);
            $forum = $DB->get_record('forum', ['id' => $cm->instance]);

            $DB->delete_records('forum_track_prefs', [
                'forumid' => $forum->id,
                'userid' => $userid,
            ]);
            $DB->delete_records('forum_subscriptions', [
                'forum' => $forum->id,
                'userid' => $userid,
            ]);
            $DB->delete_records('forum_read', [
                'forumid' => $forum->id,
                'userid' => $userid,
            ]);

            $DB->delete_records('forum_digests', [
                'forum' => $forum->id,
                'userid' => $userid,
            ]);

            // Delete all discussion items.
            $DB->delete_records_select(
                'forum_queue',
                "userid = :userid AND discussionid IN (SELECT id FROM {forum_discussions} WHERE forum = :forum)",
                [
                    'userid' => $userid,
                    'forum' => $forum->id,
                ]
            );

            $DB->delete_records('forum_discussion_subs', [
                'forum' => $forum->id,
                'userid' => $userid,
            ]);

            // Do not delete discussion or forum posts.
            // Instead update them to reflect that the content has been deleted.
            $postsql = "userid = :userid AND discussion IN (SELECT id FROM {forum_discussions} WHERE forum = :forum)";
            $postidsql = "SELECT fp.id FROM {forum_posts} fp WHERE {$postsql}";
            $postparams = [
                'forum' => $forum->id,
                'userid' => $userid,
            ];

            // Update the subject.
            $DB->set_field_select('forum_posts', 'subject', '', $postsql, $postparams);

            // Update the message and its format.
            $DB->set_field_select('forum_posts', 'message', '', $postsql, $postparams);
            $DB->set_field_select('forum_posts', 'messageformat', FORMAT_PLAIN, $postsql, $postparams);

            // Mark the post as deleted.
            $DB->set_field_select('forum_posts', 'deleted', 1, $postsql, $postparams);

            // Note: Do _not_ delete ratings of other users. Only delete ratings on the users own posts.
            // Ratings are aggregate fields and deleting the rating of this post will have an effect on the rating
            // of any post.
            \core_rating\privacy\provider::delete_ratings_select($context, 'mod_forum', 'post',
                    "IN ($postidsql)", $postparams);

            // Delete all Tags.
            \core_tag\privacy\provider::delete_item_tags_select($context, 'mod_forum', 'forum_posts',
                    "IN ($postidsql)", $postparams);

            // Delete all files from the posts.
            $fs = get_file_storage();
            $fs->delete_area_files_select($context->id, 'mod_forum', 'post', "IN ($postidsql)", $postparams);
            $fs->delete_area_files_select($context->id, 'mod_forum', 'attachment', "IN ($postidsql)", $postparams);
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        $cm = $DB->get_record('course_modules', ['id' => $context->instanceid]);
        $forum = $DB->get_record('forum', ['id' => $cm->instance]);

        list($userinsql, $userinparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
        $params = array_merge(['forumid' => $forum->id], $userinparams);

        $DB->delete_records_select('forum_track_prefs', "forumid = :forumid AND userid {$userinsql}", $params);
        $DB->delete_records_select('forum_subscriptions', "forum = :forumid AND userid {$userinsql}", $params);
        $DB->delete_records_select('forum_read', "forumid = :forumid AND userid {$userinsql}", $params);
        $DB->delete_records_select(
            'forum_queue',
            "userid {$userinsql} AND discussionid IN (SELECT id FROM {forum_discussions} WHERE forum = :forumid)",
            $params
        );
        $DB->delete_records_select('forum_discussion_subs', "forum = :forumid AND userid {$userinsql}", $params);

        // Do not delete discussion or forum posts.
        // Instead update them to reflect that the content has been deleted.
        $postsql = "userid {$userinsql} AND discussion IN (SELECT id FROM {forum_discussions} WHERE forum = :forumid)";
        $postidsql = "SELECT fp.id FROM {forum_posts} fp WHERE {$postsql}";

        // Update the subject.
        $DB->set_field_select('forum_posts', 'subject', '', $postsql, $params);

        // Update the subject and its format.
        $DB->set_field_select('forum_posts', 'message', '', $postsql, $params);
        $DB->set_field_select('forum_posts', 'messageformat', FORMAT_PLAIN, $postsql, $params);

        // Mark the post as deleted.
        $DB->set_field_select('forum_posts', 'deleted', 1, $postsql, $params);

        // Note: Do _not_ delete ratings of other users. Only delete ratings on the users own posts.
        // Ratings are aggregate fields and deleting the rating of this post will have an effect on the rating
        // of any post.
        \core_rating\privacy\provider::delete_ratings_select($context, 'mod_forum', 'post', "IN ($postidsql)", $params);

        // Delete all Tags.
        \core_tag\privacy\provider::delete_item_tags_select($context, 'mod_forum', 'forum_posts', "IN ($postidsql)", $params);

        // Delete all files from the posts.
        $fs = get_file_storage();
        $fs->delete_area_files_select($context->id, 'mod_forum', 'post', "IN ($postidsql)", $params);
        $fs->delete_area_files_select($context->id, 'mod_forum', 'attachment', "IN ($postidsql)", $params);
    }
}
