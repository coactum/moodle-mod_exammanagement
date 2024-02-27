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
 * A class for deleting old exam data to be used by Tasks API.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\task;
use context_course;
use core_user;
use mod_exammanagement\local\helper;

/**
 * A class for deleting old exam data to be used by Tasks API.
 *
 * @package   mod_exammanagement
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_old_exam_data extends \core\task\scheduled_task {
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('delete_old_exam_data', 'mod_exammanagement');
    }

    /**
     * Execute the task.
     */
    public function execute() {

        mtrace('Starting scheduled task ' . get_string('delete_old_exam_data', 'mod_exammanagement'));

        global $DB;

        // Send warning mails for soon to be deleted module instances.

        // Get all records where datadeletion date is set and that are not to be deleted yet.
        $now = time();
        $select = "datadeletion IS NOT NULL AND datadeletion >= " . $now;

        if ($DB->record_exists_select('exammanagement', $select)) {

            if ($rs = $DB->get_recordset_select("exammanagement", $select)) {

                if ($rs->valid()) {

                    $moodlesystemname = helper::getmoodlesystemname();

                    foreach ($rs as $id => $record) {
                        // Check warning mail state.
                        $warningone = strtotime("-1 month", $record->datadeletion);
                        $warningtwo = strtotime("-7 days", $record->datadeletion);
                        $warningthree = strtotime("-1 day", $record->datadeletion);

                        if (isset($record->deletionwarningmailids)) {
                            $deletionwarningmailids = json_decode($record->deletionwarningmailids ?? '');
                            $warningmailscount = count($deletionwarningmailids);
                        } else {
                            $deletionwarningmailids = [];
                            $warningmailscount = 0;
                        }

                        $warningstep = false;

                        // Check if some warningmails are already send and determine if warning period is due.
                        if ($warningone <= $now && $warningmailscount == 0) {
                            $warningstep = 1;  // No warning mails yet, first to send.
                        } else if ($warningtwo <= $now && $warningmailscount == 1) {
                            $warningstep = 2; // One warning mail yet, second to send.
                        } else if ($warningthree <= $now && $warningmailscount == 2) {
                            $warningstep = 3; // Two warning mails yet, last to send.
                        }

                        if ($warningstep) {
                            // Get user to whom warning mail should be send (teachers of course).
                            $role = $DB->get_record('role', ['shortname' => 'editingteacher']);
                            $courseid = $record->course;
                            $coursecontext = context_course::instance($courseid);
                            $teachers = get_role_users($role->id, $coursecontext);

                            // Set mail properties and contents.
                            list($course, $cm) = get_course_and_cm_from_instance($record->id, 'exammanagement');

                            switch ($warningstep) {

                                case 1:
                                    $warningmailsubject = get_string("warningmailsubjectone", "mod_exammanagement");
                                    break;
                                case 2:
                                    $warningmailsubject = get_string("warningmailsubjecttwo", "mod_exammanagement");
                                    break;
                                case 3:
                                    $warningmailsubject = get_string("warningmailsubjectthree", "mod_exammanagement");
                                    break;
                            }

                            $warningmailcontent = get_string("warningmailcontent", "mod_exammanagement" ,
                                ['systemname' => $moodlesystemname,
                                'examname' => $record->name,
                                'coursename' => $course->fullname,
                                'datadeletiondate' => helper::getdatadeletiondate($record)]);

                            $warningmailcontent .= '<br><br>'.get_string("warningmailcontentenglish", "mod_exammanagement" ,
                                ['systemname' => $moodlesystemname,
                                'examname' => $record->name,
                                'coursename' => $course->fullname,
                                'datadeletiondate' => helper::getdatadeletiondate($record)]);

                            $warningmailids = [];

                            // Send mail and save send warning mail id.
                            foreach ($teachers as $user) {
                                $warningmailid = helper::sendsinglemessage($record, $cm->id, $course, $moodlesystemname,
                                    core_user::get_noreply_user(), $user->id, $warningmailsubject, $warningmailcontent,
                                    'deletionwarningmessage', true);

                                array_push($warningmailids, $warningmailid);
                            }

                            array_push($deletionwarningmailids, $warningmailids);

                            mtrace('Sending ' . count($warningmailids) .' warning mails for step ' . $warningstep .
                                ' to teachers of exammanagement id ' . $record->id);

                            // Update module instance.

                            if (!empty($deletionwarningmailids)) {
                                $record->deletionwarningmailids = json_encode($deletionwarningmailids);
                                $DB->update_record("exammanagement", $record);

                                mtrace('Updating module instance for exammanagement id ' . $record->id .
                                    ' with new deletion warning mail ids.');
                            }
                        }
                    }

                    $rs->close();
                }
            }
        }

        // Delete expired exam data from instances.
        $select = "datadeleted IS NULL AND datadeletion IS NOT NULL AND datadeletion <= " . $now;

        if ($DB->record_exists_select('exammanagement', $select)) {

            $count = $DB->count_records_select('exammanagement', $select);

            mtrace('Starting deletion of old exam data for '. $count . ' exammanagements ...');

            if ($rs = $DB->get_recordset_select("exammanagement", $select)) {

                if ($rs->valid()) {

                    foreach ($rs as $id => $record) {

                        $cmid = get_coursemodule_from_instance('exammanagement', $record->id,
                            $record->course, false, MUST_EXIST)->id;

                        // Set deleted property of instance true (for display purposes).
                        $record->datadeleted = 1;

                        $DB->update_record("exammanagement", $record);

                        mtrace('Deleting old exam data for exammanagement id ' . $record->id);

                        $select = 'exammanagement = ' . $record->id;

                        // Delete participants data.
                        if ($DB->record_exists_select('exammanagement_participants', $select)) {
                            $count = $DB->count_records_select('exammanagement_participants', $select);

                            mtrace('Deleting ' . $count .' participants for exammanagement id ' .
                                $record->id);

                            $DB->delete_records('exammanagement_participants',
                                ['exammanagement' => $record->id]);
                        }
                    }

                    $rs->close();

                }
            }
        }

        // Restart cron after running the task because it made many DB updates and clear cron cache
        // (https://docs.moodle.org/dev/Task_API#Caches).
        \core\task\manager::clear_static_caches();
    }
}
