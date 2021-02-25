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

use mod_exammanagement\forms\assignPlacesForm;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

use mod_exammanagement;

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$uap  = optional_param('uap', 0, PARAM_INT);

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$MoodleObj = Moodle::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();
$UserObj = User::getInstance($id, $e, $ExammanagementInstanceObj->getCm()->instance);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

  if($ExammanagementInstanceObj->isExamDataDeleted()){
    $MoodleObj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
  } else {
    if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

      $MoodleObj->setPage('assignPlaces');

			if(!$ExammanagementInstanceObj->getRoomsCount()){
				$MoodleObj->redirectToOverviewPage('forexam', get_string('no_rooms_added', 'mod_exammanagement'), 'error');
			} else if (!$UserObj->getParticipantsCount()) {
				$MoodleObj->redirectToOverviewPage('forexam', get_string('no_participants_added', 'mod_exammanagement'), 'error');
			}

			$MoodleObj->outputPageHeader();

			if($uap){
				// reset all exiting places for participants
        $MoodleDBObj->setFieldInDB('exammanagement_participants', 'roomid', NULL, array('exammanagement' => $ExammanagementInstanceObj->getCm()->instance));
        $MoodleDBObj->setFieldInDB('exammanagement_participants', 'roomname', NULL, array('exammanagement' => $ExammanagementInstanceObj->getCm()->instance));
        $MoodleDBObj->setFieldInDB('exammanagement_participants', 'place', NULL, array('exammanagement' => $ExammanagementInstanceObj->getCm()->instance));
			}

			//Instantiate form
			$mform = new assignPlacesForm(null, array('id'=>$id, 'e'=>$e));

			//Form processing and displaying is done here
			if ($mform->is_cancelled()) {
				//Handle form cancel operation, if cancel button is present on form

				$MoodleObj->redirectToOverviewPage('aftercorrection', get_string('operation_canceled', 'mod_exammanagement'), 'warning');

			} else if ($fromform = $mform->get_data()) {
			  //In this case you process validated data. $mform->get_data() returns data posted in form.

        if(isset($fromform->revert_seat_assignment) && $fromform->revert_seat_assignment){ // All existing seat assignments should be deleted
            $MoodleDBObj->setFieldInDB('exammanagement_participants', 'roomid', NULL, array('exammanagement' => $ExammanagementInstanceObj->getCm()->instance));
            $MoodleDBObj->setFieldInDB('exammanagement_participants', 'roomname', NULL, array('exammanagement' => $ExammanagementInstanceObj->getCm()->instance));
            $MoodleDBObj->setFieldInDB('exammanagement_participants', 'place', NULL, array('exammanagement' => $ExammanagementInstanceObj->getCm()->instance));

            $participants = $UserObj->getExamParticipants(array('mode'=>'all'), array('matrnr'), $fromform->assignment_mode_places); // get all exam participants sorted by sortmode
        } else {
            $participants = $UserObj->getExamParticipants(array('mode'=>'no_seats_assigned'), array('matrnr'), $fromform->assignment_mode_places); // Todo: get only exam participants without places sorted by sortmode
        }

        if($fromform->assignment_mode_rooms === '1'){
          $examRooms = $ExammanagementInstanceObj->getRooms('examrooms', 'places_smalltobig');
        } else if ($fromform->assignment_mode_rooms === '2'){
          $examRooms = $ExammanagementInstanceObj->getRooms('examrooms', 'places_bigtosmall');
        } else {
          $examRooms = $ExammanagementInstanceObj->getRooms('examrooms', 'places_bigtosmall');
        }

        if(!$participants){
          $MoodleObj->redirectToOverviewPage('forexam', get_string('no_participants_added', 'mod_exammanagement'), 'error');
        }

        // debugging
        var_dump($fromform->assignment_mode_places);
        var_dump('<br>');
        var_dump($fromform->assignment_mode_rooms);
        var_dump('<br>');
        var_dump('<br>');

        foreach($examRooms as $room){
          var_dump(count(json_decode($room->places)));
          var_dump('<br>');
        }
        var_dump('<br>');
        var_dump(count($participants));

        foreach($participants as $participant){
          var_dump($participant->lastname);
          var_dump('<br>');
          var_dump($participant->matrnr);
          var_dump('<br>');
          var_dump($participant->id);
          var_dump('<br>');
          var_dump('<br>');
        }

        $participantsCount = 0;

				if($fromform->assignment_mode_places == '1'){

          foreach($examRooms as $room){

            if($room){
              $places = json_decode($room->places);	// get places of this room

              foreach($participants as $key => $participant){

                if($key >= $participantsCount){

                  if(isset($participant->moodleuserid) && $participant->login === NULL){
                    $participant->firstname = NULL;
                    $participant->lastname = NULL;
                  }

                  $participant->roomid = $room->roomid;
                  $participant->roomname = $room->name;
                  $participant->place = array_shift($places);

                  // set room and place
                  //$MoodleDBObj->UpdateRecordInDB('exammanagement_participants', $participant);

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
        }

        switch ($fromform->assignment_mode_places) { // TODO: save sortmode to db
          case 0:
              echo "1";
              break;
          case 1:
              echo "2";
              break;
          case 2:
              echo "3";
              break;
        }

        // if($participantsCount < count($participants)){	// if users are left without a room
        //   $MoodleObj->redirectToOverviewPage('forexam', get_string('participants_missing_places', 'mod_exammanagement'), 'error');
        // } else {
        //   $MoodleObj->redirectToOverviewPage('forexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');
        // }
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