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
 * Shows participants of mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use stdclass;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$dap  = optional_param('dap', 0, PARAM_INT);
$dpmatrnr  = optional_param('dpmatrnr', 0, PARAM_TEXT);
$dpmid  = optional_param('dpmid', 0, PARAM_INT);

$MoodleObj = Moodle::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$UserObj = User::getInstance($id, $e, $ExammanagementInstanceObj->getCm()->instance);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

    if($ExammanagementInstanceObj->isExamDataDeleted()){
        $MoodleObj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
	} else {

        if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

            global $OUTPUT;

            $MoodleObj->setPage('viewParticipants');
            $MoodleObj->outputPageHeader();

            // Delete all participants.
            if ($dap) {
                require_sesskey();
                $UserObj->deleteAllParticipants();
                redirect ('viewParticipants.php?id=' . $id, null, null, null);
            }

            // Delete single participant.
            if ($dpmid) {
                require_sesskey();
                $UserObj->deleteParticipant($dpmid, false);
            } else if ($dpmatrnr) {
                require_sesskey();
                $UserObj->deleteParticipant(false, $dpmatrnr);
            }

            ###### list of participants ... ######

            $moodleParticipants = $UserObj->getExamParticipants(array('mode'=>'moodle'), array('matrnr', 'profile', 'groups'));

            $noneMoodleParticipants = $UserObj->getExamParticipants(array('mode'=>'nonmoodle'), array('matrnr'));

            echo('<div class="row"><div class="col-4">');

            $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

            echo('<h3>'.get_string("viewParticipants", "mod_exammanagement"));

            if($helptextsenabled){
                echo($OUTPUT->help_icon('viewParticipants', 'mod_exammanagement', ''));
            }
            echo('</h3>');

            echo('</div><div class="col-8">');

            if(!empty($UserObj->getCourseParticipantsIDs())){
                echo('<a href="'.$ExammanagementInstanceObj->getExammanagementUrl("addCourseParticipants", $id).'" class="btn btn-primary pull-right m-r-1 m-b-1" role="button" title="'.get_string("import_course_participants_optional", "mod_exammanagement").'"><span class="d-none d-xl-block">'.get_string("import_course_participants_optional", "mod_exammanagement").'</span><i class="fa fa-user d-xl-none" aria-hidden="true"></i></a>');
            }

            if(get_config('mod_exammanagement', 'enableldap')){
                echo('<a href="'.$ExammanagementInstanceObj->getExammanagementUrl("addParticipants", $id).'" role="button" class="btn btn-primary pull-right m-r-1 m-b-1" title="'.get_string("import_participants_from_file_recommended", "mod_exammanagement").'"><span class="d-none d-xl-block">'.get_string("import_participants_from_file_recommended", "mod_exammanagement").'</span><i class="fa fa-file-text d-xl-none" aria-hidden="true"></i></a>');
            }

            if($moodleParticipants){
                echo('<a href="'.$ExammanagementInstanceObj->getExammanagementUrl("convertToGroup", $id).'" role="button" class="btn btn-primary m-r-3" title="'.get_string("convert_to_group", "mod_exammanagement").'"><span class="d-none d-xl-block">'.get_string("convert_to_group", "mod_exammanagement").'</span><i class="fa fa-file-text d-xl-none" aria-hidden="true"></i></a>');
            }

            echo('</div></div>');

            echo('<p>'.get_string("view_added_partipicants", "mod_exammanagement").'</p>');

            $i = 1;

            if($moodleParticipants || $noneMoodleParticipants){

                $courseGroups = groups_get_all_groups($ExammanagementInstanceObj->getCourse()->id);

                if(count($courseGroups) > 0){
                    $courseGroups = true;
                } else {
                    $courseGroups = false;
                }

                echo('<div class="table-responsive">');
                echo('<table class="table table-striped exammanagement_table">');
                echo('<thead class="exammanagement_tableheader exammanagement_brand_backgroundcolor"><th scope="col">#</th><th scope="col">'.get_string("participant", "mod_exammanagement").'</th><th scope="col">'.get_string("matriculation_number", "mod_exammanagement").'</th>');

                if($courseGroups){
                    echo('<th scope="col">'.get_string("course_groups", "mod_exammanagement").'</th>');
                }

                echo('<th scope="col">'.get_string("import_state", "mod_exammanagement").'</th><th scope="col" class="exammanagement_table_whiteborder_left">'.get_string("options", "mod_exammanagement").'</th></thead>');
                echo('<tbody>');

                // show participants with moodle account
                if($moodleParticipants){

                    $courseParticipants = $UserObj->getCourseParticipantsIDs();
                    $noneCourseParticipants = array();

                    foreach ($moodleParticipants as $key => $participant) {

                        if($courseParticipants && in_array($participant->moodleuserid, $courseParticipants)){
                            echo('<tr>');
                            echo('<th scope="row" id="'.$i.'">'.$i.'</th>');
                            echo('<td>'.$participant->profile.'</td>');
                            echo('<td>'.$participant->matrnr.'</td>');

                            if($courseGroups){
                                echo('<td>'.$participant->groups.'</td>');
                            }

                            echo('<td>'.get_string("state_added_to_exam", "mod_exammanagement").'</td>');
                            echo('<td class="exammanagement_brand_bordercolor_left"><a href="viewParticipants.php?id=' . $id . '&dpmid=' . $participant->moodleuserid . '&sesskey=' . sesskey() .'" onClick="javascript:return confirm(\''.get_string("participant_deletion_warning", "mod_exammanagement").'\');" title="'.get_string("delete_participant", "mod_exammanagement").'"><i class="fa fa-2x fa-trash" aria-hidden="true"></i></a>');
                            echo('<a class="pull-right" href="#end" title="'.get_string("jump_to_end", "mod_exammanagement").'"><i class="fa fa-2x fa-lg fa-arrow-down" aria-hidden="true"></i></a></td>');
                            echo('</tr>');

                            $i++;
                        } else {
                            $participant->state = 'state_added_to_exam_no_course';
                            array_push($noneCourseParticipants, $participant);
                        }
                    }
                }

                // show participants withouth moodle account

                if($noneMoodleParticipants && !empty($noneCourseParticipants)){

                    $courseid = $ExammanagementInstanceObj->getCourse()->id;
                    $participants_specialstate = array_merge($noneMoodleParticipants, $noneCourseParticipants);

                    usort($participants_specialstate, function($a, $b){
                        return strnatcmp($a->lastname, $b->lastname); // sort by lastname
                    });
                } else if(!empty($noneCourseParticipants)){
                    $courseid = $ExammanagementInstanceObj->getCourse()->id;
                    $participants_specialstate = $noneCourseParticipants;
                } else {
                    $participants_specialstate = $noneMoodleParticipants;
                }

                if($participants_specialstate){

                    if(!$moodleParticipants || $i ==1){
                        if($courseGroups){
                            echo('<tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>');
                        } else {
                            echo('<tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>');
                        }
                    }

                    echo('<tr class="exammanagement_tableheader exammanagement_brand_backgroundcolor"><td colspan="6" class="text-center"><strong>'.get_string("participants_with_special_state", "mod_exammanagement").'</strong></td></tr>');


                    foreach ($participants_specialstate as $key => $participant) {

                        echo('<tr>');
                        echo('<th scope="row" id="' . $i . '">' . $i . '</th>');

                        if (isset($participant->state) && $participant->state === 'state_added_to_exam_no_course') {
                            $moodleUser = $UserObj->getMoodleUser($participant->moodleuserid);
                            $image = $OUTPUT->user_picture($moodleUser, array('courseid' => $courseid, 'link' => false, 'includefullname' => true, 'size' => 25));
                            echo('<td>' . $image . '</td>');
                        } else {
                            echo('<td>' . $participant->firstname . ' ' . $participant->lastname . '</td>');
                        }

                        echo('<td>' . $participant->matrnr . '</td>');

                        if ($courseGroups) {
                            echo('<td> - </td>');
                        }

                        if (isset($participant->state) && $participant->state === 'state_added_to_exam_no_course') {
                            echo('<td>'.get_string("state_added_to_exam_no_course", "mod_exammanagement") . ' ' . $OUTPUT->help_icon('state_added_to_exam_no_course', 'mod_exammanagement', '').'</td>');
                            echo('<td class="exammanagement_brand_bordercolor_left"><a href="viewParticipants.php?id=' . $id . '&dpmid=' . $participant->moodleuserid . '&sesskey=' . sesskey() . '" onClick="javascript:return confirm(\''.get_string("participant_deletion_warning", "mod_exammanagement").'\');" title="'.get_string("delete_participant", "mod_exammanagement").'"><i class="fa fa-2x fa-trash" aria-hidden="true"></i></a>');
                        } else {
                            echo('<td>'.get_string("state_added_to_exam_no_moodle", "mod_exammanagement",['systemname' => $ExammanagementInstanceObj->getMoodleSystemName()]). ' ' . $OUTPUT->help_icon('state_added_to_exam_no_moodle', 'mod_exammanagement', '').'</td>');
                            echo('<td class="exammanagement_brand_bordercolor_left"><a href="viewParticipants.php?id='. $id . '&dpmatrnr=' . $participant->login . '&sesskey=' . sesskey() . '" onClick="javascript:return confirm(\''.get_string("participant_deletion_warning", "mod_exammanagement").'\');" title="'.get_string("delete_participant", "mod_exammanagement").'"><i class="fa fa-2x fa-trash" aria-hidden="true"></i></a>');
                        }
                        echo('<a class="pull-right" href="#end" title="'.get_string("jump_to_end", "mod_exammanagement").'"><i class="fa fa-2x fa-lg fa-arrow-down" aria-hidden="true"></i></a></td>');
                        echo('</tr>');

                        $i++;

                    }

                }
                echo('<tr id="end"></tr></tbody></table></div>');

            } else {
                echo('<div class="row"><p class="col-12 text-xs-center">'.get_string("no_participants_added_page", "mod_exammanagement").'</p></div>');
            }

            echo('<div class="row"><span class="col-md-3"></span><span class="col-md-9"><a href="'.$ExammanagementInstanceObj->getExammanagementUrl("view", $id).'" class="btn btn-primary">'.get_string("cancel", "mod_exammanagement").'</a>');

            if($moodleParticipants || $noneMoodleParticipants || isset($participants_specialstate)){
                echo ('<a href="viewParticipants.php?id=' . $id . '&dap=1&sesskey=' . sesskey() . '" class="btn btn-default m-l-1" onClick="javascript:return confirm(\''.get_string("all_participants_deletion_warning", "mod_exammanagement").'\');">'.get_string("delete_all_participants", "mod_exammanagement").'</a></div>');
            }

            echo('</span>');

            $MoodleObj->outputFooter();
        } else { // if user hasnt entered correct password for this session: show enterPasswordPage
            redirect ($ExammanagementInstanceObj->getExammanagementUrl('checkpassword', $ExammanagementInstanceObj->getCm()->id), null, null, null);
        }
    }
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}