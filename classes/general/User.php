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
 * class containing all user specific methods for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
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

		$MoodleDBObj = MoodleDB::getInstance();

		$allParticipantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_participants', array('plugininstanceid' => $this->id));

		if($allParticipantsArr){
			return $allParticipantsArr;

		} else {
			return false;

		}
	}

	public function getExamParticipants($participantsMode, $requestedAttributes, $sortOrder='name'){

		$MoodleDBObj = MoodleDB::getInstance();

		$allParticipants = array();

		if($participantsMode['mode'] === 'all'){
			$rs = $MoodleDBObj->getRecordset('exammanagement_participants', array('plugininstanceid'=>$this->id));
		} else if($participantsMode['mode'] === 'moodle'){
			$rs = $MoodleDBObj->getRecordset('exammanagement_participants', array('plugininstanceid' => $this->id, 'imtlogin' => NULL));
		} else if($participantsMode['mode'] === 'nonmoodle'){
			$rs = $MoodleDBObj->getRecordset('exammanagement_participants', array('plugininstanceid' => $this->id, 'moodleuserid' => NULL));
			var_dump($rs);
		} else if($participantsMode['mode'] === 'room'){
			$rs = $MoodleDBObj->getRecordset('exammanagement_participants', array('plugininstanceid' => $this->id, 'roomid' => $participantsMode['id']));
		} else if($participantsMode['mode'] === 'header'){
			$rs = $MoodleDBObj->getRecordset('exammanagement_participants', array('plugininstanceid' => $this->id, 'headerid' => $participantsMode['id']));
		} else {
			return false;
		}

        if($rs->valid()){
            foreach ($rs as $record) {

				// add login if it is requested as attribute or needed for matrnr
				if((in_array('login', $requestedAttributes) && isset($record->moodleuserid)) || (in_array('matrnr', $requestedAttributes) && isset($record->moodleuserid))){
                    $login = $MoodleDBObj->getFieldFromDB('user','username', array('id' => $record->moodleuserid));
					$record->imtlogin = $login;
				}

				// add name if it is requested as attribute or needed for sorting
				if(isset($record->moodleuserid) && (in_array('name', $requestedAttributes) || $sortOrder == 'name')){
                    $moodleUserObj = $this->getMoodleUser($record->moodleuserid);
					$record->firstname = $moodleUserObj->firstname;
					$record->lastname = $moodleUserObj->lastname;
				}

				array_push($allParticipants, $record);
			}

			$rs->close();			

			// matrnr hinzufügen
			if(in_array('matrnr', $requestedAttributes)){

				require_once(__DIR__.'/../ldap/ldapManager.php');

				$LdapManagerObj = ldapManager::getInstance($this->id, $this->e);

				$matriculationNumbers = array();
				$allLogins = array();

				if($LdapManagerObj->is_LDAP_config()){ // if ldap is configured

					foreach($allParticipants as $key => $participant){ // set logins array for ldap method
						array_push($allLogins, $participant->imtlogin);
					}

					$ldapConnection = $LdapManagerObj->connect_ldap();

					$matriculationNumbers = $LdapManagerObj->getMatriculationNumbersForLogins($ldapConnection, $allLogins);
						
				} else { // for local testing during development
					foreach($allParticipants as $participant){

						if($participant->moodleuserid !== false && $participant->moodleuserid !== NULL){
							$matrnr = $LdapManagerObj->getIMTLogin2MatriculationNumberTest($participant->moodleuserid);
						} else {
							$matrnr = $LdapManagerObj->getIMTLogin2MatriculationNumberTest(Null, $participant->imtlogin);
						}

						if($matrnr){
							$matriculationNumbers[$participant->imtlogin] = $matrnr;
						} else {
							$matriculationNumbers[$participant->imtlogin] = '-';							
						}
					}
				}

				if(!empty($matriculationNumbers)){
					foreach($allParticipants as $key => $participant){
						if(isset($participant->imtlogin) && array_key_exists($participant->imtlogin, $matriculationNumbers)){
							$participant->matrnr = $matriculationNumbers[$participant->imtlogin];
						} else {
							$participant->matrnr = '-';
						} 
					}
				} else {
					$participant->matrnr = '-';
				}
			}
			
			// sort all participant sarray
			if($sortOrder == 'name'){
				usort($allParticipants, function($a, $b){ //sort participants array by name through custom user function

					$searchArr   = array("Ä","ä","Ö","ö","Ü","ü","ß", "von ");
					$replaceArr  = array("Ae","ae","Oe","oe","Ue","ue","ss", "");
		
					if (str_replace($searchArr, $replaceArr, $a->lastname) == str_replace($searchArr, $replaceArr, $b->lastname)) { //if lastnames are even sort by first name
						return strcmp($a->firstname, $b->firstname);
					} else{
						return strcmp(str_replace($searchArr, $replaceArr, $a->lastname) , str_replace($searchArr, $replaceArr, $b->lastname)); // else sort by last name
					}
		
				});		
			} else if($sortOrder == 'matrnr'){
				usort($allParticipants, function($a, $b){ //sort participants array by matrnr through custom user function
	  
					return strnatcmp($a->matrnr, $b->matrnr); // sort by matrnr
		  
				});
			}
			
			return $allParticipants;

        } else {
			$rs->close();
			return false;
		}
	}

	public function getAllMoodleExamParticipants(){

		$MoodleDBObj = MoodleDB::getInstance();

		$allMoodleParticipantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_participants', array('plugininstanceid' => $this->id, 'imtlogin' => NULL));

		if($allMoodleParticipantsArr){
			return $allMoodleParticipantsArr;

		} else {
			return false;

		}
	}

	public function getAllNoneMoodleExamParticipants(){

		$MoodleDBObj = MoodleDB::getInstance();

		$allNoneMoodleParticipantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_participants', array('plugininstanceid' => $this->id, 'moodleuserid' => NULL));

		if($allNoneMoodleParticipantsArr){
			return $allNoneMoodleParticipantsArr;

		} else {
			return false;

		}
	}

	public function getAllExamParticipantsByRoom($roomid){

		$MoodleDBObj = MoodleDB::getInstance();

		$participantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_participants', array('plugininstanceid' => $this->id, 'roomid' => $roomid));

		if($participantsArr){
			return $participantsArr;

		} else {
			return false;

		}
	}

	public function getAllExamParticipantsByHeader($headerid){

		$MoodleDBObj = MoodleDB::getInstance();

		$participantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_participants', array('plugininstanceid' => $this->id, 'headerid' => $headerid));

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

		$participantsObj = $MoodleDBObj->getRecordFromDB('exammanagement_participants', array('plugininstanceid' => $this->id, 'moodleuserid' => $USER->id));

		if($participantsObj){
			return $participantsObj;
		} else{
			return false;
		}
	}

	public function getExamParticipantObj($userid, $userlogin = false){ // get exam participants obj

		$MoodleDBObj = MoodleDB::getInstance();

		if($userid !== false && $userid !== null){
			$participantsObj = $MoodleDBObj->getRecordFromDB('exammanagement_participants', array('plugininstanceid' => $this->id, 'moodleuserid' => $userid));
		} else if($userlogin !== false && $userlogin !== null){
			$participantsObj = $MoodleDBObj->getRecordFromDB('exammanagement_participants', array('plugininstanceid' => $this->id, 'imtlogin' => $userlogin));
		}

		if($participantsObj){
			return $participantsObj;
		} else{
			return false;
		}
	}

	#### add participants ####

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

		if($userid !== false && $MoodleDBObj->checkIfRecordExists('exammanagement_participants', array('plugininstanceid' => $this->id, 'moodleuserid' => $userid))){
			$MoodleDBObj->DeleteRecordsFromDB('exammanagement_participants', array('plugininstanceid' => $this->id, 'moodleuserid' => $userid));
		} else if($MoodleDBObj->checkIfRecordExists('exammanagement_participants', array('plugininstanceid' => $this->id, 'imtlogin' => $login))){
			$MoodleDBObj->DeleteRecordsFromDB('exammanagement_participants', array('plugininstanceid' => $this->id, 'imtlogin' => $login));
		}
	}

	public function deleteAllParticipants(){

		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);
		$MoodleDBObj = MoodleDB::getInstance();

		$ExammanagementInstanceObj->moduleinstance->importfileheaders = NULL;
		$MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

		if($MoodleDBObj->checkIfRecordExists('exammanagement_participants', array('plugininstanceid' => $this->id))){
			$MoodleDBObj->DeleteRecordsFromDB('exammanagement_participants', array('plugininstanceid' => $this->id));
		}
	}
	
	public function deleteTempParticipants(){
			$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);
			$MoodleDBObj = MoodleDB::getInstance();

			$ExammanagementInstanceObj->moduleinstance->tempimportfileheader = NULL;
			$MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

			if($MoodleDBObj->checkIfRecordExists('exammanagement_temp_part', array('plugininstanceid' => $this->id))){
				$MoodleDBObj->deleteRecordsFromDB('exammanagement_temp_part', array('plugininstanceid' => $this->id));
			} else {
				return false;
			}
	}

	#### methods to get user props

	public function getMultipleUsersMatrNr($participantsArray){

		require_once(__DIR__.'/../ldap/ldapManager.php');

		$LdapManagerObj = ldapManager::getInstance($this->id, $this->e);
		$MoodleDBObj = MoodleDB::getInstance();

		$matrNrArray = array();

		if(isset($participantsArray)){

			if($LdapManagerObj->is_LDAP_config()){ // if ldap is configured

				$loginsArray = array();

				foreach($participantsArray as $key => $participant){ // set logins array for ldap method
					if($participant->moodleuserid !== false && $participant->moodleuserid !== NULL){ // if user is moodle user get moodle username aka imtlogin and set it as login
						array_push($loginsArray, $MoodleDBObj->getFieldFromDB('user','username', array('id' => $participant->moodleuserid)));
					} else { // else set imtlogin as login
						array_push($loginsArray, $participant->imtlogin);
					}
				}

				$ldapConnection = $LdapManagerObj->connect_ldap();

				$matrNrArray = $LdapManagerObj->getMatriculationNumbersForLogins($ldapConnection, $loginsArray);
				
			} else { // for local testing during development

				foreach($participantsArray as $participant){

					if($participant->moodleuserid !== false && $participant->moodleuserid !== NULL){
						$matrNrArray[$MoodleDBObj->getFieldFromDB('user','username', array('id' => $participant->moodleuserid))] = $LdapManagerObj->getIMTLogin2MatriculationNumberTest($participant->moodleuserid);
					} else {
						$matrNrArray[$participant->imtlogin] = $LdapManagerObj->getIMTLogin2MatriculationNumberTest(NULL, $participant->imtlogin);
					}
				}
			}

			if(isset($matrNrArray) && $matrNrArray !== false){
				return $matrNrArray; // array(login/username=>matrnr)
			} else {
				return false;
			}
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
					$groupNameStr.='<strong><a href="'.$MoodleObj->getMoodleUrl('/user/index.php', $ExammanagementInstanceObj->getCourse()->id, 'group', $value2).'">'.groups_get_group_name($value2).'</a></strong> ';
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

		$NoneMoodleParticipantsEmailadressesArr = $MoodleDBObj->getFieldsetFromRecordsInDB('exammanagement_participants', 'email', $select);

		if(!empty($NoneMoodleParticipantsEmailadressesArr)){
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

		$result = false;

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

	public function calculateResultGradeWithBonus($grade, $state, $bonussteps){

		$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);

		$gradingscale = $ExammanagementInstanceObj->getGradingscale();

		if($gradingscale){
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
				if( $state !== 'normal' ) return get_string($state, "mod_exammanagement");
				if( $resultWithBonus<=1.0) return '1.0';
	
				return (str_pad (strval($resultWithBonus), 3, '.0'));
			} else {
				return '-';
			}
		} else {
			return '-';
		}
		
	}

	public function getAllParticipantsWithResults(){

		$MoodleDBObj = MoodleDB::getInstance();
	
		$participantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_participants', array('plugininstanceid' => $this->id));
	
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
	
		$participantsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_participants', array('plugininstanceid' => $this->id));
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
			
		$MoodleDBObj = MoodleDB::getInstance();

		if($potentialParticipantId){
			return $MoodleDBObj->checkIfRecordExists('exammanagement_participants', array('plugininstanceid' => $this->id, 'moodleuserid' => $potentialParticipantId));
		} else if($potentialParticipantLogin){
			$imtlogin = $potentialParticipantLogin;
			
			if($imtlogin){
				return $MoodleDBObj->checkIfRecordExists('exammanagement_participants', array('plugininstanceid' => $this->id, 'imtlogin' => $imtlogin));
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
		
		$participantsCount = $MoodleDBObj->countRecordsInDB('exammanagement_participants', $select);

		if ($participantsCount){
				return $participantsCount;
			} else {
				return false;
		}
	}

	public function getEnteredBonusCount(){

		$MoodleDBObj = MoodleDB::getInstance();

		$select = "plugininstanceid =".$this->id;
		$select .= " AND bonus IS NOT NULL";
		
		$enteredBonusCount = $MoodleDBObj->countRecordsInDB('exammanagement_participants', $select);

		if ($enteredBonusCount){
				return $enteredBonusCount;
			} else {
				return false;
		}
	}

	public function sortParticipantsArrayByName($participantsArr){ // sort participants array for all exported documents and participants overview 

		usort($participantsArr, function($a, $b){ //sort array by custom user function

			$searchArr   = array("Ä","ä","Ö","ö","Ü","ü","ß", "von ");
			$replaceArr  = array("Ae","ae","Oe","oe","Ue","ue","ss", "");

			if($a->moodleuserid){
				$aFirstname = $this->getMoodleUser($a->moodleuserid)->firstname;
				$aLastname = str_replace($searchArr, $replaceArr, $this->getMoodleUser($a->moodleuserid)->lastname);  
			} else {
				$aFirstname = $a->firstname;
				$aLastname = str_replace($searchArr, $replaceArr, $a->lastname);
			}

			if($b->moodleuserid){
				$bFirstname = $this->getMoodleUser($b->moodleuserid)->firstname;
				$bLastname = str_replace($searchArr, $replaceArr, $this->getMoodleUser($b->moodleuserid)->lastname);
			} else {
				$bFirstname = $b->firstname;
				$bLastname = str_replace($searchArr, $replaceArr, $b->lastname);
			}

			if ($aLastname == $bLastname) { //if names are even sort by first name
				return strcmp($aFirstname, $bFirstname);
			} else{
				return strcmp($aLastname, $bLastname); // else sort by last name
			}

		});

		return $participantsArr;
	}
}