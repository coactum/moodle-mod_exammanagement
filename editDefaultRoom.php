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
 * Allows admin to edit default room for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\editDefaultRoomForm;
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

    if($ExammanagementInstanceObj->isExamDataDeleted()){
        $MoodleObj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
	} else {

        if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

            global $USER;

            $MoodleObj->setPage('editDefaultRoom');
            $MoodleObj->outputPageHeader();

            if($roomid){

                $roomObj = $ExammanagementInstanceObj->getRoomObj($roomid);

                if($roomObj){
                    if($roomObj->type == 'defaultroom'){
                        $roomname = $roomObj->name;
                        $places = json_decode($roomObj->places);
                        $placescount = count($places);
                        $description = $roomObj->description;
                        $placesarray =  implode(',', $places);

                        if(isset($places) && count($places) !== 0){
                            $placespreview = implode(',', $places);
                        } else {
                            $placespreview = false;
                        }

                        $roomplanavailable = base64_decode($roomObj->seatingplan);
                    } else {
                        redirect ($ExammanagementInstanceObj->getExammanagementUrl('chooseRooms', $id), get_string('no_editable_default_room', 'mod_exammanagement'), null, 'error');
                    }
                }
            }

            //Instantiate form
            if($roomid && $roomObj && $roomObj->type == 'defaultroom'){
                $mform = new editDefaultRoomForm(null, array('id'=>$id, 'e'=>$e, 'placescount'=>$placescount, 'placespreview'=>$placespreview, 'roomplanavailable'=>$roomplanavailable, 'existingroom'=>true));
            } else {
                $mform = new editDefaultRoomForm(null, array('id'=>$id, 'e'=>$e, 'existingroom'=>false));
            }
            //Form processing and displaying is done here
            if ($mform->is_cancelled()) {
                //Handle form cancel operation, if cancel button is present on form
                redirect ($ExammanagementInstanceObj->getExammanagementUrl('chooseRooms', $ExammanagementInstanceObj->getCm()->id), get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

            } else if ($fromform = $mform->get_data()) {
                //In this case you process validated data. $mform->get_data() returns data posted in form.

                $roomid = $fromform->roomid;
                
                $roomname = $fromform->roomname;
                $description = $fromform->description;

                if(isset($fromform->editplaces)){
                    $editplaces = $fromform->editplaces;
                } else {
                    $editplaces = 1;
                }
                $placesmode = $fromform->placesmode;

                if($editplaces == 1){
                    if($placesmode == 'default'){
                        $placesroom = $fromform->placesroom;
                        $placesfree = $fromform->placesfree;
                    }
                    
                    if($placesmode == 'rows'){
                        $rowscount = $fromform->rowscount;
                        $placesrow = $fromform->placesrow;
                        $placesfree = $fromform->placesfree;
                        $rowsfree = $fromform->rowsfree;
                    }
    
                    if($placesmode == 'all_individual'){
                        $placesarray = $fromform->placesarray;
                    }
                }

                $defaultroom_svg = $mform->get_file_content('defaultroom_svg');

                if($fromform->existingroom == true && $MoodleDBObj->checkIfRecordExists('exammanagement_rooms', array('roomid' => $roomid))){ // if default room exists and should be edited

                    $roomObj = $MoodleDBObj->getRecordFromDB('exammanagement_rooms', array('roomid' => $roomid));
                        
                    $roomObj->name = $roomname;
                    $roomObj->description = $description;
                    
                    if($editplaces == 1){

                        if($placesmode == 'default'){
                            $placesArr = array();

                            for ($i = 1; $i <= $placesroom; $i+=$placesfree+1) {
                                
                                array_push($placesArr, strval($i));
                            }

                            $roomObj->places = json_encode($placesArr);
                        }
                        
                        if($placesmode == 'rows'){
                            $placesArr = array();

                            for($i = 1; $i <= $rowscount; $i = $i + 1 + $rowsfree){
                                for ($j = 1; $j <= $placesrow; $j+=$placesfree+1) {
                                    array_push($placesArr, 'R'.str_pad ( strval($i), 2, '0', STR_PAD_LEFT ).'/P'.str_pad ( strval($j), 2, '0', STR_PAD_LEFT ));
                                }
                            }

                            $roomObj->places = json_encode($placesArr);
                        }
        
                        if($placesmode == 'all_individual'){
                            $placesarray = explode(',', $placesarray);
                            $roomObj->places = json_encode($placesarray);
                        }
                    }

                    if(isset($defaultroom_svg) && $defaultroom_svg !== false){
                        $roomObj->seatingplan = base64_encode($defaultroom_svg);
                    }
                        
                    $update = $MoodleDBObj->UpdateRecordInDB('exammanagement_rooms', $roomObj);
                        
                    if($update){
                        redirect ($ExammanagementInstanceObj->getExammanagementUrl('chooseRooms', $id), get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
                    } else {
                        redirect ($ExammanagementInstanceObj->getExammanagementUrl('chooseRooms', $id), get_string('alteration_failed', 'mod_exammanagement'), null, 'error');
                    }
                } else { // if default room doesn't exists and should be created

                    $roomObj = new stdClass();
                    $roomObj->roomid = $roomid;
                    $roomObj->name = $roomname;
                    $roomObj->description = $description;
                    
                    if($placesmode == 'default'){
                        $placesArr = array();

                        for ($i = 1; $i <= $placesroom; $i+=$placesfree+1) {
                                
                            array_push($placesArr, strval($i));
                        }

                        $roomObj->places = json_encode($placesArr);
                    }
                        
                    if($placesmode == 'rows'){
                        $placesArr = array();

                        for($i = 1; $i <= $rowscount; $i = $i + 1 + $rowsfree){
                            for ($j = 1; $j <= $placesrow; $j+=$placesfree+1) {
                                array_push($placesArr, 'R'.str_pad ( strval($i), 2, '0', STR_PAD_LEFT ).'/P'.str_pad ( strval($j), 2, '0', STR_PAD_LEFT ));
                            }
                        }

                        $roomObj->places = json_encode($placesArr);
                    }
        
                    if($placesmode == 'all_individual'){
                        $placesarray = explode(',', $placesarray);
                        $roomObj->places = json_encode($placesarray);
                    }

                    if(isset($defaultroom_svg)){
                        $roomObj->seatingplan = base64_encode($defaultroom_svg);
                    }

                    $roomObj->type = 'defaultroom';
                    $roomObj->moodleuserid = NULL;
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

                    if(isset($roomObj) && $roomObj !== false && $roomObj->type == 'defaultroom'){
                        $mform->set_data(array('id'=>$id, 'roomid'=>$roomid,'roomname'=>$roomname, 'placescount'=>$placescount, 'description'=>$description, 'placesarray'=>$placesarray, 'existingroom'=>true));
                    } else {
                        $mform->set_data(array('id'=>$id, 'existingroom'=>false));                
                    }
                } else {
                    $mform->set_data(array('id'=>$id, 'existingroom'=>false));
                }

                //displays the form
                $mform->display();
            }    

            $MoodleObj->outputFooter();

        } else { // if user hasnt entered correct password for this session: show enterPasswordPage
            redirect ($ExammanagementInstanceObj->getExammanagementUrl('checkPassword', $ExammanagementInstanceObj->getCm()->id), null, null, null);
        }
    }
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}