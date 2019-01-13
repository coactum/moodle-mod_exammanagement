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

require_once(__DIR__.'/../general/MoodleDB.php');

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

        // send warning mails for soon to be deleted module instances

        $deletionDate = strtotime("+1 month");

        $now = time();
        $case = NULL;
        var_dump(time());


        ### variante 1: filter records in db via sql select and get only matching records (not working) #####
        $select = "datadeletion IS NOT NULL AND TIMESTAMP(DATE_SUB(datadeletion, INTERVAL 1 MONTH)) <= ".$now; // not working
        // SELECT * FROM `mdl_exammanagement` WHERE `datadeletion` IS NOT NULL AND TIMESTAMP(DATE_SUB(`datadeletion`, INTERVAL 1 MONTH)) > 1547146064 // in PHP-Myadmin getestet
        $records = $MoodleDBObj->getFieldsetFromRecordsInDB('exammanagement', 'datadeletion', $select);
        var_dump($records);
        var_dump(date('d.m.y h:m', $records[0]));

        #### variante 2: get all records and filter in php
        $select = "datadeletion IS NOT NULL AND datadeletion > ".$now; // not working

        $records = $MoodleDBObj->getRecordsSelectFromDB('exammanagement', $select);
        var_dump($records);

        foreach($records as $id => $record){

            // check if warning period is due
            $warningperiodOne = strtotime("-1 month", $record->datadeletion);
            $warningperiodTwo = strtotime("-7 days", $record->datadeletion);
            $warningperiodThree = strtotime("-1 day", $record->datadeletion);

            var_dump($warningperiodOne);
            var_dump($warningperiodTwo);
            var_dump($warningperiodThree);

            if($warningperiodOne <= $now){
                $case = 1;
                var_dump('should send first warning mail 1 month');
            } else if($warningperiodTwo <= $now){
                $case = 2;
                var_dump('should send second warning mail 7 days');
            } else if($warningperiodThree <= $now){
                $case = 3;
                var_dump('should send first warning mail 1 day');
            }

            var_dump($case);

            // get user to whom warning mail should be send
            
            //$role = $MoodleDBObj->getRecordFromDB('role', array('shortname' => 'editingteacher'));
            //$cmid = ... ;
            //$modulecontext = get_context_instance(CONTEXT_COURSE, $cmid);
            //$teachers = get_role_users($role->id, $modulecontext);

            // send mail
            // siehe send groupmessages
        }

        // delete expired  exam data and instances
        $select = "datadeletion IS NOT NULL AND datadeletion <= ".$now;
        //$MoodleDBObj->DeleteRecordsFromDBSelect($table, $select);
        
        \core\task\manager::clear_static_caches(); // restart cron after running the task because it made many DB updates and clear cron cache (https://docs.moodle.org/dev/Task_API#Caches)
    }
}