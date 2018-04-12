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

use mod_exammanagement\output;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

require_once(__DIR__.'/classes/output/exammanagement_overview.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

// relevant if called from itself and information is set visible for users
$datetimevisible = optional_param('datetimevisible', 0, PARAM_RAW);

$roomplacevisible = optional_param('roomplacevisible', 0, PARAM_RAW);

$calledfromformdt = optional_param('calledfromformdt', 0, PARAM_RAW);

$calledfromformrp = optional_param('calledfromformrp', 0, PARAM_RAW);

global $PAGE, $CFG;

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);

$MoodleObj = Moodle::getInstance($id, $e);

$MoodleDBObj = MoodleDB::getInstance();

if ($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){ // if teacher

  if($calledfromformdt&&$MoodleObj->checkCapability('mod/exammanagement:adddefaultrooms')){ // saveDateTime

  			$ExammanagementInstanceObj->moduleinstance->datetimevisible=$datetimevisible;

  			$MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

  			$MoodleObj->redirectToOverviewPage('forexam', 'Informationen sichtbar geschaltet', 'success');

  } elseif($calledfromformrp&&$MoodleObj->checkCapability('mod/exammanagement:adddefaultrooms')){ // saveRoomPlace

     $ExammanagementInstanceObj->moduleinstance->roomplacevisible=$roomplacevisible;

     $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

     $MoodleObj->redirectToOverviewPage('forexam', 'Informationen sichtbar geschaltet', 'success');
  }

  $MoodleObj->setPage('view');
  $MoodleObj-> outputPageHeader();

  //rendering and displaying content
  $output = $PAGE->get_renderer('mod_exammanagement');
  $page = new output\exammanagement_overview($ExammanagementInstanceObj->getCm()->id, $ExammanagementInstanceObj->checkPhaseCompletion(1), $ExammanagementInstanceObj->checkPhaseCompletion(2), $ExammanagementInstanceObj->checkPhaseCompletion(3), $ExammanagementInstanceObj->checkPhaseCompletion(4), $ExammanagementInstanceObj->getHrExamtimeTemplate(), $ExammanagementInstanceObj->getShortenedTextfield(), $ExammanagementInstanceObj->getParticipantsCount(), $ExammanagementInstanceObj->getRoomsCount(), $ExammanagementInstanceObj->getChoosenRoomNames(), $ExammanagementInstanceObj->isStateOfPlacesCorrect(), $ExammanagementInstanceObj->isStateOfPlacesError(), $ExammanagementInstanceObj->isDateTimeVisible(),$ExammanagementInstanceObj->isRoomPlaceVisible());
  echo $output->render($page);

  //$this->debugElementsOverview();

  //$ExammanagementInstanceObj->setModuleinstance('textfield','Hi');

  $MoodleObj->outputFooter();

} elseif ($MoodleObj->checkCapability('mod/exammanagement:viewparticipantspage')){ // student view

  //require_capability('mod/exammanagement:viewparticipantspage', $ExammanagementInstanceObj->getModulecontext());

  $MoodleObj->setPage('view');
  $MoodleObj-> outputPageHeader();

  //rendering and displaying content
  $output = $PAGE->get_renderer('mod_exammanagement');
  $page = new output\exammanagement_participantsview($ExammanagementInstanceObj->getCm()->id, $ExammanagementInstanceObj->isParticipant(), $ExammanagementInstanceObj->getDateForParticipants(), $ExammanagementInstanceObj->getTimeForParticipants(), $ExammanagementInstanceObj->getRoomForParticipants(), $ExammanagementInstanceObj->getPlaceForParticipants(), $ExammanagementInstanceObj->getTextFromTextfield());
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
