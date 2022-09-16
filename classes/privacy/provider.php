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
use \core_privacy\local\request\writer;
use \core_privacy\local\request\helper;
use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;

defined('MOODLE_INTERNAL') || die();

/**
 * Implementation of the privacy subsystem plugin provider for the exammanagement activity module.
 *
 * @copyright  coactum GmbH 2019
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
        $items->add_database_table('exammanagement', ['-'=>'privacy:metadata:exammanagement:no_data'
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
            'bonussteps' => 'privacy:metadata:exammanagement_participants:bonussteps',
            'bonuspoints' => 'privacy:metadata:exammanagement_participants:bonuspoints',
        ], 'privacy:metadata:exammanagement_participants');

        // The table 'exammanagement_temp_part' stores data of all potential exam participants that are temporary saved. This potential participants can not directly be mapped to a moodle user in moodle, so no export is possible.
        $items->add_database_table('exammanagement_temp_part', [
            'exammanagement' => 'privacy:metadata:exammanagement_participants:exammanagement',
            'courseid' => 'privacy:metadata:exammanagement_participants:courseid',
            'categoryid' => 'privacy:metadata:exammanagement_participants:categoryid',
            'identifier' => 'privacy:metadata:exammanagement_temp_part:identifier',
            'line' => 'privacy:metadata:exammanagement_temp_part:line',
            'headerid' => 'privacy:metadata:exammanagement_temp_part:headerid',
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

        // The exammanagement uses the messages subsystem that saves personal data.
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
            'modulename'       => 'exammanagement',
            'contextlevel'  => CONTEXT_MODULE,
            'userid'        => $userid,
        ];

        // Where user is participant.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {exammanagement} e ON e.id = cm.instance
                  JOIN {exammanagement_participants} p ON p.exammanagement = e.id
                  WHERE p.moodleuserid = :userid
        ";

        $contextlist->add_from_sql($sql, $params);

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
            'instanceid'    => $context->id,
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
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist)) {
            return;
        }

        $user = $contextlist->get_user();
        $userid = $user->id;

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        // exammanagement and participants exam data
        $sql = "SELECT
                    cm.id AS cmid,
                    e.id AS exammanagement,
                    e.name,
                    e.timecreated,
                    e.timemodified,
                    p.moodleuserid AS moodleuserid,
                    p.login,
                    p.firstname,
                    p.lastname,
                    p.email,
                    p.headerid,
                    p.roomid,
                    p.roomname,
                    p.place,
                    p.exampoints,
                    p.examstate,
                    p.timeresultsentered,
                    p.bonussteps,
                    p.bonuspoints
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

        $exammanagements = $DB->get_recordset_sql($sql, $params);

        if($exammanagements->valid()){
            foreach ($exammanagements as $exammanagement) {
                if ($exammanagement) {
                    $context = \context_module::instance($exammanagement->cmid);

                    if($exammanagement->timemodified == 0){
                        $exammanagement->timemodified = null;
                    } else {
                        $exammanagement->timemodified = \core_privacy\local\request\transform::datetime($exammanagement->timemodified);
                    }

                    $exammanagementdata = [
                        'id'       => $exammanagement->exammanagement,
                        'timecreated'   => \core_privacy\local\request\transform::datetime($exammanagement->timecreated),
                        'timemodified' => $exammanagement->timemodified,
                    ];

                    if($exammanagement->timeresultsentered !== null){
                        $exammanagement->timeresultsentered = \core_privacy\local\request\transform::datetime($exammanagement->timeresultsentered);
                    }

                    if($exammanagement->exampoints !== null){
                        $exammanagement->exampoints = json_encode($exammanagement->exampoints);
                    }

                    if($exammanagement->examstate !== null){
                        $exammanagement->examstate = json_encode($exammanagement->examstate);
                    }

                    $exammanagementdata['participant data:'] = [
                        'userid' => $exammanagement->moodleuserid,
                        'login' => $exammanagement->login,
                        'firstname' => $exammanagement->firstname,
                        'lastname' => $exammanagement->lastname,
                        'email' => $exammanagement->email,
                        'headerid' => $exammanagement->headerid,
                        'roomid' => $exammanagement->roomid,
                        'roomname' => $exammanagement->roomname,
                        'place' => $exammanagement->place,
                        'exampoints' => $exammanagement->exampoints,
                        'examstate' => $exammanagement->examstate,
                        'timeresultsentered' => $exammanagement->timeresultsentered,
                        'bonussteps' => $exammanagement->bonussteps,
                        'bonuspoints' => $exammanagement->bonuspoints,
                    ];

                    self::export_exammanagement_data_for_user($exammanagementdata, $context, [], $user);
                }
            }
        }

        $exammanagements->close();
    }

    /**
     * Export the supplied personal data for a single exammanagement activity, along with all generic data for the activity.
     *
     * @param array $exammanagementdata The personal data to export for the exammanagement activity.
     * @param \context_module $context The context of the exammanagement activity.
     * @param array $subcontext The location within the current context that this data belongs.
     * @param \stdClass $user the user record
     */
    protected static function export_exammanagement_data_for_user(array $exammanagementdata, \context_module $context, array $subcontext, \stdClass $user) {
        // Fetch the generic module data for the exammanagement activity.
        $contextdata = helper::get_context_data($context, $user);
        // Merge with exammanagement data and write it.
        $contextdata = (object)array_merge((array)$contextdata, $exammanagementdata);
        writer::with_context($context)->export_data($subcontext, $contextdata);
        // Write generic module intro files.
        helper::export_context_files($context, $user);
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
        if (!$cm = get_coursemodule_from_id('exammanagement', $context->instanceid)) {
            return;
        }

        // Delete all records.
        if($DB->record_exists('exammanagement_participants', ['exammanagement' => $cm->instance])){
            $DB->delete_records('exammanagement_participants', ['exammanagement' => $cm->instance]);
        }

        if($DB->record_exists('exammanagement_temp_part', ['exammanagement' => $cm->instance])){
            $DB->delete_records('exammanagement_temp_part', ['exammanagement' => $cm->instance]);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {

        global $DB;

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            // Get the course module.
            $cm = $DB->get_record('course_modules', ['id' => $context->instanceid]);

            if($DB->record_exists('exammanagement_participants', ['exammanagement' => $cm->instance, 'moodleuserid' => $userid])){

                $DB->delete_records('exammanagement_participants', [
                    'exammanagement' => $cm->instance,
                    'moodleuserid' => $userid,
                ]);
            }
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

        list($userinsql, $userinparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
        $params = array_merge(['exammanagementid' => $cm->instance], $userinparams);

        if($DB->record_exists_select('exammanagement_participants', "exammanagement = :exammanagementid AND moodleuserid {$userinsql}", $params)){
            $DB->delete_records_select('exammanagement_participants', "exammanagement = :exammanagementid AND moodleuserid {$userinsql}", $params);
        }
    }
}
