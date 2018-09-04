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

use mod_exammanagement\pdfs\seatingPlan;
use mod_exammanagement\ldap\ldapManager;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once(__DIR__.'/classes/ldap/ldapManager.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$LdapManagerObj = ldapManager::getInstance($id, $e);
$MoodleObj = Moodle::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();


if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

    global $CFG;

    //include pdf
    require_once(__DIR__.'/classes/pdfs/seatingPlan.php');

    if(!$ExammanagementInstanceObj->isStateOfPlacesCorrect() || $ExammanagementInstanceObj->isStateOfPlacesError()){
      $MoodleObj->redirectToOverviewPage('forexam', 'Noch keine Sitzplätze zugewiesen. Sitzplanexport noch nicht möglich', 'error');
    }

    // Include the main TCPDF library (search for installation path).
    require_once(__DIR__.'/../../config.php');
    require_once($CFG->libdir.'/pdflib.php');

    define("WIDTH_COLUMN_MATNO", 60);
    define("WIDTH_COLUMN_ROOM", 90);
    define("WIDTH_COLUMN_PLACE", 70);

    // create new PDF document
    $pdf = new seatingPlan(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('PANDA');
    $pdf->SetTitle(get_string('seatingplan', 'mod_exammanagement') . ': ' . $ExammanagementInstanceObj->getCourse()->fullname . ', '. $ExammanagementInstanceObj->moduleinstance->name);
    $pdf->SetSubject(get_string('seatingplan', 'mod_exammanagement'));
    $pdf->SetKeywords(get_string('seatingplan', 'mod_exammanagement') . ', ' . $ExammanagementInstanceObj->getCourse()->fullname . ', ' . $ExammanagementInstanceObj->moduleinstance->name);

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

    //set margins
    $pdf->SetMargins(25, 55, 25);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 19);

    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // Set font
    $pdf->SetFont('freeserif', '', 10);

    // ---------------------------------------------------------

    $pdf->AddPage();

    // get users and construct content for document
    $assignedPlaces = $ExammanagementInstanceObj->getAssignedPlaces();
    $fill = false;
    $previousRoom;
    $tbl = $ExammanagementInstanceObj->getSeatingPlanTableHeader();

    foreach ($assignedPlaces as $roomObj){
      $currentRoom = $roomObj;

      if (!empty($previousRoom) && $currentRoom != $previousRoom) {
          //new room -> finish and print current table and begin new page
          $tbl .= "</table>";
          $pdf->writeHTML($tbl, true, false, false, false, '');
          $pdf->AddPage();
          $fill = false;
          $tbl = $ExammanagementInstanceObj->getSeatingPlanTableHeader();
        }

        usort($roomObj->assignments, function($a, $b){ //sort array by custom user function

          return strcmp($a->place, $b->place); // sort by place

        });

        if($LdapManagerObj->is_LDAP_config()){
            $ldapConnection = $LdapManagerObj->connect_ldap();
        }

      foreach ($roomObj->assignments as $assignment){
        $user = $ExammanagementInstanceObj->getMoodleUser($assignment->userid);

        if($LdapManagerObj->is_LDAP_config()){
            $matrnr = $LdapManagerObj->uid2studentid($ldapConnection, $assignment->userid);
        } else {
            $matrnr = $LdapManagerObj->getIMTLogin2MatriculationNumberTest($assignment->userid);
        }

        $tbl .= ($fill) ? "<tr bgcolor=\"#DDDDDD\">" : "<tr>";
        $tbl .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\">" . $matrnr . "</td>";
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


    foreach ($assignedPlaces as $roomObj){

      $roomName = $roomObj->roomname;

    	switch ($roomName) {
    		case "AudiMax":
    			$x = 30;
    			$y = 30;
    			$width = '250';
    			break;

    		case "C1":
    			$x = 50;
    			$y = 30;
    			$width = '180';
    			break;

    		case "C2":
    			$x = 70;
    			$y = 50;
    			$width = '140';
    			break;

    		case "G":
    			$x = 35;
    			$y = 45;
    			$width = '225';
    			break;

    		case "P52.01":
    			$x = 35;
    			$y = 50;
    			$width = '225';
    			break;

    		case "P52.03":
    			$x = 55;
    			$y = 50;
    			$width = '175';
    			break;

    		case "P62.01":
    			$x = 35;
    			$y = 55;
    			$width = '225';
    			break;

    		case "P62.03":
    			$x = 70;
    			$y = 60;
    			$width = '140';
    			break;

    		case "P72.01":
    			$x = 45;
    			$y = 45;
    			$width = '200';
    			break;

    		case "P72.03":
    			$x = 60;
    			$y = 45;
    			$width = '180';
    			break;

    		case "O0.207":
    			$x = 25;
    			$y = 55;
    			$width = '240';
    			break;

    		case "O1.267":
    			$x = 85;
    			$y = 50;
    			$width = '120';
    			break;

    		case "L1":
    		case "L2":
    			$x = 80;
    			$y = 30;
    			$width = '140';
    			break;

    		case "L1.202":
    			$x = 80;
    			$y = 30;
    			$width = '140';
    			break;

        case "L2.202":
          $x = 54;
          $y = 90;
          $width = '503';
          break;

    		case "Eggelandhalle":
    			$x = 100;
    			$y = 30;
    			$width = '140';
    			break;

    		// any case where no plan is available
    		default:return;

      }

      // ---------------------------------------------------------

      $svgFile = base64_decode($MoodleDBObj->getFieldFromDB('exammanagement_rooms', 'seatingplan', array('roomid' => $roomObj->roomid)));
      $numberofPlaces = count(json_decode($MoodleDBObj->getFieldFromDB('exammanagement_rooms', 'places', array('roomid' => $roomObj->roomid))));
      $maxSeats = get_string('total_seats', 'mod_exammanagement') . ": " . $numberofPlaces;

      $pdf->setPrintHeader(false);
      $pdf->addPage('L', 'A4');
      $pdf->SetFont('freeserif', '', 20);
      $pdf->Text(0, 15, get_string('seatingplan', 'mod_exammanagement'), false, false, true, 0, 0, 'R');
      $pdf->Text(15, 15, get_string('lecture_room', 'mod_exammanagement'));
      $pdf->SetFont('freeserif', 'B', 25);
      $pdf->Text(15, 25, $roomName);
      $pdf->SetFont('freeserif', '', 10);
      $pdf->Text(15, 180, get_string('places_differ', 'mod_exammanagement'));
      $pdf->Text(15, 185, get_string('places_alternative', 'mod_exammanagement'));
      $pdf->Text(15, 180, $maxSeats, false, false, true, 0, 0, 'R');
      $pdf->setTextColor(204, 0, 0);
      $pdf->Text(15, 185, get_string('numbered_seats_usable_seats', 'mod_exammanagement'), false, false, true, 0, 0, 'R');
      $pdf->setTextColor(0, 0, 0);
      //$pdf->ImageEps('data/upb_logo_full.ai', $x, $y, $width);
      //$pdf->ImageEps('../extensions/exam_organization/images/' . $roomObj->roomid . '.svg', $x, $y, $width);
      //$pdf->writeHTML($svgFile, true, false, false, false, '');

      $pdf->ImageSVG('@'.$svgFile, $x, $y, $w = '100', $h = "100", $link = '', $border=1, $fitonpage=false);

      //$pdf->ImageSVG('@'.$svgFile, $x, $y, $w = "100", $h = "100", $border=1, $fitonpage=false)
    }


    //generate filename without umlaute
    $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
    $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
    $filenameUmlaute = get_string("seatingplan", "mod_exammanagement") . '_' . $ExammanagementInstanceObj->moduleinstance->categoryid . '_' . $ExammanagementInstanceObj->getCourse()->fullname . '_' . $ExammanagementInstanceObj->moduleinstance->name . '.pdf';
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
