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
 * Allows teachers to choose rooms for an exammanagement.
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

// ... module instance id - should be named as the first character of the module
$e = optional_param('e', 0, PARAM_INT);

// Active page.
$pagenr = optional_param('page', 1, PARAM_INT);

// Id for custom room to be deleted.
$deletecustomroomid = optional_param('deletecustomroomid', 0, PARAM_TEXT);

// Id for default room to be deleted.
$deletedefaultroomid = optional_param('deletedefaultroomid', 0, PARAM_TEXT);

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
global $OUTPUT, $PAGE, $USER;

$canimportdefaultrooms = has_capability('mod/exammanagement:importdefaultrooms', $context);

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

if ($deletecustomroomid) {
    require_sesskey();

    if ($DB->record_exists('exammanagement_rooms', ['roomid' => $deletecustomroomid, 'moodleuserid' => $USER->id])) {
        if (!json_decode($moduleinstance->rooms) || !in_array($deletecustomroomid, json_decode($moduleinstance->rooms))) {
            $DB->delete_records('exammanagement_rooms', ['roomid' => $deletecustomroomid, 'moodleuserid' => $USER->id]);
        } else {
            redirect (new moodle_url('/mod/exammanagement/chooserooms.php', ['id' => $id]),
                get_string('room_deselected_as_examroom', 'mod_exammanagement'), null, 'error');
        }
    }
}

if ($deletedefaultroomid) {
    require_sesskey();

    if ($canimportdefaultrooms) {
        if ($DB->record_exists('exammanagement_rooms', ['roomid' => $deletedefaultroomid])) {
            $DB->delete_records('exammanagement_rooms', ['roomid' => $deletedefaultroomid]);
        }
    } else {
        redirect (new moodle_url('/mod/exammanagement/chooserooms.php', ['id' => $id]),
            get_string('nopermissions', 'mod_exammanagement'), null, 'error');
    }
}

// Get data for the form.
$allrooms = helper::getrooms($moduleinstance, 'all');
$displayrooms = helper::getrooms($moduleinstance, 'all', 'name', false, true, $pagenr);
$examrooms = helper::getrooms($moduleinstance, 'examrooms');

// Instantiate form.
require_once($CFG->dirroot . '/mod/exammanagement/classes/forms/chooserooms_form.php');
$mform = new mod_exammanagement_chooserooms_form(null, [
    'id' => $id,
    'e' => $e,
    'pagenr' => $pagenr,
    'allrooms' => $allrooms,
    'displayrooms' => $displayrooms,
    'examrooms' => $examrooms,
    'canimportdefaultrooms' => $canimportdefaultrooms,
    'moduleinstance' => $moduleinstance,
]);

