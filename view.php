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
 * Outputs the overview page for the exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_exammanagement\output\exammanagement_overview;
use mod_exammanagement\output\exammanagement_overview_export_grades;
use mod_exammanagement\output\exammanagement_participantsview;
use mod_exammanagement\ldap\ldapmanager;
use mod_exammanagement\local\helper;

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// Course_module ID, or ...
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module.
$e = optional_param('e', 0, PARAM_INT);

// Relevant if called from itself and information is set visible for users or correction is marked as completed.
$calledfromformdt = optional_param('calledfromformdt', 0, PARAM_BOOL);
$datetimevisible = optional_param('datetimevisible', 0, PARAM_BOOL);

$calledfromformroom = optional_param('calledfromformroom', 0, PARAM_BOOL);
$roomvisible = optional_param('roomvisible', 0, PARAM_BOOL);

$calledfromformplace = optional_param('calledfromformplace', 0, PARAM_BOOL);
$placevisible = optional_param('placevisible', 0, PARAM_BOOL);

$calledfromformbonus = optional_param('calledfromformbonus', 0, PARAM_BOOL);
$bonusvisible = optional_param('bonusvisible', 0, PARAM_BOOL);

$calledfromformresult = optional_param('calledfromformresult', 0, PARAM_BOOL);
$resultvisible = optional_param('resultvisible', 0, PARAM_BOOL);

$calledfromformcorrection = optional_param('calledfromformcorrection', 0, PARAM_BOOL);
$correctioncompleted = optional_param('correctioncompleted', 0, PARAM_BOOL);

$calledfromformexamreview = optional_param('calledfromformexamreview', 0, PARAM_BOOL);
$examreviewvisible = optional_param('examreviewvisible', 0, PARAM_BOOL);

$togglephase = optional_param('togglephase', 0, PARAM_BOOL);
$phase = optional_param('phase', 0, PARAM_TEXT);

// Params containing the page count and redirect url for changing page count of paginated tables.
$pagecount = optional_param('pagecount', 0, PARAM_INT);
$redirect = optional_param('redirect', '', PARAM_TEXT);

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

// Get global and construct helper objects.
global $PAGE, $CFG, $USER, $SESSION, $DB, $OUTPUT;

$context = context_module::instance($cm->id);

$ldapmanager = ldapmanager::getinstance();

