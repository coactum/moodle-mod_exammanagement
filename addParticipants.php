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
 * Allows teacher to add participants from paul file to mod_exammanagement.
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
$MoodleDBObj = MoodleDB::getInstance($id, $e);
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

			//Instantiate form
			$mform = new addParticipantsForm(null, array('id'=>$id, 'e'=>$e));

			//Form processing and displaying is done here
			if ($mform->is_cancelled()) {
				//Handle form cancel operation, if cancel button is present on form

				redirect ($ExammanagementInstanceObj->getExammanagementUrl('viewParticipants', $ExammanagementInstanceObj->getCm()->id), get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

			} else if ($fromform = $mform->get_data()) {
			//In this case you process validated data. $mform->get_data() returns data posted in form.

				// retrieve Files from form
				$paul_file = $mform->get_file_content('participantslist_paul');

				if (!$paul_file){

					$time = microtime();
        var_dump($time);
        
					//saveParticipants in DB
					
					$participantsIdsArr = $UserObj->filterCheckedParticipants($fromform);
					$noneMoodleParticipantsMatrNrArr = array();
					$deletedParticipantsIdsArr = $UserObj->filterCheckedDeletedParticipants($fromform);
									
					if($participantsIdsArr != false || $deletedParticipantsIdsArr != false){

						$tempfileheader = json_decode($ExammanagementInstanceObj->moduleinstance->tempimportfileheader);
						$savedFileHeadersArr = json_decode($ExammanagementInstanceObj->moduleinstance->importfileheaders);
						$newheaderid;

						// save new file header
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

						// add new participants
						if($participantsIdsArr){ 
							$userObjArr = array();

							foreach($participantsIdsArr as $key => $identifier){

								$temp = explode('_', $identifier);

								if($temp[0]== 'mid'){
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

									for ($i = 0; $i <= 200; $i++){ // testing
										array_push($userObjArr, $user);
									} 

									unset($participantsIdsArr[$key]);

								} else {
									array_push($noneMoodleParticipantsMatrNrArr, $temp[1]);
								}
							}

							$noneMoodleParticipantsArr = array();

							if($LdapManagerObj->is_LDAP_config()){
								$ldapConnection = $LdapManagerObj->connect_ldap();

								$noneMoodleParticipantsArr = $LdapManagerObj->getLDAPAttributesForMatrNrs($ldapConnection, $NoneMoodleParticipantsMatrNrArr, array( "sn", "givenName", "upbMailPreferredAddress" ));
											
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

							foreach($participantsIdsArr as $key => $identifier){
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

							for ($i = 0; $i <= 200; $i++){ // testing
								array_push($userObjArr, $user);
							}

							// insert records of new participants
							$MoodleDBObj->InsertBulkRecordsInDB('exammanagement_participants', $userObjArr);

						}

						// delete deleted participants
						if($deletedParticipantsIdsArr){
							foreach($deletedParticipantsIdsArr as $identifier){
									$temp = explode('_', $identifier);

									if($temp[0]== 'mid'){
										$UserObj->deleteParticipant($temp[1], false);
									} else {
										$UserObj->deleteParticipant(false, $temp[1]);
									}
							}
						}

						// delete temp file header and update saved file headers
						$ExammanagementInstanceObj->moduleinstance->tempimportfileheader = NULL;

						$MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

						//delete temp participants
						$UserObj->deleteTempParticipants();

						//redirect
						redirect ($ExammanagementInstanceObj->getExammanagementUrl('viewParticipants', $id), get_string('operation_successfull', 'mod_exammanagement'), null, 'success');

					} else {
						redirect ($ExammanagementInstanceObj->getExammanagementUrl('viewParticipants', $id), get_string('alteration_failed', 'mod_exammanagement'), null, 'error');
					}

					$time2 = microtime();
        var_dump($time2 - $time . 'milisekunden');


				} else if($paul_file){

					// get matriculation numbers from paul file as an array
					$fileContentArr = explode(PHP_EOL, $paul_file); // separate lines
					
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

			//Set default data (if any)
			//$mform->set_data(array('participants'=>$this->getCourseParticipantsIDs(), 'id'=>$this->id));
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