// Form processing and displaying is done here.
if ($mform->is_cancelled()) {  // Handle form cancel operation, if cancel button is present on form.
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');
} else if ($fromform = $mform->get_data()) { // In this case you process validated data.

    $allrooms = get_object_vars($fromform);

    $roomsarray = $allrooms["rooms"];
    $checkedrooms = [];
    $uncheckedrooms = [];

    $oldrooms = json_decode($moduleinstance->rooms ?? '');

    if (!isset($oldrooms)) {
        $oldrooms = [];
    }

    foreach ($roomsarray as $key => $value) {
        if ($value == 1 && is_string($value)) {

            $roomname = explode('_', $key);
            $similiarrooms = $DB->get_records('exammanagement_rooms', ['name' => $roomname[0]]);

            foreach ($similiarrooms as $similiarroomobj) {
                if (isset($oldrooms) && in_array($similiarroomobj->roomid, $oldrooms) && $similiarroomobj->roomid != $key) {
                    redirect (new moodle_url('/mod/exammanagement/chooserooms.php', ['id' => $id]),
                        get_string('err_roomsdoubleselected', 'mod_exammanagement'), null, 'error');
                }
            }

            array_push($checkedrooms, $key);
        } else if ($value == 0) {
            array_push($uncheckedrooms, $key);
        }
    }

    // Reset places assignment if an exam room where participants are seated is deselected.
    if ($oldrooms) {
        $deselectedrooms = array_intersect($oldrooms, $uncheckedrooms); // Checking if some old exam rooms are deselected.
    } else {
        $deselectedrooms = null;
    }

    if (isset($deselectedrooms)) {

        $oldrooms = array_diff($oldrooms, $deselectedrooms);

        foreach ($deselectedrooms as $roomid) {

             // If there are participants that have places in deselected rooms: delete whole places assignment.
            if (helper::getparticipantscount($moduleinstance, 'room', $roomid)) {
                $moduleinstance->assignmentmode = null;

                $DB->set_field('exammanagement_participants', 'roomid', null, ['exammanagement' => $moduleinstance->id]);
                $DB->set_field('exammanagement_participants', 'roomname', null, ['exammanagement' => $moduleinstance->id]);
                $DB->set_field('exammanagement_participants', 'place', null, ['exammanagement' => $moduleinstance->id]);
                break;
            }
        }
    }

    $checkedrooms = array_unique(array_merge($checkedrooms, $oldrooms));

    sort($checkedrooms); // Sort checked rooms ids for saving in DB.

    $moduleinstance->rooms = json_encode($checkedrooms);

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
    $title = get_string('chooserooms', 'mod_exammanagement');

    $PAGE->set_url('/mod/exammanagement/chooserooms.php', ['id' => $id]);
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
        echo $OUTPUT->heading($title . ' ' . $OUTPUT->help_icon('chooserooms', 'mod_exammanagement', ''), 4);
    } else {
        echo $OUTPUT->heading($title, 4);
    }

    // Output buttons.
    echo '<div class="float-right">';
    if ($canimportdefaultrooms) {
        if ($allrooms) {
            echo '<a href="' . new moodle_url('/mod/exammanagement/exportdefaultrooms.php', ['id' => $id]) .
                '" class="btn btn-secondary" title="' . get_string("export_default_rooms", "mod_exammanagement") .
                '"><span class="d-none d-lg-block">' . get_string("export_default_rooms", "mod_exammanagement") .
                '</span><i class="fa fa-download d-lg-none" aria-hidden="true"></i></a>';
        }
        echo '<a href="' . new moodle_url('/mod/exammanagement/importdefaultrooms.php', ['id' => $id]) .
            '" class="btn btn-secondary ml-1" title="' . get_string("import_default_rooms", "mod_exammanagement") .
            '"><span class="d-none d-lg-block">' . get_string("import_default_rooms", "mod_exammanagement") .
            '</span><i class="fa fa-file-text d-lg-none" aria-hidden="true"></i></a>';

        echo '<a href="' . new moodle_url('/mod/exammanagement/editdefaultroom.php', ['id' => $id]) .
            '" class="btn btn-secondary ml-1" title="' . get_string("add_default_room", "mod_exammanagement") .
            '"><span class="d-none d-lg-block">' . get_string("add_default_room", "mod_exammanagement") .
            '</span><i class="fa fa-plus d-lg-none" aria-hidden="true"></i></a>';
    } else {
        echo '<a href="' . new moodle_url('/mod/exammanagement/addcustomroom.php', ['id' => $id]) .
            '" class="btn btn-secondary ml-1" title="' . get_string("add_custom_room", "mod_exammanagement") .
            '"><span class="d-none d-lg-block">' . get_string("add_custom_room", "mod_exammanagement") .
            '</span><i class="fa fa-plus d-lg-none" aria-hidden="true"></i></a>';
    }
    echo '</div>';

    // Output description.
    echo '<p>' . get_string("choose_rooms_str", "mod_exammanagement") . '</p>';

    // Output alerts.

    // Set default data.
    $mform->set_data(['id' => $id]);

    // Display form.
    $mform->display();

    // Finish the page.
    echo $OUTPUT->footer();
}
