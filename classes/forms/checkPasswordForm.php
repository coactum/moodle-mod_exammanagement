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
 * class containing checkPasswordForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\Moodle;
use moodleform;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG, $SESSION;
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/Moodle.php');

class checkPasswordForm extends moodleform {

    //Add elements to form
    public function definition() {

        global $OUTPUT;

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $MoodleObj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $mform = $this->_form; // Don't forget the underscore!

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<div class="row"><div class="col-6"><h3>'.get_string('checkPassword', 'mod_exammanagement'));

        if($helptextsenabled){
            if($MoodleObj->checkCapability('mod/exammanagement:resetpassword')){
                $mform->addElement('html', $OUTPUT->help_icon('checkPasswordAdmin', 'mod_exammanagement', ''));
            } else {
                $mform->addElement('html', $OUTPUT->help_icon('checkPassword', 'mod_exammanagement', ''));
            }
        }

        $mform->addElement('html', '</h3></div><div class="col-6">');

        if($MoodleObj->checkCapability('mod/exammanagement:resetpassword')){
            $mform->addElement('html', '<a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/checkPassword.php', $this->_customdata['id'], 'resetPW', true).'" role="button" class="btn btn-primary pull-right" title="'.get_string("reset_password_admin", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("reset_password_admin", "mod_exammanagement").'</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>');
        } else if($MoodleObj->checkCapability('mod/exammanagement:requestpasswordreset') && get_config('mod_exammanagement', 'enablepasswordresetrequest') === '1'){
            $mform->addElement('html', '<a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/checkPassword.php', $this->_customdata['id'], 'requestPWReset', true).'" role="button" class="btn btn-primary pull-right" title="'.get_string("request_password_reset", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("request_password_reset", "mod_exammanagement").'</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>');
        }

        $mform->addElement('html', '</div></div>');

        $mform->addElement('html', '<p>'.get_string('check_password', 'mod_exammanagement').'</p>');

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        $attributes=array('size'=>'20');

 		$mform->addElement('password', 'password', get_string('password', 'mod_exammanagement'), $attributes);
        $mform->setType('password', PARAM_TEXT);
        $mform->addRule('password', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

        $this->add_action_buttons(true, get_string("confirm_password", "mod_exammanagement"));

    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
