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
 * Allows to add course participants to mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\addcourseparticipants_form;
use stdclass;
use moodle_url;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or.
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e = optional_param('e', 0, PARAM_INT);

$moodleobj = Moodle::getInstance($id, $e);
$exammanagementinstanceobj = exammanagementInstance::getInstance($id, $e);
$userobj = userhandler::getinstance($id, $e, $exammanagementinstanceobj->getCm()->instance);

if ($moodleobj->checkCapability('mod/exammanagement:viewinstance')) {

    global $DB, $OUTPUT;

    if ($exammanagementinstanceobj->isExamDataDeleted()) {
        redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
            get_string('err_examdata_deleted', 'mod_exammanagement'), null, 'error');
    } else if (empty($userobj->getcourseparticipantsids())) {
        redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
            get_string('err_nocourseparticipants', 'mod_exammanagement'), null, 'error');
    } else {

         // If no password for moduleinstance is set or if user already entered correct password in this session: show main page.
        if (!isset($exammanagementinstanceobj->moduleinstance->password) || (isset($exammanagementinstanceobj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))) {

            // Instantiate form.
            $mform = new addcourseparticipants_form(null, array('id' => $id, 'e' => $e));

            // Form processing and displaying is done here.
            if ($mform->is_cancelled()) {
                // Handle form cancel operation, if cancel button is present on form.
                redirect(new moodle_url('/mod/exammanagement/viewParticipants.php', ['id' => $id]),
                    get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

            } else if ($fromform = $mform->get_data()) {
                // In this case you process validated data. $mform->get_data() returns data posted in form.

                $participantsidsarr = $userobj->filtercheckedparticipants($fromform);
                $deletedparticipantsidsarr = $userobj->filtercheckeddeletedparticipants($fromform);

                if ($participantsidsarr != false || $deletedparticipantsidsarr != false) {

                    $userobjarr = array();

                    if ($participantsidsarr) {

                        $courseid = $exammanagementinstanceobj->getCourse()->id;

                        foreach ($participantsidsarr as $participantid) {

                            if ($userobj->checkifalreadyparticipant($participantid) == false) {
                                $user = new stdClass();
                                $user->exammanagement = $exammanagementinstanceobj->getCm()->instance;
                                $user->courseid = $courseid;
                                $user->categoryid = $exammanagementinstanceobj->moduleinstance->categoryid;
                                $user->moodleuserid = $participantid;
                                $user->headerid = 0;

                                $dbman = $DB->get_manager();
                                $table = new \xmldb_table('exammanagement_participants');
                                $field = new \xmldb_field('plugininstanceid', XMLDB_TYPE_INTEGER, '10', null,
                                    XMLDB_NOTNULL, null, null);

                                if ($dbman->field_exists($table, $field)) {
                                    $user->plugininstanceid = 0; // For deprecated old version db version, should be removed.
                                }

                                array_push($userobjarr, $user);
                            }
                        }
                    }

                    if ($deletedparticipantsidsarr) {
                        foreach ($deletedparticipantsidsarr as $identifier) {
                                $temp = explode('_', $identifier);

                            if ($temp[0] == 'mid') {
                                $userobj->deleteparticipant($temp[1], false);
                            } else {
                                $userobj->deleteparticipant(false, $temp[1]);
                            }
                        }
                    }

                    $DB->insert_records('exammanagement_participants', $userobjarr);

                    redirect(new moodle_url('/mod/exammanagement/viewParticipants.php', ['id' => $id]),
                        get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
                } else {
                    redirect(new moodle_url('/mod/exammanagement/viewParticipants.php', ['id' => $id]),
                        get_string('alteration_failed', 'mod_exammanagement'), null, 'error');
                }

            } else {
                // This branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
                // or on the first display of the form.

                // Set data if checkboxes should be checked
                // (setDefault in the form is much more time consuming for big amount of participants).
                $defaultvalues = array('id' => $id);
                $courseparticipantsids = $userobj->getcourseparticipantsids();

                if (isset($courseparticipantsids)) {
                    foreach ($courseparticipantsids as $id) {
                        $defaultvalues['participants['.$id.']'] = true;
                    }
                }

                // Set default data (if any).
                $mform->set_data($defaultvalues);

                $moodleobj->setPage('addCourseParticipants');
                $moodleobj->outputPageHeader();

                $mform->display();

                // Finish the page.
                echo $OUTPUT->footer();
            }

        } else { // If user has not entered correct password for this session: show enterPasswordPage.
            redirect(new moodle_url('/mod/exammanagement/checkpassword.php', ['id' => $id]),
                null, null, null);
        }
    }
} else {
    redirect(new moodle_url('/mod/exammanagement/view.php', ['id' => $id]),
        get_string('nopermissions', 'mod_exammanagement'), null, 'error');
}
