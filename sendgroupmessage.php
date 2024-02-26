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
 * Allows teachers to send a groupmessage to all participants in an exammanagement.
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

$courseparticipantsids = helper::getcourseparticipantsids($id);

// If user has not entered the correct password: redirect to check password page.
if (isset($moduleinstance->password) &&
    (!isset($SESSION->loggedInExamOrganizationId) || $SESSION->loggedInExamOrganizationId !== $id)) {

    redirect(new moodle_url('/mod/exammanagement/checkpassword.php', ['id' => $id]), null, null, null);
}

$moodleparticipantscount = helper::getparticipantscount($moduleinstance, 'moodle');
$nonemoodleparticipantscount = helper::getparticipantscount($moduleinstance, 'nonmoodle');

// Check if requirements are met.
if (helper::isexamdatadeleted($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('err_examdata_deleted', 'mod_exammanagement'), null, 'error');
} else if (!helper::getparticipantscount($moduleinstance) || (!$moodleparticipantscount && !$nonemoodleparticipantscount)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('no_participants_added', 'mod_exammanagement'), null, 'error');
}

$moodlesystemname = helper::getmoodlesystemname();

// Instantiate form.
require_once($CFG->dirroot . '/mod/exammanagement/classes/forms/sendgroupmessage_form.php');
$mform = new mod_exammanagement_sendgroupmessage_form(null, ['id' => $id, 'e' => $e]);

// Form processing and displaying is done here.
if ($mform->is_cancelled()) { // Handle form cancel operation, if cancel button is present on form.
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');
} else if ($fromform = $mform->get_data()) { // In this case you process validated data.
    $mailsubject = get_string('mailsubject', 'mod_exammanagement', [
        'systemname' => $moodlesystemname,
        'coursename' => $course->fullname,
        'subject' => $fromform->groupmessages_subject,
    ]);
    $mailtext = $fromform->groupmessages_content;

    $participants = helper::getexamparticipants($moduleinstance, ['mode' => 'moodle'], []);

    if ($mailsubject && $mailtext && $participants) {
        global $USER;
        $userfrom = $USER;

        foreach ($participants as $key => $participant) {

            $userto = helper::getmoodleuser($participant->moodleuserid);

            helper::sendsinglemessage($moduleinstance, $id, $course, $moodlesystemname, $userfrom, $userto, $mailsubject, $mailtext,
                'groupmessage');
        }

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
    $title = get_string('sendgroupmessage', 'mod_exammanagement');

    $PAGE->set_url('/mod/exammanagement/sendgroupmessage.php', ['id' => $id]);
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
        echo $OUTPUT->heading($title . ' ' . $OUTPUT->help_icon('sendgroupmessage', 'mod_exammanagement', ''), 4);
    } else {
        echo $OUTPUT->heading($title, 4);
    }

    // Output description.
    if ($moodleparticipantscount) {
        echo '<p>' . get_string('groupmessages_text', 'mod_exammanagement',
            ['systemname' => $moodlesystemname, 'participantscount' => $moodleparticipantscount]) . '</p>';

        // Output alerts.
        if ($nonemoodleparticipantscount) {
            $mailaddresses = helper::getnonemoodleparticipantsemailadresses($moduleinstance);

            echo '<div class="alert alert-warning alert-block fade in " role="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>';

            echo '<p>' . get_string('groupmessages_warning', 'mod_exammanagement', [
                'systemname' => helper::getmoodlesystemname(),
                'participantscount' => $nonemoodleparticipantscount,
                ]) . '</p>';

            echo '<a href="mailto:?bcc=';

            foreach ($mailaddresses as $address) {
                echo $address . ';';
            }

            echo '" role="button" class="btn btn-primary" title="' . get_string('send_manual_message', 'mod_exammanagement') .
                '">' . get_string('send_manual_message', 'mod_exammanagement') . '</a>';

            echo '</div>';
        }

        echo '<span class="mt-1"><hr></span>';

        // Set default data.
        $mform->set_data(['id' => $id]);

        // Display form.
        $mform->display();

    } else if ($nonemoodleparticipantscount) {
        $mailaddresses = helper::getnonemoodleparticipantsemailadresses($moduleinstance);

        echo '<p><strong>' . $nonemoodleparticipantscount . '</strong>' .
            get_string('groupmessages_warning_2', 'mod_exammanagement') . '</p>';

        echo '<a href="mailto:?bcc=';

        foreach ($mailaddresses as $address) {
            echo $address . ';';
        }

        echo '" role="button" class="btn btn-primary" title="' . get_string('send_manual_message', 'mod_exammanagement') .
            '">' . get_string('send_manual_message', 'mod_exammanagement') . '</a>';

        echo '<span class="col-sm-5"></span><a href="' . new moodle_url('/mod/exammanagement/view.php',
            ['id' => $id]) . '" class="btn btn-primary">' . get_string("cancel", "mod_exammanagement") .
            '</a>';

    }

    // Finish the page.
    echo $OUTPUT->footer();
}
