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
 * Allows teachers to import bonus points or grade steps for an exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_exammanagement\ldap\ldapmanager;
use mod_exammanagement\local\helper;
use mod_exammanagement\local\readfilter;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_once("$CFG->libdir/phpspreadsheet/vendor/autoload.php");

// Course_module ID, or ...
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module.
$e = optional_param('e', 0, PARAM_INT);

// The number if bonus steps to be imported.
$bonusstepcount = optional_param('bonusstepcount', 0, PARAM_INT);

// If bonus should be resetted.
$dbp = optional_param('dbp', 0, PARAM_INT);

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

$misc = (array) json_decode($moduleinstance->misc ?? '');

// If user has not entered the correct password: redirect to check password page.
if (isset($moduleinstance->password) &&
    (!isset($SESSION->loggedInExamOrganizationId) || $SESSION->loggedInExamOrganizationId !== $id)) {

    redirect(new moodle_url('/mod/exammanagement/checkpassword.php', ['id' => $id]), null, null, null);
}

// Check if requirements are met.
if (helper::isexamdatadeleted($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('err_examdata_deleted', 'mod_exammanagement'), null, 'error');
} else if (!isset($misc['mode']) && !helper::placesassigned($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#aftercorrection', ['id' => $id]),
        get_string('no_places_assigned', 'mod_exammanagement'), null, 'error');
} else if (!helper::getparticipantscount($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
        get_string('no_participants_added', 'mod_exammanagement'), null, 'error');
}

if ($dbp) {
    require_sesskey();
    $DB->set_field('exammanagement_participants', 'bonuspoints', null, ['exammanagement' => $moduleinstance->id]);
    $DB->set_field('exammanagement_participants', 'bonussteps', null, ['exammanagement' => $moduleinstance->id]);
}

$bonuscount = helper::getenteredbonuscount($moduleinstance);

// Instantiate form.
require_once($CFG->dirroot . '/mod/exammanagement/classes/forms/importbonus_form.php');
$mform = new mod_exammanagement_importbonus_form(null, [
    'id' => $id,
    'e' => $e,
    'bonusstepcount' => $bonusstepcount,
    'bonuscount' => $bonuscount,
    'mode' => $misc['mode'] ?? null,
]);

