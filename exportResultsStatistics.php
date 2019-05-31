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
use PHPExcel_Style_NumberFormat;
use PHPExcel_Cell;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once("$CFG->libdir/phpexcel/PHPExcel.php");

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$UserObj = User::getInstance($id, $e);
$MoodleObj = Moodle::getInstance($id, $e);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

	if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

        global $CFG;

        //$MoodleObj->setPage('exportResultsStatistics');

        if(!$ExammanagementInstanceObj->getInputResultsCount()){
            $MoodleObj->redirectToOverviewPage('afterexam', get_string('no_results_entered', 'mod_exammanagement'), 'error');
        } else if (!$ExammanagementInstanceObj->getDataDeletionDate()){
            $MoodleObj->redirectToOverviewPage('afterexam', get_string('correction_not_completed', 'mod_exammanagement'), 'error');
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

        ////////////////////////////////////////
        ////////// SHEET 1 - Overview //////////
        ////////////////////////////////////////

        // FORMATTING for sheet 1
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

        $borderStyleArray = array(
            'borders' => array(
                'right' => array(
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
        $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A23:A28')->applyFromArray($borderStyleArray);
        $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A24:A28')->getFont()->setBold(true);
        $PHPExcelObj->setActiveSheetIndex(0)->getStyle('B24:C28')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // Table 3
        $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A31:C31')->applyFromArray($headerStyle);
        $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A31:A34')->applyFromArray($borderStyleArray);
        $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A32:A34')->getFont()->setBold(true);
        $PHPExcelObj->setActiveSheetIndex(0)->getStyle('B32:C34')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // set general exam information
        $semester = $ExammanagementInstanceObj->getCleanCourseCategoryName();
        $examdate = $ExammanagementInstanceObj->getExamtime();

        if($examdate){
            $date = date('d.m.Y', $examdate);
            $start_time = date('H:i', $examdate);    
        } else {
            $date = '-';
            $start_time = '-';
        }
        
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
        $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B3', $date);
        $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B4', $start_time);
        $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B5', $rooms);

        // set data for table 1
        
        $gradingscale = $ExammanagementInstanceObj->getGradingscale();
        $summaryTable = array();
        $totalpoints = $ExammanagementInstanceObj->getTaskTotalPoints();
        $laststeppoints = $totalpoints;

        if($gradingscale){
            foreach($gradingscale as $gradestep => $points){
                $summaryTable[$gradestep] = array("countBonus" => 0, "countNoBonus" => 0, "from" => number_format($points, 2, ',', ''), "to" => number_format($laststeppoints, 2, ',', ''));
    
                $laststeppoints = $points-0.01;
            }    
            $summaryTable[5] = array("countBonus" => 0, "countNoBonus" => 0, "from" => 0, "to" => number_format($laststeppoints, 2, ',', ''));
        }

        $rowCounter = 10;

        // set data for table 2

        $ParticipantsArray = $UserObj->getAllExamParticipants();

        usort($ParticipantsArray, function($a, $b){ //sort array by custom user function
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

        $notPassed = 0;
        $notRated = 0;
        $countNT = 0;
        $countFA = 0;
        $countSICK = 0;

        $bonusstepnotset = 0;
        $bonusstepzero = 0;
        $bonusstepone = 0;
        $bonussteptwo = 0;
        $bonusstepthree = 0;

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
                $resultWithBonus = $UserObj->calculateResultGradeWithBonus($result, $resultState, $participant->bonus);

                if ($result == '-'){
                    $notRated++;
                } else if($gradingscale){
                    $summaryTable[strval($result)]["countNoBonus"]++;
                    
                    if ($result == '5,0'){
                        $notPassed++;
                        $summaryTable[strval($resultWithBonus)]["countBonus"]++;
                    } else {
                        $summaryTable[str_pad (strval($resultWithBonus), 3, '.0')]["countBonus"]++;
                    }
                }
            }

            switch ($participant->bonus) { // for table 2 sheet 2
                case '0':
                    $bonusstepzero++;
                    break;
                case '1':
                    $bonusstepone++;
                    break;
                case '2':
                    $bonussteptwo++;
                    break;
                case '3':
                    $bonusstepthree++;
                    break;
                default:
                    $bonusstepnotset++;
                    break;
            }
                    
        }

        // output table 1

        if($gradingscale){

            $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('A9', get_string('grade', 'mod_exammanagement'));
            $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B9', get_string('points', 'mod_exammanagement'));
            $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('C9', get_string('nobonus', 'mod_exammanagement'));
            $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('D9', get_string('withbonus', 'mod_exammanagement'));

            foreach($summaryTable as $gradestep => $options){

                $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('A' . $rowCounter, str_replace('.', ',', strval($gradestep)));
                $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B' . $rowCounter, $options["from"] . " - " . $options["to"]);
                $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('C' . $rowCounter, $options["countNoBonus"]);
                $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('D' . $rowCounter, $options["countBonus"]);
                $rowCounter++;
            }
        }

        // output table 2
        $registered = $UserObj->getParticipantsCount();
        $numberParticipants = $registered - $countNT - $countFA - $countSICK;
        $numberParticipantsPercent = number_format($numberParticipants / $registered * 100, 2);
        $NTpercent = number_format($countNT / $registered * 100, 2);
        $FApercent = number_format($countFA / $registered * 100, 2);
        $SICKpercent = number_format($countSICK / $registered * 100, 2);

        $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B23', get_string('count', 'mod_exammanagement'));
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

        $PHPExcelObj->setActiveSheetIndex(0)->setCellValue('B31', get_string('count', 'mod_exammanagement'));
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

            $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A35')->applyFromArray($borderStyleArray);
            $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A35:C35')->getFont()->setBold(true);
            $PHPExcelObj->setActiveSheetIndex(0)->getStyle('A35:C35')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
            $PHPExcelObj->setActiveSheetIndex(0)->getStyle('B35:C35')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }

        ///////////////////////////////////////////
        ////////// SHEET 2 - assignments //////////
        ///////////////////////////////////////////

        $PHPExcelObj->createSheet();
        $PHPExcelObj->setActiveSheetIndex(1)->setTitle(get_string('tasks_and_boni', 'mod_exammanagement'));

        $tasks = $ExammanagementInstanceObj->getTasks();
        $taskcount = count($tasks);
        
        // fortmatting for sheet 2

        //table 1
        $PHPExcelObj->setActiveSheetIndex(1)->getStyle('A1:C1')->applyFromArray($headerStyle);
        $range = "A2:C" . ($taskcount + 1);
        $PHPExcelObj->setActiveSheetIndex(1)->getStyle($range)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $PHPExcelObj->setActiveSheetIndex(1)->getColumnDimension('A')->setWidth(13);
        $PHPExcelObj->setActiveSheetIndex(1)->getColumnDimension('B')->setWidth(20);
        $PHPExcelObj->setActiveSheetIndex(1)->getColumnDimension('C')->setWidth(20);
        
        $PHPExcelObj->setActiveSheetIndex(1)->getStyle("A1:A".($taskcount + 1))->applyFromArray($borderStyleArray);

        // Table 2
        $PHPExcelObj->setActiveSheetIndex(1)->getStyle('G1:H1')->applyFromArray($headerStyle);
        $PHPExcelObj->setActiveSheetIndex(1)->getStyle('G1:H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $PHPExcelObj->setActiveSheetIndex(1)->getColumnDimension('G')->setWidth(13);

        $PHPExcelObj->setActiveSheetIndex(1)->getStyle("G1:G6")->applyFromArray($borderStyleArray);
            
        // outpout table 1 
        $PHPExcelObj->setActiveSheetIndex(1)->setCellValue('A1', get_string('task', 'mod_exammanagement'));
        $PHPExcelObj->setActiveSheetIndex(1)->setCellValue('B1', get_string('max_points', 'mod_exammanagement'));
        $PHPExcelObj->setActiveSheetIndex(1)->setCellValue('C1', get_string('mean', 'mod_exammanagement'));

        foreach ($tasks as $tasknumber => $points){        
            $PHPExcelObj->setActiveSheetIndex(1)->setCellValueByColumnAndRow(0 , $tasknumber + 1, $tasknumber);
            $PHPExcelObj->setActiveSheetIndex(1)->setCellValueByColumnAndRow(1 , $tasknumber + 1, $points);
        }

        // outpout table 2
        $PHPExcelObj->setActiveSheetIndex(1)->setCellValue('G1', get_string('bonussteps', 'mod_exammanagement'));
        $PHPExcelObj->setActiveSheetIndex(1)->setCellValue('H1', get_string('count', 'mod_exammanagement'));

        $PHPExcelObj->setActiveSheetIndex(1)->setCellValueByColumnAndRow(6 , 2, '-');
        $PHPExcelObj->setActiveSheetIndex(1)->setCellValueByColumnAndRow(7 , 2, $bonusstepnotset);
        $PHPExcelObj->setActiveSheetIndex(1)->setCellValueByColumnAndRow(6 , 3, 0);
        $PHPExcelObj->setActiveSheetIndex(1)->setCellValueByColumnAndRow(7 , 3, $bonusstepzero);
        $PHPExcelObj->setActiveSheetIndex(1)->setCellValueByColumnAndRow(6 , 4, 1 .' (= 0,3)');
        $PHPExcelObj->setActiveSheetIndex(1)->setCellValueByColumnAndRow(7 , 4, $bonusstepone);
        $PHPExcelObj->setActiveSheetIndex(1)->setCellValueByColumnAndRow(6 , 5, 2 .' (= 0,7)');
        $PHPExcelObj->setActiveSheetIndex(1)->setCellValueByColumnAndRow(7 , 5, $bonussteptwo);
        $PHPExcelObj->setActiveSheetIndex(1)->setCellValueByColumnAndRow(6 , 6, 3 .' (= 1,0)');
        $PHPExcelObj->setActiveSheetIndex(1)->setCellValueByColumnAndRow(7 , 6, $bonusstepthree);

        ////////////////////////////////////////
        ////////// SHEET 3 - details ///////////
        ////////////////////////////////////////

        $PHPExcelObj->createSheet();
        $PHPExcelObj->setActiveSheetIndex(2)->setTitle(get_string('details', 'mod_exammanagement'));

        // FORMATTING for sheet 3

        // cell width
        $PHPExcelObj->setActiveSheetIndex(2)->getColumnDimension('A')->setWidth(10);
        $PHPExcelObj->setActiveSheetIndex(2)->getColumnDimension('B')->setWidth(20);
        $PHPExcelObj->setActiveSheetIndex(2)->getColumnDimension('C')->setWidth(16);
        $PHPExcelObj->setActiveSheetIndex(2)->getColumnDimension('D')->setWidth(9);
        $PHPExcelObj->setActiveSheetIndex(2)->getColumnDimension('E')->setWidth(14);

        for ($n = 1 ; $n <= $taskcount; $n++){
            $PHPExcelObj->setActiveSheetIndex(2)->getColumnDimension($ExammanagementInstanceObj->calculateCellAddress(5 + $n))->setWidth(8);
        }

        $PHPExcelObj->setActiveSheetIndex(2)->getColumnDimension($ExammanagementInstanceObj->calculateCellAddress(5 + $n))->setWidth(15);
        $PHPExcelObj->setActiveSheetIndex(2)->getColumnDimension($ExammanagementInstanceObj->calculateCellAddress(6 + $n))->setWidth(15);
        $PHPExcelObj->setActiveSheetIndex(2)->getColumnDimension($ExammanagementInstanceObj->calculateCellAddress(7 + $n))->setWidth(12);
        $PHPExcelObj->setActiveSheetIndex(2)->getColumnDimension($ExammanagementInstanceObj->calculateCellAddress(8 + $n))->setWidth(20);

        // header and centered
        $range = "A1:" . $ExammanagementInstanceObj->calculateCellAddress(9 + $n) . "1";
        $PHPExcelObj->setActiveSheetIndex(2)->getStyle($range)->applyFromArray($headerStyle);
        $range = "A2:" . $ExammanagementInstanceObj->calculateCellAddress(9 + $n) . ( count($ParticipantsArray) + 1 );
        $PHPExcelObj->setActiveSheetIndex(2)->getStyle($range)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // border lines
        $PHPExcelObj->setActiveSheetIndex(2)->getStyle('C1:C' . (count($ParticipantsArray) + 1))->applyFromArray($borderStyleArray);
        $PHPExcelObj->setActiveSheetIndex(2)->getStyle('E1:E' . (count($ParticipantsArray) + 1))->applyFromArray($borderStyleArray);
        $PHPExcelObj->setActiveSheetIndex(2)->getStyle($ExammanagementInstanceObj->calculateCellAddress(5 + $n) . '1:' . $ExammanagementInstanceObj->calculateCellAddress(5 + $n) . (count($ParticipantsArray) + 1))->applyFromArray($borderStyleArray);
        $PHPExcelObj->setActiveSheetIndex(2)->getStyle($ExammanagementInstanceObj->calculateCellAddress(5 + $n) . '1:' . $ExammanagementInstanceObj->calculateCellAddress(5 + $n) . (count($ParticipantsArray) + 1))->applyFromArray($borderStyleArray);

        // output table 1
        $PHPExcelObj->setActiveSheetIndex(2)->setCellValue('A1', get_string('matrno', 'mod_exammanagement'));
        $PHPExcelObj->setActiveSheetIndex(2)->setCellValue('B1', get_string('lastname', 'mod_exammanagement'));
        $PHPExcelObj->setActiveSheetIndex(2)->setCellValue('C1', get_string('firstname', 'mod_exammanagement'));
        $PHPExcelObj->setActiveSheetIndex(2)->setCellValue('D1', get_string('room', 'mod_exammanagement'));
        $PHPExcelObj->setActiveSheetIndex(2)->setCellValue('E1', get_string('place', 'mod_exammanagement'));

        for ($n = 1 ; $n <= $taskcount; $n++){
            $PHPExcelObj->setActiveSheetIndex(2)->setCellValueByColumnAndRow(4 + $n, 1, 'A' . $n);
        }

        $PHPExcelObj->setActiveSheetIndex(2)->setCellValueByColumnAndRow(4 + $n, 1, get_string('points', 'mod_exammanagement'));
        $PHPExcelObj->setActiveSheetIndex(2)->setCellValueByColumnAndRow(5 + $n, 1, get_string('result', 'mod_exammanagement'));
        $PHPExcelObj->setActiveSheetIndex(2)->setCellValueByColumnAndRow(6 + $n, 1, get_string('bonussteps', 'mod_exammanagement'));
        $PHPExcelObj->setActiveSheetIndex(2)->setCellValueByColumnAndRow(7 + $n, 1, get_string('resultwithbonus', 'mod_exammanagement'));

        $rowCounter=2;

        foreach($ParticipantsArray as $participant){

            if($participant->moodleuserid){
                $moodleUserObj = $UserObj->getMoodleUser($participant->moodleuserid);
                $lastname = $moodleUserObj->lastname;
                $firstname = $moodleUserObj->firstname;
            } else if($participant->imtlogin){
                $lastname = $participant->lastname;
                $firstname = $participant->firstname;
            }

            $matrnr = $UserObj->getUserMatrNr($participant->moodleuserid, $participant->imtlogin);

            $room = $participant->roomname;
            $place = $participant->place;

            $state = $UserObj->getExamState($participant);

            $PHPExcelObj->setActiveSheetIndex(2)->setCellValue("A$rowCounter", $matrnr);
            $PHPExcelObj->setActiveSheetIndex(2)->setCellValue("B$rowCounter", $lastname);
            $PHPExcelObj->setActiveSheetIndex(2)->setCellValue("C$rowCounter", $firstname);
            $PHPExcelObj->setActiveSheetIndex(2)->setCellValue("D$rowCounter", $room);
            $PHPExcelObj->setActiveSheetIndex(2)->setCellValue("E$rowCounter", $place);

            if($state == 'normal'){
                $temp = $UserObj->calculateTotalPoints($participant);

                if($temp == '-'){
                    $totalpoints = '-';
                } else {
                    $totalpoints = number_format(floatval($temp), 2, ',', '');
                }
            } else {
                $totalpoints = get_string($state, 'mod_exammanagement');
            }

            $result = $UserObj->calculateResultGrade($participant);
            
            if($participant->bonus){
                $bonus = $participant->bonus;
            } else {
                $bonus = 0;
            }
        
            $resultWithBonus = $UserObj->calculateResultGradeWithBonus($result, $state, $bonus);    

            if($participant->exampoints){
                foreach (json_decode($participant->exampoints) as $key => $points){
                    $PHPExcelObj->setActiveSheetIndex(2)->setCellValueByColumnAndRow(4 + $key, $rowCounter, $points);
                }
            } else {
                for ($n = 1 ; $n <= $taskcount; $n++){
                    $PHPExcelObj->setActiveSheetIndex(2)->setCellValueByColumnAndRow(4 + $n, $rowCounter, '-');
                }
            }
            
            
            $PHPExcelObj->setActiveSheetIndex(2)->setCellValueByColumnAndRow(4 + $n, $rowCounter, $totalpoints);

            if($gradingscale){
                $PHPExcelObj->setActiveSheetIndex(2)->setCellValueByColumnAndRow(5 + $n, $rowCounter, $result);
            } else {
                $PHPExcelObj->setActiveSheetIndex(2)->setCellValueByColumnAndRow(5 + $n, $rowCounter, '-');
            }

            $PHPExcelObj->setActiveSheetIndex(2)->setCellValueByColumnAndRow(6 + $n, $rowCounter, $bonus);
            $PHPExcelObj->setActiveSheetIndex(2)->setCellValueByColumnAndRow(7 + $n, $rowCounter, $resultWithBonus);
            $rowCounter++;
        }

        // table 2 sheet 1 formular mean

        $participantscount = count($ParticipantsArray)+1;

        $PHPExcelObj->setActiveSheetIndex(1)->getStyle("C2:C".$n)->getNumberFormat()->setFormatCode('0.00');

        for ($n = 1 ; $n <= $taskcount; $n++){

            $PHPExcelObj->setActiveSheetIndex(1)->setCellValueByColumnAndRow(
                '2',
                1+$n,
                '=MITTELWERT('.get_string("details", "mod_exammanagement").'!'.PHPExcel_Cell::stringFromColumnIndex(4+$n).'2:'.PHPExcel_Cell::stringFromColumnIndex(4+$n).$participantscount.')'
            );
        }

        $PHPExcelObj->setActiveSheetIndex(0);

        //generate filename without umlaute
        $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
        $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
        $filenameUmlaute = get_string("examresults_statistics", "mod_exammanagement") . '_' . $ExammanagementInstanceObj->getCleanCourseCategoryName() . '_' . $ExammanagementInstanceObj->getCourse()->fullname . '_' . $ExammanagementInstanceObj->moduleinstance->name . '.xlsx';
        $filename = preg_replace($umlaute, $replace, $filenameUmlaute);

        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        
        // write excel file
        $PHPExcelWriterObj = PHPExcel_IOFactory::createWriter($PHPExcelObj, "Excel2007");
        $PHPExcelWriterObj->save('php://output');

    } else { // if user hasnt entered correct password for this session: show enterPasswordPage
        redirect ($ExammanagementInstanceObj->getExammanagementUrl('checkPassword', $ExammanagementInstanceObj->getCm()->id), null, null, null);
    }
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
