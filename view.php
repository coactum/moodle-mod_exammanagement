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

$togglephase = optional_param('togglephase', 0, PARAM_BOOL);
$phase = optional_param('phase', 0, PARAM_TEXT);

global $PAGE, $CFG, $USER, $SESSION;

$exammanagementinstanceobj = exammanagementInstance::getInstance($id, $e);

$userobj = User::getInstance($id, $e, $exammanagementinstanceobj->getCm()->instance);

$moodleobj = Moodle::getInstance($id, $e);

$moodledbobj = MoodleDB::getInstance();

$ldapmanagerobj = ldapManager::getInstance();

if ($moodleobj->checkCapability('mod/exammanagement:viewinstance')) { // if teacher

    if (!isset($exammanagementinstanceobj->moduleinstance->password) || (isset($exammanagementinstanceobj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId) && $SESSION->loggedInExamOrganizationId == $id))) { // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

        $misc = (array) json_decode($exammanagementinstanceobj->moduleinstance->misc);

        if (isset($misc['mode']) && $misc['mode'] == 'export_grades') {
            $mode = 'export_grades';
        } else {
            $mode = 'normal';
        }

        if ($togglephase) { // Set exam date visible.
            require_sesskey();

            if ($phase && $phase !== 0) {
                $phasestate = get_user_preferences('exammanagement_' . $phase);

                if (!isset($phasestate)) {
                    $activephase = $exammanagementinstanceobj->determineactivePhase();

                    if ($activephase == $phase) {
                        set_user_preference('exammanagement_' . $phase, false);
                    } else {
                        set_user_preference('exammanagement_' . $phase, true);
                    }

                } else {
                    set_user_preference('exammanagement_' . $phase, !$phasestate);
                }

            }
        }

        if (!$exammanagementinstanceobj->isExamDataDeleted()) {

            if ($calledfromformdt) { // Set exam date visible.
                require_sesskey();

                if ($datetimevisible) {
                    $exammanagementinstanceobj->moduleinstance->datetimevisible = true;
                } else {
                    $exammanagementinstanceobj->moduleinstance->datetimevisible = false;
                }

                $update = $moodledbobj->UpdateRecordInDB("exammanagement", $exammanagementinstanceobj->moduleinstance);
                if ($update) {
                    $moodleobj->redirectToOverviewPage('forexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');
                } else {
                    $moodleobj->redirectToOverviewPage('forexam', get_string('alteration_failed', 'mod_exammanagement'), 'error');
                }
            } else if ($calledfromformroom) { // Set exam room visible.
                require_sesskey();

                if ($roomvisible) {
                    $exammanagementinstanceobj->moduleinstance->roomvisible = true;
                } else {
                    $exammanagementinstanceobj->moduleinstance->roomvisible = false;
                }

                $update = $moodledbobj->UpdateRecordInDB("exammanagement", $exammanagementinstanceobj->moduleinstance);
                if ($update) {
                    $moodleobj->redirectToOverviewPage('forexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');
                } else {
                    $moodleobj->redirectToOverviewPage('forexam', get_string('alteration_failed', 'mod_exammanagement'), 'error');
                }
            } else if ($calledfromformplace) {  // Set exam place visible.
                require_sesskey();

                if ($placevisible) {
                    $exammanagementinstanceobj->moduleinstance->placevisible = true;
                } else {
                    $exammanagementinstanceobj->moduleinstance->placevisible = false;
                }

                $update = $moodledbobj->UpdateRecordInDB("exammanagement", $exammanagementinstanceobj->moduleinstance);
                if ($update) {
                    $moodleobj->redirectToOverviewPage('forexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');
                } else {
                    $moodleobj->redirectToOverviewPage('forexam', get_string('alteration_failed', 'mod_exammanagement'), 'error');
                }
            } else if ($calledfromformbonus) {  // Set exam bonus visible.
                require_sesskey();

                if ($bonusvisible) {
                    $exammanagementinstanceobj->moduleinstance->bonusvisible = true;
                } else {
                    $exammanagementinstanceobj->moduleinstance->bonusvisible = false;
                }

                $update = $moodledbobj->UpdateRecordInDB("exammanagement", $exammanagementinstanceobj->moduleinstance);
                if ($update) {
                    $moodleobj->redirectToOverviewPage('aftercorrection', get_string('operation_successfull', 'mod_exammanagement'), 'success');
                } else {
                    $moodleobj->redirectToOverviewPage('aftercorrection', get_string('alteration_failed', 'mod_exammanagement'), 'error');
                }
            } else if ($calledfromformresult) {  // Set exam result visible.
                require_sesskey();

                if ($resultvisible) {
                    $exammanagementinstanceobj->moduleinstance->resultvisible = true;
                } else {
                    $exammanagementinstanceobj->moduleinstance->resultvisible = false;
                }

                $update = $moodledbobj->UpdateRecordInDB("exammanagement", $exammanagementinstanceobj->moduleinstance);
                if ($update) {
                    $moodleobj->redirectToOverviewPage('aftercorrection', get_string('operation_successfull', 'mod_exammanagement'), 'success');
                } else {
                    $moodleobj->redirectToOverviewPage('aftercorrection', get_string('alteration_failed', 'mod_exammanagement'), 'error');
                }
            } else if ($calledfromformcorrection) {  // Set correction completed.
                require_sesskey();

                $resultscount = $userobj->getEnteredResultsCount();

                $bonuscount = $userobj->getEnteredBonusCount('points'); // If mode is export_grades.

                if (($mode === 'normal' && $resultscount) || $mode = 'export_grades' && $bonuscount) {
                    if ($correctioncompleted) {
                        $exammanagementinstanceobj->moduleinstance->datadeletion = strtotime("+3 months", time());
                    } else {
                        $exammanagementinstanceobj->moduleinstance->datadeletion = null;
                        $exammanagementinstanceobj->moduleinstance->deletionwarningmailids = null;
                    }
                } else {
                    $moodleobj->redirectToOverviewPage('afterexam', get_string('no_results_entered', 'mod_exammanagement'), 'error');
                }

                $update = $moodledbobj->UpdateRecordInDB("exammanagement", $exammanagementinstanceobj->moduleinstance);
                if ($update) {
                    $moodleobj->redirectToOverviewPage('afterexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');
                } else {
                    $moodleobj->redirectToOverviewPage('afterexam', get_string('alteration_failed', 'mod_exammanagement'), 'error');
                }
            } else if ($calledfromformexamreview) { // Set exam rewview visible.
                require_sesskey();

                if ($examreviewvisible) {
                    $exammanagementinstanceobj->moduleinstance->examreviewvisible = true;
                } else {
                    $exammanagementinstanceobj->moduleinstance->examreviewvisible = false;
                }

                $update = $moodledbobj->UpdateRecordInDB("exammanagement", $exammanagementinstanceobj->moduleinstance);
                if ($update) {
                    $moodleobj->redirectToOverviewPage('forexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');
                } else {
                    $moodleobj->redirectToOverviewPage('forexam', get_string('alteration_failed', 'mod_exammanagement'), 'error');
                }
            }
        }

        $moodleobj->setPage('view');
        $moodleobj->outputPageHeader();

        // If instance was moved to new category.
        $oldcategoryid = $exammanagementinstanceobj->moduleinstance->categoryid;
        $coursecategoryid = $PAGE->category->id; // Set course category.

        if ($oldcategoryid !== $coursecategoryid) {

            // Update category id for instance.
            $exammanagementinstanceobj->moduleinstance->categoryid = $coursecategoryid;
            $moodledbobj->UpdateRecordInDB("exammanagement", $exammanagementinstanceobj->moduleinstance);

            // Opdate  category ids for participants.
            $moodledbobj->setFieldInDB('exammanagement_participants', 'categoryid', $coursecategoryid, array('exammanagement' => $exammanagementinstanceobj->getCm()->instance));
            $moodledbobj->setFieldInDB('exammanagement_temp_part', 'categoryid', $coursecategoryid, array('exammanagement' => $exammanagementinstanceobj->getCm()->instance));

        }

        // Delete temp participants and headers if exist.
        if ($moodledbobj->checkIfRecordExists('exammanagement_temp_part', array('exammanagement' => $exammanagementinstanceobj->getCm()->instance))) {
            $exammanagementinstanceobj->moduleinstance->tempimportfileheader = null;

            $moodledbobj->UpdateRecordInDB("exammanagement", $exammanagementinstanceobj->moduleinstance);

            $moodledbobj->DeleteRecordsFromDB('exammanagement_temp_part', array('exammanagement' => $exammanagementinstanceobj->getCm()->instance));
        }

        // Reset phase information if participants are deleted.
        if (!$exammanagementinstanceobj->isExamDataDeleted() && !$userobj->getParticipantsCount()) {
            $exammanagementinstanceobj->moduleinstance->importfileheaders = null;
            $exammanagementinstanceobj->moduleinstance->assignmentmode = null;
            $exammanagementinstanceobj->moduleinstance->datetimevisible = null;
            $exammanagementinstanceobj->moduleinstance->roomvisible = null;
            $exammanagementinstanceobj->moduleinstance->placevisible = null;
            $exammanagementinstanceobj->moduleinstance->datadeletion = null;
            $exammanagementinstanceobj->moduleinstance->examreviewvisible = null;

            $moodledbobj->UpdateRecordInDB("exammanagement", $exammanagementinstanceobj->moduleinstance);
        }

        if ($mode === 'normal') {
            // Rendering and displaying content.

            $cmid = $exammanagementinstanceobj->getCm()->id;

            // Phases information.
            $activephase = $exammanagementinstanceobj->determineactivePhase();

            $phases = new stdclass();

            $phaseone = new stdclass();
            $phaseone->completed = $exammanagementinstanceobj->checkPhaseCompletion('phase_one');

            if (null !== get_user_preferences('exammanagement_phase_one')) {
                $phaseone->open = get_user_preferences('exammanagement_phase_one');
            } else {
                if ($activephase == 'phase_one') {
                    $phaseone->open = true;
                } else {
                    $phaseone->open = false;
                }
            }

            $phases->phase_one = $phaseone;

            $phasetwo = new stdclass();
            $phasetwo->completed = $exammanagementinstanceobj->checkPhaseCompletion('phase_two');

            if (null !== get_user_preferences('exammanagement_phase_two')) {
                $phasetwo->open = get_user_preferences('exammanagement_phase_two');
            } else {
                if ($activephase == 'phase_two') {
                    $phasetwo->open = true;
                } else {
                    $phasetwo->open = false;
                }
            }

            $phases->phase_two = $phasetwo;

            $phaseexam = new stdclass();
            $phaseexam->completed = $exammanagementinstanceobj->checkPhaseCompletion('phase_exam');

            if (null !== get_user_preferences('exammanagement_phase_exam')) {
                $phaseexam->open = get_user_preferences('exammanagement_phase_exam');
            } else {
                if ($activephase == "phase_exam") {
                    $phaseexam->open = true;
                } else {
                    $phaseexam->open = false;
                }
            }

            $phases->phase_exam = $phaseexam;

            $phasethree = new stdclass();
            $phasethree->completed = $exammanagementinstanceobj->checkPhaseCompletion('phase_three');

            if (null !== get_user_preferences('exammanagement_phase_three')) {
                $phasethree->open = get_user_preferences('exammanagement_phase_three');
            } else {
                if ($activephase == 'phase_three') {
                    $phasethree->open = true;
                } else {
                    $phasethree->open = false;
                }
            }

            $phases->phase_three = $phasethree;

            $phasefour = new stdclass();
            $phasefour->completed = $exammanagementinstanceobj->checkPhaseCompletion('phase_four');

            if (null !== get_user_preferences('exammanagement_phase_four')) {
                $phasefour->open = get_user_preferences('exammanagement_phase_four');
            } else {
                if ($activephase == 'phase_four') {
                    $phasefour->open = true;
                } else {
                    $phasefour->open = false;
                }
            }

            $phases->phase_four = $phasefour;

            $phasefive = new stdclass();

            if (isset($misc) && isset($misc['configoptions']) && in_array('noexamreview', $misc['configoptions'])) { // If exam review is disabled.
                $phasefive = false;
            } else {
                $phasefive->completed = $exammanagementinstanceobj->checkPhaseCompletion('phase_five');

                if (null !== get_user_preferences('exammanagement_phase_five')) {
                    $phasefive->open = get_user_preferences('exammanagement_phase_five');
                } else {
                    if ($activephase == 'phase_five') {
                        $phasefive->open = true;
                    } else {
                        $phasefive->open = false;
                    }
                }
            }

            $phases->phase_five = $phasefive;

            if (get_config('mod_exammanagement', 'enablehelptexts')) {
                $helptexticon = $OUTPUT->help_icon('overview', 'mod_exammanagement', '');
                $additionalressourceslink = get_config('mod_exammanagement', 'additionalressources');
            } else {
                $helptexticon = false;
                $additionalressourceslink = false;
            }

            $examtime = $exammanagementinstanceobj->getExamtime();
            $taskcount = $exammanagementinstanceobj->getTaskCount();
            $taskpoints = $exammanagementinstanceobj->formatNumberForDisplay($exammanagementinstanceobj->getTaskTotalPoints());
            $textfieldcontent = $exammanagementinstanceobj->getTextFromTextfield();

            if ($textfieldcontent) {
                if (format_string($textfieldcontent)) {
                    $textfieldcontent = format_string($textfieldcontent);
                } else {
                    $textfieldcontent = get_string('mediacontent', 'mod_exammanagement');
                }
            }

            $participantscount = $userobj->getParticipantsCount();
            $roomscount = $exammanagementinstanceobj->getRoomsCount();
            $roomnames = $exammanagementinstanceobj->getChoosenRoomNames();
            $totalseats = $exammanagementinstanceobj->getTotalNumberOfSeats();
            $placesassigned = $exammanagementinstanceobj->placesAssigned();
            $allplacesassigned = $exammanagementinstanceobj->allPlacesAssigned();
            $assignedplacescount = $exammanagementinstanceobj->getAssignedPlacesCount();
            $datetimevisible = $exammanagementinstanceobj->isDateTimeVisible();
            $roomvisible = $exammanagementinstanceobj->isRoomVisible();
            $placevisible = $exammanagementinstanceobj->isPlaceVisible();
            $bonuscount = $userobj->getEnteredBonusCount();
            $bonuspointsentered = $userobj->getEnteredBonusCount('points');
            $bonusvisible = $exammanagementinstanceobj->isBonusVisible();
            $gradingscale = $exammanagementinstanceobj->getGradingscale();
            $resultscount = $userobj->getEnteredResultsCount();
            $resultvisible = $exammanagementinstanceobj->isResultVisible();
            $datadeletiondate = $exammanagementinstanceobj->getDataDeletionDate();
            $examreviewtime = $exammanagementinstanceobj->getHrExamReviewTime();
            $examreviewroom = $exammanagementinstanceobj->getExamReviewRoom();
            $examreviewvisible = $exammanagementinstanceobj->isExamReviewVisible();
            $deleted = $exammanagementinstanceobj->isExamDataDeleted();

            if ($ldapmanagerobj->isLDAPenabled() && $ldapmanagerobj->isLDAPconfigured()) {
                $ldapavailable = true;
            } else {
                $ldapavailable = false;
            }

            if ($exammanagementinstanceobj->getExamReviewTime()) {
                $resultsenteredafterexamreview = $userobj->getEnteredResultsCount($exammanagementinstanceobj->getExamReviewTime());
            } else {
                $resultsenteredafterexamreview = false;
            }

            $page = new exammanagement_overview($cmid, $phases, $helptexticon, $additionalressourceslink, $examtime, $taskcount, $taskpoints, $textfieldcontent, $participantscount, $roomscount, $roomnames, $totalseats, $placesassigned, $allplacesassigned, $assignedplacescount, $datetimevisible, $roomvisible, $placevisible, $bonuscount, $bonuspointsentered, $bonusvisible, $gradingscale, $resultscount, $resultvisible, $datadeletiondate, $examreviewtime, $examreviewroom, $examreviewvisible, $resultsenteredafterexamreview, $deleted, $ldapavailable);
            echo $OUTPUT->render($page);
        } else if ($mode === 'export_grades') {
            // Rendering and displaying content.

            $cmid = $exammanagementinstanceobj->getCm()->id;

            if (get_config('mod_exammanagement', 'enablehelptexts')) {
                $helptexticon = $OUTPUT->help_icon('export_grades', 'mod_exammanagement', '');
                $additionalressourceslink = get_config('mod_exammanagement', 'additionalressources');
            } else {
                $helptexticon = false;
                $additionalressourceslink = false;
            }

            if ($ldapmanagerobj->isLDAPenabled() && $ldapmanagerobj->isLDAPconfigured()) {
                $ldapavailable = true;
            } else {
                $ldapavailable = false;
            }

            $participantscount = $userobj->getParticipantsCount();
            $bonuspointsentered = $userobj->getEnteredBonusCount('points');
            $gradingscale = $exammanagementinstanceobj->getGradingscale();
            $resultscount = $userobj->getEnteredResultsCount();

            $datadeletiondate = $exammanagementinstanceobj->getDataDeletionDate();
            $deleted = $exammanagementinstanceobj->isExamDataDeleted();

            $page = new exammanagement_overview_export_grades($cmid, $helptexticon, $additionalressourceslink, $participantscount, $bonuspointsentered, $gradingscale, $resultscount, $datadeletiondate, $deleted, $ldapavailable);
            echo $OUTPUT->render($page);
        }

        $moodleobj->outputFooter();

    } else { // if user hasnt entered correct password for this session: show enterPasswordPage
        redirect ($exammanagementinstanceobj->getExammanagementUrl('checkpassword', $exammanagementinstanceobj->getCm()->id), null, null, null);
    }

} else if ($moodleobj->checkCapability('mod/exammanagement:viewparticipantspage')) { // student view

    // require_capability('mod/exammanagement:viewparticipantspage', $ExammanagementInstanceObj->getModulecontext());

    $moodleobj->setPage('view');
    $moodleobj->outputPageHeader();

    // Exam time.
    $examtime = $exammanagementinstanceobj->getExamtime();

    if ($exammanagementinstanceobj->isDateTimeVisible() && $examtime) {
        $date = userdate($examtime, get_string('strftimedatefullshort', 'core_langconfig'));
        $time = userdate($examtime, get_string('strftimetime', 'core_langconfig'));
    } else {
        $date = false;
        $time = false;
    }

    // room and place
    global $USER;

    $moodledbobj = MoodleDB::getInstance();

    $participantobj = $userobj->getExamParticipantObj($USER->id);

    if ($exammanagementinstanceobj->isRoomVisible() && $participantobj && $participantobj->roomname) {
        $room = $participantobj->roomname;
    } else {
        $room = false;
    }

    if ($exammanagementinstanceobj->isPlaceVisible() && $participantobj && $participantobj->place) {
        $place = $participantobj->place;
    } else {
        $place = false;
    }

    // textfield
    $textfield = $exammanagementinstanceobj->getTextFromTextfield();

    // bonussteps
    if ($exammanagementinstanceobj->isBonusVisible() && $participantobj) {
        if ($participantobj->bonussteps === '0') { // allows mustache template to render 0
            $bonussteps = get_string('no_bonus_earned', 'mod_exammanagement');
        } else {
            $bonussteps = $participantobj->bonussteps;
        }
    } else {
        $bonussteps = false;
    }

    // Bonuspoints
    if ($exammanagementinstanceobj->isBonusVisible() && $participantobj) {
        if ($participantobj->bonuspoints === '0') { // Allows mustache template to render 0.
            $bonuspoints = get_string('no_bonus_earned', 'mod_exammanagement');
        } else {
            $bonuspoints = $exammanagementinstanceobj->formatNumberForDisplay($participantobj->bonuspoints);
        }
    } else {
        $bonuspoints = false;
    }

    // Totalpoints
    if ($exammanagementinstanceobj->isResultVisible() && $participantobj) {

        $examstate = $userobj->getExamState($participantobj);

        if ($examstate === 'normal') {
            $examstate = false;
            $totalpoints = $userobj->calculatePoints($participantobj);

            $tasktotalpoints = $exammanagementinstanceobj->formatNumberForDisplay($exammanagementinstanceobj->getTaskTotalPoints());

            $totalpointswithbonus = $exammanagementinstanceobj->formatNumberForDisplay($userobj->calculatePoints($participantobj, true));

            if ($totalpoints === '0') {
                $totalpoints = get_string('no_points_earned', 'mod_exammanagement');
            } else {
                $totalpoints = $exammanagementinstanceobj->formatNumberForDisplay($totalpoints);
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
    if ($exammanagementinstanceobj->isExamReviewVisible() && $exammanagementinstanceobj->getHrExamReviewTime() && $exammanagementinstanceobj->getExamReviewRoom()) {
        $examreviewtime = $exammanagementinstanceobj->getHrExamReviewTime();
        $examreviewroom = $exammanagementinstanceobj->getExamReviewRoom();
    }

    // Check if exam data is deleted.
    $deleted = $exammanagementinstanceobj->isExamDataDeleted();

    // Rendering and displaying content.

    $page = new exammanagement_participantsview($exammanagementinstanceobj->getCm()->id, $userobj->checkIfAlreadyParticipant($USER->id), $date, $time, $room, $place, $textfield, $bonussteps, $bonuspoints, $examstate, $totalpoints, $tasktotalpoints, $totalpointswithbonus, $examreviewtime, $examreviewroom, $deleted);
    echo $OUTPUT->render($page);

    $moodleobj->outputFooter();

} else {
    redirect($CFG->wwwroot, get_string('nopermissions', 'mod_exammanagement'), null, \core\output\notification::NOTIFY_ERROR);
}

$exammanagementinstanceobj->startEvent('view');
