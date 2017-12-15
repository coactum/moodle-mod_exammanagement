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
	protected $date;
	protected $time;
	protected $day;
	protected $month;
	protected $year;
	protected $hour;
	protected $minute;
	protected $rolestr; //to be deleted
	protected $firststagecompleted; // to be deleted

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
			
			$this->modulecontext = get_context_instance(CONTEXT_MODULE, $this->cm->id); //veraltet, Methode darunter funktioniert aber nicht
			//$this->modulecontext = context_module::instance($this->cm->id);	//not working, $this ersetzen bringt nichts, Problem ist :: Operator
			//$this->modulecontext='1'; //for testing
			
		//set Date and Time values
		
		$this->date = $this->getFieldFromDB('exammanagement','date');
		$this->time = $this->getFieldFromDB('exammanagement','time');
		
		//disassemble date and time (to be deleted if timestampes are used in the future)
		if ($this->date) {
			$datecomponents = explode("-", $this->date);

			$this->day=$datecomponents[2];
			$this->month=$datecomponents[1];
			$this->year=$datecomponents[0];
		}
		else{
			$this->day='';
			$this->month='';
			$this->year='';
		}

		if ($this->time) {
			$timecomponents = explode(":", $this->time);

			$this->hour=$timecomponents[0];
			$this->minute=$timecomponents[1];
		}

		else{
			$this->hour='';
			$this->minute='';
		}
		
		$this->rolestr='Test'; //to be deleted here and in renderer and template because its not needed

		//check if stages are completed (to be moved to own function) 
		$this->firststagecompleted = true; //for testing, later to be calculated depending on if all data is set
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
			
	protected function setPage(){
		global $PAGE;
		
		// Print the page header.
		$PAGE->set_url('/mod/exammanagement/view.php', array('id' => $this->cm->id));
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
		
		$this->setPage();
		$this-> outputPageHeader();
		
		//rendering and displaying basic content (overview).
		$output = $PAGE->get_renderer('mod_exammanagement');
		$page = new \mod_exammanagement\output\exammanagement_overview($this->cm->id, $this->rolestr, $this->firststagecompleted, $this->day, $this->month, $this->year, $this->hour, $this->minute); 
		echo $output->render($page);

		//rendering and displaying debug info (to be moved to renderer)
		if($USER->username=="admin"){
	
			$output = $PAGE->get_renderer('mod_exammanagement');
			$page = new \mod_exammanagement\output\exammanagement_debug_infos($this->id, $this->cm, $this->course, $this->moduleinstance, $this->firststagecompleted, $this->day, $this->month, $this->year, $this->hour, $this->minute);
			echo $output->render($page);
		}
		
		$this-> outputFooter();
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
	
	protected function getFieldFromDB($table, $fieldname){
		global $DB;
	
		$field = $DB->get_field($table, $fieldname, array('id' => $this->cm->instance), '*', MUST_EXIST);
	
		return $field;
	}
}