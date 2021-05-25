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
 * class containing assignPlacesForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2021
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;

use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\User;
use mod_exammanagement\general\Moodle;
use moodleform;
use stdclass;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/User.php');
require_once(__DIR__.'/../general/Moodle.php');

class assignPlacesForm extends moodleform{

    //Add elements to form
    public function definition(){
        global $PAGE, $CFG, $OUTPUT;

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e'], $ExammanagementInstanceObj->getCm()->instance);
        $MoodleObj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $PAGE->requires->js_call_amd('mod_exammanagement/remove_cols', 'remove_cols'); //remove col-md classes for better layout
        $PAGE->requires->js_call_amd('mod_exammanagement/assign_places', 'init'); // call jquery
        $PAGE->requires->js_call_amd('mod_exammanagement/assign_places', 'toggleAvailablePlaces'); // call jquery to enable toggling of new available places

        $mform = $this->_form; // Don't forget the underscore!

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<div class="row"><h3 class="col-md-4">'.get_string("assignPlaces", "mod_exammanagement"));

        if($helptextsenabled){
            $mform->addElement('html', $OUTPUT->help_icon('assignPlaces', 'mod_exammanagement', ''));
        }

        $mform->addElement('html', '</h3><div class="col-md-8">');

        if($this->_customdata['map']){
            $mform->addElement('html', '<a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/assignPlaces.php', $this->_customdata['id']).'" class="btn btn-primary pull-right m-r-1 m-b-1" title="'.get_string("assign_places", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("assign_places", "mod_exammanagement").'</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>');
        } else {
            $mform->addElement('html', '<a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/assignPlaces.php', $this->_customdata['id'], 'map', true).'" class="btn btn-primary pull-right m-r-1 m-b-1" title="'.get_string("assign_places_manually", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("assign_places_manually", "mod_exammanagement").'</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>');
        }

        $assignedplacescount = $ExammanagementInstanceObj->getAssignedPlacesCount();

        if($assignedplacescount){
            $mform->addElement('html', '<a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/assignPlaces.php', $this->_customdata['id'], 'uap', true).'" role="button" class="btn btn-primary pull-right m-r-1 m-b-1" title="'.get_string("revert_places_assignment", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("revert_places_assignment", "mod_exammanagement").'</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>');
        }

        $mform->addElement('html', '</div></div>');

        $mform->addElement('html', '<p>'.get_string('assign_places_text', 'mod_exammanagement').'</p>');

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        $contextid = $ExammanagementInstanceObj->getModulecontext()->id;

        if($this->_customdata['map']){
            $mform->addElement('html', '<h4 class="d-flex justify-content-center">'.get_string('assign_places_manually', 'mod_exammanagement').'</h4>');

            $participants = $UserObj->getExamParticipants(array('mode'=>'all'), array('matrnr')); // get all exam participants sorted by sortmode
            $examrooms = json_decode($ExammanagementInstanceObj->moduleinstance->rooms);

            if($examrooms && $participants){

                $mform->addElement('html', '<div class="exammanagement_tablewrapper">');

                $mform->addElement('html', '<table class="table table-striped exammanagement_table">');

                $mform->addElement('html', '<thead class="exammanagement_tableheader exammanagement_brand_backgroundcolor">');

                $mform->addElement('html', '<th scope="col">#</th><th scope="col">'.get_string("firstname", "mod_exammanagement").'</th><th scope="col">'.get_string("lastname", "mod_exammanagement").'</th><th scope="col">'.get_string("matriculation_number", "mod_exammanagement").'</th><th scope="col">'.get_string("room", "mod_exammanagement").'</th><th scope="col">'.get_string("place", "mod_exammanagement").'</th><th scope="col">'.get_string("available_places", "mod_exammanagement").'</th>');

                $mform->addElement('html', '</thead>');

                $mform->addElement('html', '<tbody>');

                $mform->addElement('hidden', 'map', true);
                $mform->setType('map', PARAM_INT);

                $roomOptionsArr = array('not_selected' => '-');

                $i = 1;

                $roomPlacesPatternsArr = array('not_selected' => '-');

                foreach($examrooms as $id => $roomid){

                    $roomObj = $ExammanagementInstanceObj->getRoomObj($roomid);

                    if($roomObj){
                        $roomOptionsArr[$roomid] = $roomObj->name;

                        $decodedPlaces = json_decode($roomObj->places);
                        $roomPlacesPatternsArr[$roomid] = array_shift($decodedPlaces) . ', ' . array_shift($decodedPlaces) . ', ..., ' . array_pop($decodedPlaces) . ' ' . '<a id="show" class="pointer"><i class="fa fa-2x fa-info-circle"></i></a><div class="exammanagement_available_places collapse">'.implode(', ', json_decode($roomObj->places)).'</div>';
                    }
                }

                foreach($participants as $participant){
                    $mform->addElement('html', '<tr>');
                    $mform->addElement('html', '<th scope="row" id="'.$i.'">'.$i.'</th>');
                    $mform->addElement('html', '<td>'.$participant->firstname.'</td>');
                    $mform->addElement('html', '<td>'.$participant->lastname.'</td>');
                    $mform->addElement('html', '<td>'.$participant->matrnr.'</td>');
                    $mform->addElement('html', '<td>');

                    $select = $mform->addElement('select', 'rooms['.$participant->id.']', '', $roomOptionsArr);
                    if($participant->roomid){
                        $select->setSelected($participant->roomid);
                    } else {
                        $select->setSelected('not_selected');
                    }

                    $mform->addElement('html', '</td><td>');

                    $mform->addElement('text', 'places['.$participant->id.']', '');
                    $mform->setType('places['.$participant->id.']', PARAM_TEXT);

                    if($participant->place){
                        $mform->setDefault('places['.$participant->id.']', $participant->place);
                    }

                    $mform->addElement('html', '</td><td id="available_places_'.$participant->id.'">');

                    foreach($roomPlacesPatternsArr as $roomid => $places){
                        if($participant->roomid == $roomid || ($participant->roomid == NULL && $roomid =="not_selected")){
                            $mform->addElement('html', '<div id="'.$roomid.'" class="hideablepattern">' . $places . '</div>');
                        } else {
                            $mform->addElement('html', '<div id="'.$roomid.'" class="hideablepattern hidden">' . $places . '</div>');
                        }
                    }

                    $mform->addElement('html', '</td></tr>');

                    $i++;

                }

                $mform->addElement('html', '</tbody></table></div>');

            }

            $this->add_action_buttons(true, get_string("assign_places_manually", "mod_exammanagement"));

        } else {
            $mform->addElement('html', '<h4>'.get_string('choose_assignment_mode', 'mod_exammanagement').'</h4>');

            $assignmentmode = $ExammanagementInstanceObj->getAssignmentMode();
            if($assignmentmode){

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

                if(isset($manuallassignment) && $manuallassignment == '1'){
                    $mform->addElement('html', ' | <strong>');
                    $mform->addElement('html', get_string('edited_manually', 'mod_exammanagement'));
                    $mform->addElement('html', ' </strong>');
                }

                $mform->addElement('html', '</span></div>');

            }

            $select = $mform->addElement('select', 'assignment_mode_places', get_string('assignment_mode_places', 'mod_exammanagement'), array('name' => get_string('mode_places_lastname', 'mod_exammanagement'), 'matrnr' => get_string('mode_places_matrnr', 'mod_exammanagement'), 'random' => get_string('mode_places_random', 'mod_exammanagement')));
            if(isset($placesmode)){
                $select->setSelected($placesmode);
            } else {
                $select->setSelected('name');
            }

            if($ExammanagementInstanceObj->getRoomsCount() > 1){
                $select = $mform->addElement('select', 'assignment_mode_rooms', get_string('assignment_mode_rooms', 'mod_exammanagement'), array('1' => get_string('mode_room_ascending', 'mod_exammanagement'), '2' => get_string('mode_room_descending', 'mod_exammanagement')));
                if(isset($roommode) && $roommode !== ''){
                    $select->setSelected($roommode);
                } else {
                    $select->setSelected('2');
                }
            } if($assignedplacescount && !$ExammanagementInstanceObj->allPlacesAssigned()){
                 $mform->addElement('advcheckbox', 'keep_seat_assignment', get_string('keep_seat_assignment_left', 'mod_exammanagement'), get_string('keep_seat_assignment_right', 'mod_exammanagement'), null, null);
                 $mform->setDefault('keep_seat_assignment', true);
            } else if($assignedplacescount && $ExammanagementInstanceObj->allPlacesAssigned()){
                $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>'.get_string("all_places_already_assigned", "mod_exammanagement").'</div>');
            }

            $this->add_action_buttons(true, get_string("assign_places", "mod_exammanagement"));
        }

        $mform->disable_form_change_checker();
    }

    //Custom validation should be added here
    public function validation($data, $files){
        $errors= array();

        return $errors;
    }
}