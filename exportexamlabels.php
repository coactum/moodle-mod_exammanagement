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
 * Outputs the exam labels for the exammanagement as a pdf file.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_exammanagement\ldap\ldapmanager;
use mod_exammanagement\pdfs\examlabels;
use mod_exammanagement\local\helper;

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// Course_module ID, or ...
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module.
$e = optional_param('e', 0, PARAM_INT);

// If barcodoes or qrcodes should beexported.
$mode = optional_param('mode', 'barcode', PARAM_TEXT);

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
} else if (!$ldapmanager->isldapenabled()) { // Cancel export if no matrnrs are availiable because ldap is not enabled.
    redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
        get_string('not_possible_no_matrnr', 'mod_exammanagement') . ' '.
        get_string('ldapnotenabled', 'mod_exammanagement'), null, 'error');
} else if (!$ldapmanager->isldapconfigured()) { // Cancel export if no matrnrs are availiable because ldap is not configured.
    redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
        get_string('not_possible_no_matrnr', 'mod_exammanagement') . ' '.
        get_string('ldapnotconfigured', 'mod_exammanagement'), null, 'error');
}

// Include pdf.
require_once(__DIR__ . '/classes/pdfs/examlabels.php');

define('LABEL_HEIGHT', 52);
define('X1', 7.7 + 2); // Plus Offset within Label.
define('X2', 106.3 + 2); // Plus Offset within Label.
define('Y', 21 + 2); // Plus Offset within Label.

// Include the main TCPDF library (search for installation path).
require_once($CFG->libdir . '/pdflib.php');

// Create new PDF document.
$pdf = new examlabels(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information.
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(helper::getmoodlesystemname());
$pdf->SetTitle(get_string('examlabels', 'mod_exammanagement') . ': ' . $course->fullname . ', '. $moduleinstance->name);
$pdf->SetSubject(get_string('examlabels', 'mod_exammanagement'));
$pdf->SetKeywords(get_string('examlabels', 'mod_exammanagement') . ', ' . $course->fullname . ', ' . $moduleinstance->name);

// Set default monospaced font.
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins.
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

// Set auto page breaks.
$pdf->SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);

// Set image scale factor.
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Remove default header.
$pdf->setPrintHeader(false);

$style = [
    'position' => 'S',
    'border' => false,
    'padding' => 0,
    'fgcolor' => [0, 0, 0],
    'bgcolor' => false,
    'text' => true,
    'font' => 'helvetica',
    'fontsize' => 8,
    'stretchtext' => 4,
];

// Get users and construct content for document.
$roomsarray = json_decode($moduleinstance->rooms);
$idcounter = 0;

if (strlen($course->fullname) <= 40) {
    $coursename = $course->fullname;
} else {
    $coursename = $course->shortname;
}

$semester = helper::getcleancoursecategoryname();

$date = helper::gethrexamtime($moduleinstance);

$lineoffset = -7;

if ($mode !== 'barcode') {
    $styleqr = [
        'border' => false,
        'padding' => 0,
        'fgcolor' => [0, 0, 0],
        'bgcolor' => false,
    ];
}

$first = true;

