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
 * Allows user to enter password to access module instance for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\checkPasswordForm;

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

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

		if($MoodleObj->checkCapability('mod/exammanagement:resetpassword') && $resetPW == true){
			$ExammanagementInstanceObj->moduleinstance->password = NULL;
			$MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
			$MoodleObj->redirectToOverviewPage('beforeexam', get_string('password_reset_successful', 'mod_exammanagement'), 'success');
		}

		if(!isset($ExammanagementInstanceObj->moduleinstance->password)){
			$MoodleObj->redirectToOverviewPage(NULL, NULL, NULL);
		}

		$MoodleObj->setPage('checkPassword');
		$MoodleObj-> outputPageHeader();

		global $SESSION;

		//Instantiate form
		$mform = new checkPasswordForm(null, array('id'=>$id, 'e'=>$e));

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
			$MoodleObj->redirectToOverviewPage('beforeexam', get_string('operation_canceled', 'mod_exammanagement'), 'warning');

		} else if ($fromform = $mform->get_data()) {
		  //In this case you process validated data. $mform->get_data() returns data posted in form.

		  $password = $fromform->password;
		  $password_hash = base64_decode($ExammanagementInstanceObj->moduleinstance->password);

		  if( password_verify($password, $password_hash) ){ // check if password is correct

			if( password_needs_rehash($password_hash, PASSWORD_DEFAULT) ){ // check if passwords needs rehash because of newer hash algorithm

				// if so update saved password_hash

				$hash = password_hash($password, PASSWORD_DEFAULT);
				$ExammanagementInstanceObj->moduleinstance->password = $hash;
				
				$MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
			
			}

			// remember login and redirect
			$SESSION->loggedInExamOrganizationId = $id;
			$MoodleObj->redirectToOverviewPage('beforeexam',get_string('operation_successfull', 'mod_exammanagement'), 'success');
			
			} else{ // if password is not correct
				$MoodleObj->redirectToOverviewPage('beforeexam', get_string('wrong_password', 'mod_exammanagement'), 'error');
				
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
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
