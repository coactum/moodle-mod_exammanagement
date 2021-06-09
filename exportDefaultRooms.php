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
 * Outputs default exam rooms as txt file
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);


$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$MoodleObj = Moodle::getInstance($id, $e);

define( "SEPARATOR", chr(42) ); //comma
define( "NEWLINE", "\r\n" );

if($MoodleObj->checkCapability('mod/exammanagement:importdefaultrooms')){
    if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

        $allDefaultRooms = $ExammanagementInstanceObj->getRooms('defaultrooms');

        if(!isset($allDefaultRooms) || $allDefaultRooms == false){
            redirect ('chooseRooms.php?id='.$id, get_string('no_default_rooms', 'mod_exammanagement'), null, 'error');
        }

        global $CFG;

        $textfile = '';

        foreach($allDefaultRooms as $roomObj){

            $textfile .= $roomObj->roomid . SEPARATOR . $roomObj->name . SEPARATOR . $roomObj->description . SEPARATOR . json_encode($roomObj->places) . SEPARATOR;

            if(isset($roomObj->seatingplan) && $roomObj->seatingplan !== ''){
                $textfile .= str_replace(array("\r\n","\r","\n"), '', base64_decode($roomObj->seatingplan));
            }

            if($roomObj !== end($allDefaultRooms)){
                $textfile .= NEWLINE;
            }
        }

        //generate filename without umlaute
        $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
        $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
        $filenameUmlaute = get_string("default_exam_rooms", "mod_exammanagement") . '_' . date('d_m_y') . '.txt';
        $filename = preg_replace($umlaute, $replace, $filenameUmlaute);

        //return content as file
        header( "Content-Type: application/force-download; charset=UTF-8" );
        header( "Content-Disposition: attachment; filename=\"" . $filename . "\"" );
        header( "Content-Length: ". strlen( $textfile ) );
        echo $textfile;

    } else { // if user hasnt entered correct password for this session: show enterPasswordPage
            redirect ($ExammanagementInstanceObj->getExammanagementUrl('checkPassword', $ExammanagementInstanceObj->getCm()->id), null, null, null);
    }
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}