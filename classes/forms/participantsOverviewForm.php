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
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\User;

use moodleform;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG, $PAGE;
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/User.php');

class participantsOverviewForm extends moodleform {

    //Add elements to form
    public function definition() {

        global $PAGE;

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
		$UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $PAGE->requires->js_call_amd('mod_exammanagement/participants_overview', 'init'); //call jquery for tracking input value change events and creating input type number fields

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('html', '<div class="row"><h3 class="col-sm-10">'.get_string('participantsOverview', 'mod_exammanagement').'</h3>');
        $mform->addElement('html', '<div class="col-sm-2"><a class="pull-right helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;" title="'.get_string("helptext_open", "mod_exammanagement").'"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');
        $mform->addElement('html', '</div>');

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('participantsOverview'));

        $mform->addElement('html', '<p>'.get_string("participants_overview_text", "mod_exammanagement").'</p>');
        
        $mform->addElement('html', '<div class="table-responsive">');
        $mform->addElement('html', '<table class="table table-striped exammanagement_table" id="0">');
        $mform->addElement('html', '<thead class="exammanagement_tableheader exammanagement_brand_backgroundcolor"><th scope="col">#</th><th scope="col">'.get_string("firstname", "mod_exammanagement").'</th><th scope="col">'.get_string("lastname", "mod_exammanagement").'</th><th scope="col">'.get_string("matriculation_number", "mod_exammanagement").'</th><th scope="col" class="exammanagement_table_width_room">'.get_string("room", "mod_exammanagement").'</th><th scope="col" class="exammanagement_table_width_place">'.get_string("place", "mod_exammanagement").'</th><th scope="col">'.get_string("points", "mod_exammanagement").'</th><th scope="col">'.get_string("totalpoints", "mod_exammanagement").'</th><th scope="col">'.get_string("result", "mod_exammanagement").'</th><th scope="col">'.get_string("bonussteps", "mod_exammanagement").'</th><th scope="col">'.get_string("resultwithbonus", "mod_exammanagement").'</th><th scope="col" class="exammanagement_table_whiteborder_left">'.get_string("edit", "mod_exammanagement").'</th></thead>');
        $mform->addElement('html', '<tbody>');

        $participantsArr = $UserObj->getAllExamParticipants();
        $examrooms = json_decode($ExammanagementInstanceObj->getModuleinstance()->rooms);
        $gradingscale = $ExammanagementInstanceObj->getGradingscale();

        usort($participantsArr, function($a, $b){ //sort array by custom user function
            global $UserObj;

            if($a->moodleuserid){
            $aFirstname = $UserObj->getMoodleUser($a->moodleuserid)->firstname;
            $aLastname = $UserObj->getMoodleUser($a->moodleuserid)->lastname;  
            } else {
            $aFirstname = $a->firstname;
            $aLastname = $a->lastname;
            }

            if($b->moodleuserid){
            $bFirstname = $UserObj->getMoodleUser($b->moodleuserid)->firstname;
            $bLastname = $UserObj->getMoodleUser($b->moodleuserid)->lastname;
            } else {
            $bFirstname = $b->firstname;
            $bLastname = $b->lastname;
            }

            if ($aLastname == $bLastname) { //if names are even sort by first name
                return strcmp($aFirstname, $bFirstname);
            } else{
                return strcmp($aLastname, $bLastname); // else sort by last name
            }

        });

