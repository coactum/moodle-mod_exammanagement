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
 * class containing groupmessagesForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\Moodle;
use mod_exammanagement\general\User;

use moodleform;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/Moodle.php');
require_once(__DIR__.'/../general/User.php');

class groupmessagesForm extends moodleform {

    //Add elements to form
    public function definition() {

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $MoodleObj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('html', '<div class="row"><h3 class="col-xs-10">'.get_string('groupmessages_str', 'mod_exammanagement').'</h3>');
        $mform->addElement('html', '<div class="col-xs-2"><a class="pull-right helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');
        $mform->addElement('html', '</div>');

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('sendGroupmessages'));

        $MoodleParticipants = $UserObj->getAllMoodleExamParticipants();
        $NoneMoodleParticipants = $UserObj->getAllNoneMoodleExamParticipants();

        if($MoodleParticipants){
            $MoodleParticipantsCount = count($MoodleParticipants);
        }

        if($NoneMoodleParticipants){
            $NoneMoodleParticipantsCount = count($NoneMoodleParticipants);
        }

 		if($MoodleParticipantsCount){

			$mform->addElement('html', '<p>'.get_string('groupmessages_text_1', 'mod_exammanagement').'<strong>'.$MoodleParticipantsCount.'</strong>'.get_string('groupmessages_text_2', 'mod_exammanagement').'</p>');
            
            if($NoneMoodleParticipantsCount){
                $mailAdressArr = $UserObj->getNoneMoodleParticipantsEmailadresses();

                $mform->addElement('html', '<p><strong>'.$NoneMoodleParticipantsCount. '</strong>' .get_string('groupmessages_text_3', 'mod_exammanagement').'</p>');

                foreach($mailAdressArr as $adress){
                    $mform->addElement('html', '<a href="mailto:'.$adress.'?subject=""">'.$adress.'</a><br>');
                }
            }

            $mform->addElement('html', '<span class="m-t-1"><hr></span>');

            $mform->addElement('textarea', 'groupmessages_subject', '<strong>Betreff</strong>', 'wrap="virtual" rows="1" cols="50"');
            $mform->setType('groupmessages_subject', PARAM_TEXT);
            $mform->addRule('groupmessages_subject', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');
            $mform->addElement('textarea', 'groupmessages_content', '<strong>Inhalt</strong>', 'wrap="virtual" rows="10" cols="50"');
            $mform->setType('groupmessages_content', PARAM_TEXT);
            $mform->addRule('groupmessages_content', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

			$mform->addElement('hidden', 'id', 'dummy');
			$mform->setType('id', PARAM_INT);
			$this->add_action_buttons(true,'Mail abschicken');
		    }
		else {
            $MoodleObj->redirectToOverviewPage('', 'Es wurden noch keine Teilnehmer zur Prüfung hinzugefügt', 'error');
	   	}
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
