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
 * Prints main page of an instance of mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\output\exammanagement_overview;
use mod_exammanagement\output\exammanagement_participantsview;
use stdclass;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

// relevant if called from itself and information is set visible for users or correction is marked as completed
$calledfromformdt = optional_param('calledfromformdt', 0, PARAM_RAW);
$datetimevisible = optional_param('datetimevisible', 0, PARAM_RAW);

$calledfromformroom = optional_param('calledfromformroom', 0, PARAM_RAW);
$roomvisible = optional_param('roomvisible', 0, PARAM_RAW);

$calledfromformplace = optional_param('calledfromformplace', 0, PARAM_RAW);
$placevisible = optional_param('placevisible', 0, PARAM_RAW);

$calledfromformcorrection = optional_param('calledfromformcorrection', 0, PARAM_RAW);
$correctioncompleted = optional_param('correctioncompleted', 0, PARAM_RAW);

$calledfromformexamreview = optional_param('calledfromformexamreview', 0, PARAM_RAW);
$examreviewvisible = optional_param('examreviewvisible', 0, PARAM_RAW);

global $PAGE, $CFG, $USER, $SESSION;

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);

$UserObj = User::getInstance($id, $e, $ExammanagementInstanceObj->getCm()->instance);

$MoodleObj = Moodle::getInstance($id, $e);

$MoodleDBObj = MoodleDB::getInstance();

