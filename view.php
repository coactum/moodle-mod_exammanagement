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

if ($id) {
    $cm             = get_coursemodule_from_id('exammanagement', $id, 0, false, MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('exammanagement', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($e) {
    $moduleinstance = $DB->get_record('exammanagement', array('id' => $e), '*', MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm             = get_coursemodule_from_instance('exammanagement', $moduleinstance->id, $course->id, false, MUST_EXIST);
} else {
    print_error(get_string('missingidandcmid', mod_exammanagement));
}

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);

$event = \mod_exammanagement\event\course_module_viewed::create(array(
    'objectid' => $moduleinstance->id,
    'context' => $modulecontext
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('exammanagement', $moduleinstance);
$event->trigger();

// Print the page header.
$PAGE->set_url('/mod/exammanagement/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name).' ('.get_string('modulename','mod_exammanagement').')');
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_cacheable(false);
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('newmodule-'.$somevar);
 */
 
// Output starts here.
echo $OUTPUT->header();

// Conditions to show the intro can change to look for own settings or whatever.
// if ($moduleinstance->intro) {
//     echo $OUTPUT->box(format_module_intro('exammanagement', $moduleinstance, $cm->id), 'generalbox mod_introbox', 'newmoduleintro');
// }

// set basic content

echo $OUTPUT->heading(get_string('maintitle', 'mod_exammanagement'));

$roles = get_user_roles($modulecontext, $USER->id);
foreach ($roles as $role) {
    $rolestr[]= role_get_name($role, $modulecontext);
}
$rolestr = implode(', ', $rolestr);

$output = $PAGE->get_renderer('mod_exammanagement');
$page = new \mod_exammanagement\output\exammanagement_overview($rolestr);

// displaying basic content.
echo $output->render($page);

// for testing index.php
echo '<a href="http://localhost:8888/moodle33/mod/exammanagement/index.php">Testlink zu index.php</a>';

//displaying debug info

if($USER->username=="admin"){
	echo '<br>';
	echo ' id: ';
	var_dump($id);
	echo '<br> cm:';
	var_dump($cm);
	echo '<br> moduleinstance:';
	var_dump ($moduleinstance);
}

// Finish the page.
echo $OUTPUT->footer();
