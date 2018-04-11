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
 * class containing textfieldForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use moodleform;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");

class textfieldForm extends moodleform {

    //Add elements to form
    public function definition() {

        $mform = $this->_form; // Don't forget the underscore!

        $ExammanagementInstanceObj = \mod_exammanagement\general\exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('setTextfield'));

 		    $mform->addElement('html', '<h3>Freitext hinzufügen</h3>');
 		    $mform->addElement('html', '<p>Der unten eingegebene Text wird den Teilnehmern in der Teilnehmeransicht angezeigt.</p>');
 		    $mform->addElement('editor', 'textfield', '');
		    $mform->setType('textfield', PARAM_RAW);

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);
        $this->add_action_buttons();
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
