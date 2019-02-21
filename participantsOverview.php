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
 * Prints participantsOverview form for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\participantsOverviewForm;
use mod_exammanagement\ldap\ldapManager;
use stdClass;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$edit  = optional_param('edit', 0, PARAM_INT);

$MoodleDBObj = MoodleDB::getInstance();
$MoodleObj = Moodle::getInstance($id, $e);
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$UserObj = User::getInstance($id, $e);
$LdapManagerObj = ldapManager::getInstance($id, $e);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

    if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && $SESSION->loggedInExamOrganizationId == $id)){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

        if(!$UserObj->getParticipantsCount()){
            $MoodleObj->redirectToOverviewPage('beforeexam', 'Es müssen erst Prüfungsteilnehmer importiert werden.', 'error');
        } else if(!$ExammanagementInstanceObj->getRoomsCount()){
            $MoodleObj->redirectToOverviewPage('beforeexam', 'Es müssen erst Prüfungsräume ausgewählt werden.', 'error');
        }

        $MoodleObj->setPage('participantsOverview');
        $MoodleObj->outputPageHeader();

        //Instantiate Form
        $mform = new participantsOverviewForm(null, array('id'=>$id, 'e'=>$e, 'edit'=>$edit));

        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            //Handle form cancel operation, if cancel button is present on form
            redirect ($ExammanagementInstanceObj->getExammanagementUrl('participantsOverview', $ExammanagementInstanceObj->getCm()->id), get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

        } else if ($fromform = $mform->get_data()) {
            //In this case you process validated data. $mform->get_data() returns data posted in form.

            if(isset($fromform->editmoodleuserid) && $fromform->editmoodleuserid !== 0){
                $moodleuserid = $fromform->editmoodleuserid;
                $userlogin = NULL;
            } else {
                $moodleuserid = NULL;
                $userlogin = NULL;

				if($LdapManagerObj->is_LDAP_config()){
					$ldapConnection = $LdapManagerObj->connect_ldap();

					$userlogin = $LdapManagerObj->studentid2uid($ldapConnection, $fromform->edit);

				} else {
					$userlogin = $LdapManagerObj->getMatriculationNumber2ImtLoginNoneMoodleTest($fromform->edit);

				}
            }

            $participantObj = $UserObj->getExamParticipantObj($moodleuserid, $userlogin);
           
            $participantObj->roomid = $fromform->room;

            $participantObj->roomname = $ExammanagementInstanceObj->getRoomObj($fromform->room)->name;

            $participantObj->place = $fromform->place;

            $participantObj->exampoints = json_encode($fromform->points);

            switch ($fromform->state){

                case 'normal':
                    $examstate = new stdClass;
                    $examstate->nt = "0";
                    $examstate->fa = "0";
                    $examstate->ill = "0";
                    break;
                case 'nt':
                    $examstate = new stdClass;
                    $examstate->nt = "1";
                    $examstate->fa = "0";
                    $examstate->ill = "0";
                    break;
                case 'fa':
                    $examstate = new stdClass;
                    $examstate->nt = "0";
                    $examstate->fa = "1";
                    $examstate->ill = "0";
                    break;
                case 'ill':
                    $examstate = new stdClass;
                    $examstate->nt = "0";
                    $examstate->fa = "0";
                    $examstate->ill = "1";
                    break;
                default:
                    $examstate = new stdClass;
                    $examstate->nt = "0";
                    $examstate->fa = "0";
                    $examstate->ill = "0";
                    break;
            }


            $participantObj->examstate = json_encode($examstate);

            $participantObj->timeresultsentered = time();

            $participantObj->bonus = $fromform->bonus;
           
            $update = $MoodleDBObj->UpdateRecordInDB('exammanagement_participants', $participantObj);
            
            if($update){
				redirect ($ExammanagementInstanceObj->getExammanagementUrl('participantsOverview', $id), null, null, null);
			} else {
				redirect ($ExammanagementInstanceObj->getExammanagementUrl('participantsOverview', $id), 'Speichern fehlgeschlagen', null, notification::NOTIFY_ERROR);
			}


        } else {
            // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
            // or on the first display of the form.

            $mform->set_data(array('id'=>$id));

            //displays the form
            $mform->display();
        }

        $MoodleObj->outputFooter();
    } else { // if user hasnt entered correct password for this session: show enterPasswordPage
        redirect ($ExammanagementInstanceObj->getExammanagementUrl('checkPassword', $ExammanagementInstanceObj->getCm()->id), null, null, null);
    }
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
