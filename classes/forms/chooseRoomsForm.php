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

namespace mod_exammanagement\forms;
use moodleform;

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

        $ExammanagementInstanceObj = \mod_exammanagement\general\exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $MoodleObj = \mod_exammanagement\general\Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('addRooms'));

        $mform->addElement('hidden', 'id', 'dummy');
		$mform->setType('id', PARAM_INT);

		$mform->addElement('html', '<div class="row"><div class="col-xs-6">');
		$mform->addElement('html', '<h3 class="choose">Räume auswählen</h3>');
		$mform->addElement('html', '<h3 class="import">Neue Räume hinzufügen</h3>');

		$mform->addElement('html', '</div><div class="col-xs-6"><button type="button" id="switch_mode_rooms" class="btn btn-primary" title="Umschalten zwischen Raumwahl und -import"><span class="import">Raumwahl</span><span class="choose">Neue Räume hinzufügen</span></button>');

    if($MoodleObj->checkCapability('mod/exammanagement:adddefaultrooms', $this->_customdata['id'], $this->_customdata['e'])){
        $mform->addElement('html', '<a href="'.$ExammanagementInstanceObj->getExammanagementUrl("addDefaultRooms", $this->_customdata['id']).'" class="btn btn-primary import" title="Standardräume importieren"><span>Standardräume importieren</span></a>');
    }

    $mform->addElement('html', '</div></div><p class="choose">Die unten stehenden Räume können als Prüfungsräume gewählt werden.</p><p class="import">Sie können zudem selbst neue Räume in die Liste der verfügbaren Prüfungsräume aufnehmen.</p>');

		###### chooseRooms ######
		$mform->addElement('html', '<div class="choose exammanagement-rooms"><div class="row"><div class="col-xs-3"><h4>Raum</h4></div><div class="col-xs-3"><h4>Beschreibung</h4></div><div class="col-xs-3"><h4>Sitzplan</h4></div><div class="col-xs-3"><h4>Raumart</h4></div></div>');

 		$allRoomIDs= $ExammanagementInstanceObj->getAllRoomIDsSortedByName();
 		$checkedRoomIDs = $ExammanagementInstanceObj->getSavedRooms();

 		$mform->addElement('html', '<div class="row"><div class="col-xs-3">');
		$mform->addElement('advcheckbox', 'checkall', 'Alle aus-/abwählen', null, array('group' => 1, 'id' => 'checkboxgroup1',));
		$mform->addElement('html', '</div><div class="col-xs-3"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');

 		if ($allRoomIDs){
			foreach($allRoomIDs as $key => $value){

        $roomObj = $ExammanagementInstanceObj->getRoomObj($value);
				$mform->addElement('html', '<div class="row"><div class="col-xs-3">');
				$mform->addElement('advcheckbox', 'rooms['.$value.']', $roomObj->name, null, array('group' => 1));
				$mform->addElement('html', '</div><div class="col-xs-3"> '.$roomObj->description.' </div>');
				$mform->addElement('html', '<div class="col-xs-3">');
				if ($roomObj->seatingplan){

          $svgStr = base64_decode($roomObj->seatingplan);

					$mform->addElement('html', '<a id="show"><i class="fa fa-2x fa-info-circle"></i></a><div class="svg hidden">'.$svgStr.'</div>');

        } else {
					$mform->addElement('html', ' Nein ');
				}

        if ($roomObj->type=='defaultroom'){
            $mform->addElement('html', '</div><div class="col-xs-3"> Standardraum </div>');
        } else {
            $mform->addElement('html', '</div><div class="col-xs-3"> Externer Raum </div>');
        }

        $mform->addElement('html', '</div>');

				if($checkedRoomIDs){
					foreach($checkedRoomIDs as $key2 => $value2){
						if($allRoomIDs[$key]==$value2){
							$mform->setDefault('rooms['.$value.']', true);
						}
					}
				}
			}

      $mform->addElement('html', '<b>Hinweis:</b><p>Einige Räume sind hier mehrfach aufgeführt. Dabei handelt es sich um unterschiedliche Modellierungen des selben Raumes. "1 Platz frei" bedeutet, dass jeder 2. Platz benutzt werden kann. "2 Plätze frei" bedeutet, dass jeder 3. Platz benutzt werden kann.</p>');
			$this->add_action_buttons(true,'Räume für Prüfung auswählen');

		} else{
			$mform->addElement('html', 'Keine Räume gefunden');
		}

		$mform->addElement('html', '</div></div>');

		###### import Rooms from File ######

		$maxbytes=$CFG->maxbytes;

		$mform->addElement('html', '<div class="import"><h4>Excel-Datei</h4><i class="fa fa-times text-error"> Diese Funktion befindet sich derzeit in der Entwicklung.</i>');
		$mform->addElement('filepicker', 'userfile', 'Räume aus Excel-Datei als Standardräume importieren (...).', null, array('maxbytes' => $maxbytes, 'accepted_types' => '.csv'));
		$mform->addElement('html', '</div>');

    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
