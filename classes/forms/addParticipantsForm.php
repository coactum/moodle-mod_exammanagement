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
            $mform->addElement('html', '<h3>'.get_string("import_participants_from_file", "mod_exammanagement").'</h3>');
        }
        $mform->addElement('html', '</div><div class="col-xs-2"><a class="helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');

        if($tempParticipants){
            $mform->addElement('html', '<div class="col-xs-4"><a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/addParticipants.php', $this->_customdata['id'], 'dtp', true).'" role="button" class="btn btn-primary pull-right" title="'.get_string("import_new_participants", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("import_new_participants", "mod_exammanagement").'</span><i class="fa fa-plus d-lg-none" aria-hidden="true"></i></a></div>');
        }

        $mform->addElement('html', '</div>');

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('addParticipants'));

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        if($tempParticipants){

            $newMoodleParticipantsArr = array();
            $newNoneMoodleParticipantsArr = array();
            $badMatriculationnumbersArr = array();
            $oddMatriculationnumbersArr = array();
            $existingMoodleParticipantsArr = array();
            $existingMatriculationnumbersArr = array();
            $deletedMoodleParticipantsArr = array();
            $deletedMatriculationnumbersArr = array();

            $tempIDsArray = array();

            ###### classify temp participants
            
            foreach($tempParticipants as $key => $participant){

                // filter invalid/bad matrnr
                if (!$UserObj->checkIfValidMatrNr($participant->identifier)){
                    $tempUserObj = new stdclass;
                    $tempUserObj->line = $participant->line;
                    $tempUserObj->matrnr = $participant->identifier;
                    $tempUserObj->state = 'state_badmatrnr';
                    
                    array_push($badMatriculationnumbersArr, $tempUserObj);
                    unset($tempParticipants[$key]);
                } else {
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

                        if(in_array($moodleuserid, $tempIDsArray)){ // if participant is already known as temp participant
                            $tempUserObj->state = 'state_doubled';
                            array_push($badMatriculationnumbersArr, $tempUserObj);
                            unset($tempParticipants[$key]);
                        } else if($UserObj->checkIfAlreadyParticipant($moodleuserid)){ // if participant is already saved for instance
                            array_push($existingMoodleParticipantsArr, $tempUserObj);
                            unset($tempParticipants[$key]);
                            array_push($tempIDsArray, $moodleuserid); //for finding already temp users
                        } else if (!in_array($moodleuserid, $UserObj->getCourseParticipantsIDs())){ // if participant is not in course
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

                        if(in_array($participant->identifier, $tempIDsArray)){
                            $tempUserObj->state = 'state_doubled';
                            array_push($badMatriculationnumbersArr, $tempUserObj);
                            unset($tempParticipants[$key]);
                        } else if($UserObj->checkIfAlreadyParticipant(false, $participant->identifier)){ // if participant is already saved for instance
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
            }

            // push all remaining matriculation numbers that could not be resolved by ldap into the $matriculationnumbersarray
            foreach($tempParticipants as $key => $participant){
                $tempUserObj = new stdclass;
                $tempUserObj->line = $participant->line;
                $tempUserObj->matrnr = $participant->identifier;
                $tempUserObj->state = 'state_badmatrnr';

                array_push($badMatriculationnumbersArr, $tempUserObj);
                unset($tempParticipants[$key]);
            }

            // get header id
            $tempfileheader = json_decode($ExammanagementInstanceObj->moduleinstance->tempimportfileheader);
		    $savedFileHeadersArr = json_decode($ExammanagementInstanceObj->moduleinstance->importfileheaders);
            $headerid;

			if(!$savedFileHeadersArr && $tempfileheader){ // if there are no saved headers by now
				$headerid = 1;
			} else if($savedFileHeadersArr && $tempfileheader){
                $saved = false;
				foreach($savedFileHeadersArr as $key => $header){ // if new header is already saved
					if($tempfileheader == $header){
						$headerid = $key+1;
						$saved = true;
					}
				}
					
				if(!$saved){ // if new header is not saved yet
					$headerid = count($savedFileHeadersArr)+1;
				}
            } else if(!$tempfileheader){ // if reading of tempfileheader fails
				$headerid = 0;
            }

            // get saved participants for headerid
            $oldParticipants = $MoodleDBObj->getRecordsFromDB('exammanagement_part_' . $ExammanagementInstanceObj->moduleinstance->categoryid, array('plugininstanceid' => $this->_customdata['id'], 'headerid' => $headerid));
            
            if($oldParticipants){ //if participant is deleted

                foreach($oldParticipants as $key => $participant){

                    if($LdapManagerObj->is_LDAP_config()){
                        $ldapConnection = $LdapManagerObj->connect_ldap();
                        $pmatrnr = $LdapManagerObj->studentid2uid($ldapConnection, $participant->imtlogin);
                    } else {
                        $pmatrnr = $LdapManagerObj->getIMTLogin2MatriculationNumberTest(false, $participant->imtlogin);
                    }
    
                    if(!in_array($participant->moodleuserid, $tempIDsArray) && !in_array($pmatrnr, $tempIDsArray)){

                        if($participant->moodleuserid){
                            $deletedMatrNrObj = new stdclass;
                            $deletedMatrNrObj->moodleid = $participant->moodleuserid;
                            $deletedMatrNrObj->line = '';
        
                            array_push($deletedMoodleParticipantsArr, $deletedMatrNrObj);
                        } else if($participant->imtlogin){
                            $deletedMatrNrObj = new stdclass;
                            $deletedMatrNrObj->matrnr = $pmatrnr;
                            $deletedMatrNrObj->firstname = $participant->firstname;
                            $deletedMatrNrObj->lastname = $participant->lastname;
                            $deletedMatrNrObj->line = '';
        
                            array_push($deletedMatriculationnumbersArr, $deletedMatrNrObj);
                        }
                        
                    }
                }
            }

            ###### view all temporary imported participants ######

            if($badMatriculationnumbersArr){

                $mform->addElement('html', '<div class="panel panel-danger">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . count($badMatriculationnumbersArr) . ' ' . get_string("badmatrnr", "mod_exammanagement"). '</h3></div>');
                $mform->addElement('html', '<div class="panel-body">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-2"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');

                foreach ((array) $badMatriculationnumbersArr as $key => $tempUserObj) { // bad or double matrnr
                  $matrnr = $tempUserObj->matrnr;

                  $mform->addElement('html', '<div class="row text-danger">');
                  $mform->addElement('html', '<div class="col-xs-1"> # '.$tempUserObj->line);
                  $mform->addElement('html', '</div><div class="col-xs-3"> - </div>');
                  $mform->addElement('html', '<div class="col-xs-2">'.$matrnr.'</div>');
                  $mform->addElement('html', '<div class="col-xs-3"> - </div>');
                  $mform->addElement('html', '<div class="col-xs-3">'.get_string($tempUserObj->state, "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');
            }

            if($oddMatriculationnumbersArr || $newNoneMoodleParticipantsArr){

                $count = count($oddMatriculationnumbersArr) + count($newNoneMoodleParticipantsArr);

                $mform->addElement('html', '<div class="panel panel-warning">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . $count . ' ' . get_string("oddmatrnr", "mod_exammanagement"). '</h3></div>');
                $mform->addElement('html', '<div class="panel-body">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-2"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');
                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3 remove_col">');
                $mform->addElement('advcheckbox', 'checkall_odds', 'Alle aus-/abwählen', null, array('group' => 1, 'id' => 'checkboxgroup1'));
                $mform->addElement('html', '</div><div class="col-xs-2"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');

                if($oddMatriculationnumbersArr){ // no course participant
                    foreach ((array) $oddMatriculationnumbersArr as $key => $userObj) {
                        $moodleid = $userObj->moodleid;
                        $matrnr = $userObj->matrnr;
      
                        $mform->addElement('html', '<div class="row text-warning">');
                        $mform->addElement('html', '<div class="col-xs-1"> # '.$userObj->line);
                        $mform->addElement('html', '</div><div class="col-xs-3 remove_col">');
                        $mform->addElement('advcheckbox', 'participants[mid_'.$moodleid.']', ' '.$UserObj->getUserPicture($moodleid).' '.$UserObj->getUserProfileLink($moodleid), null, array('group' => 1));
                        $mform->addElement('html', '</div><div class="col-xs-2">'.$matrnr.'</div>');
                        $mform->addElement('html', '<div class="col-xs-3"> - </div>');
                        $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_oddmatrnr_nocourseparticipant", "mod_exammanagement").'</div></div>');
                      }      
                }
                
                if ($newNoneMoodleParticipantsArr){ // no moodle participant
                    foreach ((array) $newNoneMoodleParticipantsArr as $key => $userObj) {
                        $matrnr = $userObj->matrnr;
      
                        $mform->addElement('html', '<div class="row text-warning">');
                        $mform->addElement('html', '<div class="col-xs-1"> # '.$userObj->line);
                        $mform->addElement('html', '</div><div class="col-xs-3 remove_col">');
                        $mform->addElement('advcheckbox', 'participants[matrnr_'.$matrnr.']', '', null, array('group' => 1));
                        $mform->addElement('html', '</div><div class="col-xs-2">'.$matrnr.'</div>');
                        $mform->addElement('html', '<div class="col-xs-3"> - </div>');
                        $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_newmatrnr_no_moodle", "mod_exammanagement").'</div></div>');
                      }
      
                }
                $mform->addElement('html', '</div></div>');
                
            }

            if($deletedMoodleParticipantsArr || $deletedMatriculationnumbersArr){

                $deletedcount = count($deletedMoodleParticipantsArr) + count($deletedMatriculationnumbersArr);

                $mform->addElement('html', '<div class="panel panel-success">');
                $mform->addElement('html', '<div class="panel-heading text-danger"><h3 class="panel-title">' . $deletedcount . ' ' . get_string("deletedmatrnr", "mod_exammanagement"). '</h3></div>');
                $mform->addElement('html', '<div class="panel-body">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-2"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');
                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3 remove_col pl-4">');
                $mform->addElement('advcheckbox', 'checkall_deleted', 'Alle aus-/abwählen', null, array('group' => 1, 'id' => 'checkboxgroup1'));
                $mform->setDefault('checkall_deleted', true);
                $mform->addElement('html', '</div><div class="col-xs-2"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');

                #### deleted with moodle account ######
                if($deletedMoodleParticipantsArr){

                    foreach ($deletedMoodleParticipantsArr as $key => $participantObj) {

                        $matrnr = $UserObj->getUserMatrNr($participantObj->moodleid);

                        $mform->addElement('html', '<div class="row text-danger"><div class="col-xs-1"></div><div class="col-xs-3 remove_col pl-4">');

                        $mform->addElement('advcheckbox', 'deletedparticipants[mid_'.$participantObj->moodleid.']', ' '.$UserObj->getUserPicture($participantObj->moodleid).' '.$UserObj->getUserProfileLink($participantObj->moodleid), null, array('group' => 1));

                        $mform->setDefault('deletedparticipants[mid_'.$participantObj->moodleid.']', true);

                        $mform->addElement('html', '</div><div class="col-xs-2">'.$matrnr.'</div><div class="col-xs-3">');

                        $mform->addElement('html', $UserObj->getParticipantsGroupNames($participantObj->moodleid));

                        $mform->addElement('html', '</div><div class="col-xs-3">'.get_string("state_deletedmatrnr", "mod_exammanagement").'</div></div>');

                    }
                }

                ###### deleted without moodle account  ######

                if($deletedMatriculationnumbersArr){

                    foreach ($deletedMatriculationnumbersArr as $key => $participantObj) {

                        $matrnr = $participantObj->matrnr;

                        $mform->addElement('html', '<div class="row text-danger"><div class="col-xs-1"></div><div class="col-xs-3 remove_col pl-4">');

                        $mform->addElement('advcheckbox', 'deletedparticipants[matrnr_'.$matrnr.']', ' '. $participantObj->firstname .' '.$participantObj->lastname, null, array('group' => 1));

                        $mform->setDefault('deletedparticipants[matrnr_'.$matrnr.']', true);

                        $mform->addElement('html', '</div><div class="col-xs-2">'.$matrnr.'</div><div class="col-xs-3">');

                        $mform->addElement('html', '-');

                        $mform->addElement('html', '</div><div class="col-xs-3">'.get_string("state_deletedmatrnr", "mod_exammanagement").'</div></div>');
                    }
                }

                $mform->addElement('html', '</div></div>');

            }

            if($existingMoodleParticipantsArr || $existingMatriculationnumbersArr){

                $count = count($existingMoodleParticipantsArr) + count($existingMatriculationnumbersArr);

                $mform->addElement('html', '<div class="panel panel-info">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . $count . ' ' . get_string("existingmatrnr", "mod_exammanagement"). '</h3></div>');
                $mform->addElement('html', '<div class="panel-body">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-2"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');

                if($existingMoodleParticipantsArr){ // existing with moodle
    
                    foreach ((array) $existingMoodleParticipantsArr as $key => $userObj) {
    
                      $moodleid = $userObj->moodleid;
                      $matrnr = $userObj->matrnr;
    
                      $mform->addElement('html', '<div class="row text-info">');
                      $mform->addElement('html', '<div class="col-xs-1"> # '.$userObj->line);
                      $mform->addElement('html', '</div><div class="col-xs-3"> ' . $UserObj->getUserPicture($moodleid).' '.$UserObj->getUserProfileLink($moodleid) . ' </div>');
                      $mform->addElement('html', '<div class="col-xs-2">'.$matrnr.'</div>');
                      $mform->addElement('html', '<div class="col-xs-3">'.$UserObj->getParticipantsGroupNames($moodleid).'</div>');
                      $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_existingmatrnr", "mod_exammanagement").'</div></div>');
                    }
                }

                if($existingMatriculationnumbersArr){
    
                    foreach ((array) $existingMatriculationnumbersArr as $key => $userObj) { // existing without moodle
    
                      $matrnr = $userObj->matrnr;
    
                      $mform->addElement('html', '<div class="row text-info">');
                      $mform->addElement('html', '<div class="col-xs-1"> # '.$userObj->line);
                      $mform->addElement('html', '</div><div class="col-xs-3"></div>');
                      $mform->addElement('html', '<div class="col-xs-2">'.$matrnr.'</div>');
                      $mform->addElement('html', '<div class="col-xs-3"></div>');
                      $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_existingmatrnr", "mod_exammanagement").'</div></div>');
                    }
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

                foreach ($newMoodleParticipantsArr as $key => $userObj) { // new moodle
                    $moodleid = $userObj->moodleid;
                    $matrnr = $userObj->matrnr;

                    $mform->addElement('html', '<div class="row text-success">');
                    $mform->addElement('html', '<div class="col-xs-1"> # '.$userObj->line);
                    $mform->addElement('html', '</div><div class="col-xs-3 remove_col">');
                    $mform->addElement('advcheckbox', 'participants[mid_'.$moodleid.']', ' '.$UserObj->getUserPicture($moodleid).' '.$UserObj->getUserProfileLink($moodleid), null, array('group' => 2));
                    $mform->setDefault('participants[mid_'.$moodleid.']', true);
                    $mform->addElement('html', '</div><div class="col-xs-2">'.$matrnr.'</div>');
                    $mform->addElement('html', '<div class="col-xs-3">'.$UserObj->getParticipantsGroupNames($moodleid).'</div>');
                    $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_newmatrnr", "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');

            }

            if ($newMoodleParticipantsArr || $newNoneMoodleParticipantsArr || $oddMatriculationnumbersArr || $deletedMatriculationnumbersArr){
                if($ExammanagementInstanceObj->isStateofPlacesCorrect()){
                    $mform->addElement('html', '<p><b>Achtung:</b> Es wurden bereits Sitzplätze zugewiesen. Diese Zuweisung wird durch das Hinzufügen der Teilnehmer gelöscht und muss dann neu durchgeführt werden.</p>');
                }

                $maxbytes=$CFG->maxbytes;

                $mform->addElement('html', '<div class="hidden">');
                $mform->addElement('filepicker', 'participantslist_paul', get_string("import_from_paul_file", "mod_exammanagement"), null, array('maxbytes' => $maxbytes, 'accepted_types' => '.txt'));
                $mform->addElement('html', '</div>');

                $this->add_action_buttons(true, get_string("add_to_exam", "mod_exammanagement"));
            } else {
              $mform->addElement('html', '<div class="row"><span class="col-sm-5"></span><a href="'.$ExammanagementInstanceObj->getExammanagementUrl("view", $this->_customdata['id']).'" class="btn btn-primary">'.get_string("cancel", "mod_exammanagement").'</a></div>');
            }

        } else {

            ###### add Participants from File ######

            $maxbytes=$CFG->maxbytes;

            $mform->addElement('filepicker', 'participantslist_paul', get_string("import_from_paul_file", "mod_exammanagement"), null, array('maxbytes' => $maxbytes, 'accepted_types' => '.txt'));
            $mform->addRule('participantslist_paul', get_string('err_nofile', 'mod_exammanagement'), 'required', 'client');

            $mform->addElement('html', '<div class="hidden">');
            $mform->addElement('filepicker', 'participantslist_excel', get_string("import_from_excel_file", "mod_exammanagement"), null, array('maxbytes' => $maxbytes, 'accepted_types' => '.csv, .xlsx, .ods, .xls'));
            //$mform->addRule('participantslist_excel', get_string('err_nofile', 'mod_exammanagement'), 'required', 'client');
            $mform->addElement('html', '</div>');

            $this->add_action_buttons(true, get_string("read_file", "mod_exammanagement"));
          }
    }

    //Custom validation should be added here
    public function validation($data, $files){

        $errors = array();

        if($data['participants']){
            foreach($data['participants'] as $participantid => $checked){

                if(!preg_match("/^[a-zA-Z0-9_\-]+$/", $participantid)){
                    $errors['participants['.$participantid.']'] = get_string('err_invalidcheckboxid_participants', 'mod_exammanagement');
                }
            }
        }

        if($data['deletedparticipants']){
            foreach($data['deletedparticipants'] as $participantid => $checked){

                if(!preg_match("/^[a-zA-Z0-9_\-]+$/", $participantid)){
                    $errors['deletedparticipants['.$participantid.']'] = get_string('err_invalidcheckboxid_participants', 'mod_exammanagement');
                }
            }
        }

        return $errors;
    }
}
