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
 * Allows teacher to assign places to participants for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
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

  if($ExammanagementInstanceObj->isExamDataDeleted()){
    $MoodleObj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
  } else {
    if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

      $participants = $UserObj->getExamParticipants(array('mode'=>'all'), array()); // get all exam participants sorted by name

      $savedRoomIDs = $ExammanagementInstanceObj->getSavedRooms(); // get the ids of all used exam rooms
      $savedRooms = false;

      foreach($savedRoomIDs as $roomid){                            // construct array with all used exam room objects
          $room = $ExammanagementInstanceObj->getRoomObj($roomid);
          
          $savedRooms[$roomid] = $room;
      }

      if(!$savedRooms){
        $MoodleObj->redirectToOverviewPage('forexam', get_string('no_rooms_added', 'mod_exammanagement'), 'error');

      } else if(!$participants){
        $MoodleObj->redirectToOverviewPage('forexam', get_string('no_participants_added', 'mod_exammanagement'), 'error');

      }

      usort($savedRooms, function($a, $b){ // sort rooms by places count through custom user function (small to big rooms)

        $aPlaces = count(json_decode($a->places));
        $bPlaces = count(json_decode($b->places));

        return strnatcmp($aPlaces, $bPlaces); // sort by places count

      });

      $savedRooms = array_reverse($savedRooms); // reverse array: now big to small rooms

      $participantsCount = 0;

      foreach($savedRooms as $key => $room){

        if($room){
          $places = json_decode($room->places);	// get places of this room

          foreach($participants as $key1 => $participant){
    
            if($key1 >= $participantsCount){
              
              $participant->roomid = $room->roomid;
              $participant->roomname = $room->name;
              $participant->place = array_shift($places);
    
              // set room and place
              $MoodleDBObj->UpdateRecordInDB('exammanagement_participants', $participant);
    
              $participantsCount +=1;
    
              if($places == NULL){  // if all places of room are assigned
                break;
              }
    
            } else if($participantsCount == count($participants)){ // if all users have a place
              break 2;
            }
          }
        }
        
      }

      if($participantsCount < count($participants)){	// if users are left without a room
        $MoodleObj->redirectToOverviewPage('forexam', get_string('participants_missing_places', 'mod_exammanagement'), 'error');
      }

      $MoodleObj->redirectToOverviewPage('forexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');

    } else { // if user hasnt entered correct password for this session: show enterPasswordPage
      redirect ($ExammanagementInstanceObj->getExammanagementUrl('checkPassword', $ExammanagementInstanceObj->getCm()->id), null, null, null);
    }
  }
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}