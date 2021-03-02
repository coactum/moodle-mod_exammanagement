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
 * class containing all common functions for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

defined('MOODLE_INTERNAL') || die();
use mod_exammanagement\event\course_module_viewed;
use context_module;
use core\message\message;

class exammanagementInstance{

	protected $id;
	protected $e;
	protected $cron;
	protected $cm;
	protected $course;
	public $moduleinstance;
	protected $modulecontext;

	public function __construct($id, $e, $cron=false ) {

		$MoodleDBObj = MoodleDB::getInstance();

		$this->id=$id;
		$this->e=$e;
		$this->cron=$cron;

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

			if($cron == false){ // do this only when not called to send message from cron job
				require_login($this->course, true, $this->cm);

				$this->modulecontext = context_module::instance($this->cm->id);
			}

    }

	#### singleton class ####

	public static function getInstance($id, $e){

		static $inst = null;
			if ($inst === null) {
				$inst = new exammanagementInstance($id, $e);
			}
			return $inst;

	}

	#### getter for object properties  ####

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

	public function getMoodleSystemName(){

		$moodlename = get_config('mod_exammanagement', 'moodlesystemname');

		if($moodlename){
			return $moodlename;
		} else {
			return '';
		}
	}

	#### universal functions for exammanagement ####

 	public function getExammanagementUrl($component, $id){

		$MoodleObj = Moodle::getInstance($this->id, $this->e);

 		$url = $MoodleObj->getMoodleUrl('/mod/exammanagement/'.$component.'.php', $id);

 		return $url;
	}

	public function getCleanCourseCategoryName(){

		global $PAGE;

		$categoryname = substr(strtoupper(preg_replace("/[^0-9a-zA-Z]/", "", $PAGE->category->name)), 0, 20);

 		if ($categoryname) {
			return $categoryname;
		} else {
			return get_string('coursecategory_name_no_semester', 'mod_exammanagement');
		}
 	}

