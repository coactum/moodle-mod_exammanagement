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

    $header1 = $ExammanagementInstanceObj->getPaulTextfileHeader();
    $PAULFileHeadersArr = '"Prüfungsnummer"' . SEPARATOR . '"Matrikelnummer"' . SEPARATOR . '"Vorname"' . SEPARATOR . '"Mittelname"' . SEPARATOR . '"Name"' . SEPARATOR . '"Noten"';
    $courseName = $course->get_attribute( "OBJ_DESC" );

    foreach($PAULFileHeadersArr as $key => $PAULFileHeader){

        if ( empty( $header1 ) ){
        	$id = $course->get_course_id();
        	$day = $examObject->getDateDay( $examTerm );
        	$month = $examObject->getDateMonth( $examTerm );
        	$year = $examObject->getDateYear( $examTerm );
        	$startHour = $examObject->getTimeStartHour( $examTerm );
        	$startMinute = $examObject->getTimeStartMinute( $examTerm );
        	$endHour = $examObject->getTimeEndHour( $examTerm );
        	$endMinute = $examObject->getTimeEndMinute( $examTerm );
        	$date = sprintf( "%02d.%02d.%04d %02d:%02d %02d:%02d", $day, $month, $year, $startHour, $startMinute, $endHour, $endMinute );
        	$header1 = '"' . $id . '"' . SEPARATOR . '"' . $courseName . '"' . SEPARATOR . '"Prüfung"' . SEPARATOR . '""' . SEPARATOR . '"' . $date . '"';
        }

        $textfile = $header1 . NEWLINE . $PAULFileHeader . NEWLINE;

        foreach ( $participants as $participant ){
        	$resultWithBonus = $eoDatabase->getExamResultWithBonus( $examTerm, $participant["imtLogin"] );
        	$resultWithBonus = str_replace( '.', ',', $resultWithBonus );

        	if ( $participant["isNT"] == 1 ) $resultWithBonus = "";
        	if ( $participant["isNT"] == "NT" ) $resultWithBonus = "";
        	if ( $participant["isNT"] == "BV" ) $resultWithBonus = "";
        	if ( $participant["isNT"] == "SICK" ) $resultWithBonus = "";

        	$examNumber = '""';
        	$matNr = '"' . $participant["matriculationNumber"] .'"';
        	$foreName = '"' . $participant["forename"] . '"';
        	$middleName = '""';
        	$name = '"' . $participant["name"] . '"';
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
    }

} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
