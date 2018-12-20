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
 * Prints an instance of mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 namespace mod_exammanagement\general;

use mod_exammanagement\output\exammanagement_overview;
use mod_exammanagement\output\exammanagement_participantsview;

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

global $PAGE, $CFG, $USER;

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);

$UserObj = User::getInstance($id, $e, $ExammanagementInstanceObj->moduleinstance->categoryid);

$MoodleObj = Moodle::getInstance($id, $e);

$MoodleDBObj = MoodleDB::getInstance();

if ($MoodleObj->checkCapability('mod/exammanagement:viewinstance')) { // if teacher

    if ($calledfromformdt) { // saveDateTime

        if ($datetimevisible) {
            $ExammanagementInstanceObj->moduleinstance->datetimevisible = true;
        } else {
            $ExammanagementInstanceObj->moduleinstance->datetimevisible = false;
        }

        $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
        if ($update) {
            $MoodleObj->redirectToOverviewPage('forexam', 'Informationen sichtbar geschaltet', 'success');
        } else {
            $MoodleObj->redirectToOverviewPage('forexam', 'Informationen konnten nicht sichtbar geschaltet werden', 'error');
        }
    } elseif ($calledfromformroom) { // saveRoom

        if ($roomvisible) {
            $ExammanagementInstanceObj->moduleinstance->roomvisible = true;
        } else {
            $ExammanagementInstanceObj->moduleinstance->roomvisible = false;
        }

        $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
        if ($update) {
            $MoodleObj->redirectToOverviewPage('forexam', 'Informationen sichtbar geschaltet', 'success');
        } else {
            $MoodleObj->redirectToOverviewPage('forexam', 'Informationen konnten nicht sichtbar geschaltet werden', 'error');
        }
    } elseif ($calledfromformplace) { // savePlace

        if ($placevisible) {
            $ExammanagementInstanceObj->moduleinstance->placevisible = true;
        } else {
            $ExammanagementInstanceObj->moduleinstance->placevisible = false;
        }

        $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
        if ($update) {
            $MoodleObj->redirectToOverviewPage('forexam', 'Informationen sichtbar geschaltet', 'success');
        } else {
            $MoodleObj->redirectToOverviewPage('forexam', 'Informationen konnten nicht sichtbar geschaltet werden', 'error');
        }
    } elseif ($calledfromformcorrection) { // save correction as completed

        if ($correctioncompleted) {
            $ExammanagementInstanceObj->moduleinstance->correctioncompletiondate = time();
        } else {
            $ExammanagementInstanceObj->moduleinstance->correctioncompletiondate = null;
        }

        $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
        if ($update) {
            $MoodleObj->redirectToOverviewPage('forexam', 'Korrektur abgeschlossen', 'success');
        } else {
            $MoodleObj->redirectToOverviewPage('forexam', 'Korrektur konnte nicht abgeschlossen werden', 'error');
        }
    } elseif ($calledfromformexamreview) { // save exam review

        if ($examreviewvisible) {
            $ExammanagementInstanceObj->moduleinstance->examreviewvisible = true;
        } else {
            $ExammanagementInstanceObj->moduleinstance->examreviewvisible = false;
        }

        $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
        if ($update) {
            $MoodleObj->redirectToOverviewPage('forexam', 'Informationen zur Klausureinsicht freigeschaltet.', 'success');
        } else {
            $MoodleObj->redirectToOverviewPage('forexam', 'Informationen zur Klausureinsicht konnten nicht freigeschaltet werden', 'error');
        }
    }

    $MoodleObj->setPage('view');
    $MoodleObj-> outputPageHeader();

    // if plugin instance was moved to new category:

    $oldcategoryid = $ExammanagementInstanceObj->moduleinstance->categoryid;
    $coursecategoryid = substr(strtolower(preg_replace("/[^0-9a-zA-Z]/", "", $PAGE->category->name)), 0, 6); //set course category

    if ($oldcategoryid !== $coursecategoryid) {

    // update categoryid
        $ExammanagementInstanceObj->moduleinstance->categoryid = $coursecategoryid;
        $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

        // update participants categoryids
        $MoodleDBObj->setFieldInDB('exammanagement_participants', 'categoryid', $coursecategoryid, array('plugininstanceid' => $id, 'cartegoryid' => $oldcategoryid));
        $MoodleDBObj->setFieldInDB('exammanagement_temp_part', 'categoryid', $coursecategoryid, array('plugininstanceid' => $id, 'cartegoryid' => $oldcategoryid));        

        if ($update) {
            $MoodleObj->redirectToOverviewPage('', 'Kurs wurde manuell in ein anderes Semester verschoben. Alle Teilnehmerdaten wurden entsprechend angepasst.', 'success');
        } else {
            $MoodleObj->redirectToOverviewPage('', 'Kurs wurde manuell in ein anderes Semester verschoben. Sollten bereits eingetragene Teilnehmer nicht mehr angezeigt werden müssen diese ggf. erneut eingetragen werden.', 'warning');
        }
    }

    // delete temp participants and headers if exist

    $tempparticipants = $MoodleDBObj->getRecordsFromDB('exammanagement_temp_part', array('plugininstanceid' => $id));

    $ExammanagementInstanceObj->moduleinstance->tempimportfileheader = null;

    $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

    if ($tempparticipants && $MoodleDBObj->checkIfRecordExists('exammanagement_temp_part', array('plugininstanceid' => $id))) {
        $MoodleDBObj->DeleteRecordsFromDB('exammanagement_temp_part', array('plugininstanceid' => $id));
    }

    // reset phase information if participants are deleted
    if (!$UserObj->getParticipantsCount()) {
        $ExammanagementInstanceObj->moduleinstance->importfileheader = null;
        $ExammanagementInstanceObj->moduleinstance->stateofplaces = null;
        $ExammanagementInstanceObj->moduleinstance->datetimevisible = null;
        $ExammanagementInstanceObj->moduleinstance->roomvisible = null;
        $ExammanagementInstanceObj->moduleinstance->placevisible = null;
        $ExammanagementInstanceObj->moduleinstance->correctioncompletiondate = null;
  
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

    $examtime = $ExammanagementInstanceObj->getHrExamtimeTemplate();
    $taskcount = $ExammanagementInstanceObj->getTaskCount();
    $taskpoints = str_replace( '.', ',', $ExammanagementInstanceObj->getTaskTotalPoints());
    $textfieldcontent = $ExammanagementInstanceObj->getShortenedTextfield();
    $participantscount = $UserObj->getParticipantsCount();
    $roomscount = $ExammanagementInstanceObj->getRoomsCount();
    $roomnames = $ExammanagementInstanceObj->getChoosenRoomNames();
    $stateofplaces = $ExammanagementInstanceObj->isStateOfPlacesCorrect();
    $stateofplaceserror = $ExammanagementInstanceObj->isStateOfPlacesError();
    $datetimevisible = $ExammanagementInstanceObj->isDateTimeVisible();
    $roomvisible = $ExammanagementInstanceObj->isRoomVisible();
    $placevisible = $ExammanagementInstanceObj->isPlaceVisible();
    $bonuscount = $UserObj->getEnteredBonusCount();
    $gradingscale = $ExammanagementInstanceObj->getGradingscale();
    $resultscount = $ExammanagementInstanceObj->getInputResultsCount();
    $datadeletiondate = $ExammanagementInstanceObj->getDataDeletionDate();
    $examreviewtime = $ExammanagementInstanceObj->getHrExamReviewTime();
    $examreviewroom = $ExammanagementInstanceObj->getExamReviewRoom();
    $examreviewvisible = $ExammanagementInstanceObj->isExamReviewVisible();

    $resultsenteredafterexamreview = $UserObj->getAllParticipantsWithResultsAfterExamReview();
    if ($resultsenteredafterexamreview) {
        $resultsenteredafterexamreview = count($resultsenteredafterexamreview);
    }

    $page = new exammanagement_overview($cmid, $statePhaseOne, $statePhaseTwo, $statePhaseExam, $statePhaseThree, $statePhaseFour, $statePhaseFive, $currentPhaseOne, $currentPhaseTwo, $currentPhaseExam, $currentPhaseThree, $currentPhaseFour, $currentPhaseFive, $examtime, $taskcount, $taskpoints, $textfieldcontent, $participantscount, $roomscount, $roomnames, $stateofplaces, $stateofplaceserror, $datetimevisible, $roomvisible, $placevisible, $bonuscount, $gradingscale, $resultscount, $datadeletiondate, $examreviewtime, $examreviewroom, $examreviewvisible, $resultsenteredafterexamreview);
    echo $output->render($page);

    //$this->debugElementsOverview();

    $MoodleObj->outputFooter();
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
    $participantObj = $UserObj->getParticipantObj();

    if ($ExammanagementInstanceObj->isRoomVisible() && $participantObj->roomname) {
        $room = $participantObj->roomname;
    } else {
        $room = false;
    }

    if ($ExammanagementInstanceObj->isPlaceVisible() && $participantObj->place) {
        $place = $participantObj->place;
    } else {
        $place = false;
    }

    //textfield
    $textfield = $ExammanagementInstanceObj->getTextFromTextfield();

    //bonus
    $bonus = $participantObj->bonus;

    //examreview date and room
    $examreviewtime = false;
    $examreviewroom = false;
    if ($ExammanagementInstanceObj->isExamReviewVisible() && $ExammanagementInstanceObj->getHrExamReviewTime() && $ExammanagementInstanceObj->getExamReviewRoom()) {
        $examreviewtime = $ExammanagementInstanceObj->getHrExamReviewTime();
        $examreviewroom = $ExammanagementInstanceObj->getExamReviewRoom();
    }

    //rendering and displaying content
    $output = $PAGE->get_renderer('mod_exammanagement');

    $page = new exammanagement_participantsview($ExammanagementInstanceObj->getCm()->id, $UserObj->checkIfAlreadyParticipant($USER->id), $date, $time, $room, $place, $textfield, $bonus, $examreviewtime, $examreviewroom);
    echo $output->render($page);

    $MoodleObj->outputFooter();
} else {
    redirect($CFG->wwwroot, 'Sie haben keine gültigen Rechte.', null, \core\output\notification::NOTIFY_ERROR);
}

$ExammanagementInstanceObj->startEvent('view');

//for testing
// global $SESSION;
//
// var_dump($SESSION);
//
// set_user_preference('helptexts','10011001');
// var_dump(get_user_preferences());