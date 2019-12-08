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
 * Allows teacher to import bonus points for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\importBonusForm;
use mod_exammanagement\ldap\ldapManager;
use PHPExcel_IOFactory;
use PHPExcel_Reader_IReadFilter;
use stdclass;
use core\output\notification;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once("$CFG->libdir/phpexcel/PHPExcel.php");

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$bonusstepcount  = optional_param('bonusstepcount', 0, PARAM_INT);

$MoodleObj = Moodle::getInstance($id, $e);
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();
$UserObj = User::getInstance($id, $e);
$LdapManagerObj = LdapManager::getInstance($id, $e);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

	if($ExammanagementInstanceObj->isExamDataDeleted()){
        $MoodleObj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
	} else {

		if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

			$MoodleObj->setPage('importBonus');
			
			if(!$ExammanagementInstanceObj->allPlacesAssigned()){
				$MoodleObj->redirectToOverviewPage('aftercorrection', get_string('not_all_places_assigned', 'mod_exammanagement'), 'error');
			} else if (!$UserObj->getParticipantsCount()) {
				$MoodleObj->redirectToOverviewPage('aftercorrection', get_string('no_participants_added', 'mod_exammanagement'), 'error');
			}

			$MoodleObj->outputPageHeader();

			//Instantiate form
			$mform = new importBonusForm(null, array('id'=>$id, 'e'=>$e, 'bonusstepcount'=>$bonusstepcount));

			//Form processing and displaying is done here
			if ($mform->is_cancelled()) {
				//Handle form cancel operation, if cancel button is present on form

				$MoodleObj->redirectToOverviewPage('aftercorrection', get_string('operation_canceled', 'mod_exammanagement'), 'warning');

			} else if ($fromform = $mform->get_data()) {
			//In this case you process validated data. $mform->get_data() returns data posted in form.	

				if ($fromform->bonuspoints_list){

					if((isset($fromform->bonussteppoints[2]) && $fromform->bonussteppoints[1]>=$fromform->bonussteppoints[2]) || (isset($fromform->bonussteppoints[3]) && $fromform->bonussteppoints[2]>=$fromform->bonussteppoints[3])){
						redirect($ExammanagementInstanceObj->getExammanagementUrl('importBonus', $id), get_string('points_bonussteps_invalid', 'mod_exammanagement'), null, notification::NOTIFY_ERROR);
					}
				
					// retrieve Files from form
					$file = $mform->get_file_content('bonuspoints_list');
					$filename = $mform->get_new_filename('bonuspoints_list');
					
					$tempfile = tempnam(sys_get_temp_dir(), 'bonuslist_');
					rename($tempfile, $tempfile .= $filename);

					$handle = fopen($tempfile, "w");
					fwrite($handle, $file);

					$ExcelReaderWrapper = PHPExcel_IOFactory::createReaderForFile($tempfile);
					$ExcelReaderWrapper->setReadDataOnly(true);

					function excelColumnRange($lower, $upper) {
						++$upper;
						for ($i = $lower; $i !== $upper; ++$i) {
							yield $i;
						}
					}

					class MyReadFilter implements PHPExcel_Reader_IReadFilter { // not working in the way intended (read only cells with relevant data)

						private $columns = array();

						public function __construct($toColumn) {
							
							foreach (excelColumnRange('A', $toColumn) as $value) {
								array_push($this->columns, $value);
							}

						}

						public function readCell($column, $row, $worksheetName = '') {

							if(in_array($column, $this->columns)){

								return true;
							}
							
							return false;
						}
					}

					$ExcelReaderWrapper->setReadFilter( new MyReadFilter($fromform->pointsfield) );
					
					$ExcelReaderWrapper->setReadDataOnly(true);

					$readerObj = $ExcelReaderWrapper->load($tempfile);

					$worksheetObj = $readerObj->getActiveSheet();
					$highestRow = $worksheetObj->getHighestRow();

					$dataArr = array();
					$matrNrsArr = array();
					$loginsArray = array();
					$linesArr = array();

					$potentialUserIDsArr = $worksheetObj->rangeToArray($fromform->idfield.'2:'.$fromform->idfield.$highestRow);
					$pointsArr = $worksheetObj->rangeToArray($fromform->pointsfield.'2:'.$fromform->pointsfield.$highestRow);

					foreach($potentialUserIDsArr as $key => $potentialIdentifier){ // unset all identifiers that are no valid matriculation numbers or mail adresses

						if ($potentialIdentifier[0] && filter_var($potentialIdentifier[0], FILTER_VALIDATE_EMAIL)){ // if identifier is mail adress (import of moodle grades export)
							$dataArr[$key] = array('matrnr' => false, 'login' => false, 'moodleuserid' => $MoodleDBObj->getFieldFromDB('user', 'id', array('email'=>$potentialIdentifier[0])), 'points' =>$pointsArr[$key][0]);
						} else if($potentialIdentifier[0] && $UserObj->checkIfValidMatrNr($potentialIdentifier[0])){ // if identifier is matrnr (individual import)
							$matrNrsArr[$key] = $potentialIdentifier[0];
							array_push($linesArr, $key);
						}
					}
					
					if(!empty($matrNrsArr)){
						if($LdapManagerObj->isLDAPenabled()){
							if($LdapManagerObj->isLDAPconfigured()){
								$ldapConnection = $LdapManagerObj->connect_ldap();

								if($ldapConnection){
									$loginsArray = $LdapManagerObj->getLDAPAttributesForMatrNrs($ldapConnection, $matrNrsArr, 'usernames_and_matriculationnumbers', $linesArr);
								} else {
									\core\notification::error(get_string('ldaoconnectionfailed', 'mod_exammanagement'), 'error');
								}
								
							} else {
								\core\notification::error(get_string('ldapnotconfigured', 'mod_exammanagement'). ' ' .get_string('importmatrnrnotpossible', 'mod_exammanagement'), 'error');
							}
	
						} else {
							foreach($matrNrsArr as $key => $matrnr){
								$loginsArray[$key] = array('login' => $LdapManagerObj->getMatriculationNumber2ImtLoginNoneMoodleTest($matrnr), 'moodleuserid' => false);
							}
						}
						// } else {
						// 	\core\notification::error(get_string('ldapnotenabled', 'mod_exammanagement'). ' ' .get_string('importmatrnrnotpossible', 'mod_exammanagement'), 'error');
						// }
					}

					if(!empty($loginsArray)){
						foreach($loginsArray as $key => $data){

							$moodleuserid = $MoodleDBObj->getFieldFromDB('user', 'id', array('username'=>$data['login']));
	
							if($moodleuserid){
								$dataArr[$key] = array('login' => false, 'moodleuserid' => $moodleuserid, 'points' =>$pointsArr[$key][0]);
							} else {
								$dataArr[$key] = array('login' => $data['login'], 'moodleuserid' => false, 'points' =>$pointsArr[$key][0]);
							}
	
						}
					}

					foreach($dataArr as $line => $data){
						$participantObj = false;

						if($data['moodleuserid'] && $UserObj->checkIfAlreadyParticipant($data['moodleuserid'])){
							$participantObj = $UserObj->getExamParticipantObj($data['moodleuserid']);
						} else if($data['login'] && $UserObj->checkIfAlreadyParticipant(false, $data['login'])){
							$participantObj = $UserObj->getExamParticipantObj(false, $data['login']);
						}

						if($participantObj){

							$participantObj->bonus = 0;
							
							if(isset($data['points']) && $data['points']){

								foreach($fromform->bonussteppoints as $step => $points){
									
									if(floatval($data['points']) >= $points){
										$participantObj->bonus = $step; // change to detect bonus step
									} else {
										break;
									}
								}
							}

							$update = $MoodleDBObj->UpdateRecordInDB('exammanagement_participants', $participantObj);
						
						}
					}

					fclose($handle);
					unlink($tempfile);	

					if($update){
						$MoodleObj->redirectToOverviewPage('aftercorrection', get_string('operation_successfull', 'mod_exammanagement'), 'success');
					} else {
						$MoodleObj->redirectToOverviewPage('aftercorrection', get_string('alteration_failed', 'mod_exammanagement'), 'error');
					}
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