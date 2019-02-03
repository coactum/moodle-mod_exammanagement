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
use mod_exammanagement\ldap\ldapManager;

use moodleform;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG, $PAGE;
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/User.php');
require_once(__DIR__.'/../ldap/ldapManager.php');

class participantsOverviewForm extends moodleform {

    //Add elements to form
    public function definition() {

        global $PAGE;

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
		$UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e'], $ExammanagementInstanceObj->moduleinstance->categoryid);

        $PAGE->requires->js_call_amd('mod_exammanagement/participants_overview', 'init'); //call jquery for tracking input value change events and creating input type number fields

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('html', '<div class="row"><h3 class="col-sm-10">'.get_string('show_results_str', 'mod_exammanagement').'</h3>');
        $mform->addElement('html', '<div class="col-sm-2"><a class="pull-right helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');
        $mform->addElement('html', '</div>');

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('participantsOverview'));

        $mform->addElement('html', '<div class="table-responsive">');
        $mform->addElement('html', '<table class="table table-striped exammanagement_table">');
        $mform->addElement('html', '<thead class="exammanagement_tableheader exammanagement_brand_backgroundcolor"><th scope="col">#</th><th scope="col">'.get_string("firstname", "mod_exammanagement").'</th><th scope="col">'.get_string("lastname", "mod_exammanagement").'</th><th scope="col">'.get_string("matriculation_number", "mod_exammanagement").'</th><th scope="col">'.get_string("room", "mod_exammanagement").'</th><th scope="col">'.get_string("place", "mod_exammanagement").'</th><th scope="col">'.get_string("points", "mod_exammanagement").'</th><th scope="col">'.get_string("totalpoints", "mod_exammanagement").'</th><th scope="col">'.get_string("result", "mod_exammanagement").'</th><th scope="col">'.get_string("bonussteps", "mod_exammanagement").'</th><th scope="col">'.get_string("resultwithbonus", "mod_exammanagement").'</th><th scope="col" class="exammanagement_table_whiteborder_left">'.get_string("edit", "mod_exammanagement").'</th></thead>');
        $mform->addElement('html', '<div class="tbody">');

        $participantsArr = $UserObj->getAllExamParticipants();

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

                $room = $participant->roomname;
                $place = $participant->place;

                $totalpoints = false;

                $gradingscale = $ExammanagementInstanceObj->getGradingscale();

                $state = $UserObj->getExamState($participant);

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