if ($MoodleObj->checkCapability('mod/exammanagement:viewinstance')) { // if teacher

    if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId) && $SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

        if(!$ExammanagementInstanceObj->isExamDataDeleted()){

            if ($calledfromformdt) { // saveDateTimeVisible

                if ($datetimevisible) {
                    $ExammanagementInstanceObj->moduleinstance->datetimevisible = true;
                } else {
                    $ExammanagementInstanceObj->moduleinstance->datetimevisible = false;
                }

                $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
                if ($update) {
                    $MoodleObj->redirectToOverviewPage('forexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');
                } else {
                    $MoodleObj->redirectToOverviewPage('forexam', get_string('alteration_failed', 'mod_exammanagement'), 'error');
                }
            } elseif ($calledfromformroom) { // saveRoomVisible

                if ($roomvisible) {
                    $ExammanagementInstanceObj->moduleinstance->roomvisible = true;
                } else {
                    $ExammanagementInstanceObj->moduleinstance->roomvisible = false;
                }

                $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
                if ($update) {
                    $MoodleObj->redirectToOverviewPage('forexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');
                } else {
                    $MoodleObj->redirectToOverviewPage('forexam', get_string('alteration_failed', 'mod_exammanagement'), 'error');
                }
            } elseif ($calledfromformplace) { // savePlaceVisible

                if ($placevisible) {
                    $ExammanagementInstanceObj->moduleinstance->placevisible = true;
                } else {
                    $ExammanagementInstanceObj->moduleinstance->placevisible = false;
                }

                $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
                if ($update) {
                    $MoodleObj->redirectToOverviewPage('forexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');
                } else {
                    $MoodleObj->redirectToOverviewPage('forexam', get_string('alteration_failed', 'mod_exammanagement'), 'error');
                }
            } elseif ($calledfromformcorrection) { // save correction as completed

                $resultscount = $UserObj->getEnteredResultsCount();

                if($resultscount){
                    if ($correctioncompleted) {
                        $ExammanagementInstanceObj->moduleinstance->datadeletion = strtotime("+3 months", time());
                    } else {
                        $ExammanagementInstanceObj->moduleinstance->datadeletion = null;
                        $ExammanagementInstanceObj->moduleinstance->deletionwarningmailids = null;
                    }
                } else {
                    $MoodleObj->redirectToOverviewPage('afterexam', get_string('no_results_entered', 'mod_exammanagement'), 'error');
                }

                $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
                if ($update) {
                    $MoodleObj->redirectToOverviewPage('forexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');
                } else {
                    $MoodleObj->redirectToOverviewPage('forexam', get_string('alteration_failed', 'mod_exammanagement'), 'error');
                }
            } elseif ($calledfromformexamreview) { // save exam review date time Visible

                if ($examreviewvisible) {
                    $ExammanagementInstanceObj->moduleinstance->examreviewvisible = true;
                } else {
                    $ExammanagementInstanceObj->moduleinstance->examreviewvisible = false;
                }

                $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
                if ($update) {
                    $MoodleObj->redirectToOverviewPage('forexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');
                } else {
                    $MoodleObj->redirectToOverviewPage('forexam', get_string('alteration_failed', 'mod_exammanagement'), 'error');
                }
            }
        }

        $MoodleObj->setPage('view');
        $MoodleObj-> outputPageHeader();

        // if plugin instance was moved to new category:

        $oldcategoryid = $ExammanagementInstanceObj->moduleinstance->categoryid;
        $coursecategoryid = $PAGE->category->id; //set course category

        if ($oldcategoryid !== $coursecategoryid) {

            // update categoryid
            $ExammanagementInstanceObj->moduleinstance->categoryid = $coursecategoryid;
            $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

            // update participants categoryids
            $MoodleDBObj->setFieldInDB('exammanagement_participants', 'categoryid', $coursecategoryid, array('exammanagement' => $id));
            $MoodleDBObj->setFieldInDB('exammanagement_temp_part', 'categoryid', $coursecategoryid, array('exammanagement' => $id));

        }

        // delete temp participants and headers if exist

        if ($MoodleDBObj->checkIfRecordExists('exammanagement_temp_part', array('exammanagement' => $id))) {
            $ExammanagementInstanceObj->moduleinstance->tempimportfileheader = null;

            $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

            $MoodleDBObj->DeleteRecordsFromDB('exammanagement_temp_part', array('exammanagement' => $id));
        }

        // reset phase information if participants are deleted
        if (!$ExammanagementInstanceObj->isExamDataDeleted() && !$UserObj->getParticipantsCount()) {
            $ExammanagementInstanceObj->moduleinstance->importfileheaders = null;
            $ExammanagementInstanceObj->moduleinstance->datetimevisible = null;
            $ExammanagementInstanceObj->moduleinstance->roomvisible = null;
            $ExammanagementInstanceObj->moduleinstance->placevisible = null;
            $ExammanagementInstanceObj->moduleinstance->datadeletion = null;
            $ExammanagementInstanceObj->moduleinstance->examreviewvisible = null;

            $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
        }

        //rendering and displaying content
        $output = $PAGE->get_renderer('mod_exammanagement');

        $cmid = $ExammanagementInstanceObj->getCm()->id;
        $statePhaseOne = $ExammanagementInstanceObj->checkPhaseCompletion(1);
        $statePhaseTwo = $ExammanagementInstanceObj->checkPhaseCompletion(2);
        $statePhaseExam = $ExammanagementInstanceObj->checkPhaseCompletion("Exam");
        $statePhaseThree = $ExammanagementInstanceObj->checkPhaseCompletion(3);
        $statePhaseFour = $ExammanagementInstanceObj->checkPhaseCompletion(4);
        $statePhaseFive = $ExammanagementInstanceObj->checkPhaseCompletion(5);

        $currentPhaseOne = false;
        $currentPhaseTwo = false;
        $currentPhaseExam = false;
        $currentPhaseThree = false;
        $currentPhaseFour = false;
        $currentPhaseFive = false;

        $currentPhase = $ExammanagementInstanceObj->determineCurrentPhase();
        switch ($currentPhase) {
        case '1':
            $currentPhaseOne = true;
            break;
        case '2':
            $currentPhaseTwo = true;
            break;
        case 'exam':
            $currentPhaseExam = true;
            break;
        case '3':
            $currentPhaseThree = true;
            break;
        case '4':
            $currentPhaseFour = true;
            break;
        case '5':
            $currentPhaseFive = true;
            break;
        default:
            break;
        }

        if(get_config('mod_exammanagement', 'enablehelptexts')){
            $helptexticon = $OUTPUT->help_icon('overview', 'mod_exammanagement', '');
            $additionalressourceslink = get_config('mod_exammanagement', 'additionalressources');
        } else {
            $helptexticon = false;
            $additionalressourceslink = false;
        }

        $examtime = $ExammanagementInstanceObj->getHrExamtimeTemplate();
        $taskcount = $ExammanagementInstanceObj->getTaskCount();
        $taskpoints = str_replace( '.', ',', $ExammanagementInstanceObj->getTaskTotalPoints());
        $textfieldcontent = format_string($ExammanagementInstanceObj->getTextFromTextfield());
        $participantscount = $UserObj->getParticipantsCount();
        $roomscount = $ExammanagementInstanceObj->getRoomsCount();
        $roomnames = $ExammanagementInstanceObj->getChoosenRoomNames();
        $totalseats = $ExammanagementInstanceObj->getTotalNumberOfSeats();
        $allplacesassigned = $ExammanagementInstanceObj->allPlacesAssigned();
        $assignedplacescount = $ExammanagementInstanceObj->getAssignedPlacesCount();
        $datetimevisible = $ExammanagementInstanceObj->isDateTimeVisible();
        $roomvisible = $ExammanagementInstanceObj->isRoomVisible();
        $placevisible = $ExammanagementInstanceObj->isPlaceVisible();
        $bonuscount = $UserObj->getEnteredBonusCount();
        $gradingscale = $ExammanagementInstanceObj->getGradingscale();
        $resultscount = $UserObj->getEnteredResultsCount();
        $datadeletiondate = $ExammanagementInstanceObj->getDataDeletionDate();
        $examreviewtime = $ExammanagementInstanceObj->getHrExamReviewTime();
        $examreviewroom = $ExammanagementInstanceObj->getExamReviewRoom();
        $examreviewvisible = $ExammanagementInstanceObj->isExamReviewVisible();
        $deleted = $ExammanagementInstanceObj->isExamDataDeleted();

        if($ExammanagementInstanceObj->getExamReviewTime()){
            $resultsenteredafterexamreview = $UserObj->getEnteredResultsCount($ExammanagementInstanceObj->getExamReviewTime());
        } else {
            $resultsenteredafterexamreview = false;
        }

        $page = new exammanagement_overview($cmid, $statePhaseOne, $statePhaseTwo, $statePhaseExam, $statePhaseThree, $statePhaseFour, $statePhaseFive, $currentPhaseOne, $currentPhaseTwo, $currentPhaseExam, $currentPhaseThree, $currentPhaseFour, $currentPhaseFive, $helptexticon, $additionalressourceslink, $examtime, $taskcount, $taskpoints, $textfieldcontent, $participantscount, $roomscount, $roomnames, $totalseats, $allplacesassigned, $assignedplacescount, $datetimevisible, $roomvisible, $placevisible, $bonuscount, $gradingscale, $resultscount, $datadeletiondate, $examreviewtime, $examreviewroom, $examreviewvisible, $resultsenteredafterexamreview, $deleted);
        echo $output->render($page);

        $MoodleObj->outputFooter();

    } else { // if user hasnt entered correct password for this session: show enterPasswordPage
        redirect ($ExammanagementInstanceObj->getExammanagementUrl('checkPassword', $ExammanagementInstanceObj->getCm()->id), null, null, null);
    }

} elseif ($MoodleObj->checkCapability('mod/exammanagement:viewparticipantspage')) { // student view

    //require_capability('mod/exammanagement:viewparticipantspage', $ExammanagementInstanceObj->getModulecontext());

    $MoodleObj->setPage('view');
    $MoodleObj-> outputPageHeader();

    //examtime
    $examtime = $ExammanagementInstanceObj->getExamtime();

    if ($ExammanagementInstanceObj->isDateTimeVisible() && $examtime) {
        $date = date('d.m.Y', $examtime);
        $time = date('H:i', $examtime);
    } else {
        $date = false;
        $time = false;
    }

    //room and place
    global $USER;

    $MoodleDBObj = MoodleDB::getInstance();

    $participantObj = $UserObj->getExamParticipantObj($USER->id);

    if ($ExammanagementInstanceObj->isRoomVisible() && $participantObj && $participantObj->roomname) {
        $room = $participantObj->roomname;
    } else {
        $room = false;
    }

    if ($ExammanagementInstanceObj->isPlaceVisible() && $participantObj && $participantObj->place) {
        $place = $participantObj->place;
    } else {
        $place = false;
    }

    //textfield
    $textfield = $ExammanagementInstanceObj->getTextFromTextfield();

    //bonus
    if($participantObj){
        if($participantObj->bonus === '0'){ // allows mustache template to render 0
            $bonus = get_string('no_bonus_earned', 'mod_exammanagement');
        } else {
            $bonus = $participantObj->bonus;
        }
    } else {
        $bonus = false;
    }

    //examreview date and room
    $examreviewtime = false;
    $examreviewroom = false;
    if ($ExammanagementInstanceObj->isExamReviewVisible() && $ExammanagementInstanceObj->getHrExamReviewTime() && $ExammanagementInstanceObj->getExamReviewRoom()) {
        $examreviewtime = $ExammanagementInstanceObj->getHrExamReviewTime();
        $examreviewroom = $ExammanagementInstanceObj->getExamReviewRoom();
    }

    //check if exam data is deleted
    $deleted = $ExammanagementInstanceObj->isExamDataDeleted();

    //rendering and displaying content
    $output = $PAGE->get_renderer('mod_exammanagement');

    $page = new exammanagement_participantsview($ExammanagementInstanceObj->getCm()->id, $UserObj->checkIfAlreadyParticipant($USER->id), $date, $time, $room, $place, $textfield, $bonus, $examreviewtime, $examreviewroom, $deleted);
    echo $output->render($page);

    $MoodleObj->outputFooter();

} else {
    redirect($CFG->wwwroot, get_string('nopermissions', 'mod_exammanagement'), null, \core\output\notification::NOTIFY_ERROR);
}

$ExammanagementInstanceObj->startEvent('view');

// if ($rs = $MoodleDBObj->getRecordset('exammanagement_participants', array())) {

//     if($rs->valid()){

//         foreach ($rs as $record) {

//             $cm = get_coursemodule_from_id('exammanagement', $record->plugininstanceid, 0, false, MUST_EXIST);

//             $exammanagement = $MoodleDBObj->getRecordFromDB('exammanagement', array('id' => $cm->instance));

//             $record->exammanagement = '0';

//             $MoodleDBObj->UpdateRecordInDB("exammanagement_participants", $record);

//         }

//         $rs->close();

//     }

// }

// $event = \mod_exammanagement\event\log_variable::create(['other' => 'export_user_data:' .  'exammanagements' . json_encode($exammanagements)]);
// $event->trigger();