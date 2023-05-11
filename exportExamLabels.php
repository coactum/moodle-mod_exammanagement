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
 * Outputs pdf file for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\ldap\ldapManager;
use mod_exammanagement\pdfs\examLabels;
use moodle_url;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or.
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$mode  = optional_param('mode', 'barcode', PARAM_TEXT);

$exammanagementinstanceobj = exammanagementInstance::getInstance($id, $e);
$userobj = User::getInstance($id, $e, $exammanagementinstanceobj->getCm()->instance);
$moodleobj = Moodle::getInstance($id, $e);
$ldapmanagerobj = LDAPManager::getInstance();

if ($moodleobj->checkCapability('mod/exammanagement:viewinstance')) {

    if ($exammanagementinstanceobj->isExamDataDeleted()) {
        $moodleobj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
    } else {

        global $CFG, $SESSION;

        if (!isset($exammanagementinstanceobj->moduleinstance->password) || (isset($exammanagementinstanceobj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))) { // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

            if (!$userobj->getParticipantsCount()) {
                $moodleobj->redirectToOverviewPage('forexam', get_string('no_participants_added', 'mod_exammanagement'), 'error');
            }

            if (!$ldapmanagerobj->isLDAPenabled()) { // Cancel export if no matrnrs are availiable because ldap is not enabled or configured.
                $moodleobj->redirectToOverviewPage('forexam', get_string('not_possible_no_matrnr', 'mod_exammanagement') . ' '. get_string('ldapnotenabled', 'mod_exammanagement'), 'error');
            } else if (!$ldapmanagerobj->isLDAPconfigured()) {
                $moodleobj->redirectToOverviewPage('forexam', get_string('not_possible_no_matrnr', 'mod_exammanagement') . ' '. get_string('ldapnotconfigured', 'mod_exammanagement'), 'error');
            }

            // Include pdf.
            require_once(__DIR__.'/classes/pdfs/examLabels.php');

            define('LABEL_HEIGHT', 52);
            define('X1', 7.7 + 2); // Plus Offset within Label.
            define('X2', 106.3 + 2); // Plus Offset within Label.
            define('Y', 21 + 2); // Plus Offset within Label.

            // Include the main TCPDF library (search for installation path).
            require_once(__DIR__.'/../../config.php');
            require_once($CFG->libdir.'/pdflib.php');

            // Create new PDF document.
            $pdf = new examLabels(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Set document information.
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($exammanagementinstanceobj->getMoodleSystemName());
            $pdf->SetTitle(get_string('examlabels', 'mod_exammanagement') . ': ' . $exammanagementinstanceobj->getCourse()->fullname . ', '. $exammanagementinstanceobj->moduleinstance->name);
            $pdf->SetSubject(get_string('examlabels', 'mod_exammanagement'));
            $pdf->SetKeywords(get_string('examlabels', 'mod_exammanagement') . ', ' . $exammanagementinstanceobj->getCourse()->fullname . ', ' . $exammanagementinstanceobj->moduleinstance->name);

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

            $style = array(
                'position' => 'S',
                'border' => false,
                'padding' => 0,
                'fgcolor' => array(0, 0, 0),
                'bgcolor' => false,
                'text' => true,
                'font' => 'helvetica',
                'fontsize' => 8,
                'stretchtext' => 4,
            );

            // Get users and construct content for document.
            $roomsarray = json_decode($exammanagementinstanceobj->moduleinstance->rooms);
            $idcounter = 0;

            $examname = $exammanagementinstanceobj->getCourse()->fullname;
            $semester = $exammanagementinstanceobj->getCleanCourseCategoryName();

            $date = $exammanagementinstanceobj->getHrExamtime();

            $lineoffset = -7;

            if ($mode !== 'barcode') {

                $styleqr = array(
                    'border' => false,
                    'padding' => 0,
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => false
                );
            }

            $first = true;

            if ($roomsarray && $exammanagementinstanceobj->placesAssigned()) { // If rooms are already set and places are assigned.

                foreach ($roomsarray as $roomid) {

                    $participants = $userobj->getExamParticipants(array('mode' => 'room', 'id' => $roomid), array('matrnr'), 'name', false, null, 10, 'withmatrnr');

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
                                    $pdf->MultiCell(90, 5, $examname, 0, 'C', 0, 0, X1, $y, true);
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
                                        $checksum = $exammanagementinstanceobj->buildChecksumExamLabels('00000' . $participant->matrnr);
                                        $pdf->write1DBarcode('00000' . $participant->matrnr . $checksum, 'EAN13', X1 + 22, $y + 20, 37, 19, 0.4, $style, 'C');
                                    } else {
                                        $url = new moodle_url("/mod/exammanagement/inputResults.php", array("id" => $id, 'matrnr' => $participant->matrnr));

                                        $pdf->write2DBarcode($url->out(false), 'QRCODE,Q', X1 + 25, $y + 18, 25, 25, $styleqr, 'N');
                                        $pdf->Text(20, 145, '');
                                    }

                                    // if ($first == false) {
                                    //     $linestyle = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 10, 10, 'color' => array(160, 160, 160));
                                    //     $pdf->Line(X1, $y + $lineoffset, X1 + 82, $y + $lineoffset, $linestyle);
                                    // }

                                } else { // Print right label.
                                    $pdf->SetFont('helvetica', '', 12);
                                    $pdf->MultiCell(90, 5, $examname, 0, 'C', 0, 0, X2, $y, true);
                                    $pdf->SetFont('helvetica', 'B', 12);
                                    $pdf->MultiCell(90, 5, $participant->lastname . ', ' . $participant->firstname .
                                        ' (' . $participant->matrnr . ')', 0, 'C', 0, 0, X2, $y + 6, true);
                                    $pdf->SetFont('helvetica', '', 10);
                                    $pdf->MultiCell(21, 5, $date, 0, 'C', 0, 0, X2 + 1, $y + 21, true);
                                    $pdf->MultiCell(21, 5, strtoupper($semester), 0, 'C', 0, 0, X2, $y + 32, true);
                                    $pdf->MultiCell(32, 5, get_string('room', 'mod_exammanagement') . ': ' .$participant->roomname, 0, 'L', 0, 0, X2 + 61, $y + 21, true);
                                    $pdf->MultiCell(32, 5, get_string('place', 'mod_exammanagement') . ': ' .
                                        $participant->place, 0, 'L', 0, 0, X2 + 61, $y + 26 + $roomnamelinesoffsety, true);
                                    $pdf->SetFont('helvetica', 'B', 14);
                                    $pdf->MultiCell(18, 5, ++$idcounter, 0, 'C', 0, 0, X2 + 68, $y + 34, true);

                                    if ($mode == 'barcode') {
                                        $checksum = $exammanagementinstanceobj->buildChecksumExamLabels('00000' . $participant->matrnr);
                                        $pdf->write1DBarcode('00000' . $participant->matrnr . $checksum, 'EAN13', X2 + 22, $y + 20, 37, 19, 0.4, $style, 'C');
                                    } else {
                                        $url = new moodle_url("/mod/exammanagement/inputResults.php", array("id" => $id, 'matrnr' => $participant->matrnr));

                                        $pdf->write2DBarcode($url->out(false), 'QRCODE,Q', X2 + 25, $y + 18, 25, 25, $styleqr, 'N');
                                        $pdf->Text(20, 145, '');
                                    }

                                    // if ($first == false) {
                                    //     $linestyle = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 10, 10, 'color' => array(160, 160, 160));
                                    //     $pdf->Line(X2, $y + $lineoffset, X2 + 82, $y + $lineoffset, $linestyle);
                                    // }
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

                $participants = $userobj->getExamParticipants(array('mode' => 'all'), array('matrnr'));

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
                                $pdf->MultiCell(90, 5, $examname, 0, 'C', 0, 0, X1, $y, true);
                                $pdf->SetFont('helvetica', 'B', 12);
                                $pdf->MultiCell(90, 5, $participant->lastname . ', ' . $participant->firstname . ' (' . $participant->matrnr . ')', 0, 'C', 0, 0, X1, $y + 6, true);
                                $pdf->SetFont('helvetica', '', 10);
                                $pdf->MultiCell(21, 5, $date, 0, 'C', 0, 0, X1 + 1, $y + 21, true);
                                $pdf->MultiCell(21, 5, strtoupper($semester), 0, 'C', 0, 0, X1, $y + 32, true);
                                $pdf->SetFont('helvetica', 'B', 14);
                                $pdf->MultiCell(18, 5, ++$idcounter, 0, 'C', 0, 0, X1 + 68, $y + 32, true);

                                if ($mode == 'barcode') {
                                    $checksum = $exammanagementinstanceobj->buildChecksumExamLabels('00000' . $participant->matrnr);
                                    $pdf->write1DBarcode('00000' . $participant->matrnr . $checksum, 'EAN13', X1 + 22, $y + 20, 37, 19, 0.4, $style, 'C');
                                } else {
                                    $url = new moodle_url("/mod/exammanagement/inputResults.php", array("id" => $id, 'matrnr' => $participant->matrnr));

                                    $pdf->write2DBarcode($url->out(false), 'QRCODE,Q', X1 + 25, $y + 18, 25, 25, $styleqr, 'N');
                                    $pdf->Text(20, 145, '');
                                }

                                // if ($first == false) {
                                //     $linestyle = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 10, 10, 'color' => array(160, 160, 160));
                                //     $pdf->Line(X1, $y + $lineoffset, X1 + 82, $y + $lineoffset, $linestyle);
                                // }

                            } else { // Print right label.
                                $pdf->SetFont('helvetica', '', 12);
                                $pdf->MultiCell(90, 5, $examname, 0, 'C', 0, 0, X2, $y, true);
                                $pdf->SetFont('helvetica', 'B', 12);
                                $pdf->MultiCell(90, 5, $participant->lastname . ', ' . $participant->firstname . ' (' . $participant->matrnr . ')', 0, 'C', 0, 0, X2, $y + 6, true);
                                $pdf->SetFont('helvetica', '', 10);
                                $pdf->MultiCell(21, 5, $date, 0, 'C', 0, 0, X2 + 1, $y + 21, true);
                                $pdf->MultiCell(21, 5, strtoupper($semester), 0, 'C', 0, 0, X2, $y + 32, true);
                                $pdf->SetFont('helvetica', 'B', 14);
                                $pdf->MultiCell(18, 5, ++$idcounter, 0, 'C', 0, 0, X2 + 68, $y + 32, true);

                                if ($mode == 'barcode') {
                                    $checksum = $exammanagementinstanceobj->buildChecksumExamLabels('00000' . $participant->matrnr);
                                    $pdf->write1DBarcode('00000' . $participant->matrnr . $checksum, 'EAN13', X2 + 22, $y + 20, 37, 19, 0.4, $style, 'C');
                                } else {
                                    $url = new moodle_url("/mod/exammanagement/inputResults.php", array("id" => $id, 'matrnr' => $participant->matrnr));

                                    $pdf->write2DBarcode($url->out(false), 'QRCODE,Q', X2 + 25, $y + 18, 25, 25, $styleqr, 'N');
                                    $pdf->Text(20, 145, '');
                                }

                                // if ($first == false) {
                                //     $linestyle = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 10, 10, 'color' => array(160, 160, 160));
                                //     $pdf->Line(X2, $y + $lineoffset, X2 + 82, $y + $lineoffset, $linestyle);
                                // }
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
            $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
            $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
            $filenameumlaute = get_string("examlabels", "mod_exammanagement"). '_' . $exammanagementinstanceobj->getCleanCourseCategoryName() .
                '_' . $exammanagementinstanceobj->getCourse()->fullname. '_' . $exammanagementinstanceobj->moduleinstance->name . '.pdf';
            $filename = preg_replace($umlaute, $replace, $filenameumlaute);

            // Close and output PDF document
            // This method has several options, check the source code documentation for more information.
            $pdf->Output($filename, 'D');

        } else { // If user has not entered correct password for this session: show enterPasswordPage.
            redirect ($exammanagementinstanceobj->getExammanagementUrl('checkpassword', $exammanagementinstanceobj->getCm()->id), null, null, null);
        }
    }
} else {
    $moodleobj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
