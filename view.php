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

global $PAGE, $CFG, $USER;

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);

$UserObj = User::getInstance($id, $e, $ExammanagementInstanceObj->moduleinstance->categoryid);

$MoodleObj = Moodle::getInstance($id, $e);

$MoodleDBObj = MoodleDB::getInstance();

if ($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){ // if teacher

  if($calledfromformdt){ // saveDateTime

        if($datetimevisible){
          $ExammanagementInstanceObj->moduleinstance->datetimevisible = true;
        } else {
          $ExammanagementInstanceObj->moduleinstance->datetimevisible = false;
        }

        $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
        if($update){
          $MoodleObj->redirectToOverviewPage('forexam', 'Informationen sichtbar geschaltet', 'success');
        } else {
          $MoodleObj->redirectToOverviewPage('forexam', 'Informationen konnten nicht sichtbar geschaltet werden', 'error');
        }

  } elseif($calledfromformroom){ // saveRoom

    if($roomvisible){
      $ExammanagementInstanceObj->moduleinstance->roomvisible = true;
    } else {
      $ExammanagementInstanceObj->moduleinstance->roomvisible = false;
    }

     $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
     if($update){
       $MoodleObj->redirectToOverviewPage('forexam', 'Informationen sichtbar geschaltet', 'success');
     } else {
       $MoodleObj->redirectToOverviewPage('forexam', 'Informationen konnten nicht sichtbar geschaltet werden', 'error');
     }
  }

  elseif($calledfromformplace){ // savePlace

     if($placevisible){
       $ExammanagementInstanceObj->moduleinstance->placevisible = true;
     } else {
       $ExammanagementInstanceObj->moduleinstance->placevisible = false;
     }

     $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
     if($update){
       $MoodleObj->redirectToOverviewPage('forexam', 'Informationen sichtbar geschaltet', 'success');
     } else {
       $MoodleObj->redirectToOverviewPage('forexam', 'Informationen konnten nicht sichtbar geschaltet werden', 'error');
     }
  } elseif($calledfromformcorrection){ // save correction as completed

    if($correctioncompleted){
      $ExammanagementInstanceObj->moduleinstance->correctioncompletiondate = time();
    } else {
      $ExammanagementInstanceObj->moduleinstance->correctioncompletiondate = NULL;
    }

     $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
     if($update){
       $MoodleObj->redirectToOverviewPage('forexam', 'Korrektur abgeschlossen', 'success');
     } else {
       $MoodleObj->redirectToOverviewPage('forexam', 'Korrektur konnte nicht abgeschlossen werden', 'error');
     }
  }

  $MoodleObj->setPage('view');
  $MoodleObj-> outputPageHeader();

  // update categoryid if neccesarry

  $oldcategoryid = $ExammanagementInstanceObj->moduleinstance->categoryid;
  $coursecategoryid = substr(strtolower(preg_replace("/[^0-9a-zA-Z]/", "", $PAGE->category->name)), 0, 6); //set course category

  if($oldcategoryid !== $coursecategoryid){

    $ExammanagementInstanceObj->moduleinstance->categoryid = $coursecategoryid;
    $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

    // tn aus Tabelle mit alter courseid löschen
    $oldrecords = $MoodleDBObj->getRecordsFromDB('exammanagement_part_'.$oldcategoryid, array('plugininstanceid' => $id));

    if($oldrecords){
      $MoodleDBObj->InsertBulkRecordsInDB('exammanagement_part_'.$coursecategoryid, $oldrecords);
      
      $MoodleDBObj->DeleteRecordsFromDB('exammanagement_part_'.$oldcategoryid, array('plugininstanceid' => $id));
      
      $MoodleObj->redirectToOverviewPage('', 'Kurs wurde manuell in ein anderes Semester verschoben. Sollten bereits eingetragene Teilnehmer nicht mehr angezeigt werden müssen diese ggf. erneut eingetragen werden.', 'warning');
      
    }

  }

  // delete temp participants if exist

  $tempparticipants = $MoodleDBObj->getRecordsFromDB('exammanagement_temp_part', array('plugininstanceid' => $id));

    if($tempparticipants){
      $MoodleDBObj->DeleteRecordsFromDB('exammanagement_temp_part', array('plugininstanceid' => $id));      
    }

  //rendering and displaying content
  $output = $PAGE->get_renderer('mod_exammanagement');

  $cmid = $ExammanagementInstanceObj->getCm()->id;
  $statePhaseOne = $ExammanagementInstanceObj->checkPhaseCompletion(1);
  $statePhaseTwo = $ExammanagementInstanceObj->checkPhaseCompletion(2);
  $statePhaseExam = $ExammanagementInstanceObj->checkPhaseCompletion("Exam");
  $statePhaseThree = $ExammanagementInstanceObj->checkPhaseCompletion(3);
  $statePhaseFour = $ExammanagementInstanceObj->checkPhaseCompletion(4);

  $currentPhaseOne = false;
  $currentPhaseTwo = false;
  $currentPhaseExam = false;
  $currentPhaseThree = false;
  $currentPhaseFour = false;

  $currentPhase = $ExammanagementInstanceObj->determineCurrentPhase();
  switch ($currentPhase){
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
    default:
        break;
  }

  $examtime = $ExammanagementInstanceObj->getHrExamtimeTemplate();
  $taskcount = $ExammanagementInstanceObj->getTaskCount();
  $taskpoints = $ExammanagementInstanceObj->getTaskTotalPoints();
  $textfieldcontent = $ExammanagementInstanceObj->getShortenedTextfield();
  $participantscount = $UserObj->getParticipantsCount();
  $roomscount = $ExammanagementInstanceObj->getRoomsCount();
  $roomnames = $ExammanagementInstanceObj->getChoosenRoomNames();
  $stateofplaces = $ExammanagementInstanceObj->isStateOfPlacesCorrect();
  $stateofplaceserror = $ExammanagementInstanceObj->isStateOfPlacesError();
  $datetimevisible = $ExammanagementInstanceObj->isDateTimeVisible();
  $roomvisible = $ExammanagementInstanceObj->isRoomVisible();
  $placevisible = $ExammanagementInstanceObj->isPlaceVisible();
  $gradingscale = $ExammanagementInstanceObj->getGradingscale();
  $resultscount = $ExammanagementInstanceObj->getInputResultsCount();
  $datadeletiondate = $ExammanagementInstanceObj->getDataDeletionDate();

  $page = new exammanagement_overview($cmid, $statePhaseOne, $statePhaseTwo, $statePhaseExam, $statePhaseThree, $statePhaseFour, $currentPhaseOne, $currentPhaseTwo, $currentPhaseExam, $currentPhaseThree, $currentPhaseFour, $examtime, $taskcount, $taskpoints, $textfieldcontent, $participantscount, $roomscount, $roomnames, $stateofplaces, $stateofplaceserror, $datetimevisible, $roomvisible, $placevisible, $gradingscale, $resultscount, $datadeletiondate);
  echo $output->render($page);

  //$this->debugElementsOverview();

  $MoodleObj->outputFooter();

} elseif ($MoodleObj->checkCapability('mod/exammanagement:viewparticipantspage')){ // student view

  //require_capability('mod/exammanagement:viewparticipantspage', $ExammanagementInstanceObj->getModulecontext());

  $MoodleObj->setPage('view');
  $MoodleObj-> outputPageHeader();
  
  //examtime
  $examtime = $ExammanagementInstanceObj->getExamtime();

  if($ExammanagementInstanceObj->isDateTimeVisible() && $examtime){
    $date = date('d.m.Y', $examtime);
    $time = date('H:i', $examtime);
  } else{
    $date = false;
    $time = false;
  }

  //room and place
  $participantObj = $UserObj->getParticipantObj();

  if($ExammanagementInstanceObj->isRoomVisible() && $participantObj->roomname){
    $room = $participantObj->roomname;
  } else {
    $room = false;
  }

  if($ExammanagementInstanceObj->isPlaceVisible() && $participantObj->place){
    $place = $participantObj->place;
  } else {
    $place = false;
  }

  //textfield
  $textfield = $ExammanagementInstanceObj->getTextFromTextfield();

  //rendering and displaying content
  $output = $PAGE->get_renderer('mod_exammanagement');

  $page = new exammanagement_participantsview($ExammanagementInstanceObj->getCm()->id, $UserObj->checkIfAlreadyParticipant($USER->id), $date, $time, $room, $place, $textfield);
  echo $output->render($page);

  $MoodleObj->outputFooter();

} else{
    redirect ($CFG->wwwroot, 'Sie haben keine gültigen Rechte.', null, \core\output\notification::NOTIFY_ERROR);
}

