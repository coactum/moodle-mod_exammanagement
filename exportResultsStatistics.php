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
 * @copyright   coactum GmbH 2020
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Style\Alignment;
use \PhpOffice\PhpSpreadsheet\Style\Border;
use \PhpOffice\PhpSpreadsheet\Style\Color;
use \PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use \PhpOffice\PhpSpreadsheet\Cell\Cell;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once("$CFG->libdir/phpspreadsheet/vendor/autoload.php");

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$UserObj = User::getInstance($id, $e, $ExammanagementInstanceObj->getCm()->instance);
$MoodleObj = Moodle::getInstance($id, $e);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

    if($ExammanagementInstanceObj->isExamDataDeleted()){
        $MoodleObj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
	} else {

        if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page


            if(!$UserObj->getEnteredResultsCount()){
                $MoodleObj->redirectToOverviewPage('afterexam', get_string('no_results_entered', 'mod_exammanagement'), 'error');
            } else if (!$ExammanagementInstanceObj->getDataDeletionDate()){
                $MoodleObj->redirectToOverviewPage('afterexam', get_string('correction_not_completed', 'mod_exammanagement'), 'error');
            }
            require_once("$CFG->libdir/phpspreadsheet/vendor/autoload.php");

            // Create new Spreadsheet object
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

            // Set properties for document
            $spreadsheet->getProperties()->setCreator($ExammanagementInstanceObj->getMoodleSystemName())
                                        ->setLastModifiedBy($ExammanagementInstanceObj->getMoodleSystemName())
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
                    'center' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ),
            );

            $headerStyle = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'bottom' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('argb' => '00000000'),
                    ),
                ),
            );

            $borderStyleArray = array(
                'borders' => array(
                    'right' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('argb' => '00000000'),
                    ),
                ),
            );

            $worksheet = $spreadsheet->setActiveSheetIndex(0);

            // Col-Width
            $worksheet->getColumnDimension('A')->setWidth(15);
            $worksheet->getColumnDimension('B')->setWidth(13);
            $worksheet->getColumnDimension('C')->setWidth(14);
            $worksheet->getColumnDimension('D')->setWidth(22);

            // General Information
            $worksheet->getStyle('A1:A5')->applyFromArray($boldStyle);

            // Table 1
            $worksheet->getStyle('A9:D9')->applyFromArray($headerStyle);
            $worksheet->getStyle('A10:D20')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Table 2
            $worksheet->getStyle('A23:C23')->applyFromArray($headerStyle);
            $worksheet->getStyle('A23:A28')->applyFromArray($borderStyleArray);
            $worksheet->getStyle('A24:A28')->getFont()->setBold(true);
            $worksheet->getStyle('B24:C28')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Table 3
            $worksheet->getStyle('A31:C31')->applyFromArray($headerStyle);
            $worksheet->getStyle('A31:A34')->applyFromArray($borderStyleArray);
            $worksheet->getStyle('A32:A34')->getFont()->setBold(true);
            $worksheet->getStyle('B32:C34')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

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
            $worksheet->setTitle(get_string('overview', 'mod_exammanagement'));
            $worksheet->setCellValue('A1', get_string('examname', 'mod_exammanagement'));
            $worksheet->setCellValue('A2', get_string('examterm', 'mod_exammanagement'));
            $worksheet->setCellValue('A3', get_string('examdate', 'mod_exammanagement'));
            $worksheet->setCellValue('A4', get_string('examtime', 'mod_exammanagement'));
            $worksheet->setCellValue('A5', get_string('examrooms', 'mod_exammanagement'));

            $worksheet->setCellValue('B1', $ExammanagementInstanceObj->moduleinstance->name);
            $worksheet->setCellValue('B2', $semester);
            $worksheet->setCellValue('B3', $date);
            $worksheet->setCellValue('B4', $start_time);
            $worksheet->setCellValue('B5', $rooms);

            // set data for table 1

            $gradingscale = $ExammanagementInstanceObj->getGradingscale();
            $summaryTable = array();
            $totalpoints = $ExammanagementInstanceObj->getTaskTotalPoints();
            $laststeppoints = $totalpoints;

            if($gradingscale){
                foreach($gradingscale as $gradestep => $points){
                    $summaryTable[$gradestep] = array("countBonus" => 0, "countNoBonus" => 0, "from" => $points, "to" => $ExammanagementInstanceObj->formatNumberForDisplay($laststeppoints));

                    $laststeppoints = $points-0.01;
                }
                $summaryTable[5] = array("countBonus" => 0, "countNoBonus" => 0, "from" => 0, "to" => $ExammanagementInstanceObj->formatNumberForDisplay($laststeppoints));
            }

            $rowCounter = 10;

            // set data for table 2

            $participants = $UserObj->getExamParticipants(array('mode'=>'all'), array('matrnr'));

            $notPassed = 0;
            $notRated = 0;
            $countNT = 0;
            $countFA = 0;
            $countSICK = 0;

            $bonusstepsEntered = $UserObj->getEnteredBonusCount('steps');
            $bonuspointsEntered = $UserObj->getEnteredBonusCount('points');

            $bonusstepnotset = 0;
            $bonusstepzero = 0;
            $bonusstepone = 0;
            $bonussteptwo = 0;
            $bonusstepthree = 0;

            foreach($participants as $participant){

                $resultState = $UserObj->getExamState($participant);

                if ($resultState == "nt"){
                    $countNT++;
                } else if ($resultState == "fa"){
                    $countFA++;
                } else if ($resultState == "ill") {
                    $countSICK++;
                } else {
                    $result = $UserObj->calculateResultGrade($participant);
                    $resultWithBonus = $UserObj->calculateResultGradeWithBonus($result, $resultState, $participant->bonussteps);

                    if ($result == '-'){
                        $notRated++;
                    } else if($result && $gradingscale){

                        $summaryTable[strval($result)]["countNoBonus"]++;

                        if ($result == '5,0'){
                            $notPassed++;

                            if($resultWithBonus){
                                $summaryTable[strval($resultWithBonus)]["countBonus"]++;
                            }
                        } else {
                            if($resultWithBonus){
                                $summaryTable[str_pad (strval($resultWithBonus), 3, '.0')]["countBonus"]++;
                            }
                        }
                    }
                }

                switch ($participant->bonussteps) { // for table 2 sheet 2
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

                $worksheet->setCellValue('A9', get_string('grade', 'mod_exammanagement'));
                $worksheet->setCellValue('B9', get_string('points', 'mod_exammanagement'));
                $worksheet->setCellValue('C9', get_string('nobonus', 'mod_exammanagement'));
                $worksheet->setCellValue('D9', get_string('withbonus', 'mod_exammanagement'));

                foreach($summaryTable as $gradestep => $options){


                    $worksheet->setCellValue("A".$rowCounter, strval($gradestep));
                    $worksheet->setCellValue("B".$rowCounter, $options["from"] . " - " . $options["to"]);

                    if($bonusstepsEntered){
                        $worksheet->setCellValue("C".$rowCounter, $options["countNoBonus"]);
                        $worksheet->setCellValue("D".$rowCounter, $options["countBonus"]);
                    }

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

            $worksheet->setCellValue('B23', get_string('count', 'mod_exammanagement'));
            $worksheet->setCellValue('C23', get_string('inpercent', 'mod_exammanagement'));

            $worksheet->setCellValue('A24', get_string('registered', 'mod_exammanagement'));
            $worksheet->setCellValue('A25', get_string('participants', 'mod_exammanagement'));
            $worksheet->setCellValue('A26', get_string('nt', 'mod_exammanagement'));
            $worksheet->setCellValue('A27', get_string('fa', 'mod_exammanagement'));
            $worksheet->setCellValue('A28', get_string('ill', 'mod_exammanagement'));

            $worksheet->setCellValue('B24', $registered);
            $worksheet->setCellValue('B25', $numberParticipants);
            $worksheet->setCellValue('B26', $countNT);
            $worksheet->setCellValue('B27', $countFA);
            $worksheet->setCellValue('B28', $countSICK);

            $worksheet->setCellValue('C24', 100);
            $worksheet->setCellValue('C25', $numberParticipantsPercent);
            $worksheet->setCellValue('C26', $NTpercent);
            $worksheet->setCellValue('C27', $FApercent);
            $worksheet->setCellValue('C28', $SICKpercent);

            // output table 3
            $passed = $numberParticipants - $notPassed - $notRated;

            if($numberParticipants > 0){
                $passedPercent = number_format($passed / $numberParticipants * 100, 2);
                $notPassedPercent = number_format($notPassed / $numberParticipants * 100 ,2);
                $notRatedPercent = number_format($notRated / $numberParticipants * 100 , 2);
            } else {
                $passedPercent = 0;
                $notPassedPercent = 0;
                $notRatedPercent = 0;
            }

            $worksheet->setCellValue('B31', get_string('count', 'mod_exammanagement'));
            $worksheet->setCellValue('C31', get_string('inpercent', 'mod_exammanagement'));

            $worksheet->setCellValue('A32', get_string('participants', 'mod_exammanagement'));
            $worksheet->setCellValue('A33', get_string('passed', 'mod_exammanagement'));
            $worksheet->setCellValue('A34', get_string('notpassed', 'mod_exammanagement'));

            $worksheet->setCellValue('B32', $numberParticipants);
            $worksheet->setCellValue('B33', $passed);
            $worksheet->setCellValue('B34', $notPassed);

            $worksheet->setCellValue('C32', 100);
            $worksheet->setCellValue('C33', $passedPercent);
            $worksheet->setCellValue('C34', $notPassedPercent);

            if ( $notRated > 0 ){
                $worksheet->setCellValue('A35', get_string('notrated', 'mod_exammanagement'));
                $worksheet->setCellValue('B35', $notRated);
                $worksheet->setCellValue('C35', $notRatedPercent);

                $worksheet->getStyle('A35')->applyFromArray($borderStyleArray);
                $worksheet->getStyle('A35:C35')->getFont()->setBold(true);
                $worksheet->getStyle('A35:C35')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                $worksheet->getStyle('B35:C35')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }

            ///////////////////////////////////////////
            ////////// SHEET 2 - assignments //////////
            ///////////////////////////////////////////

            $spreadsheet->createSheet();
            $worksheet = $spreadsheet->setActiveSheetIndex(1);

            $worksheet->setTitle(get_string('tasks_and_boni', 'mod_exammanagement'));

            $tasks = $ExammanagementInstanceObj->getTasks();
            $taskcount = count($tasks);

            // fortmatting for sheet 2

            // table 1
            $worksheet->getStyle('A1:C1')->applyFromArray($headerStyle);
            $range = "A2:C" . ($taskcount + 1);
            $worksheet->getStyle($range)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $worksheet->getColumnDimension('A')->setWidth(13);
            $worksheet->getColumnDimension('B')->setWidth(20);
            $worksheet->getColumnDimension('C')->setWidth(20);

            $worksheet->getStyle("A1:A".($taskcount + 1))->applyFromArray($borderStyleArray);

            // table 2
            if($bonusstepsEntered){
                $worksheet->getStyle('G1:H1')->applyFromArray($headerStyle);
                $worksheet->getStyle('G1:H6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $worksheet->getColumnDimension('G')->setWidth(13);

                $worksheet->getStyle("G1:G6")->applyFromArray($borderStyleArray);
            }

            // outpout table 1
            $worksheet->setCellValue('A1', get_string('task', 'mod_exammanagement'));
            $worksheet->setCellValue('B1', get_string('max_points', 'mod_exammanagement'));
            $worksheet->setCellValue('C1', get_string('mean', 'mod_exammanagement'));

            foreach ($tasks as $tasknumber => $points){
                $worksheet->setCellValueByColumnAndRow(1 , $tasknumber + 1, $tasknumber);
                $worksheet->setCellValueByColumnAndRow(2 , $tasknumber + 1, $points);
            }

            // outpout table 2 - bonussteps
            if($bonusstepsEntered){
                if(current_language() === 'de'){
                    $separator = ',';
                } else {
                    $separator = '.';
                }

                $worksheet->setCellValue('G1', get_string('bonussteps', 'mod_exammanagement'));
                $worksheet->setCellValue('H1', get_string('count', 'mod_exammanagement'));

                $worksheet->setCellValueByColumnAndRow(7 , 2, '-');
                $worksheet->setCellValueByColumnAndRow(8 , 2, $bonusstepnotset);
                $worksheet->setCellValueByColumnAndRow(7 , 3, 0);
                $worksheet->setCellValueByColumnAndRow(8 , 3, $bonusstepzero);
                $worksheet->setCellValueByColumnAndRow(7 , 4, 1 .' (= 0'.$separator.'3)');
                $worksheet->setCellValueByColumnAndRow(8 , 4, $bonusstepone);
                $worksheet->setCellValueByColumnAndRow(7 , 5, 2 .' (= 0'.$separator.'7)');
                $worksheet->setCellValueByColumnAndRow(8 , 5, $bonussteptwo);
                $worksheet->setCellValueByColumnAndRow(7 , 6, 3 .' (= 1'.$separator.'0)');
                $worksheet->setCellValueByColumnAndRow(8 , 6, $bonusstepthree);
            }

            // ////////////////////////////////////////
            // ////////// SHEET 3 - details ///////////
            // ////////////////////////////////////////

            $spreadsheet->createSheet();
            $worksheet = $spreadsheet->setActiveSheetIndex(2);

            $worksheet->setTitle(get_string('details', 'mod_exammanagement'));

            // FORMATTING for sheet 3

            if($bonuspointsEntered){
                $bc = 2;
            } else {
                $bc = 0;
            }

            // cell width
            $worksheet->getColumnDimension('A')->setWidth(10);
            $worksheet->getColumnDimension('B')->setWidth(20);
            $worksheet->getColumnDimension('C')->setWidth(15);
            $worksheet->getColumnDimension('D')->setWidth(10);
            $worksheet->getColumnDimension('E')->setWidth(15);

            for ($n = 1 ; $n <= $taskcount; $n++){
                $worksheet->getColumnDimension($ExammanagementInstanceObj->calculateCellAddress(5 + $n))->setWidth(8);
            }

            $worksheet->getColumnDimension($ExammanagementInstanceObj->calculateCellAddress(5 + $n))->setWidth(15);

            if($bonuspointsEntered){
                $worksheet->getColumnDimension($ExammanagementInstanceObj->calculateCellAddress(6 + $n))->setWidth(15);
                $worksheet->getColumnDimension($ExammanagementInstanceObj->calculateCellAddress(7 + $n))->setWidth(18);

            }

            $worksheet->getColumnDimension($ExammanagementInstanceObj->calculateCellAddress(6 + $n + $bc))->setWidth(15);

            if($bonusstepsEntered){
                $worksheet->getColumnDimension($ExammanagementInstanceObj->calculateCellAddress(7 + $n + $bc))->setWidth(12);
                $worksheet->getColumnDimension($ExammanagementInstanceObj->calculateCellAddress(8 + $n + $bc))->setWidth(30);
            }

            // header and centered
            $range = "A1:" . $ExammanagementInstanceObj->calculateCellAddress(9 + $n + $bc) . "1";
            $worksheet->getStyle($range)->applyFromArray($headerStyle);
            $range = "A2:" . $ExammanagementInstanceObj->calculateCellAddress(9 + $n + $bc) . ( count($participants) + 1 );
            $worksheet->getStyle($range)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // border lines
            $worksheet->getStyle('C1:C' . (count($participants) + 1))->applyFromArray($borderStyleArray);
            $worksheet->getStyle('E1:E' . (count($participants) + 1))->applyFromArray($borderStyleArray);
            $worksheet->getStyle($ExammanagementInstanceObj->calculateCellAddress(5 + $n) . '1:' . $ExammanagementInstanceObj->calculateCellAddress(5 + $n) . (count($participants) + 1))->applyFromArray($borderStyleArray);
            $worksheet->getStyle($ExammanagementInstanceObj->calculateCellAddress(5 + $n) . '1:' . $ExammanagementInstanceObj->calculateCellAddress(5 + $n) . (count($participants) + 1))->applyFromArray($borderStyleArray);

            if($bonuspointsEntered){
                $worksheet->getStyle($ExammanagementInstanceObj->calculateCellAddress(5 + $n + $bc) . '1:' . $ExammanagementInstanceObj->calculateCellAddress(5 + $n + $bc) . (count($participants) + 1))->applyFromArray($borderStyleArray);
                $worksheet->getStyle($ExammanagementInstanceObj->calculateCellAddress(5 + $n + $bc) . '1:' . $ExammanagementInstanceObj->calculateCellAddress(6 + $n + $bc) . (count($participants) + 1))->applyFromArray($borderStyleArray);
            }

            $worksheet->getStyle($ExammanagementInstanceObj->calculateCellAddress(6 + $n + $bc) . '1:' . $ExammanagementInstanceObj->calculateCellAddress(5 + $n + $bc) . (count($participants) + 1))->applyFromArray($borderStyleArray);

            // output table 1
            $worksheet->setCellValue('A1', get_string('matrno', 'mod_exammanagement'));
            $worksheet->setCellValue('B1', get_string('lastname', 'mod_exammanagement'));
            $worksheet->setCellValue('C1', get_string('firstname', 'mod_exammanagement'));
            $worksheet->setCellValue('D1', get_string('room', 'mod_exammanagement'));
            $worksheet->setCellValue('E1', get_string('place', 'mod_exammanagement'));
            $worksheet->setCellValueByColumnAndRow(5 + $n, 1, get_string('points', 'mod_exammanagement'));

            for ($n = 1 ; $n <= $taskcount; $n++){
                $worksheet->setCellValueByColumnAndRow(5 + $n, 1, 'A' . $n);
            }

            if($bonuspointsEntered){
                $worksheet->setCellValueByColumnAndRow(6 + $n, 1, get_string('bonuspoints', 'mod_exammanagement'));
                $worksheet->setCellValueByColumnAndRow(7 + $n, 1, get_string('points_with_bonus', 'mod_exammanagement'));
            }

            $worksheet->setCellValueByColumnAndRow(6 + $n + $bc, 1, get_string('result', 'mod_exammanagement'));

            if($bonusstepsEntered){
                $worksheet->setCellValueByColumnAndRow(7 + $n + $bc, 1, get_string('bonussteps', 'mod_exammanagement'));
                $worksheet->setCellValueByColumnAndRow(8 + $n + $bc, 1, get_string('resultwithbonus', 'mod_exammanagement'));
            }

            $rowCounter=2;

            foreach($participants as $participant){

                $state = $UserObj->getExamState($participant);

                $worksheet->setCellValue("A".$rowCounter, $participant->matrnr);
                $worksheet->setCellValue("B".$rowCounter, $participant->lastname);
                $worksheet->setCellValue("C".$rowCounter, $participant->firstname);
                $worksheet->setCellValue("D".$rowCounter, $participant->roomname);
                $worksheet->setCellValue("E".$rowCounter, $participant->place);

                $totalpoints = $UserObj->calculatePoints($participant);
                $totalpointsWithBonus = $UserObj->calculatePoints($participant, true);

                $result = $UserObj->calculateResultGrade($participant);

                if(isset($participant->bonussteps)){
                    $bonussteps = $participant->bonussteps;
                } else {
                    $bonussteps = '-';
                }

                if(isset($participant->bonuspoints)){
                    $bonuspoints = $participant->bonuspoints;
                } else {
                    $bonuspoints = '-';
                }

                $resultWithBonus = $UserObj->calculateResultGradeWithBonus($result, $state, $bonussteps);

                if($participant->exampoints){
                    foreach (json_decode($participant->exampoints) as $key => $points){
                        $worksheet->setCellValueByColumnAndRow(5 + $key, $rowCounter, $points);
                    }
                } else {
                    for ($n = 1 ; $n <= $taskcount; $n++){
                        $worksheet->setCellValueByColumnAndRow(5 + $n, $rowCounter, '-');
                    }
                }

                $worksheet->setCellValueByColumnAndRow(5 + $n, $rowCounter, $ExammanagementInstanceObj->formatNumberForDisplay($totalpoints));

                if($bonuspointsEntered){
                    $worksheet->setCellValueByColumnAndRow(6 + $n, $rowCounter, $bonuspoints);
                    $worksheet->setCellValueByColumnAndRow(7 + $n, $rowCounter, $ExammanagementInstanceObj->formatNumberForDisplay($totalpointsWithBonus));
                }

                if($gradingscale){
                    $worksheet->setCellValueByColumnAndRow(6 + $n + $bc, $rowCounter, $result);
                } else {
                    $worksheet->setCellValueByColumnAndRow(6 + $n  + $bc, $rowCounter, '-');
                }

                if($bonusstepsEntered){
                    $worksheet->setCellValueByColumnAndRow(7 + $n + $bc, $rowCounter, $bonussteps);
                    $worksheet->setCellValueByColumnAndRow(8 + $n + $bc, $rowCounter, $resultWithBonus);
                }

                $rowCounter++;
            }

            //table 2 sheet 1 formular mean
            $worksheet = $spreadsheet->setActiveSheetIndex(1);

            $participantscount = count($participants);

            $worksheet->getStyle("C2:C".$n)->getNumberFormat()->setFormatCode('0.00');

            for ($n = 1 ; $n <= $taskcount; $n++){

                $start = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(5+$n).'2';
                $end = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(5+$n). ($participantscount+1);

                $mean = 0;

                foreach($spreadsheet->setActiveSheetIndex(2)->rangeToArray($start.':'.$end) as $val){
                    if(is_numeric($val[0])){
                        $mean += $val[0];
                    }
                }

                $mean = $mean/$participantscount;

                $worksheet->setCellValueByColumnAndRow(
                    3,
                    1+$n,
                    $mean
                );
            }

            $spreadsheet->setActiveSheetIndex(0);

            //generate filename without umlaute
            $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
            $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
            $filenameUmlaute = get_string("examresults_statistics", "mod_exammanagement") . '_' . $ExammanagementInstanceObj->getCleanCourseCategoryName() . '_' . $ExammanagementInstanceObj->getCourse()->fullname . '_' . $ExammanagementInstanceObj->moduleinstance->name . '.xlsx';
            $filename = preg_replace($umlaute, $replace, $filenameUmlaute);

            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            // write excel file
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
            $writer->save('php://output');

        } else { // if user hasnt entered correct password for this session: show enterPasswordPage
            redirect ($ExammanagementInstanceObj->getExammanagementUrl('checkPassword', $ExammanagementInstanceObj->getCm()->id), null, null, null);
        }
    }
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}