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
 * Allows teachers to add course participants to an exammanagement.
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

// Check if requirements are met.
if (helper::isexamdatadeleted($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('err_examdata_deleted', 'mod_exammanagement'), null, 'error');
} else if (empty($courseparticipantsids)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('err_nocourseparticipants', 'mod_exammanagement'), null, 'error');
}

// Instantiate form.
require_once($CFG->dirroot . '/mod/exammanagement/classes/forms/addcourseparticipants_form.php');
$mform = new mod_exammanagement_addcourseparticipants_form(null, ['id' => $id, 'e' => $e]);

// Form processing and displaying is done here.
if ($mform->is_cancelled()) { // Handle form cancel operation, if cancel button is present on form.
    redirect(new moodle_url('/mod/exammanagement/viewparticipants.php', ['id' => $id]),
        get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

} else if ($fromform = $mform->get_data()) { // In this case you process validated data.

    // Get IDs of (deleted) participants.
    $participantids = helper::filtercheckedparticipants($fromform);

    if (isset($participantids['participants'])) {
        $newparticipantsids = $participantids['participants'];
    } else {
        $newparticipantsids = false;
    }

    if (isset($participantids['deletedparticipants'])) {
        $deletedparticipantsids = $participantids['deletedparticipants'];
    } else {
        $deletedparticipantsids = false;
    }

    if ($newparticipantsids || $deletedparticipantsids) {
        $users = [];

        if ($newparticipantsids) {

            foreach ($newparticipantsids as $participantid) {

                if (helper::checkifalreadyparticipant($moduleinstance, $participantid) == false) {
                    $user = new stdClass();
                    $user->exammanagement = $cm->instance;
                    $user->courseid = $course->id;
                    $user->categoryid = $moduleinstance->categoryid;
                    $user->moodleuserid = $participantid;
                    $user->headerid = 0;

                    // To be removed.
                    $dbman = $DB->get_manager();
                    $table = new \xmldb_table('exammanagement_participants');
                    $field = new \xmldb_field('plugininstanceid', XMLDB_TYPE_INTEGER, '10', null,
                        XMLDB_NOTNULL, null, null);
                    if ($dbman->field_exists($table, $field)) {
                        $user->plugininstanceid = 0; // For deprecated old version db version, should be removed.
                    }

                    array_push($users, $user);
                }
            }
        }

        if ($deletedparticipantsids) {
            foreach ($deletedparticipantsids as $identifier) {
                $temp = explode('_', $identifier);

                if ($temp[0] == 'mid') {
                    helper::deleteparticipant($moduleinstance, $temp[1], false);
                } else {
                    helper::deleteparticipant($moduleinstance, false, $temp[1]);
                }
            }
        }

        $DB->insert_records('exammanagement_participants', $users);

        redirect(new moodle_url('/mod/exammanagement/viewparticipants.php', ['id' => $id]),
            get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
    } else {
        redirect(new moodle_url('/mod/exammanagement/viewparticipants.php', ['id' => $id]),
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
    $title = get_string('addcourseparticipants', 'mod_exammanagement');

    $PAGE->set_url('/mod/exammanagement/addcourseparticipants.php', ['id' => $id]);
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
        echo $OUTPUT->heading($title . ' ' . $OUTPUT->help_icon('addcourseparticipants', 'mod_exammanagement', ''), 4);
    } else {
        echo $OUTPUT->heading($title, 4);
    }

    // Output description.
    echo '<p>' . get_string("view_added_and_course_partipicants", "mod_exammanagement") . '</p>';

    // Output alerts.
    echo '<div class="alert alert-warning alert-block fade in " role="alert">';
    echo '<button type="button" class="close" data-dismiss="alert">×</button>';
    echo  get_string("course_participant_import_preventing_text_export", "mod_exammanagement");
    echo '</div>';

    if (helper::placesassigned($moduleinstance)) {
        echo '<div class="alert alert-warning alert-block fade in " role="alert">';
        echo '<button type="button" class="close" data-dismiss="alert">×</button>';
        echo get_string("places_already_assigned_participants", "mod_exammanagement");
        echo '</div>';
    }

    // Set default data.

    // Set data if checkboxes should be checked.
    // SetDefault in the form is much more time consuming for big amount of participants.
    $defaultvalues = ['id' => $id];

    if (isset($courseparticipantsids)) {
        foreach ($courseparticipantsids as $id) {
            $defaultvalues['participants[' . $id . ']'] = true;
        }
    }

    $mform->set_data($defaultvalues);

    // Display form.
    $mform->display();

    // Finish the page.
    echo $OUTPUT->footer();
}
