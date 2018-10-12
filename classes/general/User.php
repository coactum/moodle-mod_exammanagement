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

	public function getAllExamParticipantsIds(){
		global $PAGE;

		$MoodleDBObj = MoodleDB::getInstance($this->id, $this->e);

		$allParticipantsIdsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_participants', array('plugininstance' => $this->id));

		if($allParticipantsIdsArr){
			return $allParticipantsIdsArr;

		} else {
			return false;

		}
	}

	#### import participants ####

	public function saveCourseParticipants($participantsIdsArr){

			if($participantsIdsArr != false){
				$MoodleDBObj = MoodleDB::getInstance();
				$MoodleObj = Moodle::getInstance($this->id, $this->e);

				$insert;
				$userObjArr = array();

				foreach($participantsIdsArr as $participantId){

					if($this->checkIfAlreadyParticipant($participantId) == false){
						$user = new stdClass();
						$user->plugininstance = $this->id;
						$user->moodleuserid = $participantId;
						$user->headerid = 'course';

						array_push($userObjArr, $user);
					}
				}

				$insert = $MoodleDBObj->InsertRecordInDB("exammanagement_participants", $userObjArr);

				if($insert){
					$MoodleObj->redirectToOverviewPage('beforeexam', 'Kursteilnehmer zur Prüfung hinzugefügt', 'success');
				} else {
					$MoodleObj->redirectToOverviewPage('beforeexam', 'Kursteilnehmer konnten nicht zur Prüfung hinzugefügt werden', 'error');
				}
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

	#### other methods  ####

	public function checkIfAlreadyParticipant($potentialParticipantId){
			$MoodleDBObj = MoodleDB::getInstance();

			$user = $MoodleDBObj->getRecordFromDB("exammanagement_participants", array('moodleuserid' => $potentialParticipantId));

			if($user){
				return true;
			} else {
				return false;
			}
	}

}
