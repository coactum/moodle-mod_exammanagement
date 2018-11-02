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

use mod_exammanagement\pdfs\participantsList;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$UserObj = User::getInstance($id, $e, $ExammanagementInstanceObj->moduleinstance->categoryid);
$MoodleObj = Moodle::getInstance($id, $e);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){
    global $CFG;

    $MoodleObj->setPage('exportParticipantsListPlaces');

    if(!$ExammanagementInstanceObj->isStateOfPlacesCorrect() || $ExammanagementInstanceObj->isStateOfPlacesError()){
      $MoodleObj->redirectToOverviewPage('forexam', 'Noch keine Sitzplätze zugewiesen. Listenexport noch nicht möglich', 'error');
    } else if (!$ExammanagementInstanceObj->getAllRoomIDsSortedByName()) {
      $MoodleObj->redirectToOverviewPage('forexam', 'Noch keine Prüfungsräume ausgewählt. Listenexport noch nicht möglich', 'error');
    } else if (!$UserObj->getParticipantsCount()) {
      $MoodleObj->redirectToOverviewPage('forexam', 'Noch keine Teilnehmer ausgewählt. Listenexport noch nicht möglich', 'error');
    }

    //include pdf
    require_once(__DIR__.'/classes/pdfs/participantsList.php');

    define("WIDTH_COLUMN_NAME", 200);
    define("WIDTH_COLUMN_FIRSTNAME", 150);
    define("WIDTH_COLUMN_MATNO", 60);
    define("WIDTH_COLUMN_ROOM", 90);
    define("WIDTH_COLUMN_PLACE", 70);

    // Include the main TCPDF library (search for installation path).
    require_once(__DIR__.'/../../config.php');
    require_once($CFG->libdir.'/pdflib.php');

    // create new PDF document
    $pdf = new participantsList(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('PANDA');
    $pdf->SetTitle(get_string('participantslist_places', 'mod_exammanagement') . ': ' . $ExammanagementInstanceObj->getCourse()->fullname . ', '. $ExammanagementInstanceObj->moduleinstance->name);
    $pdf->SetSubject(get_string('participantslist_places', 'mod_exammanagement'));
    $pdf->SetKeywords(get_string('participantslist_places', 'mod_exammanagement') . ', ' . $ExammanagementInstanceObj->getCourse()->fullname . ', ' . $ExammanagementInstanceObj->moduleinstance->name);

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
    $roomsArr = json_decode($ExammanagementInstanceObj->moduleinstance->rooms);
    $fill = false;
    $previousRoom;
    $tbl = $ExammanagementInstanceObj->getParticipantsListTableHeader();

    foreach ($roomsArr as $roomID){
      $currentRoom = $ExammanagementInstanceObj->getRoomObj($roomID);

      $participantsArray = $UserObj->getAllExamParticipantsByRoom($roomID);

      if($participantsArray){
        if (!empty($previousRoom) && $currentRoom != $previousRoom) {
          //new room -> finish and print current table and begin new page
          $tbl .= "</table>";
          $pdf->writeHTML($tbl, true, false, false, false, '');
          $pdf->AddPage();
          $fill = false;
          $tbl = $ExammanagementInstanceObj->getParticipantsListTableHeader();
        }

        usort($participantsArray, function($a, $b){ //sort array by custom user function

          return strcmp($a->place, $b->place); // sort by place

        });

      foreach ($participantsArray as $participant){
        $user = $UserObj->getMoodleUser($participant->moodleuserid);

        if($user){
            $name = utf8_encode($user->lastname);
            $firstname = utf8_encode($user->firstname);
        } else {
            $name = utf8_encode($participant->lastname);
            $firstname = utf8_encode($participant->firstname);
        }

        $matrnr = $UserObj->getUserMatrNr($participant->moodleuserid, $participant->imtlogin);

        $tbl .= ($fill) ? "<tr bgcolor=\"#DDDDDD\">" : "<tr>";
        $tbl .= "<td width=\"" . WIDTH_COLUMN_NAME . "\">" . $name . "</td>";
        $tbl .= "<td width=\"" . WIDTH_COLUMN_FIRSTNAME . "\">" . $firstname . "</td>";
        $tbl .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\">" . $matrnr . "</td>";
        $tbl .= "<td width=\"" . WIDTH_COLUMN_ROOM . "\" align=\"center\">" . $currentRoom->name . "</td>";
        $tbl .= "<td width=\"" . WIDTH_COLUMN_PLACE . "\" align=\"center\">" . $participant->place . "</td>";
        $tbl .= "</tr>";

        $fill = !$fill;
      }

      $previousRoom = $currentRoom;
      }

    }

    $tbl .= "</table>";

    // Print text using writeHTMLCell()

    $pdf->writeHTML($tbl, true, false, false, false, '');

    //generate filename without umlaute
    $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
    $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
    $filenameUmlaute = get_string("participantslist_places", "mod_exammanagement") . '_' . strtoupper($ExammanagementInstanceObj->moduleinstance->categoryid) . '_' . $ExammanagementInstanceObj->getCourse()->fullname . '_' . $ExammanagementInstanceObj->moduleinstance->name . '.pdf';
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
