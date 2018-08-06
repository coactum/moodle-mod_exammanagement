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
use mod_exammanagement\ldap\ldapManager;
use PHPExcel_IOFactory;

defined('MOODLE_INTERNAL') || die();

class exammanagementForms{

	protected $id;
	protected $e;
	protected $newtaskcount;

	private function __construct($id, $e, $newtaskcount) {
		$this->id = $id;
		$this->e = $e;

		if($newtaskcount){
				$this->newtaskcount = $newtaskcount;
		}

	}

	#### singleton class ######

	public static function getInstance($id, $e, $newtaskcount = false){

		static $inst = null;
			if ($inst === null) {
				$inst = new exammanagementForms($id, $e, $newtaskcount);
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
		require_once(__DIR__.'/../ldap/ldapManager.php');
		require_once("$CFG->libdir/phpexcel/PHPExcel.php");

		$MoodleObj = Moodle::getInstance($this->id, $this->e);
		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);
		$LdapManagerObj = ldapManager::getInstance($this->id, $this->e);

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
			$excel_file = $mform->get_file_content('participantslist_excel');
			$filecontentarray = array();
			$matriculationnumbersarray = array();
			$badmatriculationnumbersarray = array();
			$moodleuseridsarray = array();
			$savedParticipantsArray = $ExammanagementInstanceObj->getSavedParticipants();

			if(!$savedParticipantsArray){
					$savedParticipantsArray = array();
			}

			if (!$excel_file && !$paul_file){
				//saveParticipants in DB
				$participants=$ExammanagementInstanceObj->filterCheckedParticipants($fromform);

				$ExammanagementInstanceObj->saveParticipants($participants, '');

				$$ExammanagementInstanceObj->clearTempParticipants();

			} else if($paul_file){

				// get matriculation numbers from paul file as an array

				$filecontentarray = explode(PHP_EOL, $paul_file); // separate lines
				$matriculationnumbersarray = explode("	", $filecontentarray[1]); // from 2nd line: get all potential numbers

				foreach ($matriculationnumbersarray as $key => $pmatrnr) { // Validate potential matrnr
						if (!$ExammanagementInstanceObj->checkIfValidMatrNr($pmatrnr)){ //if not a valid matrnr
								array_push($badmatriculationnumbersarray, $matriculationnumbersarray[$key]);
								unset($matriculationnumbersarray[$key]);
						}
				}

				// convert matriculation numbers to moodle userdis using LDAP and save them in moodleuseridsarray
				$ldapConnection = $LdapManagerObj->connect_ldap();
				 foreach($matriculationnumbersarray as $key => $matrnr){

					 if($LdapManagerObj->is_LDAP_config()){
						 	var_dump($LdapManagerObj->is_LDAP_config());
							 $ldapConnection = $LdapManagerObj->connect_ldap();
							 $moodleuserid = $LdapManagerObj->studentid2uid($ldapConnection, $matrnr);
					 } else {
						 		$moodleuserid = $LdapManagerObj->getMatriculationNumber2ImtLoginTest($matrnr);
					 }

					 if ($moodleuserid && !in_array($moodleuserid, $savedParticipantsArray) && !in_array($moodleuserid, $moodleuseridsarray)){ // dont save userid as temp_participant if userid is already saved as participant or temp_participant
							array_push($moodleuseridsarray, $moodleuserid);
							unset($matriculationnumbersarray[$key]);
					}
				 }

				 // push all remaining matriculation numbers that could not be resolved by ldap into the $matriculationnumbersarray
				 foreach($matriculationnumbersarray as $key => $matrnr){
				 		array_push($badmatriculationnumbersarray, $matriculationnumbersarray[$key]);
						unset($matriculationnumbersarray[$key]);
					}


			} else if($excel_file){
				//$PHPExcelObj = PHPExcel_IOFactory::load($excel_file);
				//var_dump($PHPExcelObj);

				var_dump(file_get_submitted_draft_itemid('participantslist_excel'));

				//$fs = get_file_storage('participantslist_excel');
				$fs = get_file_storage();

				var_dump($fs);
				//$fs->get_area_files(...);
				//moodle_url::make_pluginfile_url(...)


				//$url = $CFG->wwwroot/pluginfile.php/$contextid/$component/$filearea/arbitrary/extra/infomation.ext;
 				var_dump($url);
				// get matriculation numbers from excel file

				// convert matriculation numbers to moodle userdis using LDAP

				//remember moodle ids

			}

			// reload page with participants for final user confirmation and saving
			if(!$moodleuseridsarray){
					$moodleuseridsarray = NULL;
			}

			$ExammanagementInstanceObj->saveParticipants($moodleuseridsarray, 'tmp', $badmatriculationnumbersarray);

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

		$MoodleObj = Moodle::getInstance($this->id, $this->e);
		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);

		//Instantiate form
		$mform = new addCourseParticipantsForm(null, array('id'=>$this->id, 'e'=>$this->e));

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$MoodleObj->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

		} else if ($fromform = $mform->get_data()) {
			//In this case you process validated data. $mform->get_data() returns data posted in form.

			$participants = $ExammanagementInstanceObj->filterCheckedParticipants($fromform);

			$ExammanagementInstanceObj->saveParticipants($participants, '');

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

		public function buildInputResultsForm($matrnr){

			//include form
			require_once(__DIR__.'/inputResultsForm.php');
			require_once(__DIR__.'/../ldap/ldapManager.php');

			$MoodleObj = Moodle::getInstance($this->id, $this->e);
			$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);
			$LdapManagerObj = ldapManager::getInstance($this->id, $this->e);

			$case;
			$result;

			if ($matrnr){
				if($ExammanagementInstanceObj->checkIfValidMatrNr($matrnr)){

						if($LdapManagerObj->is_LDAP_config()){
								$ldapConnection = $LdapManagerObj->connect_ldap();
								$userid = $LdapManagerObj->studentid2uid($ldapConnection, $matrnr);
						} else {
								$userid = $LdapManagerObj->getMatriculationNumber2ImtLoginTest($matrnr);
						}

						$participantsIds = json_decode($ExammanagementInstanceObj->moduleinstance->participants);

						if(in_array($userid, $participantsIds)){
							$case = 'participant';

							$results = json_decode($ExammanagementInstanceObj->moduleinstance->results);

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

						} else {
							$case = 'noparticipant';
						}
				} else {
					$case = 'novalidmatrnr';
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

				$ExammanagementInstanceObj->saveResults($fromform);

			} else {
			  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
			  // or on the first display of the form.

				switch ($case) {
				    case 'participantwithresults':
								$mform->set_data(array('id'=>$this->id, 'matrnr'=>$matrnr, 'state[nt]'=>$resultObj->state->nt, 'state[fa]'=>$resultObj->state->fa, 'state[ill]'=>$resultObj->state->ill));

								foreach ($resultObj->points as $key=>$points){
									$mform->set_data(array('points['.$key.']'=>$points));
								}
				        break;
				    case 'participant':
								$mform->set_data(array('id'=>$this->id, 'matrnr'=>$matrnr));
				        break;
				    case 'noparticipant':
								$mform->set_data(array('id'=>$this->id));
								\core\notification::add('Ungültige Matrikelnummer', 'error');
				        break;
						case 'novalidmatrnr':
								$mform->set_data(array('id'=>$this->id));
								\core\notification::add('Keine gültige Matrikelnummer', 'error');
				        break;
						default:
								$mform->set_data(array('id'=>$this->id));
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
