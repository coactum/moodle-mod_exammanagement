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
 * Outputs the exam results in percentages for the exammanagement as a pdf file.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_exammanagement\pdfs\resultspercentages;
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
} else if (!helper::getenteredresultscount($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#afterexam', ['id' => $id]),
        get_string('no_results_entered', 'mod_exammanagement'), null, 'error');
} else if (!helper::getdatadeletiondate($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#afterexam', ['id' => $id]),
        get_string('correction_not_completed', 'mod_exammanagement'), null, 'error');
}

// Include pdf.
require_once(__DIR__ . '/classes/pdfs/resultspercentages.php');

define("WIDTH_COLUMN_NAME", 185);
define("WIDTH_COLUMN_FORENAME", 185);
define("WIDTH_COLUMN_MATNO", 70);
define("WIDTH_COLUMN_POINTS", 80);
define("WIDTH_COLUMN_PERCENT", 80);

// Include the main TCPDF library (search for installation path).
require_once($CFG->libdir . '/pdflib.php');

// Create new PDF document.
$pdf = new resultspercentages(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information.
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(helper::getmoodlesystemname());
$pdf->SetTitle(get_string('pointslist_percentages', 'mod_exammanagement') . ': ' . $course->fullname . ', '. $moduleinstance->name);
$pdf->SetSubject(get_string('pointslist_percentages', 'mod_exammanagement'));
$pdf->SetKeywords(get_string('pointslist_percentages', 'mod_exammanagement') . ', ' . $course->fullname .
    ', ' . $moduleinstance->name);

// Set header and footer.
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);

// Set default monospaced font.
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins.
$pdf->SetMargins(20, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

// Set auto page breaks.
$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

// Set image scale factor.
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add page.
$pdf->AddPage();

$pdf->Line(20, 15, 190, 15);

if (file_exists(__DIR__.'/../../data/logo_full.ai')) {
    $pdf->ImageEps('data/logo.ai', 30, 25, 13);
}

$pdf->SetFont('helvetica', '', 16);
$pdf->MultiCell(130, 3, get_string('pointslist_examreview', 'mod_exammanagement'), 0, 'C', 0, 0, 50, 17);

$pdf->SetFont('helvetica', 'B', 16);
$pdf->MultiCell(130, 3, $course->fullname . ', ' . $moduleinstance->name, 0, 'C', 0, 0, 50, 25);
$pdf->SetFont('helvetica', '', 16);

$date = helper::gethrexamtime($moduleinstance);

if ($date) {
    $pdf->MultiCell(130, 3, $date . ', ' . helper::getcleancoursecategoryname(), 0, 'C', 0, 0, 50, 42);
} else {
    $pdf->MultiCell(130, 3, helper::getcleancoursecategoryName(), 0, 'C', 0, 0, 50, 42);
}

$pdf->SetFont('helvetica', '', 10);

$pdf->SetTextColor(255, 0, 0);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->MultiCell(130, 3, "- " . get_string('internal_use', 'mod_exammanagement') . " -", 0, 'C', 0, 0, 50, 55);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', '', 10);

$pdf->Line(20, 62, 190, 62);
$pdf->SetXY(20, 65);

$totalpoints = helper::gettasktotalpoints($moduleinstance);

$maxpoints = helper::formatnumberfordisplay($totalpoints);

$fill = false;

$tbl = "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">";
$tbl .= "<thead>";
$tbl .= "<tr bgcolor=\"#000000\" color=\"#FFFFFF\">";
$tbl .= "<td width=\"" . WIDTH_COLUMN_NAME . "\"><b>" . get_string('lastname', 'mod_exammanagement') . "</b></td>";
$tbl .= "<td width=\"" . WIDTH_COLUMN_FORENAME . "\"><b>" . get_string('firstname', 'mod_exammanagement') . "</b></td>";
$tbl .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\"><b>" .
    get_string('matrno', 'mod_exammanagement') . "</b></td>";
$tbl .= "<td width=\"" . WIDTH_COLUMN_POINTS . "\" align=\"center\"><b>" .
    get_string('points', 'mod_exammanagement') . "<br>(max. " . $maxpoints . ")" . "</b></td>";
$tbl .= "<td width=\"" . WIDTH_COLUMN_PERCENT . "\" align=\"center\"><b>" .
    get_string('percentages', 'mod_exammanagement') . "</b></td>";
$tbl .= "</tr>";
$tbl .= "</thead>";

$participants = helper::getexamparticipants($moduleinstance, ['mode' => 'all'], ['matrnr']);

foreach ($participants as $participant) {

    $percentages = '-';

    $points = helper::calculatepoints($participant);

    if (is_numeric($points)) {
        $percentages = number_format( ( $points / $totalpoints ) * 100, 2, "," , "." ).' %';
    }

    $points = helper::formatnumberfordisplay($points);

    $tbl .= ($fill) ? "<tr bgcolor=\"#DDDDDD\">" : "<tr>";
    $tbl .= "<td width=\"" . WIDTH_COLUMN_NAME . "\">" . $participant->lastname . "</td>";
    $tbl .= "<td width=\"" . WIDTH_COLUMN_FORENAME . "\">" . $participant->firstname . "</td>";
    $tbl .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\">" . $participant->matrnr . "</td>";
    $tbl .= "<td width=\"" . WIDTH_COLUMN_POINTS . "\" align=\"center\">" . $points . "</td>";
    $tbl .= "<td width=\"" . WIDTH_COLUMN_PERCENT . "\" align=\"center\">" . $percentages . "</td>";
    $tbl .= "</tr>";

    $fill = !$fill;

}

$tbl .= "</table>";

// Print text using writeHTMLCell().
$pdf->writeHTML($tbl, true, false, false, false, '');

// Generate filename without umlaute.
$umlaute = ["/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/"];
$replace = ["ae", "oe", "ue", "Ae", "Oe", "Ue", "ss"];
$filenameumlaute = get_string("pointslist_percentages", "mod_exammanagement") . '_' . helper::getcleancoursecategoryname() .
    '_' . $course->fullname . '_' . $moduleinstance->name . '.pdf';
$filename = preg_replace($umlaute, $replace, $filenameumlaute);

// Close and output PDF document.
$pdf->Output($filename, 'D');
