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
 * Renderer class for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

defined('MOODLE_INTERNAL') || die();
use context_module;
use tcpdf;
use \stdClass;

/**
 * class containing all general functions for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class exammanagementInstance{

	protected $id;
	protected $e;
	protected $cm;
	protected $course;
	protected $moduleinstance;
	protected $modulecontext;

	private function __construct($id, $e) {
		global $DB, $CFG;

		$this->id=$id;
		$this->e=$e;

        if ($id) {
				$this->cm             = get_coursemodule_from_id('exammanagement', $id, 0, false, MUST_EXIST);
				$this->course = get_course($this->cm->course);
				$this->moduleinstance = $DB->get_record('exammanagement', array('id' => $this->cm->instance), '*', MUST_EXIST);
			} else if ($e) {
				$this->moduleinstance = $DB->get_record('exammanagement', array('id' => $e), '*', MUST_EXIST);
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

	#### wrapped general moodle functions #####

	protected function setPage($substring){
		global $PAGE;

		// Print the page header.
		$PAGE->set_url('/mod/exammanagement/'.$substring.'.php', array('id' => $this->cm->id));
		$PAGE->set_title(get_string('modulename','mod_exammanagement').': '.format_string($this->moduleinstance->name));
		$PAGE->set_heading(format_string($this->course->fullname));
		$PAGE->set_context($this->modulecontext);

		/*
		 * Other things you may want to set - remove if not needed.
		 * $PAGE->set_cacheable(false);
		 * $PAGE->set_focuscontrol('some-html-id');
		 * $PAGE->add_body_class('newmodule-'.$somevar);
		 */

	}

	protected function outputPageHeader(){
		global $OUTPUT;
		echo $OUTPUT->header();

		// set basic content (to be moved to renderer that has to define which usecas it is (e.g. overview, subpage, debug infos etc.)
		echo $OUTPUT->heading(get_string('maintitle', 'mod_exammanagement'));

		// Conditions to show the intro can change to look for own settings or whatever.
 		if ($this->moduleinstance->intro) {
     		echo $OUTPUT->box(format_module_intro('exammanagement', $this->moduleinstance, $this->cm->id), 'generalbox mod_introbox', 'newmoduleintro');
 		}
 	}

 	protected function outputFooter(){

 		global $OUTPUT;

		// Finish the page.
		echo $OUTPUT->footer();

 	}

 	public function getModuleUrl($component){

 		global $CFG;

 		$url=$CFG->wwwroot.'/mod/exammanagement/'.$component.'.php?id='.$this->id;

 		return $url;
 	}

 	public function redirectToOverviewPage($anchor, $message, $type){

		$url = $this->getModuleUrl('view');

		if ($anchor){
				$url .= '#'.$anchor;
		}

		switch ($type) {
    		case 'success':
        		redirect ($url, $message, null, \core\output\notification::NOTIFY_SUCCESS);
        		break;
    		case 'warning':
        		redirect ($url, $message, null, \core\output\notification::NOTIFY_WARNING);
        		break;
    		case 'error':
        		redirect ($url, $message, null, \core\output\notification::NOTIFY_ERROR);
        		break;
        	case 'info':
        		redirect ($url, $message, null, \core\output\notification::NOTIFY_INFO);
        		break;
        	default:
        		redirect ($url, $message, null, \core\output\notification::NOTIFY_ERROR);
        		break;
		}
	}

	public function checkCapability($capname){
				if (has_capability($capname, $this->modulecontext)){
						return true;
				} else {
						return false;
				}
		}

	public function ConcatHelptextStr($langstr){

		$helptextstr= '';
		$helptextstr.= '<div class="alert alert-info alert-dismissible fade in" role="alert">';
		$helptextstr.= '<div class="helptextbox">';
		$helptextstr.= '<div class="helptextboxcontent">';
		$helptextstr.= '<div class="row">';
		$helptextstr.= '<h4 class="alert-heading col-xs-11">'.get_string('helptext_str', 'mod_exammanagement').'</h4>';
		$helptextstr.= '<button type="button" class="close col-xs-1" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
		$helptextstr.= '</div>';
		$helptextstr.= '<p>'.get_string('helptext_'.$langstr, 'mod_exammanagement').'</p>';
		$helptextstr.= '<hr>';
		$helptextstr.= '<p class="mb-0">'.get_string('helptext_link', 'mod_exammanagement').' <a href="https://hilfe.uni-paderborn.de/PANDA" class="alert-link" target="_blank">https://hilfe.uni-paderborn.de/PANDA</a></p>';
		$helptextstr.= '<p class="read-more"><a href="#" onclick="return showMore()" class="button">Mehr lesen</a></p>';
		$helptextstr.= '</div>';
		$helptextstr.= '</div>';
		$helptextstr.= '</div>';

		$helptextstr.= <<< EOF
<script>
showMore = function() {

			helptextboxcontent = jQuery.find('.helptextboxcontent');
			helptextboxcontentheight = jQuery(helptextboxcontent).outerHeight();

			helptextbox = jQuery.find('.helptextbox');
			jQuery(helptextbox).css({
			      "height": jQuery(helptextbox).height(),
			      "max-height": 9999
			    })
			    .animate({
			      "height": helptextboxcontentheight
			    });


			readMore = jQuery.find('.read-more');
			jQuery(readMore).fadeOut();
			return false;	    
		};
</script>
EOF;

		return $helptextstr;

	}

	#### overview ####

	public function outputOverviewPage($calledfromformdt, $datetimevisible, $calledfromformrp, $roomplacevisible){

		global $PAGE;

		require_capability('mod/exammanagement:viewinstance', $this->modulecontext);

		if($calledfromformdt&&$this->checkCapability('mod/exammanagement:adddefaultrooms')){
			$this->saveStateOfDateTimeVisibility($datetimevisible);

		}

		if($calledfromformrp&&$this->checkCapability('mod/exammanagement:adddefaultrooms')){
			$this->saveStateOfRoomPlaceVisibility($roomplacevisible);

		}

		$this->setPage('view');
		$this-> outputPageHeader();

		//rendering and displaying content
		$output = $PAGE->get_renderer('mod_exammanagement');
		$page = new \mod_exammanagement\output\exammanagement_overview($this->cm->id, $this->checkPhaseCompletion(1), $this->checkPhaseCompletion(2), $this->checkPhaseCompletion(3), $this->checkPhaseCompletion(4), $this->getHrExamtime(), $this->getShortenedTextfield(), $this->getParticipantsCount(), $this->getRoomsCount(), $this->getChoosenRoomNames(), $this->isStateOfPlacesCorrect(), $this->isStateOfPlacesError(), $this->isDateTimeVisible(),$this->isRoomPlaceVisible());
		echo $output->render($page);

		//$this->debugElementsOverview();

		$this->outputFooter();
 	}

 	protected function getExamtime(){		//get examtime (for form)
		if ($this->getFieldFromDB('exammanagement','examtime', array('id' => $this->cm->instance))){
				return $this->getFieldFromDB('exammanagement','examtime', array('id' => $this->cm->instance));
			} else {
				return false;
			}
	}

	protected function getHrExamtime() {	//convert examtime to human readable format for template
		$examtime=$this->getExamtime();
		if($examtime){
			$hrexamtime = date('d.m.Y', $examtime).', '.date('H:i', $examtime);
			return $hrexamtime;
		} else {
			return false;
		}
 	}

 	protected function getTextfieldObject(){

 		$textfield= $this->getFieldFromDB('exammanagement','textfield', array('id' => $this->cm->instance));

		$textfield =json_decode($textfield);

		return $textfield;
	}

 	protected function getTextFromTextfield(){

 		$textfield= $this->getTextfieldObject('exammanagement','textfield', array('id' => $this->cm->instance));
		if ($textfield){
				$text=$textfield->text;
				return $text;
			} else {
				return false;
			}
	}

	protected function getFormatFromTextfield(){

 		$textfield= $this->getTextfieldObject('exammanagement','textfield', array('id' => $this->cm->instance));
		if ($textfield){
				$format=$textfield->format;
				return $format;
			} else {
				return false;
			}
	}

	protected function getShortenedTextfield(){
		$textfield=format_string($this->getTextFromTextfield());

		if ($textfield && strlen($textfield)>49){
				$shtextfield=substr($textfield, 0, 49).' ...';
				return $shtextfield;
			} elseif($textfield) {
				return $textfield;
			} else{
				return false;
			}
	}

	public function getParticipantsCount(){
		$participants=$this->getFieldFromDB('exammanagement','participants', array('id' => $this->cm->instance));
		if ($participants){
				$temp= json_decode($participants);
				$participantsCount=count($temp);
				return $participantsCount;
			} else {
				return false;
		}
	}

	public function getRoomsCount(){
		$rooms = $this->getFieldFromDB('exammanagement','rooms', array('id' => $this->cm->instance));
		if ($rooms){
				$roomsArr = json_decode($rooms);
				$roomsCount = count($roomsArr);
				return $roomsCount;
			} else {
				return false;
		}
	}

	public function getChoosenRoomNames(){
		$rooms = $this->getFieldFromDB('exammanagement','rooms', array('id' => $this->cm->instance));
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

 	protected function checkPhaseCompletion($phase){

 	switch ($phase){

			case 1:
				if ($this->getRoomsCount()&&$this->getExamtime()&&$this->getParticipantsCount()){
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

	public function outputParticipantsView(){

		global $PAGE;

		require_capability('mod/exammanagement:viewparticipantspage', $this->modulecontext);

		$this->setPage('view');
		$this-> outputPageHeader();

		//rendering and displaying content
		$output = $PAGE->get_renderer('mod_exammanagement');
		$page = new \mod_exammanagement\output\exammanagement_participantsview($this->cm->id, $this->isParticipant(), $this->getDateForParticipants(), $this->getTimeForParticipants(), $this->getRoomForParticipants(), $this->getPlaceForParticipants(), $this->getTextFromTextfield());
		echo $output->render($page);

		$this->outputFooter();
 	}

	protected function isParticipant(){

			global $USER;

			$participantsList = json_decode($this->getFieldFromDB('exammanagement', 'participants', array('id' => $this->cm->instance)));

			if ($participantsList){
					foreach ($participantsList as $key => $value){

							if($USER->id == $value){

									return true;
							}
					}

					return false;
			}
	}

	protected function getDateForParticipants(){

			$dateState = $this->getFieldFromDB('exammanagement','datetimevisible', array('id' => $this->cm->instance));
			$examtime = $this->getExamtime();

			if($dateState && $examtime){
						return date('d.m.Y', $examtime);
			} else{
						return false;
			}
	}

	protected function getTimeForParticipants(){

			$timeState = $this->isDateTimeVisible();
			$examtime = $this->getExamtime();

			if($timeState && $examtime){
						return date('H:i', $examtime);
			} else{
						return false;
			}
	}

	protected function getRoomForParticipants(){

			global $USER;

			$roomState = $this->isRoomPlaceVisible();
			$assignmentArray = $this->getAssignedPlaces();
			$participantsRoom =  false;

			if($roomState && $assignmentArray){
						foreach ($assignmentArray as $key => $assignment){
								if ($assignment->userid == $USER->id){
										$participantsRoom = $assignment->roomname;
								}
						}

						return $participantsRoom;

			} else{
						return false;
			}
	}

	protected function getPlaceForParticipants(){

			global $USER;

			$placesState = $this->isRoomPlaceVisible();
			$assignmentArray = $this->getAssignedPlaces();
			$participantsPlace =  false;

			if($placesState && $assignmentArray){
						foreach ($assignmentArray as $key => $assignment){
								if ($assignment->userid == $USER->id){
										$participantsPlace = $assignment->place;
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

		switch ($type){

			case 'view':
				$event = \mod_exammanagement\event\course_module_viewed::create(array(
					'objectid' => $this->moduleinstance->id,
					'context' => $this->modulecontext
				));
				$event->add_record_snapshot('course', $this->course);
				$event->add_record_snapshot('exammanagement', $this->moduleinstance);
				$event->trigger();
		}
	}

	#### wrapped Moodle DB functions #####

	protected function getFieldFromDB($table, $fieldname, $condition){
		global $DB;

		$field = $DB->get_field($table, $fieldname, $condition, '*', MUST_EXIST);

		return $field;
	}

	protected function getRecordFromDB($table, $condition){
		global $DB;

		$record = $DB->get_record($table, $condition);

		return $record;
	}

	protected function getRecordsFromDB($table, $condition){
		global $DB;

		$records = $DB->get_records($table, $condition);

		return $records;
	}

	protected function UpdateRecordInDB($table, $obj){
		global $DB;

		return $DB->update_record($table, $obj);
	}

	protected function InsertRecordInDB($table, $dataobject){
		global $DB;

		return $DB->insert_record($table, $dataobject, $returnid=true, $bulk=false);
	}

	protected function InsertBulkRecordsInDB($table, $dataobjects){
		global $DB;

		$DB->insert_records($table, $dataobjects);
	}

	######### feature: chooseRooms ##########

	public function outputchooseRoomsPage(){
		global $PAGE;

		$this->setPage('chooseRooms');
		$this-> outputPageHeader();
		$this->buildchooseRoomsForm();

		$this->outputFooter();
	}

	protected function saveRooms($roomsArr){

		$rooms=json_encode($roomsArr);

		$this->moduleinstance->rooms=$rooms;

		$this->UpdateRecordInDB("exammanagement", $this->moduleinstance);

		$this->redirectToOverviewPage('beforeexam', 'Räume für die Prüfung wurden ausgewählt', 'success');

	}

	public function getRoomObj($roomID){
		$room = $this->getRecordFromDB('exammanagement_rooms', array('id' => $roomID));;

		return $room;
	}

	public function getAllRoomIDs($format){ //not used at the moment, use getAllRoomsIDsSortedByName() instead
		$allRooms = $this->getRecordsFromDB('exammanagement_rooms', array());
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
		$allRooms = $this->getRecordsFromDB('exammanagement_rooms', array());
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

	protected function buildchooseRoomsForm(){

		//include form
		require_once(__DIR__.'/../forms/chooseRoomsForm.php');

		//Instantiate Textfield_form
		$mform = new forms\chooseRoomsForm(null, array('id'=>$this->id, 'e'=>$this->e));

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$this->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

		} else if ($fromform = $mform->get_data()) {
		  //In this case you process validated data. $mform->get_data() returns data posted in form.

		  $rooms=$this->filterCheckedRooms($fromform);

		  $this->saveRooms($rooms);

		} else {
		  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
		  // or on the first display of the form.

		  //Set default data (if any)
		  $mform->set_data(array('id'=>$this->id));

		  //displays the form
		  $mform->display();
		}

	}

	public function getSavedRooms(){

		$rooms = $this->getFieldFromDB('exammanagement','rooms', array('id' => $this->cm->instance));

		if ($rooms){
				$roomsArray = json_decode($rooms);
				return $roomsArray;
			} else {
				return false;
		}
	}

	protected function isDateTimeVisible(){

		$isDateTimeVisible = $this->getFieldFromDB('exammanagement','datetimevisible', array('id' => $this->cm->instance));

		if($isDateTimeVisible){
				return true;
		} else {
				return false;
		}
	}

	protected function isRoomPlaceVisible(){

		$isRoomPlaceVisible = $this->getFieldFromDB('exammanagement','roomplacevisible', array('id' => $this->cm->instance));

		if($isRoomPlaceVisible){
				return true;
		} else {
				return false;
		}

	}

	############## feature: add default rooms ############

	public function addDefaultRooms(){

		global $CFG;

		//$records= array();

		$defaultRoomsFile = file($CFG->wwwroot.'/mod/exammanagement/data/rooms.txt');

		foreach ($defaultRoomsFile as $key => $roomstr){

			$roomParameters=explode('+', $roomstr);

			$roomObj = new stdClass();
			$roomObj->roomid=$roomParameters[0];
			$roomObj->name=$roomParameters[1];
 			$roomObj->description=$roomParameters[2];

			$svgStr = base64_encode($roomParameters[3]);

 			$roomObj->seatingplan = $svgStr;
 			$roomObj->places = $roomParameters[4];
			$roomObj->type = 'defaultroom';
 			$roomObj->misc = NULL;

 			//array_push($records, $roomObj);

			$this->InsertRecordInDB('exammanagement_rooms', $roomObj); // bulkrecord insert too big
		}

		//$this->InsertBulkRecordsInDB('exammanagement_rooms', $records); // to big for bulkrecord insert

		$this->redirectToOverviewPage('beforeexam', 'Standardräume angelegt', 'success');

	}

	############## feature: setDateTime #########
	public function outputDateTimePage(){
		global $PAGE;

		$this->setPage('set_date_time');
		$this-> outputPageHeader();
		$this->buildDateTimeForm();

		$this->outputFooter();
	}

	protected function saveDateTime($examtime){

			$this->moduleinstance->examtime=$examtime;

			$this->UpdateRecordInDB("exammanagement", $this->moduleinstance);

			$this->redirectToOverviewPage('beforeexam', 'Datum und Uhrzeit erfolgreich gesetzt', 'success');

	}

	protected function resetDateTime(){

		$this->UpdateRecordInDB("exammanagement", NULL);

		$this->redirectToOverviewPage('beforeexam', 'Datum und Uhrzeit erfolgreich zurückgesetzt', 'success');
	}

	protected function buildDateTimeForm(){

		//include form
		require_once(__DIR__.'/../forms/dateTimeForm.php');

		//Instantiate dateTime_form
		$mform = new forms\dateTimeForm();

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$this->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

		} else if ($fromform = $mform->get_data()) {
		  //In this case you process validated data. $mform->get_data() returns data posted in form.

			if (!empty($fromform->resetdatetime)) { // not working
    		$this->resetDateTime();
  		} else {
				$this->saveDateTime($fromform->examtime);
			}

		} else {
		  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
		  // or on the first display of the form.

		  //Set default data (if any)
		  $mform->set_data(array('examtime'=>$this->getExamtime(), 'id'=>$this->id));

		  //displays the form
		  $mform->display();
		}

	}

######### feature: addParticipants ##########

	public function outputaddParticipantsPage(){
		global $PAGE;

		$this->setPage('addParticipants');
		$this-> outputPageHeader();
		$this->buildaddParticipantsForm();

		$this->outputFooter();
	}

	protected function saveParticipants($participantsArr){

			$participants=json_encode($participantsArr);

			$this->moduleinstance->participants = $participants;
			$this->moduleinstance->userinformation = $this->setUsersInformationPO($participantsArr);

			$this->UpdateRecordInDB("exammanagement", $this->moduleinstance);

			$this->redirectToOverviewPage('beforeexam', 'Teilnehmer zur Prüfung hinzugefügt', 'success');

	}

	public function getCourseParticipantsIDs($format){
			$CourseParticipants = get_enrolled_users($this->modulecontext, 'mod/exammanagement:takeexams'); //sorted by last Name
			$CourseParticipantsID;

			foreach ($CourseParticipants as $key => $value){
				$temp=get_object_vars($value);
				$CourseParticipantsID[$key] = $temp['id'];
			}

			if ($format=='String'){
				$CourseParticipantsID = implode(',', $CourseParticipantsID);
			}

			return $CourseParticipantsID;


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

			sort($participants); //sort checked participants ids for saving in DB

			return $participants;

	}

	protected function buildaddParticipantsForm(){

		//include form
		require_once(__DIR__.'/../forms/addParticipantsForm.php');

		//Instantiate Textfield_form
		$mform = new forms\addParticipantsForm(null, array('id'=>$this->id, 'e'=>$this->e));

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$this->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

		} else if ($fromform = $mform->get_data()) {
		  //In this case you process validated data. $mform->get_data() returns data posted in form.

		  $participants=$this->filterCheckedParticipants($fromform);

		  $this->saveParticipants($participants);

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

	public function getSavedParticipants(){

		$participants = $this->getFieldFromDB('exammanagement','participants', array('id' => $this->cm->instance));

		if ($participants){
				$participantsArray = json_decode($participants);
				return $participantsArray;
			} else {
				return false;
		}
	}

	public function getUser($userid){

		$user = $this->getRecordFromDB('user', array('id'=>$userid));

		return $user;

	}

	public function getUserPicture($userid){

		global $OUTPUT;

		$user = $this->getUser($userid);
		return $OUTPUT->user_picture($user, array('courseid' => $this->course->id, 'link' => true));

	}

	public function getUserProfileLink($userid){

		global $CFG;

		$user = $this->getUser($userid);
		$profilelink = '<strong><a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$this->course->id.'">'.fullname($user).'</a></strong>';

		return $profilelink;

	}

	public function getUserMatrNrPO($userid){

		$usersinformation = json_decode($this->getUsersInformationPO());

		$userMatrNr = '-';

		if ($usersinformation){
			foreach($usersinformation as $key => $user){
					if ($user->moodleid == $userid){
							$userMatrNr = $user->matrNr;
					}

			}
		}

		return $userMatrNr;

	}

	public function getParticipantsGroupNames($userid){

		global $CFG;

		$userGroups = groups_get_user_groups($this->course->id, $userid);
		$groupNameStr = false;

		foreach ($userGroups as $key => $value){
			if ($value){
				foreach ($value as $key2 => $value2){
					$groupNameStr.='<strong><a href="'.$CFG->wwwroot.'/user/index.php?id='.$this->course->id.'&amp;group='.$value2.'">'.groups_get_group_name($value2).'</a></strong>, ';
				}
			}
			else{
				$groupNameStr='-';
				break;
			}
		}

		return $groupNameStr;

	}

	public function assignMatrNrToUser($userid){

			$user = $this->getUser($userid); // for temp matrNr

			// constructing test MatrN., later needs to be readed from csv-File

			$matrNr = 70 . $user->id;;

			$array = str_split($user->firstname);

			$matrNr .= ord($array[0]);
			$matrNr .= ord($array[2]);

			$matrNr = substr($matrNr, 0, 6);

			return $matrNr;

	}

	public function getUsersInformationPO(){
			$usersinformation = $this->getFieldFromDB('exammanagement','userinformation', array('id' => $this->cm->instance));
			return $usersinformation;
	}


	public function setUsersInformationPO($participantsArray){ //needs Array of moodle ids at the moment, later other mapping neccessary
			$usersInformationArray = array();

			foreach($participantsArray as $key => $participantID){

					array_push($usersInformationArray, $this->setUserInformationPO($participantID, $this->assignMatrNrToUser($participantID)));

			}

			$usersInformation = json_encode($usersInformationArray);

			return $usersInformation;

	}

	public function setUserInformationPO($uid, $matrNr){
			$user = new stdClass;
			 $user->moodleid = $uid;
			 $user->matrNr = $matrNr;
			return $user;
	}

	######### feature: textfield ##########

	public function outputTextfieldPage(){
		global $PAGE;

		$this->setPage('textfield');
		$this-> outputPageHeader();
		$this->buildTextfieldForm();

		$this->outputFooter();
	}

	protected function saveTextfield($fromform){

			$textfield=json_encode($fromform->textfield);

			$this->moduleinstance->textfield=$textfield;

			$this->UpdateRecordInDB("exammanagement", $this->moduleinstance);

			$this->redirectToOverviewPage('beforeexam', 'Inhalt gespeichert', 'success');

	}

	protected function buildTextfieldForm(){

		//include form
		require_once(__DIR__.'/../forms/textfieldForm.php');

		//Instantiate Textfield_form
		$mform = new forms\textfieldForm();

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$this->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

		} else if ($fromform = $mform->get_data()) {
		  //In this case you process validated data. $mform->get_data() returns data posted in form.

		  $this->saveTextfield($fromform);

		} else {
		  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
		  // or on the first display of the form.

		  //Set default data (if any)

		  if ($this->getTextFromTextfield() && $this->getFormatFromTextfield()){
		 		$mform->set_data(array('textfield'=>['text' => $this->getTextFromTextfield(), 'format' => $this->getFormatFromTextfield()], 'id'=>$this->id));
		  } else {
				$mform->set_data(array('id'=>$this->id));
		  }


		  //displays the form
		  $mform->display();
		}

	}

	########### Send Groupmessage to all Participants ####

	public function outputGroupmessagesPage(){
		global $PAGE;

		if(!$this->getParticipantsCount()){
			$this->redirectToOverviewPage('beforexam', 'Es müssen erst Teilnehmer zur Prüfung hinzugefügt werden, bevor an diese eine Nachricht gesendet werden kann.', 'error');
		}

		$this->setPage('groupmessage');
		$this-> outputPageHeader();
		$this->buildGroupmessagesForm();

		$this->outputFooter();
	}

	protected function buildGroupmessagesForm(){

		//include form
		require_once(__DIR__.'/../forms/groupmessagesForm.php');

		//Instantiate Textfield_form
		$mform = new forms\groupmessagesForm(null, array('id'=>$this->id, 'e'=>$this->e));

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$this->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

		} else if ($fromform = $mform->get_data()) {
		  //In this case you process validated data. $mform->get_data() returns data posted in form.

		  $this->sendGroupMessage($fromform->groupmessages_subject, $fromform->groupmessages_content);
		  $this->redirectToOverviewPage('beforeexam', 'Nachricht verschickt', 'success');

		} else {
		  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
		  // or on the first display of the form.

		  //Set default data (if any)
		  $mform->set_data(array('id'=>$this->id));

		  //displays the form
		  $mform->display();
		}

	}

	public function sendGroupMessage($subject, $content){

		$mailsubject="PANDA - Prüfungsorganisation: Kurs ".$this->course->fullname.' Betreff: '.$subject;
		$mailtext=$content;
		$participants=$this->getSavedParticipants();

		foreach ($participants as $key => $value){

			$user=$this->getUser($value);

			$this->sendSingleMessage($user, $mailsubject, $mailtext);

		}

		$this->redirectToOverviewPage('beforeexam', 'Nachricht erfolgreich versendet.', 'success');

	}

	protected function sendSingleMessage($user, $subject, $text){

		global $USER, $CFG;

		$message = new \core\message\message();
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
		$url=$CFG->wwwroot.'/mod/exammanagement/view.php?id='.$this->id;
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

	public function assignPlaces(){

		$choosenRoomsArray = $this->getSavedRooms();
		$UserIDsArray = $this->getSavedParticipants();
		$newAssignmentObj = '';
		$assignmentArray = array();

		if(!$choosenRoomsArray){
			$this->unsetStateOfPlaces('error');
			$this->redirectToOverviewPage('forexam', 'Noch keine Räume ausgewählt. Fügen Sie mindestens einen Raum zur Prüfung hinzu und starten Sie die automatische Sitzplatzzuweisung erneut.', 'error');

		} elseif(!$UserIDsArray){
			$this->unsetStateOfPlaces('error');
			$this->redirectToOverviewPage('forexam', 'Noch keine Benutzer zur Prüfung hinzugefügt. Fügen Sie mindestens einen Benutzer zur Prüfung hinzu und starten Sie die automatische Sitzplatzzuweisung erneut.', 'error');

		}

		foreach($choosenRoomsArray as $key => $roomID){

			$RoomObj = $this->getRoomObj($roomID);		//get current Room Object

			$Places = json_decode($RoomObj->places);	//get Places of this Room

			foreach($Places as $key => $placeID){
				$currentUserID = array_pop($UserIDsArray);

				$newAssignmentObj = $this->assignPlaceToUser($currentUserID, $RoomObj->roomid, $RoomObj->name, $placeID);
				array_push($assignmentArray, $newAssignmentObj);

				if(!$UserIDsArray){						//if all users have a place: stop
					break 2;
				}
			}

			if(!$UserIDsArray){						//if all users have a place: stop
				break;
			}
		}

		if($UserIDsArray){								//if users are left without a room
			$this->unsetStateOfPlaces('error');
			$this->redirectToOverviewPage('forexam', 'Einige Benutzer haben noch keinen Sitzplatz. Fügen Sie ausreichend Räume zur Prüfung hinzu und starten Sie die automatische Sitzplatzzuweisung erneut.', 'error');

		}

		$this->moduleinstance->stateofplaces='set';

		$this->savePlacesAssignment($assignmentArray);

		$this->UpdateRecordInDB("exammanagement", $this->moduleinstance);

		$this->redirectToOverviewPage('forexam', 'Plätze zugewiesen', 'success');

	}

	protected function assignPlaceToUser($userid, $roomid, $roomname, $place){

		$assignment = new stdClass();

		$assignment->userid = $userid;
		$assignment->roomid = $roomid;
		$assignment->roomname = $roomname;
		$assignment->place = $place;

		 return $assignment;
	}

	protected function savePlacesAssignment($assignmentArray){

		$this->moduleinstance->assignedplaces=json_encode($assignmentArray);

		$this->UpdateRecordInDB("exammanagement", $this->moduleinstance);

	}

	protected function unsetStateofPlaces($type){
		$this->moduleinstance->stateofplaces=$type;
		$this->UpdateRecordInDB("exammanagement", $this->moduleinstance);
	}

	protected function getStateOfPlaces(){

		$StateOfPlaces = $this->getFieldFromDB('exammanagement','stateofplaces', array('id' => $this->cm->instance));

		return $StateOfPlaces;

	}

	protected function getAssignedPlaces(){

		$getAssignedPlaces = json_decode($this->getFieldFromDB('exammanagement','assignedplaces', array('id' => $this->cm->instance)));

		return $getAssignedPlaces;

	}

	########### show Information to users ##############

	protected function saveStateOfDateTimeVisibility($datetimevisible){

			$this->moduleinstance->datetimevisible=$datetimevisible;

			$this->UpdateRecordInDB("exammanagement", $this->moduleinstance);

			$this->redirectToOverviewPage('forexam', 'Informationen sichtbar geschaltet', 'success');

	}

	protected function saveStateOfRoomPlaceVisibility($roomplacevisible){

			$this->moduleinstance->roomplacevisible=$roomplacevisible;

			$this->UpdateRecordInDB("exammanagement", $this->moduleinstance);

			$this->redirectToOverviewPage('forexam', 'Informationen sichtbar geschaltet', 'success');

	}

	########### Export PDFS ####

	public function exportDemoPDF(){

		global $CFG;

		if(!$this->isStateOfPlacesCorrect() || !$this->isStateOfPlacesError()){
			$this->redirectToOverviewPage('forexam', 'Noch keine Sitzplätze zugewiesen. Sitzplanexport noch nicht möglich', 'error');
		}

		//============================================================+
		// File name   : example_001.php
		// Begin       : 2008-03-04
		// Last Update : 2013-05-14
		//
		// Description : Example 001 for TCPDF class
		//               Default Header and Footer
		//
		// Author: Nicola Asuni
		//
		// (c) Copyright:
		//               Nicola Asuni
		//               Tecnick.com LTD
		//               www.tecnick.com
		//               info@tecnick.com
		//============================================================+

		/**
		 * Creates an example PDF TEST document using TCPDF
		 * @package com.tecnick.tcpdf
		 * @abstract TCPDF - Example: Default Header and Footer
		 * @author Nicola Asuni
		 * @since 2008-03-04
		 */

		// Include the main TCPDF library (search for installation path).
		require_once(__DIR__.'/../../../../config.php');
		require_once($CFG->libdir.'/pdflib.php');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Nicola Asuni');
		$pdf->SetTitle('TCPDF Example 001');
		$pdf->SetSubject('TCPDF Tutorial');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
		$pdf->setFooterData(array(0,64,0), array(0,64,128));

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(__DIR__.'/lang/eng.php')) {
			require_once(__DIR__.'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('helvetica', 'BI', 12);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();

		// set text shadow effect
		$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

		// Set some content to print
		$html = 'Hallo';

		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

		// ---------------------------------------------------------

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$pdf->Output('example_001.pdf', 'D');

		//============================================================+
		// END OF FILE
		//============================================================+

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