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
 * add custom room for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 namespace mod_exammanagement\general;

 use mod_exammanagement\forms\addCustomRoomForm;
 use stdclass;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$roomid  = optional_param('roomid', 0, PARAM_TEXT);

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$MoodleObj = Moodle::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

    if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && $SESSION->loggedInExamOrganizationId == $id)){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

        global $USER;

        $MoodleObj->setPage('addCustomRoomForm');
        $MoodleObj->outputPageHeader();

        //Instantiate form
        $mform = new addCustomRoomForm(null, array('id'=>$id, 'e'=>$e));

        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            //Handle form cancel operation, if cancel button is present on form
            redirect ($ExammanagementInstanceObj->getExammanagementUrl('chooseRooms', $ExammanagementInstanceObj->getCm()->id), get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

        } else if ($fromform = $mform->get_data()) {
            //In this case you process validated data. $mform->get_data() returns data posted in form.

            $roomname = $fromform->roomname;
            $placesCount = $fromform->placescount;
            $description = $fromform->description;

            if($MoodleDBObj->checkIfRecordExists('exammanagement_rooms', array('roomid' => $roomname.'_'.$USER->id.'c', 'moodleuserid' => $USER->id))){

                $roomObj = $MoodleDBObj->getRecordFromDB('exammanagement_rooms', array('roomid' => $roomname.'_'.$USER->id.'c', 'moodleuserid' => $USER->id));
                
                if($description){
                    $roomObj->description = $description;
                } else {
                    $roomObj->description = 'Keine Beschreibung vorhanden, '.$placesCount.' Plätze';
                }
                
                $placesArr = array();

                for ($i = 0; $i < $placesCount; $i++) {
                    array_push($placesArr, strval($i+1));
                }

                $roomObj->places = json_encode($placesArr);
                
                $update = $MoodleDBObj->UpdateRecordInDB('exammanagement_rooms', $roomObj);
                
                if($update){
                    redirect ($ExammanagementInstanceObj->getExammanagementUrl('chooseRooms', $id), get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
                } else {
                    redirect ($ExammanagementInstanceObj->getExammanagementUrl('chooseRooms', $id), get_string('alteration_failed', 'mod_exammanagement'), null, 'error');
                }
            } else {

                $roomObj = new stdClass();
                $roomObj->roomid = $roomname.'_'.$USER->id.'c';
                $roomObj->name = $roomname;
                
                if($description){
                    $roomObj->description = $description;
                } else {
                    $roomObj->description = get_string('no_description_new_room', 'mod_exammanagement').$placesCount.' '. get_string('places', 'mod_exammanagement');
                }
                $roomObj->seatingplan = base64_encode('');
                
                $placesArr = array();

                for ($i = 0; $i < $placesCount; $i++) {
                    array_push($placesArr, strval($i+1));
                }

                $roomObj->places = json_encode($placesArr);
                
                $roomObj->type = 'customroom';
                $roomObj->moodleuserid = $USER->id;
                $roomObj->misc = NULL;

                $import = $MoodleDBObj->InsertRecordInDB('exammanagement_rooms', $roomObj);

                if($import){
                    redirect ($ExammanagementInstanceObj->getExammanagementUrl('chooseRooms', $id), get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
                } else {
                    redirect ($ExammanagementInstanceObj->getExammanagementUrl('chooseRooms', $id), get_string('alteration_failed', 'mod_exammanagement'), null, 'error');
                }
            }
            
        } else {
            // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
            // or on the first display of the form.

            //Set default data (if any)

            if($roomid){
                $roomObj = $ExammanagementInstanceObj->getRoomObj($roomid);

                if($roomObj->moodleuserid == $USER->id){
                    $roomname = $roomObj->name;
                    $placescount = count(json_decode($roomObj->places));
                    $description = $roomObj->description;
                    $mform->set_data(array('id'=>$id, 'roomname'=>$roomname, 'placescount'=>$placescount, 'description'=>$description, 'existingroom'=>true));
                } else {
                    $mform->set_data(array('id'=>$id));                
                }
            } else {
                $mform->set_data(array('id'=>$id));
            }

            //displays the form
            $mform->display();
        }    

        $MoodleObj->outputFooter();
    } else { // if user hasnt entered correct password for this session: show enterPasswordPage
        redirect ($ExammanagementInstanceObj->getExammanagementUrl('checkPassword', $ExammanagementInstanceObj->getCm()->id), null, null, null);
    }
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
