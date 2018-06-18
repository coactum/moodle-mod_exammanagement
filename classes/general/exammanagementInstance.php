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
 * class containing all general functions for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

defined('MOODLE_INTERNAL') || die();
use mod_exammanagement\event\course_module_viewed;
use mod_exammanagement\ldap\ldapManager;
use context_module;
use stdClass;
use core\message\message;
use core\output\notification;

class exammanagementInstance{

	protected $id;
	protected $e;
	protected $cm;
	protected $course;
	public $moduleinstance;
	protected $modulecontext;

	private function __construct($id, $e) {

		$MoodleDBObj = MoodleDB::getInstance();

		$this->id=$id;
		$this->e=$e;

        if ($id) {
				$this->cm             = get_coursemodule_from_id('exammanagement', $id, 0, false, MUST_EXIST);
				$this->course = get_course($this->cm->course);
				$this->moduleinstance = $MoodleDBObj->getRecordFromDB('exammanagement', array('id' => $this->cm->instance), '*', MUST_EXIST);
			} else if ($e) {
				$this->moduleinstance = $MoodleDBObj->getRecordFromDB('exammanagement', array('id' => $e), '*', MUST_EXIST);
				$this->course = get_course($this->moduleinstance->course);
				$this->cm             = get_coursemodule_from_instance('exammanagement', $this->moduleinstance->id, $this->course->id, false, MUST_EXIST);
			} else {
				print_error(get_string('missingidandcmid', 'mod_exammanagement'));
			}

			require_login($this->course, true, $this->cm);

			$this->modulecontext = context_module::instance($this->cm->id);

    }

	#### singleton class ######

	public static function getInstance($id, $e){

		static $inst = null;
			if ($inst === null) {
				$inst = new exammanagementInstance($id, $e);
			}
			return $inst;

	}

	#### getter for object properties

	public function getModuleinstance(){

			return $this->moduleinstance;

	}

	public function getCourse(){

			return $this->course;

	}

	public function getCm(){

			return $this->cm;

	}

	public function getModulecontext(){

			return $this->modulecontext;

	}

	#### universal functions for exammanagement ####

 	public function getExammanagementUrl($component, $id){

		$MoodleObj = Moodle::getInstance($this->id, $this->e);

 		$url= $MoodleObj->getMoodleUrl('/mod/exammanagement/'.$component.'.php', $id);

 		return $url;
 	}

	public function ConcatHelptextStr($langstr){

		$helptextstr= '';
		$helptextstr.= '<div class="panel panel-info helptextpanel collapse">';
		$helptextstr.= '<div class="panel-heading">';
		$helptextstr.= '<h4>'.get_string('helptext_str', 'mod_exammanagement').'</h4>';
		$helptextstr.= '</div>';
		$helptextstr.= '<div class="panel-body">';
		$helptextstr.= '<p>'.get_string('helptext_'.$langstr, 'mod_exammanagement').'</p>';
		$helptextstr.= '</div>';
		$helptextstr.= '<div class="panel-footer">';
		$helptextstr.= '	<p class="mb-0">'.get_string('helptext_link', 'mod_exammanagement').'<a href="https://hilfe.uni-paderborn.de/PANDA" class="alert-link" target="_blank">https://hilfe.uni-paderborn.de/PANDA</a></p>';
		$helptextstr.= '</div>';
		$helptextstr.= '</div>';

		$helptextstr.= <<< EOF
<script>
toogleHelptextPanel = function(){
	$('.helptextpanel').slideToggle("slow");
	$('.helptextpanel-icon').toggle();
};
</script>
EOF;

		return $helptextstr;

	}

	#### overview ####

 	public function getExamtime(){		//get examtime (for form)
		if ($this->moduleinstance->examtime){
				return $this->moduleinstance->examtime;
			} else {
				return false;
			}
	}

	public function getHrExamtimeTemplate() {	//convert examtime to human readable format for template
		$examtime = $this->getExamtime();
		if($examtime){
			$hrexamtimetemplate = date('d.m.Y', $examtime).' um '.date('H:i', $examtime);
			return $hrexamtimetemplate;
		} else {
			return false;
		}
 	}

