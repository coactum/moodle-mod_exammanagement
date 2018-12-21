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
 * Outputs exam results statistics as excel file for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once("$CFG->libdir/phpexcel/PHPExcel.php");

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$UserObj = User::getInstance($id, $e, $ExammanagementInstanceObj->moduleinstance->categoryid);
$MoodleObj = Moodle::getInstance($id, $e);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

    global $CFG;

    $MoodleObj->setPage('exportResultsStatistics');

    if(!$ExammanagementInstanceObj->getInputResultsCount()){
      $MoodleObj->redirectToOverviewPage('afterexam', 'Noch keine Prüfungsergebnisse eingegeben.', 'error');
    } else if (!$ExammanagementInstanceObj->getDataDeletionDate()){
      $MoodleObj->redirectToOverviewPage('afterexam', 'Korrektur noch nicht abgeschloßen.', 'error');
    }

    // Create new PHPExcel object
    $PHPExcelObj = new PHPExcel();

    // Set properties for document
    $PHPExcelObj->getProperties()->setCreator('PANDA')
                                ->setLastModifiedBy('PANDA')
                                ->setTitle(get_string('examresults_statistics', 'mod_exammanagement') . ': ' . $ExammanagementInstanceObj->getCourse()->fullname . ', '. $ExammanagementInstanceObj->moduleinstance->name)
                                ->setSubject(get_string('examresults_statistics', 'mod_exammanagement'))
                                ->setDescription(get_string('examresults_statistics_description', 'mod_exammanagement'))
                                ->setKeywords(get_string('examresults_statistics', 'mod_exammanagement') . ', ' . $ExammanagementInstanceObj->getCourse()->fullname . ', ' . $ExammanagementInstanceObj->moduleinstance->name)
                                ->setCategory(get_string('examresults_statistics_category', 'mod_exammanagement'));

    // FORMATTING information for document
    $boldStyle = array(
        'font' => array(
            'bold' => true,
        ),
        'alignment' => array(
            'center' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ),
    );

    $headerStyle = array(
        'font' => array(
            'bold' => true,
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => '00000000'),
            ),
        ),
    );

    // Col-Width
    $PHPExcelObj->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(15);
    $PHPExcelObj->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(13);
    $PHPExcelObj->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(13);
    $PHPExcelObj->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(13);

    // General Information
    $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A1:A5')->applyFromArray($boldStyle);

    // Table 1
    $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A9:D9')->applyFromArray($headerStyle);
    $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A10:D20')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Table 2
    $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A23:C23')->applyFromArray($headerStyle);
    $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A23:A28')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A24:A28')->getFont()->setBold(true);
    $PHPExcelObj->setActiveSheetIndex(0)->getStyle('B24:C28')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Table 3
    $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A31:C31')->applyFromArray($headerStyle);
    $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A31:A34')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A32:A34')->getFont()->setBold(true);
    $PHPExcelObj->setActiveSheetIndex(0)->getStyle('B32:C34')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    ////////////////////////////////////////
    ////////// SHEET 1 - Overview //////////
    ////////////////////////////////////////

    // set general exam information
    $semester = strtoupper($ExammanagementInstanceObj->moduleinstance->categoryid);
    $examdate = date('d.m.Y', $ExammanagementInstanceObj->getExamtime());
    $start_time = date('H:i', $ExammanagementInstanceObj->getExamtime());
    $end_time = '';
    $rooms = $ExammanagementInstanceObj->getChoosenRoomNames();

    // output general Information
    $PHPExcelObj->setActiveSheetIndex(0)->setTitle(get_string('overview', 'mod_exammanagement'));
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('A1', get_string('examname', 'mod_exammanagement'));
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('A2', get_string('examterm', 'mod_exammanagement'));
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('A3', get_string('examdate', 'mod_exammanagement'));
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('A4', get_string('examtime', 'mod_exammanagement'));
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('A5', get_string('examrooms', 'mod_exammanagement'));

    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B1', $ExammanagementInstanceObj->moduleinstance->name);
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B2', $semester);
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B3', $examdate);
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B4', $start_time);
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B5', $rooms);

    // set data for table 1
    $gradingscale = $ExammanagementInstanceObj->getGradingscale();
    $summaryTable = array();
    $totalpoints = $ExammanagementInstanceObj->getTaskTotalPoints();
    $laststeppoints = $totalpoints;

    foreach($gradingscale as $gradestep => $points){
        $summaryTable[$gradestep] = array("countBonus" => 0, "countNoBonus" => 0, "from" => number_format($points, 2, ',', ''), "to" => number_format($laststeppoints, 2, ',', ''));

        $laststeppoints = $points-0.01;
    }

    $summaryTable[5] = array("countBonus" => 0, "countNoBonus" => 0, "from" => 0, "to" => number_format($laststeppoints, 2, ',', ''));

    $rowCounter = 10;

    // set data for table 2

    $ParticipantsArray = $UserObj->getAllExamParticipants();

    $notPassed = 0;
    $notRated = 0;
    $countNT = 0;
    $countFA = 0;
    $countSICK = 0;

    foreach($ParticipantsArray as $participant){

        $resultState = $UserObj->getExamState($participant);

        if ($resultState == "nt"){
            $countNT++;
        } else if ($resultState == "fa"){
            $countFA++;
        } else if ($resultState == "ill") {
            $countSICK++;
        } else {
            $result = $UserObj->calculateResultGrade($participant);
            $resultWithBonus = $UserObj->calculateResultGradeWithBonus($result, $participant->bonus);

            if ($result == '-'){
                $notRated++;
            } else {
                $summaryTable[strval($result)]["countNoBonus"]++;
                
                if ($result == '5,0'){
                    $notPassed++;
                }

                $summaryTable[str_pad (strval($resultWithBonus), 3, '.0')]["countBonus"]++;
            }
        }
    }
    
    // output table 1
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('A9', get_string('grade', 'mod_exammanagement'));
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B9', get_string('points', 'mod_exammanagement'));
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('C9', get_string('nobonus', 'mod_exammanagement'));
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('D9', get_string('withbonus', 'mod_exammanagement'));

    foreach($summaryTable as $gradestep => $options){
        $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('A' . $rowCounter, $gradestep);
        $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B' . $rowCounter, $options["from"] . " - " . $options["to"]);
        $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('C' . $rowCounter, $options["countNoBonus"]);
        $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('D' . $rowCounter, $options["countBonus"]);
        $rowCounter++;
    }

    // output table 2
    $registered = $UserObj->getParticipantsCount();
    $numberParticipants = $registered - $countNT - $countFA - $countSICK;
    $numberParticipantsPercent = number_format($numberParticipants / $registered * 100, 2);
    $NTpercent = number_format($countNT / $registered * 100, 2);
    $FApercent = number_format($countFA / $registered * 100, 2);
    $SICKpercent = number_format($countSICK / $registered * 100, 2);

    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B23', get_string('number', 'mod_exammanagement'));
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('C23', get_string('inpercent', 'mod_exammanagement'));

    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('A24', get_string('registered', 'mod_exammanagement'));
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('A25', get_string('participants', 'mod_exammanagement'));
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('A26', get_string('nt', 'mod_exammanagement'));
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('A27', get_string('fa', 'mod_exammanagement'));
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('A28', get_string('ill', 'mod_exammanagement'));

    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B24', $registered);
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B25', $numberParticipants);
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B26', $countNT);
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B27', $countFA);
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B28', $countSICK);

    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('C24', 100);
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('C25', $numberParticipantsPercent);
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('C26', $NTpercent);
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('C27', $FApercent);
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('C28', $SICKpercent);

    // output table 3
    $passed = $numberParticipants - $notPassed - $notRated;
    $passedPercent = number_format($passed / $numberParticipants * 100, 2);
    $notPassedPercent = number_format($notPassed / $numberParticipants * 100 ,2);
    $notRatedPercent = number_format($notRated / $numberParticipants * 100 , 2);

    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B31', get_string('number', 'mod_exammanagement'));
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('C31', get_string('inpercent', 'mod_exammanagement'));

    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('A32', get_string('participants', 'mod_exammanagement'));
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('A33', get_string('passed', 'mod_exammanagement'));
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('A34', get_string('notpassed', 'mod_exammanagement'));

    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B32', $numberParticipants);
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B33', $passed);
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B34', $notPassed);

    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('C32', 100);
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('C33', $passedPercent);
    $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('C34', $notPassedPercent);

    if ( $notRated > 0 ){
        $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('A35', get_string('notrated', 'mod_exammanagement'));
        $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B35', $notRated);
        $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('C35', $notRatedPercent);

        $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A35')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A35:C35')->getFont()->setBold(true);
        $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A35:C35')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
        $PHPExcelObj->setActiveSheetIndex(0)->getStyle('B35:C35')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    }

    //generate filename without umlaute
    $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
    $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
    $filenameUmlaute = get_string("examresults_statistics", "mod_exammanagement") . '_' . strtoupper($ExammanagementInstanceObj->moduleinstance->categoryid) . '_' . $ExammanagementInstanceObj->getCourse()->fullname . '_' . $ExammanagementInstanceObj->moduleinstance->name . '.xlsx';
    $filename = preg_replace($umlaute, $replace, $filenameUmlaute);

    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');
    
    // write excel file
    $PHPExcelWriterObj = PHPExcel_IOFactory::createWriter($PHPExcelObj, "Excel2007");
    $PHPExcelWriterObj->save('php://output');

} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