// Form processing and displaying is done here.
if ($mform->is_cancelled()) { // Handle form cancel operation, if cancel button is present on form.
    redirect(new moodle_url('/mod/exammanagement/view.php#aftercorrection', ['id' => $id]),
        get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');
} else if ($fromform = $mform->get_data()) { // In this case you process validated data.
    if ($fromform->bonuspoints_list) {

        if ($fromform->bonusmode === 'steps' && ((isset($fromform->bonussteppoints[2])
            && $fromform->bonussteppoints[1] >= $fromform->bonussteppoints[2])
            || (isset($fromform->bonussteppoints[3]) && $fromform->bonussteppoints[2] >= $fromform->bonussteppoints[3]))) {

            redirect(new moodle_url('/mod/exammanagement/importbonus.php', ['id' => $id]),
                get_string('points_bonussteps_invalid', 'mod_exammanagement'), null, 'error');
        }

        // Retrieve Files from form.
        $file = $mform->get_file_content('bonuspoints_list');
        $filename = $mform->get_new_filename('bonuspoints_list');

        $tempfile = tempnam(sys_get_temp_dir(), 'bonuslist_');
        rename($tempfile, $tempfile .= $filename);

        $handle = fopen($tempfile, "w");
        fwrite($handle, $file);

        $excelreader = IOFactory::createReaderForFile($tempfile);
        $excelreader->setReadDataOnly(true);

        $excelreader->setReadFilter( new readfilter($fromform->pointsfield) );

        $excelreader->setReadDataOnly(true);

        $reader = $excelreader->load($tempfile);

        $worksheet = $reader->getActiveSheet();
        $highestrow = $worksheet->getHighestRow();

        $alldata = [];
        $mrnrs = [];
        $logins = [];
        $lines = [];

        $potentialuserids = $worksheet->rangeToArray($fromform->idfield . '2:' . $fromform->idfield . $highestrow);
        $points = $worksheet->rangeToArray($fromform->pointsfield . '2:' . $fromform->pointsfield . $highestrow);

        // Unset all identifiers that are no valid matriculation numbers or mail adresses.
        foreach ($potentialuserids as $key => $identifier) {

            // If identifier is mail adress (import of moodle grades export).
            if ($identifier[0] && filter_var($identifier[0], FILTER_VALIDATE_EMAIL)) {
                $alldata[$key] = [
                    'matrnr' => false,
                    'login' => false,
                    'moodleuserid' => $DB->get_field('user', 'id', ['email' => $identifier[0]]),
                    'points' => $points[$key][0],
                ];
            } else if ($identifier[0] && helper::checkifvalidmatrnr($identifier[0])) {
                // If identifier is matrnr (individual import).
                $mrnrs[$key] = $identifier[0];
                array_push($lines, $key);
            }
        }

        if (!empty($mrnrs)) {
            $logins = $ldapmanager->getldapattributesformatrnrs($mrnrs, 'usernames_and_matriculationnumbers', $lines);
        }

        if (!empty($logins)) {
            foreach ($logins as $key => $data) {

                $moodleuserid = $DB->get_field('user', 'id', ['username' => $data['login']]);

                if ($moodleuserid) {
                    $alldata[$key] = ['login' => false, 'moodleuserid' => $moodleuserid, 'points' => $points[$key][0]];
                } else {
                    $alldata[$key] = ['login' => $data['login'], 'moodleuserid' => false, 'points' => $points[$key][0]];
                }

            }
        }

        $update = false;

        foreach ($alldata as $line => $data) {
            $participant = false;

            if ($data['moodleuserid'] && helper::checkifalreadyparticipant($moduleinstance, $data['moodleuserid'])) {
                $participant = helper::getexamparticipant($moduleinstance, $data['moodleuserid']);
            } else if ($data['login'] && helper::checkifalreadyparticipant($moduleinstance, false, $data['login'])) {
                $participant = helper::getexamparticipant($moduleinstance, false, $data['login']);
            }

            if ($participant) {
                if ($fromform->bonusmode === "steps" && isset($data['points']) && $data['points'] && is_numeric($data['points'])) {
                    $participant->bonussteps = 0;

                    foreach ($fromform->bonussteppoints as $step => $points) {

                        if (floatval($data['points']) >= $points) {
                            $participant->bonussteps = $step; // Change to detect bonus step.
                            $participant->bonuspoints = false;
                        } else {
                            break;
                        }
                    }
                } else if ($fromform->bonusmode === "points" && isset($data['points'])
                    && $data['points']&& is_numeric($data['points'])) {

                    $participant->bonussteps = false;
                    $participant->bonuspoints = $data['points'];

                    if ($moduleinstance->misc !== null ) { // If mode is export_gradings.
                        $participant->exampoints = '{"1":0}'; // Add 0 points as exam result.
                        $participant->examstate = '{"nt":"0","fa":"0","ill":"0"}';
                    }
                }

                $update = $DB->update_record('exammanagement_participants', $participant);
            }
        }

        if ($fromform->bonusmode === "steps") {
            $DB->set_field('exammanagement_participants', 'bonuspoints', null, ['exammanagement' => $moduleinstance->id]);
        } else if ($fromform->bonusmode === "points") {
            $DB->set_field('exammanagement_participants', 'bonussteps', null, ['exammanagement' => $moduleinstance->id]);
        }

        fclose($handle);
        unlink($tempfile);

        if ($update) {
            redirect(new moodle_url('/mod/exammanagement/view.php#aftercorrection', ['id' => $id]),
                get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
        } else {
            redirect(new moodle_url('/mod/exammanagement/view.php#aftercorrection', ['id' => $id]),
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

    if (isset($allparticipants)) {
        $title = get_string('importbonus', 'mod_exammanagement');
    } else {
        $title = get_string('import_grades', 'mod_exammanagement');
    }

    $PAGE->set_url('/mod/exammanagement/importbonus.php', ['id' => $id]);
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
            echo $OUTPUT->heading($title . ' ' . $OUTPUT->help_icon('importbonus', 'mod_exammanagement', ''), 4);
        } else {
            echo $OUTPUT->heading($title . ' ' . $OUTPUT->help_icon('importbonus_grades', 'mod_exammanagement', ''), 4);
        }
    } else {
        echo $OUTPUT->heading($title, 4);
    }

    // Output buttons.
    if ($bonuscount) {
        if (!isset($misc['mode'])) {
            echo '<a href="' . new moodle_url('/mod/exammanagement/importbonus.php',
                ['id' => $id, 'dbp' => 1, 'sesskey' => sesskey()]) .
                '" role="button" class="btn btn-secondary float-right" title="' .
                get_string("revert_bonus", "mod_exammanagement") . '"><span class="d-none d-lg-block">' .
                get_string("revert_bonus", "mod_exammanagement") .
                '</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>';
        } else {
            echo '<a href="' . new moodle_url('/mod/exammanagement/importbonus.php',
                ['id' => $id, 'dbp' => 1, 'sesskey' => sesskey()]) .
                '" role="button" class="btn btn-secondary float-right" title="' .
                get_string("revert_grades", "mod_exammanagement") . '"><span class="d-none d-lg-block">' .
                get_string("revert_grades", "mod_exammanagement") .
                '</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>';
        }
    }

    // Output description.
    if (!isset($misc['mode'])) {
        echo '<p>' . get_string('import_bonus_text', 'mod_exammanagement') . '</p>';
    } else {
        echo '<p>' . get_string('import_grades_text', 'mod_exammanagement') . '</p>';
    }

    // Output alerts.

    // Set default data.
    $mform->set_data(['id' => $id]);

    // Display form.
    $mform->display();

    // Finish the page.
    echo $OUTPUT->footer();
}


