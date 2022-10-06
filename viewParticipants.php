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

            $moodleparticipants = $userobj->getExamParticipants(array('mode' => 'moodle'), array('matrnr', 'profile', 'groups'), 'name', true, $pagenr);

            $nonemoodleparticipants = $userobj->getExamParticipants(array('mode' => 'nonmoodle'), array('matrnr'));

            echo('<div class="d-flex justify-content-between"><div>');

            $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

            echo('<h3>'.get_string("viewParticipants", "mod_exammanagement"));

            if ($helptextsenabled) {
                echo($OUTPUT->help_icon('viewParticipants', 'mod_exammanagement', ''));
            }
            echo('</h3>');

            echo('</div><div>');

            if (!empty($userobj->getCourseParticipantsIDs())) {
                echo('<a href="' . $exammanagementinstanceobj->getExammanagementUrl("addCourseParticipants", $id) . '" class="btn btn-primary pull-right m-r-1 m-b-1" role="button" title="'.get_string("import_course_participants_optional", "mod_exammanagement").'"><span class="d-none d-xl-block">'.get_string("import_course_participants_optional", "mod_exammanagement").'</span><i class="fa fa-user d-xl-none" aria-hidden="true"></i></a>');
            }

            if (get_config('mod_exammanagement', 'enableldap')) {
                echo('<a href="' . $exammanagementinstanceobj->getExammanagementUrl("addParticipants", $id) . '" role="button" class="btn btn-primary pull-right m-r-1 m-b-1" title="'.get_string("import_participants_from_file_recommended", "mod_exammanagement").'"><span class="d-none d-xl-block">'.get_string("import_participants_from_file_recommended", "mod_exammanagement").'</span><i class="fa fa-file-text d-xl-none" aria-hidden="true"></i></a>');
            }

            if ($moodleparticipants) {
                echo('<a href="' . $exammanagementinstanceobj->getExammanagementUrl("convertToGroup", $id) . '" role="button" class="btn btn-primary m-r-3" title="'.get_string("convert_to_group", "mod_exammanagement").'"><span class="d-none d-xl-block">'.get_string("convert_to_group", "mod_exammanagement").'</span><i class="fa fa-file-text d-xl-none" aria-hidden="true"></i></a>');
            }

            echo('</div></div>');

            echo('<p>'.get_string("view_added_partipicants", "mod_exammanagement").'</p>');

            $i = 10 * ($pagenr - 1) + 1;

            if ($moodleparticipants || $nonemoodleparticipants) {

                $coursegroups = groups_get_all_groups($exammanagementinstanceobj->getCourse()->id);

                if (count($coursegroups) > 0) {
                    $coursegroups = true;
                } else {
                    $coursegroups = false;
                }

                $pagebar = new exammanagement_pagebar($id, 'viewParticipants.php?id=' . $id, sesskey(), $exammanagementinstanceobj->get_pagebar($allparticipants, 10, $pagenr), $exammanagementinstanceobj->get_pagecountoptions(),  count($moodleparticipants), count($allparticipants));
                echo $OUTPUT->render($pagebar);

                echo('<div class="table-responsive">');
                echo('<table class="table table-striped exammanagement_table">');
                echo('<thead class="exammanagement_tableheader exammanagement_brand_backgroundcolor"><th scope="col">#</th><th scope="col">'.get_string("participant", "mod_exammanagement").'</th><th scope="col">'.get_string("matriculation_number", "mod_exammanagement").'</th>');

                if ($coursegroups) {
                    echo('<th scope="col">'.get_string("course_groups", "mod_exammanagement").'</th>');
                }

                echo('<th scope="col">'.get_string("import_state", "mod_exammanagement").'</th><th scope="col" class="exammanagement_table_whiteborder_left"></th></thead>');
                echo('<tbody>');

                // Show participants with moodle account.
                if ($moodleparticipants) {

                    $courseparticipants = $userobj->getCourseParticipantsIDs();
                    $nonecourseparticipants = array();

                    foreach ($moodleparticipants as $key => $participant) {

                        if ($courseparticipants && in_array($participant->moodleuserid, $courseparticipants)) {
                            echo('<tr>');
                            echo('<th scope="row" id="'.$i.'">'.$i.'</th>');
                            echo('<td>'.$participant->profile.'</td>');
                            echo('<td>'.$participant->matrnr.'</td>');

                            if ($coursegroups) {
                                echo('<td>'.$participant->groups.'</td>');
                            }

                            echo('<td>'.get_string("state_added_to_exam", "mod_exammanagement").'</td>');
                            echo('<td class="exammanagement_brand_bordercolor_left"><a href="viewParticipants.php?id=' . $id . '&dpmid=' . $participant->moodleuserid . '&sesskey=' . sesskey() .'" onClick="javascript:return confirm(\''.get_string("participant_deletion_warning", "mod_exammanagement").'\');" title="'.get_string("delete_participant", "mod_exammanagement").'"><i class="fa fa-2x fa-trash" aria-hidden="true"></i></a></td>');
                            echo('</tr>');

                            $i++;
                        } else {
                            $participant->state = 'state_added_to_exam_no_course';
                            array_push($nonecourseparticipants, $participant);
                        }
                    }
                }

                // Show participants withouth moodle account.
                if ($nonemoodleparticipants && !empty($nonecourseparticipants)) {

                    $courseid = $exammanagementinstanceobj->getCourse()->id;
                    $participantsspecialstate = array_merge($nonemoodleparticipants, $nonecourseparticipants);

                    usort($participantsspecialstate, function($a, $b) {
                        return strnatcmp($a->lastname, $b->lastname); // Sort by lastname.
                    });
                } else if (!empty($nonecourseparticipants)) {
                    $courseid = $exammanagementinstanceobj->getCourse()->id;
                    $participantsspecialstate = $nonecourseparticipants;
                } else {
                    $participantsspecialstate = $nonemoodleparticipants;
                }

                if ($participantsspecialstate) {

                    if (!$moodleparticipants || $i == 1) {
                        if ($coursegroups) {
                            echo('<tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>');
                        } else {
                            echo('<tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>');
                        }
                    }

                    echo('<tr class="exammanagement_tableheader exammanagement_brand_backgroundcolor"><td colspan="6" class="text-center"><strong>'.get_string("participants_with_special_state", "mod_exammanagement").'</strong></td></tr>');

                    foreach ($participantsspecialstate as $key => $participant) {

                        echo('<tr>');
                        echo('<th scope="row" id="' . $i . '">' . $i . '</th>');

                        if (isset($participant->state) && $participant->state === 'state_added_to_exam_no_course') {
                            $moodleuser = $userobj->getMoodleUser($participant->moodleuserid);
                            $image = $OUTPUT->user_picture($moodleuser, array('courseid' => $courseid, 'link' => false, 'includefullname' => true, 'size' => 25));
                            echo('<td>' . $image . '</td>');
                        } else {
                            echo('<td>' . $participant->firstname . ' ' . $participant->lastname . '</td>');
                        }

                        echo('<td>' . $participant->matrnr . '</td>');

                        if ($coursegroups) {
                            echo('<td> - </td>');
                        }

                        if (isset($participant->state) && $participant->state === 'state_added_to_exam_no_course') {
                            echo('<td>'.get_string("state_added_to_exam_no_course", "mod_exammanagement") . ' ' . $OUTPUT->help_icon('state_added_to_exam_no_course', 'mod_exammanagement', '') . '</td>');
                            echo('<td class="exammanagement_brand_bordercolor_left"><a href="viewParticipants.php?id=' . $id . '&dpmid=' . $participant->moodleuserid . '&sesskey=' . sesskey() . '" onClick="javascript:return confirm(\''.get_string("participant_deletion_warning", "mod_exammanagement").'\');" title="'.get_string("delete_participant", "mod_exammanagement").'"><i class="fa fa-2x fa-trash" aria-hidden="true"></i></a></td>');
                        } else {
                            echo('<td>'.get_string("state_added_to_exam_no_moodle", "mod_exammanagement", ['systemname' => $exammanagementinstanceobj->getMoodleSystemName()]). ' ' . $OUTPUT->help_icon('state_added_to_exam_no_moodle', 'mod_exammanagement', '').'</td>');
                            echo('<td class="exammanagement_brand_bordercolor_left"><a href="viewParticipants.php?id='. $id . '&dpmatrnr=' . $participant->login . '&sesskey=' . sesskey() . '" onClick="javascript:return confirm(\''.get_string("participant_deletion_warning", "mod_exammanagement").'\');" title="'.get_string("delete_participant", "mod_exammanagement").'"><i class="fa fa-2x fa-trash" aria-hidden="true"></i></a></td>');
                        }

                        echo('</tr>');

                        $i++;

                    }

                }
                echo('<tr id="end"></tr></tbody></table></div>');

            } else {
                echo('<div class="row"><p class="col-12 text-xs-center">'.get_string("no_participants_added_page", "mod_exammanagement").'</p></div>');
            }

            echo('<div class="row"><span class="col-md-3"></span><span class="col-md-9"><a href="'.$exammanagementinstanceobj->getExammanagementUrl("view", $id).'" class="btn btn-primary">'.get_string("cancel", "mod_exammanagement").'</a>');

            if ($moodleparticipants || $nonemoodleparticipants || isset($participantsspecialstate)) {
                echo ('<a href="viewParticipants.php?id=' . $id . '&dap=1&sesskey=' . sesskey() . '" class="btn btn-default m-l-1" onClick="javascript:return confirm(\''.get_string("all_participants_deletion_warning", "mod_exammanagement").'\');">'.get_string("delete_all_participants", "mod_exammanagement").'</a></div>');
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
