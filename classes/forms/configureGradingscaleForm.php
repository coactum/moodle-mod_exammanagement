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
 * The form for configuring the gradingscale for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use mod_exammanagement\general\exammanagementInstance;
use moodleform;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__.'/../general/exammanagementInstance.php');

/**
 * The form for configuring the gradingscale for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class configureGradingscaleForm extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {

        global $PAGE, $OUTPUT;

        $exammanagementinstanceobj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $totalpoints = $exammanagementinstanceobj->getTaskTotalPoints();

        if ($totalpoints) {
            $jsargs = array('totalpoints' => $totalpoints);
        } else {
            $jsargs = array('totalpoints' => 10000);
        }

        $PAGE->requires->js_call_amd('mod_exammanagement/configure_gradingscale', 'init', $jsargs); // Creating input type number fields.

        $mform = $this->_form;

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<h3>'.get_string("configureGradingscale", "mod_exammanagement"));

        if ($helptextsenabled) {
            $mform->addElement('html', $OUTPUT->help_icon('configureGradingscale', 'mod_exammanagement', ''));
        }

        $mform->addElement('html', '</h3>');

        $mform->addElement('html', '<p>'.get_string("configure_gradingscale_text", "mod_exammanagement").'</p>');

        if ($totalpoints) {
            $mform->addElement('html', '<div class="mb-1"><strong class="exammanagement_gradingscale_totalpoints mr-2">'
                . get_string('configure_gradingscale_totalpoints', 'mod_exammanagement').'</strong><span id="totalpoints"> ' .
                $exammanagementinstanceobj->formatNumberForDisplay($totalpoints).'</span></div>');
        }

        // Create gradingscale input list.
        $gradingscale = $exammanagementinstanceobj->getGradingscale();
        $attributes = array('size' => '1'); // Length of input field.

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        if (!$gradingscale) {
            $gradingscale = array(
            "1.0" => 0,
            "1.3" => 0,
            "1.7" => 0,
            "2.0" => 0,
            "2.3" => 0,
            "2.7" => 0,
            "3.0" => 0,
            "3.3" => 0,
            "3.7" => 0,
            "4.0" => 0,
            );
        }

        // Add labels for grading steps.
        $mform->addElement('html', '<div class="form-group row fitem">');

        foreach ($gradingscale as $key => $points) {
            $mform->addElement('html', '<span class="exammanagement_gradingscale_steps_spacing">
                <strong>' . $exammanagementinstanceobj->formatNumberForDisplay($key) . '</strong>');

            $mform->addElement('text', 'gradingsteppoints[' . $key . ']', '', $attributes);
            $mform->setType('gradingsteppoints[' . $key . ']', PARAM_FLOAT);
            $mform->setDefault('gradingsteppoints[' . $key . ']', $points);

            $mform->addElement('html', '</span>');
        }

        $mform->addElement('html', '</div>');

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

        $errors = array();

        $exammanagementinstanceobj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $maxpoints = $exammanagementinstanceobj->getTaskTotalPoints();

        foreach ($data['gradingsteppoints'] as $key => $gradingsteppoints) {

            $floatval = floatval($gradingsteppoints);
            $isnumeric = is_numeric($gradingsteppoints);

            if (($gradingsteppoints && !$floatval) || !$isnumeric) {
                $errors['gradingsteppoints['.$key.']'] = get_string('err_novalidinteger', 'mod_exammanagement');
            } else if ($gradingsteppoints < 0) {
                $errors['gradingsteppoints['.$key.']'] = get_string('err_underzero', 'mod_exammanagement');
            } else if ($maxpoints && $gradingsteppoints > $maxpoints) {
                $errors['gradingsteppoints['.$key.']'] = get_string('err_overmaxpoints', 'mod_exammanagement');
            } else if ($key !== '1.0' && !array_key_exists('gradingsteppoints['.$lastgradingstepkey.']', $errors) && ($lastgradingsteppoints <= $gradingsteppoints)) {
                $errors['gradingsteppoints['.$key.']'] = get_string('err_gradingstepsnotcorrect', 'mod_exammanagement');
                $errors['gradingsteppoints['.$lastgradingstepkey.']'] = get_string('err_gradingstepsnotcorrect', 'mod_exammanagement');

            }

            $lastgradingsteppoints = $gradingsteppoints;
            $lastgradingstepkey = $key;
        }

        return $errors;
    }
}
