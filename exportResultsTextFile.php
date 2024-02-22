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
 * Outputs the exam results for the exammanagement as a text file.
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

// If the results after the exam review should be exported.
$afterexamreview = optional_param('afterexamreview', 0, PARAM_BOOL);

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
global $CFG;

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

define( "SEPARATOR", chr(9) ); // Tabulator.
define( "NEWLINE", "\r\n" );

if (!isset($moduleinstance->misc)) {
    $mode = 'normal';
} else {
    $misc = json_decode($moduleinstance->misc);

    if (isset($misc->mode) && $misc->mode === 'export_grades') {
        $mode = 'export_grades';
    }
}

if (($mode === 'normal' && !helper::getenteredresultscount($moduleinstance)) ||
    ($mode === 'export_grades' && !helper::getenteredbonuscount($moduleinstance, 'points'))) {

    redirect(new moodle_url('/mod/exammanagement/view.php#afterexam', ['id' => $id]),
        get_string('no_results_entered', 'mod_exammanagement'), null, 'error');
} else if (!helper::getdatadeletiondate($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#afterexam', ['id' => $id]),
        get_string('correction_not_completed', 'mod_exammanagement'), null, 'error');
}

$gradingscale = json_decode($moduleinstance->gradingscale ?? '');

$coursename = $course->fullname;

// Get saved import file headers.
$textfileheaders = json_decode($moduleinstance->importfileheaders ?? '');
$resultfilesziparchive = false;

// If no headers of import files are saved because all participants are imported from course.

