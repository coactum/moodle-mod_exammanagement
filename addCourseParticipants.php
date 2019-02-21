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
 * Prints participants form for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\addCourseParticipantsForm;
use stdclass;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$MoodleObj = Moodle::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$UserObj = User::getInstance($id, $e);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

	if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && $SESSION->loggedInExamOrganizationId == $id)){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

        $MoodleObj->setPage('addCourseParticipants');
        $MoodleObj->outputPageHeader();
            
        //Instantiate form
        $mform = new addCourseParticipantsForm(null, array('id'=>$id, 'e'=>$e));

        //Form processing and displaying is done here
        if ($mform->is_cancelled()) {
            //Handle form cancel operation, if cancel button is present on form
            redirect ($ExammanagementInstanceObj->getExammanagementUrl('showParticipants', $ExammanagementInstanceObj->getCm()->id), get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

        } else if ($fromform = $mform->get_data()) {
            //In this case you process validated data. $mform->get_data() returns data posted in form.

            $participantsIdsArr = $UserObj->filterCheckedParticipants($fromform);
            $deletedParticipantsIdsArr = $UserObj->filterCheckedDeletedParticipants($fromform);

            if($participantsIdsArr != false || $deletedParticipantsIdsArr != false){

                $insert;
                $userObjArr = array();

                if($participantsIdsArr){
                    foreach($participantsIdsArr as $participantId){

                        if($UserObj->checkIfAlreadyParticipant($participantId) == false){
                            $user = new stdClass();
                            $user->plugininstanceid = $id;
                            $user->courseid = $ExammanagementInstanceObj->getCourse()->id;
                            $user->categoryid = $ExammanagementInstanceObj->moduleinstance->categoryid;
                            $user->moodleuserid = $participantId;
                            $user->headerid = 0;

                            array_push($userObjArr, $user);

                        }
                    }
                }

                if($deletedParticipantsIdsArr){
                    foreach($deletedParticipantsIdsArr as $identifier){
                            $temp = explode('_', $identifier);

                            if($temp[0]== 'mid'){
                                $UserObj->deleteParticipant($temp[1], false);
                            } else {

                                if($temp[1] && $temp[2]){ //for testing

                                    $UserObj->deleteParticipant(false, $temp[1].'_'.$temp[2].'_'.$temp[3]);
                                } else {
                                    $UserObj->deleteParticipant(false, $temp[1]);
                                }
                            }
                    }
                }

                // reset state of places assignment if already set
                if($ExammanagementInstanceObj->isStateOfPlacesCorrect()){
                    $ExammanagementInstanceObj->moduleinstance->stateofplaces = 'error';
                    $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
                }

                $MoodleDBObj->InsertBulkRecordsInDB('exammanagement_participants', $userObjArr);

                $MoodleObj->redirectToOverviewPage('beforeexam', 'Kursteilnehmer wurden zur Prüfung hinzugefügt.', 'success');

            } else {
                $MoodleObj->redirectToOverviewPage('beforeexam', 'Kursteilnehmer konnten nicht zur Prüfung hinzugefügt werden', 'error');
            }

        } else {
            // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
            // or on the first display of the form.

            //Set default data (if any)
            //$mform->set_data(array('participants'=>$this->getCourseParticipantsIDs(), 'id'=>$this->id));
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
