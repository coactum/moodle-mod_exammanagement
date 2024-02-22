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
 * Allows teachers to assign places to participants in an exammanagement.
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

// Unassign existing places.
$uap = optional_param('uap', 0, PARAM_INT);

// Manually assign places.
$map = optional_param('map', 0, PARAM_INT);

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
} if (!helper::getroomscount($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('no_rooms_added', 'mod_exammanagement'), null, 'error');
} else if (!helper::getparticipantscount($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('no_participants_added', 'mod_exammanagement'), null, 'error');
} else if (helper::gettotalnumberofseats($moduleinstance) == 0) {
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('no_rooms_added', 'mod_exammanagement'), null, 'error');
}

// Reset all existing places for participants.
if ($uap) {
    require_sesskey();
    $DB->set_field('exammanagement_participants', 'roomid', null, ['exammanagement' => $moduleinstance->id]);
    $DB->set_field('exammanagement_participants', 'roomname', null, ['exammanagement' => $moduleinstance->id]);
    $DB->set_field('exammanagement_participants', 'place', null, ['exammanagement' => $moduleinstance->id]);
    $moduleinstance->assignmentmode = null;
    $DB->update_record("exammanagement", $moduleinstance);
}

$assignedplacescount = helper::getassignedplacescount($moduleinstance);

// Instantiate form.
require_once($CFG->dirroot . '/mod/exammanagement/classes/forms/assignplaces_form.php');
if ($map) {
    $mform = new mod_exammanagement_assignplaces_form(null,
        ['id' => $id, 'e' => $e, 'map' => $map, 'pagenr' => $pagenr]);
} else {
    $mform = new mod_exammanagement_assignplaces_form(null,
        ['id' => $id, 'e' => $e, 'map' => 0, 'assignedplacescount' => $assignedplacescount]);
}