	public function getHrExamtime() {	//convert examtime to human readable format for template
		$examtime = $this->getExamtime();
		if($examtime){
			$hrexamtime = date('d.m.Y', $examtime).' '.date('H:i', $examtime);
			return $hrexamtime;
		} else {
			return false;
		}
 	}

 	public function getTextfieldObject(){

 		$textfield= $this->moduleinstance->textfield;

		$textfield =json_decode($textfield);

		return $textfield;
	}

 	public function getTextFromTextfield(){

 		$textfield = $this->getTextfieldObject('exammanagement','textfield', array('id' => $this->cm->instance));
		if ($textfield){
				$text = $textfield->text;
				return $text;
			} else {
				return false;
			}
	}

	public function getFormatFromTextfield(){

 		$textfield = $this->getTextfieldObject('exammanagement','textfield', array('id' => $this->cm->instance));
		if ($textfield){
				$format = $textfield->format;
				return $format;
			} else {
				return false;
			}
	}

	public function getShortenedTextfield(){
		$textfield = format_string($this->getTextFromTextfield());

		if ($textfield && strlen($textfield)>49){
				$shtextfield = substr($textfield, 0, 49).' ...';
				return $shtextfield;
			} elseif($textfield) {
				return $textfield;
			} else{
				return false;
			}
	}

	public function getParticipantsCount(){
		$participants = $this->moduleinstance->participants;
		if ($participants){
				$temp = json_decode($participants);
				$participantsCount = count($temp);
				return $participantsCount;
			} else {
				return false;
		}
	}

	public function getRoomsCount(){
		$rooms = $this->moduleinstance->rooms;
		if ($rooms){
				$roomsArr = json_decode($rooms);
				$roomsCount = count($roomsArr);
				return $roomsCount;
			} else {
				return false;
		}
	}

	public function getChoosenRoomNames(){
		$rooms = $this->moduleinstance->rooms;
		$roomNames = array();

		if ($rooms){
				$roomsArray = json_decode($rooms);

				foreach ($roomsArray as $key => $value){
					$temp = $this->getRoomObj($value);

					if ($temp){
						array_push($roomNames, $temp->name);
						}

					}

				asort($roomNames);

				$roomsStr = implode(", ", $roomNames);

				return $roomsStr;

			} else {
				return false;
		}
	}

	public function isStateOfPlacesCorrect(){

		$StateOfPlaces = $this->getStateOfPlaces();

		if ($StateOfPlaces == 'set'){
			return true;

		} else {
			return false;

		}

	}

	public function isStateOfPlacesError(){

		$StateOfPlaces = $this->getStateOfPlaces();

		if ($StateOfPlaces == 'error'){
			return true;

		} else {
			return false;

		}

	}

 	public function checkPhaseCompletion($phase){

 	switch ($phase){

			case 1:
				if ($this->getRoomsCount() && $this->getExamtime() && $this->getParticipantsCount()){
					return true;
					} else {
						return false;
					}
			case 2:
				if ($this->isStateOfPlacesCorrect()){
					return true;
					} else {
						return false;
					}
			case 3:
				return false;
			case 4:
				return false;
 		}

 	}

	#### participants view ####

	public function isParticipant(){

			global $USER;

			$participantsList = json_decode($this->moduleinstance->participants);

			if ($participantsList){
					foreach ($participantsList as $key => $value){

							if($USER->id == $value){

									return true;
							}
					}

					return false;
			}
	}

	public function getDateForParticipants(){

			$dateState = $this->moduleinstance->datetimevisible;
			$examtime = $this->getExamtime();

			if($dateState && $examtime){
						return date('d.m.Y', $examtime);
			} else{
						return false;
			}
	}

	public function getTimeForParticipants(){

			$timeState = $this->isDateTimeVisible();
			$examtime = $this->getExamtime();

			if($timeState && $examtime){
						return date('H:i', $examtime);
			} else{
						return false;
			}
	}

