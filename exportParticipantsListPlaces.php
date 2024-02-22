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
 * Outputs the exam participants list sorted by places for the exammanagement as a pdf file.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_exammanagement\pdfs\participantslist;
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
} else if (!helper::getroomscount($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
        get_string('no_rooms_added', 'mod_exammanagement'), null, 'error');
} else if (!helper::placesassigned($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
        get_string('no_places_assigned', 'mod_exammanagement'), null, 'error');
}

// Include pdf.
require_once(__DIR__ . '/classes/pdfs/participantslist.php');

define("WIDTH_COLUMN_NAME", 200);
define("WIDTH_COLUMN_FIRSTNAME", 150);
define("WIDTH_COLUMN_MATNO", 60);
define("WIDTH_COLUMN_ROOM", 90);
define("WIDTH_COLUMN_PLACE", 70);

// Include the main TCPDF library (search for installation path).
require_once($CFG->libdir . '/pdflib.php');

// Create new PDF document.
$pdf = new participantslist(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information.
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(helper::getmoodlesystemname());
$pdf->SetTitle(get_string('participantslist_places', 'mod_exammanagement') . ': ' .
    $course->fullname . ', '. $moduleinstance->name);
$pdf->SetSubject(get_string('participantslist_places', 'mod_exammanagement'));
$pdf->SetKeywords(get_string('participantslist_places', 'mod_exammanagement') . ', ' .
    $course->fullname . ', ' . $moduleinstance->name);

// Set default monospaced font.
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set default header data.
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// Set margins.
$pdf->SetMargins(25, 55, 25);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks.
$pdf->SetAutoPageBreak(true, 19);

// Set image scale factor.
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Set font.
$pdf->SetFont('freeserif', '', 10);

// Add a page.
$pdf->AddPage();

// Get users and construct content for document.
$roomids = json_decode($moduleinstance->rooms);
$fill = false;
$previousroom;
$tbl = helper::getparticipantslisttableheader();

foreach ($roomids as $roomid) {
    $currentroom = $DB->get_record('exammanagement_rooms', ['roomid' => $roomid]);

    $participants = helper::getexamparticipants($moduleinstance, ['mode' => 'room', 'id' => $roomid], ['matrnr']);

    if ($participants) {
        if (!empty($previousroom) && $currentroom != $previousroom) {
            // New room started. Finish and print current table and begin new page.
            $tbl .= "</table>";
            $pdf->writeHTML($tbl, true, false, false, false, '');
            $pdf->AddPage();
            $fill = false;
            $tbl = helper::getparticipantslisttableheader();
        }

        usort($participants, function($a, $b) { // Sort array by custom user function.
            return strnatcmp($a->place, $b->place); // Sort by place.
        });

        foreach ($participants as $participant) {

            $tbl .= ($fill) ? "<tr bgcolor=\"#DDDDDD\">" : "<tr>";
            $tbl .= "<td width=\"" . WIDTH_COLUMN_NAME . "\">" . $participant->lastname . "</td>";
            $tbl .= "<td width=\"" . WIDTH_COLUMN_FIRSTNAME . "\">" . $participant->firstname . "</td>";
            $tbl .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\">" . $participant->matrnr . "</td>";
            $tbl .= "<td width=\"" . WIDTH_COLUMN_ROOM . "\" align=\"center\">" . $participant->roomname . "</td>";
            $tbl .= "<td width=\"" . WIDTH_COLUMN_PLACE . "\" align=\"center\">" . $participant->place . "</td>";
            $tbl .= "</tr>";

            $fill = !$fill;
        }

        $previousroom = $currentroom;
    }
}

$tbl .= "</table>";

// Print text using writeHTMLCell().

$pdf->writeHTML($tbl, true, false, false, false, '');

// Generate filename without umlaute.
$umlaute = ["/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/"];
$replace = ["ae", "oe", "ue", "Ae", "Oe", "Ue", "ss"];
$filenameumlaute = get_string("participantslist_places", "mod_exammanagement") . '_' . helper::getcleancoursecategoryname() .
    '_' . $course->fullname . '_' . $moduleinstance->name . '.pdf';
$filename = preg_replace($umlaute, $replace, $filenameumlaute);

// Close and output PDF document.
$pdf->Output($filename, 'D');
