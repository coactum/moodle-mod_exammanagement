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

namespace mod_exammanagement\general\forms;
use moodleform;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");

class groupmessagesForm extends moodleform {

    //Add elements to form
    public function definition() {

        $ExammanagementInstanceObj = \mod_exammanagement\general\exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $MoodleObj = \mod_exammanagement\general\Moodle::getInstance();

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('sendGroupmessages'));

     	  $mform->addElement('html', '<h3>Nachrichtentext hinzufügen</h3>');

     	  $participantsCount = $ExammanagementInstanceObj->getParticipantsCount();

 		if($participantsCount){

			$mform->addElement('html', '<p>Der unten eingegebene Text wird allen <strong>'.$participantsCount.'</strong> zur Prüfung hinzugefügten Teilnehmern als Email zugeschickt.</p>');
			$mform->addElement('textarea', 'groupmessages_subject', '<strong>Betreff</strong>', 'wrap="virtual" rows="1" cols="50"');
			$mform->addElement('textarea', 'groupmessages_content', '<strong>Inhalt</strong>', 'wrap="virtual" rows="10" cols="50"');
			$mform->addElement('hidden', 'id', 'dummy');
			$mform->setType('id', PARAM_INT);
			$this->add_action_buttons(true,'Mail abschicken');
		    }
		else{
      $MoodleObj->redirectToOverviewPage($this->id, $this->e, '', 'Es wurden noch keine Teilnehmer zur Prüfung hinzugefügt', 'error');
	   		}
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
