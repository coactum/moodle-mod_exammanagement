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
 * The form for sending group messages for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\Moodle;
use mod_exammanagement\general\User;

use moodleform;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/Moodle.php');
require_once(__DIR__.'/../general/User.php');

/**
 * The form for sending group messages for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sendgroupmessage_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {

        $exammanagementinstanceobj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $moodleobj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $userobj = User::getInstance($this->_customdata['id'], $this->_customdata['e'], $exammanagementinstanceobj->getCm()->instance);

        $mform = $this->_form;

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<h3>'.get_string("sendGroupmessage", "mod_exammanagement"));

        if ($helptextsenabled) {
            global $OUTPUT;
            $mform->addElement('html', $OUTPUT->help_icon('sendGroupmessage', 'mod_exammanagement', ''));
        }

        $mform->addElement('html', '</h3>');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $moodleparticipantscount = $userobj->getParticipantsCount('moodle');
        $nonemoodleparticipantscount = $userobj->getParticipantsCount('nonmoodle');

        if ($moodleparticipantscount) {

            $mform->addElement('html', '<p>'.get_string('groupmessages_text', 'mod_exammanagement', ['systemname' => $exammanagementinstanceobj->getMoodleSystemName(), 'participantscount' => $moodleparticipantscount]).'</p>');

            if ($nonemoodleparticipantscount) {
                $mailadressarr = $userobj->getNoneMoodleParticipantsEmailadresses();

                $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>');

                $mform->addElement('html', '<p>'.get_string('groupmessages_warning', 'mod_exammanagement', ['systemname' => $exammanagementinstanceobj->getMoodleSystemName(), 'participantscount' => $nonemoodleparticipantscount]).'</p>');

                $mform->addElement('html', '<a href="mailto:?bcc=');

                foreach ($mailadressarr as $adress) {
                    $mform->addElement('html', $adress.';');
                }

                $mform->addElement('html', '" role="button" class="btn btn-primary" title="'.get_string('send_manual_message', 'mod_exammanagement').'">'.get_string('send_manual_message', 'mod_exammanagement').'</a>');

                $mform->addElement('html', '</div>');
            }

            $mform->addElement('html', '<span class="mt-1"><hr></span>');

            $mform->addElement('textarea', 'groupmessages_subject', '<strong>'.get_string('subject', 'mod_exammanagement').'</strong>', 'wrap="virtual" rows="1" cols="50"');
            $mform->setType('groupmessages_subject', PARAM_TEXT);
            $mform->addRule('groupmessages_subject', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');
            $mform->addElement('textarea', 'groupmessages_content', '<strong>'.get_string('content', 'mod_exammanagement').'</strong>', 'wrap="virtual" rows="10" cols="50"');
            $mform->setType('groupmessages_content', PARAM_TEXT);
            $mform->addRule('groupmessages_content', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

            $this->add_action_buttons(true, get_string('send_message', 'mod_exammanagement'));

        } else if ($nonemoodleparticipantscount) {
            $mailadressarr = $userobj->getNoneMoodleParticipantsEmailadresses();

            $mform->addElement('html', '<p><strong>'.$nonemoodleparticipantscount. '</strong>' .get_string('groupmessages_warning_2', 'mod_exammanagement').'</p>');

            $mform->addElement('html', '<a href="mailto:?bcc=');

            foreach ($mailadressarr as $adress) {
                $mform->addElement('html', $adress.';');
            }

            $mform->addElement('html', '" role="button" class="btn btn-primary" title="'.get_string('send_manual_message', 'mod_exammanagement').'">'.get_string('send_manual_message', 'mod_exammanagement').'</a>');

            $mform->addElement('html', '<span class="col-sm-5"></span><a href="'.$exammanagementinstanceobj->getExammanagementUrl("view", $this->_customdata['id']).'" class="btn btn-primary">'.get_string("cancel", "mod_exammanagement").'</a>');

        } else {
            $moodleobj->redirectToOverviewPage('', get_string('no_participants_added', 'mod_exammanagement'), 'error');
        }
    }

    // Custom validation should be added here.
    public function validation($data, $files) {
        return array();
    }
}
