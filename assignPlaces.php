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

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

    $MoodleObj->setPage('assignPlaces');

    $savedRoomsArray = $ExammanagementInstanceObj->getSavedRooms();
    $participantsIDsArray = $ExammanagementInstanceObj->getSavedParticipants();
    $assignmentArray = array();
    $newAssignmentObj = '';

    if(!$savedRoomsArray){
      $ExammanagementInstanceObj->unsetStateOfPlaces('error');
      $MoodleObj->redirectToOverviewPage('forexam', 'Noch keine Räume ausgewählt. Fügen Sie mindestens einen Raum zur Prüfung hinzu und starten Sie die automatische Sitzplatzzuweisung erneut.', 'error');

    } elseif(!$participantsIDsArray){
      $ExammanagementInstanceObj->unsetStateOfPlaces('error');
      $MoodleObj->redirectToOverviewPage('forexam', 'Noch keine Benutzer zur Prüfung hinzugefügt. Fügen Sie mindestens einen Benutzer zur Prüfung hinzu und starten Sie die automatische Sitzplatzzuweisung erneut.', 'error');

    }

    foreach($savedRoomsArray as $key => $roomID){

      $RoomObj = $ExammanagementInstanceObj->getRoomObj($roomID);		//get current Room Object

      $Places = json_decode($RoomObj->places);	//get Places of this Room

      $assignmentRoomObj = new stdClass();

      $assignmentRoomObj->roomid = $RoomObj->roomid;
      $assignmentRoomObj->roomname = $RoomObj->name;
      $assignmentRoomObj->assignments = array();

      foreach($Places as $key => $placeID){
        $currentParticipantID = array_pop($participantsIDsArray);

        $newAssignmentObj = $ExammanagementInstanceObj->assignPlaceToUser($currentParticipantID, $placeID);
        array_push($assignmentRoomObj->assignments, $newAssignmentObj);

        if(!$participantsIDsArray){						//if all users have a place: stop
          array_push($assignmentArray, $assignmentRoomObj);
          break 2;
        }

      }

      array_push($assignmentArray, $assignmentRoomObj);

      if(!$participantsIDsArray){						//if all users have a place: stop
        break;
      }
    }

    if($participantsIDsArray){								//if users are left without a room
      $ExammanagementInstanceObj->unsetStateOfPlaces('error');
      $MoodleObj->redirectToOverviewPage('forexam', 'Einige Benutzer haben noch keinen Sitzplatz. Fügen Sie ausreichend Räume zur Prüfung hinzu und starten Sie die automatische Sitzplatzzuweisung erneut.', 'error');

    }

    $ExammanagementInstanceObj->moduleinstance->stateofplaces='set';

    $ExammanagementInstanceObj->savePlacesAssignment($assignmentArray);

    $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

    $MoodleObj->redirectToOverviewPage('forexam', 'Plätze zugewiesen', 'success');

} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