if ( !$textfileheaders ) {
    $examdate = helper::gethrexamtime($moduleinstance);
    $header1 = '"' . $coursename . '"' . SEPARATOR . '"Prüfung"' . SEPARATOR . '""' . SEPARATOR . '"' . $examdate . '"';
    $header2 = '"Prüfungsnummer"' . SEPARATOR . '"Matrikelnummer"' . SEPARATOR . '"Vorname"' . SEPARATOR . '"Mittelname"' .
        SEPARATOR . '"Name"' . SEPARATOR . '"Noten"';

    $textfile = $header1 . NEWLINE . $header2 . NEWLINE;

    if ($afterexamreview == false) {
        $participants = helper::getexamparticipants($moduleinstance, ['mode' => 'all'], ['matrnr']);
    } else {  // If export of changed results after exam review.
        $participants = helper::getexamparticipants($moduleinstance, ['mode' => 'resultsafterexamreview'], ['matrnr']);
    }

    $examnumber = '""';

    foreach ($participants as $participant) { // Construct lines for each participant.

        if ($mode === 'export_grades') {
            if ($gradingscale) {
                $resultwithnonus = helper::formatnumberfordisplay(
                        helper::calculateresultgrade($moduleinstance, $participant->bonuspoints));
            } else {
                $resultwithnonus = helper::formatnumberfordisplay($participant->bonuspoints);
            }
        } else {
            $resultwithnonus = helper::formatnumberfordisplay(helper::calculateresultgrade($moduleinstance,
                helper::calculatepoints($participant, true), $participant->bonussteps));

        }

        $resultwithnonus = '"' . $resultwithnonus . '"';

        $textfile .= $examnumber . SEPARATOR . '"' . $participant->matrnr . '"' . SEPARATOR . '"' . $participant->firstname . '"' .
            SEPARATOR . '""' . SEPARATOR . '"' . $participant->lastname . '"' . SEPARATOR . $resultwithnonus . NEWLINE;
    }

    // Generate filename without umlaute.
    $umlaute = ["/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/"];
    $replace = ["ae", "oe", "ue", "Ae", "Oe", "Ue", "ss"];
    $filenameumlaute = get_string("results", "mod_exammanagement") . '_' . helper::getcleancoursecategoryname() . '_' .
        $course->fullname . '_' . $moduleinstance->name . '.txt';
    $filename = preg_replace($umlaute, $replace, $filenameumlaute);

    // Return content as file.
    header( "Content-Type: application/force-download; charset=UTF-8" );
    header( "Content-Disposition: attachment; filename=\"" . $filename . "\"" );
    header( "Content-Length: ". strlen( $textfile ) );
    echo $textfile;

} else {

    // Generate filename without umlaute.
    $umlaute = ["/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/"];
    $replace = ["ae", "oe", "ue", "Ae", "Oe", "Ue", "ss"];
    $filenameumlaute = get_string("results", "mod_exammanagement") . '_' . helper::getcleancoursecategoryname() . '_' .
        $course->fullname . '_' . $moduleinstance->name;
    $filename = preg_replace($umlaute, $replace, $filenameumlaute);

    // Get all participants that are imported from course (header id = 0).
    $participantsfromcourse = helper::getexamparticipants($moduleinstance, ['mode' => 'header', 'id' => 0], ['matrnr']);

    // If there are other participants that are read in from file.
    if (count($textfileheaders) > 1 || (count($textfileheaders) == 1 && $participantsfromcourse)) {

        // Prepare zip file.
        $tempfile = tempnam(sys_get_temp_dir(), "examresults.zip");
        $resultfilesziparchive = new ZipArchive();
        $resultfilesziparchive->open($tempfile, ZipArchive::OVERWRITE);
    }

    $filecount = 0;

    if ($participantsfromcourse && $afterexamreview == false) { // Construct lines for participants from course (header id = 0).

        $examdate = helper::gethrexamtime($moduleinstance);

        $header1 = '"' . $coursename . '"' . SEPARATOR . '"Prüfung"' . SEPARATOR . '""' . SEPARATOR . '"' . $examdate . '"';
        $header2 = '"Prüfungsnummer"' . SEPARATOR . '"Matrikelnummer"' . SEPARATOR . '"Vorname"' . SEPARATOR .
            '"Mittelname"' . SEPARATOR . '"Name"' . SEPARATOR . '"Noten"';
        $textfile = $header1 . NEWLINE . $header2 . NEWLINE;

        $examnumber = '""';

        foreach ($participantsfromcourse as $participant) {

            if ($mode === 'export_grades') {
                if ($gradingscale) {
                    $resultwithnonus = helper::formatnumberfordisplay(
                        helper::calculateresultgrade($moduleinstance, $participant->bonuspoints));
                } else {
                    $resultwithnonus = helper::formatnumberfordisplay($participant->bonuspoints);
                }
            } else {
                $resultwithnonus = helper::formatnumberfordisplay(
                    helper::calculateresultgrade($moduleinstance, helper::calculatepoints($participant, true),
                        $participant->bonussteps));
            }

            $resultwithnonus = '"' . $resultwithnonus . '"';

            $textfile .= $examnumber . SEPARATOR . '"' . $participant->matrnr . '"' . SEPARATOR . '"' . $participant->firstname .
                '"' . SEPARATOR . '""' . SEPARATOR . '"' . $participant->lastname . '"' . SEPARATOR . $resultwithnonus . NEWLINE;
        }

        $filecount += 1;

        // If there are more files coming: add content to archive (else it will be send to browser at the end of the code).
        if ($textfile && (count($textfileheaders) > 1
            || (count($textfileheaders) == 1 && $participantsfromcourse)) && $resultfilesziparchive) {

                $resultfilesziparchive->addFromString($filename . '_' . $filecount . '.txt', $textfile);
        }
    }

    foreach ($textfileheaders as $key => $textfileheader) { // Iterate over all headers and create new file for archive.

        if ($afterexamreview == false) {
            $participants = helper::getexamparticipants($moduleinstance, ['mode' => 'header', 'id' => $key + 1], ['matrnr']);
        } else {
            $participants = helper::getexamparticipants($moduleinstance, ['mode' => 'resultsafterexamreview'], ['matrnr']);
        }

        $textfile = false;

        if ($participants) {

            $textfile = $textfileheader . NEWLINE;

            $examnumber = '""';

            foreach ($participants as $participant) {

                if ($mode === 'export_grades') {
                    if ($gradingscale) {
                        $resultwithnonus = helper::formatnumberfordisplay(
                            helper::calculateresultgrade($moduleinstance, $participant->bonuspoints));
                    } else {
                        $resultwithnonus = helper::formatnumberfordisplay($participant->bonuspoints);
                    }
                } else {
                    $resultwithnonus = helper::formatnumberfordisplay(
                        helper::calculateresultgrade($moduleinstance, helper::calculatepoints($participant, true),
                            $participant->bonussteps));
                }

                $resultwithnonus = '"' . $resultwithnonus . '"';

                $textfile .= $examnumber . SEPARATOR . '"' . $participant->matrnr . '"' . SEPARATOR . '"' .
                    $participant->firstname . '"' . SEPARATOR . '""' . SEPARATOR . '"' . $participant->lastname .
                     '"' . SEPARATOR . $resultwithnonus . NEWLINE;
            }
        }

        $filecount += 1;

        if ($textfile && (count($textfileheaders) > 1
            || (count($textfileheaders) == 1 && $participantsfromcourse)) && $resultfilesziparchive) {

            // Add content.
            $resultfilesziparchive->addFromString($filename . '_' . $filecount . '.txt', $textfile);
        }

        if ($afterexamreview == true) {
            break;
        }
    }

    if ($textfile && (count($textfileheaders) == 1 || (count($textfileheaders) == 0 && $participantsfromcourse)
        || $afterexamreview == true) && $resultfilesziparchive == false) {

        unlink($tempfile);

        header( "Content-Type: application/force-download; charset=UTF-8"  );
        header( "Content-Disposition: attachment; filename=\"" . $filename . ".txt\"" );
        header( "Content-Length: ". strlen( $textfile ) );
        echo($textfile);
    } else if ($resultfilesziparchive) {
        // Close and send to users.
        $resultfilesziparchive->close();
        header('Content-Type: application/zip; charset=UTF-8');
        header('Content-Length: ' . filesize($tempfile));
        header('Content-Disposition: attachment; filename="'.$filename.'.zip"');
        readfile($tempfile);
        unlink($tempfile);
    } else {
        redirect(new moodle_url('/mod/exammanagement/view.php', ['id' => $id]),
            get_string('cannot_create_zip_archive', 'mod_exammanagement'), null, 'error');
    }
}
