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
 * class containing addParticipantsForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;

use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\User;
use mod_exammanagement\ldap\ldapManager;
use mod_exammanagement\general\Moodle;
use mod_exammanagement\general\MoodleDB;
use moodleform;
use stdclass;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/User.php');
require_once(__DIR__.'/../ldap/ldapManager.php');
require_once(__DIR__.'/../general/Moodle.php');
require_once(__DIR__.'/../general/MoodleDB.php');

class addParticipantsForm extends moodleform{

    //Add elements to form
    public function definition(){
        global $PAGE, $CFG;

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $LdapManagerObj = ldapManager::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e'], $ExammanagementInstanceObj->moduleinstance->categoryid);
        $MoodleObj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $MoodleDBObj = MoodleDB::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'remove_form_classes_col'); //call removing moodle form classes col-md for better layout
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'enable_cb'); //call jquery for checking all checkboxes via following checkbox

        $mform = $this->_form; // Don't forget the underscore!

        $tempParticipants = $MoodleDBObj->getRecordsFromDB('exammanagement_temp_part', array('plugininstanceid' => $this->_customdata['id']));

        $mform->addElement('html', '<div class="row"><div class="col-xs-6">');

        if($tempParticipants){
            $mform->addElement('html', '<h3>'.get_string("import_participants", "mod_exammanagement").'</h3>');
        } else {
            $mform->addElement('html', '<h3>'.get_string("add_participants_from_file", "mod_exammanagement").'</h3>');
        }
        $mform->addElement('html', '</div><div class="col-xs-2"><a class="helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');

        if($tempParticipants){
            $mform->addElement('html', '<div class="col-xs-4"><a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/addParticipants.php', $this->_customdata['id'], 'dtp', true).'" role="button" class="btn btn-primary pull-right" title="'.get_string("import_new_participants", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("import_new_participants", "mod_exammanagement").'</span><i class="fa fa-plus d-lg-none" aria-hidden="true"></i></a></div>');
        }

        $mform->addElement('html', '</div></div>');

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('addParticipants'));

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        if($tempParticipants){

            $newMoodleParticipantsArr = array();
            $newNoneMoodleParticipantsArr = array();
            $badMatriculationnumbersArr = array();
            $oddMatriculationnumbersArr = array();
            $existingMatriculationnumbersArr = array();
            $deletedMatriculationnumbersArr = array();

            $tempIDsArray = array();

            ###### classify temp participants
            
            foreach($tempParticipants as $key => $participant){
                var_dump($participant);

                // filter invalid/bad matrnr
                if (!$UserObj->checkIfValidMatrNr($participant->identifier)){
                    $tempUserObj = new stdclass;
                    $tempUserObj->line = $participant->line;
                    $tempUserObj->matrnr = $participant->identifier;
                    $tempUserObj->state = 'badmatrnr';
                    
                    array_push($badMatriculationnumbersArr, $tempUserObj);
                    unset($tempParticipants[$key]);
                }

                // convert matriculation numbers to moodle userdis using LDAP and save them in moodleuseridsarray
                
                if($LdapManagerObj->is_LDAP_config()){
                    $ldapConnection = $LdapManagerObj->connect_ldap();

                    $username = $LdapManagerObj->studentid2uid($ldapConnection, $participant->identifier);

                    if($username){
                        $moodleuserid = $MoodleDBObj->getFieldFromDB('user','id', array('username' => $username));
                    }

                } else {
                        $moodleuserid = $LdapManagerObj->getMatriculationNumber2ImtLoginTest($participant->identifier);
                }

                if ($moodleuserid){
                    $tempUserObj = new stdclass;
                    $tempUserObj->line = $participant->line;
                    $tempUserObj->moodleid = $moodleuserid;
                    $tempUserObj->matrnr = $participant->identifier;

                    if($UserObj->checkIfAlreadyParticipant($moodleuserid)){ // if participant is already saved for instance
                        array_push($existingMatriculationnumbersArr, $tempUserObj);
                        unset($tempParticipants[$key]);
                        array_push($tempIDsArray, $moodleuserid); //for finding already temp users
                    } else if(in_array($moodleuserid, $tempIDsArray)){ // if participant is already known as temp participant
                        $tempUserObj->state = 'doubled';
                        array_push($badMatriculationnumbersArr, $tempUserObj);
                        unset($tempParticipants[$key]);
                    } else if (!in_array($moodleuserid, $UserObj->getCourseParticipantsIDs())){ // if participant is not in course
                        $tempUserObj->state = 'nocourse';
                        array_push($oddMatriculationnumbersArr, $tempUserObj);
                        unset($tempParticipants[$key]);
                        array_push($tempIDsArray, $moodleuserid); //for finding already temp users
                    } else {	// if participant is valid participant
                        array_push($newMoodleParticipantsArr, $tempUserObj);
                        unset($tempParticipants[$key]);
                        array_push($tempIDsArray, $moodleuserid); //for finding already temp users
                    }
                } else {	// if participant is no moodle user
                    $tempUserObj = new stdclass;
                    $tempUserObj->line = $participant->line;
                    $tempUserObj->moodleid = false;
                    $tempUserObj->matrnr = $participant->identifier;

                    if($UserObj->checkIfAlreadyParticipant(false, $participant->identifier)){ // if participant is already saved for instance
                        array_push($existingMatriculationnumbersArr, $tempUserObj);
                        unset($tempParticipants[$key]);
                        array_push($tempIDsArray, $participant->identifier); //for finding already temp users
                    } else {
                        array_push($newNoneMoodleParticipantsArr, $tempUserObj);
                        unset($tempParticipants[$key]);
                        array_push($tempIDsArray, $participant->identifier); //for finding already temp users
                    }
                }
            }

            // push all remaining matriculation numbers that could not be resolved by ldap into the $matriculationnumbersarray
            foreach($tempParticipants as $key => $participant){
                $tempUserObj = new stdclass;
                $tempUserObj->line = $participant->line;
                $tempUserObj->matrnr = $participant->identifier;
                $tempUserObj->state = 'badmatrnr';

                array_push($badMatriculationnumbersArr, $tempUserObj);
                unset($tempParticipants[$key]);
            }

            $oldheaders;
            if($PAULFileHeadersArr){ //if participant is deleted

                foreach($PAULFileHeadersArr as $key => $PAULFileHeader){
    
                        if($PAULFileHeader->header == $fileheader){
                                foreach($PAULFileHeader->participants as $key => $savedParticipantId){
    
                                        if(!in_array($savedParticipantId, $tempMoodleIDsArr)){
                                                $deletedMatrNrObj = new stdclass;
                                                $deletedMatrNrObj->moodleid = $savedParticipantId;
                                                $deletedMatrNrObj->matrnr = false;
                                                $deletedMatrNrObj->row = '';
    
                                                array_push($deletedMatriculationnumbersArr, $deletedMatrNrObj);
                                        }
                                }
                        }
                }
            }

            var_dump($badMatriculationnumbersArr);
            var_dump($oddMatriculationnumbersArr);
            var_dump($deletedMatriculationnumbersArr);
            var_dump($existingMatriculationnumbersArr);
            var_dump($newMoodleParticipantsArr);
            var_dump($newNoneMoodleParticipantsArr);
            var_dump($tempParticipants);

            ###### view all temporary imported participants ######

            if($badMatriculationnumbersArr){

                $mform->addElement('html', '<div class="panel panel-danger">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . count($badMatriculationnumbersArr) . ' ' . get_string("badmatrnr", "mod_exammanagement"). '</h3></div>');
                $mform->addElement('html', '<div class="panel-body">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-2"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');

                foreach ((array) $badMatriculationnumbersArr as $key => $tempUserObj) {
                  $matrnr = $tempUserObj->matrnr;

                  $mform->addElement('html', '<div class="row text-danger">');
                  $mform->addElement('html', '<div class="col-xs-1"> # '.$tempUserObj->line);
                  $mform->addElement('html', '</div><div class="col-xs-3"> - </div>');
                  $mform->addElement('html', '<div class="col-xs-2">'.$matrnr.'</div>');
                  $mform->addElement('html', '<div class="col-xs-3"> - </div>');
                  $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_badmatrnr", "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');
            }

            if($oddMatriculationnumbersArr || $newNoneMoodleParticipantsArr){

                $mform->addElement('html', '<div class="panel panel-warning">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . count($oddMatriculationnumbersArr) . ' ' . get_string("oddmatrnr", "mod_exammanagement"). '</h3></div>');
                $mform->addElement('html', '<div class="panel-body">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-2"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');
                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3 remove_col">');
                $mform->addElement('advcheckbox', 'checkall_odds', 'Alle aus-/abwählen', null, array('group' => 1, 'id' => 'checkboxgroup1'));
                $mform->addElement('html', '</div><div class="col-xs-2"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');

                if($oddMatriculationnumbersArr){
                    foreach ((array) $oddMatriculationnumbersArr as $key => $userObj) {
                        $moodleid = $userObj->moodleid;
                        $matrnr = $userObj->matrnr;
      
                        $mform->addElement('html', '<div class="row text-warning">');
                        $mform->addElement('html', '<div class="col-xs-1"> # '.$userObj->line);
                        $mform->addElement('html', '</div><div class="col-xs-3 remove_col">');
                        $mform->addElement('advcheckbox', 'participants['.$moodleid.']', ' '.$UserObj->getUserPicture($moodleid).' '.$UserObj->getUserProfileLink($moodleid), null, array('group' => 1));
                        $mform->addElement('html', '</div><div class="col-xs-2">'.$matrnr.'</div>');
                        $mform->addElement('html', '<div class="col-xs-3"> - </div>');
                        $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_oddmatrnr_nocourseparticipant", "mod_exammanagement").'</div></div>');
                      }
      
                      $mform->addElement('html', '</div></div>');
                } else if ($newNoneMoodleParticipantsArr){
                    foreach ((array) $newNoneMoodleParticipantsArr as $key => $userObj) {
                        $matrnr = $userObj->matrnr;
      
                        $mform->addElement('html', '<div class="row text-warning">');
                        $mform->addElement('html', '<div class="col-xs-1"> # '.$userObj->line);
                        $mform->addElement('html', '</div><div class="col-xs-3 remove_col">');
                        $mform->addElement('advcheckbox', 'participants['.$matrnr.']', '', null, array('group' => 1));
                        $mform->addElement('html', '</div><div class="col-xs-2">'.$matrnr.'</div>');
                        $mform->addElement('html', '<div class="col-xs-3"> - </div>');
                        $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_newmatrnr_no_moodle", "mod_exammanagement").'</div></div>');
                      }
      
                      $mform->addElement('html', '</div></div>');
                }
                
            }

            if($deletedMatriculationnumbersArr){

                $mform->addElement('html', '<div class="panel panel-success">');
                $mform->addElement('html', '<div class="panel-heading text-danger"><h3 class="panel-title">' . count($deletedMatriculationnumbersArr) . ' ' . get_string("deletedmatrnr", "mod_exammanagement"). '</h3></div>');
                $mform->addElement('html', '<div class="panel-body text">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-2"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');
                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3 remove_col">');
                $mform->addElement('advcheckbox', 'checkall_deleted', 'Alle aus-/abwählen', null, array('group' => 3, 'id' => 'checkboxgroup3'));
                $mform->setDefault('checkall_deleted', true);
                $mform->addElement('html', '</div><div class="col-xs-2"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');

                foreach ($deletedMatriculationnumbersArr as $key => $userObj) {

                    $moodleid = $userObj->moodleid;
                    $matrnr = $userObj->matrnr;

                    $mform->addElement('html', '<div class="row text-danger">');
                    if($userObj->row){
                      $mform->addElement('html', '<div class="col-xs-1"> # '.$userObj->line);
                    } else {
                      $mform->addElement('html', '<div class="col-xs-1">');
                    }
                    $mform->addElement('html', '</div><div class="col-xs-3 remove_col">');
                    $mform->addElement('advcheckbox', 'participants['.$moodleid.']', ' '.$UserObj->getUserPicture($moodleid).' '.$UserObj->getUserProfileLink($moodleid), null, array('group' => 3));
                    $mform->setDefault('participants['.$moodleid.']', true);
                    $mform->addElement('html', '</div><div class="col-xs-2">'.$matrnr.'</div>');
                    $mform->addElement('html', '<div class="col-xs-3">'.$UserObj->getParticipantsGroupNames($moodleid).'</div>');
                    $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_deletedmatrnr", "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');

            }

            if($existingMatriculationnumbersArr){

                $mform->addElement('html', '<div class="panel panel-info">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . count($existingMatriculationnumbersArr) . ' ' . get_string("existingmatrnr", "mod_exammanagement"). '</h3></div>');
                $mform->addElement('html', '<div class="panel-body">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-2"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');

                foreach ((array) $existingMatriculationnumbersArr as $key => $userObj) {

                  $moodleid = $userObj->moodleid;
                  $matrnr = $userObj->matrnr;

                  $mform->addElement('html', '<div class="row text-info">');
                  $mform->addElement('html', '<div class="col-xs-1"> # '.$userObj->line);
                  $mform->addElement('html', '</div><div class="col-xs-3"> ' . $UserObj->getUserPicture($moodleid).' '.$UserObj->getUserProfileLink($moodleid) . ' </div>');
                  $mform->addElement('html', '<div class="col-xs-2">'.$matrnr.'</div>');
                  $mform->addElement('html', '<div class="col-xs-3">'.$UserObj->getParticipantsGroupNames($moodleid).'</div>');
                  $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_existingmatrnr", "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');
            }

            if($newMoodleParticipantsArr){

                $mform->addElement('html', '<div class="panel panel-success">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . count($newMoodleParticipantsArr) . ' ' . get_string("newmatrnr", "mod_exammanagement"). '</h3></div>');
                $mform->addElement('html', '<div class="panel-body">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-2"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');
                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3 remove_col">');
                $mform->addElement('advcheckbox', 'checkall_new', 'Alle aus-/abwählen', null, array('group' => 2, 'id' => 'checkboxgroup2'));
                $mform->setDefault('checkall_new', true);
                $mform->addElement('html', '</div><div class="col-xs-2"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');

                foreach ($newMoodleParticipantsArr as $key => $userObj) {
                    $moodleid = $userObj->moodleid;
                    $matrnr = $userObj->matrnr;

                    $mform->addElement('html', '<div class="row text-success">');
                    $mform->addElement('html', '<div class="col-xs-1"> # '.$userObj->line);
                    $mform->addElement('html', '</div><div class="col-xs-3 remove_col">');
                    $mform->addElement('advcheckbox', 'participants['.$moodleid.']', ' '.$UserObj->getUserPicture($moodleid).' '.$UserObj->getUserProfileLink($moodleid), null, array('group' => 2));
                    $mform->setDefault('participants['.$moodleid.']', true);
                    $mform->addElement('html', '</div><div class="col-xs-2">'.$matrnr.'</div>');
                    $mform->addElement('html', '<div class="col-xs-3">'.$UserObj->getParticipantsGroupNames($moodleid).'</div>');
                    $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_newmatrnr", "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');

            }

            if ($newMoodleParticipantsArr || $newNoneMoodleParticipantsArr || $oddMatriculationnumbersArr || $deletedMatriculationnumbersArr){
                  $this->add_action_buttons(true, get_string("add_to_exam", "mod_exammanagement"));
            } else {
              $mform->addElement('html', '<div class="row"><span class="col-sm-5"></span><a href="'.$ExammanagementInstanceObj->getExammanagementUrl("view", $this->_customdata['id']).'" class="btn btn-primary">'.get_string("cancel", "mod_exammanagement").'</a></div>');
            }

            $mform->addElement('html', '</div>');

        } else {

            ###### add Participants from File ######

            $maxbytes=$CFG->maxbytes;

            $mform->addElement('html', '<h4>'.get_string("paul_file", "mod_exammanagement").'</h4>');
            $mform->addElement('filepicker', 'participantslist_paul', get_string("import_from_paul_file", "mod_exammanagement"), null, array('maxbytes' => $maxbytes, 'accepted_types' => '.txt'));

            $mform->addElement('html', '<div class="hidden"><h4>'.get_string("excel_file", "mod_exammanagement").'</h4>');
            $mform->addElement('filepicker', 'participantslist_excel', get_string("import_from_excel_file", "mod_exammanagement"), null, array('maxbytes' => $maxbytes, 'accepted_types' => '.csv, .xlsx, .ods, .xls'));
            $mform->addElement('html', '</div>');

            $this->add_action_buttons(true, get_string("read_file", "mod_exammanagement"));
          }
    }

    //Custom validation should be added here
    public function validation($data, $files){
        return array();
    }
}
