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
 * Allows admin to edit default room for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\editDefaultRoomForm;
use stdclass;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or.
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$roomid  = optional_param('roomid', 0, PARAM_TEXT);

$exammanagementinstanceobj = exammanagementInstance::getInstance($id, $e);
$moodleobj = Moodle::getInstance($id, $e);
$moodledbobj = MoodleDB::getInstance();

if ($moodleobj->checkCapability('mod/exammanagement:importdefaultrooms')) {

    if ($exammanagementinstanceobj->isExamDataDeleted()) {
        $moodleobj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
    } else {

        // If no password for moduleinstance is set or if user already entered correct password in this session: show main page.
        if (!isset($exammanagementinstanceobj->moduleinstance->password) ||
            (isset($exammanagementinstanceobj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))) {

            global $USER;

            if ($roomid) {

                $roomobj = $exammanagementinstanceobj->getRoomObj($roomid);

                if ($roomobj) {
                    if ($roomobj->type == 'defaultroom') {
                        $roomname = $roomobj->name;
                        $places = json_decode($roomobj->places);
                        $placescount = count($places);
                        $description = $roomobj->description;
                        $placesarray = implode(',', $places);

                        if (isset($places) && count($places) !== 0) {
                            $placespreview = implode(',', $places);
                        } else {
                            $placespreview = false;
                        }

                        $roomplanavailable = base64_decode($roomobj->seatingplan);
                    } else {
                        redirect ($exammanagementinstanceobj->getExammanagementUrl('chooseRooms', $id),
                            get_string('no_editable_default_room', 'mod_exammanagement'), null, 'error');
                    }
                }
            }

            // Instantiate form.
            if ($roomid && $roomobj && $roomobj->type == 'defaultroom') {
                $mform = new editDefaultRoomForm(null, array('id' => $id, 'e' => $e, 'placescount' => $placescount,
                    'placespreview' => $placespreview, 'roomplanavailable' => $roomplanavailable, 'existingroom' => true));
            } else {
                $mform = new editDefaultRoomForm(null, array('id' => $id, 'e' => $e, 'existingroom' => false));
            }
            // Form processing and displaying is done here.
            if ($mform->is_cancelled()) {
                // Handle form cancel operation, if cancel button is present on form.
                redirect ($exammanagementinstanceobj->getExammanagementUrl('chooseRooms', $exammanagementinstanceobj->getCm()->id),
                    get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

            } else if ($fromform = $mform->get_data()) {
                // In this case you process validated data. $mform->get_data() returns data posted in form.

                $roomid = $fromform->roomid;

                $roomname = $fromform->roomname;
                $description = $fromform->description;

                if (isset($fromform->editplaces)) {
                    $editplaces = $fromform->editplaces;
                } else {
                    $editplaces = 1;
                }
                $placesmode = $fromform->placesmode;

                if ($editplaces == 1) {
                    if ($placesmode == 'default') {
                        $placesroom = $fromform->placesroom;
                        $placesfree = $fromform->placesfree;
                    }

                    if ($placesmode == 'rows') {
                        $rowscount = $fromform->rowscount;
                        $placesrow = $fromform->placesrow;
                        $placesfree = $fromform->placesfree;
                        $rowsfree = $fromform->rowsfree;
                    }

                    if ($placesmode == 'all_individual') {
                        $placesarray = $fromform->placesarray;
                    }
                }

                $defaultroomsvg = $mform->get_file_content('defaultroom_svg');

                 // If default room exists and should be edited.
                if ($fromform->existingroom == true && $moodledbobj->checkIfRecordExists('exammanagement_rooms', array('roomid' => $roomid))) {

                    $roomobj = $moodledbobj->getRecordFromDB('exammanagement_rooms', array('roomid' => $roomid));

                    $roomobj->name = $roomname;
                    $roomobj->description = $description;

                    if ($editplaces == 1) {

                        if ($placesmode == 'default') {
                            $placesarr = array();

                            for ($i = 1; $i <= $placesroom; $i += $placesfree + 1) {

                                array_push($placesarr, strval($i));
                            }

                            $roomobj->places = json_encode($placesarr);
                        }

                        if ($placesmode == 'rows') {
                            $placesarr = array();

                            for ($i = 1; $i <= $rowscount; $i = $i + 1 + $rowsfree) {
                                for ($j = 1; $j <= $placesrow; $j += $placesfree + 1) {
                                    array_push($placesarr, 'R'.str_pad ( strval($i), 2, '0', STR_PAD_LEFT ).'/P'.str_pad ( strval($j), 2, '0', STR_PAD_LEFT ));
                                }
                            }

                            $roomobj->places = json_encode($placesarr);
                        }

                        if ($placesmode == 'all_individual') {
                            $placesarray = explode(',', $placesarray);
                            $placesarray = array_values(array_filter($placesarray, function($value) { return !is_null($value) && $value !== '' && $value !== ' ' && $value !== '  ';
                            }));
                            $roomobj->places = json_encode($placesarray);
                        }
                    }

                    if (isset($defaultroomsvg) && $defaultroomsvg !== false) {
                        $roomobj->seatingplan = base64_encode(str_replace(array("\r\n", "\r", "\n"), '', $defaultroomsvg));
                    }

                    $roomobj->misc = json_encode(array('timelastmodified' => time()));

                    $update = $moodledbobj->UpdateRecordInDB('exammanagement_rooms', $roomobj);

                    if ($update) {
                        redirect ($exammanagementinstanceobj->getExammanagementUrl('chooseRooms', $id), get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
                    } else {
                        redirect ($exammanagementinstanceobj->getExammanagementUrl('chooseRooms', $id), get_string('alteration_failed', 'mod_exammanagement'), null, 'error');
                    }
                } else { // If default room doesn't exists and should be created.

                    $roomobj = new stdClass();
                    $roomobj->roomid = $roomid;
                    $roomobj->name = $roomname;
                    $roomobj->description = $description;

                    if ($placesmode == 'default') {
                        $placesarr = array();

                        for ($i = 1; $i <= $placesroom; $i += $placesfree + 1) {

                            array_push($placesarr, strval($i));
                        }

                        $roomobj->places = json_encode($placesarr);
                    }

                    if ($placesmode == 'rows') {
                        $placesarr = array();

                        for ($i = 1; $i <= $rowscount; $i = $i + 1 + $rowsfree) {
                            for ($j = 1; $j <= $placesrow; $j += $placesfree + 1) {
                                array_push($placesarr, 'R'.str_pad ( strval($i), 2, '0', STR_PAD_LEFT ).'/P'.str_pad ( strval($j), 2, '0', STR_PAD_LEFT ));
                            }
                        }

                        $roomobj->places = json_encode($placesarr);
                    }

                    if ($placesmode == 'all_individual' && $placesarray !== 0) {
                        $placesarray = explode(',', $placesarray);
                        $placesarray = array_values(array_filter($placesarray, function($value) { return !is_null($value) && $value !== '' && $value !== ' ' && $value !== '  ';
                        }));
                        $roomobj->places = json_encode($placesarray);
                    }

                    if (isset($defaultroomsvg)) {
                        $roomobj->seatingplan = base64_encode(str_replace(array("\r\n", "\r", "\n"), '', $defaultroomsvg));
                    }

                    $roomobj->type = 'defaultroom';
                    $roomobj->moodleuserid = null;
                    $roomobj->misc = json_encode(array('timelastmodified' => time()));

                    $import = $moodledbobj->InsertRecordInDB('exammanagement_rooms', $roomobj);

                    if ($import) {
                        redirect ($exammanagementinstanceobj->getExammanagementUrl('chooseRooms', $id), get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
                    } else {
                        redirect ($exammanagementinstanceobj->getExammanagementUrl('chooseRooms', $id), get_string('alteration_failed', 'mod_exammanagement'), null, 'error');
                    }
                }

            } else {
                // This branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
                // or on the first display of the form.

                // Set default data (if any).
                if ($roomid) {

                    if (isset($roomobj) && $roomobj !== false && $roomobj->type == 'defaultroom') {
                        $mform->set_data(array('id' => $id, 'roomid' => $roomid, 'roomname' => $roomname,
                            'placescount' => $placescount, 'description' => $description, 'placesarray' => $placesarray, 'existingroom' => true));
                    } else {
                        $mform->set_data(array('id' => $id, 'existingroom' => false));
                    }
                } else {
                    $mform->set_data(array('id' => $id, 'existingroom' => false));
                }

                // Displays the form.
                $moodleobj->setPage('editDefaultRoom');
                $moodleobj->outputPageHeader();

                $mform->display();

                $moodleobj->outputFooter();

            }

        } else { // if user hasnt entered correct password for this session: show enterPasswordPage
            redirect ($exammanagementinstanceobj->getExammanagementUrl('checkpassword', $exammanagementinstanceobj->getCm()->id), null, null, null);
        }
    }
} else {
    $moodleobj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
