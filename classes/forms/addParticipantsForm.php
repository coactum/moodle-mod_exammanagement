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
 * @copyright   coactum GmbH 2019
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
        $UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $MoodleObj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $MoodleDBObj = MoodleDB::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'remove_form_classes_col'); //call removing moodle form classes col-md for better layout
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'enable_cb'); //call jquery for checking all checkboxes via following checkbox

        $mform = $this->_form; // Don't forget the underscore!

        $tempParticipants = $MoodleDBObj->getRecordsFromDB('exammanagement_temp_part', array('plugininstanceid' => $this->_customdata['id']));

        $mform->addElement('html', '<div class="row"><div class="col-xs-6">');

        if($tempParticipants){
            $mform->addElement('html', '<h3>'.get_string("addParticipants", "mod_exammanagement").'</h3>');
        } else {
            $mform->addElement('html', '<h3>'.get_string("import_participants_from_file", "mod_exammanagement").'</h3>');
        }
        $mform->addElement('html', '</div><div class="col-xs-2"><a class="helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;" title="'.get_string("helptext_open", "mod_exammanagement").'"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');

        if($tempParticipants){
            $mform->addElement('html', '<div class="col-xs-4"><a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/addParticipants.php', $this->_customdata['id'], 'dtp', true).'" role="button" class="btn btn-primary pull-right" title="'.get_string("import_new_participants", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("import_new_participants", "mod_exammanagement").'</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a></div>');
        }

        $mform->addElement('html', '</div>');

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('addParticipants'));

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        if($tempParticipants){

            if($ExammanagementInstanceObj->allPlacesAssigned()){
                $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>'.get_string("places_already_assigned_participants", "mod_exammanagement").'</div>');
            }

            $newMoodleParticipantsArr = array();
            $newNoneMoodleParticipantsArr = array();
            $badMatriculationnumbersArr = array();
            $oddMatriculationnumbersArr = array();
            $existingMoodleParticipantsArr = array();
            $existingMatriculationnumbersArr = array();
            $deletedMoodleParticipantsArr = array();
            $deletedMatriculationnumbersArr = array();

            $tempIDsArray = array();

            $loginsArray = array();
            $moodleuseridsArray = array();

            var_dump('saved tempParticipants');
            var_dump($tempParticipants);

            ###### classify temp participants
            
            foreach($tempParticipants as $key => $participant){ // filter invalid/bad matrnr

                if (!$UserObj->checkIfValidMatrNr($participant->identifier)){
                    $tempUserObj = new stdclass;
                    $tempUserObj->line = $participant->line;
                    $tempUserObj->matrnr = $participant->identifier;
                    $tempUserObj->state = 'state_badmatrnr';
                    
                    array_push($badMatriculationnumbersArr, $tempUserObj);
                    unset($tempParticipants[$key]);
                }
            }

            if($LdapManagerObj->is_LDAP_config()){
                $ldapConnection = $LdapManagerObj->connect_ldap();

                $matrnrArray = array();
                
                foreach($tempParticipants as $key => $participant){
                    array_push($matrnrArray, $participant->identifier);
                }

                $linesArray = array_column($tempParticipants, 'line');

                $loginsArray = $LdapManagerObj->getLDAPAttributesForMatrNrs($ldapConnection, $matrnrArray, array(LDAP_ATTRIBUTE_UID), $linesArray);

                foreach($loginsArray as $line => $login){
                    $moodleuserid = $MoodleDBObj->getFieldFromDB('user','id', array('username' => $login['login']));

                    if($moodleuserid){
                        $moodleuseridsArray[$line] = array('matrnr' => $login->matrnr, 'moodleuserid' => $moodleuserid);
                        unset($loginsArray[$line]);
                    }
                }

            } else {
                foreach($tempParticipants as $key => $participant){ // unterscheiden für nicht moodle users

                    $userid = $LdapManagerObj->getMatriculationNumber2ImtLoginTest($participant->identifier);

                    if($userid){
                        $moodleuseridsArray[$participant->line] = array('matrnr' => $participant->identifier, 'moodleuserid' => $userid);
                    } else {
                        $login = $LdapManagerObj->getMatriculationNumber2ImtLoginNoneMoodleTest($participant->identifier);
                        $loginsArray[$participant->line] = array('matrnr' => $participant->identifier, 'login' => $login);
                    }
                }
            }

            var_dump('moodleuserarray');
            var_dump($moodleuseridsArray);
            var_dump('loginuserarray');
            var_dump($loginsArray);

            // check tempusers moodle userids using LDAP and save them in moodleuseridsarray                
                    
            foreach($moodleuseridsArray as $line => $data){ // check all moodle users

                if (isset($data['moodleuserid']) && $data['moodleuserid']){
                    $tempUserObj = new stdclass;
                    $tempUserObj->line = $line;
                    $tempUserObj->moodleuserid = $data['moodleuserid'];
                    $tempUserObj->matrnr = $data['matrnr'];

                    if(in_array($data['moodleuserid'], $tempIDsArray)){ // if participant is already known as temp participant
                        $tempUserObj->state = 'state_doubled';
                        array_push($badMatriculationnumbersArr, $tempUserObj);
                    } else if($UserObj->checkIfAlreadyParticipant($data['moodleuserid'])){ // if participant is already saved for instance
                        array_push($existingMoodleParticipantsArr, $tempUserObj);
                        array_push($tempIDsArray, $data['moodleuserid']); //for finding already temp users
                    } else if (!in_array($data['moodleuserid'], $UserObj->getCourseParticipantsIDs())){ // if participant is not in course
                        array_push($oddMatriculationnumbersArr, $tempUserObj);
                        array_push($tempIDsArray, $data['moodleuserid']); //for finding already temp users
                    } else {	// if participant is valid participant
                        array_push($newMoodleParticipantsArr, $tempUserObj);
                        array_push($tempIDsArray, $data['moodleuserid']); //for finding already temp users
                    }

                    foreach($tempParticipants as $key => $participant){
                        if($participant->identifier == $data['matrnr']){
                            unset($tempParticipants[$key]);
                            break;
                        }
                    }
                }
            }
            
            foreach($loginsArray as $line => $data){

                if (isset($data['login']) && $data['login']){	// if participant is no moodle user
                    $tempUserObj = new stdclass;
                    $tempUserObj->line = $line;
                    $tempUserObj->moodleuserid = false;
                    $tempUserObj->matrnr = $data['matrnr'];

                    if(in_array($data['login'], $tempIDsArray)){
                        $tempUserObj->state = 'state_doubled';
                        array_push($badMatriculationnumbersArr, $tempUserObj);
                    } else if($UserObj->checkIfAlreadyParticipant(false, $data['login'])){ // if participant is already saved for instance
                        array_push($existingMatriculationnumbersArr, $tempUserObj);
                        array_push($tempIDsArray, $data['login']); //for finding already temp users
                    } else {
                        array_push($newNoneMoodleParticipantsArr, $tempUserObj);
                        array_push($tempIDsArray, $data['login']); //for finding already temp users
                    }

                    foreach($tempParticipants as $key => $participant){ // unterscheiden für nicht moodle users
                        if($participant->identifier == $data['matrnr']){
                            unset($tempParticipants[$key]);
                            break;
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
            $oldParticipants = $MoodleDBObj->getRecordsFromDB('exammanagement_participants', array('plugininstanceid' => $this->_customdata['id'], 'headerid' => $headerid));
            
            $matrNrForOldParticipants = $UserObj->getMultipleUsersMatrNr($oldParticipants);

            if($oldParticipants){ //if participant is deleted

                foreach($oldParticipants as $key => $participant){
                    if($participant->moodleuserid){
                        $login = $MoodleDBObj->getFieldFromDB('user','username', array('id' => $participant->moodleuserid));
                    } else {
                        $login = false;
                    }

                    $matrnr = false;

                    if($matrNrForOldParticipants){
                        if($login && array_key_exists($login, $matrNrForOldParticipants)){
                            $matrnr = $matrNrForOldParticipants[$login];
                        } else if($participant->imtlogin && array_key_exists($participant->imtlogin, $matrNrForOldParticipants)){
                            $matrnr = $matrNrForOldParticipants[$participant->imtlogin];
                        } 
                    }
    
                    if(!in_array($participant->moodleuserid, $tempIDsArray) && $matrnr && !in_array($matrnr, $tempIDsArray)){

                        if($participant->moodleuserid){
                            $deletedMatrNrObj = new stdclass;
                            $deletedMatrNrObj->moodleuserid = $participant->moodleuserid;
                            $deletedMatrNrObj->line = '';
        
                            array_push($deletedMoodleParticipantsArr, $deletedMatrNrObj);
                        } else if($participant->imtlogin){
                            $deletedMatrNrObj = new stdclass;
                            $deletedMatrNrObj->matrnr = $matrnr;
                            $deletedMatrNrObj->firstname = $participant->firstname;
                            $deletedMatrNrObj->lastname = $participant->lastname;
                            $deletedMatrNrObj->line = '';
        
                            array_push($deletedMatriculationnumbersArr, $deletedMatrNrObj);
                        }
                        
                    }
                }
            }

            ###### view all temporary imported participants ######

            $mform->addElement('html', '<div class="exammanagement_overview">');

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
                $mform->addElement('advcheckbox', 'checkall_odds', get_string("select_deselect_all", "mod_exammanagement"), null, array('group' => 1, 'id' => 'checkboxgroup1'));
                $mform->addElement('html', '</div><div class="col-xs-2"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');

                if($oddMatriculationnumbersArr){ // no course participant
                    foreach ((array) $oddMatriculationnumbersArr as $key => $userObj) {
                        $moodleuserid = $userObj->moodleuserid;
                        $matrnr = $userObj->matrnr;
      
                        $mform->addElement('html', '<div class="row text-warning">');
                        $mform->addElement('html', '<div class="col-xs-1"> # '.$userObj->line);
                        $mform->addElement('html', '</div><div class="col-xs-3 remove_col">');
                        $mform->addElement('advcheckbox', 'participants[mid_'.$moodleuserid.']', ' '.$UserObj->getUserPicture($moodleuserid).' '.$UserObj->getUserProfileLink($moodleuserid), null, array('group' => 1));
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
                $mform->addElement('advcheckbox', 'checkall_deleted', get_string("select_deselect_all", "mod_exammanagement"), null, array('group' => 2, 'id' => 'checkboxgroup2'));
                $mform->setDefault('checkall_deleted', true);
                $mform->addElement('html', '</div><div class="col-xs-2"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');

                #### deleted with moodle account ######
                if($deletedMoodleParticipantsArr){

                    foreach ($deletedMoodleParticipantsArr as $key => $participantObj) {
                        if($participantObj->moodleuserid){
                            $login = $MoodleDBObj->getFieldFromDB('user','username', array('id' => $participantObj->moodleuserid));
                        } else {
                            $login = false;
                        }
    
                        $matrnr = false;
    
                        if($matrNrForOldParticipants){
                            if($login && array_key_exists($login, $matrNrForOldParticipants)){
                                $matrnr = $matrNrForOldParticipants[$login];
                            }
                        }

                        if($matrnr === false){
                            $matrnr = '-';
                        }

                        $mform->addElement('html', '<div class="row text-danger"><div class="col-xs-1"></div><div class="col-xs-3 remove_col pl-4">');

                        $mform->addElement('advcheckbox', 'deletedparticipants[mid_'.$participantObj->moodleuserid.']', ' '.$UserObj->getUserPicture($participantObj->moodleuserid).' '.$UserObj->getUserProfileLink($participantObj->moodleuserid), null, array('group' => 2));

                        $mform->setDefault('deletedparticipants[mid_'.$participantObj->moodleuserid.']', true);

                        $mform->addElement('html', '</div><div class="col-xs-2">'.$matrnr.'</div><div class="col-xs-3">');

                        $mform->addElement('html', $UserObj->getParticipantsGroupNames($participantObj->moodleuserid));

                        $mform->addElement('html', '</div><div class="col-xs-3">'.get_string("state_deletedmatrnr", "mod_exammanagement").'</div></div>');

                    }
                }

                ###### deleted without moodle account  ######

                if($deletedMatriculationnumbersArr){

                    foreach ($deletedMatriculationnumbersArr as $key => $participantObj) {

                        $matrnr = $participantObj->matrnr;

                        $mform->addElement('html', '<div class="row text-danger"><div class="col-xs-1"></div><div class="col-xs-3 remove_col pl-4">');

                        $mform->addElement('advcheckbox', 'deletedparticipants[matrnr_'.$matrnr.']', ' '. $participantObj->firstname .' '.$participantObj->lastname, null, array('group' => 2));

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
    
                      $moodleuserid = $userObj->moodleuserid;
                      $matrnr = $userObj->matrnr;
    
                      $mform->addElement('html', '<div class="row text-info">');
                      $mform->addElement('html', '<div class="col-xs-1"> # '.$userObj->line);
                      $mform->addElement('html', '</div><div class="col-xs-3"> ' . $UserObj->getUserPicture($moodleuserid).' '.$UserObj->getUserProfileLink($moodleuserid) . ' </div>');
                      $mform->addElement('html', '<div class="col-xs-2">'.$matrnr.'</div>');
                      $mform->addElement('html', '<div class="col-xs-3">'.$UserObj->getParticipantsGroupNames($moodleuserid).'</div>');
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
                $mform->addElement('advcheckbox', 'checkall_new', get_string("select_deselect_all", "mod_exammanagement"), null, array('group' => 3, 'id' => 'checkboxgroup3'));
                $mform->setDefault('checkall_new', true);
                $mform->addElement('html', '</div><div class="col-xs-2"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');

                foreach ($newMoodleParticipantsArr as $key => $userObj) { // new moodle
                    $moodleuserid = $userObj->moodleuserid;
                    $matrnr = $userObj->matrnr;

                    $mform->addElement('html', '<div class="row text-success">');
                    $mform->addElement('html', '<div class="col-xs-1"> # '.$userObj->line);
                    $mform->addElement('html', '</div><div class="col-xs-3 remove_col">');
                    $mform->addElement('advcheckbox', 'participants[mid_'.$moodleuserid.']', ' '.$UserObj->getUserPicture($moodleuserid).' '.$UserObj->getUserProfileLink($moodleuserid), null, array('group' => 3));
                    $mform->setDefault('participants[mid_'.$moodleuserid.']', true);
                    $mform->addElement('html', '</div><div class="col-xs-2">'.$matrnr.'</div>');
                    $mform->addElement('html', '<div class="col-xs-3">'.$UserObj->getParticipantsGroupNames($moodleuserid).'</div>');
                    $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_newmatrnr", "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');

            }

            if ($newMoodleParticipantsArr || $newNoneMoodleParticipantsArr || $oddMatriculationnumbersArr || $deletedMatriculationnumbersArr){

                $maxbytes=$CFG->maxbytes;

                $mform->addElement('html', '<div class="hidden">');
                $mform->addElement('filepicker', 'participantslist_paul', get_string("import_from_paul_file", "mod_exammanagement"), null, array('maxbytes' => $maxbytes, 'accepted_types' => '.txt'));
                $mform->addElement('html', '</div>');

                $this->add_action_buttons(true, get_string("add_to_exam", "mod_exammanagement"));
            } else {
              $mform->addElement('html', '<div class="row"><span class="col-sm-5"></span><a href="'.$ExammanagementInstanceObj->getExammanagementUrl("view", $this->_customdata['id']).'" class="btn btn-primary">'.get_string("cancel", "mod_exammanagement").'</a></div>');
            }

            $mform->addElement('html', '</div>');

        } else {

            ###### add Participants from File ######

            $maxbytes=$CFG->maxbytes;

            $mform->addElement('filepicker', 'participantslist_paul', get_string("import_from_paul_file", "mod_exammanagement"), null, array('maxbytes' => $maxbytes, 'accepted_types' => '.txt'));
            $mform->addRule('participantslist_paul', get_string('err_nofile', 'mod_exammanagement'), 'required', 'client');

            $this->add_action_buttons(true, get_string("read_file", "mod_exammanagement"));
        }
    }

    //Custom validation should be added here
    public function validation($data, $files){

        $errors = array();

        if(isset($data['participants'])){
            foreach($data['participants'] as $participantid => $checked){

                if(!preg_match("/^[a-zA-Z0-9_\-]+$/", $participantid)){
                    $errors['participants['.$participantid.']'] = get_string('err_invalidcheckboxid_participants', 'mod_exammanagement');
                }
            }
        }

        if(isset($data['deletedparticipants'])){
            foreach($data['deletedparticipants'] as $participantid => $checked){

                if(!preg_match("/^[a-zA-Z0-9_\-]+$/", $participantid)){
                    $errors['deletedparticipants['.$participantid.']'] = get_string('err_invalidcheckboxid_participants', 'mod_exammanagement');
                }
            }
        }

        return $errors;
    }
}