        if($participantsArr){

            $i = 1;

            foreach($participantsArr as $key => $participant){

                if($participant->moodleuserid){
                    $moodleUserObj = $UserObj->getMoodleUser($participant->moodleuserid);
                    $lastname = $moodleUserObj->lastname;
                    $firstname = $moodleUserObj->firstname;
                } else if($participant->imtlogin){
                    $lastname = $participant->lastname;
                    $firstname = $participant->firstname;
                }

                $matrnr = $UserObj->getUserMatrNr($participant->moodleuserid, $participant->imtlogin);

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
                $exampoints = array_values((array) json_decode($participant->exampoints));
                $tasks = $ExammanagementInstanceObj->getTasks();

                if($state == 'nt'){
                    $totalpoints = get_string("nt", "mod_exammanagement");
                } else if ($state == 'fa'){
                    $totalpoints = get_string("fa", "mod_exammanagement");
                } else if ($state == 'ill'){
                    $totalpoints = get_string("ill", "mod_exammanagement");
                }

                if (!$totalpoints){
                    $totalpoints = str_replace('.', ',', $UserObj->calculateTotalPoints($participant));
                }

                if((isset($this->_customdata['edit']) && $this->_customdata['edit'] != 0 && ($this->_customdata['edit']==$matrnr)) || (isset($this->_customdata['editline']) && $this->_customdata['editline'] != 0 && $this->_customdata['editline']==$i)){ // if user is editable

                    if(isset($this->_customdata['edit'])){                    
                        $mform->addElement('hidden', 'edit', $this->_customdata['edit']);
                        $mform->setType('edit', PARAM_INT);
                    }
                    if(isset($this->_customdata['editline'])){
                        $mform->addElement('hidden', 'editline', $this->_customdata['editline']);
                        $mform->setType('editline', PARAM_INT);    
                    }
                    
                    $mform->addElement('hidden', 'editmoodleuserid', $participant->moodleuserid);
                    $mform->setType('editmoodleuserid', PARAM_INT);

                    $mform->addElement('hidden', 'pne', true);
                    $mform->setType('pne', PARAM_INT);

                    $mform->addElement('html', '<tr class="table-info">');
                    $mform->addElement('html', '<th scope="row" id="'.$i.'">'.$i.'</th>');
                    $mform->addElement('html', '<td>'.$firstname.'</td>');
                    $mform->addElement('html', '<td>'.$lastname.'</td>');
                    $mform->addElement('html', '<td>'.$matrnr.'</td>');
                    
                    $attributes = array('size'=>'3');

                    if($examrooms){

                        $mform->addElement('html', '<td>');

                        $roomOptionsArr = array();

                        $roomPlacesPatternsArr = array();

                        foreach($examrooms as $id => $roomid){

                            $roomObj = $ExammanagementInstanceObj->getRoomObj($roomid);

                            if($roomObj){
                                $roomOptionsArr[$roomid] = $roomObj->name;

                                $decodedPlaces = json_decode($roomObj->places);
                                $roomPlacesPatternsArr[$roomid] = array_shift($decodedPlaces) . ', ' . array_shift($decodedPlaces) . ', ..., ' . array_pop($decodedPlaces);
                            }
                        }                
    
                        if(!empty($roomOptionsArr)){
                            $select = $mform->addElement('select', 'room', '', $roomOptionsArr, $attributes); 
                            $select->setSelected($participant->roomid);    
                        } else {
                            $mform->addElement('html', $participant->roomname.' ('.get_string('deleted_room', 'mod_exammanagement').')');
                        }
                         
                        $mform->addElement('html', '</td><td>');
    
                        if(!empty($roomOptionsArr)){
                            $mform->addElement('text', 'place', '', $attributes);
                            $mform->setType('place', PARAM_TEXT);
                            $mform->setDefault('place', $place);

                            $mform->addElement('html', '<span class="exammanagement_position_existing_places_column">'.get_string('available', 'mod_exammanagement').': <br>');
                            if ($roomPlacesPatternsArr){
                                foreach($roomPlacesPatternsArr as $roomid => $placesPattern){
                                    if($roomid == $participant->roomid){
                                        $mform->addElement('html', '<span id="'.$roomid.'" class="hideablepattern" >'.$placesPattern.'</span>');
                                    } else {
                                        $mform->addElement('html', '<span id="'.$roomid.'" class="hideablepattern hiddenpattern hidden">'.$placesPattern.'</span>');
                                    }
                                }
                            }
                            $mform->addElement('html', '</span>');
    
                        } else {
                            $mform->addElement('html', $place);

                            $mform->addElement('html', '<span class="exammanagement_position_existing_places_column">'.get_string('available', 'mod_exammanagement').': <br>');
                            if ($roomPlacesPatternsArr){
                                foreach($roomPlacesPatternsArr as $roomid => $placesPattern){
                                    if($roomid == $participant->roomid){
                                        $mform->addElement('html', '<span id="'.$roomid.'" class="hideablepattern" >'.$placesPattern.'</span>');
                                    } else {
                                        $mform->addElement('html', '<span id="'.$roomid.'" class="hideablepattern hiddenpattern hidden">'.$placesPattern.'</span>');
                                    }
                                }
                            }
                            $mform->addElement('html', '</span>');

                        }

    
                        $mform->addElement('html', '</td><td>');
                    } else {
                        $mform->addElement('html', '<td>-</td>');
                        $mform->addElement('html', '<td>-</td><td>');
                    }

                
                    if($ExammanagementInstanceObj->getTaskCount()){

                            $mform->addElement('html', '<table class="table-sm"><tr>');

                            foreach( $tasks as $tasknumber => $taskmaxpoints){
                                $mform->addElement('html', '<th class="exammanagement_table_width_points">'.$tasknumber.'</th>');
                            }

                            $mform->addElement('html', '</tr><tr>');

                            foreach($tasks as $tasknumber => $taskmaxpoints){
                                    
                                $mform->addElement('html', '<td>');
                                $mform->addElement('text', 'points['.$tasknumber.']', '', $attributes);
                                $mform->setType('points['.$tasknumber.']', PARAM_FLOAT);

                                if(isset($exampoints[$tasknumber-1])){
                                    $mform->setDefault('points['.$tasknumber.']', $exampoints[$tasknumber-1]);
                                }
                                $mform->addElement('html', '</td>');
                            }

                            $mform->addElement('html', '</tr></table>');
                    } else {
                        $mform->addElement('html', '<a href="configureTasks.php?id='.$this->_customdata['id'].'" title="'.get_string("configure_tasks", "mod_exammanagement").'"><i class="fa fa-2x fa-info-circle text-warning"></i></a>');
                    }
                    
                    $mform->addElement('html', '</td>');
                    
                    $mform->addElement('html', '<td><table class="table-sm"><tr><td><span id="totalpoints">'. $totalpoints . '</span></td></tr><tr><td>');
                   
                    if($ExammanagementInstanceObj->getTaskCount()){
                        $select = $mform->addElement('select', 'state', '', array('normal' => get_string('normal', 'mod_exammanagement'), 'nt' => get_string('nt', 'mod_exammanagement'), 'fa' => get_string('fa', 'mod_exammanagement'), 'ill' => get_string('ill', 'mod_exammanagement')), $attributes); 
                        $select->setSelected($state);
                    }
                    
                    $mform->addElement('html', '</td></tr></table>');

                    if($gradingscale){
                        $mform->addElement('html', '<td> <strong><i class="fa fa-2x fa-spinner fa-pulse fa-fw"></i><span class="sr-only">{{#str}}state_loading, mod_exammanagement{{/str}}</span></strong> </td>');
                    } else {
                      $mform->addElement('html', '<td><a href="configureGradingscale.php?id='.$this->_customdata['id'].'" title="'.get_string("configure_tasks", "mod_exammanagement").'"><i class="fa fa-2x fa-info-circle text-warning"></i></a></td>');
                    }

                    if($UserObj->getEnteredBonusCount()){
                        if(isset($participant->bonus)){
                            $mform->addElement('html', '<td>');
                            $select = $mform->addElement('select', 'bonus', '', array('-' => '-', '0' => 0, '1' => 1, '2' => 2, '3' => 3)); 
                            $select->setSelected($participant->bonus);
                            $mform->addElement('html', '</td>');
                        } else {
                            $mform->addElement('html', '<td>');
                            $select = $mform->addElement('select', 'bonus', '', array('-' => '-', '0' => 0, '1' => 1, '2' => 2, '3' => 3)); 
                            $select->setSelected('-');
                            $mform->addElement('html', '</td>');                            
                        }
                    } else {
                        $mform->addElement('html', '<td>');
                        $select = $mform->addElement('select', 'bonus', '', array('-' => '-', '0' => 0, '1' => 1, '2' => 2, '3' => 3)); 
                        $select->setSelected('-');
                        $mform->addElement('html', '</td>');                            
                    }

                    if($gradingscale){
                        $mform->addElement('html', '<td> <strong><i class="fa fa-2x fa-spinner fa-pulse fa-fw"></i><span class="sr-only">{{#str}}state_loading, mod_exammanagement{{/str}}</span></strong> </td>');                    
                    } else {
                        $mform->addElement('html', '<td><a href="configureGradingscale.php?id='.$this->_customdata['id'].'" title="'.get_string("configure_gradingscale", "mod_exammanagement").'"><i class="fa fa-2x fa-info-circle text-warning"></i></a></td>');
                    }
                    
                    $mform->addElement('html', '<td class="exammanagement_brand_bordercolor_left"></td>');

                } else { // if user is non editable
                    $mform->addElement('html', '<tr>');
                    $mform->addElement('html', '<th scope="row" id="'.$i.'">'.$i.'</th>');
                    $mform->addElement('html', '<td>'.$firstname.'</td>');
                    $mform->addElement('html', '<td>'.$lastname.'</td>');
                    $mform->addElement('html', '<td>'.$matrnr.'</td>');
                    $mform->addElement('html', '<td>'.$room.'</td>');
                    $mform->addElement('html', '<td>'.$place.'</td>');

                    $mform->addElement('html', '<td>');

                    if($ExammanagementInstanceObj->getTaskCount()){
                        $mform->addElement('html', '<table class="table-sm"><tr>');

                        foreach($tasks as $tasknumber => $taskmaxpoints){
                            $mform->addElement('html', '<th class="exammanagement_table_with">'.$tasknumber.'</th>');
                        }
                        
                        $mform->addElement('html', '</tr><tr>');
    
                        foreach($tasks as $tasknumber => $taskmaxpoints){
                            if(isset($exampoints[$tasknumber-1])){
                                $mform->addElement('html', '<td>'.str_replace('.', ',',$exampoints[$tasknumber-1]).'</td>');
                            } else {
                                $mform->addElement('html', '<td> - </td>');
                            }
                        }
    
                        $mform->addElement('html', '</tr></table>');
                    } else {
                        $mform->addElement('html', '<a href="configureTasks.php?id='.$this->_customdata['id'].'" title="'.get_string("configure_tasks", "mod_exammanagement").'"><i class="fa fa-2x fa-info-circle text-warning"></i></a>');
                    }

                    $mform->addElement('html', '</td>');

                    $mform->addElement('html', '<td>'.$totalpoints.'</td>');

                    if($gradingscale){
                        $result = $UserObj->calculateResultGrade($participant);
                        $mform->addElement('html', '<td>'.str_replace('.', ',', $result).'</td>');
                    } else {
                      $mform->addElement('html', '<td><a href="configureGradingscale.php?id='.$this->_customdata['id'].'" title="'.get_string("configure_gradingscale", "mod_exammanagement").'"><i class="fa fa-2x fa-info-circle text-warning"></i></a></td>');
                    }

                    if($UserObj->getEnteredBonusCount()){
                        if(isset($participant->bonus)){
                            $mform->addElement('html', '<td>'.$participant->bonus);
                            
                            switch ($participant->bonus){

                                case 0:
                                    break;
                                case 1:
                                    $mform->addElement('html', ' (= 0,3)');
                                    break;
                                case 2:
                                    $mform->addElement('html', ' (= 0,7)');
                                    break;
                                case 3:
                                    $mform->addElement('html', ' (= 1,0)');
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
                        $mform->addElement('html', '<td>'.str_replace('.', ',', $UserObj->calculateResultGradeWithBonus($result, $state, $participant->bonus)).'</td>');                    
                    } else {
                        $mform->addElement('html', '<td><a href="configureGradingscale.php?id='.$this->_customdata['id'].'" title="'.get_string("configure_gradingscale", "mod_exammanagement").'"><i class="fa fa-2x fa-info-circle text-warning"></i></a></td>');
                    }

                    $anchorid = $i-1;
                    
                    if($matrnr !== '-'){
                        $mform->addElement('html', '<td class="exammanagement_brand_bordercolor_left"><a href="participantsOverview.php?id='.$this->_customdata['id'].'&edit='.$matrnr.'#'.$anchorid.'" title="'.get_string("edit_user", "mod_exammanagement").'" class="m-b-1"><i class="fa fa-2x fa-lg fa-pencil-square-o" aria-hidden="true"></i></a></td>');
                    } else {
                        $mform->addElement('html', '<td class="exammanagement_brand_bordercolor_left"><a href="participantsOverview.php?id='.$this->_customdata['id'].'&editline='.$i.'#'.$anchorid.'" title="'.get_string("edit_user", "mod_exammanagement").'" class="m-b-1"><i class="fa fa-2x fa-lg fa-pencil-square-o" aria-hidden="true"></i></a></td>');
                    }
                    
                }
                $mform->addElement('html', '</tr>');                
                
                $i++;

            }
        } else {
            $mform->addElement('html', get_string("no_participants_added", "mod_exammanagement"));
        }

        $mform->addElement('html', '</tbody></table></div>');

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        if((isset($this->_customdata['edit']) && $this->_customdata['edit'] != 0)|| (isset($this->_customdata['editline']) && $this->_customdata['editline'] != 0)){
            $this->add_action_buttons(true, get_string("save_changes", "mod_exammanagement"));
        } else {
            $mform->addElement('html', '<div class="row"><span class="col-sm-5"></span><a href="'.$ExammanagementInstanceObj->getExammanagementUrl("view", $this->_customdata['id']).'" class="btn btn-primary">'.get_string("cancel", "mod_exammanagement").'</a></div>');
        }

    }

    //Custom validation should be added here
    function validation($data, $files) {
        $errors = array();

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $examTasks = $ExammanagementInstanceObj->getTasks();

        if($examTasks){
            $savedTasksArr = array_values($examTasks);
        } else {
            $savedTasksArr = false;
        }

        if(isset($data['place']) && $data['place']==''){
            $errors['place'] = get_string('err_filloutfield', 'mod_exammanagement');
        }

        if(isset($data['state']) && $data['state'] != 'nt' && $data['state'] != 'fa' && $data['state'] != 'ill'){
            foreach($data['points'] as $task => $points){

                $floatval = floatval($points);
                $isnumeric = is_numeric($points);

                if(($points && !$floatval) || !$isnumeric){
                    $errors['points['. $task .']'] = get_string('err_novalidinteger', 'mod_exammanagement');
                } else if($points<0) {
                    $errors['points['.$task.']'] = get_string('err_underzero', 'mod_exammanagement');
                } else if($examTasks && $points > $savedTasksArr[$task-1]){
                     $errors['points['. $task .']'] = get_string('err_taskmaxpoints', 'mod_exammanagement');
                }
            }
        }

        return $errors;
    }
}
