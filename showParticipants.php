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

use mod_exammanagement\ldap\ldapManager;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once(__DIR__.'/classes/ldap/ldapManager.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$test  = optional_param('test', 0, PARAM_INT);

$MoodleObj = Moodle::getInstance($id, $e);
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$LdapManagerObj = ldapManager::getInstance($id, $e);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

    $MoodleObj->setPage('showParticipants');
    $MoodleObj->outputPageHeader();

    ###### list of participants ... ######

    echo('<div class="row"><div class="col-xs-6">');
    echo('<h3>'.get_string("view_participants", "mod_exammanagement").'</h3>');
    echo('</div><a class="col-xs-2" type="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a>');

    echo('<div class="col-xs-4"><a href="'.$ExammanagementInstanceObj->getExammanagementUrl("addParticipants", $id).'" role="button" class="btn btn-primary pull-right" title="'.get_string("import_participants", "mod_exammanagement").'"><span class="hidden-sm-down">'.get_string("import_participants", "mod_exammanagement").'</span><i class="fa fa-plus hidden-md-up" aria-hidden="true"></i></a>');

    if($MoodleObj->checkCapability('mod/exammanagement:importparticipantsfromcourse')){
        echo('<a href="'.$ExammanagementInstanceObj->getExammanagementUrl("addCourseParticipants", $id).'" class="btn btn-primary pull-right" role="button" title="'.get_string("import_course_participants", "mod_exammanagement").'"><span class="hidden-sm-down">'.get_string("import_course_participants", "mod_exammanagement").'</span><i class="fa fa-plus hidden-md-up" aria-hidden="true"></i></a>');
    }

    echo('</div></div>');

    echo($ExammanagementInstanceObj->ConcatHelptextStr('addParticipants'));

    echo ('<div class="row m-b-1 m-t-1"><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');

    $participantsIDs = $ExammanagementInstanceObj->getSavedParticipants();

    if($participantsIDs){
        usort($participantsIDs, function($a, $b){ //sort participants ids by name (custom function)

            global $id, $e;
            $ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);

            $aFirstname = $ExammanagementInstanceObj->getMoodleUser($a)->firstname;
            $aLastname = $ExammanagementInstanceObj->getMoodleUser($a)->lastname;
            $bFirstname = $ExammanagementInstanceObj->getMoodleUser($b)->firstname;
            $bLastname = $ExammanagementInstanceObj->getMoodleUser($b)->lastname;

            if ($aLastname == $bLastname) { //if names are even sort by first name
                return strcmp($aFirstname, $bFirstname);
            } else{
                return strcmp($aLastname, $bLastname); // else sort by last name
            }
        });

        if($LdapManagerObj->is_LDAP_config()){
            $LdapManagerObj->connect_ldap();
        }

        foreach ($participantsIDs as $key => $value) {

            $matrnr = $ExammanagementInstanceObj->getUserMatrNr($value);

            echo('<div class="row"><div class="col-xs-3">');
            echo($ExammanagementInstanceObj->getUserPicture($value).' '.$ExammanagementInstanceObj->getUserProfileLink($value));
            echo('</div><div class="col-xs-3">'.$matrnr.'</div>');
            echo('<div class="col-xs-3">'.$ExammanagementInstanceObj->getParticipantsGroupNames($value).'</div>');
            echo('<div class="col-xs-3">'.get_string("state_added_to_exam", "mod_exammanagement").'</div></div>');
        }
     }

    $MoodleObj->outputFooter();
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
