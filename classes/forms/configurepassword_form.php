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
 * The form for configuring a password for an exammanagement instance.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

/**
 * The form for configuring a password for an exammanagement instance.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_exammanagement_configurepassword_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $attributes = ['size' => '20'];

        $mform->addElement('Passwordunmask', 'password', get_string('password', 'mod_exammanagement'), $attributes);
        $mform->setType('password', PARAM_TEXT);
        $mform->addRule('password', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

        $mform->addElement('Passwordunmask', 'confirm_password', get_string('confirmpassword', 'mod_exammanagement'), $attributes);
        $mform->setType('confirm_password', PARAM_TEXT);
        $mform->addRule('confirm_password', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

        $this->add_action_buttons();

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
