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
use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\Moodle;
use mod_exammanagement\general\MoodleDB;

use moodleform;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/Moodle.php');
require_once(__DIR__.'/../general/MoodleDB.php');

class chooseRoomsForm extends moodleform {

  //Add elements to form
  public function definition() {

    global $PAGE, $CFG;

    $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
    $MoodleObj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);
    $MoodleDBObj = MoodleDB::getInstance();

    $mform = $this->_form; // Don't forget the underscore!

    $mform->addElement('hidden', 'id', 'dummy');
    $mform->setType('id', PARAM_INT);

    $mform->addElement('html', '<div class="row"><div class="col-xs-6">');
    $mform->addElement('html', '<h3>Räume auswählen</h3></div>');
    $mform->addElement('html', '<div class="col-xs-2"><a class=" helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');

    if($MoodleObj->checkCapability('mod/exammanagement:adddefaultrooms', $this->_customdata['id'], $this->_customdata['e'])){
      $mform->addElement('html', '<div class="col-xs-4"><a href="'.$ExammanagementInstanceObj->getExammanagementUrl("addDefaultRooms", $this->_customdata['id']).'" class="btn btn-primary pull-right" title="'.get_string("import_default_rooms", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("import_default_rooms", "mod_exammanagement").'</span><i class="fa fa-building d-lg-none" aria-hidden="true"></i></a></div>');
    }

    $mform->addElement('html', '</div>');

    $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('addRooms'));

    $mform->addElement('html', '<p>Die unten stehenden Räume können als Prüfungsräume gewählt werden.</p>');
    $mform->addElement('html', '<p><b>Hinweis:</b> Einige Räume sind hier mehrfach aufgeführt. Dabei handelt es sich um unterschiedliche Modellierungen des selben Raumes. "1 Platz frei" bedeutet, dass jeder 2. Platz benutzt werden kann. "2 Plätze frei" bedeutet, dass jeder 3. Platz benutzt werden kann.</p>');

    ###### chooseRooms ######
    $mform->addElement('html', '<div class="exammanagement-rooms"><div class="row"><div class="col-xs-3"><h4>Raum</h4></div><div class="col-xs-3"><h4>Beschreibung</h4></div><div class="col-xs-3"><h4>Sitzplan</h4></div><div class="col-xs-3"><h4>Raumart</h4></div></div>');

    $allRoomIDs= $ExammanagementInstanceObj->getAllRoomIDsSortedByName();
    $checkedRoomIDs = $ExammanagementInstanceObj->getSavedRooms();

    $mform->addElement('html', '<div class="row"><div class="col-xs-3">');
    $mform->addElement('html', '</div><div class="col-xs-3"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');

    if ($allRoomIDs){
      foreach($allRoomIDs as $key => $value){

        $roomObj = $ExammanagementInstanceObj->getRoomObj($value);
        $similiarRoomIDsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_rooms', array('name' => $roomObj->name));;
        $disableName = explode('_', $roomObj->roomid);

        $mform->addElement('html', '<div class="row"><div class="col-xs-3">');
        $mform->addElement('advcheckbox', 'rooms['.$roomObj->roomid.']', $roomObj->name, null, array('group' => 1));

        foreach($similiarRoomIDsArr as $key => $similiarRoomObj){ // code for preventing selection of multiple versions of the same room

            if (strpos($similiarRoomObj->roomid, $disableName[0])!==false){
              $mform->disabledif('rooms['.$roomObj->roomid.']', 'rooms['.$similiarRoomObj->roomid.']', 'checked');
            }
        }

        $mform->addElement('html', '</div><div class="col-xs-3"> '.$roomObj->description.' </div>');
        $mform->addElement('html', '<div class="col-xs-3">');
        if ($roomObj->seatingplan){

          $svgStr = base64_decode($roomObj->seatingplan);

          $mform->addElement('html', '<a id="show"><i class="fa fa-2x fa-info-circle"></i></a><div class="svg collapse">'.$svgStr.'</div>');

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
            if($roomObj->roomid==$value2){
              $mform->setDefault('rooms['.$roomObj->roomid.']', true);
            }
          }
        }
      }

      if($ExammanagementInstanceObj->isStateofPlacesCorrect()){
        $mform->addElement('html', '<p><b>Achtung:</b> Es wurden bereits Sitzplätze zugewiesen. Diese Zuweisung wird durch das Ändern der Prüfungsräume gelöscht und muss dann neu durchgeführt werden.</p>');
      }
      $this->add_action_buttons(true,'Räume für Prüfung auswählen');

    } else{
      $mform->addElement('html', 'Keine Räume gefunden');

    }

    $mform->addElement('html', '</div></div>');

    $mform->disable_form_change_checker();

  }

  //Custom validation should be added here
  function validation($data, $files) {
    $errors= array();

    foreach($data['rooms'] as $roomid => $checked){

      if($checked == "1"){
          $MoodleDBObj = MoodleDB::getInstance();

          $roomname = explode('_', $roomid);
          $similiarRoomIDsArr = $MoodleDBObj->getRecordsFromDB('exammanagement_rooms', array('name' => $roomname[0]));

          foreach($similiarRoomIDsArr as $key => $similiarRoomObj){

              if(is_string($data['rooms'][$similiarRoomObj->roomid]) && $data['rooms'][$similiarRoomObj->roomid] !== "0" && is_string($data['rooms'][$roomid]) && $similiarRoomObj->roomid !== $roomid){

                  $errors['rooms['.$roomid.']'] = get_string('err_roomsdoubleselected', 'mod_exammanagement');
              }
          }
      }
    }

    return $errors;
  }
}
