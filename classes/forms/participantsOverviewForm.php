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
global $CFG;
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/User.php');
require_once(__DIR__.'/../ldap/ldapManager.php');

class participantsOverviewForm extends moodleform {

    //Add elements to form
    public function definition() {

        $mform = $this->_form; // Don't forget the underscore!

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
		$UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e'], $ExammanagementInstanceObj->moduleinstance->categoryid);

        $mform->addElement('html', '<div class="row"><h3 class="col-sm-10">'.get_string('show_results_str', 'mod_exammanagement').'</h3>');
        $mform->addElement('html', '<div class="col-sm-2"><a class="pull-right helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');
        $mform->addElement('html', '</div>');

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('participantsOverview'));

        $mform->addElement('html', '<div class="table-responsive table-striped table-hover">');
        $mform->addElement('html', '<table class="table">');
        $mform->addElement('html', '<thead class="thead-dark"><th scope="col">#</th><th scope="col">'.get_string("firstname", "mod_exammanagement").'</th><th scope="col">'.get_string("lastname", "mod_exammanagement").'</th><th scope="col">'.get_string("matriculation_number", "mod_exammanagement").'</th><th scope="col">'.get_string("room", "mod_exammanagement").'</th><th scope="col">'.get_string("place", "mod_exammanagement").'</th><th scope="col">'.get_string("points", "mod_exammanagement").'</th><th scope="col">'.get_string("result", "mod_exammanagement").'</th><th scope="col">'.get_string("bonussteps", "mod_exammanagement").'</th><th scope="col">'.get_string("resultwithbonus", "mod_exammanagement").'</th><th scope="col" class="exammanagement_tableheader_bordercolor">'.get_string("edititing_possibilities", "mod_exammanagement").'<br>'.get_string("edititing_possibilities_examples", "mod_exammanagement").'</th></thead>');
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

                $mform->addElement('html', '<tr>');
                $mform->addElement('html', '<th scope="row">'.$i.'</th>');
                $mform->addElement('html', '<td>'.$firstname.'</td>');
                $mform->addElement('html', '<td>'.$lastname.'</td>');
                $mform->addElement('html', '<td>'.$matrnr.'</td>');
                $mform->addElement('html', '<td>'.$room.'</td>');
                $mform->addElement('html', '<td>'.$place.'</td>');
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

                $mform->addElement('html', '<td class="exammanagement_tablecell_bordercolor"><a href="participantsOverview.php?id='.$this->_customdata['id'].'&edit='.$matrnr.'" title="'.get_string("edit_user", "mod_exammanagement").'" class="m-b-1"><i class="fa fa-lg fa-pencil-square-o" aria-hidden="true"></i></a><a href="inputResults.php?id='.$this->_customdata['id'].'&matrnr='.$matrnr.'" title="'.get_string("edit_exampoints", "mod_exammanagement").'" class="m-b-1 m-l-1"><i class="fa fa-lg fa-pencil-square-o" aria-hidden="true"></i></a><a href="configureGradingscale.php?id='.$this->_customdata['id'].'" title="'.get_string("edit_gradingscale", "mod_exammanagement").'" class="m-l-1"><i class="fa fa-lg fa-edit"></i></a></td>');
                
                $mform->addElement('html', '</tr>');
                
                $i++;

            }
        } else {
            $mform->addElement('html', get_string("no_participants_added", "mod_exammanagement"));
        }

        $mform->addElement('html', '</tbody></table></div>');

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        //$this->add_action_buttons(true, get_string("save_and_next", "mod_exammanagement"));
        $mform->addElement('html', '<div class="row"><span class="col-sm-5"></span><a href="'.$ExammanagementInstanceObj->getExammanagementUrl("view", $this->_customdata['id']).'" class="btn btn-primary">'.get_string("cancel", "mod_exammanagement").'</a></div>');

    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
