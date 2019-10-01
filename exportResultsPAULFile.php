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
 * Outputs exam results as PAUL text file for mod_exammanagement.
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
$UserObj = User::getInstance($id, $e);
$MoodleObj = Moodle::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();

define( "SEPARATOR", chr(9) ); //Tabulator
define( "NEWLINE", "\r\n" );

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){
	if($ExammanagementInstanceObj->isExamDataDeleted()){
        $MoodleObj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
	} else {
        if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page    

            global $CFG;

            if(!$ExammanagementInstanceObj->getInputResultsCount()){
                $MoodleObj->redirectToOverviewPage('afterexam', get_string('no_results_entered', 'mod_exammanagement'), 'error');
            } else if (!$ExammanagementInstanceObj->getDataDeletionDate()){
                $MoodleObj->redirectToOverviewPage('afterexam', get_string('correction_not_completed', 'mod_exammanagement'), 'error');
            }

            $PAULFileHeadersArr = $ExammanagementInstanceObj->getPaulTextfileHeaders();
            $ResultFilesZipArchive = false;

            $courseName = $ExammanagementInstanceObj->getCourse()->fullname;

            if ( !$PAULFileHeadersArr ){
            $examdate = $ExammanagementInstanceObj->getHrExamtime();
            $header1 = '"' . $courseName . '"' . SEPARATOR . '"Prüfung"' . SEPARATOR . '""' . SEPARATOR . '"' . $examdate . '"';
            $header2 = '"Prüfungsnummer"' . SEPARATOR . '"Matrikelnummer"' . SEPARATOR . '"Vorname"' . SEPARATOR . '"Mittelname"' . SEPARATOR . '"Name"' . SEPARATOR . '"Noten"';

            $textfile = $header1 . NEWLINE . $header2 . NEWLINE;

            if($afterexamreview == false){
                $ParticipantsArray = $UserObj->getAllExamParticipants();
            } else {
                $ParticipantsArray = $UserObj->getAllParticipantsWithResultsAfterExamReview();
            }

            if($ParticipantsArray){
                $ParticipantsArray = $UserObj->sortParticipantsArrayByName($ParticipantsArray);
                $matrNrArr = $UserObj->getMultipleUsersMatrNr($ParticipantsArray);
            }

            foreach($ParticipantsArray as $participant){

                $resultWithBonus = "";
                $resultState = $UserObj->getExamState($participant);

                if (!($resultState == "nt") && !($resultState == "fa") && !($resultState == "ill")) {
                    $resultWithBonus = $UserObj->calculateResultGradeWithBonus($UserObj->calculateResultGrade($participant), $resultState, $participant->bonus);
                } else {
                    $resultWithBonus = get_string($resultState, "mod_exammanagement");
                }

                $resultWithBonus = str_replace( '.', ',', $resultWithBonus );

                if($participant->moodleuserid !== false && $participant->moodleuserid !== null){
                    $user = $UserObj->getMoodleUser($participant->moodleuserid);
                    $foreName = '"' . $user->firstname . '"';
                    $middleName = '"' . $user->middlename . '"';
                    $name = '"' . $user->lastname . '"';
                    $login = $MoodleDBObj->getFieldFromDB('user','username', array('id' => $participant->moodleuserid));
                } else if($participant->imtlogin !== false && $participant->imtlogin !== null){
                    $foreName = '"' . $participant->firstname . '"';
                    $middleName = '""';
                    $name = '"' . $participant->lastname . '"';
                }

                $examNumber = '""';

                $matrnr = false;

                if($matrNrArr){
                    if($login && array_key_exists($login, $matrNrArr)){
                        $matrnr = $matrNrArr[$login];
                    } else if($participant->imtlogin && array_key_exists($participant->imtlogin, $matrNrArr)){
                        $matrnr = $matrNrArr[$participant->imtlogin];
                    } 
                }
        
                if($matrnr === false){
                    $matrnr = '-';
                }

                $resultWithBonus = '"' . $resultWithBonus . '"';

                $textfile .= $examNumber . SEPARATOR . $matrnr . SEPARATOR . $foreName . SEPARATOR . $middleName . SEPARATOR . $name . SEPARATOR . $resultWithBonus . NEWLINE;
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

                if(count($PAULFileHeadersArr) > 1 || (count($PAULFileHeadersArr) == 1 && $UserObj->getAllExamParticipantsByHeader(0))){

                    // Prepare File
                    $tempfile = tempnam(sys_get_temp_dir(), "examresults.zip");
                    $ResultFilesZipArchive = new ZipArchive();
                    $ResultFilesZipArchive->open($tempfile, ZipArchive::OVERWRITE);
                }

                $filecount = 0;

                $ParticipantsArray = $UserObj->getAllExamParticipantsByHeader(0);

                if($ParticipantsArray){
                    $ParticipantsArray = $UserObj->sortParticipantsArrayByName($ParticipantsArray);
                    $matrNrArr = $UserObj->getMultipleUsersMatrNr($ParticipantsArray);
                }

                if($ParticipantsArray && $afterexamreview == false){

                    $examdate = $ExammanagementInstanceObj->getHrExamtime();

                    $header1 = '"' . $courseName . '"' . SEPARATOR . '"Prüfung"' . SEPARATOR . '""' . SEPARATOR . '"' . $examdate . '"';
                    $header2 = '"Prüfungsnummer"' . SEPARATOR . '"Matrikelnummer"' . SEPARATOR . '"Vorname"' . SEPARATOR . '"Mittelname"' . SEPARATOR . '"Name"' . SEPARATOR . '"Noten"';    
                    $textfile = $header1 . NEWLINE . $header2 . NEWLINE;

                    foreach($ParticipantsArray as $participant){
            
                            $resultWithBonus = "";
                            $resultState = $UserObj->getExamState($participant);
            
                            if (!($resultState == "nt") && !($resultState == "fa") && !($resultState == "ill")) {
                                $resultWithBonus = $UserObj->calculateResultGradeWithBonus($UserObj->calculateResultGrade($participant), $resultState, $participant->bonus);
                            } else {
                                $resultWithBonus = get_string($resultState, "mod_exammanagement");
                            }
            
                            $resultWithBonus = str_replace( '.', ',', $resultWithBonus );
            
                            if($participant->moodleuserid !== false && $participant->moodleuserid !== null){
                                $user = $UserObj->getMoodleUser($participant->moodleuserid);
                                $foreName = '"' . $user->firstname . '"';
                                $middleName = '"' . $user->middlename . '"';
                                $name = '"' . $user->lastname . '"';
                                $login = $MoodleDBObj->getFieldFromDB('user','username', array('id' => $participant->moodleuserid));
                            } else if($participant->imtlogin !== false && $participant->imtlogin !== null){
                                $foreName = '"' . $participant->firstname . '"';
                                $middleName = '""';
                                $name = '"' . $participant->lastname . '"';
                            }
            
                            $examNumber = '""';

                            $matrnr = false;

                            if($matrNrArr){
                                if($login && array_key_exists($login, $matrNrArr)){
                                    $matrnr = $matrNrArr[$login];
                                } else if($participant->imtlogin && array_key_exists($participant->imtlogin, $matrNrArr)){
                                    $matrnr = $matrNrArr[$participant->imtlogin];
                                } 
                            }
                    
                            if($matrnr === false){
                                $matrnr = '-';
                            }                            $resultWithBonus = '"' . $resultWithBonus . '"';
            
                            $textfile .= $examNumber . SEPARATOR . $matrnr . SEPARATOR . $foreName . SEPARATOR . $middleName . SEPARATOR . $name . SEPARATOR . $resultWithBonus . NEWLINE;
                    }

                    $filecount += 1;

                    if($textfile && (count($PAULFileHeadersArr) > 1 || (count($PAULFileHeadersArr) == 1 && $UserObj->getAllExamParticipantsByHeader(0))) && $ResultFilesZipArchive){
                    // add content
                    $ResultFilesZipArchive->addFromString($filename . '_' . $filecount . '.txt', $textfile);

                    }
                }

                foreach($PAULFileHeadersArr as $key => $PAULFileHeader){

                    $ParticipantsArray = false;

                    if($afterexamreview == false){
                        $ParticipantsArray = $UserObj->getAllExamParticipantsByHeader($key+1);
                    } else {
                        $ParticipantsArray = $UserObj->getAllParticipantsWithResultsAfterExamReview();
                    }

                    if($ParticipantsArray){
                        $ParticipantsArray = $UserObj->sortParticipantsArrayByName($ParticipantsArray);
                        $matrNrArr = $UserObj->getMultipleUsersMatrNr($ParticipantsArray);
                    }

                    $textfile = false;

                    if($ParticipantsArray){

                        $textfile = $PAULFileHeader;
                        
                        foreach($ParticipantsArray as $participant){
            
                            $resultWithBonus = "";
                            $resultState = $UserObj->getExamState($participant);
            
                            if (!($resultState == "nt") && !($resultState == "fa") && !($resultState == "ill")) {
                                $resultWithBonus = $UserObj->calculateResultGradeWithBonus($UserObj->calculateResultGrade($participant), $resultState, $participant->bonus);
                            } else {
                                $resultWithBonus = get_string($resultState, "mod_exammanagement");
                            }
            
                            $resultWithBonus = str_replace( '.', ',', $resultWithBonus );
            
                            if($participant->moodleuserid !== false && $participant->moodleuserid !== null){
                                $user = $UserObj->getMoodleUser($participant->moodleuserid);
                                $foreName = '"' . $user->firstname . '"';
                                $middleName = '"' . $user->middlename . '"';
                                $name = '"' . $user->lastname . '"';
                                $login = $MoodleDBObj->getFieldFromDB('user','username', array('id' => $participant->moodleuserid));
                            } else if($participant->imtlogin !== false && $participant->imtlogin !== null){
                                $foreName = '"' . $participant->firstname . '"';
                                $middleName = '""';
                                $name = '"' . $participant->lastname . '"';
                            }
            
                            $examNumber = '""';

                            $matrnr = false;

                            if($matrNrArr){
                                if($login && array_key_exists($login, $matrNrArr)){
                                    $matrnr = $matrNrArr[$login];
                                } else if($participant->imtlogin && array_key_exists($participant->imtlogin, $matrNrArr)){
                                    $matrnr = $matrNrArr[$participant->imtlogin];
                                } 
                            }
                    
                            if($matrnr === false){
                                $matrnr = '-';
                            }                            $resultWithBonus = '"' . $resultWithBonus . '"';
                                
                            $textfile .= $examNumber . SEPARATOR . $matrnr . SEPARATOR . $foreName . SEPARATOR . $middleName . SEPARATOR . $name . SEPARATOR . $resultWithBonus . NEWLINE;
                        }
                    }

                    $filecount += 1;

                    if($textfile && (count($PAULFileHeadersArr) > 1 || (count($PAULFileHeadersArr) == 1 && $UserObj->getAllExamParticipantsByHeader(0))) && $ResultFilesZipArchive){
                    // add content
                    $ResultFilesZipArchive->addFromString($filename . '_' . $filecount . '.txt', $textfile);

                    }

                    if($afterexamreview == true){
                        break;
                    }
                }

                if($textfile && (count($PAULFileHeadersArr) == 1 || (count($PAULFileHeadersArr) == 0 && $UserObj->getAllExamParticipantsByHeader(0)) || $afterexamreview == true) && $ResultFilesZipArchive == false){
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