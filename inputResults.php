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
 * Prints input results form for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\inputResultsForm;
use mod_exammanagement\ldap\ldapManager;

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
$UserObj = User::getInstance($id, $e, $ExammanagementInstanceObj->moduleinstance->categoryid);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

		if(!$UserObj->getParticipantsCount()){
			$MoodleObj->redirectToOverviewPage('aftercorrection', 'Es müssen erst Teilnehmer zur Prüfung hinzugefügt werden.', 'error');
		} else if(!$ExammanagementInstanceObj->getTaskCount()){
			$MoodleObj->redirectToOverviewPage('aftercorrection', 'Es müssen erst Aufgaben zur Prüfung hinzugefügt werden.', 'error');
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

			if($filtered_input !== $input){
				$case = 'novalidmatrnr';
				$matrnr = false;

			} else {
				//check if input is valid barcode and then convert barcoe to matrnr
				$inputLength = strlen($filtered_input);

				if ($inputLength == 13){ //input is barcode
					$checksum = $ExammanagementInstanceObj->buildChecksumExamLabels(substr($input, 0, 12));

					if ($checksum == substr($input, -1)){ //if checksum is correct
						$matrnr = substr($input, 5, -1); //extract matrnr from barcode
					}
				} else if($inputLength){ //input is no barcode
						$matrnr = $input;
				}

				if($UserObj->checkIfValidMatrNr($matrnr)){

					// convert matrnr to user
					$userlogin;
					$userid;

						if($LdapManagerObj->is_LDAP_config()){
								$ldapConnection = $LdapManagerObj->connect_ldap();

								$userlogin = $LdapManagerObj->studentid2uid($ldapConnection, $matrnr);

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

		//Instantiate Textfield_form
		$mform = new inputResultsForm(null, array('id'=>$id, 'e'=>$e, 'matrnr'=>$matrnr, 'firstname'=>$firstname, 'lastname'=>$lastname));

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$MoodleObj->redirectToOverviewPage('beforeexam', 'Vorgang beendet', 'success');

		} else if ($fromform = $mform->get_data()) {
		//In this case you process validated data. $mform->get_data() returns data posted in form.

			$matrval = $fromform->matrval;

			if ($matrval){
					redirect ('inputResults.php?id='.$id.'&matrnr='.$fromform->matrnr, null, null, null);
			} else {
					$ExammanagementInstanceObj->saveResults($fromform);
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
							\core\notification::add('Ungültige Matrikelnummer', 'error');
					break;
					case 'novalidmatrnr':
							$mform->set_data(array('id'=>$id, 'matrval'=>1,));
							\core\notification::add('Keine gültige Matrikelnummer', 'error');
					break;
					default:
							$mform->set_data(array('id'=>$id, 'matrval'=>1,));
							break;
			}

		//displays the form
		$mform->display();
		}

		$MoodleObj->outputFooter();
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
