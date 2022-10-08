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
use mod_exammanagement\output\exammanagement_pagebar;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$dap  = optional_param('dap', 0, PARAM_INT);
$dpmatrnr  = optional_param('dpmatrnr', 0, PARAM_TEXT);
$dpmid  = optional_param('dpmid', 0, PARAM_INT);

// Active page.
$pagenr  = optional_param('page', 1, PARAM_INT);

$moodleobj = Moodle::getInstance($id, $e);
$moodledbobj = MoodleDB::getInstance();
$exammanagementinstanceobj = exammanagementInstance::getInstance($id, $e);
$userobj = User::getInstance($id, $e, $exammanagementinstanceobj->getCm()->instance);

if ($moodleobj->checkCapability('mod/exammanagement:viewinstance')) {

    if ($exammanagementinstanceobj->isExamDataDeleted()) {
        $moodleobj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
    } else {

        // If no password for moduleinstance is set or if user already entered correct password in this session: show main page.
        if (!isset($exammanagementinstanceobj->moduleinstance->password) || (isset($exammanagementinstanceobj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))) {

            global $OUTPUT;

            // Delete all participants.
            if ($dap) {
                require_sesskey();
                $userobj->deleteAllParticipants();
                redirect ('viewParticipants.php?id=' . $id, null, null, null);
            }

            // Delete single participant.
            if ($dpmid) {
                require_sesskey();
                $userobj->deleteParticipant($dpmid, false);
            } else if ($dpmatrnr) {
                require_sesskey();
                $userobj->deleteParticipant(false, $dpmatrnr);
            }

            $moodleobj->setPage('viewParticipants');
            $moodleobj->outputPageHeader();

            // List of participants.

            $allparticipants = $userobj->getExamParticipants(array('mode' => 'all'), array());

            $participants = $userobj->getExamParticipants(array('mode' => 'all'), array('matrnr', 'profile', 'groups'), 'name', true, $pagenr);

            echo('<div class="d-flex justify-content-between"><div>');

            $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

            echo('<h3>'.get_string("viewParticipants", "mod_exammanagement"));

            if ($helptextsenabled) {
                echo($OUTPUT->help_icon('viewParticipants', 'mod_exammanagement', ''));
            }
            echo('</h3>');

            echo('</div><div>');

            if (!empty($userobj->getCourseParticipantsIDs())) {
                echo('<a href="' . $exammanagementinstanceobj->getExammanagementUrl("addCourseParticipants", $id) . '" class="btn btn-primary pull-right mr-1 mb-1" role="button" title="'.get_string("import_course_participants_optional", "mod_exammanagement").'"><span class="d-none d-xl-block">'.get_string("import_course_participants_optional", "mod_exammanagement").'</span><i class="fa fa-user d-xl-none" aria-hidden="true"></i></a>');
            }

            if (get_config('mod_exammanagement', 'enableldap')) {
                echo('<a href="' . $exammanagementinstanceobj->getExammanagementUrl("addParticipants", $id) . '" role="button" class="btn btn-primary pull-right mr-1 mb-1" title="'.get_string("import_participants_from_file_recommended", "mod_exammanagement").'"><span class="d-none d-xl-block">'.get_string("import_participants_from_file_recommended", "mod_exammanagement").'</span><i class="fa fa-file-text d-xl-none" aria-hidden="true"></i></a>');
            }

            if ($participants) {
                echo('<a href="' . $exammanagementinstanceobj->getExammanagementUrl("convertToGroup", $id) . '" role="button" class="btn btn-primary mr-3" title="'.get_string("convert_to_group", "mod_exammanagement").'"><span class="d-none d-xl-block">'.get_string("convert_to_group", "mod_exammanagement").'</span><i class="fa fa-file-text d-xl-none" aria-hidden="true"></i></a>');
            }

            echo('</div></div>');

            echo('<p>'.get_string("view_added_partipicants", "mod_exammanagement").'</p>');

            $i = 10 * ($pagenr - 1) + 1;

            if ($participants) {

                $coursegroups = groups_get_all_groups($exammanagementinstanceobj->getCourse()->id);

                if (count($coursegroups) > 0) {
                    $coursegroups = true;
                } else {
                    $coursegroups = false;
                }

                $pagebar = new exammanagement_pagebar($id, 'viewParticipants.php?id=' . $id, sesskey(), $exammanagementinstanceobj->get_pagebar($allparticipants, 10, $pagenr), $exammanagementinstanceobj->get_pagecountoptions(),  count($participants), count($allparticipants));

                echo $OUTPUT->render($pagebar);

                echo('<div class="table-responsive">');
                echo('<table class="table table-striped exammanagement_table">');
                echo('<thead class="exammanagement_tableheader exammanagement_brand_backgroundcolor"><th scope="col">#</th><th scope="col">'.get_string("participant", "mod_exammanagement").'</th><th scope="col">'.get_string("matriculation_number", "mod_exammanagement").'</th>');

                if ($coursegroups) {
                    echo('<th scope="col">'.get_string("course_groups", "mod_exammanagement").'</th>');
                }

                echo('<th scope="col">'.get_string("import_state", "mod_exammanagement").'</th><th scope="col" class="exammanagement_table_whiteborder_left"></th></thead>');
                echo('<tbody>');

                // Show participants.
                if ($participants) {

                    $courseparticipants = $userobj->getCourseParticipantsIDs();
                    $nonecourseparticipants = array();

                    foreach ($participants as $key => $participant) {

                        if (!isset($participant->moodleuserid)) {
                            $participant->state = 'state_added_to_exam_no_moodle';
                        } else if ($courseparticipants && in_array($participant->moodleuserid, $courseparticipants)) { // Participant is course participant.
                            $participant->state = 'state_added_to_exam';
                        } else {

                            $participant->state = 'state_added_to_exam_no_course';
                        }

                        echo('<tr>');
                        echo('<th scope="row" id="'.$i.'">'.$i.'</th>');

                        if ($participant->state == 'state_added_to_exam') {
                            echo('<td>' . $participant->profile . '</td>');
                        } else if ($participant->state == 'state_added_to_exam_no_course') {
                            $moodleuser = $userobj->getMoodleUser($participant->moodleuserid);
                            $image = $OUTPUT->user_picture($moodleuser, array('courseid' => false, 'link' => false, 'includefullname' => true, 'size' => 25));
                            echo('<td>' . $image . '</td>');
                        } else if ($participant->state == 'state_added_to_exam_no_moodle') {
                            echo('<td>' . $participant->firstname . ' ' . $participant->lastname . '</td>');
                        }

                        echo('<td>'.$participant->matrnr.'</td>');

                        if ($coursegroups) {
                            if ($participant->state == 'state_added_to_exam') {
                                echo('<td style="width: 45%">' . $participant->groups . '</td>');
                            } else {
                                echo('<td> - </td>');
                            }
                        }

                        if ($participant->state == 'state_added_to_exam') {
                            echo('<td>'.get_string($participant->state, "mod_exammanagement").'</td>');
                        } else if ($participant->state === 'state_added_to_exam_no_course') {
                            echo('<td>'.get_string("state_added_to_exam_no_course", "mod_exammanagement") . ' ' . $OUTPUT->help_icon('state_added_to_exam_no_course', 'mod_exammanagement', '') . '</td>');
                        } else if ($participant->state === 'state_added_to_exam_no_moodle') {
                            echo('<td>'.get_string("state_added_to_exam_no_moodle", "mod_exammanagement", ['systemname' => $exammanagementinstanceobj->getMoodleSystemName()]). ' ' . $OUTPUT->help_icon('state_added_to_exam_no_moodle', 'mod_exammanagement', '').'</td>');
                        }

                        echo('<td class="exammanagement_brand_bordercolor_left"><a href="viewParticipants.php?id=' . $id . '&dpmid=' . $participant->moodleuserid . '&sesskey=' . sesskey() .'" onClick="javascript:return confirm(\''.get_string("participant_deletion_warning", "mod_exammanagement").'\');" title="'.get_string("delete_participant", "mod_exammanagement").'"><i class="fa fa-2x fa-trash" aria-hidden="true"></i></a></td>');
                        echo('</tr>');
                        $i++;
                    }
                }

                echo('</tbody></table></div>');

            } else {
                echo('<div class="row"><p class="col-12 text-xs-center">'.get_string("no_participants_added_page", "mod_exammanagement").'</p></div>');
            }

            echo('<div class="row"><span class="col-md-3"></span><span class="col-md-9"><a href="'.$exammanagementinstanceobj->getExammanagementUrl("view", $id).'" class="btn btn-primary">'.get_string("cancel", "mod_exammanagement").'</a>');

            if ($participants) {
                echo ('<a href="viewParticipants.php?id=' . $id . '&dap=1&sesskey=' . sesskey() . '" class="btn btn-default ml-1" onClick="javascript:return confirm(\''.get_string("all_participants_deletion_warning", "mod_exammanagement").'\');">'.get_string("delete_all_participants", "mod_exammanagement").'</a></div>');
            }

            echo('</span>');

            $moodleobj->outputFooter();
        } else { // If user hasnt entered correct password for this session: show enterPasswordPage.
            redirect ($exammanagementinstanceobj->getExammanagementUrl('checkpassword', $exammanagementinstanceobj->getCm()->id), null, null, null);
        }
    }
} else {
    $moodleobj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
