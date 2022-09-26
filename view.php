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
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\output\exammanagement_overview;
use mod_exammanagement\output\exammanagement_overview_export_grades;
use mod_exammanagement\output\exammanagement_participantsview;
use mod_exammanagement\ldap\ldapManager;
use stdclass;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

// relevant if called from itself and information is set visible for users or correction is marked as completed
$calledfromformdt = optional_param('calledfromformdt', 0, PARAM_BOOL);
$datetimevisible = optional_param('datetimevisible', 0, PARAM_BOOL);

$calledfromformroom = optional_param('calledfromformroom', 0, PARAM_BOOL);
$roomvisible = optional_param('roomvisible', 0, PARAM_BOOL);

$calledfromformplace = optional_param('calledfromformplace', 0, PARAM_BOOL);
$placevisible = optional_param('placevisible', 0, PARAM_BOOL);

$calledfromformbonus = optional_param('calledfromformbonus', 0, PARAM_BOOL);
$bonusvisible = optional_param('bonusvisible', 0, PARAM_BOOL);

$calledfromformresult = optional_param('calledfromformresult', 0, PARAM_BOOL);
$resultvisible = optional_param('resultvisible', 0, PARAM_BOOL);

$calledfromformcorrection = optional_param('calledfromformcorrection', 0, PARAM_BOOL);
$correctioncompleted = optional_param('correctioncompleted', 0, PARAM_BOOL);

$calledfromformexamreview = optional_param('calledfromformexamreview', 0, PARAM_BOOL);
$examreviewvisible = optional_param('examreviewvisible', 0, PARAM_BOOL);

global $PAGE, $CFG, $USER, $SESSION;

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);

$UserObj = User::getInstance($id, $e, $ExammanagementInstanceObj->getCm()->instance);

$MoodleObj = Moodle::getInstance($id, $e);

$MoodleDBObj = MoodleDB::getInstance();

$LdapManagerObj = ldapManager::getInstance();

