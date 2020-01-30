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
        
        // ############# test #################

        // ### test contextlist exammanagement ####
        // $params = [
        //     'modname'       => 'exammanagement',
        //     'contextlevel'  => CONTEXT_MODULE,
        //     'userid'        => 9,
        // ];

        // // Where user is participant.
        // $sql = "SELECT c.id
        //           FROM {context} c
        //           JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
        //           JOIN {modules} m ON m.id = cm.module AND m.name = :modname
        //           JOIN {role_assignments} t ON t.contextid = c.id
        //          WHERE t.userid = :userid AND t.roleid = 3
        // ";

        // var_dump($DB->get_records_sql($sql, $params, $limitfrom=0, $limitnum=0));


        // // normally method param

        // $contextlist = new \core_privacy\local\request\contextlist();

        // ### /test contextlist exammanagement####

        // ### test exportdata glossary

        // // get exammanagement entries.
        // $sql = "SELECT c.id
        //           FROM {context} c
        //           JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
        //           JOIN {modules} m ON m.id = cm.module AND m.name = :modname
        //           JOIN {glossary} g ON g.id = cm.instance
        //           JOIN {glossary_entries} ge ON ge.glossaryid = g.id
        //          WHERE ge.userid = :glossaryentryuserid";
        // $params = [
        //     'contextlevel' => CONTEXT_MODULE,
        //     'modname' => 'glossary',
        //     'commentarea' => 'glossary_entry',
        //     'glossaryentryuserid' => 8,
        // ];
        // $contextlist->add_from_sql($sql, $params);
        
        // var_dump('contextlist: ');
        // var_dump($contextlist);

        // // method export_user_data
        // global $DB;

        // if (empty($contextlist->count())) {
        //     return;
        // }

        // $user = new stdclass; $user->id = 33;         //$user = $contextlist->get_user();

        // list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        // $params = [
        //     'userid' => $user->id,
        //     'modulename' => 'exammanagement',
        //     'contextlevel' => CONTEXT_MODULE,
        // ] + $contextparams;

        // var_dump('params: ');
        // var_dump($params);

        // $sql = "SELECT c.id, p.lastname, e.name
        //         FROM {context} c
        //         JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
        //         JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
        //         JOIN {exammanagement} p ON p.exammanagement = cm.id
        //         JOIN {exammanagement_participants} p ON p.exammanagement = cm.id
        //         WHERE p.moodleuserid = :userid
        //         ORDER BY c.id, p.moodleuserid";
        
        // $exammanagementdata = $DB->get_recordset_sql($sql, $params);

        // var_dump('exammanagementdata: ');
        // var_dump($exammanagementdata);



        // // Reference to the glossary activity seen in the last iteration of the loop. By comparing this with the
        // // current record, and because we know the results are ordered, we know when we've moved to the entries
        // // for a new glossary activity and therefore when we can export the complete data for the last activity.
        // $lastcmid = null;

        // $glossarydata = [];
        // foreach ($glossaryentries as $record) {
        //     $concept = format_string($record->concept);
        //     $path = array_merge([get_string('entries', 'mod_glossary'), $concept . " ({$record->entryid})"]);

        //     // If we've moved to a new glossary, then write the last glossary data and reinit the glossary data array.
        //     if (!is_null($lastcmid)) {
        //         if ($lastcmid != $record->cmid) {
        //             if (!empty($glossarydata)) {
        //                 $context = \context_module::instance($lastcmid);
        //                 var_dump($glossarydata); self::export_glossary_data_for_user($glossarydata, $context, [], $user);
        //                 $glossarydata = [];
        //             }
        //         }
        //     }
        //     $lastcmid = $record->cmid;
        //     $context = \context_module::instance($lastcmid);

        //     // Export files added on the glossary entry definition field.
        //     $definition = format_text(\core_privacy\local\request\writer::with_context($context)->rewrite_pluginfile_urls($path, 'mod_glossary',
        //         'entry',  $record->entryid, $record->definition), $record->definitionformat);

        //     // Export just the files attached to this user entry.
        //     if ($record->userid == $user->id) {
        //         // Get all files attached to the glossary attachment.
        //         \core_privacy\local\request\writer::with_context($context)->export_area_files($path, 'mod_glossary', 'entry', $record->entryid);

        //         // Get all files attached to the glossary attachment.
        //         \core_privacy\local\request\writer::with_context($context)->export_area_files($path, 'mod_glossary', 'attachment', $record->entryid);
        //     }

        //     // Export associated comments.
        //     \core_comment\privacy\provider::export_comments($context, 'mod_glossary', 'glossary_entry',
        //             $record->entryid, $path, $record->userid != $userid);

        //     // Export associated tags.
        //     \core_tag\privacy\provider::export_item_tags($userid, $context, $path, 'mod_glossary', 'glossary_entries',
        //             $record->entryid, $record->userid != $userid);

        //     // Export associated ratings.
        //     \core_rating\privacy\provider::export_area_ratings($userid, $context, $path, 'mod_glossary', 'entry',
        //             $record->entryid, $record->userid != $userid);

        //     $glossarydata['entries'][] = [
        //         'concept'       => $record->concept,
        //         'definition'    => $definition,
        //         'timecreated'   => \core_privacy\local\request\transform::datetime($record->timecreated),
        //         'timemodified'  => \core_privacy\local\request\transform::datetime($record->timemodified)
        //     ];
        // }
        // $glossaryentries->close();

        // // The data for the last activity won't have been written yet, so make sure to write it now!
        // if (!empty($glossarydata)) {
        //     $context = \context_module::instance($lastcmid);
        //     var_dump($glossarydata); //self::export_glossary_data_for_user($glossarydata, $context, [], $user);
        // }

        // ############# /test #################


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

    $participantObj = $MoodleDBObj->getRecordFromDB('exammanagement_participants', array('exammanagement' => $id, 'moodleuserid' => $USER->id));

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
        $bonus = $participantObj->bonus;
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

// $event = \mod_exammanagement\event\log_variable::create(['other' => 'hallo' . json_encode($ExammanagementInstanceObj)]); // auch objekte übergebbar, jedoch müssen diese in json codiert sein
// $event->trigger();



if ($rs = $MoodleDBObj->getRecordset('exammanagement_participants', array())) {
    
    if($rs->valid()){

        foreach ($rs as $record) {

            $cm = get_coursemodule_from_id('exammanagement', $record->plugininstanceid, 0, false, MUST_EXIST);
            
            $exammanagement = $MoodleDBObj->getRecordFromDB('exammanagement', array('id' => $cm->instance));

            $record->exammanagement = '0';

            $MoodleDBObj->UpdateRecordInDB("exammanagement_participants", $record);

        }

        $rs->close();
    
    }

}