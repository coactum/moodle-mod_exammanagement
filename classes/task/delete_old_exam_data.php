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
 * A cron_task class for deleting old exam data to be used by Tasks API.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\task;
use mod_exammanagement\general\MoodleDB;
use mod_exammanagement\general\exammanagementInstance;
use context_course;

require_once(__DIR__.'/../general/MoodleDB.php');
require_once(__DIR__.'/../general/exammanagementInstance.php');

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

        $MoodleDBObj = MoodleDB::getInstance();

        #### send warning mails for soon to be deleted module instances: ####

        // get all records where datadeletion date is set and that are not to be deleted yet
        $now = time();
        $select = "datadeletion IS NOT NULL AND datadeletion >= ".$now;

        if($MoodleDBObj->checkIfRecordExistsSelect('exammanagement', $select)){

            $records = $MoodleDBObj->getRecordsSelectFromDB('exammanagement', $select);

            foreach($records as $id => $record){

                // check warning mail state
                $warningperiodOne = strtotime("-1 month", $record->datadeletion);
                $warningperiodTwo = strtotime("-7 days", $record->datadeletion);
                $warningperiodThree = strtotime("-1 day", $record->datadeletion);

                $deletionwarningmailidsArray = json_decode($record->deletionwarningmailids);

                if(isset($deletionwarningmailidsArray)){
                    $warningmailscount = count($deletionwarningmailidsArray);
                } else {
                    $warningmailscount = 0;
                    $deletionwarningmailidsArray = array();
                }

                $warningstep = false;

                // check if some warningmails were already send and determine if warning period is due
                if($warningperiodOne <= $now && $warningmailscount == 0){
                    $warningstep = 1;  // no warning mails yet, first to send
                } else if($warningperiodTwo <= $now && $warningmailscount == 1){
                    $warningstep = 2; // 1 warning mail yet, second to send
                } else if($warningperiodThree <= $now && $warningmailscount == 2){
                    $warningstep = 3; // 2 warning mails yet, last to send
                }

                if($warningstep){
                    // get user to whom warning mail should be send (teachers of course)
                    $role = $MoodleDBObj->getRecordFromDB('role', array('shortname' => 'editingteacher'));
                    $courseid = $record->course;
                    $coursecontext = context_course::instance($courseid);
                    $teachers = get_role_users($role->id, $coursecontext);

                    // set mail properties and contents
                    $cmid = get_coursemodule_from_instance('exammanagement', $record->id, $record->course, false, MUST_EXIST)->id;

                    $ExammanagementInstanceObj = new exammanagementInstance($cmid, '', true);

                    switch ($warningstep){

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

                    $warningmailcontent = get_string("warningmailcontent", "mod_exammanagement" , ['systemname' => $ExammanagementInstanceObj->getMoodleSystemName(), 'examname' => $ExammanagementInstanceObj->moduleinstance->name, 'coursename' => $ExammanagementInstanceObj->getCourse()->fullname, 'datadeletiondate' => $ExammanagementInstanceObj->getDataDeletionDate()]);
                    $warningmailcontent .= '<br><br>'.get_string("warningmailcontentenglish", "mod_exammanagement" , ['systemname' => $ExammanagementInstanceObj->getMoodleSystemName(), 'examname' => $ExammanagementInstanceObj->moduleinstance->name, 'coursename' => $ExammanagementInstanceObj->getCourse()->fullname, 'datadeletiondate' => $ExammanagementInstanceObj->getDataDeletionDate()]);

                    $warningmailids = array();

                    // send mail & save send warningmailid
                    foreach($teachers as $user){
                        $warningmailid = $ExammanagementInstanceObj->sendSingleMessage($user->id, $warningmailsubject, $warningmailcontent);

                        array_push($warningmailids, $warningmailid);
                    }

                    array_push($deletionwarningmailidsArray, $warningmailids);

                    mtrace('Sending ' . count($warningmailids) .'warning mails for step' . $warningstep . ' to teachers of exammanagement id ' . $ExammanagementInstanceObj->moduleinstance->id);

                    // update module instance

                    if(!empty($deletionwarningmailidsArray)){
                        $ExammanagementInstanceObj->moduleinstance->deletionwarningmailids = json_encode($deletionwarningmailidsArray);
                        $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

                        mtrace('Updating module instance for exammanagement id'.$ExammanagementInstanceObj->moduleinstance->id.' with new deletion warning mail ids.');
                    }
                }

            }
        }

        // delete expired  exam data and instances
        $select = "datadeleted IS NULL AND datadeletion IS NOT NULL AND datadeletion <= ".$now;

        if($MoodleDBObj->checkIfRecordExistsSelect('exammanagement', $select)){

            $records = $MoodleDBObj->getRecordsSelectFromDB('exammanagement', $select);

            mtrace('Starting deletion of old exam data for '. count($records).' exammanagements ...');

            foreach($records as $record){
                $cmid = get_coursemodule_from_instance('exammanagement', $record->id, $record->course, false, MUST_EXIST)->id;

                // set deleted property of instance true (for display purposes)
                $ExammanagementInstanceObj = new exammanagementInstance($cmid, '', true);

                $ExammanagementInstanceObj->moduleinstance->datadeleted = 1;

                $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

                mtrace('Deleting old exam data for exammanagement id ' . $ExammanagementInstanceObj->moduleinstance->instance);

                // delete participants data
                if($count = $MoodleDBObj->checkIfRecordExists('exammanagement_participants', array('exammanagement' => $ExammanagementInstanceObj->moduleinstance->instance))){
                    mtrace('Deleting ' . $count .' participants for exammanagement id ' . $ExammanagementInstanceObj->moduleinstance->instance);
                    $MoodleDBObj->DeleteRecordsFromDB('exammanagement_participants', array('exammanagement' => $ExammanagementInstanceObj->moduleinstance->instance));
                }
            }
        }

        \core\task\manager::clear_static_caches(); // restart cron after running the task because it made many DB updates and clear cron cache (https://docs.moodle.org/dev/Task_API#Caches)
    }
}