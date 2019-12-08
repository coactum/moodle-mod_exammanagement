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
use core\notification;

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

	#### get array with all requested exam participants #####

	public function getExamParticipants($participantsMode, $requestedAttributes, $sortOrder='name'){

		$MoodleDBObj = MoodleDB::getInstance();

		$allParticipants = array();

		if($participantsMode['mode'] === 'all'){
			$rs = $MoodleDBObj->getRecordset('exammanagement_participants', array('plugininstanceid'=>$this->id));
		} else if($participantsMode['mode'] === 'moodle'){
			$rs = $MoodleDBObj->getRecordset('exammanagement_participants', array('plugininstanceid' => $this->id, 'imtlogin' => NULL));
		} else if($participantsMode['mode'] === 'nonmoodle'){
			$rs = $MoodleDBObj->getRecordset('exammanagement_participants', array('plugininstanceid' => $this->id, 'moodleuserid' => NULL));
		} else if($participantsMode['mode'] === 'room'){
			$rs = $MoodleDBObj->getRecordset('exammanagement_participants', array('plugininstanceid' => $this->id, 'roomid' => $participantsMode['id']));
		} else if($participantsMode['mode'] === 'header'){
			$rs = $MoodleDBObj->getRecordset('exammanagement_participants', array('plugininstanceid' => $this->id, 'headerid' => $participantsMode['id']));
		} else if($participantsMode['mode'] === 'resultsafterexamreview'){
			$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);

			$examReviewTime = $ExammanagementInstanceObj->getExamReviewTime();

			$select = "plugininstanceid =".$this->id;
			$select .= " AND exampoints IS NOT NULL";
			$select .= " AND examstate IS NOT NULL";
			$select .= " AND timeresultsentered IS NOT NULL";
			$select .= " AND timeresultsentered >=" . $examReviewTime;

			$rs = $MoodleDBObj->getRecordsetSelect('exammanagement_participants', $select);

		} else {
			return false;
		}

        if($rs->valid()){

			if(in_array('profile', $requestedAttributes) || in_array('groups', $requestedAttributes)){
				$ExammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);
				$MoodleObj = Moodle::getInstance($this->id, $this->e);

				$courseid = $ExammanagementInstanceObj->getCourse()->id;
			}

            foreach ($rs as $record) {

				// add login if it is requested as attribute or needed for matrnr
				if((in_array('login', $requestedAttributes) && isset($record->moodleuserid)) || (in_array('matrnr', $requestedAttributes) && isset($record->moodleuserid))){
                    $login = $MoodleDBObj->getFieldFromDB('user','username', array('id' => $record->moodleuserid));
					$record->imtlogin = $login;
				}

				// add name if it is requested as attribute or needed for sorting or profile
				if(isset($record->moodleuserid) && (in_array('name', $requestedAttributes) || in_array('profile', $requestedAttributes) || $sortOrder == 'name' )){
					$moodleUser = $this->getMoodleUser($record->moodleuserid);

					if($moodleUser){
						$record->firstname = $moodleUser->firstname;
						$record->lastname = $moodleUser->lastname;
						if(in_array('profile', $requestedAttributes)){ 			// add profile if it is requested
							global $OUTPUT;
							
							$image = $OUTPUT->user_picture($moodleUser, array('courseid' => $courseid, 'link' => true));
							
							$link = '<strong><a href="'.$MoodleObj->getMoodleUrl('/user/view.php', $moodleUser->id, 'course', $courseid).'">'.$record->firstname.' '.$record->lastname.'</a></strong>';

							$record->profile = $image.' '.$link;
							
						}

						if(in_array('groups', $requestedAttributes)){ 			// add group if it is requested
							$userGroups = groups_get_user_groups($ExammanagementInstanceObj->getCourse()->id, $record->moodleuserid);
							$groupnames = false;

							foreach ($userGroups as $key => $value){
								if ($value){
									foreach ($value as $key2 => $value2){
										if(!$groupnames){
											$groupnames = '<strong><a href="'.$MoodleObj->getMoodleUrl('/group/index.php', $courseid, 'group', $value2).'">'.groups_get_group_name($value2).'</a></strong>';
										} else {
											$groupnames .= ', <strong><a href="'.$MoodleObj->getMoodleUrl('/group/index.php', $courseid, 'group', $value2).'">'.groups_get_group_name($value2).'</a></strong> ';
										}
									}
								} else{
									$groupnames = '-';
									break;
								}
							}
							$record->groups = $groupnames;
						}
					} else {
						$record->firstname = get_string('deleted_user', 'mod_exammanagement',['systemname' => $ExammanagementInstanceObj->getMoodleSystemName()]);
						$record->lastname = get_string('deleted_user', 'mod_exammanagement',['systemname' => $ExammanagementInstanceObj->getMoodleSystemName()]);
						if(in_array('profile', $requestedAttributes)){
							$record->profile = get_string('deleted_user', 'mod_exammanagement',['systemname' => $ExammanagementInstanceObj->getMoodleSystemName()]);
						}
						if(in_array('groups', $requestedAttributes)){
							$record->groups = '-';
						}
					}
				}

				array_push($allParticipants, $record);
			}

			$rs->close();			

			// add matrnr if it is requested
			if(in_array('matrnr', $requestedAttributes)){

				require_once(__DIR__.'/../ldap/ldapManager.php');

				$LdapManagerObj = ldapManager::getInstance();

				$matriculationNumbers = array();
				$allLogins = array();

				foreach($allParticipants as $key => $participant){ // set logins array for ldap method
					array_push($allLogins, $participant->imtlogin);
				}
	
				$matriculationNumbers = $LdapManagerObj->getMatriculationNumbersForLogins($allLogins); // retrieve matrnrs for all logins from ldap

				if(!empty($matriculationNumbers)){
					foreach($allParticipants as $key => $participant){
						if(isset($participant->imtlogin) && $participant->imtlogin && array_key_exists($participant->imtlogin, $matriculationNumbers)){
							$participant->matrnr = $matriculationNumbers[$participant->imtlogin];
						} else {
							$participant->matrnr = '-';
						} 
					}
				} else {
					foreach($allParticipants as $key => $participant){
						$participant->matrnr = '-';
					}
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

	#### get IDs of all participants enrolled in the course #####

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

	#### get single exam participant #####

	public function getExamParticipantObj($userid, $userlogin = false){

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

	#### get single moodle user #####

	public function getMoodleUser($userid){

		$MoodleDBObj = MoodleDB::getInstance();

		$user = $MoodleDBObj->getRecordFromDB('user', array('id'=>$userid));

		if($user){
			return $user;
		} else {
			return false;
		}

	}

	#### get mail adresses of all nonmoodle users #####

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

	#### filter checked participants from form ####

	public function filterCheckedParticipants($returnObj){

			$returnObj = get_object_vars($returnObj);

			$allParicipantsArray = array();

			if(isset($returnObj["participants"])){
				$allParicipantsArray = $returnObj["participants"];
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

	#### results ####

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

	#### checks  ####

	public function checkIfAlreadyParticipant($potentialParticipantId, $potentialParticipantLogin = false){
			
		$MoodleDBObj = MoodleDB::getInstance();

		if($potentialParticipantId){
			return $MoodleDBObj->checkIfRecordExists('exammanagement_participants', array('plugininstanceid' => $this->id, 'moodleuserid' => $potentialParticipantId));
		} else if($potentialParticipantLogin){
			return $MoodleDBObj->checkIfRecordExists('exammanagement_participants', array('plugininstanceid' => $this->id, 'imtlogin' => $potentialParticipantLogin));
		}
	}

	public function checkIfValidMatrNr($mnr) {
		if (!preg_match("/^\d+$/", $mnr)) {
			return false;
		}
		
		$first = substr($mnr, 0, 1);
		
		if ($first==7 && strlen($mnr)==7) {
			return true;
		} else {
			return (($first==3 || $first==6) && strlen($mnr)==7);
		}
	}

	#### counts ####

	public function getParticipantsCount($mode = 'all', $id = false){

		$MoodleDBObj = MoodleDB::getInstance();

		$select = "plugininstanceid =".$this->id;

		if($mode == 'moodle'){
			$select .= " AND moodleuserid IS NOT NULL";
		} else if($mode =='nonmoodle'){
			$select .= " AND moodleuserid IS NULL";
		} else if($mode =='room' && $id){
			$select .= " AND roomid = '" . $id . "'";
		}
		
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

	public function getEnteredResultsCount($timestamp = false){

		$MoodleDBObj = MoodleDB::getInstance();
		
		$select = "plugininstanceid =".$this->id;
		$select .= " AND exampoints IS NOT NULL";
		$select .= " AND examstate IS NOT NULL";

		if($timestamp){
			$select .= " AND timeresultsentered IS NOT NULL";
			$select .= " AND timeresultsentered >=" . $timestamp;
		}
		
		$enteredResultsCount = $MoodleDBObj->countRecordsInDB('exammanagement_participants', $select);

		if($enteredResultsCount){
			return $enteredResultsCount;
		} else {
			return false;
		}

	}
}