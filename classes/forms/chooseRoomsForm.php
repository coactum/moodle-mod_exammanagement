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
use mod_exammanagement\general\User;
use mod_exammanagement\general\Moodle;
use mod_exammanagement\general\MoodleDB;

use moodleform;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/User.php');
require_once(__DIR__.'/../general/Moodle.php');
require_once(__DIR__.'/../general/MoodleDB.php');

class chooseRoomsForm extends moodleform {

  //Add elements to form
  public function definition() {

    $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
    $UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e']);
    $MoodleObj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);
    $MoodleDBObj = MoodleDB::getInstance();

    $mform = $this->_form; // Don't forget the underscore!

    $mform->addElement('hidden', 'id', 'dummy');
    $mform->setType('id', PARAM_INT);

    $mform->addElement('html', '<div class="row"><div class="col-xs-4">');
    $mform->addElement('html', '<h3>'.get_string('choose_exam_rooms', 'mod_exammanagement').'</h3></div>');
    $mform->addElement('html', '<div class="col-xs-2"><a class=" helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;" title="'.get_string("helptext_open", "mod_exammanagement").'"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');

    $mform->addElement('html', '<div class="col-xs-6">');

    if($MoodleObj->checkCapability('mod/exammanagement:adddefaultrooms', $this->_customdata['id'], $this->_customdata['e'])){
      $mform->addElement('html', '<a href="'.$ExammanagementInstanceObj->getExammanagementUrl("addDefaultRooms", $this->_customdata['id']).'" class="btn btn-primary pull-right m-b-1" title="'.get_string("import_default_rooms", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("import_default_rooms", "mod_exammanagement").'</span><i class="fa fa-building d-lg-none" aria-hidden="true"></i></a>');
    }

    $mform->addElement('html', '<a href="'.$ExammanagementInstanceObj->getExammanagementUrl("addCustomRoom", $this->_customdata['id']).'" class="btn btn-primary pull-right m-r-1" title="'.get_string("add_custom_room", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("add_custom_room", "mod_exammanagement").'</span><i class="fa fa-building d-lg-none" aria-hidden="true"></i></a></div>');    

    $mform->addElement('html', '</div>');

    $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('chooseRooms'));

    $mform->addElement('html', '<p>'.get_string('choose_rooms_str', 'mod_exammanagement').'</p>');

    $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>'.get_string("hint_room_modelling", "mod_exammanagement").'</div>');

    ###### chooseRooms ######
    $mform->addElement('html', '<div><div class="row"><div class="col-xs-3"><h4>'.get_string('room', 'mod_exammanagement').'</h4></div><div class="col-xs-3"><h4>'.get_string('description', 'mod_exammanagement').'</h4></div><div class="col-xs-2"><h4>'.get_string('seatingplan', 'mod_exammanagement').'</h4></div><div class="col-xs-3"><h4>'.get_string('room_type', 'mod_exammanagement').'</h4></div><div class="col-xs-1"></div></div>');

    $allRoomIDs = $ExammanagementInstanceObj->getAllRoomIDsSortedByName();
    $checkedRoomIDs = $ExammanagementInstanceObj->getSavedRooms();

    $mform->addElement('html', '<div class="row"><div class="col-xs-3">');
    $mform->addElement('html', '</div><div class="col-xs-3"></div><div class="col-xs-2"></div><div class="col-xs-3"></div><div class="col-xs-1"></div></div>');

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
        $mform->addElement('html', '<div class="col-xs-2">');
        if ($roomObj->seatingplan){

          $svgStr = base64_decode($roomObj->seatingplan);

          $mform->addElement('html', '<a id="show"><i class="fa fa-2x fa-info-circle"></i></a><div class="exammanagement_rooms_svg collapse">'.$svgStr.'</div>');

        } else {
          $mform->addElement('html', get_string('no_seatingplan_available', 'mod_exammanagement'));
        }

        if ($roomObj->type=='defaultroom'){
          $mform->addElement('html', '</div><div class="col-xs-3">'.get_string('default_room', 'mod_exammanagement').'</div>');
        } else {
          $mform->addElement('html', '</div><div class="col-xs-3">'.get_string('custom_room', 'mod_exammanagement').'</div>');
          $mform->addElement('html', '<div class="col-sm-1"><a class="m-r-1" href="addCustomRoom.php?id='.$this->_customdata['id'].'&roomid='.$roomObj->roomid.'" title="'.get_string("change_room", "mod_exammanagement").'"><i class="fa fa-edit"></i></a>');
          $mform->addElement('html', '<a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/chooseRooms.php', $this->_customdata['id'], 'deletecustomroomid', $roomObj->roomid).'" onClick="javascript:return confirm(\''.get_string("delete_room_confirm", "mod_exammanagement").'\');" title="'.get_string("delete_room", "mod_exammanagement").'"><i class="fa fa-trash"></i></a></div>');
        }

        $mform->addElement('html', '</div>');

        if($checkedRoomIDs){
          foreach($checkedRoomIDs as $key2 => $value2){
            if($roomObj->roomid==$value2){
              $mform->setDefault('rooms['.$roomObj->roomid.']', true);
            }
          }
        }

        if($UserObj->getAllExamParticipantsByRoom($value)){
          $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>'.get_string("places_already_assigned_rooms", "mod_exammanagement").'</div>');
        }
      }

      $this->add_action_buttons(true,get_string('choose_rooms', 'mod_exammanagement'));

    } else{
      $mform->addElement('html', get_string('no_rooms_found', 'mod_exammanagement'));

    }

    $mform->addElement('html', '</div>');

    $mform->disable_form_change_checker();

  }

  //Custom validation should be added here
  function validation($data, $files) {
    $errors = array();

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

      if(!preg_match("/^[a-zA-Z0-9_\-.]+$/", $roomid)){
        $errors['rooms['.$roomid.']'] = get_string('err_invalidcheckboxid_rooms', 'mod_exammanagement');
      }
    }

    return $errors;
  }
}
