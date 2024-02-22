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
 * The form for configuring the tasks for an exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_exammanagement\local\helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

/**
 * The form for configuring the tasks for an exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_exammanagement_configuretasks_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {

        global $PAGE;

        $jsargs = ['lang' => current_language()];

        // Call jquery for tracking input value change events and creating input number fields.
        $PAGE->requires->js_call_amd('mod_exammanagement/configure_tasks', 'init', $jsargs);
        // Call jquery for adding tasks.
        $PAGE->requires->js_call_amd('mod_exammanagement/configure_tasks', 'addtask', $jsargs);
        // Call jquery for removing tasks.
        $PAGE->requires->js_call_amd('mod_exammanagement/configure_tasks', 'removetask', $jsargs);

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Group for add and remove tasks buttons.
        $tasksbuttonarray = [];
        array_push($tasksbuttonarray,
            $mform->createElement('button', 'add_task', '<i class="fa fa-plus" aria-hidden="true"></i>'));
        array_push($tasksbuttonarray,
            $mform->createElement('button', 'remove_task', '<i class="fa fa-minus" aria-hidden="true"></i>'));
        $mform->addGroup($tasksbuttonarray,
            'tasks_buttonarray', get_string('add_remove_tasks', 'mod_exammanagement'), [' '], false);

        // Create list of tasks.
        $tasks = helper::gettasks($this->_customdata['moduleinstance']);

        // Add temp tasks to task array (only needed when form is recreated for saving tasks).
        $temptaskcount = $this->_customdata['newtaskcount'];

        // Modified stored tasks (only needed when form is recreated for saving tasks).
        if ($temptaskcount > 0 && $tasks && $temptaskcount < count($tasks)) {
            $tasks = array_slice($tasks, 0, $temptaskcount, true); // Remove deleted tasks.
        } else if ($temptaskcount > 0) {

            $temptaskpoints = 10;

            if (!$tasks) {
                $tasks = [];
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
        $mform->addElement('html', '<br><span style="position: relative; top: 15px;">' .
            get_string('points', 'mod_exammanagement') . '</span></span></div><div class="col-9">');

        // Add tasks to form.
        if (!$tasks) { // No tasks saved yet - add only one task field.
            $mform->addElement('html', '<div class="form-group row fitem tasksarea" style="margin-bottom:auto;">');

            $mform->addElement('html', '<span class="exammanagement_task_spacing"><strong>1</strong>');

            $mform->addElement('text', 'task[1]', '',  []);
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

                $mform->addElement('text', 'task[' . $nr . ']', '', []);
                $mform->setType('task[' . $nr . ']', PARAM_FLOAT);
                $mform->setDefault('task[' . $nr . ']', $points);

                $mform->addElement('html', '</span>');
            }

            $mform->addElement('html', '</div>');

            $mform->addElement('hidden', 'newtaskcount', count($tasks));
            $mform->setType('newtaskcount', PARAM_INT);

            $totalpoints = helper::gettasktotalpoints($this->_customdata['moduleinstance']);
        }

        $mform->addElement('html', '</div></div>');

        // Display total points.
        $mform->addelement('html', '<div class="form-group row  fitem"><span class="col-md-3"><strong>' .
            get_string('total', 'mod_exammanagement') . ':</strong></span><span class="col-md-9" id="totalpoints">' .
            helper::formatnumberfordisplay($totalpoints) . '</span></div>');

        // Action buttons.
        $this->add_action_buttons();

        $mform->disable_form_change_checker();
    }

    /**
     * Custom validation for the form.
     *
     * @param object $data The data from the form.
     * @param object $files The files from the form.
     * @return object $errors The errors.
     */
    public function validation($data, $files) {

        $errors = [];

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
