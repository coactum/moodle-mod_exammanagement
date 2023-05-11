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
 * The form for configuring the tasks for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\user;
use moodleform;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/User.php');

/**
 * The form for configuring the tasks for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class configureTasksForm extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {

        global $PAGE, $OUTPUT;

        $exammanagementinstanceobj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $userobj = User::getInstance($this->_customdata['id'], $this->_customdata['e'], $exammanagementinstanceobj->getCm()->instance);

        $jsargs = array('lang' => current_language());

        $PAGE->requires->js_call_amd('mod_exammanagement/configure_tasks', 'init', $jsargs); // Call jquery for tracking input value change events and creating input number fields.
        $PAGE->requires->js_call_amd('mod_exammanagement/configure_tasks', 'addtask', $jsargs); // call jquery for adding tasks.
        $PAGE->requires->js_call_amd('mod_exammanagement/configure_tasks', 'removetask', $jsargs); // call jquery for removing tasks.

        $mform = $this->_form;

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<h3>'.get_string("configureTasks", "mod_exammanagement"));

        if ($helptextsenabled) {
            $mform->addElement('html', $OUTPUT->help_icon('configureTasks', 'mod_exammanagement', ''));
        }

        $mform->addElement('html', '</h3>');

        $mform->addElement('html', '<p>'.get_string('configure_tasks_text', 'mod_exammanagement').'</p>');

        if ($userobj->getEnteredResultsCount()) {
            $mform->addElement('html', '<div class="alert alert-warning alert-block fade in"
                role="alert"><button type="button" class="close" data-dismiss="alert">×</button>' . get_string("results_already_entered", "mod_exammanagement") . '</div>');
        }

        if ($exammanagementinstanceobj->getGradingscale()) {
            $mform->addElement('html', '<div class="alert alert-warning alert-block fade in"
                role="alert"><button type="button" class="close" data-dismiss="alert">×</button>' . get_string("gradingscale_already_entered", "mod_exammanagement") . '</div>');
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Group for add and remove tasks buttons.
        $tasksbuttonarray = array();
        array_push($tasksbuttonarray, $mform->createElement('button', 'add_task', '<i class="fa fa-plus" aria-hidden="true"></i>'));
        array_push($tasksbuttonarray, $mform->createElement('button', 'remove_task', '<i class="fa fa-minus" aria-hidden="true"></i>'));
        $mform->addGroup($tasksbuttonarray, 'tasks_buttonarray', get_string('add_remove_tasks', 'mod_exammanagement'), array(' '), false);

        // Create list of tasks.
        $tasks = $exammanagementinstanceobj->getTasks();

        // Add temp tasks to task array (only needed when form is recreated for saving tasks).
        $temptaskcount = $this->_customdata['newtaskcount'];

        // Modified stored tasks (only needed when form is recreated for saving tasks).
        if ($temptaskcount > 0 && $tasks && $temptaskcount < count($tasks)) {
            $tasks = array_slice($tasks, 0, $temptaskcount, true); // Remove deleted tasks.
        } else if ($temptaskcount > 0) {

            $temptaskpoints = 10;

            if (!$tasks) {
                $tasks = array();
            }

            $lasttask = count($tasks);
            $addtasks = $temptaskcount - $lasttask;
            for ($i = 1; $i <= $addtasks; $i++) {
                $tasks[$lasttask + $i] = $temptaskpoints; // Add new tasks.
            }
        }

        // Add label.
        $mform->addElement('html', '<div class="form-group row" style="margin-bottom:auto;"><div class="col-3">');

        $mform->addElement('html', '<span><strong>' . get_string('task', 'mod_exammanagement') . '</strong></span>');
        $mform->addElement('html', '<br><span style="position: relative; top: 15px;">' . get_string('points', 'mod_exammanagement') . '</span></span></div><div class="col-9">');

        // Add tasks to form.
        if (!$tasks) { // No tasks saved yet - add only one task field.
            $mform->addElement('html', '<div class="form-group row fitem tasksarea" style="margin-bottom:auto;">');

            $mform->addElement('html', '<span class="exammanagement_task_spacing"><strong>1</strong>');

            $mform->addElement('text', 'task[1]', '',  array());
            $mform->setType('task[1]', PARAM_FLOAT);
            $mform->setDefault('task[1]', 10);

            $mform->addElement('html', '</span>');

            $mform->addElement('html', '</div>');

            $mform->addElement('hidden', 'newtaskcount', 1);
            $mform->setType('newtaskcount', PARAM_INT);

            $totalpoints = 10;

        } else {  // Already tasks saved.
            $mform->addElement('html', '<div class="form-group row fitem tasksarea" style="margin-bottom:auto;">');

            foreach ($tasks as $nr => $points) {
                $mform->addElement('html', '<span class="exammanagement_task_spacing">
                <strong>' . $nr . '</strong>');

                $mform->addElement('text', 'task[' . $nr . ']', '', array());
                $mform->setType('task[' . $nr . ']', PARAM_FLOAT);
                $mform->setDefault('task[' . $nr . ']', $points);

                $mform->addElement('html', '</span>');
            }

            $mform->addElement('html', '</div>');

            $mform->addElement('hidden', 'newtaskcount', count($tasks));
            $mform->setType('newtaskcount', PARAM_INT);

            $totalpoints = $exammanagementinstanceobj->getTaskTotalPoints();
        }

        $mform->addElement('html', '</div></div>');

        // Display total points.
        $mform->addelement('html', '<div class="form-group row  fitem"><span class="col-md-3"><strong>' . get_string('total', 'mod_exammanagement') .
            ':</strong></span><span class="col-md-9" id="totalpoints">'.$exammanagementinstanceobj->formatNumberForDisplay($totalpoints).'</span></div>');

        // Action buttons.
        $this->add_action_buttons();

        $mform->disable_form_change_checker();
    }

    // Custom validation should be added here.
    public function validation($data, $files) {

        $errors = array();

        foreach ($data['task'] as $key => $taskval) {
            $isnumeric = is_numeric($taskval);

            if (!$isnumeric) {
                $errors['task['.$key.']'] = get_string('err_novalidinteger', 'mod_exammanagement');
            } else if ($taskval <= 0) {
                $errors['task['.$key.']'] = get_string('err_underzero', 'mod_exammanagement');
            } else if ($taskval >= 100) {
                $errors['task['.$key.']'] = get_string('err_toohigh', 'mod_exammanagement');
            }
        }

        return $errors;
    }
}