                if(isset($this->_customdata['edit']) && $this->_customdata['edit']==$matrnr){ // if user is editable
                    $mform->addElement('html', '<tr class="table-info">');
                    $mform->addElement('html', '<th scope="row">'.$i.'</th>');
                    $mform->addElement('html', '<td>'.$firstname.'</td>');
                    $mform->addElement('html', '<td>'.$lastname.'</td>');
                    $mform->addElement('html', '<td>'.$matrnr.'</td>');
                    
                    $attributes = array('size'=>'5');

                    $mform->addElement('html', '<td>');
                    $mform->addElement('text', 'room', '', $attributes);
                    $mform->setType('room', PARAM_TEXT);
                    $mform->setDefault('room', $room);

                    $mform->addElement('html', '</td><td>');

                    $mform->addElement('text', 'place', '', $attributes);
                    $mform->setType('place', PARAM_TEXT);
                    $mform->setDefault('place', $place);

                    $mform->addElement('html', '</td><td>');

                    if (isset($participant->exampoints)){

                        $mform->addElement('html', '<table class="table-sm"><tr>');

                        foreach($ExammanagementInstanceObj->getTasks() as $tasknumber => $taskmaxpoints){
                            $mform->addElement('html', '<th class="exammanagement_table_with">'.$tasknumber.'</th>');
                        }

                        $mform->addElement('html', '</tr><tr>');

                        foreach(json_decode($participant->exampoints) as $tasknumber => $points){
                            $mform->addElement('html', '<td>'.str_replace('.', ',',$points).'</td>');
                        }

                        $mform->addElement('html', '</tr></table>');

                    } else {

                        $mform->addElement('html', '<table class="table-sm"><tr>');

                        foreach($ExammanagementInstanceObj->getTasks() as $tasknumber => $taskmaxpoints){
                            $mform->addElement('html', '<th class="exammanagement_table_with">'.$tasknumber.'</th>');
                        }

                        $mform->addElement('html', '</tr><tr>');


                        foreach($ExammanagementInstanceObj->getTasks() as $tasknumber => $taskmaxpoints){
                            $mform->addElement('html', '<td> - </td>');
                        }

                        $mform->addElement('html', '</tr></table>');
                    }

                    $mform->addElement('html', '</td>');
                    
                    $mform->addElement('html', '<td><table class="table-sm"><tr><td>'. $totalpoints . '</td></tr><tr><td>');
                   
                    $select = $mform->addElement('select', 'state', '', array('normal' => get_string('normal', 'mod_exammanagement'), 'nt' => get_string('nt', 'mod_exammanagement'), 'fa' => get_string('fa', 'mod_exammanagement'), 'ill' => get_string('ill', 'mod_exammanagement')), $attributes); 
                    $select->setSelected('normal');
                    
                    $mform->addElement('html', '</td></tr></table>');

                    if($gradingscale){
                        $mform->addElement('html', '<td> <strong>...</strong> </td>');
                        if($UserObj->getEnteredBonusCount()){
                            if($participant->bonus){
                                $mform->addElement('html', '<td>');
                                $select = $mform->addElement('select', 'bonus', '', array('0' => 0, '1' => 1, '2' => 2, '3' => 3)); 
                                $select->setSelected('0');
                                $mform->addElement('html', '</td>');
                            } else {
                                $mform->addElement('html', '<td>');
                                $select = $mform->addElement('select', 'bonus', '', array('0' => 0, '1' => 1, '2' => 2, '3' => 3)); 
                                $select->setSelected('0');
                                $mform->addElement('html', '</td>');                            
                            }
                            $mform->addElement('html', '<td> <strong>...</strong> </td>');                    
                        }
                    } else {
                      $mform->addElement('html', '<td>-</td>');
                    }
                    
                    $mform->addElement('html', '<td class="exammanagement_brand_bordercolor_left"></td>');

                } else { // if user is non editable
                    $mform->addElement('html', '<tr>');
                    $mform->addElement('html', '<th scope="row">'.$i.'</th>');
                    $mform->addElement('html', '<td>'.$firstname.'</td>');
                    $mform->addElement('html', '<td>'.$lastname.'</td>');
                    $mform->addElement('html', '<td>'.$matrnr.'</td>');
                    $mform->addElement('html', '<td>'.$room.'</td>');
                    $mform->addElement('html', '<td>'.$place.'</td>');

                    $mform->addElement('html', '<td>');

                    if (isset($participant->exampoints)){

                        $mform->addElement('html', '<table class="table-sm"><tr>');

                        foreach($ExammanagementInstanceObj->getTasks() as $tasknumber => $taskmaxpoints){
                            $mform->addElement('html', '<th class="exammanagement_table_with">'.$tasknumber.'</th>');
                        }

                        $mform->addElement('html', '</tr><tr>');

                        foreach(json_decode($participant->exampoints) as $tasknumber => $points){
                            $mform->addElement('html', '<td>'.str_replace('.', ',',$points).'</td>');
                        }

                        $mform->addElement('html', '</tr></table>');

                    } else {

                        $mform->addElement('html', '<table class="table-sm"><tr>');

                        foreach($ExammanagementInstanceObj->getTasks() as $tasknumber => $taskmaxpoints){
                            $mform->addElement('html', '<th class="exammanagement_table_with">'.$tasknumber.'</th>');
                        }

                        $mform->addElement('html', '</tr><tr>');


                        foreach($ExammanagementInstanceObj->getTasks() as $tasknumber => $taskmaxpoints){
                            $mform->addElement('html', '<td> - </td>');
                        }

                        $mform->addElement('html', '</tr></table>');
                    }

                    $mform->addElement('html', '</td>');

                    $mform->addElement('html', '<td>'.$totalpoints.'</td>');

                    if($gradingscale){
                        $result = $UserObj->calculateResultGrade($participant);
                        $mform->addElement('html', '<td>'.str_replace('.', ',', $result).'</td>');
                        if($UserObj->getEnteredBonusCount()){
                            if($participant->bonus){
                                $mform->addElement('html', '<td>'.$participant->bonus.'</td>');
                            } else {
                                $mform->addElement('html', '<td>-</td>');                            
                            }
                            $mform->addElement('html', '<td>'.str_replace('.', ',', $UserObj->calculateResultGradeWithBonus($result, $participant->bonus)).'</td>');                    
                        }
                    } else {
                      $mform->addElement('html', '<td>-</td>');
                    }
    
                    $mform->addElement('html', '<td class="exammanagement_brand_bordercolor_left"><a href="participantsOverview.php?id='.$this->_customdata['id'].'&edit='.$matrnr.'" title="'.get_string("edit_user", "mod_exammanagement").'" class="m-b-1"><i class="fa fa-lg fa-pencil-square-o" aria-hidden="true"></i></a></td>');
                    
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

        if(isset($this->_customdata['edit'])){
            $this->add_action_buttons(true, get_string("save_changes", "mod_exammanagement"));
        } else {
            $mform->addElement('html', '<div class="row"><span class="col-sm-5"></span><a href="'.$ExammanagementInstanceObj->getExammanagementUrl("view", $this->_customdata['id']).'" class="btn btn-primary">'.get_string("cancel", "mod_exammanagement").'</a></div>');
        }

    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
