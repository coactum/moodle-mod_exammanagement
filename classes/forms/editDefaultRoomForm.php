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

    global $PAGE, $OUTPUT;

    $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

    $PAGE->requires->js_call_amd('mod_exammanagement/edit_defaultroom', 'init'); //call jquery for disabling roomid field if existing room is edited

    $mform = $this->_form; // Don't forget the underscore!

    $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

    $mform->addElement('html', '<h3>'.get_string("editDefaultRoom", "mod_exammanagement"));
    
    if($helptextsenabled){
        $mform->addElement('html', $OUTPUT->help_icon('editDefaultRoom', 'mod_exammanagement', ''));
    }

    $mform->addElement('html', '</h3>');    

    $mform->addElement('html', '<p>'.get_string('edit_defaultroom_str', 'mod_exammanagement').'</p>');

    $mform->addElement('html', '<hr>');
    $mform->addElement('html', '<p><strong>'.get_string('general', 'mod_exammanagement').'</strong></p>');

    $mform->addElement('hidden', 'id', 'dummy');
    $mform->setType('id', PARAM_INT);
    $mform->addElement('hidden', 'existingroom', 0);
    $mform->setType('existingroom', PARAM_INT);

    $attributes = array('size'=>'22');

    $mform->addElement('text', 'roomid', get_string('roomid_internal', 'mod_exammanagement'), $attributes);
    $mform->setType('roomid', PARAM_TEXT);
    $mform->addRule('roomid', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

    $mform->addElement('text', 'roomname', get_string('defaultroom_name', 'mod_exammanagement'), $attributes);
    $mform->setType('roomname', PARAM_TEXT);
    $mform->addRule('roomname', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

    $mform->addElement('text', 'description', get_string('defaultroom_description', 'mod_exammanagement'), $attributes);
    $mform->setType('description', PARAM_TEXT);
    $mform->addRule('description', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

    if(isset($this->_customdata['existingroom']) && $this->_customdata['existingroom'] === true){

        if(isset($this->_customdata['placescount']) && $this->_customdata['placescount'] !== false){
          $mform->addElement('static', 'placescount', get_string('defaultroom_placescount', 'mod_exammanagement'), $this->_customdata['placescount']);
        } else {
          $mform->addElement('static', 'placescount', get_string('defaultroom_placescount', 'mod_exammanagement'), '-');
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
    $mform->addElement('html', '<p><strong>'.get_string('new_places', 'mod_exammanagement').'</strong></p>');

    
    $mform->addElement('selectyesno', 'editplaces', get_string('edit_places', 'mod_exammanagement'));
    $mform->hideIf('editplaces', 'existingroom', 'neq', 1);

    $select = $mform->addElement('select', 'placesmode', get_string('places_mode', 'mod_exammanagement'), array('default' => get_string('placesmode_default', 'mod_exammanagement'), 'rows' => get_string('placesmode_rows', 'mod_exammanagement'), 'all_individual' => get_string('placesmode_all_individual', 'mod_exammanagement'))); 

    if(isset($this->_customdata['existingroom']) && $this->_customdata['existingroom'] === true){
      $select->setSelected('all_individual');
    } else {
      $select->setSelected('default');
    }

    $mform->addElement('text', 'placesroom', get_string('placesroom', 'mod_exammanagement'), $attributes);
    $mform->setType('placesroom', PARAM_INT);
    $mform->hideIf('placesroom', 'placesmode', 'neq', 'default');

    $mform->addElement('text', 'rowscount', get_string('rowscount', 'mod_exammanagement'), $attributes);
    $mform->setType('rowscount', PARAM_INT);
    $mform->hideIf('rowscount', 'placesmode', 'neq', 'rows');

    $mform->addElement('text', 'placesrow', get_string('placesrow', 'mod_exammanagement'), $attributes);
    $mform->setType('placesrow', PARAM_INT);
    $mform->hideIf('placesrow', 'placesmode', 'neq', 'rows');

    $select = $mform->addElement('select', 'placesfree', get_string('placesfree', 'mod_exammanagement'), array(1 => get_string('one_place_free', 'mod_exammanagement'), 2 => get_string('two_places_free', 'mod_exammanagement'))); 
    $mform->hideIf('placesfree', 'placesmode', 'eq', 'all_individual');

    $select = $mform->addElement('select', 'rowsfree', get_string('rowsfree', 'mod_exammanagement'), array(0 => get_string('no_row_free', 'mod_exammanagement'), 1 => get_string('one_row_free', 'mod_exammanagement'))); 
    $mform->hideIf('rowsfree', 'placesmode', 'neq', 'rows');

    $attributes = array('size'=>'200');

    $mform->addElement('text', 'placesarray', get_string('placesarray', 'mod_exammanagement'), $attributes);
    $mform->setType('placesarray', PARAM_TEXT);
    $mform->hideIf('placesarray', 'placesmode', 'neq', 'all_individual');

    if(isset($this->_customdata['existingroom']) && $this->_customdata['existingroom'] === true){
      $mform->hideIf('placesmode', 'editplaces', 'eq', 0);
      $mform->hideIf('placesroom', 'editplaces', 'eq', 0);
      $mform->hideIf('placesrow', 'editplaces', 'eq', 0);
      $mform->hideIf('placesfree', 'editplaces', 'eq', 0);
      $mform->hideIf('rowsfree', 'editplaces', 'eq', 0);
      $mform->hideIf('rowscount', 'editplaces', 'eq', 0);
      $mform->hideIf('placesarray', 'editplaces', 'eq', 0);
    }
    
    $mform->addElement('html', '<hr>');
    $mform->addElement('html', '<p><strong>'.get_string('new_seatingplan', 'mod_exammanagement').'</strong></p>');

    $mform->addElement('filepicker', 'defaultroom_svg', get_string("defaultroom_svg", "mod_exammanagement"), null, array('accepted_types' => '.txt'));

    $mform->addElement('html', '<hr>');

    $this->add_action_buttons(true, get_string("add_room", "mod_exammanagement"));

    $mform->disable_form_change_checker();

  }

  //Custom validation should be added here
  function validation($data, $files) {

    $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

    $errors = array();


    $similiarroom = $ExammanagementInstanceObj->getRoomObj($data['roomid']);

    if(isset($data['roomid']) && $data['roomid'] !== '' && $data['existingroom'] !== 1 && $similiarroom){ // if roomid is set and not empty and there is already a room with this id
        
        $roomid = $data['roomid'];

        if(substr($roomid, -2, -1) !== '_'){
          $roomid .= '_1';
        }
                
        for($i = 1; $i <= 9; $i++){
          $roomid = substr_replace($roomid, '_'.$i, -2);

          if($ExammanagementInstanceObj->getRoomObj($roomid) == false){
            $i = 9;
          }
        }

        $errors['roomid'] = get_string('err_already_defaultroom', 'mod_exammanagement') . $roomid;
    }
    
    if(isset($data['roomid']) && $data['roomid'] !== '' && !preg_match('/^[a-zA-Z0-9_.]+$/', $data['roomid'])){ // if roomid is set and not empty and contains non alphanumerical chars
        $errors['roomid'] = get_string('err_noalphanumeric', 'mod_exammanagement');
    }

    if(isset($data['roomname']) && $data['roomname'] !== '' && !preg_match('/^[a-zA-Z0-9. ]+$/', $data['roomname'])){ // if roomname is set and not empty and contains non alphanumerical chars
      $errors['roomname'] = get_string('err_noalphanumeric', 'mod_exammanagement');
    }

    if(isset($data['description']) && $data['description'] !== '' && !preg_match('/^[a-zA-Z0-9\-., äÄüÜöÖ]+$/', $data['description'])){ // if description is set and not empty and contains non alphanumerical chars
      $errors['description'] = get_string('err_noalphanumeric', 'mod_exammanagement');
    }

    if(((isset($data['editplaces']) && $data['editplaces'] == 1) || !isset($data['editplaces'])) && $data['placesmode'] == 'default' && ( !isset($data['placesroom'])|| $data['placesroom'] == '')){ // if existing room and places should be edited or new room and placesmode is rows and placesroom is not set or empty
      $errors['placesroom'] = get_string('err_filloutfield', 'mod_exammanagement');
    }

    if(((isset($data['editplaces']) && $data['editplaces'] == 1) || !isset($data['editplaces'])) && $data['placesmode'] == 'rows' && ( !isset($data['placesrow'])|| $data['placesrow'] == '')){ // if existing room and places should be edited or new room and placesmode is rows and placesrow is not set or empty
      $errors['placesrow'] = get_string('err_filloutfield', 'mod_exammanagement');
    }

    if(((isset($data['editplaces']) && $data['editplaces'] == 1) || !isset($data['editplaces'])) && $data['placesmode'] == 'rows' && ( !isset($data['rowscount'])|| $data['rowscount'] == '')){ // if existing room and places should be edited or new room and placesmode is rows and rowscount is not set or empty
      $errors['rowscount'] = get_string('err_filloutfield', 'mod_exammanagement');
    }

    if(((isset($data['editplaces']) && $data['editplaces'] == 1) || !isset($data['editplaces'])) && $data['placesmode'] == 'all_individual' && ( !isset($data['placesarray'])|| $data['placesarray'] == '')){ // if existing room and places should be edited or new room and placesmode is all individual and placesarray is not set or empty
      $errors['placesarray'] = get_string('err_filloutfield', 'mod_exammanagement');
    } else if(((isset($data['editplaces']) && $data['editplaces'] == 1) || !isset($data['editplaces'])) && $data['placesmode'] == 'all_individual' && !preg_match('/^[a-zA-Z0-9,\-.\/ ]+$/', $data['placesarray'])){ // if existing room and places should be edited or new room and placesmode is all individual and placesarray contains non alphanumerical chars
      $errors['placesarray'] = get_string('err_noalphanumeric', 'mod_exammanagement');
    }

    return $errors;
  }
}
