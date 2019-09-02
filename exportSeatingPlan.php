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
 * @copyright   coactum GmbH 2019
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

$sortmode  = optional_param('sortmode', 0, PARAM_TEXT);

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$UserObj = User::getInstance($id, $e);
$MoodleObj = Moodle::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

  if($ExammanagementInstanceObj->isExamDataDeleted()){
    $MoodleObj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
  } else {
    if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

      global $CFG, $SESSION;

      if (!$ExammanagementInstanceObj->getRoomsCount()) {
        $MoodleObj->redirectToOverviewPage('forexam', get_string('no_rooms_added', 'mod_exammanagement'), 'error');
      } else if (!$UserObj->getParticipantsCount()) {
          $MoodleObj->redirectToOverviewPage('forexam', get_string('no_participants_added', 'mod_exammanagement'), 'error');
      } else if(!$ExammanagementInstanceObj->allPlacesAssigned()){
          $MoodleObj->redirectToOverviewPage('forexam', get_string('not_all_places_assigned', 'mod_exammanagement'), 'error');
      }  

      if($sortmode == 'matrnr' && (!get_config('auth_ldap')->host_url && !(isset($SESSION->ldaptest) || (isset($SESSION->ldaptest) && !$SESSION->ldaptest === true)))){ // cancel export if seatingplan should be sorted by matrnr and no matrnr are availiable because no ldap is configured and user is not in testmode
        $MoodleObj->redirectToOverviewPage('forexam', get_string('not_possible_no_matrnr', 'mod_exammanagement'), 'error');
      }

      //include pdf
      require_once(__DIR__.'/classes/pdfs/seatingPlan.php');

      define("WIDTH_COLUMN_MATNO", 90);
      define("WIDTH_COLUMN_ROOM", 90);
      define("WIDTH_COLUMN_PLACE", 90);
      define("WIDTH_COLUMN_MIDDLE", 30);

      // Include the main TCPDF library (search for installation path).
      require_once(__DIR__.'/../../config.php');
      require_once($CFG->libdir.'/pdflib.php');

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

      // get users and construct user tables for document
      $roomsArray = json_decode($ExammanagementInstanceObj->moduleinstance->rooms);
      
      $roomsCount = 0;
      
      foreach ($roomsArray as $roomID){

        $participantsArray = $UserObj->getAllExamParticipantsByRoom($roomID);

        if($participantsArray){

          foreach ($participantsArray as $key => $participant){
              $participant->matrnr = $UserObj->getUserMatrNr($participant->moodleuserid, $participant->imtlogin);
          }

          if($sortmode == 'place'){
            usort($participantsArray, function($a, $b){ //sort array by custom user function

              return strnatcmp($a->place, $b->place); // sort by place
    
            });
          } else if($sortmode == 'matrnr'){
            usort($participantsArray, function($a, $b){ //sort array by custom user function

              global $UserObj;

              return strnatcmp($a->matrnr, $b->matrnr); // sort by matrnr
    
            });
          }

          $leftCol = array();
          $rightCol = array();
          $tbl;

          foreach ($participantsArray as $key => $participant){
            $matrnr = $participant->matrnr;

            if($matrnr === '-'){
              $matrnr = $UserObj->getMoodleUser($participant->moodleuserid, $participant->imtlogin)->firstname . ' ' . $UserObj->getMoodleUser($participant->moodleuserid, $participant->imtlogin)->lastname;
            }

            if ($key % 70 < 35) {
              $leftCol[] = array("matrnr" => $matrnr, "roomname" => $participant->roomname, "place" => $participant->place);
            } else {
              $rightCol[] = array("matrnr" => $matrnr, "roomname" => $participant->roomname, "place" => $participant->place);
            }
          
            if ($key % 70 == 69) {
              $tbl = $ExammanagementInstanceObj->getSeatingPlanTable($leftCol, $rightCol);
              $pdf->AddPage();
              $pdf->writeHTML($tbl, true, false, false, false, '');

              $leftCol = array();
              $rightCol = array();
            }
          }

          $tbl = $ExammanagementInstanceObj->getSeatingPlanTable($leftCol, $rightCol);
          
          // Print text using writeHTMLCell()
          $pdf->AddPage();
          $pdf->writeHTML($tbl, true, false, false, false, '');

          $roomsCount += 1;
        }

      }

      // construct svg-files pages
        foreach ($roomsArray as $key => $roomID){
          $roomObj = $ExammanagementInstanceObj->getRoomObj($roomID);

          if($roomObj){
            $roomName = $roomObj->name;
    
            // ---------------------------------------------------------
            if ($key < $roomsCount){
  
                $svgFile = base64_decode($MoodleDBObj->getFieldFromDB('exammanagement_rooms', 'seatingplan', array('roomid' => $roomObj->roomid)));
  
                if(isset($svgFile) && $svgFile !== ''){
  
                    $numberofPlaces = count(json_decode($MoodleDBObj->getFieldFromDB('exammanagement_rooms', 'places', array('roomid' => $roomObj->roomid))));
                    $maxSeats = get_string('total_seats', 'mod_exammanagement') . ": " . $numberofPlaces;
              
                    $pdf->setPrintHeader(false);
                    $pdf->AddPage('L', 'A4');
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
              
                    $pdf->ImageSVG('@'.$svgFile, $x = '40', $y = '30', $w = '1000', $h = '1000', $link = '', $border=1, $fitonpage=false);
                }
            }
          }
      }

      //generate filename without umlaute
      $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
      $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
      $filenameUmlaute = get_string("seatingplan", "mod_exammanagement") . '_' . $ExammanagementInstanceObj->getCleanCourseCategoryName() . '_' . $ExammanagementInstanceObj->getCourse()->fullname . '_' . $ExammanagementInstanceObj->moduleinstance->name . '.pdf';
      $filename = preg_replace($umlaute, $replace, $filenameUmlaute);

      // ---------------------------------------------------------

      // Close and output PDF document
      // This method has several options, check the source code documentation for more information.
      $pdf->Output($filename, 'D');

      //============================================================+
      // END OF FILE
      //============================================================+
    
    } else { // if user hasnt entered correct password for this session: show enterPasswordPage
      redirect ($ExammanagementInstanceObj->getExammanagementUrl('checkPassword', $ExammanagementInstanceObj->getCm()->id), null, null, null);
    }
  }

} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}