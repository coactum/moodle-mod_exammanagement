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
$UserObj = User::getInstance($id, $e, $ExammanagementInstanceObj->moduleinstance->categoryid);
$MoodleObj = Moodle::getInstance($id, $e);

define( "SEPARATOR", chr(9) ); //Tabulator
define( "NEWLINE", "\r\n" );

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

    global $CFG;

    $MoodleObj->setPage('exportResultsPAULFile');

    if(!$ExammanagementInstanceObj->getInputResultsCount()){
      $MoodleObj->redirectToOverviewPage('afterexam', 'Noch keine Prüfungsergebnisse eingegeben.', 'error');
    } else if (!$ExammanagementInstanceObj->getDataDeletionDate()){
      $MoodleObj->redirectToOverviewPage('afterexam', 'Korrektur noch nicht abgeschloßen.', 'error');
    }

    $PAULFileHeadersArr = $ExammanagementInstanceObj->getPaulTextfileHeaders();
    //$PAULFileHeadersArr = false; //for testing

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

      foreach($ParticipantsArray as $participant){

        $resultWithBonus = "";
        $resultState = $UserObj->getExamState($participant);

        if (!($resultState == "nt") && !($resultState == "fa") && !($resultState == "ill")) {
            $resultWithBonus = $UserObj->calculateResultGrade($participant);
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
            $middleName = '';
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
      $filenameUmlaute = get_string("results", "mod_exammanagement") . '_' . strtoupper($ExammanagementInstanceObj->moduleinstance->categoryid) . '_' . $ExammanagementInstanceObj->getCourse()->fullname . '_' . $ExammanagementInstanceObj->moduleinstance->name . '.txt';
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
        $filenameUmlaute = get_string("results", "mod_exammanagement") . '_' . strtoupper($ExammanagementInstanceObj->moduleinstance->categoryid) . '_' . $ExammanagementInstanceObj->getCourse()->fullname . '_' . $ExammanagementInstanceObj->moduleinstance->name;
        $filename = preg_replace($umlaute, $replace, $filenameUmlaute);

        if(count($PAULFileHeadersArr) > 1){

            // Prepare File
            $tempfile = tempnam(sys_get_temp_dir(), "examresults.zip");
            $ResultFilesZipArchive = new ZipArchive();
            $ResultFilesZipArchive->open($tempfile, ZipArchive::OVERWRITE);
        }

        $filecount = 0;

        foreach($PAULFileHeadersArr as $key => $PAULFileHeader){

            if($afterexamreview == false){
                $ParticipantsArray = $UserObj->getAllExamParticipantsByHeader($key+1);
            } else {
                $ParticipantsArray = $UserObj->getAllParticipantsWithResultsAfterExamReview();
            }

            $textfile = false;

            if($ParticipantsArray){

                if($PAULFileHeader == true){
                    $examdate = $ExammanagementInstanceObj->getHrExamtime();

                    $header1 = '"' . $courseName . '"' . SEPARATOR . '"Prüfung"' . SEPARATOR . '""' . SEPARATOR . '"' . $examdate . '"';
                    $header2 = '"Prüfungsnummer"' . SEPARATOR . '"Matrikelnummer"' . SEPARATOR . '"Vorname"' . SEPARATOR . '"Mittelname"' . SEPARATOR . '"Name"' . SEPARATOR . '"Noten"';    
                    $textfile = $header1 . NEWLINE . $header2 . NEWLINE;
                } else {
                    $textfile = $PAULFileHeader;
                }
                
                foreach($ParticipantsArray as $participant){
    
                    $resultWithBonus = "";
                    $resultState = $UserObj->getExamState($participant);
    
                    if (!($resultState == "nt") && !($resultState == "fa") && !($resultState == "ill")) {
                        $resultWithBonus = $UserObj->calculateResultGrade($participant);
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
                        $middleName = '';
                        $name = '"' . $participant->lastname . '"';
                    }
    
                    $examNumber = '""';
                    $matNr = '"' . $UserObj->getUserMatrNr($participant->moodleuserid, $participant->imtlogin) .'"';
                    $resultWithBonus = '"' . $resultWithBonus . '"';
    
                    $textfile .= $examNumber . SEPARATOR . $matNr . SEPARATOR . $foreName . SEPARATOR . $middleName . SEPARATOR . $name . SEPARATOR . $resultWithBonus . NEWLINE;
                }
            }

            $filecount += 1;

            if($textfile && count($PAULFileHeadersArr) > 1 && $ResultFilesZipArchive){
             // add content
             $ResultFilesZipArchive->addFromString($filename . '_' . $filecount . '.txt', $textfile);

            }

            if($afterexamreview == true){
                break;
            }
        }

        if($textfile && (count($PAULFileHeadersArr) == 1 || $afterexamreview == true)){
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
            $MoodleObj->redirectToOverviewPage('', 'Fehler beim Erzeugen des zip-Archives', 'error');            
        }
    }
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
