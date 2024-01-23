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
 * The form for assigning places to participants for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;

use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\userhandler;
use mod_exammanagement\general\Moodle;
use mod_exammanagement\output\exammanagement_pagebar;
use moodleform;
use stdclass;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/userhandler.php');
require_once(__DIR__.'/../general/Moodle.php');

/**
 * The form for assigning places to participants for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assignplaces_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        global $PAGE, $CFG, $OUTPUT;

        $exammanagementinstanceobj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $userobj = userhandler::getinstance($this->_customdata['id'], $this->_customdata['e'], $exammanagementinstanceobj->getCm()->instance);
        $moodleobj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $PAGE->requires->js_call_amd('mod_exammanagement/remove_cols', 'remove_cols'); // remove col-md classes for better layout
        $PAGE->requires->js_call_amd('mod_exammanagement/assign_places', 'init'); // call jquery
        $PAGE->requires->js_call_amd('mod_exammanagement/assign_places', 'toggleAvailablePlaces'); // call jquery to enable toggling of new available places

        $mform = $this->_form;

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<div class="d-flex justify-content-between"><h3>'.get_string("assignPlaces", "mod_exammanagement"));

        if ($helptextsenabled) {
            $mform->addElement('html', $OUTPUT->help_icon('assignPlaces', 'mod_exammanagement', ''));
        }

        $mform->addElement('html', '</h3><div>');

        if ($this->_customdata['map']) {
            $mform->addElement('html', '<a href="' . new moodle_url('/mod/exammanagement/assignPlaces.php',
                ['id' => $this->_customdata['id']]) . '" class="btn btn-primary pull-right mr-1 mb-1" title="' .
                get_string("assign_places", "mod_exammanagement") . '"><span class="d-none d-sm-block">' .
                get_string("assign_places", "mod_exammanagement") .
                '</span><i class="fa fa-repeat d-sm-none" aria-hidden="true"></i></a>');
        } else {
            $mform->addElement('html', '<a href="' . new moodle_url('/mod/exammanagement/assignPlaces.php',
                ['id' => $this->_customdata['id'], 'map' => true]) . '" class="btn btn-primary pull-right mr-1 mb-1" title="' .
                get_string("assign_places_manually", "mod_exammanagement") . '"><span class="d-none d-sm-block">' .
                get_string("assign_places_manually", "mod_exammanagement") .
                '</span><i class="fa fa-repeat d-sm-none" aria-hidden="true"></i></a>');
        }

        $assignedplacescount = $exammanagementinstanceobj->getAssignedPlacesCount();

        if ($assignedplacescount) {
            $mform->addElement('html', '<a href="assignPlaces.php?id=' . $this->_customdata['id'] . '&uap=1&sesskey=' . sesskey() . '" role="button" class="btn btn-secondary pull-right mr-1 mb-1" title="'.get_string("revert_places_assignment", "mod_exammanagement").'"><span class="d-none d-md-block">'.get_string("revert_places_assignment", "mod_exammanagement").'</span><i class="fa fa-trash d-md-none" aria-hidden="true"></i></a>');
        }

        $mform->addElement('html', '</div></div>');

        $mform->addElement('html', '<p>'.get_string('assign_places_text', 'mod_exammanagement').'</p>');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $contextid = $exammanagementinstanceobj->getModulecontext()->id;

        if ($this->_customdata['map']) {
            $mform->addElement('html', '<h4 class="d-flex justify-content-center">'.get_string('assign_places_manually', 'mod_exammanagement').'</h4>');

            $allparticipants = $userobj->getexamparticipants(array('mode' => 'all'), array('matrnr'));

            $participants = $userobj->getexamparticipants(array('mode' => 'all'), array('matrnr'), 'name', true, $this->_customdata['pagenr']);
            $examrooms = json_decode($exammanagementinstanceobj->moduleinstance->rooms);

            if ($examrooms && $participants) {

                $pagebar = new exammanagement_pagebar($this->_customdata['id'], 'assignPlaces.php?id=' . $this->_customdata['id'] . '&map=1', sesskey(), $exammanagementinstanceobj->get_pagebar($allparticipants, $this->_customdata['pagenr']), $exammanagementinstanceobj->get_pagecountoptions(),  count($participants), count($allparticipants));
                $mform->addElement('html', $OUTPUT->render($pagebar));

                $mform->addElement('html', '<table class="table table-striped exammanagement_table">');

                $mform->addElement('html', '<thead class="exammanagement_tableheader exammanagement_brand_backgroundcolor">');

                $mform->addElement('html', '<th scope="col">#</th><th scope="col">'.get_string("firstname", "mod_exammanagement").'</th><th scope="col">'.get_string("lastname", "mod_exammanagement").'</th><th scope="col">'.get_string("matriculation_number", "mod_exammanagement").'</th><th scope="col">'.get_string("room", "mod_exammanagement").'</th><th scope="col">'.get_string("place", "mod_exammanagement").'</th><th scope="col">'.get_string("available_places", "mod_exammanagement").'</th>');

                $mform->addElement('html', '</thead>');

                $mform->addElement('html', '<tbody>');

                $mform->addElement('hidden', 'map', true);
                $mform->setType('map', PARAM_INT);

                $mform->addElement('hidden', 'page');
                $mform->setType('page', PARAM_INT);
                $mform->setDefault('page', $this->_customdata['pagenr']);

                $roomoptionsarr = array('not_selected' => '-');

                $i = $exammanagementinstanceobj->pagecount * ($this->_customdata['pagenr'] - 1) + 1;

                $roomplacespatternsarr = array('not_selected' => '-');

                foreach ($examrooms as $id => $roomid) {

                    $roomobj = $exammanagementinstanceobj->getRoomObj($roomid);

                    if ($roomobj) {
                        $roomoptionsarr[$roomid] = $roomobj->name;

                        $decodedplaces = json_decode($roomobj->places);
                        $roomplacespatternsarr[$roomid] = array_shift($decodedplaces) . ', ' . array_shift($decodedplaces) . ', ..., ' . array_pop($decodedplaces) . ' ' . '<a id="show" class="pointer"><i class="fa fa-2x fa-info-circle"></i></a><div class="exammanagement_available_places collapse">'.implode(', ', json_decode($roomobj->places)).'</div>';
                    }
                }

                foreach ($participants as $participant) {
                    $mform->addElement('html', '<tr>');
                    $mform->addElement('html', '<th scope="row" id="'.$i.'">'.$i.'</th>');
                    $mform->addElement('html', '<td>'.$participant->firstname.'</td>');
                    $mform->addElement('html', '<td>'.$participant->lastname.'</td>');
                    $mform->addElement('html', '<td>'.$participant->matrnr.'</td>');
                    $mform->addElement('html', '<td>');

                    $select = $mform->addElement('select', 'rooms['.$participant->id.']', '', $roomoptionsarr);
                    if ($participant->roomid) {
                        $select->setSelected($participant->roomid);
                    } else {
                        $select->setSelected('not_selected');
                    }

                    $mform->addElement('html', '</td><td>');

                    $mform->addElement('text', 'places['.$participant->id.']', '');
                    $mform->setType('places['.$participant->id.']', PARAM_TEXT);

                    if ($participant->place) {
                        $mform->setDefault('places['.$participant->id.']', $participant->place);
                    }

                    $mform->addElement('html', '</td><td id="available_places_'.$participant->id.'">');

                    foreach ($roomplacespatternsarr as $roomid => $places) {
                        if ($participant->roomid == $roomid || ($participant->roomid == null && $roomid == "not_selected")) {
                            $mform->addElement('html', '<div id="'.$roomid.'" class="hideablepattern">' . $places . '</div>');
                        } else {
                            $mform->addElement('html', '<div id="'.$roomid.'" class="hideablepattern hidden">' . $places . '</div>');
                        }
                    }

                    $mform->addElement('html', '</td></tr>');

                    $i++;

                }

                $mform->addElement('html', '</tbody></table>');

            }

            $this->add_action_buttons(true, get_string("assign_places_manually", "mod_exammanagement"));

        } else {
            $mform->addElement('html', '<h4>'.get_string('choose_assignment_mode', 'mod_exammanagement').'</h4>');

            $assignmentmode = $exammanagementinstanceobj->getAssignmentMode();
            if ($assignmentmode) {

                $placesmode = substr($assignmentmode, 0, 1);
                $roommode = substr($assignmentmode, 1, 1);
                $manuallassignment = substr($assignmentmode, 2, 2);

                $mform->addElement('html', '<div class="form-group row  fitem">');
                $mform->addElement('html', '<span class="col-md-3">' . get_string('current_assignment_mode', 'mod_exammanagement') .'</span><span class="col-md-9">');

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

            $select = $mform->addElement('select', 'assignment_mode_places', get_string('assignment_mode_places', 'mod_exammanagement'), array('name' => get_string('mode_places_lastname', 'mod_exammanagement'), 'matrnr' => get_string('mode_places_matrnr', 'mod_exammanagement'), 'random' => get_string('mode_places_random', 'mod_exammanagement')));
            if (isset($placesmode)) {
                $select->setSelected($placesmode);
            } else {
                $select->setSelected('name');
            }

            if ($exammanagementinstanceobj->getRoomsCount() > 1) {
                $select = $mform->addElement('select', 'assignment_mode_rooms', get_string('assignment_mode_rooms', 'mod_exammanagement'), array('1' => get_string('mode_room_ascending', 'mod_exammanagement'), '2' => get_string('mode_room_descending', 'mod_exammanagement')));
                if (isset($roommode) && $roommode !== '') {
                    $select->setSelected($roommode);
                } else {
                    $select->setSelected('2');
                }
            } if ($assignedplacescount && !$exammanagementinstanceobj->allPlacesAssigned()) {
                 $mform->addElement('advcheckbox', 'keep_seat_assignment', get_string('keep_seat_assignment_left', 'mod_exammanagement'), get_string('keep_seat_assignment_right', 'mod_exammanagement'), null, null);
                 $mform->setDefault('keep_seat_assignment', true);
            } else if ($assignedplacescount && $exammanagementinstanceobj->allPlacesAssigned()) {
                $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>'.get_string("all_places_already_assigned", "mod_exammanagement").'</div>');
            }

            $this->add_action_buttons(true, get_string("assign_places", "mod_exammanagement"));
        }

        $mform->disable_form_change_checker();
    }
}
