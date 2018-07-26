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
 * class containing inputResultsForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2018
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

class inputResultsForm extends moodleform {

    //Add elements to form
    public function definition() {

        global $PAGE;

        $mform = $this->_form; // Don't forget the underscore!

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $mform->addElement('html', '<div class="row"><h3 class="col-xs-10">'.get_string('input_results_str', 'mod_exammanagement').'</h3>');
        $mform->addElement('html', '<div class="col-xs-2"><a class="pull-right" type="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');
        $mform->addElement('html', '</div>');

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('inputResults'));

        //create list of tasks
        $tasks = $ExammanagementInstanceObj->getTasks();
        $totalpoints = 0;

        $tasknumbers_array = array();
        $taskspoints_array = array();
        $points_array = array();
        $attributes = array('size'=>'1'); // length of input field

        //add tasks from DB
        if ($tasks){

          foreach($tasks as $key => $points){

              //number of task
              array_push($tasknumbers_array, $mform->createElement('html', '<span class="task_spacing"><strong>'.$key.'</strong></span>'));

              //points of task
              array_push($taskspoints_array, $mform->createElement('html', '<span class="task_spacing">'.$points.'</span>'));

              //input field with exam result points
              array_push($points_array, $mform->createElement('text', 'points['.$key.']', '', $attributes));
              $mform->setType('points['.$key.']', PARAM_INT);
              $mform->setDefault('points['.$key.']', 0);

          }

        }

        $mform->addGroup($tasknumbers_array, 'tasknumbers_array', get_string('task', 'mod_exammanagement'), '', false);
        $mform->addGroup($taskspoints_array, 'tasks_array', get_string('max_points', 'mod_exammanagement'), ' ', false);
        $mform->addGroup($points_array, 'tasks_array', get_string('points', 'mod_exammanagement'), ' ', false);

        $mform->addelement('html', '<div class="row"><strong><span class="col-md-3">'.get_string('total', 'mod_exammanagement').':</span><span class="col-md-9" id="totalpoints">'.$totalpoints.'</span></strong></div>');

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons(true, get_string("save_and_next", "mod_exammanagement"));
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
