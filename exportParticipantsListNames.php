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

use mod_exammanagement\pdfs;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$MoodleObj = Moodle::getInstance($id, $e);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

    global $CFG;

    //include pdf
    require_once(__DIR__.'/classes/pdfs/participantsList.php');

    define("WIDTH_COLUMN_NAME", 200);
    define("WIDTH_COLUMN_FIRSTNAME", 150);
    define("WIDTH_COLUMN_MATNO", 60);
    define("WIDTH_COLUMN_ROOM", 90);
    define("WIDTH_COLUMN_PLACE", 70);

    if(!$ExammanagementInstanceObj->isStateOfPlacesCorrect() || $ExammanagementInstanceObj->isStateOfPlacesError()){
      $MoodleObj->redirectToOverviewPage('forexam', 'Noch keine Sitzplätze zugewiesen. Sitzplanexport noch nicht möglich', 'error');
    }

    // Include the main TCPDF library (search for installation path).
    require_once(__DIR__.'/../../config.php');
    require_once($CFG->libdir.'/pdflib.php');

    // create new PDF document
    $pdf = new pdfs\participantsList(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('PANDA');
    $pdf->SetTitle($ExammanagementInstanceObj->getCourse()->fullname);
    $pdf->SetSubject(get_string('participantslist_names', 'mod_exammanagement'));
    $pdf->SetKeywords(get_string('participantslist_names', 'mod_exammanagement') . ', ' . $ExammanagementInstanceObj->getCourse()->fullname);

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

    // set margins
    $pdf->SetMargins(25, 55, 25);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 19);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set some language-dependent strings (optional)
    // if (@file_exists(__DIR__.'/lang/eng.php')) {
    // 	require_once(__DIR__.'/lang/eng.php');
    // 	$pdf->setLanguageArray($l);
    // }

    // ---------------------------------------------------------

    // Set font
    // dejavusans is a UTF-8 Unicode font, if you only need to
    // print standard ASCII chars, you can use core fonts like
    // helvetica or times to reduce file size.
    $pdf->SetFont('freeserif', '', 10);

    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage();

    // get users and construct content for document
    $assignedPlaces = $ExammanagementInstanceObj->getAssignedPlaces();
    $fill = false;
    $previousRoom;
    $tbl = $ExammanagementInstanceObj->getParticipantsListTableHeader();

    foreach ($assignedPlaces as $roomObj){
      $currentRoom = $roomObj;

      if (!empty($previousRoom) && $currentRoom != $previousRoom) {
          //new room -> finish and print current table and begin new page
          $tbl .= "</table>";
          $pdf->writeHTML($tbl, true, false, false, false, '');
          $pdf->AddPage();
          $fill = false;
          $tbl = $ExammanagementInstanceObj->getParticipantsListTableHeader();
        }

        usort($roomObj->assignments, function($a, $b){ //sort array by custom user function
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

      foreach ($roomObj->assignments as $assignment){
        $user = $ExammanagementInstanceObj->getMoodleUser($assignment->userid);

        $tbl .= ($fill) ? "<tr bgcolor=\"#DDDDDD\">" : "<tr>";
        $tbl .= "<td width=\"" . WIDTH_COLUMN_NAME . "\">" . $user->lastname . "</td>";
        $tbl .= "<td width=\"" . WIDTH_COLUMN_FIRSTNAME . "\">" . $user->firstname . "</td>";
        $tbl .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\">" . $ExammanagementInstanceObj->getUserMatrNrPO($assignment->userid) . "</td>";
        $tbl .= "<td width=\"" . WIDTH_COLUMN_ROOM . "\" align=\"center\">" . $currentRoom->roomname . "</td>";
        $tbl .= "<td width=\"" . WIDTH_COLUMN_PLACE . "\" align=\"center\">" . $assignment->place . "</td>";
        $tbl .= "</tr>";

        $fill = !$fill;

      }

      $previousRoom = $currentRoom;

    }

    $tbl .= "</table>";

    // Print text using writeHTMLCell()

    $pdf->writeHTML($tbl, true, false, false, false, '');

    //generate filename without umlaute
    $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
    $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
    $filenameUmlaute = get_string("participantslist_names", "mod_exammanagement") . $ExammanagementInstanceObj->moduleinstance->categoryid . '' . $ExammanagementInstanceObj->getCourse()->fullname.'.pdf';
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
