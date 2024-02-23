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
 * Outputs the seating plan for the exammanagement as a pdf file.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_exammanagement\ldap\ldapmanager;
use mod_exammanagement\pdfs\seatingplan;
use mod_exammanagement\local\helper;

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// Course_module ID, or ...
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module.
$e = optional_param('e', 0, PARAM_INT);

// The sort mode for the seating plan.
$sortmode = optional_param('sortmode', 0, PARAM_TEXT);

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
} else if (!helper::placesassigned($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
        get_string('no_places_assigned', 'mod_exammanagement'), null, 'error');
} else if (!helper::getroomscount($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
        get_string('no_rooms_added', 'mod_exammanagement'), null, 'error');
}

// Cancel export if the sort mode is matrucilation numbers but ldap is not enabled or configured.
if ($sortmode == 'matrnr') {
    if (!$ldapmanager->isldapenabled()) {
        redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
            get_string('not_possible_no_matrnr', 'mod_exammanagement') . ' '.
            get_string('ldapnotenabled', 'mod_exammanagement'), null, 'error');
    } else if (!$ldapmanager->isldapconfigured()) {
        redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
            get_string('not_possible_no_matrnr', 'mod_exammanagement') . ' '.
            get_string('ldapnotconfigured', 'mod_exammanagement'), null, 'error');
    }
}

// Include pdf.
require_once(__DIR__.'/classes/pdfs/seatingplan.php');

define("WIDTH_COLUMN_MATNO", 90);
define("WIDTH_COLUMN_ROOM", 90);
define("WIDTH_COLUMN_PLACE", 90);
define("WIDTH_COLUMN_MIDDLE", 30);

// Include the main TCPDF library (search for installation path).
require_once($CFG->libdir . '/pdflib.php');

// Create new PDF document.
$pdf = new seatingplan(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information.
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(helper::getmoodlesystemname());
$pdf->SetTitle(get_string('seatingplan', 'mod_exammanagement') . ': ' . $course->fullname . ', '. $moduleinstance->name);
$pdf->SetSubject(get_string('seatingplan', 'mod_exammanagement'));
$pdf->SetKeywords(get_string('seatingplan', 'mod_exammanagement') . ', ' . $course->fullname .
    ', ' . $moduleinstance->name);

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

// Get users and construct user tables for document.
$roomids = json_decode($moduleinstance->rooms);

$roomscount = 0;

foreach ($roomids as $roomid) {

    $participants = helper::getexamparticipants($moduleinstance, ['mode' => 'room', 'id' => $roomid], ['matrnr']);

    if ($participants) {
        if ($sortmode == 'place') {
            usort($participants, function($a, $b) { // Sort array by custom user function.
                return strnatcmp($a->place, $b->place); // Sort by place.
            });
        } else if ($sortmode == 'matrnr') {
            usort($participants, function($a, $b) { // Sort array by custom user function.
                return strnatcmp($a->matrnr, $b->matrnr); // Sort by matrnr.
            });
        }

        $leftcol = [];
        $rightcol = [];
        $tbl;

        foreach ($participants as $key => $participant) {
            $matrnr = $participant->matrnr;

            if ($matrnr === '-') {
                $matrnr = $participant->firstname . ' ' . $participant->lastname;
            }

            if ($key % 70 < 35) {
                $leftcol[] = ["matrnr" => $matrnr, "roomname" => $participant->roomname, "place" => $participant->place];
            } else {
                $rightcol[] = ["matrnr" => $matrnr, "roomname" => $participant->roomname, "place" => $participant->place];
            }

            if ($key % 70 == 69 && isset($participants[$key + 1])) {
                $tbl = helper::getseatingplantable($leftcol, $rightcol);
                $pdf->AddPage();
                $pdf->writeHTML($tbl, true, false, false, false, '');

                $leftcol = [];
                $rightcol = [];
            }
        }

        $tbl = helper::getseatingplantable($leftcol, $rightcol);

        // Print text using writeHTMLCell().
        $pdf->AddPage();
        $pdf->writeHTML($tbl, true, false, false, false, '');

        $roomscount += 1;
    }

}

// Construct svg-files pages.
foreach ($roomids as $key => $roomid) {
    $room = $DB->get_record('exammanagement_rooms', ['roomid' => $roomid]);

    if ($room) {
        $roomname = $room->name;

        if ($key < $roomscount) {

            $svg = base64_decode($DB->get_field('exammanagement_rooms', 'seatingplan', ['roomid' => $room->roomid]));

            if (isset($svg) && $svg !== '') {

                $numberofplaces = count(json_decode($DB->get_field('exammanagement_rooms', 'places', ['roomid' => $room->roomid])));
                $maxseats = get_string('total_seats', 'mod_exammanagement') . ": " . $numberofplaces;

                $pdf->setPrintHeader(false);
                $pdf->AddPage('L', 'A4');
                $pdf->SetFont('freeserif', '', 20);
                $pdf->Text(0, 15, get_string('seatingplan', 'mod_exammanagement'), false, false, true, 0, 0, 'R');
                $pdf->Text(15, 15, get_string('lecture_room', 'mod_exammanagement'));
                $pdf->SetFont('freeserif', 'B', 25);
                $pdf->Text(15, 25, $roomname);
                $pdf->SetFont('freeserif', '', 10);
                $pdf->Text(15, 180, get_string('places_differ', 'mod_exammanagement'));
                $pdf->Text(15, 185, get_string('places_alternative', 'mod_exammanagement'));
                $pdf->Text(15, 180, $maxseats, false, false, true, 0, 0, 'R');
                $pdf->setTextColor(204, 0, 0);
                $pdf->Text(15, 185, get_string('numbered_seats_usable_seats', 'mod_exammanagement'), false, false, true, 0, 0, 'R');
                $pdf->setTextColor(0, 0, 0);

                // Using @ to supress php7 tempnam notice inside tcpdf lib that prevents pdf output.
                @$pdf->ImageSVG('@'.$svg, $x = '40', $y = '30', $w = '1000', $h = '1000', $link = '', $border = 1,
                    $fitonpage = false);
            }
        }
    }
}

// Generate filename without umlaute.
$umlaute = ["/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/"];
$replace = ["ae", "oe", "ue", "Ae", "Oe", "Ue", "ss"];
$filenameumlaute = get_string("seatingplan", "mod_exammanagement") . '_' . helper::getcleancoursecategoryname() .
    '_' . $course->fullname . '_' . $moduleinstance->name . '.pdf';
$filename = preg_replace($umlaute, $replace, $filenameumlaute);

// Close and output PDF document.
$pdf->Output($filename, 'D');
