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

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$p = new \mod_exammanagement\general\exammanagementInstance($id, $e);


// $setday = optional_param('setday', 0, PARAM_INT);
// $setmonth = optional_param('setmonth', 0, PARAM_INT);
// $setyear = optional_param('setyear', 0, PARAM_INT);
// $sethour = optional_param('sethour', 0, PARAM_INT);
// $setminute = optional_param('setminute', 0, PARAM_INT);

// if ($id) {
//     $cm             = get_coursemodule_from_id('exammanagement', $id, 0, false, MUST_EXIST);
//     $course = get_course($cm->course);
//     $moduleinstance = $DB->get_record('exammanagement', array('id' => $cm->instance), '*', MUST_EXIST);
// } else if ($e) {
//     $moduleinstance = $DB->get_record('exammanagement', array('id' => $e), '*', MUST_EXIST);
//     $course = get_course($moduleinstance->course);
//     $cm             = get_coursemodule_from_instance('exammanagement', $moduleinstance->id, $course->id, false, MUST_EXIST);
// } else {
//     print_error(get_string('missingidandcmid', mod_exammanagement));
// }
// 
// require_login($course, true, $cm);
// 
// $modulecontext = context_module::instance($cm->id);
// //$coursecontext = context_course::instance($course->id);
// 
// //events
// // $event = \mod_exammanagement\event\set_date_time_viewed::create(array(
// //     'objectid' => $moduleinstance->id,
// //     'context' => $coursecontext
// // ));
// // $event->add_record_snapshot('course', $course);
// // $event->add_record_snapshot('exammanagement', $moduleinstance);
// // $event->trigger();
// 
// // Print the page header.
// $PAGE->set_url('/mod/exammanagement/set_date_time.php', array('id' => $cm->id));
// $PAGE->set_title(format_string($moduleinstance->name).' ('.get_string('modulename','mod_exammanagement').')');
// $PAGE->set_heading(format_string($course->fullname));
// $PAGE->set_context($modulecontext);
// 
// /*
//  * Other things you may want to set - remove if not needed.
//  * $PAGE->set_cacheable(false);
//  * $PAGE->set_focuscontrol('some-html-id');
//  * $PAGE->add_body_class('newmodule-'.$somevar);
//  */
//  
// // Output starts here.
// echo $OUTPUT->header();

//if called from overviewpage

// if (!$setday && !$setmonth && !$setyear && !$sethour && !$setminute){
// 
// 	//get date and time from DB (own function)
// 	$date = $DB->get_field('exammanagement', 'date', array('id' => $cm->instance), '*', MUST_EXIST);
// 	$time = $DB->get_field('exammanagement', 'time', array('id' => $cm->instance), '*', MUST_EXIST);
// 
// 	//disassemble $date to day, month and year //own function
// 	if ($date) {
// 		$datecomponents = explode("-", $date);
// 
// 		$day=$datecomponents[2];
// 		$month=$datecomponents[1];
// 		$year=$datecomponents[0];
// 	}
// 
// 	else{
// 		$day='';
// 		$month='';
// 		$year='';
// 	}
// 
// 	//disassemble $time to hour and minute //own function
// 	if ($date) {
// 		$timecomponents = explode(":", $time);
// 
// 		$hour=$timecomponents[0];
// 		$minute=$timecomponents[1];
// 	}
// 
// 	else{
// 		$hour='';
// 		$minute='';
// 	}
// 
// 	//rendering and displaying page
// 	$output = $PAGE->get_renderer('mod_exammanagement');
// 	$page = new \mod_exammanagement\output\exammanagement_set_date_time($cm->id, $day, $month, $year, $hour, $minute); //
// 
// 	echo $output->render($page);
// 
// }

//if called from itself

// if ($setday && $setmonth && $setyear && $sethour && $setminute){
// 	global $CFG;
// 	// combine day+month+year and save it in DB->date ...
// 	
// 	$moduleinstance->date=$setyear.'-'.$setmonth.'-'.$setday;
// 	$moduleinstance->time=$sethour.':'.$setminute.':00';
// 	
// 	$DB->update_record("exammanagement", $moduleinstance);
// 	
// 	$url=$CFG->wwwroot.'/mod/exammanagement/view.php?id='.$id;
// 	
// 	redirect ($url);
// 
// }
// 
// //rendering and displaying debug info (to be moved to renderer)
// if($USER->username=="admin"){
// 	
// 	$output = $PAGE->get_renderer('mod_exammanagement');
// 	$page = new \mod_exammanagement\output\exammanagement_debug_infos($id,$cm,$course,$moduleinstance);
// 	echo $output->render($page);
// }
// 
// // Finish the page.
// echo $OUTPUT->footer();
