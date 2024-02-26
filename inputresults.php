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
 * Allows teachers to enter results in an exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_exammanagement\ldap\ldapmanager;
use mod_exammanagement\local\helper;

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// Course_module ID, or ...
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module.
$e = optional_param('e', 0, PARAM_INT);

// The matrnr of the participant.
$input = optional_param('matrnr', 0, PARAM_TEXT);


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

$ldapmanager = ldapmanager::getinstance();

// If user has not entered the correct password: redirect to check password page.
if (isset($moduleinstance->password) &&
    (!isset($SESSION->loggedInExamOrganizationId) || $SESSION->loggedInExamOrganizationId !== $id)) {

    redirect(new moodle_url('/mod/exammanagement/checkpassword.php', ['id' => $id]), null, null, null);
}

// Check if requirements are met.
if (helper::isexamdatadeleted($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('err_examdata_deleted', 'mod_exammanagement'), null, 'error');
} else if (!$ldapmanager->isldapenabled()) {
    redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
        get_string('not_possible_no_matrnr', 'mod_exammanagement') . ' '.
        get_string('ldapnotenabled', 'mod_exammanagement'), null, 'error');
} else if (!$ldapmanager->isldapconfigured()) {
    redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
        get_string('not_possible_no_matrnr', 'mod_exammanagement') . ' '.
        get_string('ldapnotconfigured', 'mod_exammanagement'), null, 'error');
} else if (!helper::getparticipantscount($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
        get_string('no_participants_added', 'mod_exammanagement'), null, 'error');
} else if (!helper::gettasks($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
        get_string('no_tasks_configured', 'mod_exammanagement'), null, 'error');
}

$matrnr = false;
$case = '';
$result;
$firstname = '';
$lastname = '';

if ($input) {
    $filteredinput = preg_replace('/[^0-9]/', '', $input);

    if ($filteredinput !== $input) { // Input containes invalid chars.
        $case = 'novalidmatrnr';
    } else {    // Check if input is valid barcode and then convert barcoe to matrnr.
        $inputlength = strlen($filteredinput);

        if ($inputlength == 13) { // Input is correctly formated barcode.
            $checksum = helper::buildchecksumexamlabels(substr($input, 0, 12));

            if ($checksum == substr($input, -1)) { // If checksum is correct.
                $matrnr = substr($input, 5, -1); // Extract matrnr from barcode.
            } else {
                $case = 'novalidbarcode';
            }
        } else if ($inputlength <= 7) { // Input is probably a matrnr.
            $matrnr = $input;
        } else if ($inputlength) { // Input is probably a barcode but not correctly formatted (e. g. missing leading zeros).
            $paddedinput = str_pad($input, 13, "0", STR_PAD_LEFT);

            $checksum = helper::buildchecksumexamlabels(substr($paddedinput, 0, 12));

            if ($checksum == substr($paddedinput, -1)) { // If checksum is correct.
                $matrnr = substr($paddedinput, 5, -1); // Extract matrnr from barcode.
            } else {
                $case = 'novalidbarcode';
            }
        }

        if ($matrnr) {
            if (helper::checkifvalidmatrnr($matrnr)) {
                // Convert matrnr to user.
                $userlogin = false;
                $userid = false;

                $userlogin = $ldapmanager->getloginformatrnr($matrnr, 'enterresultsmatrnr');

                if ($userlogin) {
                    $userid = $DB->get_field('user', 'id', ['username' => $userlogin]);
                }

                $participant = false;

                // Get exam participant.
                if ($userid !== false && $userid !== null) {
                    $participant = helper::getexamparticipant($moduleinstance, $userid);
                } else if ($userlogin !== false && $userlogin !== null) {
                    $participant = helper::getexamparticipant($moduleinstance, false, $userlogin);
                }

                // If user is participant.
                if ($participant && helper::checkifalreadyparticipant($moduleinstance, $participant->moodleuserid, $userlogin)) {
                    $case = 'participant';

                    if ($userid !== false && $userid !== null) {
                        $user = helper::getmoodleuser($userid);
                        $firstname = $user->firstname;
                        $lastname = $user->lastname;
                    } else {
                        $firstname = $participant->firstname;
                        $lastname = $participant->lastname;
                    }

                    if ($participant->exampoints && $participant->examstate) { // If participants has results.
                        $case = 'participantwithresults';
                    }
                } else {
                    $case = 'noparticipant';
                    $matrnr = false;
                }
            } else {
                $case = 'novalidmatrnr';
                $matrnr = false;
            }
        }
    }
}