	public function getRoomForParticipants(){

			global $USER;

			$roomState = $this->isRoomVisible();
			$assignmentArray = $this->getAssignedPlaces();
			$participantsRoom =  false;

			if($roomState && $assignmentArray){
						foreach ($assignmentArray as $key => $room){
							foreach ($room->assignments as $key => $assignment){
								if ($assignment->userid == $USER->id){
										$participantsRoom = $room->roomname;
								}
							}
						}

						return $participantsRoom;

			} else{
						return false;
			}
	}

	public function getPlaceForParticipants(){

			global $USER;

			$placesState = $this->isPlaceVisible();
			$assignmentArray = $this->getAssignedPlaces();
			$participantsPlace =  false;

			if($placesState && $assignmentArray){
						foreach ($assignmentArray as $key => $room){
							foreach ($room->assignments as $key => $assignment){
								if ($assignment->userid == $USER->id){
										$participantsPlace = $assignment->place;
								}
							}
						}

						return $participantsPlace;

			} else{
						return false;
			}
	}

	#### errors ####

	public function throwError($errorMessage){
			echo $errorMessage;

	}

 	#### events ####

 	public function startEvent($type){

		require_once(__DIR__.'/../event/course_module_viewed.php');

		switch ($type){

			case 'view':
				$event = course_module_viewed::create(array(
					'objectid' => $this->moduleinstance->id,
					'context' => $this->modulecontext
				));
				$event->add_record_snapshot('course', $this->course);
				$event->add_record_snapshot('exammanagement', $this->moduleinstance);
				$event->trigger();
		}
	}

	#### wrapped Moodle DB functions #####

	// protected function getFieldFromDB($table, $fieldname, $condition){
	// 	global $DB;
	//
	// 	$field = $DB->get_field($table, $fieldname, $condition, '*', MUST_EXIST);
	//
	// 	return $field;
	// }

	// protected function getRecordFromDB($table, $condition){
	// 	global $DB;
	//
	// 	$record = $DB->get_record($table, $condition);
	//
	// 	return $record;
	// }

	// protected function getRecordsFromDB($table, $condition){
	// 	global $DB;
	//
	// 	$records = $DB->get_records($table, $condition);
	//
	// 	return $records;
	// }

	// protected function UpdateRecordInDB($table, $obj){ // in DBobj and fully transfered
	// 	global $DB;
	//
	// 	return $DB->update_record($table, $obj);
	// }

	// protected function InsertRecordInDB($table, $dataobject){
	// 	global $DB;
	//
	// 	return $DB->insert_record($table, $dataobject, $returnid=true, $bulk=false);
	// }

	// protected function InsertBulkRecordsInDB($table, $dataobjects){
	// 	global $DB;
	//
	// 	$DB->insert_records($table, $dataobjects);
	// }

	######### feature: chooseRooms ##########

	public function saveRooms($roomsArr){

		$MoodleDBObj = MoodleDB::getInstance();
		$MoodleObj = Moodle::getInstance($this->id, $this->e);

		$rooms=json_encode($roomsArr);

		$this->moduleinstance->rooms=$rooms;

		$MoodleDBObj->UpdateRecordInDB("exammanagement", $this->moduleinstance);

		$MoodleObj->redirectToOverviewPage('beforeexam', 'Räume für die Prüfung wurden ausgewählt', 'success');

	}

	public function getRoomObj($roomID){

		$MoodleDBObj = MoodleDB::getInstance();

		$room = $MoodleDBObj->getRecordFromDB('exammanagement_rooms', array('id' => $roomID));;

		return $room;
	}

	public function getAllRoomIDs($format){ //not used at the moment, use getAllRoomsIDsSortedByName() instead

		$MoodleDBObj = MoodleDB::getInstance();

		$allRooms = $MoodleDBObj->getRecordsFromDB('exammanagement_rooms', array());
		$allRoomsIDs;

		if ($allRooms){
			foreach ($allRooms as $key => $value){
				$temp=get_object_vars($value);
				$allRoomsIDs[$key] = $temp['id'];
			}

			if ($format=='String'){
				$allsRoomsIDs = implode(',', $allRoomsIDs);
			}

			return $allRoomsIDs;

		} else{
			return false;
		}

	}

