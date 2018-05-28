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
    $pdf->SetTitle($ExammanagementInstanceObj->getCourse()->fullname);
    $pdf->SetSubject(get_string('seatingplan', 'mod_exammanagement'));
    $pdf->SetKeywords(get_string('seatingplan', 'mod_exammanagement') . ', ' . $ExammanagementInstanceObj->getCourse()->fullname);

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
    $numberofPlaces = 22;

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

      foreach ($roomObj->assignments as $assignment){
        $user = $ExammanagementInstanceObj->getMoodleUser($assignment->userid);

        $tbl .= ($fill) ? "<tr bgcolor=\"#DDDDDD\">" : "<tr>";
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


    foreach ($assignedPlaces as $roomObj){

      $roomName = $roomObj->roomname;

    	switch ($roomName) {
    		case "AudiMax":
    			$x = 30;
    			$y = 30;
    			$width = 250;
    			break;

    		case "C1":
    			$x = 50;
    			$y = 30;
    			$width = 180;
    			break;

    		case "C2":
    			$x = 70;
    			$y = 50;
    			$width = 140;
    			break;

    		case "G":
    			$x = 35;
    			$y = 45;
    			$width = 225;
    			break;

    		case "P52.01":
    			$x = 35;
    			$y = 50;
    			$width = 225;
    			break;

    		case "P52.03":
    			$x = 55;
    			$y = 50;
    			$width = 175;
    			break;

    		case "P62.01":
    			$x = 35;
    			$y = 55;
    			$width = 225;
    			break;

    		case "P62.03":
    			$x = 70;
    			$y = 60;
    			$width = 140;
    			break;

    		case "P72.01":
    			$x = 45;
    			$y = 45;
    			$width = 200;
    			break;

    		case "P72.03":
    			$x = 60;
    			$y = 45;
    			$width = 180;
    			break;

    		case "O0.207":
    			$x = 25;
    			$y = 55;
    			$width = 240;
    			break;

    		case "O1.267":
    			$x = 85;
    			$y = 50;
    			$width = 120;
    			break;

    		case "L1":
    		case "L2":
    			$x = 80;
    			$y = 30;
    			$width = 140;
    			break;

    		case "L1.202":
    			$x = 80;
    			$y = 30;
    			$width = 140;
    			break;

        case "L2.202":
          $x = 54;
          $y = 90;
          $width = 503;
          break;

    		case "Eggelandhalle":
    			$x = 100;
    			$y = 30;
    			$width = 140;
    			break;

    		// any case where no plan is available
    		default:return;

      }

      // ---------------------------------------------------------

      $maxSeats = get_string('total_seats', 'mod_exammanagement') . ": " . $numberofPlaces;
      //$svgFile = base64_decode($MoodleDBObj->getFieldFromDB('exammanagement_rooms', 'seatingplan', array('roomid' => $roomObj->roomid)));
//       $svgFile = <<< EOF
//       <svg
//          xmlns:svg="http://www.w3.org/2000/svg"
//          xmlns="http://www.w3.org/2000/svg"
//          version="1.1"
//          id="svg7198"
//          width="443.61249"
//          height="443.6188">
//         <defs
//            id="defs7200" />
//         <path
//            id="path7176"
//            style="fill:#004d9d;fill-opacity:1;fill-rule:nonzero;stroke:none"
//            d="M 221.80625,443.6188 C 99.30625,443.6188 0,344.3125 0,221.8125 0,99.3075 99.30625,0 221.80625,0 c 122.5,0 221.80625,99.3075 221.80625,221.8125 0,58.8225 -23.36875,115.2438 -64.965,156.84 -41.5975,41.5975 -98.01375,64.9663 -156.84125,64.9663" />
//         <path
//            id="path7178"
//            style="fill:#ffffff;fill-opacity:1;fill-rule:nonzero;stroke:none"
//            d="m 275.02875,338.2425 -10.89375,0 0,-19.4975 -17.02625,0 0,19.5175 -11.48375,0 c 0,0 17.73375,-74.1025 17.895,-74.4287 l -18.93,0 c 0,0 -22.32,93.1787 -22.32,92.6312 l 33.88625,0 0,30.44 17.89625,0 0,-30.44 10.89875,0 0,-18.2612 0.0775,0.039 z M 155.3125,287.515 l 0,75.81 c -0.26375,9.3225 4.56,18.0525 12.5925,22.7938 8.0375,4.7412 18.0125,4.7412 26.045,0 8.0375,-4.7413 12.86125,-13.4713 12.5975,-22.7938 l 0,-75.7512 c -0.39125,-13.8675 -11.74375,-24.9075 -25.62,-24.9075 -13.8725,0 -25.225,11.04 -25.615,24.9075 l 0,-0.059 z m 17.42625,0 c 0,-4.6537 3.775,-8.4225 8.4225,-8.4225 4.65375,0 8.42375,3.7688 8.42375,8.4225 l 0,75.8888 c 0,4.6537 -3.77,8.4225 -8.42375,8.4225 -4.6475,0 -8.4225,-3.7688 -8.4225,-8.4225 l 0,-75.85 0,-0.039 z M 215.7175,102.09 c -1.035,-2.5775 -4.0575,-16.5187 12.48,-22.3237 4.74625,-1.4213 9.83875,-1.1475 14.41,0.7712 12.73875,4.6288 15.15625,21.7975 16.08875,32.6713 l 14.0225,0 0,-59.5563 -8.6025,0 -5.50375,8.115 c -7.41125,-4.5062 -15.4925,-7.8075 -23.94,-9.78 -18.55,-3.4762 -37.715,0.6838 -53.145,11.5475 -18.71,13.2913 -29.36,43.6675 -16.235,63.8575 13.13,20.1913 50.425,74.4925 50.425,74.4925 1.5725,2.315 2.45625,5.035 2.53875,7.8325 0.0838,5.9963 -3.0025,11.5963 -8.12,14.7313 -5.2925,3.755 -10.7325,4.565 -18.0375,2.9837 -17.29,-3.6962 -16.235,-25.5912 -16.235,-36.69 l -20.595,0 0,62.6413 13.2125,0 4.21875,-8.115 c 5.175,2.3337 14.73125,7.1387 22.8075,8.5012 21.7725,4.3413 45.0875,0.5463 60.87375,-13.5937 17.11,-14.2038 25.79125,-41.7038 8.48125,-65.2 -18.26125,-27.6375 -45.77625,-64.6288 -49.165,-73.0475 l 0.02,0.1612 z m 174.975,52.1488 15.40125,0 C 371.1275,58.6338 268.91625,5.5763 170.605,32.0075 72.295,58.4325 10.46375,155.5863 28.14875,255.84 45.83,356.0938 137.1725,426.2263 238.59375,417.4225 c 101.42,-8.8037 179.31125,-93.6275 179.4525,-195.43 l -131.69375,0 0,73.1738 21.7325,0 0,43.14 c 0,14.79 0,65.4787 -86.27875,65.4787 -30.41,0.044 -60.3375,-7.5687 -87.03125,-22.1387 l 0,-319.615 c 26.69375,-14.565 56.62125,-22.1825 87.03125,-22.1388 86.27875,0 86.27875,50.7325 86.27875,65.4838 l 0,48.7012 82.6075,0.1613 z" />
//       </svg>
// EOF;

//$svgFile = 'data/FC_Schalke_04_Logo.svg';
$svgFile = '';


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

      //$pdf->ImageSVG('@'.$svgFile, $x, $y, $width, $border=1, $fitonpage=false);
      $pdf->ImageSVG('@' . $svgFile);

    }


    //generate filename without umlaute
    $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
    $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
    $filenameUmlaute = get_string("seatingplan", "mod_exammanagement") . '_' . $ExammanagementInstanceObj->moduleinstance->categoryid . '_' . $ExammanagementInstanceObj->getCourse()->fullname.'.pdf';
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