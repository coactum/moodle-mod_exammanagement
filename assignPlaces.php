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
 * Allows teacher to assign places to participants for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\assignplaces_form;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

use mod_exammanagement;

// Course_module ID, or.
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$uap  = optional_param('uap', 0, PARAM_INT);

$map = optional_param('map', 0, PARAM_INT);

// Active page.
$pagenr  = optional_param('page', 1, PARAM_INT);

$exammanagementinstanceobj = exammanagementInstance::getInstance($id, $e);
$moodleobj = Moodle::getInstance($id, $e);
$moodledbobj = MoodleDB::getInstance();
$userobj = User::getInstance($id, $e, $exammanagementinstanceobj->getCm()->instance);

if ($moodleobj->checkCapability('mod/exammanagement:viewinstance')) {

    if ($exammanagementinstanceobj->isExamDataDeleted()) {
        $moodleobj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
    } else {
        if (!isset($exammanagementinstanceobj->moduleinstance->password) || (isset($exammanagementinstanceobj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))) { // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

            if (!$exammanagementinstanceobj->getRoomsCount()) {
                $moodleobj->redirectToOverviewPage('forexam', get_string('no_rooms_added', 'mod_exammanagement'), 'error');
            } else if (!$userobj->getParticipantsCount()) {
                $moodleobj->redirectToOverviewPage('forexam', get_string('no_participants_added', 'mod_exammanagement'), 'error');
            } else if ($exammanagementinstanceobj->getTotalNumberOfSeats() == 0) {
                $moodleobj->redirectToOverviewPage('forexam', get_string('no_rooms_added', 'mod_exammanagement'), 'error');
            }

            if ($uap) {
                 require_sesskey();

                // Reset all exiting places for participants.
                 $moodledbobj->setFieldInDB('exammanagement_participants', 'roomid', null, array('exammanagement' => $exammanagementinstanceobj->getCm()->instance));
                 $moodledbobj->setFieldInDB('exammanagement_participants', 'roomname', null, array('exammanagement' => $exammanagementinstanceobj->getCm()->instance));
                 $moodledbobj->setFieldInDB('exammanagement_participants', 'place', null, array('exammanagement' => $exammanagementinstanceobj->getCm()->instance));
                 $exammanagementinstanceobj->moduleinstance->assignmentmode = null;
                 $moodledbobj->UpdateRecordInDB("exammanagement", $exammanagementinstanceobj->moduleinstance);
            }

            // Instantiate form.
            if ($map) {
                $mform = new assignplaces_form(null, array('id' => $id, 'e' => $e, 'map' => $map, 'pagenr' => $pagenr));
            } else {
                $mform = new assignplaces_form(null, array('id' => $id, 'e' => $e, 'map' => 0));
            }

            // Form processing and displaying is done here.
            if ($mform->is_cancelled()) {
                // Handle form cancel operation, if cancel button is present on form.

                $moodleobj->redirectToOverviewPage('aftercorrection', get_string('operation_canceled', 'mod_exammanagement'), 'warning');

            } else if ($fromform = $mform->get_data()) {
                // In this case you process validated data. $mform->get_data() returns data posted in form.

                if (isset($map) && $map) {
                    $examrooms = $exammanagementinstanceobj->getRooms('examrooms');
                    $participants = $userobj->getExamParticipants(array('mode' => 'all'), array());

                    foreach ($participants as $participant) {
                        if (isset($fromform->rooms[$participant->id])) {
                            if ($fromform->rooms[$participant->id] !== 'not_selected' && $fromform->places[$participant->id]) {

                                if (isset($participant->moodleuserid)) {
                                    $participant->login = null;
                                    $participant->firstname = null;
                                    $participant->lastname = null;
                                }

                                $participant->roomid = $fromform->rooms[$participant->id];
                                $participant->roomname = $examrooms[$fromform->rooms[$participant->id]]->name;
                                $participant->place = $fromform->places[$participant->id];

                                $moodledbobj->UpdateRecordInDB('exammanagement_participants', $participant);
                            } else {
                                if (isset($participant->moodleuserid)) {
                                    $participant->login = null;
                                    $participant->firstname = null;
                                    $participant->lastname = null;
                                }

                                $participant->roomid = null;
                                $participant->roomname = null;
                                $participant->place = null;

                                $moodledbobj->UpdateRecordInDB('exammanagement_participants', $participant);
                            }
                        }
                    }

                    $assignmentmode = $exammanagementinstanceobj->getAssignmentMode();

                    if ($assignmentmode) {
                        if (strlen($assignmentmode) == 3) {
                            $exammanagementinstanceobj->moduleinstance->assignmentmode = substr($assignmentmode, 0, -1).'1';
                        } else if (strlen($assignmentmode) == 2) {
                            $exammanagementinstanceobj->moduleinstance->assignmentmode = $assignmentmode.'1';
                        } else if (strlen($assignmentmode) == 1) {
                            $exammanagementinstanceobj->moduleinstance->assignmentmode = $assignmentmode.'01';
                        }
                    } else {
                        $exammanagementinstanceobj->moduleinstance->assignmentmode = '4';
                    }

                    $moodledbobj->UpdateRecordInDB("exammanagement", $exammanagementinstanceobj->moduleinstance);

                    $moodleobj->redirectToOverviewPage('forexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');

                } else {
                    if (!(isset($fromform->keep_seat_assignment) && $fromform->keep_seat_assignment)) { // All existing seat assignments should be deleted.
                        $moodledbobj->setFieldInDB('exammanagement_participants', 'roomid', null, array('exammanagement' => $exammanagementinstanceobj->getCm()->instance));
                        $moodledbobj->setFieldInDB('exammanagement_participants', 'roomname', null, array('exammanagement' => $exammanagementinstanceobj->getCm()->instance));
                        $moodledbobj->setFieldInDB('exammanagement_participants', 'place', null, array('exammanagement' => $exammanagementinstanceobj->getCm()->instance));
                        $exammanagementinstanceobj->moduleinstance->assignmentmode = null;
                        $moodledbobj->UpdateRecordInDB("exammanagement", $exammanagementinstanceobj->moduleinstance);
                        $keepseatassignment = false;
                        $participants = $userobj->getExamParticipants(array('mode' => 'all'), array('matrnr'), $fromform->assignment_mode_places); // Get all exam participants sorted by sortmode.
                    } else {
                        $exammanagementinstanceobj->moduleinstance->assignmentmode = null;
                        $keepseatassignment = true;
                        $participants = $userobj->getExamParticipants(array('mode' => 'no_seats_assigned'), array('matrnr'), $fromform->assignment_mode_places); // Todo: get only exam participants without places sorted by sortmode
                    }

                    $roommode = '0';

                    if (isset($fromform->assignment_mode_rooms) && $fromform->assignment_mode_rooms === '1') {
                        $examrooms = $exammanagementinstanceobj->getRooms('examrooms', 'places_smalltobig', $keepseatassignment);
                        $roommode = '1';
                    } else if (isset($fromform->assignment_mode_rooms) && $fromform->assignment_mode_rooms === '2') {
                        $examrooms = $exammanagementinstanceobj->getRooms('examrooms', 'places_bigtosmall', $keepseatassignment);
                        $roommode = '2';
                    } else {
                        $examrooms = $exammanagementinstanceobj->getRooms('examrooms', 'places_bigtosmall', $keepseatassignment);
                    }

                    if (!$participants) {
                        $moodleobj->redirectToOverviewPage('forexam', get_string('no_participants_added', 'mod_exammanagement'), 'error');
                    }

                    $participantscount = 0;

                    if ($examrooms) {

                        foreach ($examrooms as $room) {

                            if ($room) {
                                foreach ($participants as $key => $participant) {

                                    if ($key >= $participantscount) {

                                        if (isset($participant->moodleuserid)) {
                                            $participant->login = null;
                                            $participant->firstname = null;
                                            $participant->lastname = null;
                                        }

                                        unset($participant->matrnr);

                                        $participant->roomid = $room->roomid;
                                        $participant->roomname = $room->name;
                                        $participant->place = array_shift($room->places);

                                        // Set room and place.
                                        $moodledbobj->UpdateRecordInDB('exammanagement_participants', $participant);

                                        $participantscount += 1;

                                        if ($room->places == null) {  // If all places of room are assigned.
                                            break;
                                        }

                                    } else if ($participantscount == count($participants)) { // If all users have a place.
                                        break 2;
                                    }
                                }
                            }

                        }
                    }

                    // Save sort modes in db.
                    switch ($fromform->assignment_mode_places) {
                        case 'name':
                            $modeids = 1 . $roommode . $keepseatassignment;
                          break;
                        case 'matrnr':
                            $modeids = 2 . $roommode . $keepseatassignment;
                     break;
                        case 'random':
                            $modeids = 3 . $roommode . $keepseatassignment;
                     break;
                        default:
                            $modeids = false;
                    }

                    if ($modeids) {
                        $exammanagementinstanceobj->moduleinstance->assignmentmode = $modeids;
                        $moodledbobj->UpdateRecordInDB("exammanagement", $exammanagementinstanceobj->moduleinstance);
                    }

                    if ($participantscount < count($participants)) {    // If users are left without a room.
                        $moodleobj->redirectToOverviewPage('forexam', get_string('participants_missing_places', 'mod_exammanagement'), 'error');
                    } else {
                        $moodleobj->redirectToOverviewPage('forexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');
                    }
                }
            } else {
                // This branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
                // or on the first display of the form.

                // Set default data (if any).
                $mform->set_data(array('id' => $id));

                $moodleobj->setPage('assignPlaces');
                $moodleobj->outputPageHeader();

                $mform->display();

                $moodleobj->outputFooter();

            }

        } else { // If user has not entered correct password for this session: show enterPasswordPage.
            redirect ($exammanagementinstanceobj->getExammanagementUrl('checkpassword', $exammanagementinstanceobj->getCm()->id), null, null, null);
        }
    }
} else {
    $moodleobj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