// Form processing and displaying is done here.
if ($mform->is_cancelled()) { // Handle form cancel operation, if cancel button is present on form.
    redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
        get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');
} else if ($fromform = $mform->get_data()) { // In this case you process validated data.
    if (isset($map) && $map) {
        $examrooms = helper::getrooms($moduleinstance, 'examrooms');
        $participants = helper::getexamparticipants($moduleinstance, ['mode' => 'all'], []);

        foreach ($participants as $participant) {
            if (isset($fromform->rooms[$participant->id])) {
                if ($fromform->rooms[$participant->id] !== 'not_selected' && $fromform->places[$participant->id]) {

                    if (isset($participant->moodleuserid)) {
                        $participant->login = null;
                        $participant->firstname = null;
                        $participant->lastname = null;
                    }

                    $participant->roomid = $fromform->rooms[$participant->id];
                    $participant->roomname = $examrooms[$fromform->rooms[$participant->id]]->name;
                    $participant->place = $fromform->places[$participant->id];

                    $DB->update_record('exammanagement_participants', $participant);
                } else {
                    if (isset($participant->moodleuserid)) {
                        $participant->login = null;
                        $participant->firstname = null;
                        $participant->lastname = null;
                    }

                    $participant->roomid = null;
                    $participant->roomname = null;
                    $participant->place = null;

                    $DB->update_record('exammanagement_participants', $participant);
                }
            }
        }

        $assignmentmode = $moduleinstance->assignmentmode;

        if (isset($assignmentmode)) {
            if (strlen($assignmentmode) == 3) {
                $moduleinstance->assignmentmode = substr($assignmentmode, 0, -1) . '1';
            } else if (strlen($assignmentmode) == 2) {
                $moduleinstance->assignmentmode = $assignmentmode . '1';
            } else if (strlen($assignmentmode) == 1) {
                $moduleinstance->assignmentmode = $assignmentmode . '01';
            }
        } else {
            $moduleinstance->assignmentmode = '4';
        }

        $DB->update_record("exammanagement", $moduleinstance);

        redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
            get_string('operation_successfull', 'mod_exammanagement'), null, 'success');

    } else {
        // All existing seat assignments should be deleted.
        if (!(isset($fromform->keep_seat_assignment) && $fromform->keep_seat_assignment)) {
            $DB->set_field('exammanagement_participants', 'roomid', null, ['exammanagement' => $moduleinstance->id]);
            $DB->set_field('exammanagement_participants', 'roomname', null, ['exammanagement' => $moduleinstance->id]);
            $DB->set_field('exammanagement_participants', 'place', null, ['exammanagement' => $moduleinstance->id]);
            $moduleinstance->assignmentmode = null;
            $DB->update_record("exammanagement", $moduleinstance);
            $keepseatassignment = false;

            // Get all exam participants sorted by sortmode.
            $participants = helper::getexamparticipants($moduleinstance,
                ['mode' => 'all'], ['matrnr'], $fromform->assignment_mode_places);
        } else {
            $moduleinstance->assignmentmode = null;
            $keepseatassignment = true;
            // Todo: get only exam participants without places sorted by sortmode.
            $participants = helper::getexamparticipants($moduleinstance,
                ['mode' => 'no_seats_assigned'], ['matrnr'], $fromform->assignment_mode_places);
        }

        $roommode = '0';

        if (isset($fromform->assignment_mode_rooms) && $fromform->assignment_mode_rooms === '1') {
            $examrooms = helper::getrooms($moduleinstance, 'examrooms', 'places_smalltobig', $keepseatassignment);
            $roommode = '1';
        } else if (isset($fromform->assignment_mode_rooms) && $fromform->assignment_mode_rooms === '2') {
            $examrooms = helper::getrooms($moduleinstance, 'examrooms', 'places_bigtosmall', $keepseatassignment);
            $roommode = '2';
        } else {
            $examrooms = helper::getrooms($moduleinstance, 'examrooms', 'places_bigtosmall', $keepseatassignment);
        }

        if (!$participants) {
            redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
                get_string('no_participants_added', 'mod_exammanagement'), null, 'error');
        }

        $participantscount = 0;

        if ($examrooms) {

            foreach ($examrooms as $room) {
                if ($room) {
                    foreach ($participants as $key => $participant) {

                        if ($key >= $participantscount) {

                            if (isset($participant->moodleuserid)) {
                                $participant->login = null;
                                $participant->firstname = null;
                                $participant->lastname = null;
                            }

                            unset($participant->matrnr);

                            $participant->roomid = $room->roomid;
                            $participant->roomname = $room->name;
                            $participant->place = array_shift($room->places);

                            // Set room and place.
                            $DB->update_record('exammanagement_participants', $participant);

                            $participantscount += 1;

                            if ($room->places == null) {  // If all places of room are assigned.
                                break;
                            }

                        } else if ($participantscount == count($participants)) { // If all users have a place.
                            break 2;
                        }
                    }
                }

            }
        }

        // Save sort modes.
        switch ($fromform->assignment_mode_places) {
            case 'name':
                $modeids = 1 . $roommode . $keepseatassignment;
                break;
            case 'matrnr':
                $modeids = 2 . $roommode . $keepseatassignment;
                break;
            case 'random':
                $modeids = 3 . $roommode . $keepseatassignment;
                break;
            default:
                $modeids = false;
        }

        if ($modeids) {
            $moduleinstance->assignmentmode = $modeids;
            $DB->update_record("exammanagement", $moduleinstance);
        }

        if ($participantscount < count($participants)) {    // If users are left without a room.
            redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
                get_string('participants_missing_places', 'mod_exammanagement'), null, 'error');
        } else {
            redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
                get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
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
    $title = get_string('assignplaces', 'mod_exammanagement');

    $PAGE->set_url('/mod/exammanagement/assignplaces.php', ['id' => $id, 'map' => $map]);
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
        echo $OUTPUT->heading($title . ' ' . $OUTPUT->help_icon('assignplaces', 'mod_exammanagement', ''), 4);
    } else {
        echo $OUTPUT->heading($title, 4);
    }

    // Output buttons.
    if ($map) {
        echo '<a href="' . new moodle_url('/mod/exammanagement/assignplaces.php',
            ['id' => $id]) . '" class="btn btn-primary float-right mr-1" title="' .
            get_string('assign_places', 'mod_exammanagement') . '"><span class="d-none d-sm-block">' .
            get_string('assign_places', 'mod_exammanagement') .
            '</span><i class="fa fa-repeat d-sm-none" aria-hidden="true"></i></a>';
    } else {
        echo '<a href="' . new moodle_url('/mod/exammanagement/assignplaces.php',
            ['id' => $id, 'map' => true]) . '" class="btn btn-primary float-right mr-1" title="' .
            get_string('assign_places_manually', 'mod_exammanagement') . '"><span class="d-none d-sm-block">' .
            get_string('assign_places_manually', 'mod_exammanagement') .
            '</span><i class="fa fa-repeat d-sm-none" aria-hidden="true"></i></a>';
    }

    if ($assignedplacescount) {
        echo '<a href="assignplaces.php?id=' . $id . '&uap=1&sesskey=' . sesskey() .
            '" role="button" class="btn btn-secondary float-right mr-1" title="' .
            get_string('revert_places_assignment', 'mod_exammanagement') . '"><span class="d-none d-md-block">' .
            get_string('revert_places_assignment', 'mod_exammanagement') .
            '</span><i class="fa fa-trash d-md-none" aria-hidden="true"></i></a>';
    }

    // Output description.
    echo '<p>' . get_string('assign_places_text', 'mod_exammanagement') . '</p>';

    // Output alerts.

    // Set default data.
    $mform->set_data(['id' => $id]);

    // Display form.
    $mform->display();

    // Finish the page.
    echo $OUTPUT->footer();
}
