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
 * @copyright   2022 coactum GmbH
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

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$exammanagementinstanceobj = exammanagementInstance::getInstance($id, $e);
$userobj = User::getInstance($id, $e, $exammanagementinstanceobj->getCm()->instance);
$moodleobj = Moodle::getInstance($id, $e);

if ($moodleobj->checkCapability('mod/exammanagement:viewinstance')) {

    if ($exammanagementinstanceobj->isExamDataDeleted()) {
        $moodleobj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
    } else {

        if (!isset($exammanagementinstanceobj->moduleinstance->password) || (isset($exammanagementinstanceobj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))) { // if no password for moduleinstance is set or if user already entered correct password in this session: show main page


            if (!$userobj->getParticipantsCount()) {
                $moodleobj->redirectToOverviewPage('beforeexam', get_string('no_participants_added', 'mod_exammanagement'), 'error');
            }

            require_once("$CFG->libdir/phpspreadsheet/vendor/autoload.php");

            // Create new Spreadsheet object.
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

            // Set properties for document.
            $spreadsheet->getProperties()->setCreator($exammanagementinstanceobj->getMoodleSystemName())
                ->setLastModifiedBy($exammanagementinstanceobj->getMoodleSystemName())
                ->setTitle(get_string('examresults_statistics', 'mod_exammanagement') . ': ' . $exammanagementinstanceobj->getCourse()->fullname . ', '. $exammanagementinstanceobj->moduleinstance->name)
                ->setSubject(get_string('examresults_statistics', 'mod_exammanagement'))
                ->setDescription(get_string('examresults_statistics_description', 'mod_exammanagement'))
                ->setKeywords(get_string('examresults_statistics', 'mod_exammanagement') . ', ' . $exammanagementinstanceobj->getCourse()->fullname . ', ' . $exammanagementinstanceobj->moduleinstance->name)
                ->setCategory(get_string('examresults_statistics_category', 'mod_exammanagement'));


            // SHEET 1 - Overview.

            // FORMATTING for sheet 1.
            $boldstyle = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'center' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ),
            );

            $headerstyle = array(
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

            $borderstylearray = array(
                'borders' => array(
                    'right' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('argb' => '00000000'),
                    ),
                ),
            );

            $worksheet = $spreadsheet->setActiveSheetIndex(0);

            // Col-Width.
            $worksheet->getColumnDimension('A')->setWidth(15);
            $worksheet->getColumnDimension('B')->setWidth(13);
            $worksheet->getColumnDimension('C')->setWidth(14);
            $worksheet->getColumnDimension('D')->setWidth(22);

            // General information.
            $worksheet->getStyle('A1:A5')->applyFromArray($boldstyle);

            // Table 1.
            $bonusstepsentered = $userobj->getEnteredBonusCount('steps');
            $resultscount = $userobj->getEnteredResultsCount();
            $gradingscale = $exammanagementinstanceobj->getGradingscale();

            if ($resultscount && $gradingscale) {
                if ($bonusstepsentered) {
                    $worksheet->getStyle('A9:D9')->applyFromArray($headerstyle);
                    $worksheet->getStyle('A10:D20')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                } else {
                    $worksheet->getStyle('A9:C9')->applyFromArray($headerstyle);
                    $worksheet->getStyle('A10:C20')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }
            }

            // Table 2
            $worksheet->getStyle('A23:C23')->applyFromArray($headerstyle);
            $worksheet->getStyle('A23:A28')->applyFromArray($borderstylearray);
            $worksheet->getStyle('A24:A28')->getFont()->setBold(true);
            $worksheet->getStyle('B24:C28')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Table 3
            $worksheet->getStyle('A31:C31')->applyFromArray($headerstyle);
            $worksheet->getStyle('A31:A34')->applyFromArray($borderstylearray);
            $worksheet->getStyle('A32:A34')->getFont()->setBold(true);
            $worksheet->getStyle('B32:C34')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // set general exam information
            $semester = $exammanagementinstanceobj->getCleanCourseCategoryName();
            $examdate = $exammanagementinstanceobj->getExamtime();

            if ($examdate) {
                $date = userdate($examdate, get_string('strftimedatefullshort', 'core_langconfig'));
                $starttime = userdate($examdate, get_string('strftimetime', 'core_langconfig'));
            } else {
                $date = '-';
                $starttime = '-';
            }

            $endtime = '';
            $rooms = $exammanagementinstanceobj->getChoosenRoomNames();

            // output general Information
            $worksheet->setTitle(get_string('overview', 'mod_exammanagement'));
            $worksheet->setCellValue('A1', get_string('examname', 'mod_exammanagement'));
            $worksheet->setCellValue('A2', get_string('examterm', 'mod_exammanagement'));
            $worksheet->setCellValue('A3', get_string('examdate', 'mod_exammanagement'));
            $worksheet->setCellValue('A4', get_string('examtime', 'mod_exammanagement'));
            $worksheet->setCellValue('A5', get_string('examrooms', 'mod_exammanagement'));

            $worksheet->setCellValue('B1', $exammanagementinstanceobj->moduleinstance->name);
            $worksheet->setCellValue('B2', $semester);
            $worksheet->setCellValue('B3', $date);
            $worksheet->setCellValue('B4', $starttime);
            if ($rooms) {
                $worksheet->setCellValue('B5', $rooms);
            } else {
                $worksheet->setCellValue('B5', '-');
            }

            // set data for table 1

            $summarytable = array();
            $totalpoints = $exammanagementinstanceobj->getTaskTotalPoints();
            $laststeppoints = $totalpoints;

            if ($gradingscale) {
                foreach ($gradingscale as $gradestep => $points) {
                    $summarytable[$gradestep] = array("countBonus" => 0, "countNoBonus" => 0, "from" => $points, "to" => $exammanagementinstanceobj->formatNumberForDisplay($laststeppoints));

                    $laststeppoints = number_format($points - 0.01, 2);
                }
                $summarytable[5] = array("countBonus" => 0, "countNoBonus" => 0, "from" => 0, "to" => $exammanagementinstanceobj->formatNumberForDisplay($laststeppoints));
            }

            $rowcounter = 10;

            // set data for table 2

            $participants = $userobj->getExamParticipants(array('mode' => 'all'), array('matrnr'));

            $notpassed = 0;
            $notrated = 0;
            $countnt = 0;
            $countfa = 0;
            $countsick = 0;

            $bonuspointsentered = $userobj->getEnteredBonusCount('points');

            $bonusstepnotset = 0;
            $bonusstepzero = 0;
            $bonusstepone = 0;
            $bonussteptwo = 0;
            $bonusstepthree = 0;

            foreach ($participants as $participant) {

                $resultstate = $userobj->getExamState($participant);

                if ($resultstate == "nt") {
                    $countnt++;
                } else if ($resultstate == "fa") {
                    $countfa++;
                } else if ($resultstate == "ill") {
                    $countsick++;
                } else {
                    $pointswithbonus = $userobj->calculatePoints($participant, true);
                    $result = $userobj->calculateResultGrade($pointswithbonus);
                    $resultwithbonus = $userobj->calculateResultGrade($pointswithbonus, $participant->bonussteps);

                    if ($result == '-') {
                        $notrated++;
                    } else if ($result && $gradingscale) {

                        $summarytable[strval($result)]["countNoBonus"]++;

                        if ($result == '5,0') {
                            $notpassed++;

                            if ($resultwithbonus) {
                                $summarytable[strval($resultwithbonus)]["countBonus"]++;
                            }
                        } else {
                            if ($resultwithbonus) {

                                if ($resultwithbonus != '5') {
                                    $keysummarytable = str_pad(strval($resultwithbonus), 3, '.0');
                                } else {
                                    $keysummarytable = $resultwithbonus;
                                }

                                $summarytable[strval($keysummarytable)]["countBonus"]++;

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
            if ($gradingscale && $resultscount) {

                $worksheet->setCellValue('A9', get_string('grade', 'mod_exammanagement'));
                $worksheet->setCellValue('B9', get_string('points', 'mod_exammanagement'));

                if ($bonusstepsentered) {
                    $worksheet->setCellValue('C9', get_string('nobonus', 'mod_exammanagement'));
                    $worksheet->setCellValue('D9', get_string('withbonus', 'mod_exammanagement'));
                } else {
                    $worksheet->setCellValue('C9', get_string('result', 'mod_exammanagement'));
                }

                foreach ($summarytable as $gradestep => $options) {
                    $worksheet->setCellValue("A".$rowcounter, strval($gradestep));
                    $worksheet->setCellValue("B".$rowcounter, $options["from"] . " - " . $options["to"]);

                    $worksheet->setCellValue("C".$rowcounter, $options["countNoBonus"]);

                    if ($bonusstepsentered) {
                        $worksheet->setCellValue("D".$rowcounter, $options["countBonus"]);
                    }

                    $rowcounter++;
                }
            }

            // output table 2
            $registered = $userobj->getParticipantsCount();
            $numberparticipants = $registered - $countnt - $countfa - $countsick;
            $numberparticipantspercent = number_format($numberparticipants / $registered * 100, 2);
            $ntpercent = number_format($countnt / $registered * 100, 2);
            $fapercent = number_format($countfa / $registered * 100, 2);
            $sickpercent = number_format($countsick / $registered * 100, 2);

            $worksheet->setCellValue('B23', get_string('count', 'mod_exammanagement'));
            $worksheet->setCellValue('C23', get_string('inpercent', 'mod_exammanagement'));

            $worksheet->setCellValue('A24', get_string('registered', 'mod_exammanagement'));
            $worksheet->setCellValue('A25', get_string('participants', 'mod_exammanagement'));
            $worksheet->setCellValue('A26', get_string('nt', 'mod_exammanagement'));
            $worksheet->setCellValue('A27', get_string('fa', 'mod_exammanagement'));
            $worksheet->setCellValue('A28', get_string('ill', 'mod_exammanagement'));

            $worksheet->setCellValue('B24', $registered);
            $worksheet->setCellValue('B25', $numberparticipants);
            $worksheet->setCellValue('B26', $countnt);
            $worksheet->setCellValue('B27', $countfa);
            $worksheet->setCellValue('B28', $countsick);

            $worksheet->setCellValue('C24', 100);
            $worksheet->setCellValue('C25', $numberparticipantspercent);
            $worksheet->setCellValue('C26', $ntpercent);
            $worksheet->setCellValue('C27', $fapercent);
            $worksheet->setCellValue('C28', $sickpercent);

            // output table 3
            $passed = $numberparticipants - $notpassed - $notrated;

            if ($numberparticipants > 0) {
                $passedpercent = number_format($passed / $numberparticipants * 100, 2);
                $notpassedpercent = number_format($notpassed / $numberparticipants * 100 , 2);
                $notratedpercent = number_format($notrated / $numberparticipants * 100 , 2);
            } else {
                $passedpercent = 0;
                $notpassedpercent = 0;
                $notratedpercent = 0;
            }

            $worksheet->setCellValue('B31', get_string('count', 'mod_exammanagement'));
            $worksheet->setCellValue('C31', get_string('inpercent', 'mod_exammanagement'));

            $worksheet->setCellValue('A32', get_string('participants', 'mod_exammanagement'));
            $worksheet->setCellValue('A33', get_string('passed', 'mod_exammanagement'));
            $worksheet->setCellValue('A34', get_string('notpassed', 'mod_exammanagement'));

            $worksheet->setCellValue('B32', $numberparticipants);
            $worksheet->setCellValue('B33', $passed);
            $worksheet->setCellValue('B34', $notpassed);

            $worksheet->setCellValue('C32', 100);
            $worksheet->setCellValue('C33', $passedpercent);
            $worksheet->setCellValue('C34', $notpassedpercent);

            if ( $notrated > 0 ) {
                $worksheet->setCellValue('A35', get_string('notrated', 'mod_exammanagement'));
                $worksheet->setCellValue('B35', $notrated);
                $worksheet->setCellValue('C35', $notratedpercent);

                $worksheet->getStyle('A35')->applyFromArray($borderstylearray);
                $worksheet->getStyle('A35:C35')->getFont()->setBold(true);
                $worksheet->getStyle('A35:C35')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                $worksheet->getStyle('B35:C35')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }

            // SHEET 2 - assignments.
            $tasks = $exammanagementinstanceobj->getTasks();

            if ($tasks) {
                $taskcount = count($tasks);
            } else {
                $taskcount = 0;
            }

            if ($tasks || $bonusstepsentered) {

                $spreadsheet->createSheet();
                $worksheet = $spreadsheet->setActiveSheetIndex(1);

                $worksheet->setTitle(get_string('tasks_and_boni', 'mod_exammanagement'));

                // fortmatting for sheet 2

                // table 1
                if ($tasks) {
                    $worksheet->getStyle('A1:C1')->applyFromArray($headerstyle);
                    $range = "A2:C" . ($taskcount + 1);
                    $worksheet->getStyle($range)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $worksheet->getColumnDimension('A')->setWidth(13);
                    $worksheet->getColumnDimension('B')->setWidth(20);
                    $worksheet->getColumnDimension('C')->setWidth(20);

                    $worksheet->getStyle("A1:A".($taskcount + 1))->applyFromArray($borderstylearray);
                }

                // table 2
                if ($bonusstepsentered) {
                    $worksheet->getStyle('G1:H1')->applyFromArray($headerstyle);
                    $worksheet->getStyle('G1:H6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $worksheet->getColumnDimension('G')->setWidth(13);

                    $worksheet->getStyle("G1:G6")->applyFromArray($borderstylearray);
                }

                if ($tasks) {
                    // outpout table 1
                    $worksheet->setCellValue('A1', get_string('task', 'mod_exammanagement'));
                    $worksheet->setCellValue('B1', get_string('max_points', 'mod_exammanagement'));
                    $worksheet->setCellValue('C1', get_string('mean', 'mod_exammanagement'));

                    foreach ($tasks as $tasknumber => $points) {
                        $worksheet->setCellValueByColumnAndRow(1 , $tasknumber + 1, $tasknumber);
                        $worksheet->setCellValueByColumnAndRow(2 , $tasknumber + 1, $points);
                    }
                }

                // outpout table 2 - bonussteps
                if ($bonusstepsentered) {
                    if (current_language() === 'de') {
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
            }

            // SHEET 3 - details.

            $spreadsheet->createSheet();

            if ($tasks || $bonusstepsentered) { // if sheet 2 exists
                $worksheet = $spreadsheet->setActiveSheetIndex(2);
            } else {
                $worksheet = $spreadsheet->setActiveSheetIndex(1);
            }
            $worksheet->setTitle(get_string('details', 'mod_exammanagement'));

            // FORMATTING for sheet 3

            if ($bonuspointsentered) {
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

            for ($n = 1; $n <= $taskcount; $n++) {
                $worksheet->getColumnDimension($exammanagementinstanceobj->calculateCellAddress(5 + $n))->setWidth(8);
            }

            $worksheet->getColumnDimension($exammanagementinstanceobj->calculateCellAddress(5 + $n))->setWidth(15);

            if ($bonuspointsentered) {
                $worksheet->getColumnDimension($exammanagementinstanceobj->calculateCellAddress(6 + $n))->setWidth(15);
                $worksheet->getColumnDimension($exammanagementinstanceobj->calculateCellAddress(7 + $n))->setWidth(18);

            }

            $worksheet->getColumnDimension($exammanagementinstanceobj->calculateCellAddress(6 + $n + $bc))->setWidth(15);

            if ($bonusstepsentered) {
                $worksheet->getColumnDimension($exammanagementinstanceobj->calculateCellAddress(7 + $n + $bc))->setWidth(12);
                $worksheet->getColumnDimension($exammanagementinstanceobj->calculateCellAddress(8 + $n + $bc))->setWidth(30);
                $bs = 0;
            } else {
                $bs = -3;
            }

            // header and centered
            $range = "A1:" . $exammanagementinstanceobj->calculateCellAddress(9 + $n + $bc + $bs) . "1";
            $worksheet->getStyle($range)->applyFromArray($headerstyle);
            $range = "A2:" . $exammanagementinstanceobj->calculateCellAddress(9 + $n + $bc + $bs) . ( count($participants) + 1 );
            $worksheet->getStyle($range)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // border lines
            $worksheet->getStyle('C1:C' . (count($participants) + 1))->applyFromArray($borderstylearray);
            $worksheet->getStyle('E1:E' . (count($participants) + 1))->applyFromArray($borderstylearray);
            $worksheet->getStyle($exammanagementinstanceobj->calculateCellAddress(5 + $n) . '1:' . $exammanagementinstanceobj->calculateCellAddress(5 + $n) . (count($participants) + 1))->applyFromArray($borderstylearray);
            $worksheet->getStyle($exammanagementinstanceobj->calculateCellAddress(5 + $n) . '1:' . $exammanagementinstanceobj->calculateCellAddress(5 + $n) . (count($participants) + 1))->applyFromArray($borderstylearray);

            if ($bonuspointsentered) {
                $worksheet->getStyle($exammanagementinstanceobj->calculateCellAddress(5 + $n + $bc) . '1:' . $exammanagementinstanceobj->calculateCellAddress(5 + $n + $bc) . (count($participants) + 1))->applyFromArray($borderstylearray);
                $worksheet->getStyle($exammanagementinstanceobj->calculateCellAddress(5 + $n + $bc) . '1:' . $exammanagementinstanceobj->calculateCellAddress(6 + $n + $bc) . (count($participants) + 1))->applyFromArray($borderstylearray);
            }

            $worksheet->getStyle($exammanagementinstanceobj->calculateCellAddress(6 + $n + $bc) . '1:' . $exammanagementinstanceobj->calculateCellAddress(5 + $n + $bc) . (count($participants) + 1))->applyFromArray($borderstylearray);

            // output table 1
            $worksheet->setCellValue('A1', get_string('matrno', 'mod_exammanagement'));
            $worksheet->setCellValue('B1', get_string('lastname', 'mod_exammanagement'));
            $worksheet->setCellValue('C1', get_string('firstname', 'mod_exammanagement'));
            $worksheet->setCellValue('D1', get_string('room', 'mod_exammanagement'));
            $worksheet->setCellValue('E1', get_string('place', 'mod_exammanagement'));
            $worksheet->setCellValueByColumnAndRow(5 + $n, 1, get_string('points', 'mod_exammanagement'));

            for ($n = 1; $n <= $taskcount; $n++) {
                $worksheet->setCellValueByColumnAndRow(5 + $n, 1, 'A' . $n);
            }

            if ($bonuspointsentered) {
                $worksheet->setCellValueByColumnAndRow(6 + $n, 1, get_string('bonuspoints', 'mod_exammanagement'));
                $worksheet->setCellValueByColumnAndRow(7 + $n, 1, get_string('points_with_bonus', 'mod_exammanagement'));
            }

            $worksheet->setCellValueByColumnAndRow(6 + $n + $bc, 1, get_string('result', 'mod_exammanagement'));

            if ($bonusstepsentered) {
                $worksheet->setCellValueByColumnAndRow(7 + $n + $bc, 1, get_string('bonussteps', 'mod_exammanagement'));
                $worksheet->setCellValueByColumnAndRow(8 + $n + $bc, 1, get_string('resultwithbonus', 'mod_exammanagement'));
            }

            $rowcounter = 2;

            foreach ($participants as $participant) {

                $state = $userobj->getExamState($participant);

                $worksheet->setCellValue("A".$rowcounter, $participant->matrnr);
                $worksheet->setCellValue("B".$rowcounter, $participant->lastname);
                $worksheet->setCellValue("C".$rowcounter, $participant->firstname);

                if ($rooms && isset($participant->roomname) && isset($participant->place)) {
                    $worksheet->setCellValue("D".$rowcounter, $participant->roomname);
                    $worksheet->setCellValue("E".$rowcounter, $participant->place);
                } else {
                    $worksheet->setCellValue("D".$rowcounter, '-');
                    $worksheet->setCellValue("E".$rowcounter, '-');
                }

                $totalpoints = $userobj->calculatePoints($participant);
                $totalpointswithbonus = $userobj->calculatePoints($participant, true);

                $result = $userobj->calculateResultGrade($totalpointswithbonus);

                if (isset($participant->bonussteps)) {
                    $bonussteps = $participant->bonussteps;
                } else {
                    $bonussteps = '-';
                }

                if (isset($participant->bonuspoints)) {
                    $bonuspoints = $participant->bonuspoints;
                } else {
                    $bonuspoints = '-';
                }

                $resultwithbonus = $userobj->calculateResultGrade($totalpointswithbonus, $participant->bonussteps);

                if ($participant->exampoints) {
                    foreach (json_decode($participant->exampoints) as $key => $points) {
                        $worksheet->setCellValueByColumnAndRow(5 + $key, $rowcounter, $points);
                    }
                } else {
                    for ($n = 1; $n <= $taskcount; $n++) {
                        $worksheet->setCellValueByColumnAndRow(5 + $n, $rowcounter, '-');
                    }
                }

                $worksheet->setCellValueByColumnAndRow(5 + $n, $rowcounter, $exammanagementinstanceobj->formatNumberForDisplay($totalpoints, 'number'));

                if ($bonuspointsentered) {
                    $worksheet->setCellValueByColumnAndRow(6 + $n, $rowcounter, $bonuspoints);
                    $worksheet->setCellValueByColumnAndRow(7 + $n, $rowcounter, $exammanagementinstanceobj->formatNumberForDisplay($totalpointswithbonus, 'number'));
                }

                if ($gradingscale) {
                    $worksheet->setCellValueByColumnAndRow(6 + $n + $bc, $rowcounter, $result);
                } else {
                    $worksheet->setCellValueByColumnAndRow(6 + $n + $bc, $rowcounter, '-');
                }

                if ($bonusstepsentered) {
                    $worksheet->setCellValueByColumnAndRow(7 + $n + $bc, $rowcounter, $bonussteps);
                    $worksheet->setCellValueByColumnAndRow(8 + $n + $bc, $rowcounter, $resultwithbonus);
                }

                $rowcounter++;
            }

            // table 2 sheet 1 formular mean
            if ($taskcount) {
                $worksheet = $spreadsheet->setActiveSheetIndex(1);

                $participantscount = count($participants);

                $worksheet->getStyle("C2:C".$n)->getNumberFormat()->setFormatCode('0.00');

                for ($n = 1; $n <= $taskcount; $n++) {

                    $start = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(5 + $n).'2';
                    $end = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(5 + $n). ($participantscount + 1);

                    $mean = 0;

                    foreach ($spreadsheet->setActiveSheetIndex(2)->rangeToArray($start.':'.$end) as $val) {
                        if (is_numeric($val[0])) {
                            $mean += $val[0];
                        }
                    }

                    $mean = $mean / $participantscount;

                    $worksheet->setCellValueByColumnAndRow(
                        3,
                        1 + $n,
                        $mean
                    );
                }
            }

            $spreadsheet->setActiveSheetIndex(0);

            // generate filename without umlaute
            $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
            $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
            $filenameumlaute = get_string("examresults_statistics", "mod_exammanagement") . '_' . $exammanagementinstanceobj->getCleanCourseCategoryName() . '_' . $exammanagementinstanceobj->getCourse()->fullname . '_' . $exammanagementinstanceobj->moduleinstance->name . '.xlsx';
            $filename = preg_replace($umlaute, $replace, $filenameumlaute);

            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            // write excel file
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
            $writer->save('php://output');

        } else { // if user hasnt entered correct password for this session: show enterPasswordPage
            redirect ($exammanagementinstanceobj->getExammanagementUrl('checkpassword', $exammanagementinstanceobj->getCm()->id), null, null, null);
        }
    }
} else {
    $moodleobj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
