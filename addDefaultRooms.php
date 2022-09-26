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
 * Allows admins to add default rooms to mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 namespace mod_exammanagement\general;

 use mod_exammanagement\forms\addDefaultRoomsForm;
 use stdClass;
 use core\output\notification;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$MoodleObj = Moodle::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);

if ($MoodleObj->checkCapability('mod/exammanagement:importdefaultrooms')) {

    if ($ExammanagementInstanceObj->isExamDataDeleted()) {
        $MoodleObj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
	} else {

        if (!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))) { // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

            $MoodleObj->setPage('addDefaultRooms');
            $MoodleObj->outputPageHeader();

            //Instantiate form
            $mform = new addDefaultRoomsForm(null, array('id'=>$id, 'e'=>$e));

            //Form processing and displaying is done here
            if ($mform->is_cancelled()) {
                //Handle form cancel operation, if cancel button is present on form
                redirect ($ExammanagementInstanceObj->getExammanagementUrl('chooseRooms', $ExammanagementInstanceObj->getCm()->id), get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

            } else if ($fromform = $mform->get_data()) {
                //In this case you process validated data. $mform->get_data() returns data posted in form.

                // retrieve file from form
                $defaultRoomsFile = $mform->get_file_content('defaultrooms_list');

                if ($defaultRoomsFile) {

                    if ($ExammanagementInstanceObj->countDefaultRooms()) {
                        $MoodleDBObj->DeleteRecordsFromDBSelect("exammanagement_rooms", "type = 'defaultroom'");
                    }

                    $fileContentArr = explode(PHP_EOL, $defaultRoomsFile); // separate lines

                    foreach ($fileContentArr as $key => $roomstr) {

                        $roomParameters = explode('*', $roomstr);

                        $roomObj = new stdClass();
                        $roomObj->roomid = $roomParameters[0];
                        $roomObj->name = $roomParameters[1];
                        $roomObj->description = $roomParameters[2];

                        if (isset($roomParameters[4]) && $roomParameters[4] !== '' && json_encode($roomParameters[4]) !== '"\r"' && json_encode($roomParameters[4]) !== '"\n"' && json_encode($roomParameters[4]) !== '"\r\n"') {
                            $svgStr = base64_encode($roomParameters[4]);
                        } else {
                            $svgStr = '';
                        }

                        $roomObj->seatingplan = $svgStr;
                        $roomObj->places = $roomParameters[3];
                        $roomObj->type = 'defaultroom';
                        $roomObj->moodleuserid = NULL;
                        $roomObj->misc = json_encode(array('timelastmodified' => time()));

                        $import = $MoodleDBObj->InsertRecordInDB('exammanagement_rooms', $roomObj); // bulkrecord insert too big
                    }

                    if ($import) {
                        redirect ($ExammanagementInstanceObj->getExammanagementUrl('chooseRooms', $id), get_string('operation_successfull', 'mod_exammanagement'), null, notification::NOTIFY_SUCCESS);
                    } else {
                        redirect ($ExammanagementInstanceObj->getExammanagementUrl('chooseRooms', $id), get_string('alteration_failed', 'mod_exammanagement'), null, notification::NOTIFY_ERROR);
                    }
                }

            } else {
                // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
                // or on the first display of the form.

                //Set default data (if any)
                $mform->set_data(array('id'=>$id));

                //displays the form
                $mform->display();
            }

            $MoodleObj->outputFooter();

        } else { // if user hasnt entered correct password for this session: show enterPasswordPage
            redirect ($ExammanagementInstanceObj->getExammanagementUrl('checkpassword', $ExammanagementInstanceObj->getCm()->id), null, null, null);
        }
    }
} else {

    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}