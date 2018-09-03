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

        $PAGE->requires->js_call_amd('mod_exammanagement/input_results', 'init'); ////call jquery for tracking input value change events

        $mform->addElement('html', '<div class="row"><h3 class="col-xs-10">'.get_string('input_results_str', 'mod_exammanagement').'</h3>');
        $mform->addElement('html', '<div class="col-xs-2"><a class="pull-right" type="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');
        $mform->addElement('html', '</div>');

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('inputResults'));

        //create hidden id field
        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        //create hidden field that indicates matr validation submit
        $mform->addElement('hidden', 'matrval', 1);
        $mform->setType('matrval', PARAM_INT);

        //create input field for matrnr
        $mform->addElement('text', 'matrnr', get_string('matrnr_barcode', 'mod_exammanagement'), '');
        $mform->setType('matrnr', PARAM_INT);

        if ($this->_customdata['firstname'] && $this->_customdata['lastname']){
          $mform->addElement('static', 'participant', '<strong><p>'.get_string('participant', 'mod_exammanagement').'</p></strong>', $this->_customdata['firstname'] . ' '. $this->_customdata['lastname']);
        }

        if($this->_customdata['matrnr']){
            $mform->addElement('html', '<div class="row"><span class="col-md-3"></span><span class="col-xs-9"><a class="btn btn-primary" href="inputResults.php?id='.$this->_customdata['id'].'" role="button" title="'.get_string("input_other_matrnr", "mod_exammanagement").'"><span class="hidden-sm-down">'.get_string("input_other_matrnr", "mod_exammanagement").'</span><i class="fa fa-edit hidden-md-up" aria-hidden="true"></i></a></span></div>');

        }

        //create list of tasks

        if($this->_customdata['matrnr']){
            $mform->addElement('html', '<hr /><strong><p>'.get_string('exam_points', 'mod_exammanagement').'</p></strong>');
        }

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
              array_push($taskspoints_array, $mform->createElement('html', '<span id="max_points_'.$key.'" class="task_spacing_2">'.$points.'</span>'));

              //input field with exam result points
              array_push($points_array, $mform->createElement('text', 'points['.$key.']', '', $attributes));
              $mform->setType('points['.$key.']', PARAM_INT);
              $mform->setDefault('points['.$key.']', 0);

          }

        }

        $mform->addGroup($tasknumbers_array, 'tasknumbers_array', get_string('task', 'mod_exammanagement'), '', false);
        $mform->addGroup($taskspoints_array, 'tasks_array', get_string('max_points', 'mod_exammanagement'), ' ', false);
        $mform->addGroup($points_array, 'tasks_array', get_string('points', 'mod_exammanagement'), ' ', false);

        $mform->hideIf('tasknumbers_array', 'matrval', 'eq', 1);
        $mform->hideIf('tasks_array', 'matrval', 'eq', 1);
        $mform->hideIf('tasks_array', 'matrval', 'eq', 1);

        if($this->_customdata['matrnr']){
            $mform->addelement('html', '<div class="row"><strong><span class="col-md-3">'.get_string('total', 'mod_exammanagement').':</span><span class="col-md-9" id="totalpoints">'.$totalpoints.'</span></strong></div>');
        }

        //create checkboxes for exams state
        if($this->_customdata['matrnr']){
            $mform->addElement('html', '<hr /><strong><p>'.get_string('exam_state', 'mod_exammanagement').'</p></strong>');
        }

        $mform->addElement('advcheckbox', 'state[nt]', get_string('not_participated', 'mod_exammanagement'), null, array('group' => 1));
        $mform->addElement('advcheckbox', 'state[fa]', get_string('fraud_attempt', 'mod_exammanagement'), null, array('group' => 1));
        $mform->addElement('advcheckbox', 'state[ill]', get_string('ill', 'mod_exammanagement'), null, array('group' => 1));

        $mform->hideIf('state[nt]', 'matrval', 'eq', 1);
        $mform->hideIf('state[fa]', 'matrval', 'eq', 1);
        $mform->hideIf('state[ill]', 'matrval', 'eq', 1);

        if($this->_customdata['matrnr']){
            $mform->addElement('html', '<hr />');
        }

        $this->add_action_buttons(true, get_string("save_and_next", "mod_exammanagement"));

        if(!$this->_customdata['matrnr']){
            $mform->addElement('html', '<hr /><strong><p>'.get_string('confirm_matrnr', 'mod_exammanagement').'</p></strong>');
        }

        $mform->disable_form_change_checker();

    }

    //Custom validation should be added here
    function validation($data, $files) {

        var_dump('Validiere Formularinhalt vor dem abschicken');
        var_dump($data);
        return array();
    }
}