if ($MoodleObj->checkCapability('mod/exammanagement:viewinstance')) { // if teacher

    if (!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId) && $SESSION->loggedInExamOrganizationId == $id))) { // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

        $mode = json_decode($ExammanagementInstanceObj->moduleinstance->misc);

        if ($mode) {
            $mode = 'export_grades';
        } else {
            $mode = 'normal';
        }

        if (!$ExammanagementInstanceObj->isExamDataDeleted()) {

            if ($calledfromformdt) { // Set exam date visible.
                require_sesskey();

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
            } else if ($calledfromformroom) { // Set exam room visible.
                require_sesskey();

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
            } else if ($calledfromformplace) {  // Set exam place visible.
                require_sesskey();

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
            } else if ($calledfromformbonus) {  // Set exam bonus visible.
                require_sesskey();

                if ($bonusvisible) {
                    $ExammanagementInstanceObj->moduleinstance->bonusvisible = true;
                } else {
                    $ExammanagementInstanceObj->moduleinstance->bonusvisible = false;
                }

                $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
                if ($update) {
                    $MoodleObj->redirectToOverviewPage('aftercorrection', get_string('operation_successfull', 'mod_exammanagement'), 'success');
                } else {
                    $MoodleObj->redirectToOverviewPage('aftercorrection', get_string('alteration_failed', 'mod_exammanagement'), 'error');
                }
            } else if ($calledfromformresult) {  // Set exam result visible.
                require_sesskey();

                if ($resultvisible) {
                    $ExammanagementInstanceObj->moduleinstance->resultvisible = true;
                } else {
                    $ExammanagementInstanceObj->moduleinstance->resultvisible = false;
                }

                $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
                if ($update) {
                    $MoodleObj->redirectToOverviewPage('aftercorrection', get_string('operation_successfull', 'mod_exammanagement'), 'success');
                } else {
                    $MoodleObj->redirectToOverviewPage('aftercorrection', get_string('alteration_failed', 'mod_exammanagement'), 'error');
                }
            } else if ($calledfromformcorrection) {  // Set correction completed.
                require_sesskey();

                $resultscount = $UserObj->getEnteredResultsCount();

                $bonuscount = $UserObj->getEnteredBonusCount('points'); // If mode is export_grades.

                if (($mode === 'normal' && $resultscount) || $mode = 'export_grades' && $bonuscount) {
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
            } else if ($calledfromformexamreview) { // Set exam rewview visible.
                require_sesskey();

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
        $MoodleObj->outputPageHeader();

        // If instance was moved to new category.
        $oldcategoryid = $ExammanagementInstanceObj->moduleinstance->categoryid;
        $coursecategoryid = $PAGE->category->id; // Set course category.

        if ($oldcategoryid !== $coursecategoryid) {

            // Update category id for instance.
            $ExammanagementInstanceObj->moduleinstance->categoryid = $coursecategoryid;
            $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

            // Opdate  category ids for participants.
            $MoodleDBObj->setFieldInDB('exammanagement_participants', 'categoryid', $coursecategoryid, array('exammanagement' => $ExammanagementInstanceObj->getCm()->instance));
            $MoodleDBObj->setFieldInDB('exammanagement_temp_part', 'categoryid', $coursecategoryid, array('exammanagement' => $ExammanagementInstanceObj->getCm()->instance));

        }

        // Delete temp participants and headers if exist.
        if ($MoodleDBObj->checkIfRecordExists('exammanagement_temp_part', array('exammanagement' => $ExammanagementInstanceObj->getCm()->instance))) {
            $ExammanagementInstanceObj->moduleinstance->tempimportfileheader = null;

            $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

            $MoodleDBObj->DeleteRecordsFromDB('exammanagement_temp_part', array('exammanagement' => $ExammanagementInstanceObj->getCm()->instance));
        }

        // Reset phase information if participants are deleted.
        if (!$ExammanagementInstanceObj->isExamDataDeleted() && !$UserObj->getParticipantsCount()) {
            $ExammanagementInstanceObj->moduleinstance->importfileheaders = null;
            $ExammanagementInstanceObj->moduleinstance->assignmentmode = null;
            $ExammanagementInstanceObj->moduleinstance->datetimevisible = null;
            $ExammanagementInstanceObj->moduleinstance->roomvisible = null;
            $ExammanagementInstanceObj->moduleinstance->placevisible = null;
            $ExammanagementInstanceObj->moduleinstance->datadeletion = null;
            $ExammanagementInstanceObj->moduleinstance->examreviewvisible = null;

            $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
        }

        if ($mode === 'normal') {
            // Rendering and displaying content.

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

            if (get_config('mod_exammanagement', 'enablehelptexts')) {
                $helptexticon = $OUTPUT->help_icon('overview', 'mod_exammanagement', '');
                $additionalressourceslink = get_config('mod_exammanagement', 'additionalressources');
            } else {
                $helptexticon = false;
                $additionalressourceslink = false;
            }

            $examtime = $ExammanagementInstanceObj->getExamtime();
            $taskcount = $ExammanagementInstanceObj->getTaskCount();
            $taskpoints = $ExammanagementInstanceObj->formatNumberForDisplay($ExammanagementInstanceObj->getTaskTotalPoints());
            $textfieldcontent = $ExammanagementInstanceObj->getTextFromTextfield();

            if ($textfieldcontent) {
                if (format_string($textfieldcontent)) {
                    $textfieldcontent = format_string($textfieldcontent);
                } else {
                    $textfieldcontent = get_string('mediacontent', 'mod_exammanagement');
                }
            }

            $participantscount = $UserObj->getParticipantsCount();
            $roomscount = $ExammanagementInstanceObj->getRoomsCount();
            $roomnames = $ExammanagementInstanceObj->getChoosenRoomNames();
            $totalseats = $ExammanagementInstanceObj->getTotalNumberOfSeats();
            $placesassigned = $ExammanagementInstanceObj->placesAssigned();
            $allplacesassigned = $ExammanagementInstanceObj->allPlacesAssigned();
            $assignedplacescount = $ExammanagementInstanceObj->getAssignedPlacesCount();
            $datetimevisible = $ExammanagementInstanceObj->isDateTimeVisible();
            $roomvisible = $ExammanagementInstanceObj->isRoomVisible();
            $placevisible = $ExammanagementInstanceObj->isPlaceVisible();
            $bonuscount = $UserObj->getEnteredBonusCount();
            $bonuspointsentered = $UserObj->getEnteredBonusCount('points');
            $bonusvisible = $ExammanagementInstanceObj->isBonusVisible();
            $gradingscale = $ExammanagementInstanceObj->getGradingscale();
            $resultscount = $UserObj->getEnteredResultsCount();
            $resultvisible = $ExammanagementInstanceObj->isResultVisible();
            $datadeletiondate = $ExammanagementInstanceObj->getDataDeletionDate();
            $examreviewtime = $ExammanagementInstanceObj->getHrExamReviewTime();
            $examreviewroom = $ExammanagementInstanceObj->getExamReviewRoom();
            $examreviewvisible = $ExammanagementInstanceObj->isExamReviewVisible();
            $deleted = $ExammanagementInstanceObj->isExamDataDeleted();

            if ($LdapManagerObj->isLDAPenabled() && $LdapManagerObj->isLDAPconfigured()) {
                $ldapavailable = true;
            } else {
                $ldapavailable = false;
            }

            if ($ExammanagementInstanceObj->getExamReviewTime()) {
                $resultsenteredafterexamreview = $UserObj->getEnteredResultsCount($ExammanagementInstanceObj->getExamReviewTime());
            } else {
                $resultsenteredafterexamreview = false;
            }

            $page = new exammanagement_overview($cmid, $statePhaseOne, $statePhaseTwo, $statePhaseExam, $statePhaseThree, $statePhaseFour, $statePhaseFive, $currentPhaseOne, $currentPhaseTwo, $currentPhaseExam, $currentPhaseThree, $currentPhaseFour, $currentPhaseFive, $helptexticon, $additionalressourceslink, $examtime, $taskcount, $taskpoints, $textfieldcontent, $participantscount, $roomscount, $roomnames, $totalseats, $placesassigned, $allplacesassigned, $assignedplacescount, $datetimevisible, $roomvisible, $placevisible, $bonuscount, $bonuspointsentered, $bonusvisible, $gradingscale, $resultscount, $resultvisible, $datadeletiondate, $examreviewtime, $examreviewroom, $examreviewvisible, $resultsenteredafterexamreview, $deleted, $ldapavailable);
            echo $OUTPUT->render($page);
        } else if ($mode === 'export_grades') {
            // Rendering and displaying content.

            $cmid = $ExammanagementInstanceObj->getCm()->id;

            if (get_config('mod_exammanagement', 'enablehelptexts')) {
                $helptexticon = $OUTPUT->help_icon('export_grades', 'mod_exammanagement', '');
                $additionalressourceslink = get_config('mod_exammanagement', 'additionalressources');
            } else {
                $helptexticon = false;
                $additionalressourceslink = false;
            }

            if ($LdapManagerObj->isLDAPenabled() && $LdapManagerObj->isLDAPconfigured()) {
                $ldapavailable = true;
            } else {
                $ldapavailable = false;
            }

            $participantscount = $UserObj->getParticipantsCount();
            $bonuspointsentered = $UserObj->getEnteredBonusCount('points');
            $gradingscale = $ExammanagementInstanceObj->getGradingscale();
            $resultscount = $UserObj->getEnteredResultsCount();

            $datadeletiondate = $ExammanagementInstanceObj->getDataDeletionDate();
            $deleted = $ExammanagementInstanceObj->isExamDataDeleted();

            $page = new exammanagement_overview_export_grades($cmid, $helptexticon, $additionalressourceslink, $participantscount, $bonuspointsentered, $gradingscale, $resultscount, $datadeletiondate, $deleted, $ldapavailable);
            echo $OUTPUT->render($page);
        }

        $MoodleObj->outputFooter();

    } else { // if user hasnt entered correct password for this session: show enterPasswordPage
        redirect ($ExammanagementInstanceObj->getExammanagementUrl('checkpassword', $ExammanagementInstanceObj->getCm()->id), null, null, null);
    }

} else if ($MoodleObj->checkCapability('mod/exammanagement:viewparticipantspage')) { // student view

    //require_capability('mod/exammanagement:viewparticipantspage', $ExammanagementInstanceObj->getModulecontext());

    $MoodleObj->setPage('view');
    $MoodleObj->outputPageHeader();

    // Exam time.
    $examtime = $ExammanagementInstanceObj->getExamtime();

    if ($ExammanagementInstanceObj->isDateTimeVisible() && $examtime) {
        $date = userdate($examtime, get_string('strftimedatefullshort', 'core_langconfig'));
        $time = userdate($examtime, get_string('strftimetime', 'core_langconfig'));
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

    //bonussteps
    if ($ExammanagementInstanceObj->isBonusVisible() && $participantObj) {
        if ($participantObj->bonussteps === '0') { // allows mustache template to render 0
            $bonussteps = get_string('no_bonus_earned', 'mod_exammanagement');
        } else {
            $bonussteps = $participantObj->bonussteps;
        }
    } else {
        $bonussteps = false;
    }

    // Bonuspoints
    if ($ExammanagementInstanceObj->isBonusVisible() && $participantObj) {
        if ($participantObj->bonuspoints === '0') { // Allows mustache template to render 0.
            $bonuspoints = get_string('no_bonus_earned', 'mod_exammanagement');
        } else {
            $bonuspoints = $ExammanagementInstanceObj->formatNumberForDisplay($participantObj->bonuspoints);
        }
    } else {
        $bonuspoints = false;
    }

    // Totalpoints
    if ($ExammanagementInstanceObj->isResultVisible() && $participantObj) {

        $examstate = $UserObj->getExamState($participantObj);

        if ($examstate === 'normal') {
            $examstate = false;
            $totalpoints = $UserObj->calculatePoints($participantObj);

            $tasktotalpoints = $ExammanagementInstanceObj->formatNumberForDisplay($ExammanagementInstanceObj->getTaskTotalPoints());

            $totalpointswithbonus = $ExammanagementInstanceObj->formatNumberForDisplay($UserObj->calculatePoints($participantObj, true));

            if ($totalpoints === '0') {
                $totalpoints = get_string('no_points_earned', 'mod_exammanagement');
            } else {
                $totalpoints = $ExammanagementInstanceObj->formatNumberForDisplay($totalpoints);
            }
        } else {
            if ($examstate) {
                $examstate = get_string($examstate, 'mod_exammanagement');
            }

            $totalpoints = false;
            $totalpointswithbonus = false;
            $tasktotalpoints = false;
        }

    } else {
        $examstate = false;
        $totalpoints = false;
        $totalpointswithbonus = false;
        $tasktotalpoints = false;
    }

    // Examreview date and room.
    $examreviewtime = false;
    $examreviewroom = false;
    if ($ExammanagementInstanceObj->isExamReviewVisible() && $ExammanagementInstanceObj->getHrExamReviewTime() && $ExammanagementInstanceObj->getExamReviewRoom()) {
        $examreviewtime = $ExammanagementInstanceObj->getHrExamReviewTime();
        $examreviewroom = $ExammanagementInstanceObj->getExamReviewRoom();
    }

    // Check if exam data is deleted.
    $deleted = $ExammanagementInstanceObj->isExamDataDeleted();

    // Rendering and displaying content.

    $page = new exammanagement_participantsview($ExammanagementInstanceObj->getCm()->id, $UserObj->checkIfAlreadyParticipant($USER->id), $date, $time, $room, $place, $textfield, $bonussteps, $bonuspoints, $examstate, $totalpoints, $tasktotalpoints, $totalpointswithbonus, $examreviewtime, $examreviewroom, $deleted);
    echo $OUTPUT->render($page);

    $MoodleObj->outputFooter();

} else {
    redirect($CFG->wwwroot, get_string('nopermissions', 'mod_exammanagement'), null, \core\output\notification::NOTIFY_ERROR);
}

$ExammanagementInstanceObj->startEvent('view');