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
 * @copyright   coactum GmbH 2018
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

define( "SEPARATOR", chr(9) ); //Tabulator
define( "NEWLINE", "\r\n" );

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

	if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page    

        global $CFG;

        //$MoodleObj->setPage('exportResultsPAULFile');

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
            } else if($participant->imtlogin !== false && $participant->imtlogin !== null){
                $foreName = '"' . $participant->firstname . '"';
                $middleName = '""';
                $name = '"' . $participant->lastname . '"';
            }

            $examNumber = '""';
            $matNr = '"' . $UserObj->getUserMatrNr($participant->moodleuserid, $participant->imtlogin) .'"';
            $resultWithBonus = '"' . $resultWithBonus . '"';

            $textfile .= $examNumber . SEPARATOR . $matNr . SEPARATOR . $foreName . SEPARATOR . $middleName . SEPARATOR . $name . SEPARATOR . $resultWithBonus . NEWLINE;
        }

        //generate filename without umlaute
        $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
        $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
        $filenameUmlaute = get_string("results", "mod_exammanagement") . '_' . $ExammanagementInstanceObj->getCleanCourseCategoryName() . '_' . $ExammanagementInstanceObj->getCourse()->fullname . '_' . $ExammanagementInstanceObj->moduleinstance->name . '.txt';
        $filename = preg_replace($umlaute, $replace, $filenameUmlaute);

        //convert string to Latin1
        $textfile = utf8_decode($textfile);

        //return content as file
        header( "Content-Type: application/force-download" );
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
                        } else if($participant->imtlogin !== false && $participant->imtlogin !== null){
                            $foreName = '"' . $participant->firstname . '"';
                            $middleName = '""';
                            $name = '"' . $participant->lastname . '"';
                        }
        
                        $examNumber = '""';
                        $matNr = '"' . $UserObj->getUserMatrNr($participant->moodleuserid, $participant->imtlogin) .'"';
                        $resultWithBonus = '"' . $resultWithBonus . '"';
        
                        $textfile .= $examNumber . SEPARATOR . $matNr . SEPARATOR . $foreName . SEPARATOR . $middleName . SEPARATOR . $name . SEPARATOR . $resultWithBonus . NEWLINE;
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
                        } else if($participant->imtlogin !== false && $participant->imtlogin !== null){
                            $foreName = '"' . $participant->firstname . '"';
                            $middleName = '""';
                            $name = '"' . $participant->lastname . '"';
                        }
        
                        $examNumber = '""';
                        $matNr = '"' . $UserObj->getUserMatrNr($participant->moodleuserid, $participant->imtlogin) .'"';
                        $resultWithBonus = '"' . $resultWithBonus . '"';
                            
                        $textfile .= $examNumber . SEPARATOR . $matNr . SEPARATOR . $foreName . SEPARATOR . $middleName . SEPARATOR . $name . SEPARATOR . $resultWithBonus . NEWLINE;
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
                $textfile = utf8_encode($textfile);
                header( "Content-Type: application/force-download" );
                header( "Content-Disposition: attachment; filename=\"" . $filename . ".txt \"" );
                header( "Content-Length: ". strlen( $textfile ) );
                echo($textfile);
            } else if($ResultFilesZipArchive){
            // Close and send to users
                $ResultFilesZipArchive->close();
                header('Content-Type: application/zip');
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
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