	public function getAllRoomIDsSortedByName(){ // used for displaying rooms

		$MoodleDBObj = MoodleDB::getInstance();

		$allRooms = $MoodleDBObj->getRecordsFromDB('exammanagement_rooms', array());
		$allRoomNames;
		$allRoomIDs;

		if ($allRooms){
			foreach ($allRooms as $key => $value){
				$temp=get_object_vars($value);
				$allRoomNames[$key] = $temp['name'];
			}

			foreach ($allRooms as $key => $value){
				$temp=get_object_vars($value);
				$allRoomIDs[$key] = $temp['id'];
			}

			array_multisort($allRoomNames, $allRoomIDs);

			return $allRoomIDs;

		} else{
			return false;
		}

	}

	public function filterCheckedRooms($obj){

			$obj= get_object_vars($obj);
			$roomsArray=$obj["rooms"];
			$rooms=array();

			foreach ($roomsArray as $key => $value){
				if ($value==1){
					array_push($rooms, $key);
				}

			}

			sort($rooms); //sort checked roomes ids for saving in DB

			return $rooms;

	}

	public function getSavedRooms(){

		$rooms = $this->moduleinstance->rooms;

		if ($rooms){
				$roomsArray = json_decode($rooms);
				return $roomsArray;
			} else {
				return false;
		}
	}

	public function isDateTimeVisible(){

		$isDateTimeVisible = $this->moduleinstance->datetimevisible;

		if($isDateTimeVisible){
				return true;
		} else {
				return false;
		}
	}

	public function isRoomVisible(){

		$isRoomVisible = $this->moduleinstance->roomvisible;

		if($isRoomVisible){
				return true;
		} else {
				return false;
		}

	}

	public function isPlaceVisible(){

		$isPlaceVisible = $this->moduleinstance->placevisible;

		if($isPlaceVisible){
				return true;
		} else {
				return false;
		}

	}

	public function getTaskCount(){

		$tasks = $this->getTasks();

		if($tasks){
				$taskcount = count($tasks);
				return $taskcount;
		} else {
				return false;
		}

	}

	public function getTaskTotalPoints(){

		$tasks = $this->getTasks();
		$totalpoints = 0;

		if($tasks){
				foreach($tasks as $key => $points){
						$totalpoints += $points;
					}
				return $totalpoints;
		} else {
				return false;
		}

	}


	############## feature: setDateTime #########

	public function saveDateTime($examtime){

			$MoodleDBObj = MoodleDB::getInstance();
			$MoodleObj = Moodle::getInstance($this->id, $this->e);

			$this->moduleinstance->examtime=$examtime;

			$MoodleDBObj->UpdateRecordInDB("exammanagement", $this->moduleinstance);

			$MoodleObj->redirectToOverviewPage('beforeexam', 'Datum und Uhrzeit erfolgreich gesetzt', 'success');

	}

######### feature: addParticipants ##########

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

	public function saveParticipants($participantsArr, $mode){

			$MoodleDBObj = MoodleDB::getInstance();
			$MoodleObj = Moodle::getInstance($this->id, $this->e);

			$participants = json_encode($participantsArr);

			if ($mode == 'tmp'){
						$this->moduleinstance->tmpparticipants = NULL;

						if ($participants!="null"){
								$this->moduleinstance->tmpparticipants = $participants;
						}

						$MoodleDBObj->UpdateRecordInDB("exammanagement", $this->moduleinstance);
						redirect ($this->getExammanagementUrl('addParticipants',$this->id), 'Datei eingelesen', null, notification::NOTIFY_SUCCESS);

			} else{
						$this->moduleinstance->participants = NULL;

					if ($participants!="null"){
							$this->moduleinstance->participants = $participants;
							$this->moduleinstance->tmpparticipants = NULL; //clear tmp participants
					}

					$MoodleDBObj->UpdateRecordInDB("exammanagement", $this->moduleinstance);

					$MoodleObj->redirectToOverviewPage('beforeexam', 'Teilnehmer zur Prüfung hinzugefügt', 'success');
			}
	}

