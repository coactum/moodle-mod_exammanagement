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
 * Outputs a list of all participants in an exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_exammanagement\output\exammanagement_pagebar;
use mod_exammanagement\local\helper;

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// Course_module ID, or ...
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module.
$e = optional_param('e', 0, PARAM_INT);

// If all participants should be deleted.
$dap = optional_param('dap', 0, PARAM_INT);

// If participant with a certain login should be deleted.
$dplogin = optional_param('dplogin', 0, PARAM_TEXT);
// If participant with a certain moodle id should be deleted.
$dpmid = optional_param('dpmid', 0, PARAM_INT);

// Active page.
$pagenr = optional_param('page', 1, PARAM_INT);

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

// Check if requirements are met.
if (helper::isexamdatadeleted($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('err_examdata_deleted', 'mod_exammanagement'), null, 'error');
}

// Delete all participants.
if ($dap) {
    require_sesskey();

    $moduleinstance->importfileheaders = null;
    $moduleinstance->assignmentmode = null;
    $DB->update_record("exammanagement", $moduleinstance);

    if ($DB->record_exists('exammanagement_participants', ['exammanagement' => $moduleinstance->id])) {
        $DB->delete_records('exammanagement_participants', ['exammanagement' => $moduleinstance->id]);
    }

    redirect(new moodle_url('/mod/exammanagement/viewparticipants.php', ['id' => $id]), null, null, null);
}

// Delete single participant.
if ($dpmid) {
    require_sesskey();
    helper::deleteparticipant($moduleinstance, $dpmid, false);
} else if ($dplogin) {
    require_sesskey();
    helper::deleteparticipant($moduleinstance, false, $dplogin);
}

// Set $PAGE.
$plugintype = get_string('modulename', 'mod_exammanagement');
$modulename = format_string($moduleinstance->name, true, [
    'context' => $context,
]);
$title = get_string('viewparticipants', 'mod_exammanagement');

$PAGE->set_url('/mod/exammanagement/viewparticipants.php', ['id' => $id]);
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
    echo $OUTPUT->heading($title . ' ' . $OUTPUT->help_icon('viewparticipants', 'mod_exammanagement', ''), 4);
} else {
    echo $OUTPUT->heading($title, 4);
}

// Output buttons.

$allparticipants = helper::getexamparticipants($moduleinstance, ['mode' => 'all'], []);
$participants = helper::getexamparticipants($moduleinstance, ['mode' => 'all'], ['matrnr', 'profile', 'groups'],
    'name', true, $pagenr);

echo('<div class="float-right">');

if (!empty(helper::getcourseparticipantsids($id))) {
    echo('<a href="' . new moodle_url('/mod/exammanagement/addcourseparticipants.php', ['id' => $id]) .
        '" class="btn btn-primary float-right mr-1" role="button" title="' .
        get_string("import_course_participants_optional", "mod_exammanagement") .
        '"><span class="d-none d-xl-block">' . get_string("import_course_participants_optional", "mod_exammanagement") .
        '</span><i class="fa fa-user d-xl-none" aria-hidden="true"></i></a>');
}

if (get_config('mod_exammanagement', 'enableldap')) {
    echo('<a href="' . new moodle_url('/mod/exammanagement/addparticipants.php', ['id' => $id]) .
        '" role="button" class="btn btn-primary float-right mr-1" title="' .
        get_string("import_participants_from_file_recommended", "mod_exammanagement") .
        '"><span class="d-none d-xl-block">' . get_string("import_participants_from_file_recommended", "mod_exammanagement") .
        '</span><i class="fa fa-file-text d-xl-none" aria-hidden="true"></i></a>');
}

if ($participants) {
    echo('<a href="' . new moodle_url('/mod/exammanagement/converttogroup.php', ['id' => $id]) .
        '" role="button" class="btn btn-secondary mr-3" title="' . get_string("convert_to_group", "mod_exammanagement") .
        '"><span class="d-none d-xl-block">' . get_string("convert_to_group", "mod_exammanagement") .
        '</span><i class="fa fa-users d-xl-none" aria-hidden="true"></i></a>');
}

echo('</div>');

// Output description.
echo '<p>' . get_string("view_added_partipicants", "mod_exammanagement") . '</p>';

// List of participants.
$i = helper::getpagecount() * ($pagenr - 1) + 1;

