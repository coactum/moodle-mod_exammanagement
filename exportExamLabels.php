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
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\pdfs\examLabels;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$MoodleObj = Moodle::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

    global $CFG;

    //include pdf
    require_once(__DIR__.'/classes/pdfs/examLabels.php');

    if(!$ExammanagementInstanceObj->isStateOfPlacesCorrect() || $ExammanagementInstanceObj->isStateOfPlacesError() || $ExammanagementInstanceObj->isStateOfPlacesError()){
      $MoodleObj->redirectToOverviewPage('forexam', 'Noch keine Sitzplätze zugewiesen. Prüfungsetikettenexport noch nicht möglich', 'error');
    }

    // Include the main TCPDF library (search for installation path).
    require_once(__DIR__.'/../../config.php');
    require_once($CFG->libdir.'/pdflib.php');

    define('LABEL_HEIGHT', 51);
    define('X1', 7.7 + 2); //plus Offset within Label
    define('X2', 106.3 + 2); //plus Offset within Label
    define('Y', 21 + 2); //plus Offset within Label

    $exam_name = $ExammanagementInstanceObj->getCourse()->fullname . ' (' . $ExammanagementInstanceObj->getModuleinstance()->name .')';
    $semester = $ExammanagementInstanceObj->getModuleinstance()->categoryid;

    $date = $ExammanagementInstanceObj->getHrExamtime();

    // create new PDF document
    $pdf = new examLabels(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('PANDA');
    $pdf->SetTitle($ExammanagementInstanceObj->getCourse()->fullname);
    $pdf->SetSubject(get_string('examlabels', 'mod_exammanagement'));
    $pdf->SetKeywords(get_string('examlabels', 'mod_exammanagement') . ', ' . $ExammanagementInstanceObj->getCourse()->fullname);

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    //set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

    //set auto page breaks
    $pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);

    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    //set some language-dependent strings
    //$pdf->setLanguageArray($l);

    // remove default header
    $pdf->setPrintHeader(false);

    // ---------------------------------------------------------


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

    // get users and construct content for document
    $assignedPlaces = $ExammanagementInstanceObj->getAssignedPlaces();
    $IDcounter = 0;

    foreach ($assignedPlaces as $roomObj){

        usort($roomObj->assignments, function($a, $b){ //sort array of assignments by name
          global $ExammanagementInstanceObj;
          $aFirstname = $ExammanagementInstanceObj->getMoodleUser($a->userid)->firstname;
          $aLastname = $ExammanagementInstanceObj->getMoodleUser($a->userid)->lastname;
          $bFirstname = $ExammanagementInstanceObj->getMoodleUser($b->userid)->firstname;
          $bLastname = $ExammanagementInstanceObj->getMoodleUser($b->userid)->lastname;

          if ($aLastname == $bLastname) { //if names are even sort by first name
              return strcmp($aFirstname, $bFirstname);
          } else{
              return strcmp($aLastname, $bLastname); // else sort by last name
          }

        });

        $leftLabel = true;
      	$counter = 0;
      	$pdf->AddPage();
      	$y = Y;

      foreach ($roomObj->assignments as $assignment){ // construct label

        $user = $ExammanagementInstanceObj->getMoodleUser($assignment->userid);

        $name = utf8_encode($user->lastname);
        $firstname = utf8_encode($user->firstname);
        $matrNr = $ExammanagementInstanceObj->getUserMatrNrPO($assignment->userid);
        $room = $roomObj->roomname;
        $place = $assignment->place;

        if ($leftLabel) { //print left label
          	$pdf->SetFont('helvetica', '', 12);
          	$pdf->MultiCell(90, 5, $exam_name, 0, 'C', 0, 0, X1, $y, true);
          	$pdf->SetFont('helvetica', 'B', 12);
          	$pdf->MultiCell(90, 5, $name . ', ' . $firstname . ' (' . $matrNr . ')', 0, 'C', 0, 0, X1, $y + 13, true);
          	$pdf->SetFont('helvetica', '', 10);
          	$pdf->MultiCell(21, 5, $date, 0, 'L', 0, 0, X1 + 1, $y + 25, true);
          	$pdf->MultiCell(21, 5, $semester, 0, 'C', 0, 0, X1, $y + 36, true);
          	$pdf->MultiCell(32, 5, get_string('room', 'mod_exammanagement') . ': ' . $room, 0, 'L', 0, 0, X1 + 61, $y + 25, true);
          	$pdf->MultiCell(32, 5, get_string('place', 'mod_exammanagement') . ': ' . $place, 0, 'L', 0, 0, X1 + 61, $y + 30, true);
          	$pdf->SetFont('helvetica', 'B', 14);
          	$pdf->MultiCell(18, 5, ++$IDcounter, 0, 'C', 0, 0, X1 + 68, $y + 36, true);

          	$checksum = $ExammanagementInstanceObj->buildChecksumExamLabels('00000' . $matrNr);
          	$pdf->write1DBarcode('00000' . $matrNr . $checksum, 'EAN13', X1 + 22, $y + 27, 37, 19, 0.4, $style, 'C');

        } else { //print right label
            $pdf->SetFont('helvetica', '', 12);
            $pdf->MultiCell(90, 5, $exam_name, 0, 'C', 0, 0, X2, $y, true);
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->MultiCell(90, 5, $name . ', ' . $firstname . ' (' . $matrNr . ')', 0, 'C', 0, 0, X2, $y + 13, true);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(21, 5, $date, 0, 'L', 0, 0, X2 + 1, $y + 25, true);
            $pdf->MultiCell(21, 5, $semester, 0, 'C', 0, 0, X2, $y + 36, true);
            $pdf->MultiCell(32, 5, get_string('room', 'mod_exammanagement') . ': ' .$room, 0, 'L', 0, 0, X2 + 61, $y + 25, true);
            $pdf->MultiCell(32, 5, get_string('place', 'mod_exammanagement') . ': ' . $place, 0, 'L', 0, 0, X2 + 61, $y + 30, true);
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->MultiCell(18, 5, ++$IDcounter, 0, 'C', 0, 0, X2 + 68, $y + 36, true);

            $checksum = $ExammanagementInstanceObj->buildChecksumExamLabels('00000' . $matrNr);
            $pdf->write1DBarcode('00000' . $matrNr . $checksum, 'EAN13', X2 + 22, $y + 27, 37, 19, 0.4, $style, 'C');
        }

        $leftLabel = !$leftLabel;
        $counter++;

        if ($counter % 2 == 0) {
          $y += LABEL_HEIGHT;
        }

        if ($counter % 10 == 0) {
          $y = Y;
          //if ($counter != count($participantLogins)) {
            $pdf->AddPage();
          //}

        }
      }
    }

    //generate filename without umlaute
    $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
    $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
    $filenameUmlaute = get_string("examlabels", "mod_exammanagement"). '_' . $ExammanagementInstanceObj->moduleinstance->categoryid . '_' . $ExammanagementInstanceObj->getCourse()->fullname.'.pdf';
    $filename = preg_replace($umlaute, $replace, $filenameUmlaute);

    // ---------------------------------------------------------

    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
    $pdf->Output($filename, 'D');

    //============================================================+
    // END OF FILE
    //============================================================+
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
