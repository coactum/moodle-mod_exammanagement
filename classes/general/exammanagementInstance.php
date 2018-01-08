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
	protected $examtime;
	protected $firstphasecompleted;
	protected $secondphasecompleted;
	protected $thirdphasecompleted;
	protected $fourthphasecompleted;

	public function __construct($id, $e) {
		global $DB, $CFG;

		$this->id=$id;
		$this->e=$e;
		
        if ($id) {
				$this->cm             = get_coursemodule_from_id('exammanagement', $id, 0, false, MUST_EXIST);
				//$course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
				$this->course = get_course($this->cm->course);
				$this->moduleinstance = $DB->get_record('exammanagement', array('id' => $this->cm->instance), '*', MUST_EXIST);
			} else if ($e) {
				$this->moduleinstance = $DB->get_record('exammanagement', array('id' => $e), '*', MUST_EXIST);
				//$course         = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
				$this->course = get_course($this->moduleinstance->course);
				$this->cm             = get_coursemodule_from_instance('exammanagement', $this->moduleinstance->id, $this->course->id, false, MUST_EXIST);
			} else {
				print_error(get_string('missingidandcmid', mod_exammanagement));
			}
			
			require_login($this->course, true, $this->cm);
			
			$this->modulecontext = context_module::instance($this->cm->id);
			
		//set examtime
		
		if ($this->getFieldFromDB('exammanagement','examtime')){
				$this->examtime = $this->getFieldFromDB('exammanagement','examtime');
			} else {
				$this->examtime = '';
			}
		
		
		//check if stages are completed
		$this->firstphasecompleted=$this->checkPhaseCompletion(1);
		$this->secondphasecompleted=$this->checkPhaseCompletion(2);
		$this->thirdphasecompleted=$this->checkPhaseCompletion(3);
		$this->fourthphasecompleted=$this->checkPhaseCompletion(4); 
    }
	
	public function getElement($c){ //if some extern functions need some of the objects params
	
		switch ($c){ //get requested element
		
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
			
	protected function setPage($substring){
		global $PAGE;
		
		// Print the page header.
		$PAGE->set_url('/mod/exammanagement/'.$substring.'.php', array('id' => $this->cm->id));
		$PAGE->set_title(format_string($this->moduleinstance->name).' ('.get_string('modulename','mod_exammanagement').')');
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

	public function outputOverviewPage(){ //needs rework and splitting into separate functions
	
		global $PAGE, $USER;
		
		$this->setPage('view');
		$this-> outputPageHeader();
				
		//rendering and displaying basic content (overview).
		$output = $PAGE->get_renderer('mod_exammanagement');
		$page = new \mod_exammanagement\output\exammanagement_overview($this->cm->id, $this->firstphasecompleted, $this->secondphasecompleted, $this->thirdphasecompleted, $this->fourthphasecompleted, $this->examtime); 
		echo $output->render($page);

		//rendering and displaying debug info (to be moved to renderer)
		if($USER->username=="admin"){
	
			$output = $PAGE->get_renderer('mod_exammanagement');
			$page = new \mod_exammanagement\output\exammanagement_debug_infos($this->id, $this->cm, $this->course, $this->moduleinstance, $this->firstphasecompleted);
			echo $output->render($page);
			echo $this->examtime;
		}
		
		$this->outputFooter();
 	}
 	
 	public function checkPhaseCompletion($phase){
 	
 	switch ($phase){
		
			case 1:
				if ($this->getFieldFromDB('exammanagement','examtime')){
					return "Wert";
					}
				else return false;
			case 2:
				return false;
			case 3:
				return false;
			case 4:
				return false;
 		}
 	
 	}
 	
 	public function startEvent($type){
		
		//events
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
	
	protected function redirectToOverviewPage(){
		global $CFG;
		
		$url=$CFG->wwwroot.'/mod/exammanagement/view.php?id='.$this->id;
	
		redirect ($url);
	}
	
	protected function getFieldFromDB($table, $fieldname){
		global $DB;
	
		$field = $DB->get_field($table, $fieldname, array('id' => $this->cm->instance), '*', MUST_EXIST);
	
		return $field;
	}
	
	protected function UpdateRecordInDB($table, $obj){
		global $DB;
	
		return $DB->update_record($table, $obj);
	}
	
	############## setDateTime methods (maybe moving this to own object?#########
	public function outputDateTimePage(){
		global $PAGE, $USER;
		
		$examtime = optional_param('examtime', 0, PARAM_INT);
		
		if (!$examtime){
		
			$this->setPage('set_date_time');
			$this-> outputPageHeader();
			$this->buildDateTimeForm();
			echo 'formdate Test';

		}
		
		//if called from itself

		if ($examtime){
			// combine day+month+year and save it in DB->date ...
	
			$moduleinstance->examtime=$examtime;
	
			$this->UpdateRecordInDB("exammanagement", $moduleinstance);
			echo 'updatetime Test';
	
			$this->redirectToOverviewPage();

		}

		//rendering and displaying debug info (to be moved to renderer) //eigene Methode
		if($USER->username=="admin"){
	
			$output = $PAGE->get_renderer('mod_exammanagement');
			$page = new \mod_exammanagement\output\exammanagement_debug_infos($this->id,$this->cm,$this->course,$this->moduleinstance, $this->firstphasecompleted);
			echo $output->render($page);
		}

		$this->outputFooter();
	}
	
	protected function buildDateTimeForm(){
		
		//include form
		
		require_once(__DIR__.'/../forms/dateForm.php');
 
		//Instantiate simplehtml_form 
		$mform = new forms\dateForm();
 		var_dump($this);
		//var_dump(get_parent_class ($mform));
			
		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$this->redirectToOverviewPage();
			
		} else if ($fromform = $mform->get_data()) {
		  //In this case you process validated data. $mform->get_data() returns data posted in form.
		
		} else {
		  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
		  // or on the first display of the form.
 
		  //Set default data (if any)
		  //$mform->set_data($toform);
		  
		  //displays the form
		  $mform->display();
		}
	
	}
}