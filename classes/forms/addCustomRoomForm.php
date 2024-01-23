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
 * class containing addCustomRoomForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use mod_exammanagement\general\exammanagementInstance;

use moodleform;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');

/**
 * The form for adding a custom room.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class addCustomRoomForm extends moodleform {

    /**
     * Define the form - called by parent constructor.
     */
    public function definition() {

        global $OUTPUT;

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $mform = $this->_form;

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<h3>'.get_string("addCustomRoom", "mod_exammanagement"));

        if ($helptextsenabled) {
            $mform->addElement('html', $OUTPUT->help_icon('addCustomRoom', 'mod_exammanagement', ''));
        }

        $mform->addElement('html', '</h3>');

        $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>' . get_string("change_custom_room_name", "mod_exammanagement") . '</div>');
        $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>' . get_string("custom_room_places", "mod_exammanagement") . '</div>');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'existingroom', 0);
        $mform->setType('existingroom', PARAM_INT);

        $attributes = array('size' => '20');

        $mform->addElement('text', 'roomname', get_string('customroom_name', 'mod_exammanagement'), $attributes);
        $mform->setType('roomname', PARAM_TEXT);
        $mform->addRule('roomname', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');
        $mform->addElement('text', 'placescount', get_string('customroom_placescount', 'mod_exammanagement'), $attributes);
        $mform->setType('placescount', PARAM_INT);
        $mform->addRule('placescount', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');
        $mform->addRule('placescount', get_string('err_novalidinteger', 'mod_exammanagement'), 'nonzero', 'client');
        $mform->addElement('text', 'description', get_string('customroom_description', 'mod_exammanagement'), $attributes);
        $mform->setType('description', PARAM_TEXT);

        $this->add_action_buttons(true, get_string("add_room", "mod_exammanagement"));

        $mform->disable_form_change_checker();

    }

    /**
     * Custom validation for the form.
     *
     * @param object $data The data from the form.
     * @param object $files The files from the form.
     * @return object $errors The errors.
     */
    public function validation($data, $files) {

        global $USER;

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $errors = array();

        $similiarroom = $ExammanagementInstanceObj->getRoomObj($data['roomname'].'_'.$USER->id.'c');

        if ($data['existingroom'] !== 1 && $similiarroom) {
            $errors['roomname'] = get_string('err_customroomname_taken', 'mod_exammanagement');
        } else if (!preg_match('/^[a-zA-Z0-9_\-. ]+$/', $data['roomname'])) {
            $errors['roomname'] = get_string('err_noalphanumeric', 'mod_exammanagement');
        }

        if (!$data['placescount'] || $data['placescount'] <= 0) {
            $errors['placescount'] = get_string('err_novalidinteger', 'mod_exammanagement');
        }

        if ($data['placescount'] && $data['placescount'] > 10000) {
            $errors['placescount'] = get_string('err_novalidplacescount', 'mod_exammanagement');
        }

        return $errors;
    }
}