$ExammanagementInstanceObj->startEvent('view');


//for testing
// global $SESSION;
//
// var_dump($SESSION);
//
// set_user_preference('helptexts','10011001');
// var_dump(get_user_preferences());

//#####################################################################
//old (from plugin template), now in class (exammanagementIsnatnce.php)
//#####################################################################

 // if ($id) {
//      $cm             = get_coursemodule_from_id('exammanagement', $id, 0, false, MUST_EXIST);
//      //$course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
//      $course = get_course($cm->course);
//      $moduleinstance = $DB->get_record('exammanagement', array('id' => $cm->instance), '*', MUST_EXIST);
//  } else if ($e) {
//      $moduleinstance = $DB->get_record('exammanagement', array('id' => $e), '*', MUST_EXIST);
//      //$course         = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
//      $course = get_course($moduleinstance->course);
//      $cm             = get_coursemodule_from_instance('exammanagement', $moduleinstance->id, $course->id, false, MUST_EXIST);
//  } else {
//      print_error(get_string('missingidandcmid', mod_exammanagement));
//  }
//
//  require_login($course, true, $cm);
//
// $modulecontext = context_module::instance($cm->id);
//
// //events
// $event = \mod_exammanagement\event\course_module_viewed::create(array(
//     'objectid' => $moduleinstance->id,
//     'context' => $modulecontext
// ));
// $event->add_record_snapshot('course', $course);
// $event->add_record_snapshot('exammanagement', $moduleinstance);
// $event->trigger();
//
// // Print the page header.
// $PAGE->set_url('/mod/exammanagement/view.php', array('id' => $cm->id));
// $PAGE->set_title(format_string($moduleinstance->name).' ('.get_string('modulename','mod_exammanagement').')');
// $PAGE->set_heading(format_string($course->fullname));
// $PAGE->set_context($modulecontext);

