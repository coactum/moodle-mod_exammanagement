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
    
        global $PAGE, $CFG;
 
        $mform = $this->_form; // Don't forget the underscore! 
        
        $mform->addElement('hidden', 'id', 'dummy');
		$mform->setType('id', PARAM_INT);
		
		$mform->addElement('html', '<h3>Kursteilnehmer hinzufügen</h3>');

		###### add Participants from Course ######
		$mform->addElement('html', '<div class="row"><div class="col-xs-8"><p class="Course">Teilnehmer aus dem Kurs zur Prüfung hinzufügen.</p></div><div class="col-xs-4"><label class="exammanagement-switch pull-right" title="Umschalten zwischen Kurs- und Dateiimport"><input type="checkbox"><span class="exammanagement-slider round"></span></label></div>');	
		$mform->addElement('html', '<div class="course"><div class="row"><div class="col-xs-3"><h4 class="text-center">Auswahl</h4></div><div class="col-xs-3"><h4 class="text-center">Teilnehmer</h4></div><div class="col-xs-3"><h4 class="text-center">Benutzerbild</h4></div><div class="col-xs-3"><h4 class="text-center">Gruppen</h4></div>');

 		$obj=\mod_exammanagement\general\exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
 		$allCourseParticipantsIDs= $obj->getCourseParticipantsIDs('Array');
 		$checkedParticipantsIDs = $obj->getSavedParticipants();
 		
 		$PAGE->requires->js_call_amd('mod_exammanagement/select_all_choices', 'enable_cb'); //call jquery for checking all checkboxes via following checkbox

 		$mform->addElement('html', '<div class="row"><div class="col-xs-3">');
		$mform->addElement('advcheckbox', 'checkall', 'Alle aus-/abwählen', null, array('group' => 1, 'id' => 'checkboxgroup1',));			
		$mform->addElement('html', '</div><div class="col-xs-3"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');
 		
 		foreach($allCourseParticipantsIDs as $key => $value){
			$mform->addElement('html', '<div class="row"><div class="col-xs-3">');
			$mform->addElement('advcheckbox', 'participants['.$value.']', '', null, array('group' => 1));
			$mform->addElement('html', '</div>'.$obj->getParticipantDataAsStr($value).'</div>');
			
			if($checkedParticipantsIDs){
				foreach($checkedParticipantsIDs as $key2 => $value2){
					if($allCourseParticipantsIDs[$key]==$value2){
						$mform->setDefault('participants['.$value.']', true);
					}
				}
			}		
 		}

		$mform->addElement('html', '</div></div></div><h3>Weitere Teilnehmer hinzufügen</h3>');
		
		###### add Participants from File ######
		
		$maxbytes=$CFG->maxbytes;

		$mform->addElement('html', '<div class="file">');
		$mform->addElement('filepicker', 'userfile', 'Externe Teilnehmer aus Excel-Datei importieren (Matrikelnummern in beliebiger Spalte) und zur Prüfung hinzufügen.', null, array('maxbytes' => $maxbytes, 'accepted_types' => '.csv'));
		
		$mform->addElement('filepicker', 'userfile', 'Externe Teilnehmer von aus PAUL exportierter Datei importieren (Einträge mit Tabulator getrennt; die ersten zwei Zeilen enthalten Prüfungsinformationen) und zur Prüfung hinzufügen.', null, array('maxbytes' => $maxbytes, 'accepted_types' => '.txt'));
		$mform->addElement('html', '</div>');

		### action buttons ###
		$this->add_action_buttons(true,'Zur Prüfung hinzufügen');

    }
    
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}