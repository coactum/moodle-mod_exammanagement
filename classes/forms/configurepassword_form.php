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
 * The form for configuring a password for the mod_exammanagement instance.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\Moodle;
use moodleform;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/Moodle.php');

/**
 * The form for configuring a password for the mod_exammanagement instance.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class configurepassword_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $MoodleObj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $mform = $this->_form; // Don't forget the underscore!

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<div class="row"><div class="col-6"><h3>'.get_string('configurePassword', 'mod_exammanagement'));

        if ($helptextsenabled) {
            global $OUTPUT;

            $mform->addElement('html', $OUTPUT->help_icon('configurePassword', 'mod_exammanagement', ''));
        }

        $mform->addElement('html', '</h3></div><div class="col-6">');

        if ($ExammanagementInstanceObj->getModuleinstance()->password) {
            $mform->addElement('html', '<a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/configurePassword.php', $this->_customdata['id'], 'resetPW', true).'" role="button" class="btn btn-primary pull-right" title="'.get_string("reset_password", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("reset_password", "mod_exammanagement").'</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>');
        }

        $mform->addElement('html', '</div></div>');

        $mform->addElement('html', '<p>' . get_string('configure_password', 'mod_exammanagement') . '</p>');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $attributes = array('size' => '20');

 		$mform->addElement('password', 'password', get_string('password', 'mod_exammanagement'), $attributes);
        $mform->setType('password', PARAM_TEXT);
        $mform->addRule('password', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

        $mform->addElement('password', 'confirm_password', get_string('confirmpassword', 'mod_exammanagement'), $attributes);
        $mform->setType('confirm_password', PARAM_TEXT);
        $mform->addRule('confirm_password', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

        $this->add_action_buttons();

    }

    // Custom validation.
    function validation($data, $files) {
        $errors= array();

        if ($data['password'] === '' || $data['password'] === ' ' || $data['password'] === '0' || $data['password'] === 0) {
            $errors['password'] = get_string('err_novalidpassword', 'mod_exammanagement');
        } else if ($data['password'] && $data['confirm_password']) {
            if (strcmp($data['password'], $data['confirm_password']) !== 0) {
                $errors['password'] = get_string('err_password_incorrect', 'mod_exammanagement');
                $errors['confirm_password'] = get_string('err_password_incorrect', 'mod_exammanagement');
            }
        }

        return $errors;
    }
}