// If user is teacher.
if (has_capability('mod/exammanagement:viewinstance', $context)) {
    // If user has not entered the correct password: redirect to check password page.
    if (isset($moduleinstance->password) &&
        (!isset($SESSION->loggedInExamOrganizationId) || $SESSION->loggedInExamOrganizationId !== $id)) {

        redirect(new moodle_url('/mod/exammanagement/checkpassword.php', ['id' => $id]), null, null, null);
    }

    // Reset page count if necessary.
    if ($pagecount != 0 && $redirect != '') {
        if ($pagecount < 0) {
            $pagecount = 10;
        }

        $oldpagecount = get_user_preferences('exammanagement_pagecount');

        if ($pagecount != $oldpagecount) {
            set_user_preference('exammanagement_pagecount', $pagecount);
        }

        redirect ($redirect, null, null, null);
    }

    // Determine mode.
    if (!isset($moduleinstance->misc)) {
        $mode = 'normal';
    } else {
        $misc = (array) json_decode($moduleinstance->misc);

        if (isset($misc->mode) && $misc->mode === 'export_grades') {
            $mode = 'export_grades';
        } else {
            $mode = 'normal';
        }
    }

    // Save toggled phase in the user preferences.
    if ($togglephase) {
        require_sesskey();

        if ($phase && $phase !== 0) {
            $phasestate = get_user_preferences('exammanagement_' . $phase);

            if (!isset($phasestate)) {
                $activephase = helper::determineactivephase($moduleinstance);

                if ($activephase == $phase) {
                    set_user_preference('exammanagement_' . $phase, false);
                } else {
                    set_user_preference('exammanagement_' . $phase, true);
                }
            } else {
                set_user_preference('exammanagement_' . $phase, !$phasestate);
            }
        }
    }

    // Set exam information visible.
    if (!helper::isexamdatadeleted($moduleinstance)) {

        if ($calledfromformdt) { // Set exam date visible.
            require_sesskey();

            if ($datetimevisible) {
                $moduleinstance->datetimevisible = true;
            } else {
                $moduleinstance->datetimevisible = false;
            }

            $DB->update_record("exammanagement", $moduleinstance);
        } else if ($calledfromformroom) { // Set exam room visible.
            require_sesskey();

            if ($roomvisible) {
                $moduleinstance->roomvisible = true;
            } else {
                $moduleinstance->roomvisible = false;
            }

            $DB->update_record("exammanagement", $moduleinstance);
        } else if ($calledfromformplace) {  // Set exam place visible.
            require_sesskey();

            if ($placevisible) {
                $moduleinstance->placevisible = true;
            } else {
                $moduleinstance->placevisible = false;
            }

            $DB->update_record("exammanagement", $moduleinstance);
        } else if ($calledfromformbonus) {  // Set exam bonus visible.
            require_sesskey();

            if ($bonusvisible) {
                $moduleinstance->bonusvisible = true;
            } else {
                $moduleinstance->bonusvisible = false;
            }

            $DB->update_record("exammanagement", $moduleinstance);
        } else if ($calledfromformresult) {  // Set exam result visible.
            require_sesskey();

            if ($resultvisible) {
                $moduleinstance->resultvisible = true;
            } else {
                $moduleinstance->resultvisible = false;
            }

            $DB->update_record("exammanagement", $moduleinstance);
        } else if ($calledfromformcorrection) {  // Set correction completed.
            require_sesskey();

            $resultscount = helper::getenteredresultscount($moduleinstance);

            $bonuscount = helper::getenteredbonuscount($moduleinstance, 'points'); // If mode is export_grades.

            if (($mode === 'normal' && $resultscount) || $mode = 'export_grades' && $bonuscount) {
                if ($correctioncompleted) {
                    $moduleinstance->datadeletion = strtotime("+3 months", time());
                } else {
                    $moduleinstance->datadeletion = null;
                    $moduleinstance->deletionwarningmailids = null;
                }
            } else {
                redirect(new moodle_url('/mod/exammanagement/view.php#afterexam', ['id' => $id]),
                    get_string('no_results_entered', 'mod_exammanagement'), null, 'error');
            }

            $DB->update_record("exammanagement", $moduleinstance);
        } else if ($calledfromformexamreview) { // Set exam rewview visible.
            require_sesskey();

            if ($examreviewvisible) {
                $moduleinstance->examreviewvisible = true;
            } else {
                $moduleinstance->examreviewvisible = false;
            }

            $DB->update_record("exammanagement", $moduleinstance);
        }
    }

    // If instance was moved to new category.
    $oldcategoryid = $moduleinstance->categoryid;
    $coursecategoryid = $PAGE->category->id; // Set course category.

    if ($oldcategoryid !== $coursecategoryid) {

        // Update category id for instance.
        $moduleinstance->categoryid = $coursecategoryid;
        $DB->update_record("exammanagement", $moduleinstance);

        // Update category ids for participants.
        $DB->set_field('exammanagement_participants', 'categoryid', $coursecategoryid, ['exammanagement' => $moduleinstance->id]);
        $DB->set_field('exammanagement_temp_part', 'categoryid', $coursecategoryid, ['exammanagement' => $moduleinstance->id]);
    }

    // Delete temp participants and headers if exist.
    if ($DB->record_exists('exammanagement_temp_part', ['exammanagement' => $moduleinstance->id])) {
        $moduleinstance->tempimportfileheader = null;

        $DB->update_record("exammanagement", $moduleinstance);

        $DB->delete_records('exammanagement_temp_part', ['exammanagement' => $moduleinstance->id]);
    }

    // Reset phase information if participants are deleted.
    if (!helper::isexamdatadeleted($moduleinstance) && !helper::getparticipantscount($moduleinstance)) {
        $moduleinstance->importfileheaders = null;
        $moduleinstance->assignmentmode = null;
        $moduleinstance->datetimevisible = null;
        $moduleinstance->roomvisible = null;
        $moduleinstance->placevisible = null;
        $moduleinstance->datadeletion = null;
        $moduleinstance->examreviewvisible = null;

        $DB->update_record("exammanagement", $moduleinstance);
    }

    // Rendering and displaying content.
    if ($mode === 'normal') {

        // Set phases information.
        $activephase = helper::determineactivephase($moduleinstance);

        $phases = new stdclass();

        // Phase one.
        $phaseone = new stdclass();
        $phaseone->completed = helper::checkphasecompletion($moduleinstance, 'phase_one');

        if (null !== get_user_preferences('exammanagement_phase_one')) {
            $phaseone->open = get_user_preferences('exammanagement_phase_one');
        } else {
            if ($activephase == 'phase_one') {
                $phaseone->open = true;
            } else {
                $phaseone->open = false;
            }
        }
        $phases->phase_one = $phaseone;

        // Phase two.
        $phasetwo = new stdclass();
        $phasetwo->completed = helper::checkphasecompletion($moduleinstance, 'phase_two');

        if (null !== get_user_preferences('exammanagement_phase_two')) {
            $phasetwo->open = get_user_preferences('exammanagement_phase_two');
        } else {
            if ($activephase == 'phase_two') {
                $phasetwo->open = true;
            } else {
                $phasetwo->open = false;
            }
        }
        $phases->phase_two = $phasetwo;

        // Phase exam.
        $phaseexam = new stdclass();
        $phaseexam->completed = helper::checkphasecompletion($moduleinstance, 'phase_exam');

        if (null !== get_user_preferences('exammanagement_phase_exam')) {
            $phaseexam->open = get_user_preferences('exammanagement_phase_exam');
        } else {
            if ($activephase == "phase_exam") {
                $phaseexam->open = true;
            } else {
                $phaseexam->open = false;
            }
        }
        $phases->phase_exam = $phaseexam;

        // Phase three.
        $phasethree = new stdclass();
        $phasethree->completed = helper::checkphasecompletion($moduleinstance, 'phase_three');

        if (null !== get_user_preferences('exammanagement_phase_three')) {
            $phasethree->open = get_user_preferences('exammanagement_phase_three');
        } else {
            if ($activephase == 'phase_three') {
                $phasethree->open = true;
            } else {
                $phasethree->open = false;
            }
        }
        $phases->phase_three = $phasethree;

        // Phase four.
        $phasefour = new stdclass();
        $phasefour->completed = helper::checkphasecompletion($moduleinstance, 'phase_four');

        if (null !== get_user_preferences('exammanagement_phase_four')) {
            $phasefour->open = get_user_preferences('exammanagement_phase_four');
        } else {
            if ($activephase == 'phase_four') {
                $phasefour->open = true;
            } else {
                $phasefour->open = false;
            }
        }
        $phases->phase_four = $phasefour;

        // Phase five.
        $phasefive = new stdclass();

        // If exam review is disabled.
        if (isset($misc) && isset($misc['configoptions']) && in_array('noexamreview', $misc['configoptions'])) {
            $phasefive = false;
        } else {
            $phasefive->completed = helper::checkphasecompletion($moduleinstance, 'phase_five');

            if (null !== get_user_preferences('exammanagement_phase_five')) {
                $phasefive->open = get_user_preferences('exammanagement_phase_five');
            } else {
                if ($activephase == 'phase_five') {
                    $phasefive->open = true;
                } else {
                    $phasefive->open = false;
                }
            }
        }
        $phases->phase_five = $phasefive;

        if (get_config('mod_exammanagement', 'enablehelptexts')) {
            $helptexticon = $OUTPUT->help_icon('overview', 'mod_exammanagement', '');
            $additionalressourceslink = get_config('mod_exammanagement', 'additionalressources');
        } else {
            $helptexticon = false;
            $additionalressourceslink = false;
        }

        // Get values for each workstep.
        $examtime = $moduleinstance->examtime;
        if ($tasks = helper::gettasks($moduleinstance)) {
            $taskcount = count($tasks);
        } else {
            $taskcount = false;
        }
        $taskpoints = helper::formatnumberfordisplay(helper::gettasktotalpoints($moduleinstance));
        $textfieldcontent = helper::gettextfield($moduleinstance)->text ?? false;

        if ($textfieldcontent) {
            if (!format_string($textfieldcontent)) {
                $textfieldcontent = get_string('mediacontent', 'mod_exammanagement');
            } else {
                $textfieldcontent = format_string($textfieldcontent);
            }
        }

        $participantscount = helper::getparticipantscount($moduleinstance);
        $roomscount = helper::getroomscount($moduleinstance);
        $roomnames = helper::getchoosenroomnames($moduleinstance);
        $totalseats = helper::gettotalnumberofseats($moduleinstance);
        $placesassigned = helper::placesassigned($moduleinstance);
        $allplacesassigned = helper::allplacesassigned($moduleinstance);
        $assignedplacescount = helper::getassignedplacescount($moduleinstance);
        $datetimevisible = $moduleinstance->datetimevisible;
        $roomvisible = $moduleinstance->roomvisible;
        $placevisible = $moduleinstance->placevisible;
        $bonuscount = helper::getenteredbonuscount($moduleinstance);
        $bonuspointsentered = helper::getenteredbonuscount($moduleinstance, 'points');
        $bonusvisible = $moduleinstance->bonusvisible;
        $gradingscale = json_decode($moduleinstance->gradingscale ?? '');
        $resultscount = helper::getenteredresultscount($moduleinstance);
        $resultvisible = $moduleinstance->resultvisible;
        $datadeletiondate = helper::getdatadeletiondate($moduleinstance);
        $examreviewtime = helper::gethrexamreviewtime($moduleinstance);
        $examreviewroom = $moduleinstance->examreviewroom;
        $examreviewvisible = $moduleinstance->examreviewvisible;
        $deleted = helper::isexamdatadeleted($moduleinstance);

        if ($ldapmanager->isldapenabled() && $ldapmanager->isldapconfigured()) {
            $ldapavailable = true;
        } else {
            $ldapavailable = false;
        }

        if (isset($moduleinstance->examreviewtime)) {
            $resultsenteredafterexamreview = helper::getenteredresultscount($moduleinstance, $moduleinstance->examreviewtime);
        } else {
            $resultsenteredafterexamreview = false;
        }

        $page = new exammanagement_overview($cm->id, $phases, $helptexticon, $additionalressourceslink, $examtime, $taskcount,
            $taskpoints, $textfieldcontent, $participantscount, $roomscount, $roomnames, $totalseats, $placesassigned,
            $allplacesassigned, $assignedplacescount, $datetimevisible, $roomvisible, $placevisible, $bonuscount,
            $bonuspointsentered, $bonusvisible, $gradingscale, $resultscount, $resultvisible, $datadeletiondate, $examreviewtime,
            $examreviewroom, $examreviewvisible, $resultsenteredafterexamreview, $deleted, $ldapavailable);

    } else if ($mode === 'export_grades') {

        if (get_config('mod_exammanagement', 'enablehelptexts')) {
            $helptexticon = $OUTPUT->help_icon('export_grades', 'mod_exammanagement', '');
            $additionalressourceslink = get_config('mod_exammanagement', 'additionalressources');
        } else {
            $helptexticon = false;
            $additionalressourceslink = false;
        }

        if ($ldapmanager->isldapenabled() && $ldapmanager->isldapconfigured()) {
            $ldapavailable = true;
        } else {
            $ldapavailable = false;
        }

        $participantscount = helper::getparticipantscount($moduleinstance);
        $bonuspointsentered = helper::getenteredbonuscount($moduleinstance, 'points');
        $gradingscale = json_decode($moduleinstance->gradingscale ?? '');
        $resultscount = helper::getenteredresultscount($moduleinstance);

        $datadeletiondate = helper::getdatadeletiondate($moduleinstance);
        $deleted = helper::isexamdatadeleted($moduleinstance);

        $page = new exammanagement_overview_export_grades($cm->id, $helptexticon, $additionalressourceslink, $participantscount,
            $bonuspointsentered, $gradingscale, $resultscount, $datadeletiondate, $deleted, $ldapavailable);
    }
} else if (has_capability('mod/exammanagement:viewparticipantspage', $context)) { // If user is student.

    // Exam time.
    $examtime = $moduleinstance->examtime;

    if ($moduleinstance->datetimevisible && $examtime) {
        $date = userdate($examtime, get_string('strftimedatefullshort', 'core_langconfig'));
        $time = userdate($examtime, get_string('strftimetime', 'core_langconfig'));
    } else {
        $date = false;
        $time = false;
    }

    // Room and place.
    global $USER;

    $participant = helper::getexamparticipant($moduleinstance, $USER->id);

    if ($moduleinstance->roomvisible && $participant && $participant->roomname) {
        $room = $participant->roomname;
    } else {
        $room = false;
    }

    if ($moduleinstance->placevisible && $participant && $participant->place) {
        $place = $participant->place;
    } else {
        $place = false;
    }

    // Textfield.
    $textfield = helper::gettextfield($moduleinstance)->text;

    // Bonussteps.
    if ($moduleinstance->bonusvisible && $participant) {
        if ($participant->bonussteps === '0') { // Allows mustache template to render 0.
            $bonussteps = get_string('no_bonus_earned', 'mod_exammanagement');
        } else {
            $bonussteps = $participant->bonussteps;
        }
    } else {
        $bonussteps = false;
    }

    // Bonuspoints.
    if ($moduleinstance->bonusvisible && $participant) {
        if ($participant->bonuspoints === '0') { // Allows mustache template to render 0.
            $bonuspoints = get_string('no_bonus_earned', 'mod_exammanagement');
        } else {
            $bonuspoints = helper::formatnumberfordisplay($participant->bonuspoints);
        }
    } else {
        $bonuspoints = false;
    }

    // Totalpoints.
    if ($moduleinstance->resultvisible && $participant) {

        $examstate = helper::getexamstate($participant);

        if ($examstate === 'normal') {
            $examstate = false;
            $totalpoints = helper::calculatepoints($participant);

            $tasktotalpoints = helper::formatnumberfordisplay(helper::gettasktotalpoints($moduleinstance));

            $totalpointswithbonus = helper::formatnumberfordisplay(helper::calculatepoints($participant, true));

            if ($totalpoints === '0') {
                $totalpoints = get_string('no_points_earned', 'mod_exammanagement');
            } else {
                $totalpoints = helper::formatnumberfordisplay($totalpoints);
            }
        } else {
            if ($examstate) {
                $examstate = get_string($examstate, 'mod_exammanagement');
            }

            $totalpoints = false;
            $totalpointswithbonus = false;
            $tasktotalpoints = false;
        }

    } else {
        $examstate = false;
        $totalpoints = false;
        $totalpointswithbonus = false;
        $tasktotalpoints = false;
    }

    // Examreview date and room.
    $examreviewtime = false;
    $examreviewroom = false;
    if ($moduleinstance->examreviewvisible && isset($moduleinstance->examreviewtime) && $moduleinstance->examreviewroom) {
        $examreviewtime = helper::gethrexamreviewtime($moduleinstance);
        $examreviewroom = $moduleinstance->examreviewroom;
    }

    // Check if exam data is deleted.
    $deleted = helper::isexamdatadeleted($moduleinstance);

    $page = new exammanagement_participantsview($cm->id, helper::checkifalreadyparticipant($moduleinstance, $USER->id), $date,
        $time, $room, $place, $textfield, $bonussteps, $bonuspoints, $examstate, $totalpoints, $tasktotalpoints,
        $totalpointswithbonus, $examreviewtime, $examreviewroom, $deleted);
}

// Set $PAGE.
$plugintype = get_string('modulename', 'mod_exammanagement');
$modulename = format_string($moduleinstance->name, true, [
    'context' => $context,
]);
$title = get_string('view', 'mod_exammanagement');

$PAGE->set_url('/mod/exammanagement/view.php', ['id' => $id]);
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

// Trigger course_module_viewed event.
$event = \mod_exammanagement\event\course_module_viewed::create([
    'objectid' => $moduleinstance->id,
    'context' => $context,
]);
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('exammanagement', $moduleinstance);
$event->trigger();

// Output the page from template.
echo $OUTPUT->render($page);

// Finish the page.
echo $OUTPUT->footer();
