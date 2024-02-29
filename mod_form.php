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
 * This file contains the forms to create and edit an instance of the module.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_exammanagement\local\helper;

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package    mod_exammanagement
 * @copyright  2022 coactum GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_exammanagement_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        if (isset($_GET['update'])) {
            $id = $_GET['update'];
            $moduleinstance = helper::getmoduleinstance($id, false);
            $oldpw = $moduleinstance->password;

            if (isset($moduleinstance->misc)) {
                $misc = (array) json_decode($moduleinstance->misc);
            } else {
                $misc = null;
            }
        } else {
            $oldpw = null;
            $misc = null;
        }

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        if (get_config('mod_exammanagement', 'enableglobalmessage')) {
            $mform->addElement('html', '<div class="alert alert-info alert-block fade in " role="alert">' .
                get_config('mod_exammanagement', 'globalmessage') . '</div>');
        }

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('exammanagement_name', 'mod_exammanagement'),
            ['size' => '64', 'autocomplete' => "nope", 'autocorrect' => "off", "spellcheck" => "false"]);
        $mform->addHelpButton('name', 'exammanagement_name', 'mod_exammanagement');

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Adding the standard "intro" and "introformat" fields.
        $this->standard_intro_elements();

        // Adding the rest of mod_exammanagement settings.
        $mform->addElement('header', 'security_password', get_string('security_password', 'mod_exammanagement'));

        $mform->addElement('password', 'newpassword', get_string('new_password', 'mod_exammanagement'),
           ['size' => '64', 'autocomplete' => "nope"]);
        $mform->setType('newpassword', PARAM_TEXT);
        $mform->addRule('newpassword', get_string('maximumchars', '', 25), 'maxlength', 25, 'client');
        $mform->addHelpButton('newpassword', 'security_password', 'mod_exammanagement');

        $mform->addElement('password', 'confirmnewpassword', get_string('confirm_new_password', 'mod_exammanagement'),
           ['size' => '64', 'autocomplete' => "off"]);
        $mform->setType('newpassword', PARAM_TEXT);
        $mform->addRule('newpassword', get_string('maximumchars', '', 25), 'maxlength', 25, 'client');
        $mform->addHelpButton('confirmnewpassword', 'confirm_new_password', 'mod_exammanagement');

        if (isset($oldpw) || (!isset($_GET['update']) && !isset($_GET['add']))) {
            $mform->addElement('password', 'oldpassword', get_string('old_password', 'mod_exammanagement'),
               ['size' => '64', 'autocomplete' => "off"]);
            $mform->setType('oldpassword', PARAM_TEXT);
            $mform->addRule('oldpassword', get_string('maximumchars', '', 25), 'maxlength', 25, 'client');
            $mform->addHelpButton('oldpassword', 'old_password', 'mod_exammanagement');
        }

        $mform->addElement('header', 'export_grades_as_exam_results',
            get_string('export_grades_as_exam_results', 'mod_exammanagement'));

        $mform->addElement('advcheckbox', 'exportgrades', get_string('activate_mode', 'mod_exammanagement'));
        $mform->addHelpButton('exportgrades', 'export_grades_as_exam_results', 'mod_exammanagement');

        if (isset($misc) && isset($misc['mode']) && $misc['mode'] === 'export_grades') {
            $mform->setDefault('exportgrades', 1);
        } else {
            $mform->setDefault('exportgrades', 0);
        }

        $mform->addElement('header', 'deselectstepsandphases', get_string('deselectstepsandphases', 'mod_exammanagement'));
        $mform->addElement('advcheckbox', 'deselectphaseexamreview', get_string('deselectphaseexamreview', 'mod_exammanagement'));

        if (isset($misc) && isset($misc['configoptions']) && in_array('noexamreview', $misc['configoptions'])) {
            $mform->setDefault('deselectphaseexamreview', 1);
        } else {
            $mform->setDefault('deselectphaseexamreview', 0);
        }

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
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

        if ($data['newpassword']) {
            if ($data['confirmnewpassword'] == null) {
                $errors['confirmnewpassword'] = get_string('err_filloutfield', 'mod_exammanagement');
            } else if (strcmp($data['newpassword'], $data['confirmnewpassword']) !== 0) {
                $errors['newpassword'] = get_string('err_password_incorrect', 'mod_exammanagement');
                $errors['confirmnewpassword'] = get_string('err_password_incorrect', 'mod_exammanagement');
            }
        }

        return $errors;
    }
}
