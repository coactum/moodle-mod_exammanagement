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
 * The form for setting the password for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use mod_exammanagement\general\Moodle;
use moodleform;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__.'/../general/Moodle.php');

/**
 * The form for setting the password for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class checkpassword_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {

        $moodleobj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $mform = $this->_form;

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<div class="row"><div class="col-6"><h3>'.get_string('checkpassword', 'mod_exammanagement'));

        if ($helptextsenabled) {
            global $OUTPUT;

            if ($moodleobj->checkCapability('mod/exammanagement:resetpassword')) {
                $mform->addElement('html', $OUTPUT->help_icon('checkpasswordadmin', 'mod_exammanagement', ''));
            } else {
                $mform->addElement('html', $OUTPUT->help_icon('checkpassword', 'mod_exammanagement', ''));
            }
        }

        $mform->addElement('html', '</h3></div><div class="col-6">');

        if ($moodleobj->checkCapability('mod/exammanagement:resetpassword')) {
            $mform->addElement('html', '<a href="'.$moodleobj->getMoodleUrl('/mod/exammanagement/checkpassword.php', $this->_customdata['id'], 'resetPW', true).'" role="button" class="btn btn-primary pull-right" title="'.get_string("resetpasswordadmin", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("resetpasswordadmin", "mod_exammanagement").'</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>');
        } else if ($moodleobj->checkCapability('mod/exammanagement:requestpasswordreset') && get_config('mod_exammanagement', 'enablepasswordresetrequest') === '1') {
            $url = new moodle_url('/mod/exammanagement/checkpassword.php', array('id' => $this->_customdata['id'], 'requestPWReset' => true, 'sesskey' => sesskey()));
            $mform->addElement('html', '<a href="' . $url . '" role="button" class="btn btn-secondary pull-right" title="' . get_string("requestpasswordreset", "mod_exammanagement") . '"><span class="d-none d-lg-block">' . get_string("requestpasswordreset", "mod_exammanagement") . '</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>');
        }

        $mform->addElement('html', '</div></div>');

        $mform->addElement('html', '<p>'.get_string('checkpasswordstr', 'mod_exammanagement').'</p>');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $attributes = array('size' => '20');

        $mform->addElement('password', 'password', get_string('password', 'mod_exammanagement'), $attributes);
        $mform->setType('password', PARAM_TEXT);
        $mform->addRule('password', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

        $this->add_action_buttons(true, get_string("confirmpassword", "mod_exammanagement"));

    }
}