if ($participants) {

    $coursegroups = groups_get_all_groups($course->id);

    if (count($coursegroups) > 0) {
        $coursegroups = true;
    } else {
        $coursegroups = false;
    }

    $pagebar = new exammanagement_pagebar($id,
        new moodle_url('/mod/exammanagement/viewparticipants.php', ['id' => $id]),
        $allparticipants, count($participants), $pagenr);

    echo $OUTPUT->render($pagebar);

    echo('<div class="table-responsive">');
    echo('<table class="table table-striped exammanagement_table">');
    echo('<thead class="exammanagement_tableheader exammanagement_brand_backgroundcolor"><th scope="col">#</th><th scope="col">' .
        get_string("participant", "mod_exammanagement") . '</th><th scope="col">' .
        get_string("matriculation_number", "mod_exammanagement") . '</th>');

    if ($coursegroups) {
        echo('<th scope="col">' . get_string("course_groups", "mod_exammanagement") . '</th>');
    }

    echo('<th scope="col">' . get_string("import_state", "mod_exammanagement") .
        '</th><th scope="col" class="exammanagement_table_whiteborder_left"></th></thead>');

    echo('<tbody>');

    // Show participants.
    if ($participants) {

        $courseparticipants = helper::getcourseparticipantsids($id);
        $nonecourseparticipants = [];

        foreach ($participants as $key => $participant) {

            if (!isset($participant->moodleuserid)) {
                $participant->state = 'state_added_to_exam_no_moodle';
            } else if ($courseparticipants && in_array($participant->moodleuserid, $courseparticipants)) {
                // Participant is course participant.
                $participant->state = 'state_added_to_exam';
            } else {
                $participant->state = 'state_added_to_exam_no_course';
            }

            echo('<tr>');
            echo('<th scope="row" id="' . $i . '">' . $i . '</th>');

            if ($participant->state == 'state_added_to_exam') {
                echo('<td>' . $participant->profile . '</td>');
            } else if ($participant->state == 'state_added_to_exam_no_course') {
                $moodleuser = helper::getmoodleuser($participant->moodleuserid);
                $image = $OUTPUT->user_picture($moodleuser, ['courseid' => false, 'link' => false, 'includefullname' => true]);
                echo('<td>' . $image . '</td>');
            } else if ($participant->state == 'state_added_to_exam_no_moodle') {
                echo('<td>' . $participant->firstname . ' ' . $participant->lastname . '</td>');
            }

            echo('<td>' . $participant->matrnr . '</td>');

            if ($coursegroups) {
                if ($participant->state == 'state_added_to_exam') {
                    echo('<td style="width: 45%">' . $participant->groups . '</td>');
                } else {
                    echo('<td> - </td>');
                }
            }

            if ($participant->state == 'state_added_to_exam') {
                echo('<td>' . get_string($participant->state, "mod_exammanagement") . '</td>');

                echo('<td class="exammanagement_brand_bordercolor_left"><a href="' .
                    new moodle_url('/mod/exammanagement/viewparticipants.php',
                    ['id' => $id, 'dpmid' => $participant->moodleuserid, 'sesskey' => sesskey()]) .
                    '" onClick="javascript:return confirm(\'' . get_string("participant_deletion_warning", "mod_exammanagement") .
                    '\');" title="' . get_string("delete_participant", "mod_exammanagement") .
                    '"><i class="fa fa-2x fa-trash" aria-hidden="true"></i></a></td>');

            } else if ($participant->state === 'state_added_to_exam_no_course') {
                echo('<td>' . get_string("state_added_to_exam_no_course", "mod_exammanagement") . ' ' .
                    $OUTPUT->help_icon('state_added_to_exam_no_course', 'mod_exammanagement', '') . '</td>');

                echo('<td class="exammanagement_brand_bordercolor_left"><a href="' .
                    new moodle_url('/mod/exammanagement/viewparticipants.php',
                    ['id' => $id, 'dpmid' => $participant->moodleuserid, 'sesskey' => sesskey()]) .
                    '" onClick="javascript:return confirm(\'' . get_string("participant_deletion_warning", "mod_exammanagement") .
                    '\');" title="' . get_string("delete_participant", "mod_exammanagement") .
                    '"><i class="fa fa-2x fa-trash" aria-hidden="true"></i></a></td>');

            } else if ($participant->state === 'state_added_to_exam_no_moodle') {
                echo('<td>' . get_string("state_added_to_exam_no_moodle", "mod_exammanagement",
                    ['systemname' => helper::getmoodlesystemname()]) . ' ' .
                    $OUTPUT->help_icon('state_added_to_exam_no_moodle', 'mod_exammanagement', '') . '</td>');

                echo('<td class="exammanagement_brand_bordercolor_left"><a href="' .
                    new moodle_url('/mod/exammanagement/viewparticipants.php',
                    ['id' => $id, 'dplogin' => $participant->login, 'sesskey' => sesskey()]) .
                    '" onClick="javascript:return confirm(\'' . get_string("participant_deletion_warning", "mod_exammanagement") .
                    '\');" title="' . get_string("delete_participant", "mod_exammanagement") .
                    '"><i class="fa fa-2x fa-trash" aria-hidden="true"></i></a></td>');
            }

            echo('</tr>');

            $i++;
        }
    }

    echo('</tbody></table></div>');

} else {
    echo('<div class="row"><p class="col-12 text-xs-center">' .
        get_string("no_participants_added_page", "mod_exammanagement") . '</p></div>');
}

echo('<div class="row"><span class="col-md-3"></span><span class="col-md-9"><a href="' .
     new moodle_url('/mod/exammanagement/view.php', ['id' => $id]) . '" class="btn btn-primary">' .
     get_string("cancel", "mod_exammanagement") . '</a>');

if ($participants) {
    echo ('<a href="' . new moodle_url('/mod/exammanagement/viewparticipants.php',
        ['id' => $id, 'dap' => 1, 'sesskey' => sesskey()]) .
        '" class="btn btn-secondary ml-1" onClick="javascript:return confirm(\'' .
        get_string("all_participants_deletion_warning", "mod_exammanagement") . '\');">' .
        get_string("delete_all_participants", "mod_exammanagement") . '</a></div>');
}

echo('</span>');

// Finish the page.
echo $OUTPUT->footer();
