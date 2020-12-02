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
 * class containing configureTasksForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\user;
use moodleform;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/User.php');

class configureTasksForm extends moodleform {

    //Add elements to form
    public function definition() {

        global $PAGE, $OUTPUT;

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e'], $ExammanagementInstanceObj->getCm()->instance);

        $jsArgs = array('lang'=>current_language());

        $PAGE->requires->js_call_amd('mod_exammanagement/configure_tasks', 'init', $jsArgs); //call jquery for tracking input value change events and creating input type number fields
        $PAGE->requires->js_call_amd('mod_exammanagement/configure_tasks', 'addtask', $jsArgs); //call jquery for adding tasks
        $PAGE->requires->js_call_amd('mod_exammanagement/configure_tasks', 'removetask', $jsArgs); //call jquery for removing tasks

        $mform = $this->_form; // Don't forget the underscore!

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<h3>'.get_string("configureTasks", "mod_exammanagement"));

        if($helptextsenabled){
            $mform->addElement('html', $OUTPUT->help_icon('configureTasks', 'mod_exammanagement', ''));
        }

        $mform->addElement('html', '</h3>');

        $mform->addElement('html', '<p>'.get_string('configure_tasks_text', 'mod_exammanagement').'</p>');

        if($UserObj->getEnteredResultsCount()){
            $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>'.get_string("results_already_entered", "mod_exammanagement").'</div>');
        }

        if($ExammanagementInstanceObj->getGradingscale()){
            $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>'.get_string("gradingscale_already_entered", "mod_exammanagement").'</div>');
        }

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        //group for add and remove tasks buttons
        $tasks_buttonarray = array();
        array_push($tasks_buttonarray, $mform->createElement('button', 'add_task', '<i class="fa fa-plus" aria-hidden="true"></i>'));
        array_push($tasks_buttonarray, $mform->createElement('button', 'remove_task', '<i class="fa fa-minus" aria-hidden="true"></i>'));
        $mform->addGroup($tasks_buttonarray, 'tasks_buttonarray', get_string('add_remove_tasks', 'mod_exammanagement'), array(' '), false);

        //create list of tasks
        $tasks = $ExammanagementInstanceObj->getTasks();
        $totalpoints = $ExammanagementInstanceObj->getTaskTotalPoints();

        $tasknumbers_array = array();
        $tasks_array = array();
        $attributes = array('size'=>'1'); // length of input field
        $oldtaskcount = 0;
        $temptaskcount = $this->_customdata['newtaskcount'];

        //add tasks from DB
        if ($tasks){

          foreach($tasks as $key => $points){

              $oldtaskcount+=1;

              //number of task

              array_push($tasknumbers_array, $mform->createElement('html', '<span class="exammanagement_task_spacing"><strong>'.$oldtaskcount.'</strong></span>'));

              //input field with points
              array_push($tasks_array, $mform->createElement('text', 'task['.$oldtaskcount.']', '', $attributes));
              $mform->setType('task['.$oldtaskcount.']', PARAM_FLOAT);
              $mform->setDefault('task['.$oldtaskcount.']', $points);

          }

        }

        // add temptasks
        if($temptaskcount>0){

          $temptaskpoints = 10;
          //$temptaskpoints = $this->_customdata['newtaskpoints'];

          for ($i = 0; $i < $temptaskcount; $i++) {

            $newtaskcount = intval($oldtaskcount+1+$i);

            //number of task
            array_push($tasknumbers_array, $mform->createElement('html', '<span class="exammanagement_task_spacing"><strong>'.$newtaskcount.'</strong></span>'));

            //input field with points
            array_push($tasks_array, $mform->createElement('text', 'task['.$newtaskcount.']', '', $attributes));
            $mform->setType('task['.$newtaskcount.']', PARAM_FLOAT);
            $mform->setDefault('task['.$newtaskcount.']', $temptaskpoints);

            $totalpoints += $temptaskpoints;

            }
        }

        if(!$tasks && !$temptaskcount) {
            array_push($tasknumbers_array, $mform->createElement('html', '<span class="exammanagement_task_spacing"><strong>1</strong></span>'));
            array_push($tasks_array, $mform->createElement('text', 'task[1]', '', $attributes));

            $mform->setType('task[1]', PARAM_FLOAT);
            $mform->setDefault('task[1]', 0);

            $oldtaskcount=1;

        }

        $mform->addGroup($tasknumbers_array, 'tasknumbers_array', get_string('task', 'mod_exammanagement'), '', false);
        $mform->addGroup($tasks_array, 'tasks_array', get_string('points', 'mod_exammanagement'), ' ', false);

        $mform->addelement('html', '<div class="form-group row  fitem"><span class="col-md-3"><strong>'.get_string('total', 'mod_exammanagement').':</strong></span><span class="col-md-9" id="totalpoints">'.$ExammanagementInstanceObj->formatNumberForDisplay($totalpoints).'</span></div>');

        if(!$tasks && !$temptaskcount) {
            $mform->addElement('hidden', 'newtaskcount', 1);
            $mform->setType('newtaskcount', PARAM_INT);
        } else{
            $mform->addElement('hidden', 'newtaskcount', 0);
            $mform->setType('newtaskcount', PARAM_INT);
        }

        $this->add_action_buttons();

        $mform->disable_form_change_checker();
    }

    //Custom validation should be added here
    function validation($data, $files) {

        $errors= array();

        foreach($data['task'] as $key => $taskval){
            $isnumeric = is_numeric($taskval);

            if(!$isnumeric){
                $errors['task['.$key.']'] = get_string('err_novalidinteger', 'mod_exammanagement');
            } else if($taskval<=0) {
                $errors['task['.$key.']'] = get_string('err_underzero', 'mod_exammanagement');
            } else if($taskval>=100) {
                $errors['task['.$key.']'] = get_string('err_toohigh', 'mod_exammanagement');
            }
        }

        return $errors;
    }
}
