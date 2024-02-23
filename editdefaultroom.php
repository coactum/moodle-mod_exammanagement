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
 * Allows admins to add or edit a default room stored for the plugin.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_exammanagement\local\helper;

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// Course_module ID, or.
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module.
$e = optional_param('e', 0, PARAM_INT);

// The id of the room that should be edited.
$roomid = optional_param('roomid', 0, PARAM_TEXT);

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

// Get room.
if ($roomid) {
    $room = $DB->get_record('exammanagement_rooms', ['roomid' => $roomid]);

    if (isset($room) && $room) {
        if ($room->type == 'defaultroom') {
            $roomname = $room->name;
            $places = json_decode($room->places);
            $placescount = count($places);
            $description = $room->description;
            if (isset($places) && $placescount !== 0) {
                $placespreview = implode(',', $places);
            } else {
                $placespreview = false;
            }

            $roomplanavailable = base64_decode($room->seatingplan);
        } else {
            redirect (new moodle_url('/mod/exammanagement/chooserooms.php', ['id' => $id]),
                get_string('no_editable_default_room', 'mod_exammanagement'), null, 'error');
        }
    }
}

// Instantiate form.
require_once($CFG->dirroot . '/mod/exammanagement/classes/forms/editdefaultroom_form.php');
if ($roomid && $room && $room->type == 'defaultroom') {
    $mform = new mod_exammanagement_editdefaultroom_form(null, [
        'id' => $id,
        'e' => $e,
        'placescount' => $placescount,
        'placespreview' => $placespreview,
        'roomplanavailable' => $roomplanavailable,
        'existingroom' => true,
    ]);
} else {
    $mform = new mod_exammanagement_editdefaultroom_form(null, ['id' => $id, 'e' => $e, 'existingroom' => false]);
}

