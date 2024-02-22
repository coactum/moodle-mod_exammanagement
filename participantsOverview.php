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
 * Outputs the participants overview for an exammanagement.
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

// If no points are entered.
$pne = optional_param('pne', 1, PARAM_INT);

// If no bonus points are entered.
$bpne = optional_param('bpne', 1, PARAM_INT);

// If participants can be edited.
$epm = optional_param('epm', 0, PARAM_INT);

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
} else if (!helper::getparticipantscount($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
        get_string('no_participants_added', 'mod_exammanagement'), null, 'error');
}

// Instantiate Form.
require_once($CFG->dirroot . '/mod/exammanagement/classes/forms/participantsoverview_form.php');
if ($epm) {
    $mform = new mod_exammanagement_participantsoverview_form(null, [
        'id' => $id,
        'e' => $e,
        'epm' => $epm,
        'pagenr' => $pagenr,
        'moduleinstance' => $moduleinstance,
    ]);
} else {
    $mform = new mod_exammanagement_participantsoverview_form(null, [
        'id' => $id,
        'e' => $e,
        'pagenr' => $pagenr,
        'moduleinstance' => $moduleinstance,
    ]);
}

// Form processing and displaying is done here.
if ($mform->is_cancelled()) { // Handle form cancel operation, if cancel button is present on form.
    redirect (new moodle_url('/mod/exammanagement/participantsoverview.php', ['id' => $id]),
        get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

} else if ($fromform = $mform->get_data()) { // In this case you process validated data.
    $participants = helper::getexamparticipants($moduleinstance, ['mode' => 'all'], []);
    $updatedcount = 0;

    foreach ($participants as $participant) {
        if (isset($fromform->state[$participant->id]) || isset($fromform->bonuspoints[$participant->id])
            || isset($fromform->bonussteps[$participant->id]) || isset($fromform->bonuspoints_entered[$participant->id])) {

            $oldparticipant = clone $participant;

            $misc = (array) json_decode($moduleinstance->misc ?? '');

            if (!isset($misc['mode'])) {
                if (isset($fromform->state[$participant->id]) && $fromform->state[$participant->id] !== 'not_set') {
                    switch ($fromform->state[$participant->id]) {
                        case 'normal':
                            $examstate = new stdClass;
                            $examstate->nt = "0";
                            $examstate->fa = "0";
                            $examstate->ill = "0";
                            break;
                        case 'nt':
                            $examstate = new stdClass;
                            $examstate->nt = "1";
                            $examstate->fa = "0";
                            $examstate->ill = "0";
                            break;
                        case 'fa':
                            $examstate = new stdClass;
                            $examstate->nt = "0";
                            $examstate->fa = "1";
                            $examstate->ill = "0";
                            break;
                        case 'ill':
                            $examstate = new stdClass;
                            $examstate->nt = "0";
                            $examstate->fa = "0";
                            $examstate->ill = "1";
                            break;
                        default:
                            break;
                    }

                    $participant->examstate = json_encode($examstate);

                    if ($fromform->points[$participant->id]) { // If participants points were not empty.
                        $participant->exampoints = json_encode($fromform->points[$participant->id]);
                    }

                } else {
                    $participant->examstate = null;
                    $participant->exampoints = null;
                }

                if ($fromform->bonussteps[$participant->id] !== '-') {
                    $participant->bonussteps = $fromform->bonussteps[$participant->id];
                    $participant->bonuspoints = null;
                } else if ($fromform->bonuspoints_entered[$participant->id] === 1
                    && $fromform->bonuspoints[$participant->id] !== 0) {

                    $participant->bonussteps = null;
                    $participant->bonuspoints = $fromform->bonuspoints[$participant->id];
                } else {
                    $participant->bonussteps = null;
                    $participant->bonuspoints = null;
                }

                $participant->timeresultsentered = time();

            } else {
                if ($fromform->bonuspoints_entered[$participant->id] === 1 && isset($fromform->bonuspoints[$participant->id])) {
                    $participant->timeresultsentered = time();
                    $participant->bonuspoints = $fromform->bonuspoints[$participant->id];
                }
            }

            if (isset($participant->moodleuserid)) {
                $participant->login = null;
                $participant->firstname = null;
                $participant->lastname = null;
            }

            unset($participant->matrnr);

            if ($oldparticipant->examstate != $participant->examstate || $oldparticipant->exampoints != $participant->exampoints
                || $oldparticipant->bonuspoints != $participant->bonuspoints
                || $oldparticipant->bonussteps != $participant->bonussteps) {

                if ($DB->update_record('exammanagement_participants', $participant)) {
                    $updatedcount += 1;
                }
            }
        }
    }

    if ($updatedcount) {
        $DB->update_record("exammanagement", $moduleinstance);
        redirect(new moodle_url('/mod/exammanagement/participantsoverview.php', ['id' => $id]),
            get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
    } else {
        redirect (new moodle_url('/mod/exammanagement/participantsoverview.php', ['id' => $id]),
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
    $title = get_string('participantsoverview', 'mod_exammanagement');

    $PAGE->set_url('/mod/exammanagement/participantsoverview.php', ['id' => $id]);
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
        if (!isset($misc['mode'])) {
            echo $OUTPUT->heading($title . ' ' . $OUTPUT->help_icon('participantsoverview', 'mod_exammanagement', ''), 4);
        } else {
            echo $OUTPUT->heading($title . ' ' . $OUTPUT->help_icon('participantsoverview_grades', 'mod_exammanagement', ''), 4);
        }
    } else {
        echo $OUTPUT->heading($title, 4);
    }

    // Output buttons.
    if (!$epm) {
        if (!isset($misc['mode'])) {
            echo '<a href="' .
                new moodle_url('/mod/exammanagement/participantsoverview.php', ['id' => $id, 'epm' => true, 'page' => $pagenr])  .
                '" class="btn btn-primary float-right" title="' . get_string("edit_results_and_boni", "mod_exammanagement") .
                '"><span class="d-none d-lg-block">' . get_string("edit_results_and_boni", "mod_exammanagement") .
                '</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>';
        } else {
            echo '<a href="' .
                new moodle_url('/mod/exammanagement/participantsoverview.php', ['id' => $id, 'epm' => true, 'page' => $pagenr])  .
                '" class="btn btn-primary float-right" title="' . get_string("edit_grades", "mod_exammanagement") .
                '"><span class="d-none d-lg-block">' . get_string("edit_grades", "mod_exammanagement") .
                '</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>';
        }
    }

    // Output description.
    echo '<p>' . get_string("participants_overview_text", "mod_exammanagement") . '</p>';

    // Output alerts.

    // Set default data.
    $mform->set_data(['id' => $id]);

    // Display form.
    $mform->display();

    // Finish the page.
    echo $OUTPUT->footer();
}

