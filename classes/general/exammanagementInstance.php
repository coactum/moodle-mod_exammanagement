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

 		$url = $MoodleObj->getMoodleUrl('/mod/exammanagement/'.$component.'.php', $id);

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
		$helptextstr.= '	<p class="mb-0">'.get_string('helptext_link', 'mod_exammanagement').' <a href="https://hilfe.uni-paderborn.de/PANDA" class="alert-link" target="_blank">https://hilfe.uni-paderborn.de/PANDA</a></p>';
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

		$UserObj = User::getInstance($this->id, $this->e, $this->moduleinstance->categoryid);

 		switch ($phase){

			case 1:
				if ($this->getRoomsCount() && $this->getExamtime() && $UserObj->getParticipantsCount() && $this->getTaskTotalPoints()){
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
			case "Exam":
				if ($this->getExamtime() < time()){
					return true;
				} else {
					return false;
				}
			case 3:
				if ($this->getDataDeletionDate()){
					return true;
				} else {
					return false;
				}
			case 4:
				if ($this->getExamReviewTime() && $this->getExamReviewRoom() && $this->isExamReviewVisible()){
					return true;
				} else {
					return false;
				}
			case 5:
				if ($this->getExamReviewTime() && $this->getExamReviewTime() < time()){
					return true;
				} else {
					return false;
				}
 		}

 	}

	public function determineCurrentPhase(){

			$phaseOne = $this->checkPhaseCompletion(1);
			$phaseTwo = $this->checkPhaseCompletion(2);
			$phaseThree = $this->checkPhaseCompletion(3);
			$phaseFour = $this->checkPhaseCompletion(4);
			$phaseFive = $this->checkPhaseCompletion(5);

			$examDate = $this->getExamtime();
			$examReviewDate = $this->moduleinstance->examreviewtime;
			$date = time();

 			if(!$phaseOne){
					return '1';
			} else if(!$phaseTwo){
					return '2';
			} else if($phaseTwo && $examDate > $date){
					return 'exam';
			} else if(!$phaseThree && $examDate < $date){
					return '3';
			} else if($phaseThree && $examDate < $date){
					return '4';
			} else if($phaseFour && $examReviewDate < $date){
					return '5';
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

	######### feature: chooseRooms ##########

	public function getDefaultRooms(){

		global $DB;

		$MoodleDBObj = MoodleDB::getInstance();
		$MoodleObj = Moodle::getInstance($this->id, $this->e);

		$defaultRooms = $MoodleDBObj->getRecordsSelectFromDB('exammanagement_rooms', "type = 'defaultroom'");

		if($defaultRooms){
			return $defaultRooms;
		} else {
			return false;
		}

	}

	public function getCustomRooms(){

		global $DB, $USER;

		$MoodleDBObj = MoodleDB::getInstance();
		$MoodleObj = Moodle::getInstance($this->id, $this->e);

		$customRooms = $MoodleDBObj->getRecordsSelectFromDB('exammanagement_rooms', "type = 'customroom' AND moodleuserid = " .$USER->id);

		if($customRooms){
			return $customRooms;
		} else {
			return false;
		}

	}

	public function getRoomObj($roomID){

		$MoodleDBObj = MoodleDB::getInstance();

		$room = $MoodleDBObj->getRecordFromDB('exammanagement_rooms', array('roomid' => $roomID));

		if($room){
			return $room;
		} else {
			return false;
		}
	}

	// public function getAllRoomIDs($format){ //not used at the moment, use getAllRoomsIDsSortedByName() instead
	//
	// 	$MoodleDBObj = MoodleDB::getInstance();
	//
	// 	$allRooms = $this->getDefaultRooms();
	// 	$allRoomsIDs;
	//
	// 	if ($allRooms){
	// 		foreach ($allRooms as $key => $value){
	// 			$temp=get_object_vars($value);
	// 			$allRoomsIDs[$key] = $temp['id'];
	// 		}
	//
	// 		if ($format=='String'){
	// 			$allsRoomsIDs = implode(',', $allRoomsIDs);
	// 		}
	//
	// 		return $allRoomsIDs;
	//
	// 	} else{
	// 		return false;
	// 	}
	//
	// }

	public function getAllRoomIDsSortedByName(){ // used for displaying rooms

		$defaultRooms = $this->getDefaultRooms();
		$customRooms = $this->getCustomRooms();

		if($defaultRooms && $customRooms){
			$allRooms = array_merge($defaultRooms, $customRooms);
		} else if ($defaultRooms){
			$allRooms = $defaultRooms;
		} else if($customRooms){
			$allRooms = $customRooms;
		}

		$allRoomNames;
		$allRoomIDs;

		if ($allRooms){
			foreach ($allRooms as $key => $value){
				$temp=get_object_vars($value);
				$allRoomNames[$key] = $temp['name'];
			}

			foreach ($allRooms as $key => $value){
				$temp=get_object_vars($value);
				$allRoomIDs[$key] = $temp['roomid'];
			}

			array_multisort($allRoomNames, $allRoomIDs);

			return $allRoomIDs;

		} else{
			return false;
		}

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

	public function isExamReviewVisible(){

		$isExamReviewVisible = $this->moduleinstance->examreviewvisible;

		if($isExamReviewVisible){
				return true;
		} else {
				return false;
		}
	}

	public function getTaskCount(){

		$tasks = $this->getTasks();

		if($tasks){
				$taskcount = count((array)$tasks);
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
						$totalpoints += intval($points);
				}
				return $totalpoints;

		} else {
				return false;
		}

	}

	public function getInputResultsCount(){

		$UserObj = User::getInstance($this->id, $this->e, $this->moduleinstance->categoryid);

		$users = $UserObj->getAllParticipantsWithResults();

		if($users){
			return count($users);
		} else {
				return false;
		}

	}

	public function getHRExamReviewTime(){

		$examReviewTime = $this->moduleinstance->examreviewtime;
		if($examReviewTime){
			$hrexamReviewTime = date('d.m.Y', $examReviewTime).' um '.date('H:i', $examReviewTime);
			return $hrexamReviewTime;
		} else {
			return false;
		}

	}

	public function getExamReviewTime(){

		$examReviewTime = $this->moduleinstance->examreviewtime;
		if($examReviewTime){
			return $examReviewTime;
		} else {
			return false;
		}

	}

	public function getExamReviewRoom(){

		$examReviewRoom = json_decode($this->moduleinstance->examreviewroom);
		if($examReviewRoom){
			return $examReviewRoom;
		} else {
			return '';
		}

	}

######### feature: addParticipants ##########

	public function getPAULTextFileHeaders(){

		$UserObj = User::getInstance($this->id, $this->e, $this->moduleinstance->categoryid);

		$textfileheaders = 	json_decode($this->moduleinstance->importfileheaders);

		if($textfileheaders && $UserObj->getAllExamParticipantsByHeader(0)){
			array_unshift($textfileheaders, true);
		}

		$textfileheaders = array_values($textfileheaders);

		if ($textfileheaders){
				return $textfileheaders;
			} else {
				return false;
		}
	}

	######### feature: configure tasks ##########

	public function getTasks(){

			$tasks = (array) json_decode($this->moduleinstance->tasks);

			if($tasks){
				return $tasks;
			} else {
				return false;
			}
	}

	########### Send Groupmessage to all Participants ####

	public function sendSingleMessage($user, $subject, $text){

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
		$message->fullmessagehtml = $text; // html rendered version
		$message->smallmessage = $text; // useful for plugins like sms or twitter
		$message->notification = '0';
		$message->contexturl = '';
		$message->contexturlname = 'Context name';
		$message->replyto = "";

		$header = '';
		$url = $MoodleObj->getMoodleUrl("/mod/exammanagement/view.php", $this->id);
		$footer = '--------------------------------------------------------------------- \r\n Diese Nachricht wurde über die Prüfungsorganisation in PANDA verschickt. Unter dem folgenden Link finden Sie alle weiteren Informationen.\r\n' . $this->moduleinstance->categoryid . ' -> ' . $this->course->fullname.' -> Prüfungsorganisation -> ' . $this->moduleinstance->name . ' \r\n ' . $url;
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

	public function unsetStateofPlaces($type){

		$MoodleDBObj = MoodleDB::getInstance();

		$this->moduleinstance->stateofplaces = $type;

		$MoodleDBObj->UpdateRecordInDB("exammanagement", $this->moduleinstance);
	}

	public function getStateOfPlaces(){

		$StateOfPlaces = $this->moduleinstance->stateofplaces;

		return $StateOfPlaces;

	}

	######### feature: configure gradingscale #########

	public function getGradingscale(){

			$gradingscale = json_decode($this->moduleinstance->gradingscale);

			if($gradingscale){
					return $gradingscale;
			} else{
				return false;
			}
	}

######### feature: input results ##########

public function saveResults($fromform){

		$MoodleDBObj = MoodleDB::getInstance();
		$LdapManagerObj = ldapManager::getInstance($this->id, $this->e);
		$UserObj = User::getInstance($this->id, $this->e, $this->moduleinstance->categoryid);

		if($LdapManagerObj->is_LDAP_config()){
				$ldapConnection = $LdapManagerObj->connect_ldap();

				$userlogin = $LdapManagerObj->studentid2uid($ldapConnection, $fromform->matrnr);

				if($userlogin){
					$userid = $MoodleDBObj->getFieldFromDB('user','id', array('username' => $userlogin));
				}
		} else {
				$userid = $LdapManagerObj->getMatriculationNumber2ImtLoginTest($fromform->matrnr);

				if(!$userid){
					$userlogin = 'tool_generator_'.substr($fromform->matrnr, 1);
				}
		}

		// getParticipantObj
		if($userid !== false && $userid !== null){
			$participantObj = $UserObj->getExamParticipantObj($userid);
		} else if($userlogin !== false && $userlogin !== null){
			$participantObj = $UserObj->getExamParticipantObj(false, $userlogin);
		}

		if($participantObj){
			$participantObj->examstate = json_encode($fromform->state);

			if($fromform->state['nt']=='1' || $fromform->state['fa']=='1' || $fromform->state['ill']=='1'){
					foreach ($fromform->points as $task => $points){
							$fromform->points[$task] = 0;
					}
			}

			$participantObj->exampoints = json_encode($fromform->points);
			$participantObj->timeresultsentered = time();

			$update = $MoodleDBObj->UpdateRecordInDB('exammanagement_part_'.$this->moduleinstance->categoryid, $participantObj);
			if($update){
				redirect ($this->getExammanagementUrl('inputResults', $this->id), null, null, null);
			} else {
				redirect ($this->getExammanagementUrl('inputResults', $this->id), 'Speichern fehlgeschlagen', null, notification::NOTIFY_ERROR);
			}


		} else{
			redirect ($this->getExammanagementUrl('inputResults', $this->id), 'Ungültige Matrikelnummer', null, notification::NOTIFY_ERROR);

		}
}

######### feature: exportResults ##########

public function getPAULFileHeaders(){

		$PAULFileHeaders = json_decode($this->moduleinstance->importfileheaders);

		if($PAULFileHeaders){
				return $PAULFileHeaders;
		} else{
			return false;
		}
}

// delete instance
public function getDataDeletionDate(){

		$correctionCompletionDate = $this->moduleinstance->correctioncompletiondate;

		if($correctionCompletionDate){
				$dataDeletionDate = date('d.m.Y', strtotime("+3 months", $correctionCompletionDate));
		} else {
			return false;
		}

		return $dataDeletionDate;
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
