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
 * Prints participants form for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$MoodleObj = Moodle::getInstance($id, $e);
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$UserObj = User::getInstance($id, $e);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

    $MoodleObj->setPage('showParticipants');
    $MoodleObj->outputPageHeader();

    ###### list of participants ... ######

    echo('<div class="row"><div class="col-xs-5">');
    echo('<h3>'.get_string("view_participants", "mod_exammanagement").'</h3>');
    echo('</div><div class="col-xs-2"><a class="helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');

    echo('<div class="col-xs-5"><a href="'.$ExammanagementInstanceObj->getExammanagementUrl("addParticipants", $id).'" role="button" class="btn btn-primary pull-right" title="'.get_string("import_participants_from_file", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("import_participants_from_file", "mod_exammanagement").'</span><i class="fa fa-plus d-lg-none" aria-hidden="true"></i></a>');

    echo('<a href="'.$ExammanagementInstanceObj->getExammanagementUrl("addCourseParticipants", $id).'" class="btn btn-primary pull-right m-r-1" role="button" title="'.get_string("import_course_participants", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("import_course_participants", "mod_exammanagement").'</span><i class="fa fa-plus d-lg-none" aria-hidden="true"></i></a>');

    echo('</div></div>');

    echo($ExammanagementInstanceObj->ConcatHelptextStr('addParticipants'));

    echo ('<div class="row m-b-1 m-t-1"><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');

    $moodleParticipants = $UserObj->getAllMoodleExamParticipants();
    $noneMoodleParticipants = $UserObj->getAllNoneMoodleExamParticipants();

    // show participants with moodle account
    if($moodleParticipants){
        usort($moodleParticipants, function($a, $b){ //sort participants ids by name (custom function)

            global $UserObj;

            $aFirstname = $UserObj->getMoodleUser($a->moodleuserid)->firstname;
            $aLastname = $UserObj->getMoodleUser($a->moodleuserid)->lastname;
            $bFirstname = $UserObj->getMoodleUser($b->moodleuserid)->firstname;
            $bLastname = $UserObj->getMoodleUser($b->moodleuserid)->lastname;

            if ($aLastname == $bLastname) { //if names are even sort by first name
                return strcmp($aFirstname, $bFirstname);
            } else{
                return strcmp($aLastname, $bLastname); // else sort by last name
            }
        });


        foreach ($moodleParticipants as $key => $participantObj) {

            $matrnr = $UserObj->getUserMatrNr($participantObj->moodleuserid);

            echo('<div class="row"><div class="col-xs-3">');
            echo($UserObj->getUserPicture($participantObj->moodleuserid).' '.$UserObj->getUserProfileLink($participantObj->moodleuserid));
            echo('</div><div class="col-xs-3">'.$matrnr.'</div>');
            echo('<div class="col-xs-3">'.$UserObj->getParticipantsGroupNames($participantObj->moodleuserid).'</div>');
            echo('<div class="col-xs-3">'.get_string("state_added_to_exam", "mod_exammanagement").'</div></div>');
        }
     }

     echo('<hr />');
     // show participants withouth moodle account

     if($noneMoodleParticipants){
         usort($noneMoodleParticipants, function($a, $b){ //sort participants ids by name (custom function)

             $aFirstname = $a->firstname;
             $aLastname = $a->lastname;
             $bFirstname = $b->firstname;
             $bLastname = $b->lastname;

             if ($aLastname == $bLastname) { //if names are even sort by first name
                 return strcmp($aFirstname, $bFirstname);
             } else{
                 return strcmp($aLastname, $bLastname); // else sort by last name
             }
         });

         foreach ($noneMoodleParticipants as $key => $participantObj) {

             $matrnr = $UserObj->getUserMatrNr(false, $participantObj->imtlogin);

             echo('<div class="row"><div class="col-xs-3">');
             echo($participantObj->firstname.' '.$participantObj->lastname);
             echo('</div><div class="col-xs-3">'.$matrnr.'</div>');
             echo('<div class="col-xs-3"> - </div>');
             echo('<div class="col-xs-3">'.get_string("state_added_to_exam_no_moodle", "mod_exammanagement").'</div></div>');
         }
      } else {
            echo('<div class="row"><p class="col-xs-12 text-xs-center">'.get_string("no_participants_added", "mod_exammanagement").'</p></div>');
     }

     echo('<div class="row"><span class="col-sm-5"></span><a href="'.$ExammanagementInstanceObj->getExammanagementUrl("view", $id).'" class="btn btn-primary">'.get_string("cancel", "mod_exammanagement").'</a></div>');

    $MoodleObj->outputFooter();
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
