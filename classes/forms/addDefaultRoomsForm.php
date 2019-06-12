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
 * class containing addDefaultRoomsForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
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

class addDefaultRoomsForm extends moodleform {

  //Add elements to form
  public function definition() {

    $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

    $mform = $this->_form; // Don't forget the underscore!

    $mform->addElement('hidden', 'id', 'dummy');
    $mform->setType('id', PARAM_INT);

    $mform->addElement('html', '<div class="row"><h3 class="col-xs-10">'.get_string('addDefaultRooms', 'mod_exammanagement').'</h3>');
    $mform->addElement('html', '<div class="col-xs-2"><a class="pull-right helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;" title="'.get_string("helptext_open", "mod_exammanagement").'"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');
    $mform->addElement('html', '</div>');

    $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('addDefaultRooms'));

    $mform->addElement('html', '<p>'.get_string("import_default_rooms_str", "mod_exammanagement").'</p>');

    if($ExammanagementInstanceObj->getDefaultRooms()){
      $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>'.get_string("default_rooms_already_exists", "mod_exammanagement").'</div>');
    }

    $mform->addElement('filepicker', 'defaultrooms_list', get_string("default_rooms_file_structure", "mod_exammanagement"), null, array('accepted_types' => '.txt'));
    $mform->addRule('defaultrooms_list', get_string('err_nofile', 'mod_exammanagement'), 'required', 'client');

    $this->add_action_buttons(true, get_string("read_file", "mod_exammanagement"));

    $mform->disable_form_change_checker();

  }

  //Custom validation should be added here
  function validation($data, $files) {
    return array();
  }
}