if ($roomsarray && helper::placesassigned($moduleinstance)) { // If rooms are already set and places are assigned.

    foreach ($roomsarray as $roomid) {

        $participants = helper::getexamparticipants($moduleinstance, ['mode' => 'room', 'id' => $roomid],
            ['matrnr'], 'name', false, null, 10, 'withmatrnr');

        if ($participants) {

            usort($participants, function($a, $b) { // Sort array by custom user function.
                return strnatcmp($a->place, $b->place); // Sort by place.
            });

            $counter = 0;
            $leftlabel = true;

            if ($counter < count($participants)) {
                $pdf->AddPage();
            }

            $y = Y;

            foreach ($participants as $k => $participant) { // Construct label for each participant in room.

                if ($participant->matrnr !== '-') {
                    $roomnamelinesoffsety = 0;

                    if ($participant->roomname && strlen($participant->roomname) > 10) { // Set offset.
                        $roomnamelinesoffsety = 5;
                    }
                    if ($participant->roomname && strlen($participant->roomname) > 25) { // Shorten long roomnames.
                        $participant->roomname = substr($participant->roomname, 0, 22) . '...';
                    }

                    if ($leftlabel) { // Print left label.
                        $pdf->SetFont('helvetica', '', 12);
                        $pdf->MultiCell(90, 5, $coursename, 0, 'C', 0, 0, X1, $y, true);
                        $pdf->SetFont('helvetica', 'B', 12);
                        $pdf->MultiCell(90, 5, $participant->lastname . ', ' . $participant->firstname .
                            ' (' . $participant->matrnr . ')', 0, 'C', 0, 0, X1, $y + 6, true);
                        $pdf->SetFont('helvetica', '', 10);
                        $pdf->MultiCell(21, 5, $date, 0, 'C', 0, 0, X1 + 1, $y + 21, true);
                        $pdf->MultiCell(21, 5, strtoupper($semester), 0, 'C', 0, 0, X1, $y + 32, true);
                        $pdf->MultiCell(32, 5, get_string('room', 'mod_exammanagement') . ': ' .
                            $participant->roomname, 0, 'L', 0, 0, X1 + 61, $y + 21, true);
                        $pdf->MultiCell(32, 5, get_string('place', 'mod_exammanagement') . ': ' .
                            $participant->place, 0, 'L', 0, 0, X1 + 61, $y + 26 + $roomnamelinesoffsety, true);
                        $pdf->SetFont('helvetica', 'B', 14);
                        $pdf->MultiCell(18, 5, ++$idcounter, 0, 'C', 0, 0, X1 + 68, $y + 34, true);

                        if ($mode == 'barcode') {
                            $checksum = helper::buildchecksumexamlabels('00000' . $participant->matrnr);
                            $pdf->write1DBarcode('00000' . $participant->matrnr . $checksum, 'EAN13',
                                X1 + 22, $y + 20, 37, 19, 0.4, $style, 'C');
                        } else {
                            $url = new moodle_url("/mod/exammanagement/inputresults.php",
                                ["id" => $id, 'matrnr' => $participant->matrnr]);

                            $pdf->write2DBarcode($url->out(false), 'QRCODE,Q', X1 + 25, $y + 18, 25, 25, $styleqr, 'N');
                            $pdf->Text(20, 145, '');
                        }

                    } else { // Print right label.
                        $pdf->SetFont('helvetica', '', 12);
                        $pdf->MultiCell(90, 5, $coursename, 0, 'C', 0, 0, X2, $y, true);
                        $pdf->SetFont('helvetica', 'B', 12);
                        $pdf->MultiCell(90, 5, $participant->lastname . ', ' . $participant->firstname .
                            ' (' . $participant->matrnr . ')', 0, 'C', 0, 0, X2, $y + 6, true);
                        $pdf->SetFont('helvetica', '', 10);
                        $pdf->MultiCell(21, 5, $date, 0, 'C', 0, 0, X2 + 1, $y + 21, true);
                        $pdf->MultiCell(21, 5, strtoupper($semester), 0, 'C', 0, 0, X2, $y + 32, true);
                        $pdf->MultiCell(32, 5, get_string('room', 'mod_exammanagement') . ': ' .$participant->roomname,
                            0, 'L', 0, 0, X2 + 61, $y + 21, true);
                        $pdf->MultiCell(32, 5, get_string('place', 'mod_exammanagement') . ': ' .
                            $participant->place, 0, 'L', 0, 0, X2 + 61, $y + 26 + $roomnamelinesoffsety, true);
                        $pdf->SetFont('helvetica', 'B', 14);
                        $pdf->MultiCell(18, 5, ++$idcounter, 0, 'C', 0, 0, X2 + 68, $y + 34, true);

                        if ($mode == 'barcode') {
                            $checksum = helper::buildchecksumexamlabels('00000' . $participant->matrnr);
                            $pdf->write1DBarcode('00000' . $participant->matrnr . $checksum, 'EAN13',
                                X2 + 22, $y + 20, 37, 19, 0.4, $style, 'C');
                        } else {
                            $url = new moodle_url("/mod/exammanagement/inputresults.php",
                                ["id" => $id, 'matrnr' => $participant->matrnr]);

                            $pdf->write2DBarcode($url->out(false), 'QRCODE,Q', X2 + 25, $y + 18, 25, 25, $styleqr, 'N');
                            $pdf->Text(20, 145, '');
                        }
                    }

                    $leftlabel = !$leftlabel;
                    $counter++;

                    if ($counter % 2 == 0) {
                        $y += LABEL_HEIGHT;
                        $first = false;
                    }

                    if ($counter % 10 == 0) {
                        $y = Y;
                        $first = true;
                        if ($counter < count($participants)) {
                            $pdf->AddPage();
                        }
                    }
                }
            }
        }
    }

} else { // If no rooms are set or no places are assigned.

    $participants = helper::getexamparticipants($moduleinstance, ['mode' => 'all'], ['matrnr']);

    if ($participants) {

        $counter = 0;
        $leftlabel = true;

        if ($counter < count($participants)) {
            $pdf->AddPage();
        }
        $y = Y;

        foreach ($participants as $participant) { // Construct label.

            $room = '';
            $place = '';

            if ($participant->matrnr !== '-') {
                if ($leftlabel) { // Print left label.
                    $pdf->SetFont('helvetica', '', 12);
                    $pdf->MultiCell(90, 5, $coursename, 0, 'C', 0, 0, X1, $y, true);
                    $pdf->SetFont('helvetica', 'B', 12);
                    $pdf->MultiCell(90, 5, $participant->lastname . ', ' . $participant->firstname .
                        ' (' . $participant->matrnr . ')', 0, 'C', 0, 0, X1, $y + 6, true);
                    $pdf->SetFont('helvetica', '', 10);
                    $pdf->MultiCell(21, 5, $date, 0, 'C', 0, 0, X1 + 1, $y + 21, true);
                    $pdf->MultiCell(21, 5, strtoupper($semester), 0, 'C', 0, 0, X1, $y + 32, true);
                    $pdf->SetFont('helvetica', 'B', 14);
                    $pdf->MultiCell(18, 5, ++$idcounter, 0, 'C', 0, 0, X1 + 68, $y + 32, true);

                    if ($mode == 'barcode') {
                        $checksum = helper::buildchecksumexamlabels('00000' . $participant->matrnr);
                        $pdf->write1DBarcode('00000' . $participant->matrnr . $checksum, 'EAN13',
                            X1 + 22, $y + 20, 37, 19, 0.4, $style, 'C');
                    } else {
                        $url = new moodle_url("/mod/exammanagement/inputresults.php",
                            ["id" => $id, 'matrnr' => $participant->matrnr]);

                        $pdf->write2DBarcode($url->out(false), 'QRCODE,Q', X1 + 25, $y + 18, 25, 25, $styleqr, 'N');
                        $pdf->Text(20, 145, '');
                    }

                } else { // Print right label.
                    $pdf->SetFont('helvetica', '', 12);
                    $pdf->MultiCell(90, 5, $coursename, 0, 'C', 0, 0, X2, $y, true);
                    $pdf->SetFont('helvetica', 'B', 12);
                    $pdf->MultiCell(90, 5, $participant->lastname . ', ' . $participant->firstname .
                        ' (' . $participant->matrnr . ')', 0, 'C', 0, 0, X2, $y + 6, true);
                    $pdf->SetFont('helvetica', '', 10);
                    $pdf->MultiCell(21, 5, $date, 0, 'C', 0, 0, X2 + 1, $y + 21, true);
                    $pdf->MultiCell(21, 5, strtoupper($semester), 0, 'C', 0, 0, X2, $y + 32, true);
                    $pdf->SetFont('helvetica', 'B', 14);
                    $pdf->MultiCell(18, 5, ++$idcounter, 0, 'C', 0, 0, X2 + 68, $y + 32, true);

                    if ($mode == 'barcode') {
                        $checksum = helper::buildchecksumexamlabels('00000' . $participant->matrnr);
                        $pdf->write1DBarcode('00000' . $participant->matrnr . $checksum, 'EAN13',
                            X2 + 22, $y + 20, 37, 19, 0.4, $style, 'C');
                    } else {
                        $url = new moodle_url("/mod/exammanagement/inputresults.php",
                            ["id" => $id, 'matrnr' => $participant->matrnr]);

                        $pdf->write2DBarcode($url->out(false), 'QRCODE,Q', X2 + 25, $y + 18, 25, 25, $styleqr, 'N');
                        $pdf->Text(20, 145, '');
                    }
                }

                $leftlabel = !$leftlabel;
                $counter++;

                if ($counter % 2 == 0) {
                    $y += LABEL_HEIGHT;
                    $first = false;
                }

                if ($counter % 10 == 0) {
                    $y = Y;
                    $first = false;
                    if ($counter < count($participants)) {
                        $pdf->AddPage();
                    }

                }
            }
        }
    }
}

// Generate filename without umlaute.
$umlaute = ["/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/"];
$replace = ["ae", "oe", "ue", "Ae", "Oe", "Ue", "ss"];
$filenameumlaute = get_string("examlabels", "mod_exammanagement"). '_' . helper::getcleancoursecategoryname() .
    '_' . $course->fullname. '_' . $moduleinstance->name . '.pdf';
$filename = preg_replace($umlaute, $replace, $filenameumlaute);

// Close and output PDF document.
$pdf->Output($filename, 'D');
