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
 * Allows admins to import default rooms to mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\importdefaultrooms_form;
use stdClass;
use core\output\notification;
use moodle_url;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or.
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e = optional_param('e', 0, PARAM_INT);

$moodleobj = Moodle::getInstance($id, $e);
$exammanagementinstanceobj = exammanagementInstance::getInstance($id, $e);

if ($moodleobj->checkCapability('mod/exammanagement:importdefaultrooms')) {

    global $DB, $OUTPUT;

    if ($exammanagementinstanceobj->isExamDataDeleted()) {
        redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
            get_string('err_examdata_deleted', 'mod_exammanagement'), null, 'error');
    } else {

         // If no password for moduleinstance is set or if user already entered correct password in this session: show main page.
        if (!isset($exammanagementinstanceobj->moduleinstance->password) || (isset($exammanagementinstanceobj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))) {

            // Instantiate form.
            $mform = new importdefaultrooms_form(null, array('id' => $id, 'e' => $e));

            // Form processing and displaying is done here.
            if ($mform->is_cancelled()) {
                // Handle form cancel operation, if cancel button is present on form.
                redirect (new moodle_url('/mod/exammanagement/chooseRooms.php', ['id' => $id]), get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

            } else if ($fromform = $mform->get_data()) {
                // In this case you process validated data. $mform->get_data() returns data posted in form.

                // Retrieve file from form.
                $defaultroomsfile = $mform->get_file_content('defaultrooms_list');

                if ($defaultroomsfile) {

                    if ($exammanagementinstanceobj->countDefaultRooms()) {
                        $DB->delete_records_select("exammanagement_rooms", "type = 'defaultroom'");
                    }

                    $filecontentarr = explode(PHP_EOL, $defaultroomsfile); // Separate lines.

                    foreach ($filecontentarr as $key => $roomstr) {

                        $roomparameters = explode('*', $roomstr);

                        $roomobj = new stdClass();
                        $roomobj->roomid = $roomparameters[0];
                        $roomobj->name = $roomparameters[1];
                        $roomobj->description = $roomparameters[2];

                        if (isset($roomparameters[4]) && $roomparameters[4] !== '' && json_encode($roomparameters[4]) !== '"\r"' && json_encode($roomparameters[4]) !== '"\n"' && json_encode($roomparameters[4]) !== '"\r\n"') {
                            $svgstr = base64_encode($roomparameters[4]);
                        } else {
                            $svgstr = '';
                        }

                        $roomobj->seatingplan = $svgstr;
                        $roomobj->places = $roomparameters[3];
                        $roomobj->type = 'defaultroom';
                        $roomobj->moodleuserid = null;
                        $roomobj->misc = json_encode(array('timelastmodified' => time()));

                        $import = $DB->insert_record('exammanagement_rooms', $roomobj); // Bulkrecord insert too big.
                    }

                    if ($import) {
                        redirect (new moodle_url('/mod/exammanagement/chooseRooms.php', ['id' => $id]), get_string('operation_successfull', 'mod_exammanagement'), null, notification::NOTIFY_SUCCESS);
                    } else {
                        redirect (new moodle_url('/mod/exammanagement/chooseRooms.php', ['id' => $id]), get_string('alteration_failed', 'mod_exammanagement'), null, notification::NOTIFY_ERROR);
                    }
                }

            } else {
                // This branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
                // or on the first display of the form.

                // Set default data (if any).
                $mform->set_data(array('id' => $id));

                $moodleobj->setPage('importDefaultRooms');
                $moodleobj->outputPageHeader();

                $mform->display();

                // Finish the page.
                echo $OUTPUT->footer();
            }


        } else { // If user has not entered correct password for this session: show enterPasswordPage.
            redirect(new moodle_url('/mod/exammanagement/checkpassword.php', ['id' => $id]),
                null, null, null);;
        }
    }
} else {

    redirect(new moodle_url('/mod/exammanagement/view.php', ['id' => $id]),
        get_string('nopermissions', 'mod_exammanagement'), null, 'error');
}