	public function getCourseParticipantsIDs(){
			$CourseParticipants = get_enrolled_users($this->modulecontext, 'mod/exammanagement:takeexams');
			$CourseParticipantsIDsArray;

			foreach ($CourseParticipants as $key => $value){
				$temp=get_object_vars($value);
				$CourseParticipantsIDsArray[$key] = $temp['id'];
			}

			return $CourseParticipantsIDsArray;


	}

	public function filterCheckedParticipants($obj){

			$obj= get_object_vars($obj);
			$paricipantsArray=$obj["participants"];
			$participants=array();

			foreach ($paricipantsArray as $key => $value){
				if ($value==1){
					array_push($participants, $key);
				}

			}

			if ($participants){

				sort($participants); //sort checked participants ids for saving in DB

				return $participants;

			} else {
				return Null;

			}

	}

	public function getSavedParticipants(){

		$participants = $this->moduleinstance->participants;

		if ($participants){
				$participantsArray = json_decode($participants);
				return $participantsArray;
			} else {
				return false;
		}
	}

	public function getTempParticipants(){

		$tmpparticipants = $this->moduleinstance->tmpparticipants;

		if ($tmpparticipants){
				$tmpParticipantsArray = json_decode($tmpparticipants);
				return $tmpParticipantsArray;
			} else {
				return false;
		}
	}

	public function getMoodleUser($userid){

		$MoodleDBObj = MoodleDB::getInstance();

		$user = $MoodleDBObj->getRecordFromDB('user', array('id'=>$userid));

		return $user;

	}

	public function getUserPicture($userid){

		global $OUTPUT;

		$user = $this->getMoodleUser($userid);
		return $OUTPUT->user_picture($user, array('courseid' => $this->course->id, 'link' => true));

	}

	public function getUserProfileLink($userid){

		$MoodleObj = Moodle::getInstance($this->id, $this->e);

		$user = $this->getMoodleUser($userid);
		$profilelink = '<strong><a href="'.$MoodleObj->getMoodleUrl('/user/view.php', $user->id, 'course', $this->course->id).'">'.fullname($user).'</a></strong>';

		return $profilelink;

	}

	public function getUserMatrNrPO($userid){

		require_once(__DIR__.'/../ldap/ldapManager.php');

		$ldapManagerObj = ldapManager::getInstance($this->id, $this->e);

		$userMatrNr = $ldapManagerObj->getIMTLogin2MatriculationNumberTest($userid);

		if($userMatrNr){
			return $userMatrNr;
		} else {
			return '-';
		}
	}

	public function getParticipantsGroupNames($userid){

		$MoodleObj = Moodle::getInstance($this->id, $this->e);

		$userGroups = groups_get_user_groups($this->course->id, $userid);
		$groupNameStr = false;

		foreach ($userGroups as $key => $value){
			if ($value){
				foreach ($value as $key2 => $value2){
					$groupNameStr.='<strong><a href="'.$MoodleObj->getMoodleUrl('/user/index.php', $this->course->id, 'group', $value2).'">'.groups_get_group_name($value2).'</a></strong>, ';
				}
			}
			else{
				$groupNameStr='-';
				break;
			}
		}

		return $groupNameStr;

	}

	######### feature: configure tasks ##########

	public function saveTasks($fromform){

			$MoodleDBObj = MoodleDB::getInstance();
			$MoodleObj = Moodle::getInstance($this->id, $this->e);

			$tasks = json_encode($fromform->task);
			$this->moduleinstance->tasks=$tasks;

			$MoodleDBObj->UpdateRecordInDB("exammanagement", $this->moduleinstance);

			$MoodleObj->redirectToOverviewPage('beforeexam', 'Inhalt gespeichert', 'success');

	}

	public function getTasks(){

			$tasks = json_decode($this->moduleinstance->tasks);
			return $tasks;
	}

	######### feature: textfield ##########

	public function saveTextfield($fromform){

			$MoodleDBObj = MoodleDB::getInstance();
			$MoodleObj = Moodle::getInstance($this->id, $this->e);

			$textfield = json_encode($fromform->textfield);

			$this->moduleinstance->textfield=$textfield;

			$MoodleDBObj->UpdateRecordInDB("exammanagement", $this->moduleinstance);

			$MoodleObj->redirectToOverviewPage('beforeexam', 'Inhalt gespeichert', 'success');

	}

