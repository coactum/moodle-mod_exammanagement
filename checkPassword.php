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
 * Allows users to enter a password to access the exammanagement.
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

// If password should be reseted.
$resetpw = optional_param('resetpw', 0, PARAM_INT);

// If a user requests a password reset.
$requestpwreset = optional_param('requestPWReset', 0, PARAM_INT);

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

$canresetpassword = has_capability('mod/exammanagement:resetpassword', $context);
$canrequestpasswordreset = has_capability('mod/exammanagement:requestpasswordreset', $context);
$moodlesystemname = helper::getmoodlesystemname();

// Reset password.
if ($canresetpassword && $resetpw == true && isset($moduleinstance->password)) {

    global $USER;
    $userfrom = $USER;

    $moduleinstance->password = null;
    $DB->update_record("exammanagement", $moduleinstance);

    // Send mail to all teachers to inform them about password reset.
    $role = $DB->get_record('role', ['shortname' => 'editingteacher']);
    $courseid = $course->id;
    $coursecontext = context_course::instance($courseid);
    $teachers = get_role_users($role->id, $coursecontext);

    $mailsubject = get_string('password_reset_mailsubject', 'mod_exammanagement', [
        'systemname' => $moodlesystemname,
        'name' => $moduleinstance->name,
        'coursename' => $course->fullname,
    ]);
    $text = get_string('password_reset_mailtext', 'mod_exammanagement', [
        'systemname' => $moodlesystemname,
        'name' => $moduleinstance->name,
        'coursename' => $course->fullname,
    ]);

    foreach ($teachers as $user) {
        helper::sendsinglemessage($moduleinstance, $id, $course, $moodlesystemname, $userfrom, $user->id, $mailsubject, $text,
            'passwordresetmessage');
    }

    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('password_reset_successfull', 'mod_exammanagement',
        ['systemname' => $moodlesystemname]), null, 'success');

} else if ($resetpw == true) {
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('password_reset_failed', 'mod_exammanagement'), null, 'error');
}

// Handle password reset request.
if ($canrequestpasswordreset && $requestpwreset == true
    && get_config('mod_exammanagement', 'enablepasswordresetrequest') === '1'
    && isset($moduleinstance->password)) {

    require_sesskey();

    // Send message to users with global manager role to request password reset.
    $systemcontext = context_system::instance();
    $supportusers = get_users_by_capability($systemcontext, 'mod/exammanagement:resetpassword');

    global $USER;
    $userfrom = $USER;

    $mailsubject = get_string('password_reset_request_mailsubject', 'mod_exammanagement', [
        'systemname' => $moodlesystemname,
        'name' => $moduleinstance->name,
        'coursename' => $course->fullname,
    ]);

    $url = new moodle_url('/user/view.php', [
        'id' => $USER->id,
        'course' => $course->id,
    ]);
    $profilelink = '<strong><a href="' . $url . '">' . $USER->firstname . ' ' . $USER->lastname . '</a></strong>';

    $urlstr = strval(new moodle_url('/mod/exammanagement/checkpassword.php', ['id' => $id, 'resetpw' => true]));
    $text = get_string('password_reset_request_mailtext', 'mod_exammanagement', [
        'systemname' => $moodlesystemname,
        'user' => $profilelink,
        'coursename' => $course->fullname,
        'url' => $urlstr,
    ]);

    foreach ($supportusers as $userto) {
        $messageid = $helper::sendsinglemessage($moduleinstance, $id, $course, $moodlesystemname, $userfrom, $userto, $mailsubject,
            $text, 'passwordresetrequest');
    }

    if (isset($messageid)) {
        redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
            get_string('password_reset_request_successfull', 'mod_exammanagement',
            ['systemname' => $moodlesystemname]), null, 'success');
    } else {
        redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
            get_string('password_reset_request_failed', 'mod_exammanagement'), null, 'error');
    }
}

// If password is not set and user has manually called this page.
if (!isset($moduleinstance->password)) {
    redirect(new moodle_url('/mod/exammanagement/view.php', ['id' => $id]), null, null, null);
}

// Instantiate form.
require_once($CFG->dirroot . '/mod/exammanagement/classes/forms/checkpassword_form.php');
$mform = new mod_exammanagement_checkpassword_form(null, ['id' => $id, 'e' => $e]);

// Form processing and displaying is done here.
if ($mform->is_cancelled()) { // Handle form cancel operation, if cancel button is present on form.
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

} else if ($fromform = $mform->get_data()) { // In this case you process validated data.

    $password = $fromform->password;
    $passwordhash = base64_decode($moduleinstance->password);

    if (password_verify($password, $passwordhash)) { // Check if password is correct.

        if (password_needs_rehash($passwordhash, PASSWORD_DEFAULT)) { // Check if passwords needs rehash because of new algorithm.

            // If so update saved password hash.
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $moduleinstance->password = $hash;

            $DB->update_record("exammanagement", $moduleinstance);

        }

        global $SESSION;

        // Remember login and redirect.
        $SESSION->loggedInExamOrganizationId = $id;

        redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
            get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
    } else { // If password is not correct.
        redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
            get_string('wrong_password', 'mod_exammanagement'), null, 'error');
    }

} else {
    // This branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.

    $plugintype = get_string('modulename', 'mod_exammanagement');
    $modulename = format_string($moduleinstance->name, true, [
        'context' => $context,
    ]);
    $title = get_string('checkpassword', 'mod_exammanagement');

    $PAGE->set_url('/mod/exammanagement/checkpassword.php', ['id' => $id]);
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

        if ($canresetpassword) {
            echo $OUTPUT->heading($title . ' ' . $OUTPUT->help_icon('checkpasswordadmin', 'mod_exammanagement', ''), 4);
        } else {
            echo $OUTPUT->heading($title . ' ' . $OUTPUT->help_icon('checkpassword', 'mod_exammanagement', ''), 4);
        }
    } else {
        echo $OUTPUT->heading($title, 4);
    }

    // Output buttons.
    if ($canresetpassword) {
        $url = new moodle_url('/mod/exammanagement/checkpassword.php',
            ['id' => $id, 'resetPW' => true]
        );
        echo '<a href="' . $url . '" role="button" class="btn btn-primary float-right" title="' .
            get_string("resetpasswordadmin", "mod_exammanagement") . '"><span class="d-none d-lg-block">' .
            get_string("resetpasswordadmin", "mod_exammanagement") .
            '</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>';
    } else if ($canrequestpasswordreset && get_config('mod_exammanagement', 'enablepasswordresetrequest') === '1') {
        $url = new moodle_url('/mod/exammanagement/checkpassword.php',
            ['id' => $id,
            'requestPWReset' => true,
            'sesskey' => sesskey()]
        );
        echo '<a href="' . $url . '" role="button" class="btn btn-secondary float-right" title="' .
            get_string("requestpasswordreset", "mod_exammanagement") . '"><span class="d-none d-lg-block">' .
            get_string("requestpasswordreset", "mod_exammanagement") .
            '</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>';
    }

    // Output description.
    echo '<p>' . get_string("checkpasswordstr", "mod_exammanagement") . '</p>';

    // Output alerts.

    // Set default data.
    $mform->set_data(['id' => $id]);

    // Display form.
    $mform->display();

    // Finish the page.
    echo $OUTPUT->footer();

}
