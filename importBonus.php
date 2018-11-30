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
 * Imports bonus points for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\importBonusForm;
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

$MoodleObj = Moodle::getInstance($id, $e);
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
//$LdapManagerObj = ldapManager::getInstance($id, $e);	
//$MoodleDBObj = MoodleDB::getInstance($id, $e);
$UserObj = User::getInstance($id, $e, $ExammanagementInstanceObj->moduleinstance->categoryid);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

	$MoodleObj->setPage('importBonus');
	
	if(!$ExammanagementInstanceObj->isStateOfPlacesCorrect() || $ExammanagementInstanceObj->isStateOfPlacesError()){
		$MoodleObj->redirectToOverviewPage('aftercorrection', 'Noch keine Sitzplätze zugewiesen. Bonuspunkteimport noch nicht möglich', 'error');
	} else if (!$UserObj->getParticipantsCount()) {
		$MoodleObj->redirectToOverviewPage('aftercorrection', 'Noch keine Teilnehmer ausgewählt. Bonuspunkteimport noch nicht möglich', 'error');
	  }

    $MoodleObj->outputPageHeader();

    //Instantiate form
    $mform = new importBonusForm(null, array('id'=>$id, 'e'=>$e));

    //Form processing and displaying is done here
    if ($mform->is_cancelled()) {
        //Handle form cancel operation, if cancel button is present on form

        $MoodleObj->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

    } else if ($fromform = $mform->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.

        // retrieve Files from form
    	$excel_file = $mform->get_file_content('bonuspoints_list');

        if ($excel_file){
            /* //saveParticipants in DB
            
            $participantsIdsArr = $UserObj->filterCheckedParticipants($fromform);
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

					foreach($participantsIdsArr as $identifier){

						$temp = explode('_', $identifier);

						if($temp[0]== 'mid'){
							$user = new stdClass();
							$user->plugininstanceid = $id;
							$user->courseid = $ExammanagementInstanceObj->getCourse()->id;
							$user->moodleuserid = $temp[1];
							$user->imtlogin = null;
							$user->firstname = null;
							$user->lastname = null;
							$user->email = null;
							$user->headerid = $newheaderid;

							array_push($userObjArr, $user);
						} else {

							$user = new stdClass();
							$user->plugininstanceid = $id;
							$user->courseid = $ExammanagementInstanceObj->getCourse()->id;
							$user->moodleuserid = null;

							if($LdapManagerObj->is_LDAP_config()){
								$ldapConnection = $LdapManagerObj->connect_ldap();

								$user->imtlogin = ''.$LdapManagerObj->studentid2uid($ldapConnection, $temp[1]);

								$ldapUser = $LdapManagerObj->get_ldap_attribute($ldapConnection, array( "sn", "givenName", "upbMailPreferredAddress" ), $user->imtlogin );
								if($ldapUser){
									$user->firstname = $ldapUser['givenName'];
									$user->lastname = $ldapUser['sn'];
									$user->email = ''.$ldapUser['upbMailPreferredAddress'];
								} else {
									$user->firstname = NULL;
									$user->lastname = NULL;
									$user->email = NULL;
								}				
							} else { // for local testing during development

									$user->imtlogin = ''.$LdapManagerObj->getMatriculationNumber2ImtLoginNoneMoodleTest($temp[1]);
									$user->firstname = 'Mister';
									$user->lastname = 'Testerin';
									$user->email = 'Test@Testi.test';
							}

							$user->headerid = $newheaderid;

							array_push($userObjArr, $user);
						}
					}

					// insert records of new participants
					$MoodleDBObj->InsertBulkRecordsInDB('exammanagement_part_'.$UserObj->categoryid, $userObjArr);

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

				// reset state of places assignment if already set
				if($ExammanagementInstanceObj->isStateOfPlacesCorrect()){
					$ExammanagementInstanceObj->moduleinstance->stateofplaces = 'error';
				}

				$MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

				//delete temp participants
				$UserObj->deleteTempParticipants();

				//redirect
				$MoodleObj->redirectToOverviewPage('beforeexam', 'Teilnehmer wurden zur Prüfung hinzugefügt.', 'success');

			} else {
				$MoodleObj->redirectToOverviewPage('beforeexam', 'Teilnehmer konnten nicht zur Prüfung hinzugefügt werden', 'error'); */
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
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