	########### Send Groupmessage to all Participants ####

	public function sendGroupMessage($subject, $content){

		$MoodleObj = Moodle::getInstance($this->id, $this->e);

		$mailsubject="PANDA - Prüfungsorganisation: Kurs ".$this->course->fullname.' Betreff: '.$subject;
		$mailtext=$content;
		$participants=$this->getSavedParticipants();

		foreach ($participants as $key => $value){

			$user=$this->getMoodleUser($value);

			$this->sendSingleMessage($user, $mailsubject, $mailtext);

		}

		$MoodleObj->redirectToOverviewPage('beforeexam', 'Nachricht erfolgreich versendet.', 'success');

	}

	protected function sendSingleMessage($user, $subject, $text){

		global $USER;

		$MoodleObj = Moodle::getInstance($this->id, $this->e);

		$message = new message();
		$message->component = 'mod_exammanagement'; // the component sending the message. Along with name this must exist in the table message_providers
		$message->name = 'groupmessage'; // type of message from that module (as module defines it). Along with component this must exist in the table message_providers
		$message->userfrom = $USER; // user object
		$message->userto = $user; // user object
		$message->subject = $subject; // very short one-line subject
		$message->fullmessage = $text; // raw text
		$message->fullmessageformat = FORMAT_MARKDOWN; // text format
		$message->fullmessagehtml = '<p>'.$text.'</p>'; // html rendered version
		$message->smallmessage = $text; // useful for plugins like sms or twitter
		$message->notification = '0';
		$message->contexturl = 'http://GalaxyFarFarAway.com';
		$message->contexturlname = 'Context name';
		$message->replyto = "noreply@imt.uni-paderborn.de";

		$header = '';
		$url = $MoodleObj->getMoodleUrl("/mod/exammanagement/view.php", $this->id);
		$footer = $this->course->fullname.' -> Prüfungsorganisation -> '.$this->moduleinstance->name.'<br><a href="'.$url.'">'.$url.'</a>';
		$content = array('*' => array('header' => $header, 'footer' => $footer)); // Extra content for specific processor

		$message->set_additional_content('email', $content);
		$message->courseid = $this->course->id; // This is required in recent versions, use it from 3.2 on https://tracker.moodle.org/browse/MDL-47162

		//// Create a file instance.
		//	$usercontext = context_user::instance($user->id);
		// 	$file = new stdClass;
		// 	$file->contextid = $usercontext->id;
		// 	$file->component = 'user';
		// 	$file->filearea  = 'private';
		// 	$file->itemid    = 0;
		// 	$file->filepath  = '/';
		// 	$file->filename  = '1.txt';
		// 	$file->source    = 'test';
		//
		// 	$fs = get_file_storage();
		// 	$file = $fs->create_file_from_string($file, 'file1 content');
		// 	$message->attachment = $file;

 		//var_dump($message);

		$messageid = message_send($message);

		return $messageid;

	}

	########### assign places #######

	public function assignPlaceToUser($userid, $place){

		$assignment = new stdClass();

		$assignment->userid = $userid;
		$assignment->place = $place;

		 return $assignment;
	}

	public function savePlacesAssignment($assignmentArray){

		$MoodleDBObj = MoodleDB::getInstance();

		$this->moduleinstance->assignedplaces=json_encode($assignmentArray);

		$MoodleDBObj->UpdateRecordInDB("exammanagement", $this->moduleinstance);

	}

	public function unsetStateofPlaces($type){

		$MoodleDBObj = MoodleDB::getInstance();

		$this->moduleinstance->stateofplaces=$type;

		$MoodleDBObj->UpdateRecordInDB("exammanagement", $this->moduleinstance);
	}

	public function getStateOfPlaces(){

		$StateOfPlaces = $this->moduleinstance->stateofplaces;

		return $StateOfPlaces;

	}

	public function getAssignedPlaces(){

		$getAssignedPlaces = json_decode($this->moduleinstance->assignedplaces);

		return $getAssignedPlaces;

	}

