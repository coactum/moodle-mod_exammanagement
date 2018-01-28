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
 * class containing dateForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
namespace mod_exammanagement\general\forms;
use moodleform;
use mod_exammanagement;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");
 
class addParticipantsForm extends moodleform {
    
    //Add elements to form
    public function definition() {
 
        $mform = $this->_form; // Don't forget the underscore! 
        
        $mform->addElement('hidden', 'id', 'dummy');
		$mform->setType('id', PARAM_INT);
		
		$mform->addElement('html', '<h3>Teilnehmer hinzufügen</h3>');
		$mform->addElement('html', '<p>Teilnehmer aus dem Kurs zur Prüfung hinzufügen.</p>');	
		$mform->addElement('html', '<div class="row"><div class="col-xs-3"><h4 class="text-center">Auswahl</h4></div><div class="col-xs-3"><h4 class="text-center">Teilnehmer</h4></div><div class="col-xs-3"><h4 class="text-center">Benutzerbild</h4></div><div class="col-xs-3"><h4 class="text-center">Gruppen</h4></div>');

 		$obj=\mod_exammanagement\general\exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
 		$allCourseParticipantsIDs= $obj->getCourseParticipantsIDs('Array');
 		$checkedParticipantsIDs = $obj->getSavedParticipants();
 		
 		foreach($allCourseParticipantsIDs as $key => $value){
			$mform->addElement('html', '<div class="row"><div class="col-xs-3">');
			$mform->addElement('advcheckbox', 'participants['.$value.']', '', null, array('group' => 1));
			$mform->addElement('html', '</div>'.$obj->getParticipantDataAsStr($value).'</div>');
			
			foreach($checkedParticipantsIDs as $key2 => $value2){
				if($allCourseParticipantsIDs[$key]==$value2){
					$mform->setDefault('participants['.$value.']', true);
				}
			}
					
 		}
 		
 		//$this->add_checkbox_controller(1, 'Alle auswählen', ''); //klappt nicht wenn unterschiedliche values Werte für checked bei verbundenen Checkboxes

		$this->add_action_buttons(true,'Zur Prüfung hinzufügen');
    }
    
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}