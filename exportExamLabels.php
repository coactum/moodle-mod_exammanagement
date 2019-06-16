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

use mod_exammanagement\pdfs\examLabels;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$UserObj = User::getInstance($id, $e);
$MoodleObj = Moodle::getInstance($id, $e);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

  if($ExammanagementInstanceObj->isExamDataDeleted()){
    $MoodleObj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
  } else {
  
    if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

      global $CFG;

      if (!$UserObj->getParticipantsCount()) {
        $MoodleObj->redirectToOverviewPage('forexam', get_string('no_participants_added', 'mod_exammanagement'), 'error');
      }

      //include pdf
      require_once(__DIR__.'/classes/pdfs/examLabels.php');

      define('LABEL_HEIGHT', 51);
      define('X1', 7.7 + 2); //plus Offset within Label
      define('X2', 106.3 + 2); //plus Offset within Label
      define('Y', 21 + 2); //plus Offset within Label

      // Include the main TCPDF library (search for installation path).
      require_once(__DIR__.'/../../config.php');
      require_once($CFG->libdir.'/pdflib.php');

      //$exam_name = $ExammanagementInstanceObj->getCourse()->fullname . ' (' . $ExammanagementInstanceObj->getModuleinstance()->name .')';
      $exam_name = $ExammanagementInstanceObj->getCourse()->fullname;
      $semester = $ExammanagementInstanceObj->getCleanCourseCategoryName();

      $date = $ExammanagementInstanceObj->getHrExamtime();

      // create new PDF document
      $pdf = new examLabels(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

      // set document information
      $pdf->SetCreator(PDF_CREATOR);
      $pdf->SetAuthor('PANDA');
      $pdf->SetTitle(get_string('examlabels', 'mod_exammanagement') . ': ' . $ExammanagementInstanceObj->getCourse()->fullname . ', '. $ExammanagementInstanceObj->moduleinstance->name);
      $pdf->SetSubject(get_string('examlabels', 'mod_exammanagement'));
      $pdf->SetKeywords(get_string('examlabels', 'mod_exammanagement') . ', ' . $ExammanagementInstanceObj->getCourse()->fullname . ', ' . $ExammanagementInstanceObj->moduleinstance->name);

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
      $roomsArray = json_decode($ExammanagementInstanceObj->moduleinstance->rooms);
      $IDcounter = 0;

      if($roomsArray && $ExammanagementInstanceObj->allPlacesAssigned()){ // if rooms are already set and places are assigned

        foreach ($roomsArray as $roomID){

          $participantsArray = $UserObj->getAllExamParticipantsByRoom($roomID);
    
          if($participantsArray){
    
            $participantsArray = $UserObj->sortParticipantsArrayByName($participantsArray);
    
            $counter = 0;
            $leftLabel = true;
                    
            if ($counter < count($participantsArray)) {
                $pdf->AddPage();
            }
            $y = Y;
    
            foreach ($participantsArray as $participant){ // construct label
    
              $user = $UserObj->getMoodleUser($participant->moodleuserid, $participant->imtlogin);
    
              if($user){
                $name = $user->lastname;
                $firstname = $user->firstname;
              } else {
                $name = $participant->lastname;
                $firstname = $participant->firstname;
              }
    
              $matrnr = $UserObj->getUserMatrNr($participant->moodleuserid, $participant->imtlogin);
    
              $room = $participant->roomname;
              $place = $participant->place;
    
              if($matrnr !== '-'){
                if ($leftLabel) { //print left label
                  $pdf->SetFont('helvetica', '', 12);
                  $pdf->MultiCell(90, 5, $exam_name, 0, 'C', 0, 0, X1, $y, true);
                  $pdf->SetFont('helvetica', 'B', 12);
                  $pdf->MultiCell(90, 5, $name . ', ' . $firstname . ' (' . $matrnr . ')', 0, 'C', 0, 0, X1, $y + 13, true);
                  $pdf->SetFont('helvetica', '', 10);
                  $pdf->MultiCell(21, 5, $date, 0, 'L', 0, 0, X1 + 1, $y + 25, true);
                  $pdf->MultiCell(21, 5, strtoupper($semester), 0, 'C', 0, 0, X1, $y + 36, true);
                  $pdf->MultiCell(32, 5, get_string('room', 'mod_exammanagement') . ': ' . $room, 0, 'L', 0, 0, X1 + 61, $y + 25, true);
                  $pdf->MultiCell(32, 5, get_string('place', 'mod_exammanagement') . ': ' . $place, 0, 'L', 0, 0, X1 + 61, $y + 30, true);
                  $pdf->SetFont('helvetica', 'B', 14);
                  $pdf->MultiCell(18, 5, ++$IDcounter, 0, 'C', 0, 0, X1 + 68, $y + 36, true);
    
                  $checksum = $ExammanagementInstanceObj->buildChecksumExamLabels('00000' . $matrnr);
                  $pdf->write1DBarcode('00000' . $matrnr . $checksum, 'EAN13', X1 + 22, $y + 27, 37, 19, 0.4, $style, 'C');
    
                } else { //print right label
                    $pdf->SetFont('helvetica', '', 12);
                    $pdf->MultiCell(90, 5, $exam_name, 0, 'C', 0, 0, X2, $y, true);
                    $pdf->SetFont('helvetica', 'B', 12);
                    $pdf->MultiCell(90, 5, $name . ', ' . $firstname . ' (' . $matrnr . ')', 0, 'C', 0, 0, X2, $y + 13, true);
                    $pdf->SetFont('helvetica', '', 10);
                    $pdf->MultiCell(21, 5, $date, 0, 'L', 0, 0, X2 + 1, $y + 25, true);
                    $pdf->MultiCell(21, 5, strtoupper($semester), 0, 'C', 0, 0, X2, $y + 36, true);
                    $pdf->MultiCell(32, 5, get_string('room', 'mod_exammanagement') . ': ' .$room, 0, 'L', 0, 0, X2 + 61, $y + 25, true);
                    $pdf->MultiCell(32, 5, get_string('place', 'mod_exammanagement') . ': ' . $place, 0, 'L', 0, 0, X2 + 61, $y + 30, true);
                    $pdf->SetFont('helvetica', 'B', 14);
                    $pdf->MultiCell(18, 5, ++$IDcounter, 0, 'C', 0, 0, X2 + 68, $y + 36, true);
    
                    $checksum = $ExammanagementInstanceObj->buildChecksumExamLabels('00000' . $matrnr);
                    $pdf->write1DBarcode('00000' . $matrnr . $checksum, 'EAN13', X2 + 22, $y + 27, 37, 19, 0.4, $style, 'C');
                }
    
                $leftLabel = !$leftLabel;
                $counter++;
    
                if ($counter % 2 == 0) {
                  $y += LABEL_HEIGHT;
                }
    
                if ($counter % 10 == 0) {
                  $y = Y;
                  if ($counter < count($participantsArray)) {
                    $pdf->AddPage();
                  }
    
                }
              }
            }
          }
        }

      } else { // if no rooms are set or no places are assigned
        $participantsArray = $UserObj->getAllExamParticipants();
    
          if($participantsArray){
    
            $participantsArray = $UserObj->sortParticipantsArrayByName($participantsArray);
    
            $counter = 0;
            $leftLabel = true;
                    
            if ($counter < count($participantsArray)) {
                $pdf->AddPage();
            }
            $y = Y;
    
            foreach ($participantsArray as $participant){ // construct label
    
              $user = $UserObj->getMoodleUser($participant->moodleuserid, $participant->imtlogin);
    
              if($user){
                $name = $user->lastname;
                $firstname = $user->firstname;
              } else {
                $name = $participant->lastname;
                $firstname = $participant->firstname;
              }
    
              $matrnr = $UserObj->getUserMatrNr($participant->moodleuserid, $participant->imtlogin);
    
              $room = '';
              $place = '';
    
              if($matrnr !== '-'){
                if ($leftLabel) { //print left label
                  $pdf->SetFont('helvetica', '', 12);
                  $pdf->MultiCell(90, 5, $exam_name, 0, 'C', 0, 0, X1, $y, true);
                  $pdf->SetFont('helvetica', 'B', 12);
                  $pdf->MultiCell(90, 5, $name . ', ' . $firstname . ' (' . $matrnr . ')', 0, 'C', 0, 0, X1, $y + 13, true);
                  $pdf->SetFont('helvetica', '', 10);
                  $pdf->MultiCell(21, 5, $date, 0, 'L', 0, 0, X1 + 1, $y + 25, true);
                  $pdf->MultiCell(21, 5, strtoupper($semester), 0, 'C', 0, 0, X1, $y + 36, true);
                  $pdf->MultiCell(32, 5, get_string('room', 'mod_exammanagement') . ': ' . $room, 0, 'L', 0, 0, X1 + 61, $y + 25, true);
                  $pdf->MultiCell(32, 5, get_string('place', 'mod_exammanagement') . ': ' . $place, 0, 'L', 0, 0, X1 + 61, $y + 30, true);
                  $pdf->SetFont('helvetica', 'B', 14);
                  $pdf->MultiCell(18, 5, ++$IDcounter, 0, 'C', 0, 0, X1 + 68, $y + 36, true);
    
                  $checksum = $ExammanagementInstanceObj->buildChecksumExamLabels('00000' . $matrnr);
                  $pdf->write1DBarcode('00000' . $matrnr . $checksum, 'EAN13', X1 + 22, $y + 27, 37, 19, 0.4, $style, 'C');
    
                } else { //print right label
                    $pdf->SetFont('helvetica', '', 12);
                    $pdf->MultiCell(90, 5, $exam_name, 0, 'C', 0, 0, X2, $y, true);
                    $pdf->SetFont('helvetica', 'B', 12);
                    $pdf->MultiCell(90, 5, $name . ', ' . $firstname . ' (' . $matrnr . ')', 0, 'C', 0, 0, X2, $y + 13, true);
                    $pdf->SetFont('helvetica', '', 10);
                    $pdf->MultiCell(21, 5, $date, 0, 'L', 0, 0, X2 + 1, $y + 25, true);
                    $pdf->MultiCell(21, 5, strtoupper($semester), 0, 'C', 0, 0, X2, $y + 36, true);
                    $pdf->MultiCell(32, 5, get_string('room', 'mod_exammanagement') . ': ' .$room, 0, 'L', 0, 0, X2 + 61, $y + 25, true);
                    $pdf->MultiCell(32, 5, get_string('place', 'mod_exammanagement') . ': ' . $place, 0, 'L', 0, 0, X2 + 61, $y + 30, true);
                    $pdf->SetFont('helvetica', 'B', 14);
                    $pdf->MultiCell(18, 5, ++$IDcounter, 0, 'C', 0, 0, X2 + 68, $y + 36, true);
    
                    $checksum = $ExammanagementInstanceObj->buildChecksumExamLabels('00000' . $matrnr);
                    $pdf->write1DBarcode('00000' . $matrnr . $checksum, 'EAN13', X2 + 22, $y + 27, 37, 19, 0.4, $style, 'C');
                }
    
                $leftLabel = !$leftLabel;
                $counter++;
    
                if ($counter % 2 == 0) {
                  $y += LABEL_HEIGHT;
                }
    
                if ($counter % 10 == 0) {
                  $y = Y;
                  if ($counter < count($participantsArray)) {
                    $pdf->AddPage();
                  }
    
                }
              }
            }
          }
      }

      //generate filename without umlaute
      $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
      $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
      $filenameUmlaute = get_string("examlabels", "mod_exammanagement"). '_' . $ExammanagementInstanceObj->getCleanCourseCategoryName() . '_' . $ExammanagementInstanceObj->getCourse()->fullname. '_' . $ExammanagementInstanceObj->moduleinstance->name . '.pdf';
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