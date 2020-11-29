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
 * Outputs exam results as text file for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;
use zipArchive;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$afterexamreview  = optional_param('afterexamreview', 0, PARAM_RAW);

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$UserObj = User::getInstance($id, $e, $ExammanagementInstanceObj->getCm()->instance);
$MoodleObj = Moodle::getInstance($id, $e);

define( "SEPARATOR", chr(9) ); //Tabulator
define( "NEWLINE", "\r\n" );

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

            $courseName = $ExammanagementInstanceObj->getCourse()->fullname;

            # get saved import file headers #

            $TextFileHeadersArr = $ExammanagementInstanceObj->getTextfileHeaders();
            $ResultFilesZipArchive = false;

            # if no headers of import files are saved because all participants are imported from course #

            if ( !$TextFileHeadersArr ){
                $examdate = $ExammanagementInstanceObj->getHrExamtime();
                $header1 = '"' . $courseName . '"' . SEPARATOR . '"Prüfung"' . SEPARATOR . '""' . SEPARATOR . '"' . $examdate . '"';
                $header2 = '"Prüfungsnummer"' . SEPARATOR . '"Matrikelnummer"' . SEPARATOR . '"Vorname"' . SEPARATOR . '"Mittelname"' . SEPARATOR . '"Name"' . SEPARATOR . '"Noten"';

                $textfile = $header1 . NEWLINE . $header2 . NEWLINE;

                if($afterexamreview == false){
                    $participants = $UserObj->getExamParticipants(array('mode'=>'all'), array('matrnr'));
                } else {  // if export of changed results after exam review
                    $participants = $UserObj->getExamParticipants(array('mode'=>'resultsafterexamreview'), array('matrnr'));
                }

                $examNumber = '""';

                foreach($participants as $participant){ // construct lines for each participant

                    $resultWithBonus = $ExammanagementInstanceObj->formatNumberForDisplay($UserObj->calculateResultGrade($UserObj->calculatePoints($participant, true), $participant->bonussteps));

                    $resultWithBonus = '"' . $resultWithBonus . '"';

                    $textfile .= $examNumber . SEPARATOR . '"' . $participant->matrnr . '"' . SEPARATOR . '"' . $participant->firstname . '"' . SEPARATOR . '""' . SEPARATOR . '"' . $participant->lastname . '"' . SEPARATOR . $resultWithBonus . NEWLINE;
                }

                //generate filename without umlaute
                $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
                $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
                $filenameUmlaute = get_string("results", "mod_exammanagement") . '_' . $ExammanagementInstanceObj->getCleanCourseCategoryName() . '_' . $ExammanagementInstanceObj->getCourse()->fullname . '_' . $ExammanagementInstanceObj->moduleinstance->name . '.txt';
                $filename = preg_replace($umlaute, $replace, $filenameUmlaute);

                //return content as file
                header( "Content-Type: application/force-download; charset=UTF-8" );
                header( "Content-Disposition: attachment; filename=\"" . $filename . "\"" );
                header( "Content-Length: ". strlen( $textfile ) );
                echo $textfile;

            } else {

                //generate filename without umlaute
                $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
                $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
                $filenameUmlaute = get_string("results", "mod_exammanagement") . '_' . $ExammanagementInstanceObj->getCleanCourseCategoryName() . '_' . $ExammanagementInstanceObj->getCourse()->fullname . '_' . $ExammanagementInstanceObj->moduleinstance->name;
                $filename = preg_replace($umlaute, $replace, $filenameUmlaute);

                $participantsFromCourse = $UserObj->getExamParticipants(array('mode'=>'header', 'id' => 0), array('matrnr')); // get all participants that are imported from course (header id = 0)

                if(count($TextFileHeadersArr) > 1 || (count($TextFileHeadersArr) == 1 && $participantsFromCourse)){ // if there are other participants that are read in from file

                    // Prepare zip file
                    $tempfile = tempnam(sys_get_temp_dir(), "examresults.zip");
                    $ResultFilesZipArchive = new ZipArchive();
                    $ResultFilesZipArchive->open($tempfile, ZipArchive::OVERWRITE);
                }

                $filecount = 0;

                if($participantsFromCourse && $afterexamreview == false){ // construct lines for participants from course (header id = 0)

                    $examdate = $ExammanagementInstanceObj->getHrExamtime();

                    $header1 = '"' . $courseName . '"' . SEPARATOR . '"Prüfung"' . SEPARATOR . '""' . SEPARATOR . '"' . $examdate . '"';
                    $header2 = '"Prüfungsnummer"' . SEPARATOR . '"Matrikelnummer"' . SEPARATOR . '"Vorname"' . SEPARATOR . '"Mittelname"' . SEPARATOR . '"Name"' . SEPARATOR . '"Noten"';
                    $textfile = $header1 . NEWLINE . $header2 . NEWLINE;

                    $examNumber = '""';

                    foreach($participantsFromCourse as $participant){

                        $resultWithBonus = $ExammanagementInstanceObj->formatNumberForDisplay($UserObj->calculateResultGrade($UserObj->calculatePoints($participant, true), $participant->bonussteps));

                        $resultWithBonus = '"' . $resultWithBonus . '"';

                        $textfile .= $examNumber . SEPARATOR . '"' . $participant->matrnr . '"' . SEPARATOR . '"' . $participant->firstname . '"' . SEPARATOR . '""' . SEPARATOR . '"' . $participant->lastname . '"' . SEPARATOR . $resultWithBonus . NEWLINE;
                    }

                    $filecount += 1;

                    if($textfile && (count($TextFileHeadersArr) > 1 || (count($TextFileHeadersArr) == 1 && $participantsFromCourse)) && $ResultFilesZipArchive){ // if there are more files coming: add content to archive (else it will be send to browser at the end of the code)
                        $ResultFilesZipArchive->addFromString($filename . '_' . $filecount . '.txt', $textfile);
                    }
                }

                foreach($TextFileHeadersArr as $key => $TextFileHeader){ // iterate over all headers and create new file for archive

                    if($afterexamreview == false){
                        $participants = $UserObj->getExamParticipants(array('mode'=>'header', 'id' => $key+1), array('matrnr'));
                    } else {
                        $participants = $UserObj->getExamParticipants(array('mode'=>'resultsafterexamreview'), array('matrnr'));
                    }

                    $textfile = false;

                    if($participants){

                        $textfile = $TextFileHeader . NEWLINE;

                        $examNumber = '""';

                        foreach($participants as $participant){

                            $resultWithBonus = $ExammanagementInstanceObj->formatNumberForDisplay($UserObj->calculateResultGrade($UserObj->calculatePoints($participant, true), $participant->bonussteps));

                            $resultWithBonus = '"' . $resultWithBonus . '"';

                            $textfile .= $examNumber . SEPARATOR . '"' . $participant->matrnr . '"' . SEPARATOR . '"' . $participant->firstname . '"' . SEPARATOR . '""' . SEPARATOR . '"' . $participant->lastname . '"' . SEPARATOR . $resultWithBonus . NEWLINE;
                        }
                    }

                    $filecount += 1;

                    if($textfile && (count($TextFileHeadersArr) > 1 || (count($TextFileHeadersArr) == 1 && $participantsFromCourse)) && $ResultFilesZipArchive){
                    // add content
                    $ResultFilesZipArchive->addFromString($filename . '_' . $filecount . '.txt', $textfile);

                    }

                    if($afterexamreview == true){
                        break;
                    }
                }

                if($textfile && (count($TextFileHeadersArr) == 1 || (count($TextFileHeadersArr) == 0 && $participantsFromCourse) || $afterexamreview == true) && $ResultFilesZipArchive == false){
                    header( "Content-Type: application/force-download; charset=UTF-8"  );
                    header( "Content-Disposition: attachment; filename=\"" . $filename . ".txt\"" );
                    header( "Content-Length: ". strlen( $textfile ) );
                    echo($textfile);
                } else if($ResultFilesZipArchive){
                // Close and send to users
                    $ResultFilesZipArchive->close();
                    header('Content-Type: application/zip; charset=UTF-8');
                    header('Content-Length: ' . filesize($tempfile));
                    header('Content-Disposition: attachment; filename="'.$filename.'.zip"');
                    readfile($tempfile);
                    unlink($tempfile);
                } else {
                    $MoodleObj->redirectToOverviewPage('', get_string('cannot_create_zip_archive', 'mod_exammanagement'), 'error');
                }
            }
        } else { // if user hasnt entered correct password for this session: show enterPasswordPage
            redirect ($ExammanagementInstanceObj->getExammanagementUrl('checkPassword', $ExammanagementInstanceObj->getCm()->id), null, null, null);
        }
    }
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}