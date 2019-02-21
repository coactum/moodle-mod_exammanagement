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
 * class containing examReviewDateRoomForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\User; // for testing

use moodleform;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/User.php');

class examReviewDateRoomForm extends moodleform {

    //Add elements to form
    public function definition() {

        $mform = $this->_form; // Don't forget the underscore!

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $mform->addElement('html', '<div class="row"><h3 class="col-xs-10">'.get_string('examreview_dateroom', 'mod_exammanagement').'</h3>');
        $mform->addElement('html', '<div class="col-xs-2"><a class="pull-right helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;" title="'.get_string("helptext_open", "mod_exammanagement").'"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');
        $mform->addElement('html', '</div>');

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('examReviewDateRoom'));

        $mform->addElement('date_time_selector', 'examreviewtime', get_string('examreview_date', 'mod_exammanagement'));

        $attributes = array('size'=>'20');

        $mform->addElement('text', 'examreviewroom', get_string('examreview_room', 'mod_exammanagement'), $attributes);
        $mform->setType('examreviewroom', PARAM_TEXT);
        $mform->addRule('examreviewroom', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);
        
        $this->add_action_buttons();

        $mform->disable_form_change_checker();

    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
