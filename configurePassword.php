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
 * Allows teachers to configure the password for an exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_exammanagement\local\helper;

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// Course_module ID, or ...
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module.
$e = optional_param('e', 0, PARAM_INT);

// If the password should be reseted.
$resetpw = optional_param('resetpw', 0, PARAM_INT);

// Set the basic variables $course, $cm and $moduleinstance.
if ($id) {
    [$course, $cm] = get_course_and_cm_from_cmid($id, 'exammanagement');
    $moduleinstance = $DB->get_record('exammanagement', ['id' => $cm->instance], '*', MUST_EXIST);
} else {
    throw new moodle_exception('missingparameter');
}

// Check if course module, course and course section exist.
if (!$cm) {
    throw new moodle_exception(get_string('incorrectmodule', 'exammanagement'));
} else if (!$course) {
    throw new moodle_exception(get_string('incorrectcourseid', 'exammanagement'));
} else if (!$coursesections = $DB->get_record("course_sections", ["id" => $cm->section])) {
    throw new moodle_exception(get_string('incorrectmodule', 'exammanagement'));
}

// Check login and capability.
require_login($course, true, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/exammanagement:viewinstance', $context);

// Get global and construct helper objects.
global $OUTPUT, $PAGE;

// If user has not entered the correct password: redirect to check password page.
if (isset($moduleinstance->password) &&
    (!isset($SESSION->loggedInExamOrganizationId) || $SESSION->loggedInExamOrganizationId !== $id)) {

    redirect(new moodle_url('/mod/exammanagement/checkpassword.php', ['id' => $id]), null, null, null);
}

if ($resetpw == true) {
    $moduleinstance->password = null;
    $DB->update_record("exammanagement", $moduleinstance);
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
}

// Check if requirements are met.
if (helper::isexamdatadeleted($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('err_examdata_deleted', 'mod_exammanagement'), null, 'error');
}

// Instantiate form.
require_once($CFG->dirroot . '/mod/exammanagement/classes/forms/configurepassword_form.php');
$mform = new mod_exammanagement_configurepassword_form(null, ['id' => $id, 'e' => $e]);

// Form processing and displaying is done here.
if ($mform->is_cancelled()) { // Handle form cancel operation, if cancel button is present on form.
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

} else if ($fromform = $mform->get_data()) { // In this case you process validated data.

    $password = $fromform->password;

    $hashedpassword = base64_encode(password_hash($password, PASSWORD_DEFAULT));

    $moduleinstance->password = $hashedpassword;

    $update = $DB->update_record("exammanagement", $moduleinstance);

    if ($update) {
        redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
            get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
    } else {
        redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
            get_string('alteration_failed', 'mod_exammanagement'), null, 'error');
    }

} else {
    // This branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.

    // Set $PAGE.
    $plugintype = get_string('modulename', 'mod_exammanagement');
    $modulename = format_string($moduleinstance->name, true, [
        'context' => $context,
    ]);
    $title = get_string('configurepassword', 'mod_exammanagement');

    $PAGE->set_url('/mod/exammanagement/configurepassword.php', ['id' => $id]);
    $PAGE->navbar->add($title);
    $PAGE->set_title($plugintype . ': ' . $modulename . ' - ' . $title);
    $PAGE->set_heading($course->fullname);
    if ($CFG->branch < 400) {
        $PAGE->force_settings_menu();
    }

    // Output header.
    echo $OUTPUT->header();

    if ($CFG->branch < 400) {
        echo $OUTPUT->heading($modulename);

        if ($moduleinstance->intro) {
            echo $OUTPUT->box(format_module_intro('exammanagement', $moduleinstance, $cm->id), 'generalbox', 'intro');
        }
    }

    // Output heading.
    if (get_config('mod_exammanagement', 'enablehelptexts')) {
        echo $OUTPUT->heading($title . ' ' . $OUTPUT->help_icon('configurepassword', 'mod_exammanagement', ''), 4);
    } else {
        echo $OUTPUT->heading($title, 4);
    }

    // Output alerts.

    // Output buttons.
    if (isset($moduleinstance->password)) {
        $url = new moodle_url('/mod/exammanagement/configurepassword.php', ['id' => $id, 'resetpw' => true]);

        echo '<div class="float-right"><a href="' . $url . '" role="button" class="btn btn-primary" title="' .
            get_string("reset_password", "mod_exammanagement") . '"><span class="d-none d-lg-block">' .
            get_string("reset_password", "mod_exammanagement") .
            '</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a></div>';
    }

    // Output description.
    echo '<p>' . get_string("configure_password", "mod_exammanagement") . '</p>';

    // Set default data.
    $mform->set_data(['id' => $id]);

    // Display form.
    $mform->display();

    // Finish the page.
    echo $OUTPUT->footer();
}
