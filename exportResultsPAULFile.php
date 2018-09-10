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

use mod_exammanagement\ldap\ldapManager;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once(__DIR__.'/classes/ldap/ldapManager.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$LdapManagerObj = ldapManager::getInstance($id, $e);
$MoodleObj = Moodle::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();

define( "SEPARATOR", chr(9) ); //Tabulator
define( "NEWLINE", "\r\n" );

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

    global $CFG;

    if(!$ExammanagementInstanceObj->getInputResultsCount()){
      $MoodleObj->redirectToOverviewPage('forexam', 'Noch keine Prüfungsergebnisse eingegeben.', 'error');
    }

    $PAULFileHeadersArr = $ExammanagementInstanceObj->getPaulTextfileHeaders();

    $courseName = $ExammanagementInstanceObj->getCourse()->fullname;
    $results = $ExammanagementInstanceObj->getResults(); //to be changed

    if ( !$PAULFileHeadersArr ){
      $examdate = $ExammanagementInstanceObj->getHrExamtime();
      $header1 = '"' . $courseName . '"' . SEPARATOR . '"Prüfung"' . SEPARATOR . '""' . SEPARATOR . '"' . $examdate . '"';
      $header2 = '"Prüfungsnummer"' . SEPARATOR . '"Matrikelnummer"' . SEPARATOR . '"Vorname"' . SEPARATOR . '"Mittelname"' . SEPARATOR . '"Name"' . SEPARATOR . '"Noten"';

      $textfile = $header1 . NEWLINE . $header2 . NEWLINE;

      $savedParticipantsArray = $ExammanagementInstanceObj->getSavedParticipants();

      foreach($savedParticipantsArray as $participant){

        $resultWithBonus = "";

        foreach ($results as $resultObj){
            if($resultObj->uid == $participant){
                $resultState = $ExammanagementInstanceObj->getResultState($resultObj);

                if (!($resultState == "nt") && !($resultState == "fa") && !($resultState == "ill")) {
                    $resultWithBonus = $ExammanagementInstanceObj->calculateResultGrade($resultObj);
                } else {
                    $resultWithBonus = '5.0';
                }
            }
        }

        $resultWithBonus = str_replace( '.', ',', $resultWithBonus );

        $user = $ExammanagementInstanceObj->getMoodleUser($participant);
        $examNumber = '""';
        $matNr = '"' . $ExammanagementInstanceObj->getUserMatrNr($participant) .'"';
        $foreName = '"' . $user->firstname . '"';
        $middleName = '"' . $user->middlename . '"';
        $name = '"' . $user->lastname . '"';
        $resultWithBonus = '"' . $resultWithBonus . '"';

        $textfile .= $examNumber . SEPARATOR . $matNr . SEPARATOR . $foreName . SEPARATOR . $middleName . SEPARATOR . $name . SEPARATOR . $resultWithBonus . NEWLINE;
      }

      //generate filename without umlaute
      $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
      $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
      $filenameUmlaute = get_string("results", "mod_exammanagement") . '_' . $ExammanagementInstanceObj->moduleinstance->categoryid . '_' . $ExammanagementInstanceObj->getCourse()->fullname . '_' . $ExammanagementInstanceObj->moduleinstance->name . '.txt';
      $filename = preg_replace($umlaute, $replace, $filenameUmlaute);

      //convert string to Latin1
      //$textfile = mb_convert_encoding( $textfile, "ISO-8859-1");
      $textfile = utf8_decode($textfile);

      //return content as file
      header( "Content-Type: application/force-download" );
      header( "Content-Disposition: attachment; filename=\"" . $filename . "\"" );
      header( "Content-Length: ". strlen( $textfile ) );
      echo $textfile;

    } else {
        $filecount = 0;

        foreach($PAULFileHeadersArr as $key => $PAULFileHeader){
            $textfile = $PAULFileHeader->header;

            foreach ( $PAULFileHeader->participants as $participant ){

              $resultWithBonus = "";

              foreach ($results as $resultObj){
                  if($resultObj->uid == $participant){
                      $resultState = $ExammanagementInstanceObj->getResultState($resultObj);

                      if (!($resultState == "nt") && !($resultState == "fa") && !($resultState == "ill")) {
                          $resultWithBonus = $ExammanagementInstanceObj->calculateResultGrade($resultObj);
                      }
                  }
              }

            	//$resultWithBonus = str_replace( '.', ',', $resultWithBonus );

              $user = $ExammanagementInstanceObj->getMoodleUser($participant);
            	$examNumber = '""';
            	$matNr = '"' . $ExammanagementInstanceObj->getUserMatrNr($participant) .'"';
            	$foreName = '"' . $user->firstname . '"';
            	$middleName = '"' . $user->middlename . '"';
            	$name = '"' . $user->lastname . '"';
            	$resultWithBonus = '"' . $resultWithBonus . '"';

            	$textfile .= $examNumber . SEPARATOR . $matNr . SEPARATOR . $foreName . SEPARATOR . $middleName . SEPARATOR . $name . SEPARATOR . $resultWithBonus . NEWLINE;
            }

            $filecount += 1;

            //generate filename without umlaute
            $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
            $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
            $filenameUmlaute = get_string("results", "mod_exammanagement") . '_' . $ExammanagementInstanceObj->moduleinstance->categoryid . '_' . $ExammanagementInstanceObj->getCourse()->fullname . '_' . $ExammanagementInstanceObj->moduleinstance->name . '_' . $filecount . '.txt';
            $filename = preg_replace($umlaute, $replace, $filenameUmlaute);

            //convert string to Latin1
            //$textfile = mb_convert_encoding( $textfile, "ISO-8859-1");
            $textfile = utf8_decode($textfile);

            //return content as file
            header( "Content-Type: application/force-download" );
            header( "Content-Disposition: attachment; filename=\"" . $filename . "\"" );
            header( "Content-Length: ". strlen( $textfile ) );

            var_dump('now zip Files');
            echo $textfile;
        }
    }
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
