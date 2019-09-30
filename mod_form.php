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
 * The main mod_exammanagement configuration form.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package    mod_exammanagement
 * @copyright  coactum GmbH 2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_exammanagement_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('exammanagementname', 'mod_exammanagement'), array('size' => '64', 'autocomplete' => "nope", 'autocorrect' => "off", "spellcheck" => "false"));

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        // Adding the rest of mod_exammanagement settings, spreading all them into this fieldset
        // ... or adding more fieldsets ('header' elements) if needed for better logic.
        $mform->addElement('header', get_string('security_password', 'mod_exammanagement'), get_string('security_password', 'mod_exammanagement'));

        $mform->addElement('password', 'newpassword', get_string('new_password', 'mod_exammanagement'), array('size' => '64', 'autocomplete' => "nope"));
        $mform->setType('newpassword', PARAM_TEXT);
        $mform->addRule('newpassword', get_string('maximumchars', '', 25), 'maxlength', 25, 'client');
        $mform->addHelpButton('newpassword', 'security_password', 'mod_exammanagement');

        $mform->addElement('password', 'confirmnewpassword', get_string('confirm_new_password', 'mod_exammanagement'), array('size' => '64', 'autocomplete' => "off"));
        $mform->setType('newpassword', PARAM_TEXT);
        $mform->addRule('newpassword', get_string('maximumchars', '', 25), 'maxlength', 25, 'client');
        $mform->addHelpButton('confirmnewpassword', 'confirm_new_password', 'mod_exammanagement');

        $mform->addElement('password', 'oldpassword', get_string('old_password', 'mod_exammanagement'), array('size' => '64', 'autocomplete' => "off"));
        $mform->setType('oldpassword', PARAM_TEXT);
        $mform->addRule('oldpassword', get_string('maximumchars', '', 25), 'maxlength', 25, 'client');
        $mform->addHelpButton('oldpassword', 'old_password', 'mod_exammanagement');

        // Add standard grading elements.
        //$this->standard_grading_coursemodule_elements();

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }

    //Custom validation should be added here
    function validation($data, $files) {
        $errors= array();
  
        if($data['newpassword']){
            if ($data['confirmnewpassword'] == Null){
                $errors['confirmnewpassword'] = get_string('err_filloutfield', 'mod_exammanagement');
            } else if(strcmp($data['newpassword'], $data['confirmnewpassword']) !== 0){
                $errors['newpassword'] = get_string('err_password_incorrect', 'mod_exammanagement');
                $errors['confirmnewpassword'] = get_string('err_password_incorrect', 'mod_exammanagement');
            }
        }
  
        return $errors;   
    }
}