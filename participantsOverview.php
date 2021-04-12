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
 * @copyright   coactum GmbH 2019
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

$pne  = optional_param('pne', 1, PARAM_INT);
$bpne  = optional_param('bpne', 1, PARAM_INT);

$epm = optional_param('epm', 0, PARAM_INT);

$MoodleDBObj = MoodleDB::getInstance();
$MoodleObj = Moodle::getInstance($id, $e);
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$UserObj = User::getInstance($id, $e, $ExammanagementInstanceObj->getCm()->instance);
$LdapManagerObj = ldapManager::getInstance();

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

    if($ExammanagementInstanceObj->isExamDataDeleted()){
        $MoodleObj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
	} else {
        if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

            if(!$UserObj->getParticipantsCount()){
                $MoodleObj->redirectToOverviewPage('beforeexam', get_string('no_participants_added', 'mod_exammanagement'), 'error');
            }

            $MoodleObj->setPage('participantsOverview');
            $MoodleObj->outputPageHeader();

            //Instantiate Form
            if($epm){
                $mform = new participantsOverviewForm(null, array('id'=>$id, 'e'=>$e, 'epm'=>$epm));
            } else {
                $mform = new participantsOverviewForm(null, array('id'=>$id, 'e'=>$e));
            }

            //Form processing and displaying is done here
            if ($mform->is_cancelled()) {
                //Handle form cancel operation, if cancel button is present on form
                redirect ($ExammanagementInstanceObj->getExammanagementUrl('participantsOverview', $ExammanagementInstanceObj->getCm()->id), get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

            } else if ($fromform = $mform->get_data()) {
                //In this case you process validated data. $mform->get_data() returns data posted in form.

                var_dump($fromform->bonuspoints);

                $participants = $UserObj->getExamParticipants(array('mode'=>'all'), array());
                $updatedCount = 0;

                foreach($participants as $participant){
                    if($ExammanagementInstanceObj->moduleinstance->misc === NULL){
                        if(isset($fromform->state[$participant->id]) && $fromform->state[$participant->id] !== 'not_set'){
                            switch ($fromform->state[$participant->id]){
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
                                    break;
                            }

                            $participant->examstate = json_encode($examstate);

                            if($fromform->points[$participant->id]){ // if participants points were not empty
                                $participant->exampoints = json_encode($fromform->points[$participant->id]);
                            }

                        } else {
                            $participant->examstate = NULL;
                            $participant->exampoints = NULL;
                        }

                        $participant->timeresultsentered = time();

                        if($fromform->bonussteps[$participant->id] !== '-'){
                            $participant->bonussteps = $fromform->bonussteps[$participant->id];
                            $participant->bonuspoints = NULL;
                        } else if($fromform->bonuspoints[$participant->id] !== '-' && $fromform->bonuspoints[$participant->id] !== 0){
                            $participant->bonussteps = NULL;
                            $participant->bonuspoints = $fromform->bonuspoints[$participant->id];
                        } else {
                            $participant->bonussteps = NULL;
                            $participant->bonuspoints = NULL;
                        }

                    } else {
                        if($fromform->bonuspoints[$participant->id] !== '-'){
                            $participant->timeresultsentered = time();
                            $participant->bonuspoints = $fromform->bonuspoints[$participant->id];
                        }
                    }

                    if($MoodleDBObj->UpdateRecordInDB('exammanagement_participants', $participant)){
                        $updatedCount += 1;
                    }

                }

                var_dump($participants);

                var_dump($updatedCount);

                if($updatedCount){
                    $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
                    redirect ($ExammanagementInstanceObj->getExammanagementUrl('participantsOverview', $id), get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
                } else {
                    redirect ($ExammanagementInstanceObj->getExammanagementUrl('participantsOverview', $id), get_string('alteration_failed', 'mod_exammanagement'), null, notification::NOTIFY_ERROR);
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
    }
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}