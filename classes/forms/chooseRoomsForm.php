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
 * @copyright   coactum GmbH 2019
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

    global $PAGE, $OUTPUT;

    $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
    $UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e'], $ExammanagementInstanceObj->getCm()->instance);
    $MoodleObj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);
    $MoodleDBObj = MoodleDB::getInstance();

    $PAGE->requires->js_call_amd('mod_exammanagement/remove_form_classes_col', 'remove_form_classes_col'); //call removing moodle form classes col-md for better layout

    $mform = $this->_form; // Don't forget the underscore!

    $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

    $mform->addElement('html', '<div class="row"><div class="col-xs-6"><h3>'. get_string('chooseRooms', 'mod_exammanagement'));

    if($helptextsenabled){
      $mform->addElement('html', $OUTPUT->help_icon('chooseRooms', 'mod_exammanagement', ''));
    }

    $mform->addElement('html', '</h3></div><div class="col-xs-6">');

    $allRooms = $ExammanagementInstanceObj->getRooms('all');
    $examRooms = $ExammanagementInstanceObj->getRooms('examrooms');
    $i = 1;

    if($MoodleObj->checkCapability('mod/exammanagement:importdefaultrooms', $this->_customdata['id'], $this->_customdata['e'])){
      if($allRooms){
        $mform->addElement('html', '<a href="'.$ExammanagementInstanceObj->getExammanagementUrl("exportDefaultRooms", $this->_customdata['id']).'" class="btn btn-primary m-b-1" title="'.get_string("export_default_rooms", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("export_default_rooms", "mod_exammanagement").'</span><i class="fa fa-download d-lg-none" aria-hidden="true"></i></a>');
      }
      $mform->addElement('html', '<a href="'.$ExammanagementInstanceObj->getExammanagementUrl("addDefaultRooms", $this->_customdata['id']).'" class="btn btn-primary pull-right m-b-1" title="'.get_string("import_default_rooms", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("import_default_rooms", "mod_exammanagement").'</span><i class="fa fa-file-text d-lg-none" aria-hidden="true"></i></a>');
    }

    if($MoodleObj->checkCapability('mod/exammanagement:importdefaultrooms', $this->_customdata['id'], $this->_customdata['e'])){
      $mform->addElement('html', '<a href="'.$ExammanagementInstanceObj->getExammanagementUrl("editDefaultRoom", $this->_customdata['id']).'" class="btn btn-primary pull-right m-r-1" title="'.get_string("add_default_room", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("add_default_room", "mod_exammanagement").'</span><i class="fa fa-plus d-lg-none" aria-hidden="true"></i></a></div>');
    } else {
      $mform->addElement('html', '<a href="'.$ExammanagementInstanceObj->getExammanagementUrl("addCustomRoom", $this->_customdata['id']).'" class="btn btn-primary pull-right m-r-1" title="'.get_string("add_custom_room", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("add_custom_room", "mod_exammanagement").'</span><i class="fa fa-plus d-lg-none" aria-hidden="true"></i></a></div>');
    }

    $mform->addElement('html', '</div>');

    $mform->addElement('html', '<p>'.get_string('choose_rooms_str', 'mod_exammanagement').'</p>');

    $mform->addElement('hidden', 'id', 'dummy');
    $mform->setType('id', PARAM_INT);

    if ($allRooms){

      $mform->addElement('html', '<div class="table-responsive">');
      $mform->addElement('html', '<table class="table table-striped exammanagement_table">');

      if($MoodleObj->checkCapability('mod/exammanagement:importdefaultrooms', $this->_customdata['id'], $this->_customdata['e'])){
        $mform->addElement('html', '<thead class="exammanagement_tableheader exammanagement_brand_backgroundcolor"><th scope="col">#</th><th scope="col">'.get_string("roomid", "mod_exammanagement").'</th><th scope="col">'.get_string("exam_room", "mod_exammanagement").'</th><th scope="col">'.get_string("description", "mod_exammanagement").'</th><th scope="col">'.get_string("customroom_placescount", "mod_exammanagement").'</th><th scope="col">'.get_string("seatingplan", "mod_exammanagement").'</th><th scope="col">'.get_string("room_type", "mod_exammanagement").'</th><th scope="col" class="exammanagement_table_width_room exammanagement_table_whiteborder_left">'.get_string("options", "mod_exammanagement").'</th></thead>');
      } else {
        $mform->addElement('html', '<thead class="exammanagement_tableheader exammanagement_brand_backgroundcolor"><th scope="col">#</th><th scope="col">'.get_string("exam_room", "mod_exammanagement").'</th><th scope="col">'.get_string("description", "mod_exammanagement").'</th><th scope="col">'.get_string("customroom_placescount", "mod_exammanagement").'</th><th scope="col">'.get_string("seatingplan", "mod_exammanagement").'</th><th scope="col">'.get_string("room_type", "mod_exammanagement").'</th><th scope="col" class="exammanagement_table_width_room exammanagement_table_whiteborder_left">'.get_string("options", "mod_exammanagement").'</th></thead>');
      }

      $mform->addElement('html', '<tbody>');

      foreach($allRooms as $room){

        $isRoomFilled = $UserObj->getParticipantsCount('room', $room->roomid);

        $similiarRooms = $MoodleDBObj->getRecordsFromDB('exammanagement_rooms', array('name' => $room->name));;
        $disableName = explode('_', $room->roomid);

        $mform->addElement('html', '<tr>');

        $mform->addElement('html', '<th scope="row" id="'.$i.'">'.$i.'</th>');

        if($MoodleObj->checkCapability('mod/exammanagement:importdefaultrooms', $this->_customdata['id'], $this->_customdata['e'])){

          $mform->addElement('html', '<td>');

          $mform->addElement('html', $room->roomid);
          $mform->addElement('html', '</td>');
        }

        $mform->addElement('html', '<td>');

        $mform->addElement('advcheckbox', 'rooms['.$room->roomid.']', $room->name, null, array('group' => 1));

        foreach($similiarRooms as $similiarRoomObj){ // code for preventing selection of multiple versions of the same room

            if (strpos($similiarRoomObj->roomid, $disableName[0])!==false){
              $mform->disabledif('rooms['.$room->roomid.']', 'rooms['.$similiarRoomObj->roomid.']', 'checked');
            }
        }

        $mform->addElement('html', '</td><td> '.$room->description.' </td>');

        $mform->addElement('html', '<td> '.count(json_decode($room->places)).' </td>');

        $mform->addElement('html', '<td>');
        if ($room->seatingplan){

          $svgStr = base64_decode($room->seatingplan);

          $mform->addElement('html', '<a id="show" class="pointer"><i class="fa fa-2x fa-info-circle"></i></a><div class="exammanagement_rooms_svg collapse">'.$svgStr.'</div>');

        } else {
          $mform->addElement('html', get_string('no_seatingplan_available', 'mod_exammanagement'));
        }
        $mform->addElement('html', '</td><td>');


        if ($room->type=='defaultroom'){
          if($MoodleObj->checkCapability('mod/exammanagement:importdefaultrooms')){
              $mform->addElement('html', get_string('default_room', 'mod_exammanagement'));
              $mform->addElement('html', '</td><td class="exammanagement_brand_bordercolor_left">');
              $mform->addElement('html', '<a href="editDefaultRoom.php?id='.$this->_customdata['id'].'&roomid='.$room->roomid.'" title="'.get_string("change_room", "mod_exammanagement").'"><i class="fa fa-2x fa-edit"></i></a>');
              $mform->addElement('html', ' <a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/chooseRooms.php', $this->_customdata['id'], 'deletedefaultroomid', $room->roomid).'" onClick="javascript:return confirm(\''.get_string("delete_defaultroom_confirm", "mod_exammanagement").'\');" title="'.get_string("delete_defaultroom_confirm", "mod_exammanagement").'"><i class="fa fa-2x fa-trash"></i></a>');
              $mform->addElement('html', ' <a href="#end" title="'.get_string("jump_to_end", "mod_exammanagement").'"><i class="fa fa-2x fa-lg fa-arrow-down" aria-hidden="true"></i></a></td>');

          } else {
            $mform->addElement('html', get_string('default_room', 'mod_exammanagement').'</td>');
            $mform->addElement('html', '<td class="exammanagement_brand_bordercolor_left"><a class="pull-right" href="#end" title="'.get_string("jump_to_end", "mod_exammanagement").'"><i class="fa fa-2x fa-lg fa-arrow-down" aria-hidden="true"></i></a></td>');
          }

        } else {

          $mform->addElement('html', get_string('custom_room', 'mod_exammanagement'));
          $mform->addElement('html', '</td><td class="exammanagement_brand_bordercolor_left">');
          $mform->addElement('html', '<a href="addCustomRoom.php?id='.$this->_customdata['id'].'&roomid='.$room->roomid.'" title="'.get_string("change_room", "mod_exammanagement").'"><i class="fa fa-2x fa-edit"></i></a>');
          $mform->addElement('html', ' <a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/chooseRooms.php', $this->_customdata['id'], 'deletecustomroomid', $room->roomid).'" onClick="javascript:return confirm(\''.get_string("delete_room_confirm", "mod_exammanagement").'\');" title="'.get_string("delete_room", "mod_exammanagement").'"><i class="fa fa-2x fa-trash"></i></a>');
          $mform->addElement('html', ' <a href="#end" title="'.get_string("jump_to_end", "mod_exammanagement").'"><i class="fa fa-2x fa-lg fa-arrow-down" aria-hidden="true"></i></a></td>');
        }

        if($examRooms){
          foreach($examRooms as $room2){
            if($room->roomid == $room2->roomid){
              $mform->setDefault('rooms['.$room->roomid.']', true);
            }
          }
        }

        $mform->addElement('html', '</tr>');

        if($isRoomFilled){

          $colspan = 7;

          if($MoodleObj->checkCapability('mod/exammanagement:importdefaultrooms', $this->_customdata['id'], $this->_customdata['e'])){
            $colspan += 1;
          }

          $mform->addElement('html', '<tr><td colspan="'.$colspan.'"><div class="alert alert-warning alert-block fade in " role="alert">'.get_string("places_already_assigned_rooms", "mod_exammanagement").'</div></td></tr>');
        }
        $i++;
      }

      $mform->addElement('html', '</tbody></table></div>');

      $mform->addElement('html', '<div id="end"></div>');

      $this->add_action_buttons(true,get_string('choose_rooms', 'mod_exammanagement'));

    } else{
      $mform->addElement('html', '<p>'.get_string('no_rooms_found', 'mod_exammanagement').'</p>');

    }

    $mform->disable_form_change_checker();

  }

  //Custom validation should be added here
  function validation($data, $files) {
    $errors = array();

    foreach($data['rooms'] as $roomid => $checked){

      if($checked == "1"){
          $MoodleDBObj = MoodleDB::getInstance();

          $roomname = explode('_', $roomid);
          $similiarRooms = $MoodleDBObj->getRecordsFromDB('exammanagement_rooms', array('name' => $roomname[0]));

          foreach($similiarRooms as $key => $similiarRoomObj){

              if(is_string($data['rooms'][$similiarRoomObj->roomid]) && $data['rooms'][$similiarRoomObj->roomid] !== "0" && is_string($data['rooms'][$roomid]) && $similiarRoomObj->roomid !== $roomid){

                  $errors['rooms['.$roomid.']'] = get_string('err_roomsdoubleselected', 'mod_exammanagement');
              }
          }
      }

      if(!preg_match("/^[a-zA-Z0-9_\-. ]+$/", $roomid)){
        $errors['rooms['.$roomid.']'] = get_string('err_invalidcheckboxid_rooms', 'mod_exammanagement');
      }
    }

    return $errors;
  }
}
