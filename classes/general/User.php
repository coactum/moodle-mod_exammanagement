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

defined('MOODLE_INTERNAL') || die();

class User{

	protected $id;
	protected $e;

	private function __construct($id, $e) {
		$this->id = $id;
		$this->e = $e;

	}

	#### singleton class ######

	public static function getInstance($id, $e){

		static $inst = null;
			if ($inst === null) {
				$inst = new User($id, $e);
			}
			return $inst;

	}

	#### getting ids for multiple participants #####

	public function getAllExamParticipants(){
		global $PAGE;

		$MoodleDBObj = MoodleDB::getInstance($this->id, $this->e);

		$allParticipantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_participants', array('plugininstanceid' => $this->id));

		if($allParticipantsArr){
			return $allParticipantsArr;

		} else {
			return false;

		}
	}

	public function getAllMoodleExamParticipants(){
		global $PAGE;

		$MoodleDBObj = MoodleDB::getInstance($this->id, $this->e);

		$allMoodleParticipantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_participants', array('plugininstanceid' => $this->id, 'imtlogin' => NULL));

		if($allMoodleParticipantsArr){
			return $allMoodleParticipantsArr;

		} else {
			return false;

		}
	}

	public function getAllNoneMoodleExamParticipants(){
		global $PAGE;

		$MoodleDBObj = MoodleDB::getInstance($this->id, $this->e);

		$allNoneMoodleParticipantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_participants', array('plugininstanceid' => $this->id, 'moodleuserid' => NULL));

		if($allNoneMoodleParticipantsArr){
			return $allNoneMoodleParticipantsArr;

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

	#### import participants ####

	public function saveCourseParticipants($participantsIdsArr, $deletedParticipantsIdsArr){

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

				$insert = $MoodleDBObj->InsertBulkRecordsInDB("exammanagement_participants", $userObjArr);

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
			$delete = $MoodleDBObj->DeleteRecordsFromDB("exammanagement_participants", array('plugininstanceid' => $this->id, 'moodleuserid' => $userid));
		} else {
			$delete = $MoodleDBObj->DeleteRecordsFromDB("exammanagement_participants", array('plugininstanceid' => $this->id, 'imtlogin' => $login));
		}

		return $delete;
	}

	public function deleteAllParticipants(){

		$MoodleDBObj = MoodleDB::getInstance($this->id, $this->e);

		$delete;

		$delete = $MoodleDBObj->DeleteRecordsFromDB("exammanagement_participants", array('plugininstanceid' => $this->id));

		return $delete;
	}

	#### methods to get user props

	public function getUserMatrNr($userid, $login = false){

		require_once(__DIR__.'/../ldap/ldapManager.php');

		$LdapManagerObj = ldapManager::getInstance($this->id, $this->e);
		$MoodleDBObj = MoodleDB::getInstance($this->id, $this->e);

		if($LdapManagerObj->is_LDAP_config()){
				$ldapConnection = $LdapManagerObj->connect_ldap();

				if($userid !== false){
					$login = $MoodleDBObj->getFieldFromDB('user','username', array('id' => $userid));
				}

				$userMatrNr = $LdapManagerObj->studentid2uid($ldapConnection, $login);

		} else { // for local testing during development

			if($userid !== false){
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

	#### other methods  ####

	public function checkIfAlreadyParticipant($potentialParticipantId){
			$MoodleDBObj = MoodleDB::getInstance();

			$user = $MoodleDBObj->getRecordFromDB("exammanagement_participants", array('plugininstanceid' => $this->id, 'moodleuserid' => $potentialParticipantId));

			if($user){
				return true;
			} else {
				return false;
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
