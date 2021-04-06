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
 * class containing participantsOverviewForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\User;
use mod_exammanagement\general\MoodleDB;
use mod_exammanagement\general\Moodle;

use moodleform;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG, $PAGE;
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/User.php');
require_once(__DIR__.'/../general/MoodleDB.php');
require_once(__DIR__.'/../general/Moodle.php');

class participantsOverviewForm extends moodleform {

    //Add elements to form
    public function definition() {

        global $PAGE, $OUTPUT;

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e'], $ExammanagementInstanceObj->getCm()->instance);
        $MoodleObj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $MoodleDBObj = MoodleDB::getInstance();

        if($ExammanagementInstanceObj->getTaskCount()){
            $tasks = $ExammanagementInstanceObj->getTasks();
        } else {
            $tasks = false;
        }

        $jsArgs = array('tasks'=>(array) $tasks);

        $PAGE->requires->js_call_amd('mod_exammanagement/participants_overview', 'init', $jsArgs); //call jquery for tracking input value change events and creating input type number fields

        $mform = $this->_form; // Don't forget the underscore!

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<div class="row"><h3 class="col-md-4">'.get_string("participantsOverview", "mod_exammanagement"));

        if($helptextsenabled){
            if($ExammanagementInstanceObj->moduleinstance->misc === NULL){
                $mform->addElement('html', $OUTPUT->help_icon('participantsOverview', 'mod_exammanagement', ''));
            } else {
                $mform->addElement('html', $OUTPUT->help_icon('participantsOverview_grades', 'mod_exammanagement', ''));
            }
        }

        $mform->addElement('html', '</h3><div class="col-md-8">');

        if(!isset($this->_customdata['epm'])){
            $mform->addElement('html', '<a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/participantsOverview.php', $this->_customdata['id'], 'epm', true).'" class="btn btn-primary pull-right" title="'.get_string("edit_results_and_boni", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("edit_results_and_boni", "mod_exammanagement").'</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>');
        }

        $mform->addElement('html', '</div></div>');

        $mform->addElement('html', '<p>'.get_string("participants_overview_text", "mod_exammanagement").'</p>');

        $mform->addElement('html', '<div class="exammanagement_tablewrapper_high">');
        $mform->addElement('html', '<table class="table table-striped exammanagement_table" id="0">');

        $mform->addElement('html', '<thead class="exammanagement_tableheader exammanagement_brand_backgroundcolor">');
        $mform->addElement('html', '<th scope="col">#</th><th scope="col">'.get_string("firstname", "mod_exammanagement").'</th><th scope="col">'.get_string("lastname", "mod_exammanagement").'</th><th scope="col">'.get_string("matriculation_number", "mod_exammanagement").'</th>');

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        if(!isset($this->_customdata['epm'])){ // participants can not be edited
            $mform->addElement('hidden', 'epm', false);
            $mform->setType('epm', PARAM_INT);

            if($ExammanagementInstanceObj->moduleinstance->misc === NULL){

                $mform->addElement('html', '<th scope="col" class="exammanagement_table_width_room">'.get_string("room", "mod_exammanagement").'</th><th scope="col" class="exammanagement_table_width_place">'.get_string("place", "mod_exammanagement").'</th>');

                $mform->addElement('html', '<th scope="col">'.get_string("points", "mod_exammanagement").'</th><th scope="col">'.get_string("totalpoints", "mod_exammanagement").'</th>');

                $mform->addElement('html', '<th scope="col">'.get_string("bonuspoints", "mod_exammanagement").'</th>');

                $mform->addElement('html', '<th scope="col">'.get_string("totalpoints_with_bonuspoints", "mod_exammanagement").'</th>');

                $mform->addElement('html', '<th scope="col">'.get_string("result", "mod_exammanagement").'</th>');

                $mform->addElement('html', '<th scope="col">'.get_string("bonussteps", "mod_exammanagement").'</th>');

                $mform->addElement('html', '<th scope="col">'.get_string("resultwithbonus", "mod_exammanagement").'</th>');


            } else {
                $mform->addElement('html', '<th scope="col">'.get_string("grading_points", "mod_exammanagement").'</th>');
                $mform->addElement('html', '<th scope="col">'.get_string("result_based_of_grades", "mod_exammanagement").'</th>');
            }

        } else { // participants can be edited
            $mform->addElement('hidden', 'epm', true);
            $mform->setType('epm', PARAM_INT);

            if($ExammanagementInstanceObj->moduleinstance->misc === NULL){
                $mform->addElement('html', '<th scope="col">'.get_string("points", "mod_exammanagement").'</th>');

                $mform->addElement('html', '<th scope="col">'.get_string("bonuspoints", "mod_exammanagement").'</th>');

                $mform->addElement('html', '<th scope="col">'.get_string("bonussteps", "mod_exammanagement").'</th>');

                $mform->addElement('html', '<th scope="col">'.get_string("exam_state", "mod_exammanagement").'</th>');

            } else {
                $mform->addElement('html', '<th scope="col">'.get_string("grading_points", "mod_exammanagement").'</th>');
            }

        }

        $mform->addElement('html', '</thead>');

        $mform->addElement('html', '<tbody>');

        $participants = $UserObj->getExamParticipants(array('mode'=>'all'), array('matrnr'));
        $examrooms = json_decode($ExammanagementInstanceObj->moduleinstance->rooms);
        $gradingscale = $ExammanagementInstanceObj->getGradingscale();

        if($participants){

            $i = 1;

            foreach($participants as $key => $participant){

                if(isset($participant->roomname)){
                    $room = $participant->roomname;
                } else {
                    $room = '-';
                }

                if(isset($participant->place)){
                    $place = $participant->place;
                } else {
                    $place = '-';
                }

                $totalpoints = false;

                $state = $UserObj->getExamState($participant);

                if(!$state){
                    $state = 'not_set';
                }

                $exampoints = array_values((array) json_decode($participant->exampoints));

                $totalpoints = $UserObj->calculatePoints($participant);
                $totalpointsDisplay = $ExammanagementInstanceObj->formatNumberForDisplay($totalpoints);
                $totalpointsWithBonus = $UserObj->calculatePoints($participant, true);
                $totalpointsWithBonusDisplay = $ExammanagementInstanceObj->formatNumberForDisplay($totalpointsWithBonus);

                if(!isset($this->_customdata['epm']) || $this->_customdata['epm'] === 0 ){ // if user is non editable
                    $mform->addElement('html', '<tr>');
                    $mform->addElement('html', '<th scope="row" id="'.$i.'">'.$i.'</th>');
                    $mform->addElement('html', '<td>'.$participant->firstname.'</td>');
                    $mform->addElement('html', '<td>'.$participant->lastname.'</td>');
                    $mform->addElement('html', '<td>'.$participant->matrnr.'</td>');

                    if($ExammanagementInstanceObj->moduleinstance->misc === NULL){
                        $mform->addElement('html', '<td>'.$room.'</td>');
                        $mform->addElement('html', '<td>'.$place.'</td>');

                        $mform->addElement('html', '<td>');

                        if($tasks){
                            $mform->addElement('html', '<table class="table-sm"><tr>');

                            foreach($tasks as $tasknumber => $taskmaxpoints){
                                $mform->addElement('html', '<th class="exammanagement_table_with">'.$tasknumber.'</th>');
                            }

                            $mform->addElement('html', '</tr><tr>');

                            foreach($tasks as $tasknumber => $taskmaxpoints){
                                if(isset($exampoints[$tasknumber-1])){
                                    $mform->addElement('html', '<td>'.$ExammanagementInstanceObj->formatNumberForDisplay($exampoints[$tasknumber-1]).'</td>');
                                } else {
                                    $mform->addElement('html', '<td> - </td>');
                                }
                            }

                            $mform->addElement('html', '</tr></table>');
                        } else {
                            $mform->addElement('html', '<a href="configureTasks.php?id='.$this->_customdata['id'].'" title="'.get_string("configure_tasks", "mod_exammanagement").'"><i class="fa fa-2x fa-info-circle text-warning"></i></a>');
                        }

                        $mform->addElement('html', '</td>');

                        # totalpoints
                        $mform->addElement('html', '<td>'.$totalpointsDisplay.'</td>');

                        # bonuspoints
                        if($UserObj->getEnteredBonusCount('points')){
                            if(isset($participant->bonuspoints)){
                                $mform->addElement('html', '<td>'.$ExammanagementInstanceObj->formatNumberForDisplay(number_format($participant->bonuspoints, 2)).'</td>');
                            } else {
                                $mform->addElement('html', '<td>-</td>');
                            }
                        } else {
                            $mform->addElement('html', '<td>-</td>');
                        }
                    }

                    # totalpoints with bonuspoints
                    if($UserObj->getEnteredBonusCount('points')){
                        $mform->addElement('html', '<td>'. $totalpointsWithBonusDisplay .'</td>');
                    } else {
                        $mform->addElement('html', '<td>-</td>');
                    }

                    # result
                    if($gradingscale){
                        $result = $UserObj->calculateResultGrade($totalpointsWithBonus);
                        $mform->addElement('html', '<td>'.$ExammanagementInstanceObj->formatNumberForDisplay($result).'</td>');
                    } else {
                        $mform->addElement('html', '<td><a href="configureGradingscale.php?id='.$this->_customdata['id'].'" title="'.get_string("configure_gradingscale", "mod_exammanagement").'"><i class="fa fa-2x fa-info-circle text-warning"></i></a></td>');
                    }

                    if($ExammanagementInstanceObj->moduleinstance->misc === NULL){
                        if($UserObj->getEnteredBonusCount('steps')){
                            if(isset($participant->bonussteps)){
                                $mform->addElement('html', '<td>'.$participant->bonussteps);

                                if(current_language() === 'de'){
                                    $separator = ',';
                                } else {
                                    $separator = '.';
                                }

                                switch ($participant->bonussteps){

                                    case 0:
                                        break;
                                    case 1:
                                        $mform->addElement('html', ' (= 0'.$separator.'3)');
                                        break;
                                    case 2:
                                        $mform->addElement('html', ' (= 0'.$separator.'7)');
                                        break;
                                    case 3:
                                        $mform->addElement('html', ' (= 1'.$separator.'0)');
                                        break;
                                }

                                $mform->addElement('html', '</td>');
                            } else {
                                $mform->addElement('html', '<td>-</td>');
                            }
                        } else {
                            $mform->addElement('html', '<td>-</td>');
                        }

                        if($gradingscale){
                            $mform->addElement('html', '<td>'.$ExammanagementInstanceObj->formatNumberForDisplay($UserObj->calculateResultGrade($totalpointsWithBonus, $participant->bonussteps)).'</td>');
                        } else {
                            $mform->addElement('html', '<td><a href="configureGradingscale.php?id='.$this->_customdata['id'].'" title="'.get_string("configure_gradingscale", "mod_exammanagement").'"><i class="fa fa-2x fa-info-circle text-warning"></i></a></td>');
                        }
                    }

                } else if(isset($this->_customdata['epm']) && $this->_customdata['epm'] != 0){ // if user is editable
                    $mform->addElement('html', '<tr>');
                    $mform->addElement('html', '<th scope="row" id="'.$i.'">'.$i.'</th>');
                    $mform->addElement('html', '<td>'.$participant->firstname.'</td>');
                    $mform->addElement('html', '<td>'.$participant->lastname.'</td>');
                    $mform->addElement('html', '<td>'.$participant->matrnr.'</td>');

                    if($ExammanagementInstanceObj->moduleinstance->misc === NULL){

                        $mform->addElement('html', '<td class="p-1">');

                        if($tasks){

                            $mform->addElement('html', '<table class="table-sm exammanagement_table_edit_tasks"><tr>');

                            $mform->addElement('html', '<th>'.get_string("nr", "mod_exammanagement").'</th>');

                            foreach( $tasks as $tasknumber => $taskmaxpoints){
                                $mform->addElement('html', '<th>'.$tasknumber.'</th>');
                            }

                            $mform->addElement('html', '</tr><tr>');

                            $mform->addElement('html', '<td class="exammanagement_vertical_align_middle"><strong>'.get_string("points", "mod_exammanagement").'</strong></td>');

                            foreach($tasks as $tasknumber => $taskmaxpoints){

                                $mform->addElement('html', '<td>');
                                $mform->addElement('text', 'points['.$participant->id.']['.$tasknumber.']', '');
                                $mform->setType('points['.$participant->id.']['.$tasknumber.']', PARAM_FLOAT);

                                if(isset($exampoints[$tasknumber-1])){
                                    $mform->setDefault('points['.$participant->id.']['.$tasknumber.']', $exampoints[$tasknumber-1]);
                                }
                                $mform->addElement('html', '</td>');
                            }

                            $mform->addElement('html', '</tr><tr>');

                            $mform->addElement('html', '<td class="p-0 text-center"><strong>'.get_string("max", "mod_exammanagement").'</strong></td>');

                            foreach($tasks as $tasknumber => $taskmaxpoints){

                                $mform->addElement('html', '<td class="p-0 text-center">');
                                $mform->addElement('html', $ExammanagementInstanceObj->formatNumberForDisplay($taskmaxpoints));
                                $mform->addElement('html', '</td>');
                            }

                            $mform->addElement('html', '</tr></table>');
                        } else {
                            $mform->addElement('html', '<a href="configureTasks.php?id='.$this->_customdata['id'].'" title="'.get_string("configure_tasks", "mod_exammanagement").'"><i class="fa fa-2x fa-info-circle text-warning"></i></a>');
                        }

                        $mform->addElement('html', '</td>');

                        $mform->addElement('html', '<td>');
                        $mform->addElement('text', 'bonuspoints['.$participant->id.']', '');
                        $mform->setType('bonuspoints['.$participant->id.']', PARAM_FLOAT);
                        $mform->addElement('html', '</td>');

                        if(isset($participant->bonuspoints)){
                            $mform->setDefault('bonuspoints['.$participant->id.']', $participant->bonuspoints);
                        } else {
                            $mform->setDefault('bonuspoints['.$participant->id.']', NULL);
                        }

                        $mform->addElement('html', '<td>');
                        $select = $mform->addElement('select', 'bonussteps['.$participant->id.']', '', array('-' => '-', '0' => 0, '1' => 1, '2' => 2, '3' => 3));
                        $mform->addElement('html', '</td>');

                        if(isset($participant->bonussteps)){
                            $select->setSelected($participant->bonussteps);
                        }

                        if($ExammanagementInstanceObj->getTaskCount()){
                            $mform->addElement('html', '<td>');
                            $select = $mform->addElement('select', 'state['.$participant->id.']', '', array('not_set' => '-', 'normal' => get_string('normal', 'mod_exammanagement'), 'nt' => get_string('nt', 'mod_exammanagement'), 'fa' => get_string('fa', 'mod_exammanagement'), 'ill' => get_string('ill', 'mod_exammanagement')));
                            $select->setSelected($state);
                            $mform->addElement('html', '</td>');
                        } else {
                            $mform->addElement('html', '-');
                        }
                    } else {
                        $mform->addElement('html', '<td>');
                        $mform->addElement('text', 'bonuspoints['.$participant->id.']', '');
                        $mform->setType('bonuspoints['.$participant->id.']', PARAM_FLOAT);
                        $mform->addElement('html', '</td>');
                    }
                }

                $mform->addElement('html', '</tr>');

                $i++;

            }
        } else {
            $mform->addElement('html', get_string("no_participants_added", "mod_exammanagement"));
        }

        $mform->addElement('html', '</tbody></table></div>');

        if(isset($this->_customdata['epm'])){
            $this->add_action_buttons(true, get_string("save_changes", "mod_exammanagement"));
        } else {
            $mform->addElement('html', '<div class="row"><span class="col-sm-5"></span><a href="'.$ExammanagementInstanceObj->getExammanagementUrl("view", $this->_customdata['id']).'" class="btn btn-primary">'.get_string("cancel", "mod_exammanagement").'</a></div>');
        }

    }

    //Custom validation should be added here
    function validation($data, $files) {
        $errors = array();
        return $errors;
    }
}
