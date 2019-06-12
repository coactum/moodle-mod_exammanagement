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
 * A cron_task class for checking if participants without moodle account now have an account to be used by Tasks API.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\task;
use mod_exammanagement\general\MoodleDB;

require_once(__DIR__.'/../general/MoodleDB.php');

class check_participants_without_moodle_account extends \core\task\scheduled_task { 
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('check_participants_without_moodle_account', 'mod_exammanagement');
    }
 
    /**
     * Execute the task.
     */
    public function execute() {

        $MoodleDBObj = MoodleDB::getInstance();

        // get all participants without moodle account
        if($MoodleDBObj->checkIfRecordExists("exammanagement_participants", array('moodleuserid' => NULL))){
            var_dump('none moodle participants exist');
            
            $NoneMoodleParticipants = $MoodleDBObj->getRecordsFromDB("exammanagement_participants", array('moodleuserid' => NULL));
            var_dump($NoneMoodleParticipants);

            foreach($NoneMoodleParticipants as $participant){
                
                var_dump($participant);
                var_dump('is checked');

                if($MoodleDBObj->checkIfRecordExists("user", array('username' => $participant->imtlogin))){                 // check if none moodle participants have now moodle account
                    
                    // if this is the case set moodle id instead of username and email
                    $user = $MoodleDBObj->getRecordFromDB('user', array('username'=>$participant->imtlogin));
                    
                    $participant->moodleuserid = $user->id;
                    $participant->imtlogin = Null;
                    $participant->firstname = Null;
                    $participant->lastname = Null;
                    $participant->email = Null;

                    var_dump($participant);
                    var_dump('is updated');

                    $MoodleDBObj->UpdateRecordInDB("exammanagement_participants", $participant);
                }


            }
            
            \core\task\manager::clear_static_caches(); // restart cron after running the task because it made many DB updates and clear cron cache (https://docs.moodle.org/dev/Task_API#Caches)

        }
    }
}