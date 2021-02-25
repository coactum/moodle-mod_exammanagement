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
 * class containing assignPlacesForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2021
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;

use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\User;
use mod_exammanagement\general\Moodle;
use moodleform;
use stdclass;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/User.php');
require_once(__DIR__.'/../general/Moodle.php');

class assignPlacesForm extends moodleform{

    //Add elements to form
    public function definition(){
        global $PAGE, $CFG, $OUTPUT;

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e'], $ExammanagementInstanceObj->getCm()->instance);
        $MoodleObj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $mform = $this->_form; // Don't forget the underscore!

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<div class="row"><h3 class="col-md-4">'.get_string("assignPlaces", "mod_exammanagement"));

        if($helptextsenabled){
            $mform->addElement('html', $OUTPUT->help_icon('assignPlaces', 'mod_exammanagement', ''));
        }

        $mform->addElement('html', '</h3><div class="col-md-8">');

        $assignedplacescount = $ExammanagementInstanceObj->getAssignedPlacesCount();

        if($assignedplacescount){
            $mform->addElement('html', '<a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/assignPlaces.php', $this->_customdata['id'], 'uap', true).'" role="button" class="btn btn-primary pull-right m-r-1 m-b-1" title="'.get_string("revert_places_assignment", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("revert_places_assignment", "mod_exammanagement").'</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>');
        }

        $mform->addElement('html', '<a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/participantsOverview.php', $this->_customdata['id']).'" role="button" class="btn btn-primary pull-right m-r-1 m-b-1" title="'.get_string("assign_places_manually", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("assign_places_manually", "mod_exammanagement").'</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>');

        $mform->addElement('html', '</div></div>');

        $mform->addElement('html', '<p>'.get_string('assign_places_text', 'mod_exammanagement').'</p>');


        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('html', '<h4>'.get_string('choose_assignment_mode', 'mod_exammanagement').'</h4>');
        $select = $mform->addElement('select', 'assignment_mode_places', get_string('assignment_mode_places', 'mod_exammanagement'), array('name' => get_string('mode_places_lastname', 'mod_exammanagement'), 'matrnr' => get_string('mode_places_matrnr', 'mod_exammanagement'), 'random' => get_string('mode_places_random', 'mod_exammanagement')));
        $select->setSelected('1');
        $select = $mform->addElement('select', 'assignment_mode_rooms', get_string('assignment_mode_rooms', 'mod_exammanagement'), array('1' => get_string('mode_room_ascending', 'mod_exammanagement'), '2' => get_string('mode_room_descending', 'mod_exammanagement'), '3' => get_string('mode_room_equal', 'mod_exammanagement')));
        $select->setSelected('1');

        if($assignedplacescount){
            $mform->addElement('advcheckbox', 'revert_seat_assignment', get_string('revert_seat_assignment_left', 'mod_exammanagement'), get_string('revert_seat_assignment_right', 'mod_exammanagement'), null, null);
            $mform->setDefault('revert_seat_assignment', true);
        }

        $this->add_action_buttons(true, get_string("assign_places", "mod_exammanagement"));
        $mform->disable_form_change_checker();
    }

    //Custom validation should be added here
    public function validation($data, $files){
        $errors= array();

        return $errors;
    }
}
