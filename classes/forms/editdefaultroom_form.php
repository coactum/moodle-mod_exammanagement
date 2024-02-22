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
 * The form for adding or editing a default room for exammanagements.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_exammanagement\local\helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

/**
 * The form for adding or editing a default room for exammanagements.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_exammanagement_editdefaultroom_form extends moodleform {

    /**
     * Define the form - called by parent constructor.
     */
    public function definition() {

        global $PAGE, $OUTPUT;

        // Call jquery for disabling roomid field if existing room is edited.
        $PAGE->requires->js_call_amd('mod_exammanagement/edit_defaultroom', 'init');

        $mform = $this->_form;

        $mform->addElement('html', '<hr>');
        $mform->addElement('html', '<p><strong>' . get_string('general', 'mod_exammanagement') . '</strong></p>');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'existingroom', 0);
        $mform->setType('existingroom', PARAM_INT);

        $attributes = ['size' => '22'];

        $mform->addElement('text', 'roomid', get_string('roomid_internal', 'mod_exammanagement'), $attributes);
        $mform->setType('roomid', PARAM_TEXT);
        $mform->addRule('roomid', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

        $mform->addElement('text', 'roomname', get_string('defaultroom_name', 'mod_exammanagement'), $attributes);
        $mform->setType('roomname', PARAM_TEXT);
        $mform->addRule('roomname', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

        $mform->addElement('text', 'description', get_string('defaultroom_description', 'mod_exammanagement'), $attributes);
        $mform->setType('description', PARAM_TEXT);
        $mform->addRule('description', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

        if (isset($this->_customdata['existingroom']) && $this->_customdata['existingroom'] === true) {

            if (isset($this->_customdata['placescount']) && $this->_customdata['placescount'] !== false) {
                $mform->addElement('static', 'placescount',
                    get_string('defaultroom_placescount', 'mod_exammanagement'), $this->_customdata['placescount']);
            } else {
                $mform->addElement('static', 'placescount',
                    get_string('defaultroom_placescount', 'mod_exammanagement'), '-');
            }

            if (isset($this->_customdata['placespreview']) && $this->_customdata['placespreview'] !== false) {
                $mform->addElement('static', 'placespreview',
                    get_string('placespreview', 'mod_exammanagement'),
                    '<div>' . $this->_customdata['placespreview'] . '</div>');
            } else {
                $mform->addElement('static', 'placespreview',
                    get_string('placespreview', 'mod_exammanagement'), '-');
            }

            if (isset($this->_customdata['roomplanavailable']) && $this->_customdata['roomplanavailable'] !== '') {
                $mform->addElement('static', 'roomplanavailable', get_string('roomplan_available', 'mod_exammanagement'),
                    '<div class="exammanagement_editdefaultroom_svg">' . $this->_customdata['roomplanavailable'] . '</div>');
            } else {
                $mform->addElement('static', 'roomplanavailable', get_string('roomplan_available', 'mod_exammanagement'), '-');
            }

        }

        $mform->addElement('html', '<hr>');
        $mform->addElement('html', '<p><strong>'.get_string('new_places', 'mod_exammanagement').'</strong></p>');

        $mform->addElement('selectyesno', 'editplaces', get_string('edit_places', 'mod_exammanagement'));
        $mform->hideif ('editplaces', 'existingroom', 'neq', 1);

        $select = $mform->addElement('select', 'placesmode', get_string('places_mode', 'mod_exammanagement'), [
            'default' => get_string('placesmode_default', 'mod_exammanagement'),
            'rows' => get_string('placesmode_rows', 'mod_exammanagement'),
            'all_individual' => get_string('placesmode_all_individual', 'mod_exammanagement'),
        ]);

        if (isset($this->_customdata['existingroom']) && $this->_customdata['existingroom'] === true) {
            $select->setSelected('all_individual');
        } else {
            $select->setSelected('default');
        }

        $mform->addElement('text', 'placesroom', get_string('placesroom', 'mod_exammanagement'), $attributes);
        $mform->setType('placesroom', PARAM_INT);
        $mform->hideif ('placesroom', 'placesmode', 'neq', 'default');

        $mform->addElement('text', 'rowscount', get_string('rowscount', 'mod_exammanagement'), $attributes);
        $mform->setType('rowscount', PARAM_INT);
        $mform->hideif ('rowscount', 'placesmode', 'neq', 'rows');

        $mform->addElement('text', 'placesrow', get_string('placesrow', 'mod_exammanagement'), $attributes);
        $mform->setType('placesrow', PARAM_INT);
        $mform->hideif ('placesrow', 'placesmode', 'neq', 'rows');

        $select = $mform->addElement('select', 'placesfree', get_string('placesfree', 'mod_exammanagement'), [
            1 => get_string('one_place_free', 'mod_exammanagement'),
            2 => get_string('two_places_free', 'mod_exammanagement'),
        ]);
        $mform->hideif ('placesfree', 'placesmode', 'eq', 'all_individual');

        $select = $mform->addElement('select', 'rowsfree', get_string('rowsfree', 'mod_exammanagement'), [
            0 => get_string('no_row_free', 'mod_exammanagement'),
            1 => get_string('one_row_free', 'mod_exammanagement'),
        ]);
        $mform->hideif ('rowsfree', 'placesmode', 'neq', 'rows');

        $attributes = ['size' => '200'];

        $mform->addElement('text', 'places', get_string('placesarray', 'mod_exammanagement'), $attributes);
        $mform->setType('places', PARAM_TEXT);
        $mform->hideif ('places', 'placesmode', 'neq', 'all_individual');

        if (isset($this->_customdata['existingroom']) && $this->_customdata['existingroom'] === true) {
            $mform->hideif ('placesmode', 'editplaces', 'eq', 0);
            $mform->hideif ('placesroom', 'editplaces', 'eq', 0);
            $mform->hideif ('placesrow', 'editplaces', 'eq', 0);
            $mform->hideif ('placesfree', 'editplaces', 'eq', 0);
            $mform->hideif ('rowsfree', 'editplaces', 'eq', 0);
            $mform->hideif ('rowscount', 'editplaces', 'eq', 0);
            $mform->hideif ('places', 'editplaces', 'eq', 0);
        }

        $mform->addElement('html', '<hr>');
        $mform->addElement('html', '<p><strong>' . get_string('new_seatingplan', 'mod_exammanagement') . '</strong></p>');

        $mform->addElement('filepicker', 'defaultroom_svg', get_string("defaultroom_svg", "mod_exammanagement"), null, [
            'accepted_types' => '.txt',
        ]);

        $mform->addElement('html', '<hr>');

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

        global $DB;

        $errors = [];

        $roomid = $data['roomid'];
        $similiarroom = $DB->get_record('exammanagement_rooms', ['roomid' => $roomid]);

         // If roomid is set and not empty and there is already a room with this id.
        if (isset($roomid) && $roomid !== '' && $data['existingroom'] !== 1 && $similiarroom) {

            if (substr($roomid, -2, -1) !== '_') {
                $roomid .= '_1';
            }

            for ($i = 1; $i <= 9; $i++) {
                $roomid = substr_replace($roomid, '_' . $i, -2);

                if ($DB->get_record('exammanagement_rooms', ['roomid' => $roomid]) == false) {
                    $i = 9;
                }
            }

            $errors['roomid'] = get_string('err_already_defaultroom', 'mod_exammanagement') . $roomid;
        }

        // If roomid is set and not empty and contains non alphanumerical chars.
        if (isset($data['roomid']) && $data['roomid'] !== '' && !preg_match('/^[a-zA-Z0-9_.]+$/', $data['roomid'])) {
            $errors['roomid'] = get_string('err_noalphanumeric', 'mod_exammanagement');
        }

        // If roomname is set and not empty and contains non alphanumerical chars.
        if (isset($data['roomname']) && $data['roomname'] !== '' && !preg_match('/^[a-zA-Z0-9. ]+$/', $data['roomname'])) {
            $errors['roomname'] = get_string('err_noalphanumeric', 'mod_exammanagement');
        }

        // If description is set and not empty and contains non alphanumerical chars.
        if (isset($data['description']) && $data['description'] !== ''
            && !preg_match('/^[a-zA-Z0-9\-., äÄüÜöÖ]+$/', $data['description'])) {
            $errors['description'] = get_string('err_noalphanumeric', 'mod_exammanagement');
        }

        // If existing room and places should be edited or new room and placesmode is rows and placesroom is not set or empty.
        if (((isset($data['editplaces']) && $data['editplaces'] == 1) || !isset($data['editplaces']))
            && $data['placesmode'] == 'default' && ( !isset($data['placesroom'])|| $data['placesroom'] == '')) {
            $errors['placesroom'] = get_string('err_filloutfield', 'mod_exammanagement');
        }

        // If existing room and places should be edited or new room and placesmode is rows and placesrow is not set or empty.
        if (((isset($data['editplaces']) && $data['editplaces'] == 1) || !isset($data['editplaces']))
            && $data['placesmode'] == 'rows' && ( !isset($data['placesrow'])|| $data['placesrow'] == '')) {
            $errors['placesrow'] = get_string('err_filloutfield', 'mod_exammanagement');
        }

        // If existing room and places should be edited or new room and placesmode is rows and rowscount is not set or empty.
        if (((isset($data['editplaces']) && $data['editplaces'] == 1) || !isset($data['editplaces']))
            && $data['placesmode'] == 'rows' && ( !isset($data['rowscount'])|| $data['rowscount'] == '')) {
            $errors['rowscount'] = get_string('err_filloutfield', 'mod_exammanagement');
        }

        // If existing room and places should be edited or new room and placesmode is all individual and places is not set or empty.
        if (((isset($data['editplaces']) && $data['editplaces'] == 1) || !isset($data['editplaces']))
            && $data['placesmode'] == 'all_individual' && ( !isset($data['places'])|| $data['places'] == '')) {
            $errors['places'] = get_string('err_filloutfield', 'mod_exammanagement');
        } else if (((isset($data['editplaces']) && $data['editplaces'] == 1) || !isset($data['editplaces']))
            && $data['placesmode'] == 'all_individual' && !preg_match('/^[a-zA-Z0-9,\-.\/ ]+$/', $data['places'])) {
            // If places should be edited in existing room or new room with individual places that contain nonalphanumerical chars.
            $errors['places'] = get_string('err_noalphanumeric', 'mod_exammanagement');
        }

        if (isset($data['roomid']) && strlen($data['roomid']) > 25) {
            $errors['roomid'] = get_string('err_too_long', 'mod_exammanagement');
        }

        if (isset($data['roomname']) && strlen($data['roomname']) > 100) {
            $errors['roomname'] = get_string('err_too_long', 'mod_exammanagement');
        }

        return $errors;
    }
}
