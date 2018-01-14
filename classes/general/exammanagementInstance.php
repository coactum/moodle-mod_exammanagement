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
				print_error(get_string('missingidandcmid', mod_exammanagement));
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
	
	#### multiple #####
			
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
 	
 	protected function redirectToOverviewPage(){
		global $CFG;
		
		$url=$CFG->wwwroot.'/mod/exammanagement/view.php?id='.$this->id;
	
		redirect ($url);
	}


	#### overview ####
	public function outputOverviewPage(){
	
		global $PAGE;
		
		$this->setPage('view');
		$this-> outputPageHeader();
				
		//rendering and displaying content
		$output = $PAGE->get_renderer('mod_exammanagement');
		$page = new \mod_exammanagement\output\exammanagement_overview($this->cm->id, $this->checkPhaseCompletion(1), $this->checkPhaseCompletion(2), $this->checkPhaseCompletion(3), $this->checkPhaseCompletion(4), $this->getHrExamtime()); 
		echo $output->render($page);
		
		$this->debugElementsOverview();
		
		$this->outputFooter();
 	}
 	
 	protected function getExamtime(){		//get examtime (for form)
		if ($this->getFieldFromDB('exammanagement','examtime')){
				return $this->getFieldFromDB('exammanagement','examtime');
			} else {
				return '';
			}
	}
	
	protected function getHrExamtime() {	//convert examtime to human readable format for template
		$examtime=$this->getExamtime();
		if($examtime){
			$hrexamtime=date('d.m.Y', $examtime).', '.date('H:i', $examtime);
			return $hrexamtime;
		} else {
			return '';
		}
 	}
 	
 	protected function checkPhaseCompletion($phase){
 	
 	switch ($phase){
		
			case 1:
				if ($this->getExamtime()){
					return true;
					}
			case 2:
				return false;
			case 3:
				return false;
			case 4:
				return false;
 		}
 	
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
	
	#### DB #####
	
	protected function getFieldFromDB($table, $fieldname){
		global $DB;
	
		$field = $DB->get_field($table, $fieldname, array('id' => $this->cm->instance), '*', MUST_EXIST);
	
		return $field;
	}
	
	protected function UpdateRecordInDB($table, $obj){
		global $DB;
	
		return $DB->update_record($table, $obj);
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
	
			$this->redirectToOverviewPage();

	}
	
	protected function buildDateTimeForm(){
		
		global $CFG;
		
		//include form
		require_once(__DIR__.'/../forms/dateTimeForm.php');
 		
		//Instantiate dateTime_form 
		$mform = new forms\dateTimeForm();
			
		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$this->redirectToOverviewPage();
			
		} else if ($fromform = $mform->get_data()) {
		  //In this case you process validated data. $mform->get_data() returns data posted in form.
		  $this->saveDateTime($fromform->examtime);
		
		} else {
		  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
		  // or on the first display of the form.
 
		  //Set default data (if any)
		  $mform->set_data(array('examtime'=>$this->getExamtime(), 'id'=>$this->id));
		  
		  //displays the form
		  $mform->display();
		}
	
	}
	
	########### debugging ########
	
	public function debugElementsOverview(){
		echo '<h4> Debug-Information </h4>';
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