// Form processing and displaying is done here.
if ($mform->is_cancelled()) { // Handle form cancel operation, if cancel button is present on form.
    redirect (new moodle_url('/mod/exammanagement/chooserooms.php', ['id' => $id]),
        get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

} else if ($fromform = $mform->get_data()) { // In this case you process validated data.

    $roomid = $fromform->roomid;
    $roomname = $fromform->roomname;
    $description = $fromform->description;

    if (isset($fromform->editplaces)) {
        $editplaces = $fromform->editplaces;
    } else {
        $editplaces = 1;
    }

    $placesmode = $fromform->placesmode;

    if ($editplaces == 1) {
        if ($placesmode == 'default') {
            $placesroom = $fromform->placesroom;
            $placesfree = $fromform->placesfree;
        }

        if ($placesmode == 'rows') {
            $rowscount = $fromform->rowscount;
            $placesrow = $fromform->placesrow;
            $placesfree = $fromform->placesfree;
            $rowsfree = $fromform->rowsfree;
        }

        if ($placesmode == 'all_individual') {
            $places = $fromform->places;
        }
    }

    $defaultroomsvg = $mform->get_file_content('defaultroom_svg');

    // If default room exists and should be edited.
    if ($fromform->existingroom == true && $DB->record_exists('exammanagement_rooms', ['roomid' => $roomid])) {

        $room = $DB->get_record('exammanagement_rooms', ['roomid' => $roomid]);

        $room->name = $roomname;
        $room->description = $description;

        if ($editplaces == 1) {

            if ($placesmode == 'default') {
                $places = [];

                for ($i = 1; $i <= $placesroom; $i += $placesfree + 1) {

                    array_push($places, strval($i));
                }

                $room->places = json_encode($places);
            }

            if ($placesmode == 'rows') {
                $places = [];

                for ($i = 1; $i <= $rowscount; $i = $i + 1 + $rowsfree) {
                    for ($j = 1; $j <= $placesrow; $j += $placesfree + 1) {
                        array_push($places, 'R' . str_pad ( strval($i), 2, '0', STR_PAD_LEFT ) .
                            '/P' . str_pad ( strval($j), 2, '0', STR_PAD_LEFT ));
                    }
                }

                $room->places = json_encode($places);
            }

            if ($placesmode == 'all_individual') {
                $places = explode(',', $places);
                $places = array_values(array_filter($places, function($value) {
                    return !is_null($value) && $value !== '' && $value !== ' ' && $value !== '  ';
                }));

                $room->places = json_encode($places);
            }
        }

        if (isset($defaultroomsvg) && $defaultroomsvg !== false) {
            $room->seatingplan = base64_encode(str_replace(["\r\n", "\r", "\n"], '', $defaultroomsvg));
        }

        $room->misc = json_encode(['timelastmodified' => time()]);

        $update = $DB->update_record('exammanagement_rooms', $room);

        if ($update) {
            redirect(new moodle_url('/mod/exammanagement/chooserooms.php', ['id' => $id]),
                get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
        } else {
            redirect(new moodle_url('/mod/exammanagement/chooserooms.php', ['id' => $id]),
                get_string('alteration_failed', 'mod_exammanagement'), null, 'error');
        }
    } else { // If default room doesn't exists and should be created.

        $room = new stdClass();
        $room->roomid = $roomid;
        $room->name = $roomname;
        $room->description = $description;

        if ($placesmode == 'default') {
            $places = [];

            for ($i = 1; $i <= $placesroom; $i += $placesfree + 1) {
                array_push($places, strval($i));
            }

            $room->places = json_encode($places);
        }

        if ($placesmode == 'rows') {
            $places = [];

            for ($i = 1; $i <= $rowscount; $i = $i + 1 + $rowsfree) {
                for ($j = 1; $j <= $placesrow; $j += $placesfree + 1) {
                    array_push($places, 'R' . str_pad ( strval($i), 2, '0', STR_PAD_LEFT ) .
                        '/P' . str_pad ( strval($j), 2, '0', STR_PAD_LEFT ));
                }
            }

            $room->places = json_encode($places);
        }

        if ($placesmode == 'all_individual' && $places !== 0) {
            $places = explode(',', $places);
            $places = array_values(array_filter($places, function($value) {
                return !is_null($value) && $value !== '' && $value !== ' ' && $value !== '  ';
            }));
            $room->places = json_encode($places);
        }

        if (isset($defaultroomsvg)) {
            $room->seatingplan = base64_encode(str_replace(["\r\n", "\r", "\n"], '', $defaultroomsvg));
        }

        $room->type = 'defaultroom';
        $room->moodleuserid = null;
        $room->misc = json_encode(['timelastmodified' => time()]);

        $import = $DB->insert_record('exammanagement_rooms', $room);

        if ($import) {
            redirect(new moodle_url('/mod/exammanagement/chooserooms.php', ['id' => $id]),
                get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
        } else {
            redirect(new moodle_url('/mod/exammanagement/chooserooms.php', ['id' => $id]),
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
    $title = get_string('editdefaultroom', 'mod_exammanagement');

    $PAGE->set_url('/mod/exammanagement/editdefaultroom.php', ['id' => $id]);
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
        echo $OUTPUT->heading($title . ' ' . $OUTPUT->help_icon('editdefaultroom', 'mod_exammanagement', ''), 4);
    } else {
        echo $OUTPUT->heading($title, 4);
    }

    // Output description.
    echo '<p>' . get_string("edit_defaultroom_str", "mod_exammanagement") . '</p>';

    // Output alerts.

    // Set default data.
    if ($roomid) {
        if (isset($room) && $room !== false && $room->type == 'defaultroom') {
            $mform->set_data([
                'id' => $id,
                'roomid' => $roomid,
                'roomname' => $roomname,
                'placescount' => $placescount,
                'description' => $description,
                'places' => $placespreview,
                'existingroom' => true,
            ]);
        } else {
            $mform->set_data(['id' => $id, 'existingroom' => false]);
        }
    } else {
        $mform->set_data(['id' => $id, 'existingroom' => false]);
    }

    // Display form.
    $mform->display();

    // Finish the page.
    echo $OUTPUT->footer();
}
