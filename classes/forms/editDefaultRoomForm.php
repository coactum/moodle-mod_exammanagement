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
 * class containing editDefaultRoomForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use mod_exammanagement\general\exammanagementInstance;

use moodleform;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');

class editDefaultRoomForm extends moodleform {

  //Add elements to form
  public function definition() {

    $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

    $mform = $this->_form; // Don't forget the underscore!

    $mform->addElement('hidden', 'id', 'dummy');
    $mform->setType('id', PARAM_INT);
    $mform->addElement('hidden', 'existingroom', 0);
    $mform->setType('existingroom', PARAM_INT);

    $mform->addElement('html', '<div class="row"><h3 class="col-xs-10">'.get_string('editDefaultRoom', 'mod_exammanagement').'</h3>');
    $mform->addElement('html', '<div class="col-xs-2"><a class="pull-right helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;" title="'.get_string("helptext_open", "mod_exammanagement").'"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');
    $mform->addElement('html', '</div>');

    $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('editDefaultRoom'));
    
    //$mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>'.get_string("change_custom_room_name", "mod_exammanagement").'</div>');

    $mform->addElement('html', '<p>'.get_string('edit_defaultroom_str', 'mod_exammanagement').'</p>');

    $mform->addElement('html', '<hr>');
    $mform->addElement('html', '<p>'.get_string('general', 'mod_exammanagement').'</p>');

    $attributes = array('size'=>'20');

    $mform->addElement('text', 'roomid', get_string('roomid', 'mod_exammanagement'), $attributes);
    $mform->setType('roomid', PARAM_TEXT);
    $mform->addRule('roomid', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

    $mform->addElement('text', 'roomname', get_string('customroom_name', 'mod_exammanagement'), $attributes);
    $mform->setType('roomname', PARAM_TEXT);
    $mform->addRule('roomname', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

    $mform->addElement('text', 'description', get_string('defaultroom_description', 'mod_exammanagement'), $attributes);
    $mform->setType('description', PARAM_TEXT);
    $mform->addRule('description', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

    if(isset($this->_customdata['existingroom']) && $this->_customdata['existingroom'] === true){

        if(isset($this->_customdata['placescount']) && $this->_customdata['placescount'] !== false){
          $mform->addElement('static', 'placescount', get_string('customroom_placescount', 'mod_exammanagement'), $this->_customdata['placescount']);
        } else {
          $mform->addElement('static', 'placescount', get_string('customroom_placescount', 'mod_exammanagement'), '-');
        }

        if(isset($this->_customdata['placespreview']) && $this->_customdata['placespreview'] !== false){
          $mform->addElement('static', 'placespreview', get_string('placespreview', 'mod_exammanagement'), '<div class="exammanagement_editdefaultroom_placespreview">'.$this->_customdata['placespreview'].'</div>');
        } else {
          $mform->addElement('static', 'placespreview', get_string('placespreview', 'mod_exammanagement'), '-');
        }

        if(isset($this->_customdata['roomplanavailable']) && $this->_customdata['roomplanavailable']!== ''){
          $mform->addElement('static', 'roomplanavailable', get_string('roomplan_available', 'mod_exammanagement'), '<div class="exammanagement_editdefaultroom_svg">'.$this->_customdata['roomplanavailable'].'</div>');
        } else {
          $mform->addElement('static', 'roomplanavailable', get_string('roomplan_available', 'mod_exammanagement'), '-');
        }

    }

    $mform->addElement('html', '<hr>');
    $mform->addElement('html', '<p>'.get_string('new_places', 'mod_exammanagement').'</p>');

    
    $mform->addElement('selectyesno', 'editplaces', get_string('edit_places', 'mod_exammanagement'));
    $mform->hideIf('editplaces', 'existingroom', 'neq', 1);

    $select = $mform->addElement('select', 'placesmode', get_string('places_mode', 'mod_exammanagement'), array('default' => get_string('placesmode_default', 'mod_exammanagement'), 'rows' => get_string('placesmode_rows', 'mod_exammanagement'), 'all_individual' => get_string('placesmode_all_individual', 'mod_exammanagement'))); 
    $select->setSelected('default');

    $mform->addElement('text', 'placesroom', get_string('placesroom', 'mod_exammanagement'), $attributes);
    $mform->setType('placesroom', PARAM_INT);
    $mform->hideIf('placesroom', 'placesmode', 'neq', 'default');

    $mform->addElement('text', 'placesrow', get_string('placesrow', 'mod_exammanagement'), $attributes);
    $mform->setType('placesrow', PARAM_INT);
    $mform->hideIf('placesrow', 'placesmode', 'neq', 'rows');

    $select = $mform->addElement('select', 'placesfree', get_string('placesfree', 'mod_exammanagement'), array(1 => get_string('one_place_free', 'mod_exammanagement'), 2 => get_string('two_places_free', 'mod_exammanagement'))); 
    $mform->hideIf('placesfree', 'placesmode', 'eq', 'all_individual');

    $mform->addElement('text', 'rowscount', get_string('rowscount', 'mod_exammanagement'), $attributes);
    $mform->setType('rowscount', PARAM_INT);
    $mform->hideIf('rowscount', 'placesmode', 'neq', 'rows');

    $mform->addElement('text', 'placesarray', get_string('placesarray', 'mod_exammanagement'), $attributes);
    $mform->setType('placesarray', PARAM_TEXT);
    $mform->hideIf('placesarray', 'placesmode', 'neq', 'all_individual');

    if(isset($this->_customdata['existingroom']) && $this->_customdata['existingroom'] === true){
      $mform->hideIf('placesmode', 'editplaces', 'eq', 0);
      $mform->hideIf('placesroom', 'editplaces', 'eq', 0);
      $mform->hideIf('placesrow', 'editplaces', 'eq', 0);
      $mform->hideIf('placesfree', 'editplaces', 'eq', 0);
      $mform->hideIf('rowscount', 'editplaces', 'eq', 0);
      $mform->hideIf('placesarray', 'editplaces', 'eq', 0);
    }
    
    $mform->addElement('html', '<hr>');
    $mform->addElement('html', '<p>'.get_string('new_seatingplan', 'mod_exammanagement').'</p>');

    $mform->addElement('filepicker', 'defaultroom_svg', get_string("defaultroom_svg", "mod_exammanagement"), null, array('accepted_types' => '.txt'));

    $mform->addElement('html', '<hr>');

    $this->add_action_buttons(true, get_string("add_room", "mod_exammanagement"));

    $mform->disable_form_change_checker();

  }

  //Custom validation should be added here
  function validation($data, $files) {

    // global $USER;

    // $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

    $errors = array();

    // $similiarroom = $ExammanagementInstanceObj->getRoomObj($data['roomname'].'_'.$USER->id.'c');

    // if($data['existingroom'] !== 1 && $similiarroom){
    //    $errors['roomname'] = get_string('err_customroomname_taken', 'mod_exammanagement');
    // } else if(!preg_match('/^[a-zA-Z0-9_\-. ]+$/', $data['roomname'])){
    //   $errors['roomname'] = get_string('err_noalphanumeric', 'mod_exammanagement');
    // }

    // if(!$data['placescount'] || $data['placescount'] <= 0){
    //    $errors['placescount'] = get_string('err_novalidinteger', 'mod_exammanagement');
    // }

    return $errors;
  }
}
