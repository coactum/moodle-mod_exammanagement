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
 * class containing dateForm for exammanagement
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
 
class dateTimeForm extends moodleform {
    
    //Add elements to form
    public function definition() {
 
        $mform = $this->_form; // Don't forget the underscore! 
 
        $mform->addElement('html', '<h3>Prüfungstermin festlegen</h3>');
 		$mform->addElement('html', '<p>Datum und Uhrzeit der Prüfung setzen.</p>'); 		
        $mform->addElement('date_time_selector', 'examtime', '');
        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);
        $this->add_action_buttons();
    }
    
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}