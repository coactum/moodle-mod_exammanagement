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
 * class containing all methods for users in mod_exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\ldap\ldapManager;
use stdClass;
use core\output\notification;

defined('MOODLE_INTERNAL') || die();

class User{

	protected $id;
	protected $e;

	private function __construct($id, $e, $categoryid) {
		$this->id = $id;
		$this->e = $e;
		$this->categoryid = $categoryid;

	}

	#### singleton class ######

	public static function getInstance($id, $e, $categoryid){

		static $inst = null;
			if ($inst === null) {
				$inst = new User($id, $e, $categoryid);
			}
			return $inst;

	}

	#### getting ids for multiple participants #####

	public function getAllExamParticipants(){

		$MoodleDBObj = MoodleDB::getInstance($this->id, $this->e);

		$allParticipantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id));

		if($allParticipantsArr){
			return $allParticipantsArr;

		} else {
			return false;

		}
	}

	public function getAllMoodleExamParticipants(){

		$MoodleDBObj = MoodleDB::getInstance($this->id, $this->e);

		$allMoodleParticipantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'imtlogin' => NULL));

		if($allMoodleParticipantsArr){
			return $allMoodleParticipantsArr;

		} else {
			return false;

		}
	}

	public function getAllNoneMoodleExamParticipants(){

		$MoodleDBObj = MoodleDB::getInstance($this->id, $this->e);

		$allNoneMoodleParticipantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'moodleuserid' => NULL));

		if($allNoneMoodleParticipantsArr){
			return $allNoneMoodleParticipantsArr;

		} else {
			return false;

		}
	}

	public function getAllExamParticipantsByRoom($roomid){

		$MoodleDBObj = MoodleDB::getInstance($this->id, $this->e);

		$participantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'roomid' => $roomid));

		if($participantsArr){
			return $participantsArr;

		} else {
			return false;

		}
	}

	public function getCourseParticipantsIDs(){

			$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);

			$CourseParticipants = get_enrolled_users($ExammanagementInstanceObj->getModulecontext(), 'mod/exammanagement:takeexams');
			$CourseParticipantsIDsArray;

			foreach ($CourseParticipants as $key => $value){
				$temp = get_object_vars($value);
				$CourseParticipantsIDsArray[$key] = $temp['id'];
			}

			if($CourseParticipantsIDsArray){
					return $CourseParticipantsIDsArray;
			} else {
					return false;
			}

	}

	public function getParticipantObj(){ // get current user obj

		global $USER;

		$MoodleDBObj = MoodleDB::getInstance();

		$participantsObj = $MoodleDBObj->getRecordFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'moodleuserid' => $USER->id));

		if($participantsObj){
			return $participantsObj;
		} else{
			return false;
		}
	}

	public function getExamParticipantObj($userid, $userlogin){ // get exam participants obj

		$MoodleDBObj = MoodleDB::getInstance();

		if($userid !== false && $userid !== null){
			$participantsObj = $MoodleDBObj->getRecordFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'moodleuserid' => $userid));
		} else if($userlogin !== false && $userlogin !== null){
			$participantsObj = $MoodleDBObj->getRecordFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'imtlogin' => $userlogin));
		}

		if($participantsObj){
			return $participantsObj;
		} else{
			return false;
		}
	}
	
	public function saveParticipants($participantsIdsArr, $deletedParticipantsIdsArr){

			$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);
			$MoodleDBObj = MoodleDB::getInstance();
			$MoodleObj = Moodle::getInstance($this->id, $this->e);

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
							$user->plugininstanceid = $this->id;
							$user->courseid = $ExammanagementInstanceObj->getCourse()->id;
							$user->moodleuserid = $temp[1];
							$user->imtlogin = null;
							$user->firstname = null;
							$user->lastname = null;
							$user->headerid = $newheaderid;

							array_push($userObjArr, $user);
						} else {
							$user = new stdClass();
							$user->plugininstanceid = $this->id;
							$user->courseid = $ExammanagementInstanceObj->getCourse()->id;
							$user->moodleuserid = null;
							$user->imtlogin = $temp[1];
							$user->firstname = 'Testi';
							$user->lastname = 'Testa';
							$user->headerid = $newheaderid;

							array_push($userObjArr, $user);
						}
					}

					// insert records of new participants
					$MoodleDBObj->InsertBulkRecordsInDB('exammanagement_part_'.$this->categoryid, $userObjArr);

				}

				// delete deleted participants
				if($deletedParticipantsIdsArr){
					foreach($deletedParticipantsIdsArr as $identifier){
							$temp = explode('_', $identifier);

							if($temp[0]== 'mid'){
								$this->deleteParticipant($temp[1], false);
							} else {
								$this->deleteParticipant(false, $temp[1]);
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
				$this->deleteTempParticipants();

				//redirect
				$MoodleObj->redirectToOverviewPage('beforeexam', 'Teilnehmer wurden zur Prüfung hinzugefügt.', 'success');

			} else {
				$MoodleObj->redirectToOverviewPage('beforeexam', 'Teilnehmer konnten nicht zur Prüfung hinzugefügt werden', 'error');
			}

	}

	#### import participants ####

	public function saveCourseParticipants($participantsIdsArr, $deletedParticipantsIdsArr){

			$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);
			$MoodleDBObj = MoodleDB::getInstance();
			$MoodleObj = Moodle::getInstance($this->id, $this->e);

			if($participantsIdsArr != false || $deletedParticipantsIdsArr != false){

				$insert;
				$userObjArr = array();

				if($participantsIdsArr){
					foreach($participantsIdsArr as $participantId){

						if($this->checkIfAlreadyParticipant($participantId) == false){
							$user = new stdClass();
							$user->plugininstanceid = $this->id;
							$user->courseid = $ExammanagementInstanceObj->getCourse()->id;
							$user->moodleuserid = $participantId;
							$user->headerid = 0;

							array_push($userObjArr, $user);

						}
					}
				}

				if($deletedParticipantsIdsArr){
					foreach($deletedParticipantsIdsArr as $identifier){
							$temp = explode('_', $identifier);

							if($temp[0]== 'mid'){
								$this->deleteParticipant($temp[1], false);
							} else {
								$this->deleteParticipant(false, $temp[1]);
							}
					}
				}

				// reset state of places assignment if already set
				if($ExammanagementInstanceObj->isStateOfPlacesCorrect()){
					$ExammanagementInstanceObj->moduleinstance->stateofplaces = 'error';
					$MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
				}

				$MoodleDBObj->InsertBulkRecordsInDB('exammanagement_part_'.$this->categoryid, $userObjArr);

				$MoodleObj->redirectToOverviewPage('beforeexam', 'Kursteilnehmer wurden zur Prüfung hinzugefügt.', 'success');

			} else {
				$MoodleObj->redirectToOverviewPage('beforeexam', 'Kursteilnehmer konnten nicht zur Prüfung hinzugefügt werden', 'error');
			}
	}

	public function filterCheckedParticipants($returnObj){

			$returnObj = get_object_vars($returnObj);
			$allParicipantsArray = $returnObj["participants"];
			$participantsArr = array();

			foreach ($allParicipantsArray as $key => $value){
				if ($value == 1){
					array_push($participantsArr, $key);
				}
			}

			if ($participantsArr){
				return $participantsArr;
			} else {
				return false;
			}

	}

	public function filterCheckedDeletedParticipants($returnObj){

			$returnObj = get_object_vars($returnObj);

			$allParicipantsArray = array();

			if(isset($returnObj["deletedparticipants"])){
				$allParicipantsArray = $returnObj["deletedparticipants"];
			}

			$participantsArr = array();

			if($allParicipantsArray){
				foreach ($allParicipantsArray as $key => $value){
					if ($value == 1){
						array_push($participantsArr, $key);
					}
				}
			}


			if ($participantsArr){
				return $participantsArr;
			} else {
				return false;
			}

	}

	#### delete participants ####

	public function deleteParticipant($userid, $login = false){

		$MoodleDBObj = MoodleDB::getInstance($this->id, $this->e);

		$delete;

		if($userid !== false){
			$delete = $MoodleDBObj->DeleteRecordsFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'moodleuserid' => $userid));
		} else {
			$delete = $MoodleDBObj->DeleteRecordsFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'imtlogin' => $login));
		}

		return $delete;
	}

	public function deleteAllParticipants(){

		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);
		$MoodleDBObj = MoodleDB::getInstance($this->id, $this->e);

		$delete;

		$ExammanagementInstanceObj->moduleinstance->importfileheaders = NULL;
		$MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

		$delete = $MoodleDBObj->DeleteRecordsFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id));

		return $delete;
	}
	
	public function deleteTempParticipants(){
			$MoodleDBObj = MoodleDB::getInstance();

			$exists = $MoodleDBObj->getRecordsFromDB('exammanagement_temp_part', array('plugininstanceid' => $this->id));
			if (!$exists) {
				return false;
			}

			$MoodleDBObj->deleteRecordsFromDB('exammanagement_temp_part', array('plugininstanceid' => $this->id));
	}

	#### methods to get user props

	public function getUserMatrNr($userid, $login = false){

		require_once(__DIR__.'/../ldap/ldapManager.php');

		$LdapManagerObj = ldapManager::getInstance($this->id, $this->e);
		$MoodleDBObj = MoodleDB::getInstance($this->id, $this->e);

		if($LdapManagerObj->is_LDAP_config()){
				$ldapConnection = $LdapManagerObj->connect_ldap();

				if($userid !== false && $userid !== NULL){
					$login = $MoodleDBObj->getFieldFromDB('user','username', array('id' => $userid));
				}

				$userMatrNr = $LdapManagerObj->studentid2uid($ldapConnection, $login);

		} else { // for local testing during development

			if($userid !== false && $userid !== NULL){
				$userMatrNr = $LdapManagerObj->getIMTLogin2MatriculationNumberTest($userid);

			} else {
				$userMatrNr = $LdapManagerObj->getIMTLogin2MatriculationNumberTest(NULL, $login);
			}
		}

		if($userMatrNr){
			return $userMatrNr;
		} else {
			return '-';
		}
	}

	public function getMoodleUser($userid){

		$MoodleDBObj = MoodleDB::getInstance();

		$user = $MoodleDBObj->getRecordFromDB('user', array('id'=>$userid));

		if($user){
			return $user;
		} else {
			return false;
		}

	}

	public function getUserPicture($userid){

		global $OUTPUT;

		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);

		$user = $this->getMoodleUser($userid);
		return $OUTPUT->user_picture($user, array('courseid' => $ExammanagementInstanceObj->getCourse()->id, 'link' => true));

	}

	public function getUserProfileLink($userid){

		$MoodleObj = Moodle::getInstance($this->id, $this->e);
		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);

		$user = $this->getMoodleUser($userid);
		$profilelink = '<strong><a href="'.$MoodleObj->getMoodleUrl('/user/view.php', $user->id, 'course', $ExammanagementInstanceObj->getCourse()->id).'">'.fullname($user).'</a></strong>';

		return $profilelink;

	}

	public function getParticipantsGroupNames($userid){

		$MoodleObj = Moodle::getInstance($this->id, $this->e);
		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);

		$userGroups = groups_get_user_groups($ExammanagementInstanceObj->getCourse()->id, $userid);
		$groupNameStr = false;

		foreach ($userGroups as $key => $value){
			if ($value){
				foreach ($value as $key2 => $value2){
					$groupNameStr.='<strong><a href="'.$MoodleObj->getMoodleUrl('/user/index.php', $ExammanagementInstanceObj->getCourse()->id, 'group', $value2).'">'.groups_get_group_name($value2).'</a></strong>, ';
				}
			}
			else{
				$groupNameStr='-';
				break;
			}
		}

		return $groupNameStr;

	}

	public function participantHasResults($participantObj){

		if($participantObj->exampoints && $participantObj->examstate){
			return true;
		} else{
			return false;
		}
	}

	public function getAllParticipantsWithResults(){

		$MoodleDBObj = MoodleDB::getInstance();
	
		$participantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id));
	
		if($participantsArr){
			foreach($participantsArr as $key => $participant){
				if(!$participant->exampoints || !$participant->examstate){
					unset($participantsArr[$key]);
				}
			}
			return $participantsArr;
		} else{
			return false;
		}
	}

	#### other methods  ####

	public function checkIfAlreadyParticipant($potentialParticipantId, $potentialParticipantLogin = false){
			$MoodleDBObj = MoodleDB::getInstance();

			if($potentialParticipantId){
				$user = $MoodleDBObj->getRecordFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'moodleuserid' => $potentialParticipantId));
			} else if($potentialParticipantLogin){
				$user = $MoodleDBObj->getRecordFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'imtlogin' => $potentialParticipantLogin));
			}

			if($user){
				return true;
			} else {
				return false;
			}
	}

	public function checkIfValidMatrNr($mnr) {
		if (!preg_match("/^\d+$/", $mnr)) {
			return false;
		}
		
		$first = substr($mnr, 0, 1);
		$prf   = substr($mnr, strlen($mnr)-1, 1);
		$mod   = $mnr % 11;
		
		if ($first==7 && strlen($mnr)==7) {
			return true;
		} else {
			return (($first==3 || $first==6) /*&& ($mod==0 ? TRUE : ($mod==1 && $prf==0))*/);
		}
	}

	public function getParticipantsCount(){

		$participantsArr = $this->getAllExamParticipants();
		$participantsCount = false;

		if($participantsArr){
			$participantsCount = count($participantsArr);
		}

		if ($participantsCount){
				return $participantsCount;
			} else {
				return false;
		}
	}

}
