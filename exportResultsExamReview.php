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

use mod_exammanagement\pdfs\resultsExamReview;

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

        //include pdf
        require_once(__DIR__.'/classes/pdfs/resultsExamReview.php');

        define("WIDTH_COLUMN_NAME", 225);
        define("WIDTH_COLUMN_FORENAME", 225);
        define("WIDTH_COLUMN_MATNO", 70);
        define("WIDTH_COLUMN_POINTS", 80);

        if(!$ExammanagementInstanceObj->getInputResultsCount()){
          $MoodleObj->redirectToOverviewPage('afterexam', get_string('no_results_entered', 'mod_exammanagement'), 'error');
        } else if (!$ExammanagementInstanceObj->getDataDeletionDate()){
          $MoodleObj->redirectToOverviewPage('afterexam', get_string('correction_not_completed', 'mod_exammanagement'), 'error');
        }

        // Include the main TCPDF library (search for installation path).
        require_once(__DIR__.'/../../config.php');
        require_once($CFG->libdir.'/pdflib.php');

        // create new PDF document
        $pdf = new resultsExamReview(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('PANDA');
        $pdf->SetTitle(get_string('pointslist_examreview', 'mod_exammanagement') . ': ' . $ExammanagementInstanceObj->getCourse()->fullname . ', '. $ExammanagementInstanceObj->moduleinstance->name);
        $pdf->SetSubject(get_string('pointslist_examreview', 'mod_exammanagement'));
        $pdf->SetKeywords(get_string('pointslist_examreview', 'mod_exammanagement') . ', ' . $ExammanagementInstanceObj->getCourse()->fullname . ', ' . $ExammanagementInstanceObj->moduleinstance->name);

        // header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //set margins
        $pdf->SetMargins(20, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //set some language-dependent strings
        //$pdf->setLanguageArray($l);

        // ---------------------------------------------------------

        $pdf->AddPage();
        $pdf->Line(20, 15, 190, 15);
        $pdf->ImageEps('data/upb_logo.ai', 30, 25, 13);
        $pdf->SetFont('helvetica', '', 16);
        $pdf->MultiCell(130, 3, get_string('pointslist_examreview', 'mod_exammanagement'), 0, 'C', 0, 0, 50, 18);
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->MultiCell(130, 3, $ExammanagementInstanceObj->getCourse()->fullname . ', ' . $ExammanagementInstanceObj->moduleinstance->name, 0, 'C', 0, 0, 50, 25);
        $pdf->SetFont('helvetica', '', 16);

        $date = $ExammanagementInstanceObj->getHrExamtime();

        if($date){
          $pdf->MultiCell(130, 3, $date . ', ' . $ExammanagementInstanceObj->getCleanCourseCategoryName(), 0, 'C', 0, 0, 50, 42);

        } else {
          $pdf->MultiCell(130, 3, $ExammanagementInstanceObj->getCleanCourseCategoryName(), 0, 'C', 0, 0, 50, 42);
        }

        $pdf->SetFont('helvetica', '', 10);

        $pdf->SetTextColor(255, 0, 0);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(130, 3, "- " . get_string('internal_use', 'mod_exammanagement') . " -", 0, 'C', 0, 0, 50, 55);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);

        $pdf->Line(20, 62, 190, 62);
        $pdf->SetXY(20, 65);

        $maxPoints = str_replace( '.', ',', $ExammanagementInstanceObj->getTaskTotalPoints());
        $participantsArray = $UserObj->getAllExamParticipants();
        $fill = false;

        $tbl = "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">";
        $tbl .= "<thead>";
        $tbl .= "<tr bgcolor=\"#000000\" color=\"#FFFFFF\">";
        $tbl .= "<td width=\"" . WIDTH_COLUMN_NAME . "\"><b>" . get_string('lastname', 'mod_exammanagement') . "</b></td>";
        $tbl .= "<td width=\"" . WIDTH_COLUMN_FORENAME . "\"><b>" . get_string('firstname', 'mod_exammanagement') . "</b></td>";
        $tbl .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\"><b>" . get_string('matrno', 'mod_exammanagement') . "</b></td>";
        $tbl .= "<td width=\"" . WIDTH_COLUMN_POINTS . "\" align=\"center\"><b>" . get_string('points', 'mod_exammanagement') . "<br>(max. " . $maxPoints . ")" . "</b></td>";
        $tbl .= "</tr>";
        $tbl .= "</thead>";

        usort($participantsArray, function($a, $b){ //sort array by custom user function
          global $UserObj;
          if($a->moodleuserid){
            $aFirstname = $UserObj->getMoodleUser($a->moodleuserid)->firstname;
            $aLastname = $UserObj->getMoodleUser($a->moodleuserid)->lastname;  
          } else {
            $aFirstname = $a->firstname;
            $aLastname = $a->lastname;
          }

          if($b->moodleuserid){
            $bFirstname = $UserObj->getMoodleUser($b->moodleuserid)->firstname;
            $bLastname = $UserObj->getMoodleUser($b->moodleuserid)->lastname;
          } else {
            $bFirstname = $b->firstname;
            $bLastname = $b->lastname;
          }

          if ($aLastname == $bLastname) { //if names are even sort by first name
              return strcmp($aFirstname, $bFirstname);
          } else{
              return strcmp($aLastname, $bLastname); // else sort by last name
          }

        });

        foreach ($participantsArray as $participant){

          $totalPoints = 0;

          $state = $UserObj->getExamState($participant);

          if ($state == "nt") {
            $totalPoints = get_string('nt', 'mod_exammanagement');
          } else if ($state == "fa") {
            $totalPoints = get_string('fa', 'mod_exammanagement');
          } else if ($state == "ill") {
            $totalPoints = get_string('ill', 'mod_exammanagement');
          } else {
            $totalPoints = str_replace( '.', ',', $UserObj->calculateTotalPoints($participant));
          }

          $user = $UserObj->getMoodleUser($participant->moodleuserid);
      
          if($user){
              $name = $user->lastname;
              $firstname = $user->firstname;
          } else {
              $name = $participant->lastname;
              $firstname = $participant->firstname;
          }

          $matrnr = $UserObj->getUserMatrNr($participant->moodleuserid, $participant->imtlogin);

          $tbl .= ($fill) ? "<tr bgcolor=\"#DDDDDD\">" : "<tr>";
          $tbl .= "<td width=\"" . WIDTH_COLUMN_NAME . "\">" . $name . "</td>";
          $tbl .= "<td width=\"" . WIDTH_COLUMN_FORENAME . "\">" . $firstname . "</td>";
          $tbl .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\">" . $matrnr . "</td>";
          $tbl .= "<td width=\"" . WIDTH_COLUMN_POINTS . "\" align=\"center\">" . $totalPoints . "</td>";
          $tbl .= "</tr>";

          $fill = !$fill;

        }

        $tbl .= "</table>";

        // Print text using writeHTMLCell()

        $pdf->writeHTML($tbl, true, false, false, false, '');

        //generate filename without umlaute
        $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
        $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
        $filenameUmlaute = get_string("pointslist_examreview", "mod_exammanagement") . '_' . $ExammanagementInstanceObj->getCleanCourseCategoryName() . '_' . $ExammanagementInstanceObj->getCourse()->fullname . '_' . $ExammanagementInstanceObj->moduleinstance->name . '.pdf';
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