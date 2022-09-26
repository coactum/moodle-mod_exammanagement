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
 * Allows teacher to configure password for module instance for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\configurepassword_form;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$resetPW  = optional_param('resetPW', 0, PARAM_INT);

$MoodleObj = Moodle::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);

if ($MoodleObj->checkCapability('mod/exammanagement:viewinstance')) {

	if ($ExammanagementInstanceObj->isExamDataDeleted()) {
        $MoodleObj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
	} else {

		if (!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))) { // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

			if ($resetPW == true) {
				$ExammanagementInstanceObj->moduleinstance->password = NULL;
				$MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
				$MoodleObj->redirectToOverviewPage('beforeexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');
			}

			// Instantiate form.
			$mform = new configurepassword_form(null, array('id'=>$id, 'e'=>$e));

			// Form processing and displaying is done here.
			if ($mform->is_cancelled()) {
				// Handle form cancel operation, if cancel button is present on form.
				$MoodleObj->redirectToOverviewPage('beforeexam', get_string('operation_canceled', 'mod_exammanagement'), 'warning');

			} else if ($fromform = $mform->get_data()) { // In this case you process validated data.

				$password = $fromform->password;

				$password_hash = base64_encode(password_hash($password, PASSWORD_DEFAULT));

				$ExammanagementInstanceObj->moduleinstance->password = $password_hash;

				$update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

				if ($update) {
					$MoodleObj->redirectToOverviewPage('beforeexam',get_string('operation_successfull', 'mod_exammanagement'), 'success');
				} else {
					$MoodleObj->redirectToOverviewPage('beforeexam', get_string('alteration_failed', 'mod_exammanagement'), 'error');
				}

				} else {
					// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
					// or on the first display of the form.

					// Set default data (if any).
					$mform->set_data(array('id'=>$id));

					$MoodleObj->setPage('configurePassword');
					$MoodleObj->outputPageHeader();

					$mform->display();

					$MoodleObj->outputFooter();
				}

		} else { // If user has not entered correct password for this session.
			redirect ($ExammanagementInstanceObj->getExammanagementUrl('checkpassword', $ExammanagementInstanceObj->getCm()->id), null, null, null);
		}
	}
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}