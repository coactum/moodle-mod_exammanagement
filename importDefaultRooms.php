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
 * Allows admins to import default rooms to the exammanagements.
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

require_capability('mod/exammanagement:importdefaultrooms', $context);

// Get global and construct helper objects.
global $DB, $OUTPUT, $PAGE;

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

// Instantiate form.
require_once($CFG->dirroot . '/mod/exammanagement/classes/forms/importdefaultrooms_form.php');
$mform = new mod_exammanagement_importdefaultrooms_form(null, ['id' => $id, 'e' => $e]);

$defaultroomsexist = $DB->count_records_select('exammanagement_rooms', "type = 'defaultroom'");

// Form processing and displaying is done here.
if ($mform->is_cancelled()) { // Handle form cancel operation, if cancel button is present on form.
    redirect (new moodle_url('/mod/exammanagement/chooserooms.php', ['id' => $id]),
        get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');
} else if ($fromform = $mform->get_data()) { // In this case you process validated data.

    // Retrieve file from form.
    $defaultroomsfile = $mform->get_file_content('defaultrooms_list');

    if ($defaultroomsfile) {

        if ($defaultroomsexist) {
            $DB->delete_records_select("exammanagement_rooms", "type = 'defaultroom'");
        }

        $filecontent = explode(PHP_EOL, $defaultroomsfile); // Separate lines.

        foreach ($filecontent as $key => $roomstr) {

            $roomparameters = explode('*', $roomstr);

            $room = new stdClass();
            $room->roomid = $roomparameters[0];
            $room->name = $roomparameters[1];
            $room->description = $roomparameters[2];

            if (isset($roomparameters[4]) && $roomparameters[4] !== ''
                && json_encode($roomparameters[4]) !== '"\r"'
                && json_encode($roomparameters[4]) !== '"\n"'
                && json_encode($roomparameters[4]) !== '"\r\n"') {

                $svgstr = base64_encode($roomparameters[4]);
            } else {
                $svgstr = '';
            }

            $room->seatingplan = $svgstr;
            $room->places = $roomparameters[3];
            $room->type = 'defaultroom';
            $room->moodleuserid = null;
            $room->misc = json_encode(['timelastmodified' => time()]);

            $import = $DB->insert_record('exammanagement_rooms', $room); // Bulkrecord insert too big.
        }

        if ($import) {
            redirect (new moodle_url('/mod/exammanagement/chooserooms.php', ['id' => $id]),
                get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
        } else {
            redirect (new moodle_url('/mod/exammanagement/chooserooms.php', ['id' => $id]),
                get_string('alteration_failed', 'mod_exammanagement'), null, 'error');
        }
    }

} else {
    // This branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.

    // Set $PAGE.
    $plugintype = get_string('modulename', 'mod_exammanagement');
    $modulename = format_string($moduleinstance->name, true, [
        'context' => $context,
    ]);
    $title = get_string('importdefaultrooms', 'mod_exammanagement');

    $PAGE->set_url('/mod/exammanagement/importdefaultrooms.php', ['id' => $id]);
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
        echo $OUTPUT->heading($title . ' ' . $OUTPUT->help_icon('importdefaultrooms', 'mod_exammanagement', ''), 4);
    } else {
        echo $OUTPUT->heading($title, 4);
    }

    // Output description.
    echo '<p>' . get_string("import_default_rooms_str", "mod_exammanagement") . '</p>';

    // Output alerts.
    if ($defaultroomsexist) {
        echo '<div class="alert alert-warning alert-block fade in " role="alert">
            <button type="button" class="close" data-dismiss="alert">×</button>' .
            get_string("default_rooms_already_exists", "mod_exammanagement") . '</div>';
    }

    // Set default data.
    $mform->set_data(['id' => $id]);

    // Display form.
    $mform->display();

    // Finish the page.
    echo $OUTPUT->footer();
}
