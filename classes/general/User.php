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

		$MoodleDBObj = MoodleDB::getInstance();

		$allParticipantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id));

		if($allParticipantsArr){
			return $allParticipantsArr;

		} else {
			return false;

		}
	}

	public function getAllMoodleExamParticipants(){

		$MoodleDBObj = MoodleDB::getInstance();

		$allMoodleParticipantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'imtlogin' => NULL));

		if($allMoodleParticipantsArr){
			return $allMoodleParticipantsArr;

		} else {
			return false;

		}
	}

	public function getAllNoneMoodleExamParticipants(){

		$MoodleDBObj = MoodleDB::getInstance();

		$allNoneMoodleParticipantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'moodleuserid' => NULL));

		if($allNoneMoodleParticipantsArr){
			return $allNoneMoodleParticipantsArr;

		} else {
			return false;

		}
	}

	public function getAllExamParticipantsByRoom($roomid){

		$MoodleDBObj = MoodleDB::getInstance();

		$participantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'roomid' => $roomid));

		if($participantsArr){
			return $participantsArr;

		} else {
			return false;

		}
	}

	public function getAllExamParticipantsByHeader($headerid){

		$MoodleDBObj = MoodleDB::getInstance();

		$participantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'headerid' => $headerid));

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

	public function getExamParticipantObj($userid, $userlogin = false){ // get exam participants obj

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

	#### import participants ####

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

		$MoodleDBObj = MoodleDB::getInstance();

		if($userid !== false && $MoodleDBObj->checkIfRecordExists('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'moodleuserid' => $userid))){
			$MoodleDBObj->DeleteRecordsFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'moodleuserid' => $userid));
		} else if($MoodleDBObj->checkIfRecordExists('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'imtlogin' => $login))){
			$MoodleDBObj->DeleteRecordsFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'imtlogin' => $login));
		}
	}

	public function deleteAllParticipants(){

		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);
		$MoodleDBObj = MoodleDB::getInstance();

		$ExammanagementInstanceObj->moduleinstance->importfileheaders = NULL;
		$MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

		if($MoodleDBObj->checkIfRecordExists('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id))){
			$MoodleDBObj->DeleteRecordsFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id));
		}
	}
	
	public function deleteTempParticipants(){
			$MoodleDBObj = MoodleDB::getInstance();

			if($MoodleDBObj->checkIfRecordExists('exammanagement_temp_part', array('plugininstanceid' => $this->id))){
				$MoodleDBObj->deleteRecordsFromDB('exammanagement_temp_part', array('plugininstanceid' => $this->id));
			} else {
				return false;
			}
	}

	#### methods to get user props

	public function getUserMatrNr($userid, $login = false){

		require_once(__DIR__.'/../ldap/ldapManager.php');

		$LdapManagerObj = ldapManager::getInstance($this->id, $this->e);
		$MoodleDBObj = MoodleDB::getInstance();

		if($LdapManagerObj->is_LDAP_config()){
				$ldapConnection = $LdapManagerObj->connect_ldap();

				if($userid !== false && $userid !== NULL){
					$login = $MoodleDBObj->getFieldFromDB('user','username', array('id' => $userid));
				}

				$userMatrNr = $LdapManagerObj->uid2studentid($ldapConnection, $login);

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

	public function getNoneMoodleParticipantsEmailadresses(){

		$MoodleDBObj = MoodleDB::getInstance();

		$select = "plugininstanceid =".$this->id;
		$select .= " AND moodleuserid IS NULL";

		$NoneMoodleParticipantsEmailadressesArr = $MoodleDBObj->getFieldsetFromRecordsInDB('exammanagement_part_'.$this->categoryid, 'email', $select);

		if($NoneMoodleParticipantsEmailadressesArr){
			return $NoneMoodleParticipantsEmailadressesArr;

		} else {
			return false;

		}
	}

	public function participantHasResults($participantObj){

		if($participantObj->exampoints && $participantObj->examstate){
			return true;
		} else{
			return false;
		}
	}

	public function getExamState($participantObj){

		$stateArr = json_decode($participantObj->examstate);

		if($stateArr){
			foreach($stateArr as $key => $value){
				if($key == 'nt' && $value == "1"){
						return 'nt';
				} else if ($key == 'fa' && $value == "1"){
						return 'fa';
				} else if ($key == 'ill' && $value == "1"){
						return 'ill';
				}
			}
		}

		return 'normal';
	}

	public function calculateTotalPoints($participantObj){
		$points = 0;

		$pointsArr = json_decode($participantObj->exampoints);

		if($pointsArr != Null){
			foreach($pointsArr as $key => $taskpoints){
				$points += floatval($taskpoints);
			}
			return floatval($points);
		} else {
			return '-';
		}
	}

	public function calculateResultGrade($participantObj){

		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);

		$gradingscale = $ExammanagementInstanceObj->getGradingscale();

		$state = $this->getExamState($participantObj);

		$totalpoints = $this->calculateTotalPoints($participantObj);
		$lastpoints = 0;

		if($totalpoints === '-'){
		    $result = '-';
		} else if($state == "nt" || $state == "fa" || $state == "ill"){
			$result = get_string($state, "mod_exammanagement");
		} else if($totalpoints <= 0){
			$result = 5;
		} else if($totalpoints && $gradingscale){
			foreach($gradingscale as $key => $step){

				if($key == '1.0' && $totalpoints >= floatval($step)){
						$result = $key;
				} else if($totalpoints < $lastpoints && $totalpoints >= floatval($step)){
						$result = $key;
				} else if($key == '4.0' && $totalpoints < floatval($step)){
						$result = 5;
				}

				$lastpoints = floatval($step);

			}
		}

		return $result;
	}

	public function calculateResultGradeWithBonus($grade, $bonussteps){

		switch ($bonussteps){
			case '1':
				$bonus = 0.3;
				break;
			case '2':
				$bonus = 0.7;
				break;
			case '3':
				$bonus = 1.0;
				break;
			default:
				$bonus = 0;
				break;
		}

		if(isset($grade) && $grade !== "-"){
			$resultWithBonus = $grade-$bonus;

			$test = round($resultWithBonus-floor($resultWithBonus),1);

			if( 0.4==$test ) {$resultWithBonus=$resultWithBonus-0.1;}
			if( 0.6==$test ) {$resultWithBonus=$resultWithBonus+0.1;}

			if($bonus == 0) return $grade;
			if( $grade == 5.0 ) return 5.0;
			if( $grade == 'NT' ) return 'NT';
			if( $grade == 'FA' ) return 'FA';
			if( $grade == 'ILL' ) return 'ILL';
			if( $resultWithBonus<=1.0) return 1.0;

			return ($resultWithBonus);
		} else {
			return '-';
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

	public function getAllParticipantsWithResultsAfterExamReview(){

		$ExammanagementInstanceObj = ExammanagementInstance::getInstance($this->id, $this->e);
		$MoodleDBObj = MoodleDB::getInstance();
	
		$participantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id));
		$examReviewTime = $ExammanagementInstanceObj->getExamReviewTime();

		if($participantsArr && $examReviewTime){
			foreach($participantsArr as $key => $participant){
				if(!$participant->exampoints || !$participant->examstate || !$participant->timeresultsentered || $participant->timeresultsentered == null || intval($participant->timeresultsentered) < $examReviewTime){
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
			
		require_once(__DIR__.'/../ldap/ldapManager.php');

		$LdapManagerObj = ldapManager::getInstance($this->id, $this->e);
		$MoodleDBObj = MoodleDB::getInstance();

		if($potentialParticipantId){
			return $MoodleDBObj->checkIfRecordExists('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'moodleuserid' => $potentialParticipantId));
		} else if($potentialParticipantLogin){
			$imtlogin = $potentialParticipantLogin;
			
			if($imtlogin){
				return $MoodleDBObj->checkIfRecordExists('exammanagement_part_'.$this->categoryid, array('plugininstanceid' => $this->id, 'imtlogin' => $imtlogin));
			}
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

		$MoodleDBObj = MoodleDB::getInstance();

		$select = "plugininstanceid =".$this->id;
		
		$participantsCount = $MoodleDBObj->countRecordsInDB('exammanagement_part_'.$this->categoryid, $select);

		if ($participantsCount){
				return $participantsCount;
			} else {
				return false;
		}
	}

	public function getEnteredBonusCount(){

		$MoodleDBObj = MoodleDB::getInstance();

		$select = "plugininstanceid =".$this->id;
		$select .= " AND bonuspoints IS NOT NULL";
		
		$enteredBonusCount = $MoodleDBObj->countRecordsInDB('exammanagement_part_'.$this->categoryid, $select);

		if ($enteredBonusCount){
				return $enteredBonusCount;
			} else {
				return false;
		}
	}

}
