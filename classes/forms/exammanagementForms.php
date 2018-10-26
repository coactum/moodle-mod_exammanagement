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
 * class containing all build forms methods for moodle
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;

use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\Moodle;
use mod_exammanagement\general\MoodleDB;
use mod_exammanagement\general\User;
use mod_exammanagement\ldap\ldapManager;
use PHPExcel_IOFactory;
use stdclass;

defined('MOODLE_INTERNAL') || die();

class exammanagementForms{

	protected $id;
	protected $e;
	protected $newtaskcount;
	protected $dtp;
	protected $test;

	private function __construct($id, $e, $newtaskcount, $dtp, $test) {
		$this->id = $id;
		$this->e = $e;

		if($newtaskcount){
				$this->newtaskcount = $newtaskcount;
		}

		if($dtp){
			$this->dtp = $dtp;
		}

		if($test){
			$this->test = $test;
		}
	}

	#### singleton class ######

	public static function getInstance($id, $e, $newtaskcount = false, $dtp = false, $test = false){

		static $inst = null;
			if ($inst === null) {
				$inst = new exammanagementForms($id, $e, $newtaskcount, $dtp, $test);
			}
			return $inst;

	}

	#### build Forms Methods #####

	public function buildChooseRoomsForm(){

		//include form
		require_once(__DIR__.'/chooseRoomsForm.php');

		$MoodleObj = Moodle::getInstance($this->id, $this->e);
		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);

