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
        $ExammanagementInstanceObj->moduleinstance->assignmentmode = null;
        $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
      }

			//Instantiate form
			$mform = new assignPlacesForm(null, array('id'=>$id, 'e'=>$e));

			//Form processing and displaying is done here
			if ($mform->is_cancelled()) {
				//Handle form cancel operation, if cancel button is present on form

				$MoodleObj->redirectToOverviewPage('aftercorrection', get_string('operation_canceled', 'mod_exammanagement'), 'warning');

			} else if ($fromform = $mform->get_data()) {
			  //In this case you process validated data. $mform->get_data() returns data posted in form.

        var_dump($fromform);

        if($fromform->assign_places_manually){

            $assign_places_manually = 1;
            $examRooms = $ExammanagementInstanceObj->getRooms('examrooms');
            $participants = $UserObj->getExamParticipants(array('mode'=>'all'), array());

            foreach($participants as $participant){
              if($fromform->rooms[$participant->id] !== 'not_selected' && $fromform->places[$participant->id]){

                if(isset($participant->moodleuserid)){
                  $participant->login = NULL;
                  $participant->firstname = NULL;
                  $participant->lastname = NULL;
                }

                $participant->roomid = $fromform->rooms[$participant->id];
                $participant->roomname = $examRooms[$fromform->rooms[$participant->id]]->name;
                $participant->place = $fromform->places[$participant->id];

                $MoodleDBObj->UpdateRecordInDB('exammanagement_participants', $participant);
              }
            }
        } else {
          $assign_places_manually = 0;
        }

        if(!(isset($fromform->keep_seat_assignment) && $fromform->keep_seat_assignment)){ // All existing seat assignments should be deleted
            $MoodleDBObj->setFieldInDB('exammanagement_participants', 'roomid', NULL, array('exammanagement' => $ExammanagementInstanceObj->getCm()->instance));
            $MoodleDBObj->setFieldInDB('exammanagement_participants', 'roomname', NULL, array('exammanagement' => $ExammanagementInstanceObj->getCm()->instance));
            $MoodleDBObj->setFieldInDB('exammanagement_participants', 'place', NULL, array('exammanagement' => $ExammanagementInstanceObj->getCm()->instance));
            $ExammanagementInstanceObj->moduleinstance->assignmentmode = null;
            $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
            $keep_seat_assignment = false;
            $participants = $UserObj->getExamParticipants(array('mode'=>'all'), array('matrnr'), $fromform->assignment_mode_places); // get all exam participants sorted by sortmode
        } else {
            $ExammanagementInstanceObj->moduleinstance->assignmentmode = null;
            $keep_seat_assignment = true;
            $participants = $UserObj->getExamParticipants(array('mode'=>'no_seats_assigned'), array('matrnr'), $fromform->assignment_mode_places); // Todo: get only exam participants without places sorted by sortmode
        }

        if(isset($fromform->assignment_mode_rooms) && $fromform->assignment_mode_rooms === '1'){
          $examRooms = $ExammanagementInstanceObj->getRooms('examrooms', 'places_smalltobig', $keep_seat_assignment);
          $roommode = '1';
        } else if (isset($fromform->assignment_mode_rooms) && $fromform->assignment_mode_rooms === '2'){
          $examRooms = $ExammanagementInstanceObj->getRooms('examrooms', 'places_bigtosmall', $keep_seat_assignment);
          $roommode = '2';
        } else {
          $examRooms = $ExammanagementInstanceObj->getRooms('examrooms', 'places_bigtosmall', $keep_seat_assignment);
          $roommode = '';
        }

        if(!$participants){
          $MoodleObj->redirectToOverviewPage('forexam', get_string('no_participants_added', 'mod_exammanagement'), 'error');
        }

        $participantsCount = 0;

				if($examRooms){

          foreach($examRooms as $room){

            if($room){
              foreach($participants as $key => $participant){

                if($key >= $participantsCount){

                  if(isset($participant->moodleuserid)){
                    $participant->login = NULL;
                    $participant->firstname = NULL;
                    $participant->lastname = NULL;
                  }

                  unset($participant->matrnr);

                  $participant->roomid = $room->roomid;
                  $participant->roomname = $room->name;
                  $participant->place = array_shift($room->places);

                  // set room and place
                  $MoodleDBObj->UpdateRecordInDB('exammanagement_participants', $participant);

                  $participantsCount +=1;

                  if($room->places == NULL){  // if all places of room are assigned
                    break;
                  }

                } else if($participantsCount == count($participants)){ // if all users have a place
                  break 2;
                }
              }
            }

          }
        }

        ## save sort modes in db
        switch ($fromform->assignment_mode_places) {
          case 'name':
              $mode_ids = 1 . $roommode . $assign_places_manually;
              break;
          case 'matrnr':
              $mode_ids = 2 . $roommode . $assign_places_manually;
              break;
          case 'random':
              $mode_ids = 3 . $roommode . $assign_places_manually;
              break;
          default:
              $mode_ids = false;
        }

        if($mode_ids){
          $ExammanagementInstanceObj->moduleinstance->assignmentmode = $mode_ids;
          $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
        }

        if($participantsCount < count($participants)){	// if users are left without a room
          $MoodleObj->redirectToOverviewPage('forexam', get_string('participants_missing_places', 'mod_exammanagement'), 'error');
        } else {
          $MoodleObj->redirectToOverviewPage('forexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');
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