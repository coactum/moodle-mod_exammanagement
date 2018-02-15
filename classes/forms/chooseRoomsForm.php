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
 * class containing chooseRoomsForm for exammanagement
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
 
class chooseRoomsForm extends moodleform {
    
    //Add elements to form
    public function definition() {
    
        global $PAGE, $CFG;
        
        $PAGE->requires->js_call_amd('mod_exammanagement/select_all_choices', 'enable_cb'); //call jquery for checking all checkboxes via following checkbox
        $PAGE->requires->js_call_amd('mod_exammanagement/switch_mode_rooms', 'switch_mode'); //call jquery for switching between course import and import from file
 
		$obj=\mod_exammanagement\general\exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
		
        $mform = $this->_form; // Don't forget the underscore! 
        
        $mform->addElement('hidden', 'id', 'dummy');
		$mform->setType('id', PARAM_INT);
		
		$mform->addElement('html', '<div class="row"><div class="col-xs-6">');
		$mform->addElement('html', '<h3 class="choose">Räume auswählen</h3>');
		$mform->addElement('html', '<h3 class="import">Neue Räume hinzufügen</h3>');

		$mform->addElement('html', '</div><div class="col-xs-6"><button type="button" id="switch_mode_rooms" class="btn btn-primary" title="Umschalten zwischen Raumwahl und -import"><span class="import">Raumwahl</span><span class="choose">Neue Räume hinzufügen</span></button>');
		$mform->addElement('html', '<a href="'.$obj->getModuleUrl("addDefaultRooms").'" class="btn btn-primary import" title="Standardräume import"><span>Standardräume importieren</span></a></div>');
		$mform->addElement('html', '<p class="choose">Räume für die Prüfung auswählen (Standardräume oder Benutzerdefinierte).</p><p class="import">Neue Prüfungsräume anlegen.</p>');	
		
		###### chooseRooms ######
		$mform->addElement('html', '<div class="choose exammanagement-rooms"><div class="row"><div class="col-xs-3"><h4>Raum</h4></div><div class="col-xs-3"><h4>Beschreibung</h4></div><div class="col-xs-3"><h4>Sitzplan</h4></div><div class="col-xs-3"><h4>Raumart</h4></div></div>');

 		$allRoomIDs= $obj->getAllRoomIDsSortedByName();
 		$checkedRoomIDs = $obj->getSavedRooms();
 
 		$mform->addElement('html', '<div class="row"><div class="col-xs-3">');
		$mform->addElement('advcheckbox', 'checkall', 'Alle aus-/abwählen', null, array('group' => 1, 'id' => 'checkboxgroup1',));			
		$mform->addElement('html', '</div><div class="col-xs-3"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');
 		
 		if ($allRoomIDs){
			foreach($allRoomIDs as $key => $value){
				$mform->addElement('html', '<div class="row"><div class="col-xs-3">');
				$mform->addElement('advcheckbox', 'rooms['.$value.']', $obj->getRoomObj($value)->name, null, array('group' => 1));
				$mform->addElement('html', '</div><div class="col-xs-3"> '.$obj->getRoomObj($value)->description.' </div>');
				$mform->addElement('html', '<div class="col-xs-3">');
				if ($obj->getRoomObj($value)->seatingplan){
					$mform->addElement('html', '<a id="show" href="#">Ja</a><div class="svg hidden">'.$obj->getRoomObj($value)->seatingplan.'</div>');
				} else {
					$mform->addElement('html', ' Nein ');
				}
				$mform->addElement('html', '</div><div class="col-xs-3"> Standardraum </div></div>');

				if($checkedRoomIDs){
					foreach($checkedRoomIDs as $key2 => $value2){
						if($allRoomIDs[$key]==$value2){
							$mform->setDefault('rooms['.$value.']', true);
						}
					}
				}		
			}
			
			$this->add_action_buttons(true,'Raum für Prüfung auswählen');
			
		} else{
			$mform->addElement('html', 'Keine Räume gefunden');
		}

		$mform->addElement('html', '</div></div>');
				
		###### import Rooms from File ######
		
		$maxbytes=$CFG->maxbytes;

		$mform->addElement('html', '<div class="import"><h4>Excel-Datei</h4>');
		$mform->addElement('filepicker', 'userfile', 'Räume aus Excel-Datei als Standardräume importieren (...).', null, array('maxbytes' => $maxbytes, 'accepted_types' => '.csv'));
		$mform->addElement('html', '</div>');

    }
    
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}