		//Instantiate form
		$mform = new chooseRoomsForm(null, array('id'=>$this->id, 'e'=>$this->e));

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$MoodleObj->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

		} else if ($fromform = $mform->get_data()) {
			//In this case you process validated data. $mform->get_data() returns data posted in form.

			$rooms=$ExammanagementInstanceObj->filterCheckedRooms($fromform);

			$ExammanagementInstanceObj->saveRooms($rooms);

		} else {
			// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
			// or on the first display of the form.

			//Set default data (if any)
			$mform->set_data(array('id'=>$this->id));

			//displays the form
			$mform->display();
		}

	}

	public function buildAddDefaultRoomsForm(){

		//include form
		require_once(__DIR__.'/addDefaultRoomsForm.php');

		$MoodleObj = Moodle::getInstance($this->id, $this->e);
		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);

		//Instantiate form
		$mform = new addDefaultRoomsForm(null, array('id'=>$this->id, 'e'=>$this->e));

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$MoodleObj->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

		} else if ($fromform = $mform->get_data()) {
			//In this case you process validated data. $mform->get_data() returns data posted in form.

			// retrieve file from form
			$rooms_file = $mform->get_file_content('defaultrooms_list');

			if($rooms_file){
					$ExammanagementInstanceObj->saveDefaultRooms($rooms_file);
			}

		} else {
			// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
			// or on the first display of the form.

			//Set default data (if any)
			$mform->set_data(array('id'=>$this->id));

			//displays the form
			$mform->display();
		}

	}

	public function buildDateTimeForm(){

		//include form
		require_once(__DIR__.'/dateTimeForm.php');

		$MoodleObj = Moodle::getInstance($this->id, $this->e);
		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);

		//Instantiate form
		$mform = new dateTimeForm();

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$MoodleObj->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

		} else if ($fromform = $mform->get_data()) {
		  //In this case you process validated data. $mform->get_data() returns data posted in form.

			if (!empty($fromform->resetdatetime)) { // not working
    		$ExammanagementInstanceObj->resetDateTime();
  		} else {
				$ExammanagementInstanceObj->saveDateTime($fromform->examtime);
			}

		} else {
		  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
		  // or on the first display of the form.

		  //Set default data (if any)
		  $mform->set_data(array('examtime'=>$ExammanagementInstanceObj->getExamtime(), 'id'=>$this->id));

		  //displays the form
		  $mform->display();
		}

	}

	public function buildAddParticipantsForm(){

		//include form
		require_once(__DIR__.'/addParticipantsForm.php');
		require_once("$CFG->libdir/phpexcel/PHPExcel.php");

		$MoodleObj = Moodle::getInstance($this->id, $this->e);
		$MoodleDBObj = MoodleDB::getInstance($this->id, $this->e);
		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);
		$UserObj = User::getInstance($this->id, $this->e, $ExammanagementInstanceObj->moduleinstance->categoryid);

		if($this->dtp){
			$UserObj->deleteTempParticipants();
		}

		//Instantiate form
		$mform = new addParticipantsForm(null, array('id'=>$this->id, 'e'=>$this->e));

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form

			$MoodleObj->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

		} else if ($fromform = $mform->get_data()) {
		  //In this case you process validated data. $mform->get_data() returns data posted in form.

			// retrieve Files from form
			$paul_file = $mform->get_file_content('participantslist_paul');
			$excel_file = false;
			//$excel_file = $mform->get_file_content('participantslist_excel');

			if (!$excel_file && !$paul_file){
				//saveParticipants in DB
				
				$participants = $UserObj->filterCheckedParticipants($fromform);
				$deletedParticipants = $UserObj->filterCheckedDeletedParticipants($fromform);
								
				$UserObj->saveParticipants($participants, $deletedParticipants);

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
										$tempUserObj->plugininstanceid = $this->id;
										$tempUserObj->identifier = $identifier;
										$tempUserObj->line = $key+1;
		
										array_push($usersObjArr, $tempUserObj);

									}
								}
							}
					}

					$ExammanagementInstanceObj->moduleinstance->tempimportfileheader = json_encode($fileheader);

					$MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

					$UserObj->deleteTempParticipants();
					$MoodleDBObj->InsertBulkRecordsInDB('exammanagement_temp_part', $usersObjArr);

					redirect ($ExammanagementInstanceObj->getExammanagementUrl('addParticipants',$this->id), null , null, null);
	
				}
			}

		} else {
		  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
		  // or on the first display of the form.

		  //Set default data (if any)
		  //$mform->set_data(array('participants'=>$this->getCourseParticipantsIDs(), 'id'=>$this->id));
		  $mform->set_data(array('id'=>$this->id));

		  //displays the form
		  $mform->display();
		}

	}

	public function buildAddCourseParticipantsForm(){

		//include form
		require_once(__DIR__.'/addCourseParticipantsForm.php');

		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);
		$MoodleObj = Moodle::getInstance($this->id, $this->e);
		$UserObj = User::getInstance($this->id, $this->e, $ExammanagementInstanceObj->moduleinstance->categoryid);

		//Instantiate form
		$mform = new addCourseParticipantsForm(null, array('id'=>$this->id, 'e'=>$this->e));

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$MoodleObj->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

		} else if ($fromform = $mform->get_data()) {
			//In this case you process validated data. $mform->get_data() returns data posted in form.

			$participants = $UserObj->filterCheckedParticipants($fromform);
			$deletedParticipants = $UserObj->filterCheckedDeletedParticipants($fromform);

			$UserObj->saveCourseParticipants($participants, $deletedParticipants);

		} else {
			// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
			// or on the first display of the form.

			//Set default data (if any)
			//$mform->set_data(array('participants'=>$this->getCourseParticipantsIDs(), 'id'=>$this->id));
			$mform->set_data(array('id'=>$this->id));

			//displays the form
			$mform->display();
		}

	}

	public function buildConfigureTasksForm(){

		//include form
		require_once(__DIR__.'/configureTasksForm.php');

		$MoodleObj = Moodle::getInstance($this->id, $this->e);
		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);

		//Instantiate form
		$mform = new configureTasksForm(null, array('id'=>$this->id, 'e'=>$this->e, 'newtaskcount'=>$this->newtaskcount));

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$MoodleObj->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

		} else if ($fromform = $mform->get_data()) {
		  //In this case you process validated data. $mform->get_data() returns data posted in form.

			$ExammanagementInstanceObj->saveTasks($fromform);

		} else {
		  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
		  // or on the first display of the form.

		  //Set default data (if any)
		  $mform->set_data(array('id'=>$this->id));

		  //displays the form
		  $mform->display();
		}

	}

	public function buildTextfieldForm(){

		//include form
		require_once(__DIR__.'/textfieldForm.php');

		$MoodleObj = Moodle::getInstance($this->id, $this->e);
		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);

		//Instantiate form
		$mform = new textfieldForm();

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$MoodleObj->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

		} else if ($fromform = $mform->get_data()) {
			//In this case you process validated data. $mform->get_data() returns data posted in form.

			$ExammanagementInstanceObj->saveTextfield($fromform);

		} else {
			// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
			// or on the first display of the form.

			//Set default data (if any)

			$text = $ExammanagementInstanceObj->getTextFromTextfield();
			$format = $ExammanagementInstanceObj->getFormatFromTextfield();
			if ($text && $format){
				$mform->set_data(array('textfield'=>['text' => $text, 'format' => $format], 'id'=>$this->id));
			} else {
				$mform->set_data(array('id'=>$this->id));
			}


			//displays the form
			$mform->display();
		}

	}

	public function buildGroupmessagesForm(){

		//include form
		require_once(__DIR__.'/groupmessagesForm.php');

		$MoodleObj = Moodle::getInstance($this->id, $this->e);
		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);

		//Instantiate form
		$mform = new groupmessagesForm(null, array('id'=>$this->id, 'e'=>$this->e));

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$MoodleObj->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

		} else if ($fromform = $mform->get_data()) {
		  //In this case you process validated data. $mform->get_data() returns data posted in form.

		  $ExammanagementInstanceObj->sendGroupMessage($fromform->groupmessages_subject, $fromform->groupmessages_content);
		  $MoodleObj->redirectToOverviewPage('beforeexam', 'Nachricht verschickt', 'success');

		} else {
		  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
		  // or on the first display of the form.

		  //Set default data (if any)
		  $mform->set_data(array('id'=>$this->id));

		  //displays the form
		  $mform->display();
		}

	}

	public function buildConfigureGradingscaleForm(){

		//include form
		require_once(__DIR__.'/configureGradingscaleForm.php');

		$MoodleObj = Moodle::getInstance($this->id, $this->e);
		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);

		//Instantiate form
		$mform = new configureGradingscaleForm(null, array('id'=>$this->id, 'e'=>$this->e));

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$MoodleObj->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

		} else if ($fromform = $mform->get_data()) {
		  //In this case you process validated data. $mform->get_data() returns data posted in form.

			$ExammanagementInstanceObj->saveGradingscale($fromform);

		} else {
		  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
		  // or on the first display of the form.

		  //Set default data (if any)
		  //$mform->set_data(array('participants'=>$this->getCourseParticipantsIDs(), 'id'=>$this->id));
		  $mform->set_data(array('id'=>$this->id));

		  //displays the form
		  $mform->display();
		}
	}

		public function buildInputResultsForm($input){

			//include form
			require_once(__DIR__.'/inputResultsForm.php');
			require_once(__DIR__.'/../ldap/ldapManager.php');

			$MoodleObj = Moodle::getInstance($this->id, $this->e);
			$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);
			$LdapManagerObj = ldapManager::getInstance($this->id, $this->e);

			$matrnr = false;
			$case='';
			$result;
			$firstname = '';
			$lastname = '';

			if ($input){

				//check if input is valid barcode and then convert barcoe to matrnr
				$inputLength = strlen($input);

				if ($inputLength == 8){ //input is barcode
					$input = "00000" . $input;

					$checksum = $ExammanagementInstanceObj->buildChecksumExamLabels(substr($input, 0, 12));

					if ($checksum == substr($input, -1)){ //if checksum is correct
						$matrnr = substr($input, 5, -1); //extract matrnr from barcode
					} else {
						$matrnr = $input;
					}

				} else { //input is no barcode
						$matrnr = $input;

				}

				if($ExammanagementInstanceObj->checkIfValidMatrNr($matrnr)){

						if($LdapManagerObj->is_LDAP_config()){
								$ldapConnection = $LdapManagerObj->connect_ldap();

								$MoodleDBObj = MoodleDB::getInstance($this->id, $this->e);

								$username = $LdapManagerObj->studentid2uid($ldapConnection, $matrnr);

								if($username){
									$userid = $MoodleDBObj->getFieldFromDB('user','id', array('username' => $username));
								}

						} else {
								$userid = $LdapManagerObj->getMatriculationNumber2ImtLoginTest($matrnr);
						}

						$participantsIds = json_decode($ExammanagementInstanceObj->moduleinstance->participants);

						if(in_array($userid, $participantsIds)){
							$case = 'participant';

							$results = json_decode($ExammanagementInstanceObj->moduleinstance->results);

							if($results){
								foreach($results as $key => $resultObj){
									if ($resultObj->uid == $userid){
											$case = 'participantwithresults';

											$result = $resultObj;
											$moodleUser = $ExammanagementInstanceObj->getMoodleUser($userid);

											$firstname = $moodleUser->firstname;
											$lastname = $moodleUser->lastname;
											break;
									}
								}
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

			//Instantiate Textfield_form
			$mform = new inputResultsForm(null, array('id'=>$this->id, 'e'=>$this->e, 'matrnr'=>$matrnr, 'firstname'=>$firstname, 'lastname'=>$lastname));

			//Form processing and displaying is done here
			if ($mform->is_cancelled()) {
				//Handle form cancel operation, if cancel button is present on form
				$MoodleObj->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

			} else if ($fromform = $mform->get_data()) {
			  //In this case you process validated data. $mform->get_data() returns data posted in form.

				$matrval = $fromform->matrval;

				if ($matrval){
						redirect ('inputResults.php?id='.$this->id.'&matrnr='.$fromform->matrnr, null, null, null);
				} else {
						$ExammanagementInstanceObj->saveResults($fromform);
				}

			} else {
			  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
			  // or on the first display of the form.

				switch ($case) {
				    case 'participantwithresults':
								$mform->set_data(array('id'=>$this->id, 'matrval'=>0, 'matrnr'=>$matrnr, 'state[nt]'=>$resultObj->state->nt, 'state[fa]'=>$resultObj->state->fa, 'state[ill]'=>$resultObj->state->ill));

								foreach ($resultObj->points as $key=>$points){
									$mform->set_data(array('points['.$key.']'=>$points));
								}
				        break;
				    case 'participant':
								$mform->set_data(array('id'=>$this->id, 'matrval'=>0, 'matrnr'=>$matrnr));
				        break;
				    case 'noparticipant':
								$mform->set_data(array('id'=>$this->id, 'matrval'=>1,));
								\core\notification::add('Ungültige Matrikelnummer', 'error');
				        break;
						case 'novalidmatrnr':
								$mform->set_data(array('id'=>$this->id, 'matrval'=>1,));
								\core\notification::add('Keine gültige Matrikelnummer', 'error');
				        break;
						default:
								$mform->set_data(array('id'=>$this->id, 'matrval'=>1,));
								break;
				}

			  //displays the form
			  $mform->display();
			}
	}

	public function buildShowResultsForm(){

		//include form
		require_once(__DIR__.'/showResultsForm.php');

		$MoodleObj = Moodle::getInstance($this->id, $this->e);
		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);

		//Instantiate Form
		$mform = new showResultsForm(null, array('id'=>$this->id, 'e'=>$this->e));

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$MoodleObj->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

		} else if ($fromform = $mform->get_data()) {
			//In this case you process validated data. $mform->get_data() returns data posted in form.

		} else {
			// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
			// or on the first display of the form.

			$mform->set_data(array('id'=>$this->id));

			//displays the form
			$mform->display();
		}
}

}