/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_cacheable(false);
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('newmodule-'.$somevar);
 */

// Output starts here.
// echo $OUTPUT->header();
//
// // set basic content (to be moved to renderer that has to define which usecas it is (e.g. overview, subpage, debug infos etc.)
// echo $OUTPUT->heading(get_string('maintitle', 'mod_exammanagement'));
//
// // Conditions to show the intro can change to look for own settings or whatever.
//  if ($moduleinstance->intro) {
//      echo $OUTPUT->box(format_module_intro('exammanagement', $moduleinstance, $cm->id), 'generalbox mod_introbox', 'newmoduleintro');
//  }

//check for user_roles (not working in Moodle 34, also not needed
//$roles = get_user_roles($modulecontext, $USER->id);
//foreach ($roles as $role) {
//    $rolestr[]= role_get_name($role, $modulecontext);
//}
//$rolestr = implode(', ', $rolestr);
// $rolestr='Test';
//
// //check if stages are completed (to be moved to own function)
// $firststagecompleted = true; //for testing, later to be calculated depending on if all data is set
//
// //get date and time (to be moved to own function
// $date = $DB->get_field('exammanagement', 'date', array('id' => $cm->instance), '*', MUST_EXIST);
// $time = $DB->get_field('exammanagement', 'time', array('id' => $cm->instance), '*', MUST_EXIST);
//
// //disassemble $date and $time (to be moved to own function)
//
// if ($date) {
// 	$datecomponents = explode("-", $date);
//
// 	$day=$datecomponents[2];
// 	$month=$datecomponents[1];
// 	$year=$datecomponents[0];
// }
// else{
// 	$day='';
// 	$month='';
// 	$year='';
// }
//
// if ($time) {
// 	$timecomponents = explode(":", $time);
//
// 	$hour=$timecomponents[0];
// 	$minute=$timecomponents[1];
// }
//
// else{
// 	$hour='';
// 	$minute='';
// }
//
// //rendering and displaying basic content (overview).
// $output = $PAGE->get_renderer('mod_exammanagement');
// $page = new \mod_exammanagement\output\exammanagement_overview($cm->id, $rolestr, $firststagecompleted, $day, $month, $year, $hour, $minute);
// echo $output->render($page);
//
// //rendering and displaying debug info (to be moved to renderer)
// if($USER->username=="admin"){
//
// 	$output = $PAGE->get_renderer('mod_exammanagement');
// 	$page = new \mod_exammanagement\output\exammanagement_debug_infos($id,$cm,$course,$moduleinstance, $firststagecompleted, $day, $month, $year, $hour, $minute);
// 	echo $output->render($page);
// }
// // Finish the page.
// echo $OUTPUT->footer();
