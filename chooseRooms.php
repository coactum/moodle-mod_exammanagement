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
 * Allows teacher to choose rooms for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\chooseRoomsForm;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

global $USER;

$deletecustomroomid  = optional_param('deletecustomroomid', 0, PARAM_TEXT);

$MoodleObj = Moodle::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();
$UserObj = User::getInstance($id, $e);
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

  if($ExammanagementInstanceObj->isExamDataDeleted()){
    $MoodleObj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
  } else {

      if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

        $MoodleObj->setPage('chooseRooms');
        $MoodleObj->outputPageHeader();

        if($deletecustomroomid){

          if($MoodleDBObj->checkIfRecordExists('exammanagement_rooms', array('roomid' => $deletecustomroomid, 'moodleuserid' => $USER->id))){
            if(!in_array($deletecustomroomid, $ExammanagementInstanceObj->getSavedRooms())){
              $MoodleDBObj->DeleteRecordsFromDB('exammanagement_rooms', array('roomid' => $deletecustomroomid, 'moodleuserid' => $USER->id));
            } else {
              redirect ('chooseRooms.php?id='.$id, get_string('room_deselected_as_examroom', 'mod_exammanagement'), null, 'error');
            }
          }
        }

        //Instantiate form
        $mform = new chooseRoomsForm(null, array('id'=>$id, 'e'=>$e));

        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
          //Handle form cancel operation, if cancel button is present on form
          $MoodleObj->redirectToOverviewPage('beforeexam', get_string('operation_canceled', 'mod_exammanagement'), 'warning');

        } else if ($fromform = $mform->get_data()) {
          //In this case you process validated data. $mform->get_data() returns data posted in form.

          $allRooms = get_object_vars($fromform);

          $roomsArray = $allRooms["rooms"];
          $checkedRooms = array();
          $oldRooms = json_decode($ExammanagementInstanceObj->moduleinstance->rooms);

            foreach ($roomsArray as $key => $value){
              if ($value==1 && is_string($value)){
                array_push($checkedRooms, $key);
              }

            }

            // reset places assignment if an exam room where participants are seated is deselected
            if($oldRooms){
              $deselectedRoomsArr = array_diff($oldRooms, $checkedRooms); // checking if some old exam rooms are deselected
            } else {
              $deselectedRoomsArr = null;
            }           
            if(isset($deselectedRoomsArr)){

              foreach($deselectedRoomsArr as $roomid){

                if($UserObj->getAllExamParticipantsByRoom($roomid)){ // if there are participants that have places in some deselected room: delete whole places assignment

                  $MoodleDBObj->setFieldInDB('exammanagement_participants', 'roomid', NULL, array('plugininstanceid' => $id));
                  $MoodleDBObj->setFieldInDB('exammanagement_participants', 'roomname', NULL, array('plugininstanceid' => $id));
                  $MoodleDBObj->setFieldInDB('exammanagement_participants', 'place', NULL, array('plugininstanceid' => $id));
                  break;
                }      
              }
            }

            sort($checkedRooms); //sort checked rooms ids for saving in DB

            $ExammanagementInstanceObj->moduleinstance->rooms = json_encode($checkedRooms);

          $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
          if($update){
            $MoodleObj->redirectToOverviewPage('beforeexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');
          } else {
            $MoodleObj->redirectToOverviewPage('beforeexam', get_string('alteration_failed', 'mod_exammanagement'), 'error');
          }

        } else {
          // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
          // or on the first display of the form.

          //Set default data (if any)
          $mform->set_data(array('id'=>$id));

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