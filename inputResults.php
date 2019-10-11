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
 * Allows teacher to input results to mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\inputResultsForm;
use mod_exammanagement\ldap\ldapManager;
use core\output\notification;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$input  = optional_param('matrnr', 0, PARAM_RAW);

$MoodleObj = Moodle::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$LdapManagerObj = ldapManager::getInstance($id, $e);
$UserObj = User::getInstance($id, $e);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

	if($ExammanagementInstanceObj->isExamDataDeleted()){
        $MoodleObj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
	} else {

		if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

			if(!$UserObj->getParticipantsCount()){
				$MoodleObj->redirectToOverviewPage('aftercorrection', get_string('no_participants_added', 'mod_exammanagement'), 'error');
			} else if(!$ExammanagementInstanceObj->getTaskCount()){
				$MoodleObj->redirectToOverviewPage('aftercorrection', get_string('no_tasks_configured', 'mod_exammanagement'), 'error');
			}

			$MoodleObj->setPage('inputResults');
			$MoodleObj-> outputPageHeader();

			$matrnr = false;
			$case='';
			$result;
			$firstname = '';
			$lastname = '';

			if ($input){

				$filtered_input = preg_replace('/[^0-9]/', '', $input);

				if($filtered_input !== $input){ // input containes invalid chars
					$case = 'novalidmatrnr';

				} else {				//check if input is valid barcode and then convert barcoe to matrnr
					$inputLength = strlen($filtered_input);

					if ($inputLength == 13){ //input is correctly formatted barcode
						$checksum = $ExammanagementInstanceObj->buildChecksumExamLabels(substr($input, 0, 12));

						if ($checksum == substr($input, -1)){ //if checksum is correct
							$matrnr = substr($input, 5, -1); //extract matrnr from barcode
						} else {
							$case = 'novalidbarcode';
						}
					} else if ($inputLength <= 7){ // input is probably a matrnr
						$matrnr = $input;

					} else if ($inputLength){ //input is probably a barcode but not correctly formatted (e. g. missing leading zeros)

						$padded_input = str_pad($input, 13, "0", STR_PAD_LEFT);

						$checksum = $ExammanagementInstanceObj->buildChecksumExamLabels(substr($padded_input, 0, 12));

						if ($checksum == substr($padded_input, -1)){ //if checksum is correct
							$matrnr = substr($padded_input, 5, -1); //extract matrnr from barcode
						} else {
							$case = 'novalidbarcode';
						}
						
					}

					if($matrnr){

						if($UserObj->checkIfValidMatrNr($matrnr)){

							// convert matrnr to user
							$userlogin;
							$userid;
		
								if($LdapManagerObj->is_LDAP_config()){
										$ldapConnection = $LdapManagerObj->connect_ldap();
		
										$userlogin = $LdapManagerObj->getLoginForMatrNr($ldapConnection, $matrnr);
		
										if($userlogin){
											$userid = $MoodleDBObj->getFieldFromDB('user','id', array('username' => $userlogin));
										}
		
								} else {
									$userid = $LdapManagerObj->getMatriculationNumber2ImtLoginTest($matrnr);
		
									if(!$userid){
										$userlogin = $LdapManagerObj->getMatriculationNumber2ImtLoginNoneMoodleTest($matrnr);
									} else {
										$userlogin = false;
									}
		
								}
		
								$participantObj = false;
		
								// getParticipantObj
								if($userid !== false && $userid !== null){
									$participantObj = $UserObj->getExamParticipantObj($userid);
								} else if($userlogin !== false && $userlogin !== null){
									$participantObj = $UserObj->getExamParticipantObj(false, $userlogin);
								}
		
								// if user is participant
								if($participantObj && $UserObj->checkIfAlreadyParticipant($participantObj->moodleuserid, $userlogin)){
									$case = 'participant';
		
									if($userid !== false && $userid !== null){
										$MoodleUserObj = $UserObj->getMoodleUser($userid);
										$firstname = $MoodleUserObj->firstname;
										$lastname = $MoodleUserObj->lastname;
									} else {
										$firstname = $participantObj->firstname;
										$lastname = $participantObj->lastname;
									}
		
									if($UserObj->participantHasResults($participantObj)){ // if participants has results
										$case = 'participantwithresults';		
									}
								} else {
									$case = 'noparticipant';
									$matrnr = false;
								}
						} else {
							$case = 'novalidmatrnr';
							$matrnr = false;
						}
					}
				}
			}

			//Instantiate Textfield_form
			$mform = new inputResultsForm(null, array('id'=>$id, 'e'=>$e, 'matrnr'=>$matrnr, 'firstname'=>$firstname, 'lastname'=>$lastname));

			//Form processing and displaying is done here
			if ($mform->is_cancelled()) {
				//Handle form cancel operation, if cancel button is present on form
				$MoodleObj->redirectToOverviewPage('beforeexam', get_string('operation_canceled', 'mod_exammanagement'), 'success');

			} else if ($fromform = $mform->get_data()) {
			//In this case you process validated data. $mform->get_data() returns data posted in form.

				$matrval = $fromform->matrval;

				if ($matrval){
						redirect ('inputResults.php?id='.$id.'&matrnr='.$fromform->matrnr, null, null, null);
				} else {
					
					if($LdapManagerObj->is_LDAP_config()){
							$ldapConnection = $LdapManagerObj->connect_ldap();

							$userlogin = $LdapManagerObj->getLoginForMatrNr($ldapConnection, $fromform->matrnr);

							if($userlogin){
								$userid = $MoodleDBObj->getFieldFromDB('user','id', array('username' => $userlogin));
							}
					} else {
							$userid = $LdapManagerObj->getMatriculationNumber2ImtLoginTest($fromform->matrnr);

							if(!$userid){
								$userlogin = 'tool_generator_'.substr($fromform->matrnr, 1);
							}
					}

					// getParticipantObj
					if($userid !== false && $userid !== null){
						$participantObj = $UserObj->getExamParticipantObj($userid);
					} else if($userlogin !== false && $userlogin !== null){
						$participantObj = $UserObj->getExamParticipantObj(false, $userlogin);
					}

					if($participantObj){
						$participantObj->examstate = json_encode($fromform->state);

						if($fromform->state['nt']=='1' || $fromform->state['fa']=='1' || $fromform->state['ill']=='1'){
								foreach ($fromform->points as $task => $points){
										$fromform->points[$task] = 0;
								}
						}

						$participantObj->exampoints = json_encode($fromform->points);
						$participantObj->timeresultsentered = time();

						$update = $MoodleDBObj->UpdateRecordInDB('exammanagement_participants', $participantObj);
						if($update){
							redirect ($ExammanagementInstanceObj->getExammanagementUrl('inputResults', $id), null, null, null);
						} else {
							redirect ($ExammanagementInstanceObj->getExammanagementUrl('inputResults', $id), get_string('alteration_failed', 'mod_exammanagement'), null, notification::NOTIFY_ERROR);
						}


					} else{
						redirect ($ExammanagementInstanceObj->getExammanagementUrl('inputResults', $id), get_string('noparticipant', 'mod_exammanagement'), null, notification::NOTIFY_ERROR);

					}
					
				}

			} else {
			// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
			// or on the first display of the form.

				switch ($case) {
					case 'participantwithresults':

						$stateObj = json_decode($participantObj->examstate);
						$pointsArr = json_decode($participantObj->exampoints);

						$mform->set_data(array('id'=>$id, 'matrval'=>0, 'matrnr'=>$matrnr, 'state[nt]'=>$stateObj->nt, 'state[fa]'=>$stateObj->fa, 'state[ill]'=>$stateObj->ill));

						foreach ($pointsArr as $key=>$points){
							$mform->set_data(array('points['.$key.']'=>$points));
						}
						break;
					case 'participant':
								$mform->set_data(array('id'=>$id, 'matrval'=>0, 'matrnr'=>$matrnr));
						break;
					case 'noparticipant':
								$mform->set_data(array('id'=>$id, 'matrval'=>1,));
								\core\notification::add(get_string('noparticipant', 'mod_exammanagement'), 'error');
						break;
						case 'novalidmatrnr':
								$mform->set_data(array('id'=>$id, 'matrval'=>1,));
								\core\notification::add(get_string('invalid_matrnr', 'mod_exammanagement'), 'error');
						break;
						case 'novalidbarcode':
								$mform->set_data(array('id'=>$id, 'matrval'=>1,));
								\core\notification::add(get_string('invalid_barcode', 'mod_exammanagement'), 'error');
						break;
						default:
								$mform->set_data(array('id'=>$id, 'matrval'=>1,));
								break;
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