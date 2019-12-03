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
 * Allows teacher to add participants from text file to mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\addParticipantsForm;
use mod_exammanagement\ldap\ldapManager;
use PHPExcel_IOFactory;
use stdclass;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once("$CFG->libdir/phpexcel/PHPExcel.php");

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$dtp  = optional_param('dtp', 0, PARAM_INT);

$MoodleObj = Moodle::getInstance($id, $e);
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$LdapManagerObj = ldapManager::getInstance($id, $e);	
$MoodleDBObj = MoodleDB::getInstance();
$UserObj = User::getInstance($id, $e);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

	if($ExammanagementInstanceObj->isExamDataDeleted()){
        $MoodleObj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
	} else {

		if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page	

			$MoodleObj->setPage('addParticipants');
			$MoodleObj->outputPageHeader();

			if($dtp){
				$UserObj->deleteTempParticipants();
			}

			# define participants for form #
			$tempParticipants = $MoodleDBObj->getRecordsFromDB('exammanagement_temp_part', array('plugininstanceid' => $id)); // get all participants that are already readed in and saved as temnp participants

			if($tempParticipants){

				$allParticipants = array(); // will contain all participants ordered in their respective sub array 

				$moodleUsers = array(); // will contain all moodle users for further classification
				$nonMoodleUsers = array(); // will contain all nonmoodle users for further classification

				$badMatriculationnumbers = array(); // will contain all invalid or doubled identifier
				$oddParticipants = array(); // will contain all users that are no course members or have no moodle account but can still be added as exam participants
				$deletedParticipants = array(); // will contain all users that are already read in from file with same header but not in this file and should therefore be deleted
				$existingParticipants = array(); // will contain all user that are already exam participants
				$newMoodleParticipants = array(); // will contain all valid moodle participants that can be added

				$tempIDs = array(); // will contain moodleids and logins of all valid temp participants for checking for deleted users
				$allPotentialIdentifiers = array(); // will contain all potential identifiers from file to check for double entries

				$courseParticipantsIDs = $UserObj->getCourseParticipantsIDs(); // contains moodle user ids of all course participants

				## sort out bad matriculation numbers to badmatrnr array ##

				foreach($tempParticipants as $key => $participant){ // filter invalid/bad matrnr

					if (!$UserObj->checkIfValidMatrNr($participant->identifier)){
						$tempUserObj = new stdclass;
						$tempUserObj->line = $participant->line;
						$tempUserObj->matrnr = $participant->identifier;
						$tempUserObj->state = 'state_badmatrnr';
						
						array_push($badMatriculationnumbers, $tempUserObj);
						unset($tempParticipants[$key]);
					} else if(in_array($participant->identifier, $allPotentialIdentifiers)){
						$tempUserObj = new stdclass;
						$tempUserObj->line = $participant->line;
						$tempUserObj->matrnr = $participant->identifier;
						$tempUserObj->state = 'state_doubled';
						
						array_push($badMatriculationnumbers, $tempUserObj);
						unset($tempParticipants[$key]);
					} else {
						array_push($allPotentialIdentifiers, $participant->identifier);
					}
				}

				## construct arrays with all users (moodle and nonmoodle) with all needed data ##

				if($LdapManagerObj->isLDAPenabled()){ // if ldap is configured
					$ldapConnection = $LdapManagerObj->connect_ldap();
					
					// temp participants from stored in db that should get ldap attributes

					foreach($tempParticipants as $key => $participant){ // construct helper arrays needed for ldap method
						$allMatriculationNumbers[$key] = $participant->identifier;
						$allLines[$key] = $participant->line;
					}


					$users = $LdapManagerObj->getLDAPAttributesForMatrNrs($ldapConnection, $allMatriculationNumbers, array(LDAP_ATTRIBUTE_UID, LDAP_ATTRIBUTE_STUDID), $allLines); //get data for all remaining matriculation numbers from ldap 

					ksort($users);

					// users from ldap
					
					foreach($users as $line => $login){
						$moodleuserid = $MoodleDBObj->getFieldFromDB('user','id', array('username' => $login['login'])); // get moodleid for user

						if($moodleuserid){ // if moodle user
							$moodleUsers[$line] = array('matrnr' => $login['matrnr'], 'login' => $login['login'], 'moodleuserid' => $moodleuserid); // add to array
						} else { // if not a moodle user
							$nonMoodleUsers[$line] = array('matrnr' => $login['matrnr'], 'login' => $login['login'], 'moodleuserid' => false); // add to array
						}
					}
				} else { // for local testing
					foreach($tempParticipants as $key => $participant){

						$userid = $LdapManagerObj->getMatriculationNumber2ImtLoginTest($participant->identifier);

						if($userid){
							$login = $MoodleDBObj->getFieldFromDB('user','username', array('id' => $userid));

							$moodleUsers[$participant->line] = array('matrnr' => $participant->identifier, 'login'=> $login, 'moodleuserid' => $userid);
						} else {
							$login = $LdapManagerObj->getMatriculationNumber2ImtLoginNoneMoodleTest($participant->identifier);
							$nonMoodleUsers[$participant->line] = array('matrnr' => $participant->identifier, 'login' => $login, 'moodleuserid' => false);
						}
					}
				}

				## check moodle users and classify them to array according to case ##
						
				foreach($moodleUsers as $line => $data){ 

					if (isset($data['moodleuserid']) && $data['moodleuserid']){
						$tempUserObj = new stdclass;
						$tempUserObj->line = $line;
						$tempUserObj->moodleuserid = $data['moodleuserid'];
						$tempUserObj->matrnr = $data['matrnr'];
						$tempUserObj->login = $data['login'];

						if($UserObj->checkIfAlreadyParticipant($data['moodleuserid'])){ 	// if user is already saved for instance
							$tempUserObj->state = 'state_existingmatrnr';
							array_push($existingParticipants, $tempUserObj);
							array_push($tempIDs, $data['moodleuserid']); 						//for finding deleted users
						} else if (!in_array($data['moodleuserid'], $courseParticipantsIDs)){ 	// if user is not in course
							$tempUserObj->state = 'state_no_courseparticipant';
							array_push($oddParticipants, $tempUserObj);
							array_push($tempIDs, $data['moodleuserid']); 						//for finding deleted users
						} else {																// if user is a valid new moodle participant
							array_push($newMoodleParticipants, $tempUserObj);
							array_push($tempIDs, $data['moodleuserid']); 						//for finding deleted users
						}

						foreach($tempParticipants as $key => $participant){ 					// unset user from original tempuser array
							if($participant->identifier == $data['matrnr']){
								unset($tempParticipants[$key]);
								break;
							}
						}
					}
				}
				
				## check nonmoodle users and classify them to array according to case ##

				foreach($nonMoodleUsers as $line => $data){

					if (isset($data['login']) && $data['login']){
						$tempUserObj = new stdclass;
						$tempUserObj->line = $line;
						$tempUserObj->moodleuserid = false;
						$tempUserObj->matrnr = $data['matrnr'];
						$tempUserObj->login = $data['login'];

						if($UserObj->checkIfAlreadyParticipant(false, $data['login'])){ // if user is already saved as participant
							$tempUserObj->state = 'state_existingmatrnr';
							array_push($existingParticipants, $tempUserObj);
							array_push($tempIDs, $data['login']); 							//for finding deleted users
						} else { 															// if user is a valid new nonmoodle participant
							$tempUserObj->state = 'state_nonmoodle';
							array_push($oddParticipants, $tempUserObj);
							array_push($tempIDs, $data['login']); 							//for finding deleted users
						}

						foreach($tempParticipants as $key => $participant){					 // unset user from original tempuser array
							if($participant->identifier == $data['matrnr']){
								unset($tempParticipants[$key]);
								break;
							}
						}
					}
				}

				## push all remaining matriculation numbers that could not be resolved by ldap into the bad matriculationnumbers array ##

				foreach($tempParticipants as $key => $participant){
					$tempUserObj = new stdclass;
					$tempUserObj->line = $participant->line;
					$tempUserObj->matrnr = $participant->identifier;
					$tempUserObj->state = 'state_badmatrnr';

					array_push($badMatriculationnumbers, $tempUserObj);
					unset($tempParticipants[$key]);
				}

				## check if users should be deleted ##
				
				### get header id ###
				$tempfileheader = json_decode($ExammanagementInstanceObj->moduleinstance->tempimportfileheader);
				$savedFileHeadersArr = json_decode($ExammanagementInstanceObj->moduleinstance->importfileheaders);
				$headerid;

				if(!$savedFileHeadersArr && $tempfileheader){ // if there are no saved headers by now
					$headerid = 1;
				} else if($savedFileHeadersArr && $tempfileheader){
					$saved = false;
					foreach($savedFileHeadersArr as $key => $header){ // if new header is already saved
						if($tempfileheader == $header){
							$headerid = $key+1;
							$saved = true;
						}
					}
						
					if(!$saved){ // if new header is not saved yet
						$headerid = count($savedFileHeadersArr)+1;
					}
				} else if(!$tempfileheader){ // if reading of tempfileheader fails
					$headerid = 0;
				}

				### get saved participants for headerid ###				
				$oldParticipants = $UserObj->getExamParticipants(array('mode'=>'header', 'id' =>$headerid), array('matrnr'));        

				if($oldParticipants){

					foreach($oldParticipants as $key => $participant){
		
						if($participant->moodleuserid && !in_array($participant->moodleuserid, $tempIDs)){ // moodle participant that is not readed in again and should therefore be deleted

							$deletedMatrNrObj = new stdclass;
							$deletedMatrNrObj->moodleuserid = $participant->moodleuserid;
							$deletedMatrNrObj->matrnr = $participant->matrnr;
							$deletedMatrNrObj->firstname = false;
							$deletedMatrNrObj->lastname = false;
							$deletedMatrNrObj->line = '';
			
							array_push($deletedParticipants, $deletedMatrNrObj);

						} else if($participant->moodleuserid === null && $participant->imtlogin && !in_array($participant->imtlogin, $tempIDs)){  // moodle participant that is not readed in again and should therefore be deleted
							$deletedMatrNrObj = new stdclass;
							$deletedMatrNrObj->moodleuserid = false;
							$deletedMatrNrObj->matrnr = $participant->matrnr;
							$deletedMatrNrObj->firstname = $participant->firstname;
							$deletedMatrNrObj->lastname = $participant->lastname;
							$deletedMatrNrObj->line = '';

							array_push($deletedParticipants, $deletedMatrNrObj);
						}
					}
				}

				$allParticipants['badMatriculationNumbers'] = $badMatriculationnumbers;
				$allParticipants['deletedParticipants'] = $deletedParticipants;
				$allParticipants['oddParticipants'] = $oddParticipants;
				$allParticipants['existingParticipants'] = $existingParticipants;
				$allParticipants['newMoodleParticipants'] = $newMoodleParticipants;

				# Instantiate form #
				$mform = new addParticipantsForm(null, array('id'=>$id, 'e'=>$e, 'allParticipants' => $allParticipants));

			} else {
				# Instantiate form #
				$mform = new addParticipantsForm(null, array('id'=>$id, 'e'=>$e));
			}

			// Form processing and displaying is done here
			if ($mform->is_cancelled()) {
				// Handle form cancel operation, if cancel button is present on form

				redirect ($ExammanagementInstanceObj->getExammanagementUrl('viewParticipants', $ExammanagementInstanceObj->getCm()->id), get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

			} else if ($fromform = $mform->get_data()) {
			// In this case you process validated data. $mform->get_data() returns data posted in form.

				// retrieve Files from form
				$text_file = $mform->get_file_content('participantslist_text');

				if (!$text_file){ // if no import file and exam participants should be saved in db
					
					# get checked userids from form #
					$participantsIdsArr = $UserObj->filterCheckedParticipants($fromform);
					$noneMoodleParticipantsMatrNrArr = array();
					$deletedParticipantsIdsArr = $UserObj->filterCheckedDeletedParticipants($fromform);
									
					if($participantsIdsArr != false || $deletedParticipantsIdsArr != false){

						# get headers and temp file header #
						$tempfileheader = json_decode($ExammanagementInstanceObj->moduleinstance->tempimportfileheader);
						$savedFileHeadersArr = json_decode($ExammanagementInstanceObj->moduleinstance->importfileheaders);
						$newheaderid;

						# save new file header #
						if(!$savedFileHeadersArr && $tempfileheader){ // if there are no saved headers by now
							$savedFileHeadersArr = array();
							$newheaderid = 1;
							array_push($savedFileHeadersArr, $tempfileheader);
						} else if($savedFileHeadersArr && $tempfileheader){
							$saved = false;
							
							foreach($savedFileHeadersArr as $key => $header){ // if new header is already saved
								if($tempfileheader == $header){
									$newheaderid = $key+1;
									$saved = true;
								}
							}
							
							if(!$saved){ // if new header is not saved yet
								$newheaderid = count($savedFileHeadersArr)+1;
								array_push($savedFileHeadersArr, $tempfileheader);
							}
						}  else if(!$tempfileheader){ // if reading of tempfileheader fails
							$headerid = 0;
						}

						$ExammanagementInstanceObj->moduleinstance->importfileheaders = json_encode($savedFileHeadersArr);

						# add new participants #
						if($participantsIdsArr){ 
							$userObjArr = array();

							foreach($participantsIdsArr as $key => $identifier){

								$temp = explode('_', $identifier);

								if($temp[0]== 'mid'){ // if participant is moodle user
									$user = new stdClass();
									$user->plugininstanceid = $id;
									$user->courseid = $ExammanagementInstanceObj->getCourse()->id;
									$user->categoryid = $ExammanagementInstanceObj->moduleinstance->categoryid;
									$user->moodleuserid = $temp[1];
									$user->imtlogin = null;
									$user->firstname = null;
									$user->lastname = null;
									$user->email = null;
									$user->headerid = $newheaderid;

									array_push($userObjArr, $user);

									unset($participantsIdsArr[$key]);

								} else {
									array_push($noneMoodleParticipantsMatrNrArr, $temp[1]);
								}
							}

							$noneMoodleParticipantsArr = array();

							if($LdapManagerObj->isLDAPenabled()){ // if ldap is configured
								$ldapConnection = $LdapManagerObj->connect_ldap();

								$noneMoodleParticipantsArr = $LdapManagerObj->getLDAPAttributesForMatrNrs($ldapConnection, $noneMoodleParticipantsMatrNrArr, array( "sn", "givenName", "upbMailPreferredAddress", LDAP_ATTRIBUTE_UID, LDAP_ATTRIBUTE_STUDID));
											
							} else { // for local testing during development

								foreach($participantsIdsArr as $key => $identifier){
									$temp = explode('_', $identifier);
									$matrnr = $temp[1];

									$login = ''.$LdapManagerObj->getMatriculationNumber2ImtLoginNoneMoodleTest($matrnr);

									$rand = rand(1,3);
									switch ($rand){
										case 1:
											$firstname = 'Peter';
											break;
										case 2:
											$firstname = 'Tony';
											break;
										case 3:
											$firstname = 'Steven';
											break;
									} 

									$rand = rand(1,3);
									switch ($rand){
										case 1:
											$lastname = 'Parker';
											break;
										case 2:
											$lastname = 'Stark';
											break;
										case 3:
											$lastname = 'Strange';
											break;
									}

									$email = 'Test@Testi.test';
									
									$noneMoodleParticipantsArr[$matrnr] = array('login' => $login, 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email);
								}
							}

							foreach($participantsIdsArr as $key => $identifier){ // if participant is has no moodle account
								$temp = explode('_', $identifier);

								$matrnr = $temp[1];

								$user = new stdClass();
								$user->plugininstanceid = $id;
								$user->courseid = $ExammanagementInstanceObj->getCourse()->id;
								$user->categoryid = $ExammanagementInstanceObj->moduleinstance->categoryid;
								$user->moodleuserid = null;
								
								$login = $noneMoodleParticipantsArr[$matrnr]['login'];
								if($login){
									$user->imtlogin = $login;
								} else {
									$user->imtlogin = null;
								}

								$firstname = $noneMoodleParticipantsArr[$matrnr]['firstname'];
								if($firstname){
									$user->firstname = $firstname;
								} else {
									$user->firstname = null;
								}

								$lastname = $noneMoodleParticipantsArr[$matrnr]['lastname'];
								if($lastname){
									$user->lastname = $lastname;
								} else {
									$user->lastname = null;
								}

								$email = $noneMoodleParticipantsArr[$matrnr]['email'];
								if($email){
									$user->email = $email;
								} else {
									$user->email = null;
								}

								$user->headerid = $newheaderid;

								array_push($userObjArr, $user);
							}

							## insert records of new participants ##
							$MoodleDBObj->InsertBulkRecordsInDB('exammanagement_participants', $userObjArr);

						}

						# delete participants that should be deleted #

						if($deletedParticipantsIdsArr){
							foreach($deletedParticipantsIdsArr as $identifier){
								$temp = explode('_', $identifier);

								if($temp[0]== 'mid'){ // delete moodle participant
									$UserObj->deleteParticipant($temp[1], false);
								} else { // delete participant without moodle account

									$userlogin = false;

									if($LdapManagerObj->isLDAPenabled()){
										$ldapConnection = $LdapManagerObj->connect_ldap();
		
										$userlogin = $LdapManagerObj->getLoginForMatrNr($ldapConnection, $temp[1]);
		
									} else {
										$userlogin = $LdapManagerObj->getMatriculationNumber2ImtLoginNoneMoodleTest($temp[1]);
									}

									if($userlogin){
										$UserObj->deleteParticipant(false, $userlogin);
									}
								}
							}
						}

						# delete temp file header and update saved file headers #
						$ExammanagementInstanceObj->moduleinstance->tempimportfileheader = NULL;

						$MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

						# delete temp participants #
						$UserObj->deleteTempParticipants();

						# redirect #
						redirect ($ExammanagementInstanceObj->getExammanagementUrl('viewParticipants', $id), get_string('operation_successfull', 'mod_exammanagement'), null, 'success');

					} else {
						redirect ($ExammanagementInstanceObj->getExammanagementUrl('viewParticipants', $id), get_string('alteration_failed', 'mod_exammanagement'), null, 'error');
					}

				} else if($text_file){ // if participants are readed in from import file and should be saved as temporary participants

					# get matriculation numbers from text file as an array #
					$fileContentArr = explode(PHP_EOL, $text_file); // separate lines
					
					if($fileContentArr){
						$fileheader = $fileContentArr[0]."\r\n".$fileContentArr[1];
						unset($fileContentArr[0]);
						unset($fileContentArr[1]);

						$usersObjArr = array();

						foreach($fileContentArr as $key => $row){
								$potentialMatriculationnumbersArr = explode("	", $row); // from 2nd line: get all potential numbers

								if($potentialMatriculationnumbersArr){
									foreach ($potentialMatriculationnumbersArr as $key2 => $pmatrnr) { // create temp user obj

										$identifier = str_replace('"', '', $pmatrnr);
										if (preg_match('/\\d/', $identifier) !== 0 && ctype_alnum($identifier) && strlen($identifier) <= 10){ //if identifier contains numbers and only alpha numerical signs and is not to long
											$tempUserObj = new stdclass;
											$tempUserObj->plugininstanceid = $id;
											$tempUserObj->courseid = $ExammanagementInstanceObj->getCourse()->id;
											$tempUserObj->categoryid = $ExammanagementInstanceObj->moduleinstance->categoryid;
											$tempUserObj->identifier = $identifier;
											$tempUserObj->line = $key+1;

											array_push($usersObjArr, $tempUserObj);

										}
									}
								}
						}

						$UserObj->deleteTempParticipants();

						$ExammanagementInstanceObj->moduleinstance->tempimportfileheader = json_encode(strip_tags($fileheader));

						$MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

						$MoodleDBObj->InsertBulkRecordsInDB('exammanagement_temp_part', $usersObjArr);

						redirect ($ExammanagementInstanceObj->getExammanagementUrl('addParticipants',$id), get_string('operation_successfull', 'mod_exammanagement') , null, 'success');

					}
				}

			} else {
			// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
			// or on the first display of the form.

				# set data if checkboxes should be checked (setDefault in the form is much more time consuming for big amount of participants) # 
				$default_values = array('id'=>$id);

				if(isset($newMoodleParticipants)){
					foreach($newMoodleParticipants as $participant){
						$default_values['participants[mid_'.$participant->moodleuserid.']'] = true;
					}
				}

				if(isset($deletedParticipants)){
					foreach($deletedParticipants as $participant){
						if($participant->moodleuserid){
							$default_values['deletedparticipants[mid_'.$participant->moodleuserid.']'] = true;
						} else if($participant->matrnr){
							$default_values['deletedparticipants[matrnr_'.$participant->matrnr.']'] = true;
						}
					}
				}
				
				//Set default data (if any)
				$mform->set_data($default_values);

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