	######### feature: configure tasks ##########

	public function saveGradingscale($fromform){

			$MoodleDBObj = MoodleDB::getInstance();
			$MoodleObj = Moodle::getInstance($this->id, $this->e);

			$gradingscale = json_encode($fromform->gradingsteppoints);
			$this->moduleinstance->gradingscale=$gradingscale;

			$MoodleDBObj->UpdateRecordInDB("exammanagement", $this->moduleinstance);

			$MoodleObj->redirectToOverviewPage('beforeexam', 'Inhalt gespeichert', 'success');

	}

	public function getGradingscale(){

			$gradingscale = json_decode($this->moduleinstance->gradingscale);

			if($gradingscale){
					return $gradingscale;
			} else{
				return false;
			}
	}

	########### Export PDFS ####

		public function getParticipantsListTableHeader() { // to bemoved to pdf object
			$header = "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">";
			$header .= "<thead>";
			$header .= "<tr bgcolor=\"#000000\" color=\"#FFFFFF\">";
			$header .= "<td width=\"" . WIDTH_COLUMN_NAME . "\"><b>" . get_string('lastname', 'mod_exammanagement') . "</b></td>";
			$header .= "<td width=\"" . WIDTH_COLUMN_FIRSTNAME . "\"><b>" . get_string('firstname', 'mod_exammanagement') . "</b></td>";
			$header .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\"><b>" . get_string('matrno', 'mod_exammanagement') . "</b></td>";
			$header .= "<td width=\"" . WIDTH_COLUMN_ROOM . "\" align=\"center\"><b>" . get_string('room', 'mod_exammanagement') . "</b></td>";
			$header .= "<td width=\"" . WIDTH_COLUMN_PLACE . "\" align=\"center\"><b>" . get_string('place', 'mod_exammanagement') . "</b></td>";
			$header .= "</tr>";
			$header .= "</thead>";

			return $header;
		}

		public function getSeatingPlanTableHeader() { // to bemoved to pdf object

				$header = "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">";
				$header .= "<thead>";
				$header .= "<tr bgcolor=\"#000000\" color=\"#FFFFFF\">";
				$header .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\"><b>" . get_string('matrno', 'mod_exammanagement') . "</b></td>";
				$header .= "<td width=\"" . WIDTH_COLUMN_ROOM . "\" align=\"center\"><b>" . get_string('room', 'mod_exammanagement') . "</b></td>";
				$header .= "<td width=\"" . WIDTH_COLUMN_PLACE . "\" align=\"center\"><b>" . get_string('place', 'mod_exammanagement') . "</b></td>";
				$header .= "</tr>";
				$header .= "</thead>";

				return $header;
			}

		public function buildChecksumExamLabels($ean) {
				$s = preg_replace("/([^\d])/", "", $ean);
				if (strlen($s) != 12) {
					return false;
				}

				$check = 0;
				for ($i = 0; $i < 12; $i++) {
					$check += (($i % 2) * 2 + 1) * $s{$i};
				}

				return (10 - ($check % 10)) % 10;
		}

	########### debugging ########

	public function debugElementsOverview(){

		echo'<h4> Debug-Information </h4>';
		echo('id:'.$this->debugElement('id').'<br>');
		echo('e:'.$this->debugElement('e').'<br>');
		echo('cm:'.json_encode($this->debugElement('cm')).'<br>');
		echo('course:'.json_encode($this->debugElement('course')).'<br>');
		echo('moduleinstance:'.json_encode($this->debugElement('moduleinstance')).'<br>');
		echo('modulecontext:'.json_encode($this->debugElement('modulecontext')).'<br>');
	}

	protected function debugElement($c){ //if some extern functions need some of the objects params

		switch ($c){ //get requested element

			case 'id':
				return $this->id;
			case 'e':
				return $this->e;
			case 'cm':
				return $this->cm;
			case 'course':
				return $this->course;
			case 'moduleinstance':
				return $this->moduleinstance;
			case 'modulecontext':
				return $this->modulecontext;
		}

	}
}
