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
 * @copyright   coactum GmbH 2018
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

        $MoodleDBObj = MoodleDB::getInstance();

        // ### variante 1: filter records in db via sql select and get only matching records (not working) #####
        // $select = "datadeletion IS NOT NULL AND TIMESTAMP(DATE_SUB(datadeletion, INTERVAL 1 MONTH)) <= ".$now; // not working
        // // SELECT * FROM `mdl_exammanagement` WHERE `datadeletion` IS NOT NULL AND TIMESTAMP(DATE_SUB(`datadeletion`, INTERVAL 1 MONTH)) > 1547146064 // in PHP-Myadmin getestet
        // $records = $MoodleDBObj->getFieldsetFromRecordsInDB('exammanagement', 'datadeletion', $select);
        // var_dump($records);
        // var_dump(date('d.m.y h:m', $records[0]));

        // for legacy exam organizations on test servers: extend deletion date so that all teachers get the warning mails
        //$select = "datadeletion IS NOT NULL AND datadeletion > ". 1569794400; // get all records where datadeletion date is set and that should already or soon be deleted (before 30.09.2019 00:00:00)
        $select = "datadeletion IS NOT NULL AND datadeletion > ". 1562848200; // for testing 11.07.19 15:30:00

        if($MoodleDBObj->checkIfRecordExistsSelect('exammanagement', $select)){
            //$MoodleDBObj->setFieldInDBSelect('exammanagement', 'datadeletion', 1569880800, $select); // set new datadeletion date to 01.10.2019 00:00:00
            $MoodleDBObj->setFieldInDBSelect('exammanagement', 'datadeletion', 1562848200, $select); // for testing 11.07.19 15:30:00
            
        }


        #### send warning mails for soon to be deleted module instances: ####

        // get all records where datadeletion date is set and that are not to be deleted yet
        $now = time();
        $select = "datadeletion IS NOT NULL AND datadeletion > ".$now; 
        
        if($MoodleDBObj->checkIfRecordExistsSelect('exammanagement', $select)){
            $records = $MoodleDBObj->getRecordsSelectFromDB('exammanagement', $select);

            var_dump('instances where correction is completed and that should not be deleted yet');
            var_dump($records);

            foreach($records as $id => $record){
    
                // check warning mail state
                var_dump('warning mail timestamps (one, two, three');
                var_dump($warningperiodOne);
                var_dump($warningperiodTwo);
                var_dump($warningperiodThree);
                var_dump($deletionwarningmailidsArray);


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
                if($warningperiodOne <= $now && $warningmailscount = 0){
                    $warningstep = 1;  // no warning mails yet, first to send
                } else if($warningperiodTwo <= $now && $warningmailscount = 1){
                    $warningstep = 2; // 1 warning mail yet, second to send
                } else if($warningperiodThree <= $now && $warningmailscount = 2){
                    $warningstep = 3; // 2 warning mails yet, last to send
                } else {
                    break; // stop for this record
                }

                var_dump($warningstep);
    
                if($warningstep){
                    // get user to whom warning mail should be send (teachers of course)
                    $role = $MoodleDBObj->getRecordFromDB('role', array('shortname' => 'editingteacher'));
                    $courseid = $record->course;
                    $coursecontext = context_course::instance($courseid);
                    $teachers = get_role_users($role->id, $coursecontext);
    
                    mtrace('Preparing to send '.$warningstep.'warning mail to '.$teachers.'teachers ...'); // debug
    
                    // set mail properties and contents
                    $cmid = get_coursemodule_from_instance('exammanagement', $record->id, $record->course, false, MUST_EXIST)->id;
    
                    $ExammanagementInstanceObj = exammanagementInstance::getInstance($cmid, '');
    
                    switch ($warningstep){
    
                        case 1:
                            $warningmailsubject = get_string("warningmailsubjectone", "mod_exammanagement");
                        case 2:
                            $warningmailsubject = get_string("warningmailsubjecttwo", "mod_exammanagement");
                        case 3:
                            $warningmailsubject = get_string("warningmailsubjectthree", "mod_exammanagement");
    
                    }
    
                    $warningmailcontent = get_string("warningmailcontentpartone", "mod_exammanagement"). $ExammanagementInstanceObj->moduleinstance->name .get_string("warningmailcontentparttwo", "mod_exammanagement"). $ExammanagementInstanceObj->getCourse()->fullname .get_string("warningmailcontentpartthree", "mod_exammanagement"). $ExammanagementInstanceObj->getDataDeletionDate() .get_string("warningmailcontentpartfour", "mod_exammanagement");
    
                    $warningmailids = array();
    
                    // send mail & save send warningmailid
                    foreach($teachers as $user){
                        $warningmailid = $ExammanagementInstanceObj->sendSingleMessage($user, $warningmailsubject, $warningmailcontent);
                        
                        mtrace('Mail with id '.$warningmailid.' and subject'. $warningmailsubject .' and content' . $warningmailcontent .' send to user '.$user->id); // debug
                        
                        array_push($warningmailids, $warningmailid);
                    }
    
                    array_push($deletionwarningmailidsArray, $warningmailids);

                    var_dump('warningmailidsarray');
                    var_dump($deletionwarningmailidsArray);
    
                    // update module instance
    
                    mtrace('Almost done, updating module instance with warning mail id ...'); // debug
    
                    $ExammanagementInstanceObj->moduleinstance->deletionwarningmailids = json_encode($deletionwarningmailidsArray);
    
                    $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
    
                }
                
            }
    
            // delete expired  exam data and instances
            $select = "datadeletion IS NOT NULL AND datadeletion <= ".$now;

            if($MoodleDBObj->checkIfRecordExistsSelect('exammanagement', $select)){
               
                $records = $MoodleDBObj->getRecordsSelectFromDB('exammanagement', $select);

                var_dump('i should delete records');
                var_dump($records);

                foreach($records as $record){
                    // set deleted property of instance true (for display purposes)
                    $cmid = get_coursemodule_from_instance('exammanagement', $record->id, $record->course, false, MUST_EXIST)->id;
                    $ExammanagementInstanceObj = exammanagementInstance::getInstance($cmid, '');

                    $ExammanagementInstanceObj->moduleinstance->deleted = 1;
    
                    $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

                    // delete participants data
                    if($MoodleDBObj->checkIfRecordExists('exammanagement_participants', array('plugininstanceid' => $cmid))){
                        //$MoodleDBObj->DeleteRecordsFromDB('exammanagement_participants', array('plugininstanceid' => $cmid));
                        var_dump('i should delete all participants data for this instance');
                        var_dump($ExammanagementInstanceObj->moduleinstance);
                    }
                }
            }

            var_dump('task finished');

            \core\task\manager::clear_static_caches(); // restart cron after running the task because it made many DB updates and clear cron cache (https://docs.moodle.org/dev/Task_API#Caches)
        }
    }
}