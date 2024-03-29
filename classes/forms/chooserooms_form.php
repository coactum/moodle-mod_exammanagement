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
 * The form for choosing exam rooms for an exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_exammanagement\output\exammanagement_pagebar;
use mod_exammanagement\local\helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__.'/../output/exammanagement_pagebar.php');

/**
 * The form for choosing exam rooms for an exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_exammanagement_chooserooms_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {

        global $DB, $PAGE, $OUTPUT;

        // Remove col-md classes for better layout.
        $PAGE->requires->js_call_amd('mod_exammanagement/remove_cols', 'removecols');

        $i = helper::getpagecount() * ($this->_customdata['pagenr'] - 1) + 1;

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'page');
        $mform->setType('page', PARAM_INT);
        $mform->setDefault('page', $this->_customdata['pagenr']);

        if ($this->_customdata['displayrooms']) {

            $pagebar = new exammanagement_pagebar($this->_customdata['id'],
                new moodle_url('/mod/exammanagement/chooserooms.php', ['id' => $this->_customdata['id']]),
                $this->_customdata['allrooms'],
                count($this->_customdata['displayrooms']),
                $this->_customdata['pagenr'],
            );
            $mform->addElement('html', $OUTPUT->render($pagebar));

            $mform->addElement('html', '<div class="table-responsive">');
            $mform->addElement('html', '<table class="table table-striped exammanagement_table">');

            if ($this->_customdata['canimportdefaultrooms']) {
                $mform->addElement('html', '<thead class="exammanagement_tableheader exammanagement_brand_backgroundcolor">
                    <th scope="col">#</th><th scope="col">' . get_string("roomid", "mod_exammanagement") . '</th>
                    <th scope="col">' . get_string("exam_room", "mod_exammanagement") . '</th>
                    <th scope="col">' . get_string("description", "mod_exammanagement") . '</th>
                    <th scope="col">' . get_string("customroom_placescount", "mod_exammanagement") . '</th>
                    <th scope="col">' . get_string("seatingplan", "mod_exammanagement") . '</th>
                    <th scope="col">' . get_string("room_type", "mod_exammanagement") . '</th>
                    <th scope="col" class="exammanagement_table_whiteborder_left"></th></thead>');
            } else {
                $mform->addElement('html', '<thead class="exammanagement_tableheader exammanagement_brand_backgroundcolor">
                    <th scope="col">#</th><th scope="col">' . get_string("exam_room", "mod_exammanagement") . '</th>
                    <th scope="col">' . get_string("description", "mod_exammanagement") . '</th>
                    <th scope="col">' . get_string("customroom_placescount", "mod_exammanagement") . '</th>
                    <th scope="col">' . get_string("seatingplan", "mod_exammanagement") . '</th>
                    <th scope="col">' . get_string("room_type", "mod_exammanagement") . '</th>
                    <th scope="col" class="exammanagement_table_whiteborder_left"></th></thead>');
            }

            $mform->addElement('html', '<tbody>');

            foreach ($this->_customdata['displayrooms'] as $room) {

                $isroomfilled = helper::getparticipantscount($this->_customdata['moduleinstance'], 'room', $room->roomid);

                $similiarrooms = $DB->get_records('exammanagement_rooms', ['name' => $room->name]);;
                $disablename = explode('_', $room->roomid);

                $mform->addElement('html', '<tr>');

                $mform->addElement('html', '<th scope="row" id="' . $i . '">' . $i . '</th>');

                if ($this->_customdata['canimportdefaultrooms']) {

                    $mform->addElement('html', '<td>');

                    $mform->addElement('html', $room->roomid);
                    $mform->addElement('html', '</td>');
                }

                $mform->addElement('html', '<td>');

                $mform->addElement('advcheckbox', 'rooms[' . $room->roomid . ']', $room->name, null, ['group' => 1]);

                 // Code for preventing selection of multiple versions of the same room.
                foreach ($similiarrooms as $similiarroomobj) {

                    if (strpos($similiarroomobj->roomid, $disablename[0]) !== false) {
                        $mform->disabledif ('rooms[' . $room->roomid . ']', 'rooms[' . $similiarroomobj->roomid . ']', 'checked');
                    }
                }

                $mform->addElement('html', '</td><td> ' . $room->description . ' </td>');

                $mform->addElement('html', '<td> ' . count($room->places) . ' </td>');

                $mform->addElement('html', '<td>');
                if ($room->seatingplan) {

                    $svgstr = base64_decode($room->seatingplan);

                    $mform->addElement('html', '<a id="show" class="pointer"><i class="fa fa-2x fa-info-circle"></i></a>
                        <div class="exammanagement_rooms_svg collapse">' . $svgstr . '</div>');

                } else {
                    $mform->addElement('html', get_string('no_seatingplan_available', 'mod_exammanagement'));
                }
                $mform->addElement('html', '</td><td>');

                if ($room->type == 'defaultroom') {
                    if ($this->_customdata['canimportdefaultrooms']) {
                        $mform->addElement('html', get_string('default_room', 'mod_exammanagement'));
                        $mform->addElement('html', '</td><td class="exammanagement_brand_bordercolor_left">');
                        $mform->addElement('html', '<a href="' . new moodle_url('/mod/exammanagement/editdefaultroom.php',
                            ['id' => $this->_customdata['id'], 'roomid' => $room->roomid]) . '" title="' .
                            get_string("change_room", "mod_exammanagement") . '"><i class="fa fa-2x fa-edit"></i></a>');
                        $mform->addElement('html', ' <a href="' . new moodle_url('/mod/exammanagement/chooserooms.php',
                            ['id' => $this->_customdata['id'], 'deletedefaultroomid' => $room->roomid, 'sesskey' => sesskey()]) .
                            '" onClick="javascript:return confirm(\'' .
                            get_string("delete_defaultroom_confirm", "mod_exammanagement") . '\');" title="' .
                            get_string("delete_defaultroom_confirm", "mod_exammanagement") .
                            '"><i class="fa fa-2x fa-trash"></i></a>');

                    } else {
                        $mform->addElement('html', get_string('default_room', 'mod_exammanagement') .
                            '</td><td class="exammanagement_brand_bordercolor_left"></td>');
                    }

                } else {
                    $mform->addElement('html', get_string('custom_room', 'mod_exammanagement'));
                    $mform->addElement('html', '</td><td class="exammanagement_brand_bordercolor_left">');
                    $mform->addElement('html', '<a href="' . new moodle_url('/mod/exammanagement/addcustomroom.php',
                        ['id' => $this->_customdata['id'], 'roomid' => $room->roomid]) . '" title="' .
                        get_string("change_room", "mod_exammanagement") . '"><i class="fa fa-2x fa-edit"></i></a>');
                    $mform->addElement('html', ' <a href="' . new moodle_url('/mod/exammanagement/chooserooms.php',
                        ['id' => $this->_customdata['id'], 'deletecustomroomid' => $room->roomid, 'sesskey' => sesskey()]) .
                        '" onClick="javascript:return confirm(\'' . get_string("delete_room_confirm", "mod_exammanagement") .
                        '\');" title="' . get_string("delete_room", "mod_exammanagement") .
                        '"><i class="fa fa-2x fa-trash"></i></a>');
                }

                if ($this->_customdata['examrooms']) {
                    foreach ($this->_customdata['examrooms'] as $room2) {
                        if ($room->roomid == $room2->roomid) {
                              $mform->setDefault('rooms['.$room->roomid.']', true);
                        }
                    }
                }

                $mform->addElement('html', '</tr>');

                if ($isroomfilled) {

                    $colspan = 7;

                    if ($this->_customdata['canimportdefaultrooms']) {
                        $colspan += 1;
                    }

                    $mform->addElement('html', '<tr><td colspan="' . $colspan . '">
                        <div class="alert alert-warning alert-block fade in " role="alert">' .
                        get_string("places_already_assigned_rooms", "mod_exammanagement") . '</div></td></tr>');
                }
                $i++;
            }

            $mform->addElement('html', '<tr></tr></tbody></table></div>');

            $this->add_action_buttons(true, get_string('choose_rooms', 'mod_exammanagement'));

        } else {
            $mform->addElement('html', '<p>' . get_string('no_rooms_found', 'mod_exammanagement') . '</p>');

        }

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
        $errors = [];

        global $DB;

        foreach ($data['rooms'] as $roomid => $checked) {

            if ($checked == "1") {
                $roomname = explode('_', $roomid);
                $similiarrooms = $DB->get_records('exammanagement_rooms', ['name' => $roomname[0]]);

                foreach ($similiarrooms as $key => $similiarroomobj) {
                    if (isset($data['rooms'][$similiarroomobj->roomid]) && is_string($data['rooms'][$similiarroomobj->roomid])
                        && $data['rooms'][$similiarroomobj->roomid] !== "0"
                        && is_string($data['rooms'][$roomid]) && $similiarroomobj->roomid !== $roomid) {

                        $errors['rooms['.$roomid.']'] = get_string('err_roomsdoubleselected', 'mod_exammanagement');
                    }
                }
            }

            if (!preg_match("/^[a-zA-Z0-9_\-. ]+$/", $roomid)) {
                $errors['rooms['.$roomid.']'] = get_string('err_invalidcheckboxid_rooms', 'mod_exammanagement');
            }
        }

        return $errors;
    }
}
