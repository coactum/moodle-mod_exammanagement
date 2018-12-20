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
 * class containing showResultsForm for exammanagement
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

class showResultsForm extends moodleform {

    //Add elements to form
    public function definition() {

        $mform = $this->_form; // Don't forget the underscore!

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
		$UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e'], $ExammanagementInstanceObj->moduleinstance->categoryid);

        $mform->addElement('html', '<div class="row"><h3 class="col-sm-10">'.get_string('show_results_str', 'mod_exammanagement').'</h3>');
        $mform->addElement('html', '<div class="col-sm-2"><a class="pull-right helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');
        $mform->addElement('html', '</div>');

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('showResults'));

        $mform->addElement('html', '<div class="row"><div class="col-sm-2"><h4>'.get_string("firstname", "mod_exammanagement").'</h4></div><div class="col-sm-2"><h4>'.get_string("lastname", "mod_exammanagement").'</h4></div><div class="col-sm-2"><h4 class="d-none d-lg-block">'.get_string("matriculation_number", "mod_exammanagement").'</h4><h4 class="d-lg-none">'.get_string("matriculation_number_short", "mod_exammanagement").'</h4></div><div class="col-sm-1"><h4>'.get_string("room", "mod_exammanagement").'</h4></div><div class="col-sm-2"><h4>'.get_string("place", "mod_exammanagement").'</h4></div><div class="col-sm-1"><h4>'.get_string("points", "mod_exammanagement").'</h4></div><div class="col-sm-1"><h4>'.get_string("result", "mod_exammanagement").'</h4></div><div class="col-sm-1"><h4>'.get_string("resultwithbonus", "mod_exammanagement").'</h4></div></div>');

        $participantsWithResultArr = $UserObj->getAllParticipantsWithResults();

        if($participantsWithResultArr){
            foreach($participantsWithResultArr as $key => $participant){

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

                $mform->addElement('html', '<div class="row m-b-1"><div class="col-md-2">'.$firstname.'</div>');
                $mform->addElement('html', '<div class="col-sm-2">'.$lastname.'</div>');
                $mform->addElement('html', '<div class="col-sm-2">'.$matrnr.'</div>');
                $mform->addElement('html', '<div class="col-sm-1">'.$room.'</div>');
                $mform->addElement('html', '<div class="col-sm-2">'.$place.'</div>');
                $mform->addElement('html', '<div class="col-sm-1">'.$totalpoints.'<a href="inputResults.php?id='.$this->_customdata['id'].'&matrnr='.$matrnr.'"><i class="fa fa-pencil-square-o pull-right" aria-hidden="true"></i></a></div>');
                if($gradingscale){
                    $result = $UserObj->calculateResultGrade($participant);
                    $mform->addElement('html', '<div class="col-sm-1"><span class="pull-right">'.str_replace('.', ',', $result).'</span></div>');
                    if($UserObj->getEnteredBonusCount()){
                        $mform->addElement('html', '<div class="col-sm-1">'.str_replace('.', ',', $UserObj->calculateResultGradeWithBonus($result, $participant->bonus)));
                        if($participant->bonus){
                            $mform->addElement('html', '<span title="'.get_string("bonussteps", "mod_exammanagement").'"> ('.$participant->bonus.')</span> <a href="importBonus.php?id='.$this->_customdata['id'].'" title="'.get_string("change_bonus", "mod_exammanagement").'"><i class="fa fa-pencil-square-o pull-right" aria-hidden="true"></i></a>');
                        }
                        $mform->addElement('html', '</div>');
                    }
                } else {
                  $mform->addElement('html', '<div class="col-sm-1">-<span class="pull-right"><a href="configureGradingscale.php?id='.$this->_customdata['id'].'" title="'.get_string("gradingscale_not_set", "mod_exammanagement").'"><i class="fa fa-info-circle text-warning"></i></a></span></div>');
                }

                $mform->addElement('html', '</div>');
            }
        }

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
