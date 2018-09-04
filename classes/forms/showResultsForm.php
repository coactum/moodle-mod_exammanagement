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
use mod_exammanagement\ldap\ldapManager;

use moodleform;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../ldap/ldapManager.php');

class showResultsForm extends moodleform {

    //Add elements to form
    public function definition() {

        global $PAGE;

        $mform = $this->_form; // Don't forget the underscore!

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $LdapManagerObj = ldapManager::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $mform->addElement('html', '<div class="row"><h3 class="col-sm-10">'.get_string('show_results_str', 'mod_exammanagement').'</h3>');
        $mform->addElement('html', '<div class="col-sm-2"><a class="pull-right" type="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');
        $mform->addElement('html', '</div>');

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('showResults'));

        $mform->addElement('html', '<div class="row"><div class="col-sm-2"><h4>'.get_string("firstname", "mod_exammanagement").'</h4></div><div class="col-sm-2"><h4>'.get_string("lastname", "mod_exammanagement").'</h4></div><div class="col-sm-2"><h4 class="hidden-md-down">'.get_string("matriculation_number", "mod_exammanagement").'</h4><h4 class="hidden-lg-up">'.get_string("matriculation_number_short", "mod_exammanagement").'</h4></div><div class="col-sm-1"><h4>'.get_string("room", "mod_exammanagement").'</h4></div><div class="col-sm-2"><h4>'.get_string("place", "mod_exammanagement").'</h4></div><div class="col-sm-2"><h4>'.get_string("points", "mod_exammanagement").'</h4></div><div class="col-sm-1"><h4>'.get_string("result", "mod_exammanagement").'</h4></div></div>');

        $results = $ExammanagementInstanceObj->getResults();

        foreach($results as $key => $resultObj){

            $moodleUser = $ExammanagementInstanceObj->getMoodleUser($resultObj->uid);

            if($LdapManagerObj->is_LDAP_config()){
        				$ldapConnection = $LdapManagerObj->connect_ldap();
                $matrnr = $LdapManagerObj->uid2studentid($ldapConnection, $resultObj->uid);
        		} else {
        			  $matrnr = $LdapManagerObj->getIMTLogin2MatriculationNumberTest($resultObj->uid);
        		}

            $assignedPlaces = $ExammanagementInstanceObj->getAssignedPlaces();
            $room;
            $place;

            foreach($assignedPlaces as $roomObj){

                foreach($roomObj->assignments as $assignment){

                    if($assignment->userid == $resultObj->uid){
                        $room = $roomObj->roomname;
                        $place = $assignment->place;
                    }
                }
            }

            $points = 0;
            $state;

            foreach($resultObj as $stateArr){
                if($stateArr->nt == 1){
                    $points = get_string("nt", "mod_exammanagement");
                    $state = 'nt';
                } else if ($stateArr->fa == 1){
                    $points = get_string("fa", "mod_exammanagement");
                    $state = 'fa';
                } else if ($stateArr->ill == 1){
                    $points = get_string("ill", "mod_exammanagement");
                    $state = 'ill';
                }
            }

            if(!$points){
                foreach($resultObj->points as $key => $taskpoints){
                    $points += $taskpoints;
                }
            }

            $gradingscale = $ExammanagementInstanceObj->getGradingscale();

            $result = false;

            if($state == "nt" || $state == "fa" || $state == "ill"){
                $result = 5;
            }

            if($points && $gradingscale){

                $lastpoints;

                foreach($gradingscale as $key => $step){

                  if($key == '1.0' && $points == $step){
                      $result = $key;
                  } else if($points < $lastpoints && $points >= $step){
                    $result = $key;
                  } else if($key == '4.0' && $points <= $step){
                    $result = 5;
                  }

                  $lastpoints = $step;

                }
            }



            $mform->addElement('html', '<div class="row m-b-1"><div class="col-md-2">'.$moodleUser->firstname.'</div>');
            $mform->addElement('html', '<div class="col-sm-2">'.$moodleUser->lastname.'</div>');
            $mform->addElement('html', '<div class="col-sm-2">'.$matrnr.'</div>');
            $mform->addElement('html', '<div class="col-sm-1">'.$room.'</div>');
            $mform->addElement('html', '<div class="col-sm-2">'.$place.'</div>');
            $mform->addElement('html', '<div class="col-sm-2">'.$points.'<a href="inputResults.php?id='.$this->_customdata['id'].'&matrnr='.$matrnr.'"><i class="fa fa-pencil-square-o pull-right" aria-hidden="true"></i></a></div>');
            if($gradingscale){
                $mform->addElement('html', '<div class="col-sm-1"><span class="pull-right">'.$result.'</span></div></div>');
            } else {
              $mform->addElement('html', '<div class="col-sm-1">-<span class="pull-right"><a href="configureGradingscale.php?id='.$this->_customdata['id'].'" title="'.get_string("gradingscale_not_set", "mod_exammanagement").'"><i class="fa fa-info-circle text-warning"></i></a></span></div></div>');
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
