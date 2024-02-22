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
 * The form for entering the exam results for the participants in an exammanagement.
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
 * The form for entering the exam results for the participants in an exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_exammanagement_inputresults_form extends moodleform {

    /**
     * Define the form - called by parent constructor.
     */
    public function definition() {

        global $PAGE, $OUTPUT;

        $jsargs = ['lang' => current_language()];

        // Call jquery for tracking input value change events.
        $PAGE->requires->js_call_amd('mod_exammanagement/input_results', 'init', $jsargs);

        $mform = $this->_form;

        // Create hidden id field.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Create hidden field that indicates matr validation submit.
        $mform->addElement('hidden', 'matrval', 1);
        $mform->setType('matrval', PARAM_INT);

        if ($this->_customdata['matrnr']) {

            $mform->addElement('html', '<hr /><strong><p>' .
                get_string('exam_participant', 'mod_exammanagement') . '</p></strong>');

            // Create input field for matrnr.

            $mform->addElement('text', 'matrnr', get_string('matrnr', 'mod_exammanagement'));

            $mform->setType('matrnr', PARAM_TEXT);

            if ($this->_customdata['firstname'] && $this->_customdata['lastname']) {
                $mform->addElement('static', 'participant', '<strong><p>' .
                    get_string('participant', 'mod_exammanagement') . '</p></strong>',
                    $this->_customdata['firstname'] . ' '. $this->_customdata['lastname'] .
                    ' <a class="btn btn-secondary ml-5" href="' .
                    new moodle_url('/mod/exammanagement/inputresults.php', ['id' => $this->_customdata['id']]) .
                    '" role="button" title="' . get_string("input_other_matrnr", "mod_exammanagement") .
                    '"><span class="d-none d-lg-block">' . get_string("input_other_matrnr", "mod_exammanagement") .
                    '</span><i class="fa fa-edit d-lg-none" aria-hidden="true"></i></a>');
            }

            // Create list of tasks.
            $mform->addElement('html', '<hr /><strong><p>' . get_string('exam_points', 'mod_exammanagement') . '</p></strong>');

            $tasks = helper::gettasks($this->_customdata['moduleinstance']);
            $totalpoints = 0;

            // Add label.
            $mform->addElement('html', '<div class="form-group row" style="margin-bottom:auto;"><div class="col-3">');

            $mform->addElement('html', '<span><strong>' . get_string('task', 'mod_exammanagement') . '</strong></span>');
            $mform->addElement('html', '<br><span>' . get_string('max_points', 'mod_exammanagement') . '</span>');
            $mform->addElement('html', '<br><span style="position: relative; top: 15px;">' .
                get_string('points', 'mod_exammanagement') . '</span></span></div><div class="col-9">');
            $mform->addElement('html', '<div class="form-group row fitem tasksarea" style="margin-bottom:auto;">');

            if ($tasks) {

                foreach ($tasks as $tasknumber => $points) {

                    // Number of the task.
                    $mform->addElement('html', '<span class="exammanagement_task_spacing"><strong>' .
                        $tasknumber . '</strong><br>');

                    // Max points of the task.
                    $mform->addElement('html', '<span class="exammanagement_task_spacing">' .
                        helper::formatnumberfordisplay($points) . '</span>');

                    // Input field with the task points achieved in the exam.
                    $mform->addElement('text', 'points[' . $tasknumber . ']', '', []);
                    $mform->setType('points[' . $tasknumber . ']', PARAM_FLOAT);
                    $mform->setDefault('points[' . $tasknumber . ']', '');

                    $mform->addElement('html', '</span>');
                }

                $mform->addElement('html', '</div>');
            }

            $mform->addElement('html', '</div></div>');

            $mform->hideif ('tasknumbers_array', 'matrval', 'eq', 1);
            $mform->hideif ('tasks_array', 'matrval', 'eq', 1);
            $mform->hideif ('points_array', 'matrval', 'eq', 1);

            $mform->addelement('html', '<div class="form-group row fitem"><strong><span class="col-md-3">' .
                get_string('total', 'mod_exammanagement') . ':</span><span class="col-md-9" id="totalpoints">' .
                helper::formatnumberfordisplay($totalpoints) . '</span></strong></div>');

            // Create checkboxes for exams state.

            $mform->addElement('html', '<hr /><strong><p>' . get_string('exam_state', 'mod_exammanagement') . '</p></strong>');

            $mform->addElement('advcheckbox', 'state[nt]', get_string('not_participated', 'mod_exammanagement'), null,
                ['group' => 1]);
            $mform->addElement('advcheckbox', 'state[fa]', get_string('fraud_attempt', 'mod_exammanagement'), null, ['group' => 1]);
            $mform->addElement('advcheckbox', 'state[ill]', get_string('ill', 'mod_exammanagement'), null, ['group' => 1]);

            $mform->hideif ('state[nt]', 'matrval', 'eq', 1);
            $mform->hideif ('state[fa]', 'matrval', 'eq', 1);
            $mform->hideif ('state[ill]', 'matrval', 'eq', 1);

            end($tasks);
            $lastkey = key($tasks);

            $mform->addElement('hidden', 'lastkeypoints', $lastkey);
            $mform->setType('lastkeypoints', PARAM_INT);

            $mform->addElement('html', '<hr />');
            $this->add_action_buttons(true, get_string("save_and_next", "mod_exammanagement"));

        } else {
            $mform->addElement('text', 'matrnr', get_string('matrnr_barcode', 'mod_exammanagement'), '');
            $mform->setType('matrnr', PARAM_TEXT);

            $this->add_action_buttons(true, get_string("validate_matrnr", "mod_exammanagement"));
        }

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

        $savedtasksarr = array_values(helper::gettasks($this->_customdata['moduleinstance']));

        if ($data['matrval'] == 0 && !$data['state']['nt'] && !$data['state']['ill'] && !$data['state']['fa']) {
            foreach ($data['points'] as $task => $points) {

                $floatval = floatval($points);
                $isnumeric = is_numeric($points);

                if (($points && !$floatval) || !$isnumeric) {
                    $errors['points[' . $task .']'] = get_string('err_novalidinteger', 'mod_exammanagement');
                } else if ($points < 0) {
                    $errors['points[' . $task . ']'] = get_string('err_underzero', 'mod_exammanagement');
                } else if ($points > $savedtasksarr[$task - 1]) {
                     $errors['points[' . $task . ']'] = get_string('err_taskmaxpoints', 'mod_exammanagement');
                }
            }
        }

        return $errors;
    }
}
