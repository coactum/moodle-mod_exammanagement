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
 * class containing addCustomRoomForm for exammanagement
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

class addCustomRoomForm extends moodleform {

  //Add elements to form
  public function definition() {

    $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

    $mform = $this->_form; // Don't forget the underscore!

    $mform->addElement('hidden', 'id', 'dummy');
    $mform->setType('id', PARAM_INT);
    $mform->addElement('hidden', 'existingroom', 0);
    $mform->setType('existingroom', PARAM_INT);

    $mform->addElement('html', '<div class="row"><h3 class="col-xs-10">'.get_string('addCustomRoom', 'mod_exammanagement').'</h3>');
    $mform->addElement('html', '<div class="col-xs-2"><a class="pull-right helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;" title="'.get_string("helptext_open", "mod_exammanagement").'"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');
    $mform->addElement('html', '</div>');

    $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('addCustomRoom'));
    
    $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>'.get_string("change_custom_room_name", "mod_exammanagement").'</div>');
    $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>'.get_string("custom_room_places", "mod_exammanagement").'</div>');
    
    $attributes = array('size'=>'20');

    $mform->addElement('text', 'roomname', get_string('customroom_name', 'mod_exammanagement'), $attributes);
    $mform->setType('roomname', PARAM_TEXT);
    $mform->addRule('roomname', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');
    $mform->addElement('text', 'placescount', get_string('customroom_placescount', 'mod_exammanagement'), $attributes);
    $mform->setType('placescount', PARAM_INT);
    $mform->addRule('placescount', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');
    $mform->addRule('placescount', get_string('err_novalidinteger', 'mod_exammanagement'), 'nonzero', 'client');
    $mform->addElement('text', 'description', get_string('customroom_description', 'mod_exammanagement'), $attributes);
    $mform->setType('description', PARAM_TEXT);

    $this->add_action_buttons(true, get_string("add_room", "mod_exammanagement"));

    $mform->disable_form_change_checker();

  }

  //Custom validation should be added here
  function validation($data, $files) {

    global $USER;

    $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

    $errors = array();

    $similiarroom = $ExammanagementInstanceObj->getRoomObj($data['roomname'].'_'.$USER->id.'c');

    if($data['existingroom'] !== 1 && $similiarroom){
       $errors['roomname'] = get_string('err_customroomname_taken', 'mod_exammanagement');
    } else if(!preg_match('/^[a-zA-Z0-9_\-. ]+$/', $data['roomname'])){
      $errors['roomname'] = get_string('err_noalphanumeric', 'mod_exammanagement');
    }

    if(!$data['placescount'] || $data['placescount'] <= 0){
       $errors['placescount'] = get_string('err_novalidinteger', 'mod_exammanagement');
    }

    return $errors;
  }
}
