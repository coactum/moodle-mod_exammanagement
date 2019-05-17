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
 * assigns places to participants for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

use mod_exammanagement;
use stdClass;

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$MoodleObj = Moodle::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();
$UserObj = User::getInstance($id, $e);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

  if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

    $MoodleObj->setPage('assignPlaces');

    $savedRoomsArray = $ExammanagementInstanceObj->getSavedRooms();
    $participantsArray = array_values($UserObj->getAllExamParticipants());
    $assignmentArray = array();
    $newAssignmentObj = '';

    if(!$savedRoomsArray){
      $MoodleObj->redirectToOverviewPage('forexam', get_string('no_rooms_added', 'mod_exammanagement'), 'error');

    } elseif(!$participantsArray){
      $MoodleObj->redirectToOverviewPage('forexam', get_string('no_participants_added', 'mod_exammanagement'), 'error');

    }

    $participantsCount = 0;

    usort($savedRoomsArray, function($a, $b){ //sort array by custom user function (big rooms to smnall rooms)

      global $ExammanagementInstanceObj;

      $aPlaces = $ExammanagementInstanceObj->getRoomObj($a)->places;
      $bPlaces = $ExammanagementInstanceObj->getRoomObj($b)->places;
      
      if ($aPlaces == $bPlaces) { //if names are even sort by first name
          return strcmp($aPlaces, $bPlaces);
      } else{
          return strcmp($aPlaces, $bPlaces); // else sort by last name
      }

    });

    foreach($savedRoomsArray as $key => $roomID){

      $RoomObj = $ExammanagementInstanceObj->getRoomObj($roomID);		//get current Room Object

      $places = json_decode($RoomObj->places);	//get Places of this Room

      foreach($participantsArray as $key1 => $participantObj){

        if($key1 >= $participantsCount){
          
          $participantObj->roomid = $RoomObj->roomid;
          $participantObj->roomname = $RoomObj->name;
          $participantObj->place = array_shift($places);

          // set room and place
          $MoodleDBObj->UpdateRecordInDB('exammanagement_participants', $participantObj);

          $participantsCount +=1;

          if($places == NULL){  // if all places of room are assigned
            break;
          }

        } else if($participantsCount == count($participantsArray)){ //if all users have a place
          break 2;
        }
      }
    }

    if($participantsCount < count($participantsArray)){	//if users are left without a room
      $MoodleObj->redirectToOverviewPage('forexam', get_string('participants_missing_places', 'mod_exammanagement'), 'error');
    }

    $MoodleObj->redirectToOverviewPage('forexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');

  } else { // if user hasnt entered correct password for this session: show enterPasswordPage
    redirect ($ExammanagementInstanceObj->getExammanagementUrl('checkPassword', $ExammanagementInstanceObj->getCm()->id), null, null, null);
  }
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
