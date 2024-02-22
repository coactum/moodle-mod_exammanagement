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
 * The form for assigning places to participants in an exammanagement.
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

/**
 * The form for assigning places to participants in an exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_exammanagement_assignplaces_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        global $DB, $PAGE, $OUTPUT;

        $moduleinstance = helper::getmoduleinstance($this->_customdata['id'], $this->_customdata['e']);

        // Remove col-md classes for better layout.
        $PAGE->requires->js_call_amd('mod_exammanagement/remove_cols', 'removecols');
        $PAGE->requires->js_call_amd('mod_exammanagement/assign_places', 'init'); // Call jquery.
        // Call jquery to enable toggling of new available places.
        $PAGE->requires->js_call_amd('mod_exammanagement/assign_places', 'toggleAvailablePlaces');

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Manual assignment.
        if ($this->_customdata['map']) {
            $mform->addElement('html', '<h4 class="d-flex justify-content-center">' .
                get_string('assign_places_manually', 'mod_exammanagement') . '</h4>');

            $allparticipants = helper::getexamparticipants($moduleinstance, ['mode' => 'all'], ['matrnr']);

            $participants = helper::getexamparticipants($moduleinstance, ['mode' => 'all'], ['matrnr'], 'name', true,
                $this->_customdata['pagenr']);
            $examrooms = json_decode($moduleinstance->rooms);

            if ($examrooms && $participants) {

                $pagebar = new exammanagement_pagebar($this->_customdata['id'],
                    new moodle_url('/mod/exammanagement/assignplaces.php', ['id' => $this->_customdata['id'], 'map' => 1]),
                    $allparticipants, count($participants), $this->_customdata['pagenr']);

                $mform->addElement('html', $OUTPUT->render($pagebar));

                $mform->addElement('html', '<table class="table table-striped exammanagement_table">');

                $mform->addElement('html', '<thead class="exammanagement_tableheader exammanagement_brand_backgroundcolor">');

                $mform->addElement('html', '<th scope="col">#</th><th scope="col">' .
                    get_string("firstname", "mod_exammanagement") . '</th><th scope="col">' .
                    get_string("lastname", "mod_exammanagement") . '</th><th scope="col">' .
                    get_string("matriculation_number", "mod_exammanagement") . '</th><th scope="col">' .
                    get_string("room", "mod_exammanagement") . '</th><th scope="col">' .
                    get_string("place", "mod_exammanagement") . '</th><th scope="col">' .
                    get_string("available_places", "mod_exammanagement") . '</th>');

                $mform->addElement('html', '</thead>');

                $mform->addElement('html', '<tbody>');

                $mform->addElement('hidden', 'map', true);
                $mform->setType('map', PARAM_INT);

                $mform->addElement('hidden', 'page');
                $mform->setType('page', PARAM_INT);
                $mform->setDefault('page', $this->_customdata['pagenr']);

                $roomoptions = ['not_selected' => '-'];

                $i = helper::getpagecount() * ($this->_customdata['pagenr'] - 1) + 1;

                $roomplacespattern = ['not_selected' => '-'];

                foreach ($examrooms as $id => $roomid) {

                    $room = $DB->get_record('exammanagement_rooms', ['roomid' => $roomid]);

                    if (isset($room) && $room) {
                        $roomoptions[$roomid] = $room->name;

                        $decodedplaces = json_decode($room->places);
                        $roomplacespattern[$roomid] = array_shift($decodedplaces) . ', ' . array_shift($decodedplaces) . ', ..., ' .
                            array_pop($decodedplaces) . ' ' . '<a id="show" class="pointer">
                            <i class="fa fa-2x fa-info-circle"></i></a><div class="exammanagement_available_places collapse">' .
                            implode(', ', json_decode($room->places)) . '</div>';
                    }
                }

                foreach ($participants as $participant) {
                    $mform->addElement('html', '<tr>');
                    $mform->addElement('html', '<th scope="row" id="'.$i.'">'.$i.'</th>');
                    $mform->addElement('html', '<td>'.$participant->firstname.'</td>');
                    $mform->addElement('html', '<td>'.$participant->lastname.'</td>');
                    $mform->addElement('html', '<td>'.$participant->matrnr.'</td>');
                    $mform->addElement('html', '<td>');

                    $select = $mform->addElement('select', 'rooms[' . $participant->id . ']', '', $roomoptions);
                    if ($participant->roomid) {
                        $select->setSelected($participant->roomid);
                    } else {
                        $select->setSelected('not_selected');
                    }

                    $mform->addElement('html', '</td><td>');

                    $mform->addElement('text', 'places[' . $participant->id . ']', '');
                    $mform->setType('places[' . $participant->id . ']', PARAM_TEXT);

                    if ($participant->place) {
                        $mform->setDefault('places[' . $participant->id . ']', $participant->place);
                    }

                    $mform->addElement('html', '</td><td id="available_places_' . $participant->id . '">');

                    foreach ($roomplacespattern as $roomid => $places) {
                        if ($participant->roomid == $roomid || ($participant->roomid == null && $roomid == "not_selected")) {
                            $mform->addElement('html', '<div id="' . $roomid . '" class="hideablepattern">' . $places . '</div>');
                        } else {
                            $mform->addElement('html', '<div id="' . $roomid . '" class="hideablepattern hidden">' .
                                $places . '</div>');
                        }
                    }

                    $mform->addElement('html', '</td></tr>');

                    $i++;

                }

                $mform->addElement('html', '</tbody></table>');

            }

            $this->add_action_buttons(true, get_string("assign_places_manually", "mod_exammanagement"));

        } else {
            $mform->addElement('html', '<h4>' . get_string('choose_assignment_mode', 'mod_exammanagement') . '</h4>');

            $assignmentmode = $moduleinstance->assignmentmode;
            if (isset($assignmentmode)) {

                $placesmode = substr($assignmentmode, 0, 1);
                $roommode = substr($assignmentmode, 1, 1);
                $manuallassignment = substr($assignmentmode, 2, 2);

                $mform->addElement('html', '<div class="form-group row  fitem">');
                $mform->addElement('html', '<span class="col-md-3">' .
                    get_string('current_assignment_mode', 'mod_exammanagement') .
                    '</span><span class="col-md-9">');

                switch ($placesmode) {
                    case '1':
                        $mform->addElement('html', get_string('mode_places_lastname', 'mod_exammanagement'));
                        $placesmode = 'name';
                        break;
                    case '2':
                        $mform->addElement('html', get_string('mode_places_matrnr', 'mod_exammanagement'));
                        $placesmode = 'matrnr';
                        break;
                    case '3':
                        $mform->addElement('html', get_string('mode_places_random', 'mod_exammanagement'));
                        $placesmode = 'random';
                        break;
                    case '4':
                        $mform->addElement('html', get_string('mode_places_manual', 'mod_exammanagement'));
                        break;
                }

                switch ($roommode) {
                    case '1':
                        $mform->addElement('html', ' - ');
                        $mform->addElement('html', get_string('mode_room_ascending', 'mod_exammanagement'));
                        break;
                    case '2':
                        $mform->addElement('html', ' - ');
                        $mform->addElement('html', get_string('mode_room_descending', 'mod_exammanagement'));
                        break;
                    default:
                        break;
                }

                if (isset($manuallassignment) && $manuallassignment == '1') {
                    $mform->addElement('html', ' | <strong>');
                    $mform->addElement('html', get_string('edited_manually', 'mod_exammanagement'));
                    $mform->addElement('html', ' </strong>');
                }

                $mform->addElement('html', '</span></div>');

            }

            $select = $mform->addElement('select', 'assignment_mode_places',
                get_string('assignment_mode_places', 'mod_exammanagement'), [
                    'name' => get_string('mode_places_lastname', 'mod_exammanagement'),
                    'matrnr' => get_string('mode_places_matrnr', 'mod_exammanagement'),
                    'random' => get_string('mode_places_random', 'mod_exammanagement'),
                ]);
            if (isset($placesmode)) {
                $select->setSelected($placesmode);
            } else {
                $select->setSelected('name');
            }

            if (helper::getroomscount($moduleinstance) > 1) {
                $select = $mform->addElement('select', 'assignment_mode_rooms',
                    get_string('assignment_mode_rooms', 'mod_exammanagement'), [
                        '1' => get_string('mode_room_ascending', 'mod_exammanagement'),
                        '2' => get_string('mode_room_descending', 'mod_exammanagement')]);
                if (isset($roommode) && $roommode !== '') {
                    $select->setSelected($roommode);
                } else {
                    $select->setSelected('2');
                }
            }

            // If some or all places are already assigned.
            if ($this->_customdata['assignedplacescount'] != 0 && !helper::allplacesassigned($moduleinstance)) {
                 $mform->addElement('advcheckbox', 'keep_seat_assignment',
                    get_string('keep_seat_assignment_left', 'mod_exammanagement'),
                    get_string('keep_seat_assignment_right', 'mod_exammanagement'), null, null);
                 $mform->setDefault('keep_seat_assignment', true);
            } else if ($this->_customdata['assignedplacescount'] != 0 && helper::allplacesassigned($moduleinstance)) {
                $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert">
                    <button type="button" class="close" data-dismiss="alert">×</button>' .
                    get_string("all_places_already_assigned", "mod_exammanagement") . '</div>');
            }

            $this->add_action_buttons(true, get_string("assign_places", "mod_exammanagement"));
        }

        $mform->disable_form_change_checker();
    }
}
