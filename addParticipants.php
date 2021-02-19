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
use stdclass;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$dtp  = optional_param('dtp', 0, PARAM_INT);

$MoodleObj = Moodle::getInstance($id, $e);
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$LdapManagerObj = ldapManager::getInstance();
$MoodleDBObj = MoodleDB::getInstance();
$UserObj = User::getInstance($id, $e, $ExammanagementInstanceObj->getCm()->instance);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

	if($ExammanagementInstanceObj->isExamDataDeleted()){
        $MoodleObj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
	} else if(!$LdapManagerObj->isLDAPenabled()){
		$MoodleObj->redirectToOverviewPage('beforeexam', get_string('importmatrnrnotpossible', 'mod_exammanagement') . ' ' . get_string('ldapnotenabled', 'mod_exammanagement'), 'error');
	} else if(!$LdapManagerObj->isLDAPconfigured()){
		$MoodleObj->redirectToOverviewPage('beforeexam', get_string('importmatrnrnotpossible', 'mod_exammanagement') . ' ' . get_string('ldapnotconfigured', 'mod_exammanagement'), 'error');
	} else {

		if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

			$MoodleObj->setPage('addParticipants');
			$MoodleObj->outputPageHeader();

			if($dtp){
				$UserObj->deleteTempParticipants();
			}

			# define participants for form #
			$tempParticipants = $MoodleDBObj->getRecordsFromDB('exammanagement_temp_part', array('exammanagement' => $ExammanagementInstanceObj->getCm()->instance)); // get all participants that are already readed in and saved as temnp participants

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

				// temp participants from stored in db that should get ldap attributes

				foreach($tempParticipants as $key => $participant){ // construct helper arrays needed for ldap method
					$allMatriculationNumbers[$key] = $participant->identifier;
					$allLines[$key] = $participant->line;
				}

				$users = $LdapManagerObj->getLDAPAttributesForMatrNrs($allMatriculationNumbers, 'usernames_and_matriculationnumbers', $allLines); //get data for all remaining matriculation numbers from ldap

				if($users){
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
						} else if (!$courseParticipantsIDs || !in_array($data['moodleuserid'], $courseParticipantsIDs)){ 	// if user is not in course
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

						} else if($participant->moodleuserid === null && $participant->login && !in_array($participant->login, $tempIDs)){  // moodle participant that is not readed in again and should therefore be deleted
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

				//var_dump($fromform);
				//var_dump($mform);

				// retrieve Files from form
				//$text_file = $mform->get_file_content('participantslist_text');
				//$lists = $mform->get_file_content('participantslists');

				$draftid = file_get_submitted_draft_itemid('participantslists');
				// var_dump($draftid);
				// var_dump('<br>');
				// var_dump('<br>');

				// var_dump(file_get_all_files_in_draftarea(file_get_submitted_draft_itemid('participantslists')));
				// var_dump('<br>');
				// var_dump('<br>');

				//var_dump($mform->get_file_content('Teilnehmerliste_Entwicklungsumgebung.txt'));

				// $file = reset($files);

				//var_dump($file->get_content());

				// $fs = get_file_storage();
				// $fs->get_area_files($context->id, 'user', 'draft', $draftid, 'id DESC', false);

				//var_dump(get_new_filename('participantslists'));
				// if (empty($entry->id)) {
				// 	$entry = new stdClass;
				// 	$entry->id = null;
				// }

				// $draftitemid = file_get_submitted_draft_itemid('participantslists');

				//  file_prepare_draft_area($draftitemid, $context->id, 'mod_exammanagement', 'attachment', $entry->id,
				//  						array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 50));

				// $fileoptions = array(
				// 	'maxbytes' => 0,
				// 	'maxfiles' => '1',
				// 	'subdirs' => 0,
				// 	'context' => context_system::instance()
				// );

				// $filesdataprepare = file_prepare_standard_filemanager($data, 'participantslists',
				// 	$fileoptions, context_system::instance(), 'mod_exammanagement', 'participantslists', 0);
				// var_dump($filesdataprepare);

				// $filesdatapostupdate = file_postupdate_standard_filemanager($data, 'participantslists',
				// 	$fileoptions, context_system::instance(), 'mod_exammanagement', 'participantslists', 0);
				// var_dump($filesdatapostupdate);

				// $entry->attachments = $draftitemid;

				// $mform->set_data($entry);



				if (!$draftid){ // if no import file and exam participants should be saved in db

					# get checked userids from form #
					$participantsIdsArr = $UserObj->filterCheckedParticipants($fromform);
					$noneMoodleParticipantsMatrNrArr = array();
					$deletedParticipantsIdsArr = $UserObj->filterCheckedDeletedParticipants($fromform);
					$tempParticipants = $MoodleDBObj->getRecordsFromDB('exammanagement_temp_part', array('exammanagement' => $ExammanagementInstanceObj->getCm()->instance)); // get all participants that are already readed in and saved as temnp participants
					var_dump($tempParticipants);

					if($participantsIdsArr != false || $deletedParticipantsIdsArr != false){

						# get headers and temp file header #
						$tempfileheaders = json_decode($ExammanagementInstanceObj->moduleinstance->tempimportfileheader);
						$savedFileHeadersArr = json_decode($ExammanagementInstanceObj->moduleinstance->importfileheaders);

						$savedHeadersCount = count($savedFileHeadersArr);

						if(!$savedFileHeadersArr && $tempfileheader){ // if there are no saved headers by now
							# save new file header #
							$savedFileHeadersArr = array();
							array_push($savedFileHeadersArr, $tempfileheader);
							$convertTempHeaders = false;

						} else if($savedFileHeadersArr && $tempfileheader){
							$convertTempHeaders = array();

							foreach($tempfileheaders as $tempheaderkey => $tempfileheader){

								$saved = false;

								foreach($savedFileHeadersArr as $savedheaderkey => $header){ // if new header is already saved
									if($tempfileheader == $header){
										$saved = $savedheaderkey;
									}
								}

								if($saved){ // if new header is already saved
									$convertTempHeaders[$tempheaderkey] = $saved;
								} else {
									array_push($savedFileHeadersArr, $tempfileheader);

								}
							}
						}

						//$ExammanagementInstanceObj->moduleinstance->importfileheaders = json_encode($savedFileHeadersArr);

						# add new participants #
						if($participantsIdsArr){
							$userObjArr = array();

							foreach($participantsIdsArr as $key => $tempidentifier){

								$tempheaderid = explode('-', $tempidentifier)[1];

								$identifier = explode('_', $explode('-', $tempidentifier)[0]);

								if($identifier[0]== 'mid'){ // if participant is moodle user
									$user = new stdClass();
									$user->exammanagement = $ExammanagementInstanceObj->getCm()->instance;
									$user->courseid = $ExammanagementInstanceObj->getCourse()->id;
									$user->categoryid = $ExammanagementInstanceObj->moduleinstance->categoryid;
									$user->moodleuserid = $identifier[1];
									$user->login = null;
									$user->firstname = null;
									$user->lastname = null;
									$user->email = null;

									# get temp header id #
									// $new_array = array_filter($tempParticipants, function($tempparticipant){
									// 	return $tempparticipant[''];
									// });

									// if(in_array($user->headerid, $convertTempHeaders)){
									// 	$user->headerid = $convertTempHeaders[$user->headerid];
									// } else {
									// 	$user->headerid = $tempheaderid + $savedHeadersCount;
									// }

									$user->plugininstanceid = 0; // for deprecated old version db version, should be removed for ms 3

									array_push($userObjArr, $user);

									unset($participantsIdsArr[$key]);

								} else {
									array_push($noneMoodleParticipantsMatrNrArr, $identifier[1]);
								}
							}

							if(!empty($noneMoodleParticipantsMatrNrArr)){
								$noneMoodleParticipantsArr = $LdapManagerObj->getLDAPAttributesForMatrNrs($noneMoodleParticipantsMatrNrArr, 'all_attributes');

								foreach($participantsIdsArr as $key => $identifier){ // now only contains participants that have no moodle account
									$temp = explode('_', $identifier);

									$matrnr = $identifier[1];

									$user = new stdClass();
									$user->exammanagement = $ExammanagementInstanceObj->getCm()->instance;
									$user->courseid = $ExammanagementInstanceObj->getCourse()->id;
									$user->categoryid = $ExammanagementInstanceObj->moduleinstance->categoryid;
									$user->moodleuserid = null;

									$login = $noneMoodleParticipantsArr[$matrnr]['login'];
									if($login){
										$user->login = $login;
									} else {
										$user->login = null;
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

									// if(in_array($user->headerid, $convertTempHeaders)){
									// 	$user->headerid = $convertTempHeaders[$user->headerid];
									// } else {
									// 	$user->headerid = $tempheaderid + $savedHeadersCount;
									// }

									$user->plugininstanceid = 0; // for deprecated old version db version, should be removed for ms 3

									array_push($userObjArr, $user);
								}
							}

							## insert records of new participants ##
							//$MoodleDBObj->InsertBulkRecordsInDB('exammanagement_participants', $userObjArr);

						}

						# delete participants that should be deleted #

						// if($deletedParticipantsIdsArr){
						// 	foreach($deletedParticipantsIdsArr as $identifier){
						// 		$temp = explode('_', $identifier);

						// 		if($temp[0]== 'mid'){ // delete moodle participant
						// 			$UserObj->deleteParticipant($temp[1], false);
						// 		} else { // delete participant without moodle account

						// 			$userlogin = false;

						// 			$userlogin = $LdapManagerObj->getLoginForMatrNr($temp[1], 'importmatrnrnotpossible');

						// 			if($userlogin){
						// 				$UserObj->deleteParticipant(false, $userlogin);
						// 			}
						// 		}
						// 	}
						// }

						# delete temp file header and update saved file headers #
						//$ExammanagementInstanceObj->moduleinstance->tempimportfileheader = NULL;

						//$MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

						# delete temp participants #
						//$UserObj->deleteTempParticipants();

						# redirect #
						//redirect ($ExammanagementInstanceObj->getExammanagementUrl('viewParticipants', $id), get_string('operation_successfull', 'mod_exammanagement'), null, 'success');

					} else {
						redirect ($ExammanagementInstanceObj->getExammanagementUrl('viewParticipants', $id), get_string('alteration_failed', 'mod_exammanagement'), null, 'error');
					}

				} else if($draftid){ // if participants are readed in from import file and should be saved as temporary participants

					$UserObj->deleteTempParticipants();

					$fs = get_file_storage();
					$context = \context_user::instance($USER->id);
					$files = $fs->get_area_files($context->id, 'user', 'draft', $draftid, 'id DESC', false);

					$tempfileheaders = array();
					$usersObjArr = array();

					//var_dump($files);
					// var_dump('<br>');
					// var_dump('<br>');

					foreach($files as $file){
						// var_dump($file->get_content());

						// var_dump('<br>');
						// var_dump('<br>');

						# get matriculation numbers from text file as an array #
						$fileContentArr = explode(PHP_EOL, $file->get_content()); // separate lines

						if($fileContentArr){
							$fileheader = $fileContentArr[0]."\r\n".$fileContentArr[1];

							unset($fileContentArr[0]);
							unset($fileContentArr[1]);

							foreach($fileContentArr as $key => $row){
									$potentialMatriculationnumbersArr = explode("	", $row); // from 2nd line: get all potential numbers

									if($potentialMatriculationnumbersArr){
										foreach ($potentialMatriculationnumbersArr as $key2 => $pmatrnr) { // create temp user obj

											$identifier = str_replace('"', '', $pmatrnr);
											if (preg_match('/\\d/', $identifier) !== 0 && ctype_alnum($identifier) && strlen($identifier) <= 10){ //if identifier contains numbers and only alpha numerical signs and is not to long
												$tempUserObj = new stdclass;
												$tempUserObj->exammanagement = $ExammanagementInstanceObj->getCm()->instance;
												$tempUserObj->courseid = $ExammanagementInstanceObj->getCourse()->id;
												$tempUserObj->categoryid = $ExammanagementInstanceObj->moduleinstance->categoryid;
												$tempUserObj->identifier = $identifier;
												$tempUserObj->line = $key+1;
												$tempUserObj->plugininstanceid = 0; // for deprecated old version db version, should be removed for ms 3
												$tempUserObj->headerid = count($tempfileheaders) + 1;

												array_push($usersObjArr, $tempUserObj);

											}
										}
									}
							}

							$fileheader = strip_tags($fileheader);

							if(mb_detect_encoding($fileheader, mb_detect_order(), true) !== "UTF-8"){
								$fileheader = utf8_encode($fileheader);
							}

							array_push($tempfileheaders, $fileheader);

						}
					}

					$ExammanagementInstanceObj->moduleinstance->tempimportfileheader = json_encode($tempfileheaders);

					$MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

					$MoodleDBObj->InsertBulkRecordsInDB('exammanagement_temp_part', $usersObjArr);

					redirect ($ExammanagementInstanceObj->getExammanagementUrl('addParticipants',$id), get_string('operation_successfull', 'mod_exammanagement') , null, 'success');
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