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
 * class containing textfieldForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
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

class configureTasksForm extends moodleform {

    //Add elements to form
    public function definition() {

        global $PAGE;

        $mform = $this->_form; // Don't forget the underscore!

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

        //$PAGE->requires->js_call_amd('mod_exammanagement/configure_tasks', 'init'); //call jquery for tracking input value change events
        $PAGE->requires->js_call_amd('mod_exammanagement/configure_tasks', 'addtask'); //call jquery for adding tasks
        $PAGE->requires->js_call_amd('mod_exammanagement/configure_tasks', 'removetask'); //call jquery for removing tasks

        $mform->addElement('html', '<div class="row"><h3 class="col-xs-10">'.get_string('configure_tasks_str', 'mod_exammanagement').'</h3>');
        $mform->addElement('html', '<div class="col-xs-2"><a class="pull-right" type="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');
        $mform->addElement('html', '</div>');

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('configureTasks'));

        $mform->addElement('html', '<p>'.get_string('configure_tasks_text', 'mod_exammanagement').'</p>');

        //group for add and remove tasks buttons
        $tasks_buttonarray = array();
        array_push($tasks_buttonarray, $mform->createElement('button', 'add_task', '<i class="fa fa-plus" aria-hidden="true"></i>'));
        array_push($tasks_buttonarray, $mform->createElement('button', 'remove_task', '<i class="fa fa-minus" aria-hidden="true"></i>'));
        $mform->addGroup($tasks_buttonarray, 'tasks_buttonarray', get_string('add_remove_tasks', 'mod_exammanagement'), array(' '), false);

        //create list of tasks
        $tasks = $ExammanagementInstanceObj->getTasks();
        $totalpoints = $ExammanagementInstanceObj->getTaskTotalPoints();

        if (!$totalpoints){
          $totalpoints = 0;
        }

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

              array_push($tasknumbers_array, $mform->createElement('html', '<span class="task_spacing"><strong>'.$oldtaskcount.'</strong></span>'));

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
            array_push($tasknumbers_array, $mform->createElement('html', '<span class="task_spacing"><strong>'.$newtaskcount.'</strong></span>'));

            //input field with points
            array_push($tasks_array, $mform->createElement('text', 'task['.$newtaskcount.']', '', $attributes));
            $mform->setType('task['.$newtaskcount.']', PARAM_INT);
            $mform->setDefault('task['.$newtaskcount.']', $temptaskpoints);

            $totalpoints += $temptaskpoints;
         }
      }

      if(!$tasks && !$temptaskcount) {
          array_push($tasknumbers_array, $mform->createElement('html', '<span class="task_spacing"><strong>1</strong></span>'));
          array_push($tasks_array, $mform->createElement('text', 'task[1]', '', $attributes));

          $mform->setType('task[1]', PARAM_INT);
          $mform->setDefault('task[1]', 0);

          $oldtaskcount=1;

        }

        $mform->addGroup($tasknumbers_array, 'tasknumbers_array', get_string('task', 'mod_exammanagement'), '', false);
        $mform->addGroup($tasks_array, 'tasks_array', get_string('points', 'mod_exammanagement'), ' ', false);

        $mform->addelement('html', '<div class="row"><strong><span class="col-md-3">'.get_string('total', 'mod_exammanagement').':</span><span class="col-md-9" id="totalpoints">'.$totalpoints.'</span></strong></div>');

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        if(!$tasks && !$temptaskcount) {
            $mform->addElement('hidden', 'newtaskcount', 1);
            $mform->setType('newtaskcount', PARAM_INT);
        } else{
            $mform->addElement('hidden', 'newtaskcount', 0);
            $mform->setType('newtaskcount', PARAM_INT);
        }

        $this->add_action_buttons();
    }

    //Custom validation should be added here
    function validation($data, $files) {

        $errors= array();
        var_dump($data);

        foreach($data['task'] as $taskval){
            $floatval = floatval($taskval);
            var_dump($floatval);

            if($taskval && !$floatval){
                var_dump('error no float');
                var_dump($taskval);
                var_dump($floatval);
                $errors['nofloat']= get_string('err_nofloat', 'mod_exammanagement');
                break;
            } else if($floatval<0) {
              var_dump("error under 0");
              $errors['underzero']= get_string('shortnametaken', 'mod_exammanagement');
              break;
            }
        }

        return $errors;
    }
}
