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
 * Allows teacher to choose rooms for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\chooserooms_form;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or.
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

// Active page.
$pagenr  = optional_param('page', 1, PARAM_INT);

global $USER;

$deletecustomroomid  = optional_param('deletecustomroomid', 0, PARAM_TEXT);

$deletedefaultroomid  = optional_param('deletedefaultroomid', 0, PARAM_TEXT);

$moodleobj = Moodle::getInstance($id, $e);
$moodledbobj = MoodleDB::getInstance();
$exammanagementinstanceobj = exammanagementInstance::getInstance($id, $e);
$userobj = User::getInstance($id, $e, $exammanagementinstanceobj->getCm()->instance);

if ($moodleobj->checkCapability('mod/exammanagement:viewinstance')) {

    if ($exammanagementinstanceobj->isExamDataDeleted()) {
        $moodleobj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
    } else {

        // If no password for moduleinstance is set or if user already entered correct password in this session: show main page.
        if (!isset($exammanagementinstanceobj->moduleinstance->password) ||
            (isset($exammanagementinstanceobj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId) && $SESSION->loggedInExamOrganizationId == $id))) {

            if ($deletecustomroomid) {
                require_sesskey();

                if ($moodledbobj->checkIfRecordExists('exammanagement_rooms', array('roomid' => $deletecustomroomid, 'moodleuserid' => $USER->id))) {
                    if (!json_decode($exammanagementinstanceobj->getModuleinstance()->rooms)
                        || !in_array($deletecustomroomid, json_decode($exammanagementinstanceobj->getModuleinstance()->rooms))) {
                        $moodledbobj->DeleteRecordsFromDB('exammanagement_rooms', array('roomid' => $deletecustomroomid, 'moodleuserid' => $USER->id));
                    } else {
                        redirect ('chooseRooms.php?id='.$id, get_string('room_deselected_as_examroom', 'mod_exammanagement'), null, 'error');
                    }
                }
            }

            if ($deletedefaultroomid) {
                require_sesskey();

                if ($moodleobj->checkCapability('mod/exammanagement:importdefaultrooms')) {
                    if ($moodledbobj->checkIfRecordExists('exammanagement_rooms', array('roomid' => $deletedefaultroomid))) {
                        $moodledbobj->DeleteRecordsFromDB('exammanagement_rooms', array('roomid' => $deletedefaultroomid));
                    }
                } else {
                    redirect ('chooseRooms.php?id='.$id, get_string('nopermissions', 'mod_exammanagement'), null, 'error');
                }
            }

            // Instantiate form.
            $mform = new chooserooms_form(null, array('id' => $id, 'e' => $e, 'pagenr' => $pagenr));

            // Form processing and displaying is done here.
            if ($mform->is_cancelled()) {
                $moodleobj->redirectToOverviewPage('beforeexam', get_string('operation_canceled', 'mod_exammanagement'), 'warning');
            } else if ($fromform = $mform->get_data()) {
                // In this case you process validated data.

                $allrooms = get_object_vars($fromform);

                $roomsarray = $allrooms["rooms"];
                $checkedrooms = array();
                $uncheckedrooms = array();
                $oldrooms = json_decode($exammanagementinstanceobj->moduleinstance->rooms);

                if (!isset($oldrooms)) {
                    $oldrooms = array();
                }

                foreach ($roomsarray as $key => $value) {
                    if ($value == 1 && is_string($value)) {

                        $roomname = explode('_', $key);
                        $similiarrooms = $moodledbobj->getRecordsFromDB('exammanagement_rooms', array('name' => $roomname[0]));

                        foreach ($similiarrooms as $similiarroomobj) {
                            if (isset($oldrooms) && in_array($similiarroomobj->roomid, $oldrooms) && $similiarroomobj->roomid != $key) {
                                redirect ($exammanagementinstanceobj->getExammanagementUrl('chooseRooms', $id),
                                    get_string('err_roomsdoubleselected', 'mod_exammanagement'), null, 'error');
                            }
                        }

                        array_push($checkedrooms, $key);
                    } else if ($value == 0) {
                        array_push($uncheckedrooms, $key);
                    }
                }

                // Reset places assignment if an exam room where participants are seated is deselected.
                if ($oldrooms) {
                    $deselectedroomsarr = array_intersect($oldrooms, $uncheckedrooms); // Checking if some old exam rooms are deselected.
                } else {
                    $deselectedroomsarr = null;
                }
                if (isset($deselectedroomsarr)) {

                    $oldrooms = array_diff($oldrooms, $deselectedroomsarr);

                    foreach ($deselectedroomsarr as $roomid) {

                        if ($userobj->getParticipantsCount('room', $roomid)) { // If there are participants that have places in deselected rooms: delete whole places assignment.
                            $exammanagementinstanceobj->moduleinstance->assignmentmode = null;

                            $moodledbobj->setFieldInDB('exammanagement_participants', 'roomid', null, array('exammanagement' => $exammanagementinstanceobj->getCm()->instance));
                            $moodledbobj->setFieldInDB('exammanagement_participants', 'roomname', null, array('exammanagement' => $exammanagementinstanceobj->getCm()->instance));
                            $moodledbobj->setFieldInDB('exammanagement_participants', 'place', null, array('exammanagement' => $exammanagementinstanceobj->getCm()->instance));
                            break;
                        }
                    }
                }

                $checkedrooms = array_unique(array_merge($checkedrooms, $oldrooms));

                sort($checkedrooms); // Sort checked rooms ids for saving in DB.

                $exammanagementinstanceobj->moduleinstance->rooms = json_encode($checkedrooms);

                $update = $moodledbobj->UpdateRecordInDB("exammanagement", $exammanagementinstanceobj->moduleinstance);
                if ($update) {
                    $moodleobj->redirectToOverviewPage('beforeexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');
                } else {
                    $moodleobj->redirectToOverviewPage('beforeexam', get_string('alteration_failed', 'mod_exammanagement'), 'error');
                }

            } else {
                // This branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
                // or on the first display of the form.

                // Set default data (if any).
                $mform->set_data(array('id' => $id));

                $moodleobj->setPage('chooseRooms');
                $moodleobj->outputPageHeader();

                $mform->display();

                $moodleobj->outputFooter();
            }

        } else { // If user has not entered correct password for this session.
            redirect ($exammanagementinstanceobj->getExammanagementUrl('checkpassword', $exammanagementinstanceobj->getCm()->id), null, null, null);
        }
    }
} else {
    $moodleobj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
