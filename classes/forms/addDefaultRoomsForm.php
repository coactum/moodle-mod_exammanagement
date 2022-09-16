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
 * @copyright   2022 coactum GmbH
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

    global $OUTPUT;

    $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

    $mform = $this->_form; // Don't forget the underscore!

    $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

    $mform->addElement('html', '<h3>'.get_string("addDefaultRooms", "mod_exammanagement"));

    if($helptextsenabled){
        $mform->addElement('html', $OUTPUT->help_icon('addDefaultRooms', 'mod_exammanagement', ''));
    }

    $mform->addElement('html', '</h3>');

    $mform->addElement('html', '<p>'.get_string("import_default_rooms_str", "mod_exammanagement").'</p>');

    if($ExammanagementInstanceObj->countDefaultRooms()){
      $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>'.get_string("default_rooms_already_exists", "mod_exammanagement").'</div>');
    }

    $mform->addElement('hidden', 'id', 'dummy');
    $mform->setType('id', PARAM_INT);

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
