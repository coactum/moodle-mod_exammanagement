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
 * choose rooms for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
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
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){
  $MoodleObj->setPage('chooseRooms');
  $MoodleObj-> outputPageHeader();

  if($deletecustomroomid){

		if($MoodleDBObj->checkIfRecordExists('exammanagement_rooms', array('roomid' => $deletecustomroomid, 'moodleuserid' => $USER->id))){
      if(!in_array($deletecustomroomid, $ExammanagementInstanceObj->getSavedRooms())){
        $MoodleDBObj->DeleteRecordsFromDB('exammanagement_rooms', array('roomid' => $deletecustomroomid, 'moodleuserid' => $USER->id));
      } else {
        redirect ('chooseRooms.php?id='.$id, 'Der Raum muss zunächst als Prüfungsraum abgewählt werden.', null, 'error');
      }
    }
  }

  //Instantiate form
  $mform = new chooseRoomsForm(null, array('id'=>$id, 'e'=>$e));

  //Form processing and displaying is done here
  if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    $MoodleObj->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

  } else if ($fromform = $mform->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.

    $allRooms = get_object_vars($fromform);

    $roomsArray = $allRooms["rooms"];
    $checkedRooms = array();

			foreach ($roomsArray as $key => $value){
				if ($value==1 && is_string($value)){
					array_push($checkedRooms, $key);
				}

			}

			sort($checkedRooms); //sort checked roomes ids for saving in DB

		  $ExammanagementInstanceObj->moduleinstance->rooms = json_encode($checkedRooms);

		// reset state of places assignment if already set
		if($ExammanagementInstanceObj->isStateOfPlacesCorrect()){
			$ExammanagementInstanceObj->moduleinstance->stateofplaces = 'error';
		}

		$update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
		if($update){
			$MoodleObj->redirectToOverviewPage('beforeexam', 'Räume für die Prüfung wurden ausgewählt', 'success');
		} else {
			$MoodleObj->redirectToOverviewPage('beforeexam', 'Räume konnten nicht für die Prüfung ausgewählt werden', 'error');
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
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