	#### get display values for overview page ####

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
				} else {
					array_push($roomNames, get_string('deleted_room', 'mod_exammanagement'));
				}

				}

			asort($roomNames);

			$roomsStr = implode(", ", $roomNames);

			return $roomsStr;

		} else {
			return false;
		}
	}

	public function getTotalNumberOfSeats(){

		$rooms = $this->getRooms('examrooms');

		$totalSeats = 0;

		if($rooms){
			foreach($rooms as $room){

				$places = json_decode($room->places);

				if(isset($places)){
					$placesCount = count($places);	//get Places of this Room
				} else {
					$placesCount = 0;	//get Places of this Room
				}

				$totalSeats += $placesCount;
			}
		}

		return $totalSeats;

	}

 	public function getExamtime(){		// get examtime (for form)
		if ($this->moduleinstance->examtime){
				return $this->moduleinstance->examtime;
			} else {
				return false;
			}
	}

	public function getHrExamtimeTemplate() {	// convert examtime to human readable format for template
		$examtime = $this->getExamtime();
		if($examtime){
			$hrexamtimetemplate = date('d.m.Y', $examtime).' '.get_string('at', 'mod_exammanagement').' '.date('H:i', $examtime);
			return $hrexamtimetemplate;
		} else {
			return false;
		}
 	}

	public function getHrExamtime() {	// convert examtime to human readable format for template
		$examtime = $this->getExamtime();
		if($examtime){
			$hrexamtime = date('d.m.Y', $examtime).' '.date('H:i', $examtime);
			return $hrexamtime;
		} else {
			return false;
		}
	}

	public function getTasks(){

		$tasks = (array) json_decode($this->moduleinstance->tasks);

		if($tasks){
			return $tasks;
		} else {
			return false;
		}
	}

	public function getTaskCount(){ // get count of tasks

		$tasks = $this->getTasks();

		if($tasks){
			$taskcount = count((array)$tasks);
			return $taskcount;
		} else {
			return false;
		}

	}

	public function getTaskTotalPoints(){ // get total points off all tasks

		$tasks = $this->getTasks();
		$totalpoints = 0;

		if($tasks){
			foreach($tasks as $key => $points){
					$totalpoints += floatval($points);
			}
			return $totalpoints;

		} else {
			return $totalpoints;
		}

	}

 	public function getTextfieldObject(){

 		$textfield = $this->moduleinstance->textfield;

		$textfield = json_decode($textfield);

		return $textfield;
	}

 	public function getTextFromTextfield(){

		$textfield = $this->getTextfieldObject('exammanagement','textfield', array('id' => $this->cm->instance));

		if($textfield){
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

	public function placesAssigned(){
		$assignmentmode = $this->moduleinstance->assignmentmode;

		if(isset($assignmentmode) || $this->allPlacesAssigned()){
			return true;
		} else {
			return false;

		}

	}

	public function allPlacesAssigned(){
		$UserObj = User::getInstance($this->id, $this->e, $this->getCm()->instance);

		$assignedPlacesCount = $this->getAssignedPlacesCount();

		if ($assignedPlacesCount !== 0 && $assignedPlacesCount == $UserObj->getParticipantsCount()){
			return true;
		} else {
			return false;
		}

	}

	public function getAssignedPlacesCount(){

		$MoodleDBObj = MoodleDB::getInstance();

		$select = "exammanagement =".$this->getCm()->instance;
		$select .= " AND place IS NOT NULL";

		$assignedPlacesCount = $MoodleDBObj->countRecordsInDB('exammanagement_participants', $select);

		if (isset($assignedPlacesCount)){
			return $assignedPlacesCount;

		} else {
			return 0;

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

	public function isBonusVisible(){

		$isBonusVisible = $this->moduleinstance->bonusvisible;

		if($isBonusVisible){
			return true;
		} else {
			return false;
		}

	}

	public function isResultVisible(){

		$isResultVisible = $this->moduleinstance->resultvisible;

		if($isResultVisible){
			return true;
		} else {
			return false;
		}

	}

	public function getGradingscale(){

		$gradingscale = json_decode($this->moduleinstance->gradingscale);

		if($gradingscale){
				return $gradingscale;
		} else{
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

	public function getHRExamReviewTime(){

		$examReviewTime = $this->getExamReviewTime();
		if($examReviewTime){
			$hrexamReviewTime = date('d.m.Y', $examReviewTime).' '.get_string('at', 'mod_exammanagement').' '.date('H:i', $examReviewTime);
			return $hrexamReviewTime;
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

	public function isExamReviewVisible(){

		$isExamReviewVisible = $this->moduleinstance->examreviewvisible;

		if($isExamReviewVisible){
			return true;
		} else {
			return false;
		}
	}

	public function getDataDeletionDate(){

		$dataDeletionDate = $this->moduleinstance->datadeletion;

		if($dataDeletionDate){
				$dataDeletionDate = date('d.m.Y', $dataDeletionDate);
		} else {
			return false;
		}

		return $dataDeletionDate;
	}

	public function isExamDataDeleted(){
		$isExamDataDeleted = $this->moduleinstance->datadeleted;

		if($isExamDataDeleted){
			return true;
		} else {
			return false;
		}
	}

	#### determine state of phases ####

 	public function checkPhaseCompletion($phase){

		$UserObj = User::getInstance($this->id, $this->e, $this->getCm()->instance);

 		switch ($phase){

			case 1:
				if ($this->getRoomsCount() && $this->getExamtime() && $UserObj->getParticipantsCount() && $this->getTaskCount()){
					return true;
				} else {
						return false;
				}
			case 2:
				if ($this->placesAssigned() && (($this->isDateTimeVisible() && $this->isRoomVisible() && $this->isPlaceVisible()) || ($this->getExamtime() && $this->getExamtime() < time()))){
					return true;
				} else {
						return false;
				}
			case "Exam":
				if ($this->getExamtime() && $this->getExamtime() < time()){
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

	 #### general helper method for formating numbers depending on language

	 public function formatNumberForDisplay($number, $format='string'){
		if($number !== false){

			if($format === 'string' && is_numeric($number)){
				$lang = current_language();

				if($lang==="de"){
					$number = str_replace('.', ',', $number);
				} else {
					$number = str_replace(',', '.', $number);
				}
			}

			return $number;
		} else {
			return '-';
		}
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

	#### methods for feature sites ####

	### rooms ###

	public function getRooms($mode, $sortorder = 'name'){ // get array with rooms according to params

		$MoodleDBObj = MoodleDB::getInstance();

		$rooms = array();

		if($mode === 'examrooms'){
			$roomIDs = json_decode($this->moduleinstance->rooms);

			if($roomIDs){
				$roomIDs = implode("', '", $roomIDs);

				$select = "roomid IN ('" . $roomIDs . "')";

				if($sortorder == 'name'){
					$rs = $MoodleDBObj->getRecordsetSelect('exammanagement_rooms', $select, array(), 'name ASC');
				} else {
					$rs = $MoodleDBObj->getRecordsetSelect('exammanagement_rooms', $select);
				}

			} else {
				return false;
			}

		} else if($mode === 'defaultrooms'){

			$select = "type = 'defaultroom'";

			$rs = $MoodleDBObj->getRecordsetSelect('exammanagement_rooms', $select, array(), 'name ASC');
		} else if($mode === 'all'){

			global $USER;

			$select = "type = 'defaultroom'";
			$select .= " OR type = 'customroom' AND moodleuserid = " .$USER->id;

			$rs = $MoodleDBObj->getRecordsetSelect('exammanagement_rooms', $select, array(), 'name ASC');
		} else {
			return false;
		}

        if($rs->valid()){

            foreach ($rs as $record) {
				array_push($rooms, $record);
			}

			$rs->close();

			if($sortorder == 'places_bigtosmall' || $sortorder == 'places_smalltobig' ){
				usort($rooms, function($a, $b){ // sort rooms by places count through custom user function (small to big rooms)

					$aPlaces = count(json_decode($a->places));
					$bPlaces = count(json_decode($b->places));

					return strnatcmp($aPlaces, $bPlaces); // sort by places count

				  });

				  if($sortorder == 'places_bigtosmall'){
					$rooms = array_reverse($rooms); // reverse array: now big to small rooms
				  }
			}

			return $rooms;

        } else {
			$rs->close();
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

	public function countDefaultRooms(){

		$MoodleDBObj = MoodleDB::getInstance();

		$defaultRoomsCount = $MoodleDBObj->countRecordsInDB('exammanagement_rooms', "type = 'defaultroom'");

		if($defaultRoomsCount && $defaultRoomsCount !== 0){
			return $defaultRoomsCount;
		} else {
			return false;
		}

	}

	### text file headers ###

	public function getTextFileHeaders(){

		$textfileheaders = 	json_decode($this->moduleinstance->importfileheaders);

		if ($textfileheaders){
				return $textfileheaders;
			} else {
				return false;
		}
	}

	### send moodle message to user ###

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
		$message->contexturlname = '';
		$message->replyto = "";

		$header = '';
		$url = '<a href="'.$MoodleObj->getMoodleUrl("/mod/exammanagement/view.php", $this->id) . '" target="_blank">'.$MoodleObj->getMoodleUrl("/mod/exammanagement/view.php", $this->id).'</a>';

		if($this->cron == false){
			$footer = '<br><br> --------------------------------------------------------------------- <br> ' . get_string('mailfooter', 'mod_exammanagement', ['systemname' => $this->getMoodleSystemName(), 'categoryname' => $this->getCleanCourseCategoryName(), 'coursename' => $this->getCourse()->fullname, 'name' => $this->moduleinstance->name, 'url' => $url]);
		} else {
			$footer = '<br><br> --------------------------------------------------------------------- <br> ' . get_string('mailfooter', 'mod_exammanagement', ['systemname' => $this->getMoodleSystemName(), 'categoryname' => '', 'coursename' => $this->getCourse()->fullname, 'name' => $this->moduleinstance->name, 'url' => $url]);
		}
		$content = array('*' => array('header' => $header, 'footer' => $footer)); // Extra content for specific processor

		$message->set_additional_content('email', $content);
		$message->courseid = $this->course->id; // This is required in recent versions, use it from 3.2 on https://tracker.moodle.org/browse/MDL-47162

		$messageid = message_send($message);

		return $messageid;

	}

	#### Export PDFS ####

	public function getParticipantsListTableHeader() {
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

	public function getSeatingPlanTable($leftCol, $rightCol) {

		$fill = false;

		$table = "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">";
		$table .= "<thead>";
		$table .= "<tr bgcolor=\"#000000\" color=\"#FFFFFF\">";
		$table .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\"><b>" . get_string('matrno', 'mod_exammanagement') . "</b></td>";
		$table .= "<td width=\"" . WIDTH_COLUMN_ROOM . "\" align=\"center\"><b>" . get_string('room', 'mod_exammanagement') . "</b></td>";
		$table .= "<td width=\"" . WIDTH_COLUMN_PLACE . "\" align=\"center\"><b>" . get_string('place', 'mod_exammanagement') . "</b></td>";
		$table .= "<td width=\"" . WIDTH_COLUMN_MIDDLE . "\" bgcolor=\"#FFFFFF\"></td>";

		if(count($rightCol) > 0){
			$table .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\"><b>" . get_string('matrno', 'mod_exammanagement') . "</b></td>";
			$table .= "<td width=\"" . WIDTH_COLUMN_ROOM . "\" align=\"center\"><b>" . get_string('room', 'mod_exammanagement') . "</b></td>";
			$table .= "<td width=\"" . WIDTH_COLUMN_PLACE . "\" align=\"center\"><b>" . get_string('place', 'mod_exammanagement') . "</b></td>";
		} else {
			$table .= "<td bgcolor=\"#FFFFFF\" width=\"" . (WIDTH_COLUMN_MATNO + WIDTH_COLUMN_ROOM + WIDTH_COLUMN_PLACE) . "\" colspan=\"3\"></td>";
		}

		$table .= "</tr>";
		$table .= "</thead>";

		for ($n = 0; $n < count($leftCol); $n++) {

			$table .= ($fill) ? "<tr bgcolor=\"#DDDDDD\">" : "<tr>";
			$table .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\">" . $leftCol[$n]["matrnr"] . "</td>";
			$table .= "<td width=\"" . WIDTH_COLUMN_ROOM . "\" align=\"center\">" . $leftCol[$n]["roomname"] . "</td>";
			$table .= "<td width=\"" . WIDTH_COLUMN_PLACE . "\" align=\"center\">" . $leftCol[$n]["place"] . "</td>";
			$table .= "<td width=\"" . WIDTH_COLUMN_MIDDLE . "\" bgcolor=\"#FFFFFF\"></td>";

			if ($n < count($rightCol)) {
				$table .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\">" . $rightCol[$n]["matrnr"] . "</td>";
				$table .= "<td width=\"" . WIDTH_COLUMN_ROOM . "\" align=\"center\">" . $rightCol[$n]["roomname"] . "</td>";
				$table .= "<td width=\"" . WIDTH_COLUMN_PLACE . "\" align=\"center\">" . $rightCol[$n]["place"] . "</td>";
			} else {
				$table .= "<td bgcolor=\"#FFFFFF\" width=\"" . (WIDTH_COLUMN_MATNO + WIDTH_COLUMN_ROOM + WIDTH_COLUMN_PLACE) . "\" colspan=\"3\"></td>";
			}

			$table .= "</tr>";

			$fill = !$fill;
		}

		$table .= "</table>";

		return $table;
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

	public function calculateCellAddress($n){
		if ($n <= 26) return chr(64 + $n);
		else if ($n <= 52) return "A" . $this->calculateCellAddress($n - 26);
		else if ($n <= 78) return "B" . $this->calculateCellAddress($n - 52);
		else if ($n <= 104) return "C" . $this->calculateCellAddress($n - 78);
		else if ($n <= 130) return "D" . $this->calculateCellAddress($n - 104);
		else if ($n <= 156) return "E" . $this->calculateCellAddress($n - 130);
		else if ($n <= 192) return "F" . $this->calculateCellAddress($n - 156);
		else if ($n <= 218) return "G" . $this->calculateCellAddress($n - 192);
		else if ($n <= 244) return "H" . $this->calculateCellAddress($n - 218);
		else if ($n <= 270) return "I" . $this->calculateCellAddress($n - 244);
		else return;
	}
}