// Instantiate form.
require_once($CFG->dirroot . '/mod/exammanagement/classes/forms/inputresults_form.php');
$mform = new mod_exammanagement_inputresults_form(null, [
    'id' => $id,
    'e' => $e,
    'matrnr' => $matrnr,
    'firstname' => $firstname,
    'lastname' => $lastname,
    'moduleinstance' => $moduleinstance,
]);

// Form processing and displaying is done here.
if ($mform->is_cancelled()) { // Handle form cancel operation, if cancel button is present on form.
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        null, null, null);

} else if ($fromform = $mform->get_data()) { // In this case you process validated data.
    $matrval = $fromform->matrval;

    $userid = false;
    $userlogin = false;
    $participant = false;

    if ($matrval) {
        redirect(new moodle_url('/mod/exammanagement/inputresults.php', ['id' => $id, 'matrnr' => $fromform->matrnr]),
            null, null, null);
    } else {
        $userlogin = $ldapmanager->getloginformatrnr($fromform->matrnr, 'enterresultsmatrnr');

        if ($userlogin) {
            $userid = $DB->get_field('user', 'id', ['username' => $userlogin]);
        }

        // Get exam participant.
        if ($userid !== false && $userid !== null) {
            $participant = helper::getexamparticipant($moduleinstance, $userid);
        } else if ($userlogin !== false && $userlogin !== null) {
            $participant = helper::getexamparticipant($moduleinstance, false, $userlogin);
        }

        if ($participant) {
            $participant->examstate = json_encode($fromform->state);

            if ($fromform->state['nt'] == '1' || $fromform->state['fa'] == '1' || $fromform->state['ill'] == '1') {
                foreach ($fromform->points as $task => $points) {
                    $fromform->points[$task] = 0;
                }
            }

            $participant->exampoints = json_encode($fromform->points);
            $participant->timeresultsentered = time();

            $update = $DB->update_record('exammanagement_participants', $participant);
            if ($update) {
                redirect(new moodle_url('/mod/exammanagement/inputresults.php', ['id' => $id]), null, null, null);
            } else {
                redirect(new moodle_url('/mod/exammanagement/inputresults.php', ['id' => $id]),
                    get_string('alteration_failed', 'mod_exammanagement'), null, 'error');
            }
        } else {
            redirect(new moodle_url('/mod/exammanagement/inputresults.php', ['id' => $id]),
                get_string('noparticipant', 'mod_exammanagement'), null, 'error');
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
    $title = get_string('inputresults', 'mod_exammanagement');

    $PAGE->set_url('/mod/exammanagement/inputresults.php', ['id' => $id]);
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
        echo $OUTPUT->heading($title . ' ' . $OUTPUT->help_icon('inputresults', 'mod_exammanagement', ''), 4);
    } else {
        echo $OUTPUT->heading($title, 4);
    }

    // Output description.
    echo '<p>' . get_string("input_results_text", "mod_exammanagement") . '</p>';

    // Output alerts.
    if ($matrnr) {
        echo '<div class="alert alert-warning alert-block fade in " role="alert">
            <button type="button" class="close" data-dismiss="alert">×</button>' .
            get_string("confirm_matrnr", "mod_exammanagement") . '</div>';
    }

    // Set default data.
    switch ($case) {
        case 'participantwithresults':

            $states = json_decode($participant->examstate);
            $points = json_decode($participant->exampoints);

            $mform->set_data([
                'id' => $id,
                'matrval' => 0,
                'matrnr' => $matrnr,
                'state[nt]' => $states->nt,
                'state[fa]' => $states->fa,
                'state[ill]' => $states->ill,
            ]);

            foreach ($points as $key => $points) {
                $mform->set_data(['points[' . $key . ']' => $points]);
            }
            break;
        case 'participant':
            $mform->set_data(['id' => $id, 'matrval' => 0, 'matrnr' => $matrnr]);
            break;
        case 'noparticipant':
            $mform->set_data(['id' => $id, 'matrval' => 1]);
            \core\notification::add(get_string('noparticipant', 'mod_exammanagement'), 'error');
            break;
        case 'novalidmatrnr':
            $mform->set_data(['id' => $id, 'matrval' => 1]);
            \core\notification::add(get_string('invalid_matrnr', 'mod_exammanagement'), 'error');
            break;
        case 'novalidbarcode':
            $mform->set_data(['id' => $id, 'matrval' => 1]);
            \core\notification::add(get_string('invalid_barcode', 'mod_exammanagement'), 'error');
            break;
        default:
            $mform->set_data(['id' => $id, 'matrval' => 1]);
            break;
    }

    // Display form.
    $mform->display();

    // Finish the page.
    echo $OUTPUT->footer();
}
