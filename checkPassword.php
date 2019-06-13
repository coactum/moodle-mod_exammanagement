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
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\checkPasswordForm;
use context_course;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$resetPW  = optional_param('resetPW', 0, PARAM_INT);
$requestPWReset  = optional_param('requestPWReset', 0, PARAM_INT);

$MoodleObj = Moodle::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$UserObj = User::getInstance($id, $e);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

		// reset pw
		if($MoodleObj->checkCapability('mod/exammanagement:resetpassword') && $resetPW == true){
			$ExammanagementInstanceObj->moduleinstance->password = NULL;
			$MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);

			// send mail to all teachers to inform them about pw reset
        	$role = $MoodleDBObj->getRecordFromDB('role', array('shortname' => 'editingteacher'));
        	$courseid = $ExammanagementInstanceObj->getCourse()->id;
        	$coursecontext = context_course::instance($courseid);
			$teachers = get_role_users($role->id, $coursecontext);
			
			$mailsubject = "[PANDA-Support] Zurücksetzen des Passwortes der Prüfungsorganisation ".$ExammanagementInstanceObj->moduleinstance->name." im Kurs ".$ExammanagementInstanceObj->getCourse()->fullname . " erfolgreich";
			$text =  'Der PANDA Support hat wie angefordert das Passwort der Prüfungsorganisation '.$ExammanagementInstanceObj->moduleinstance->name.' im Kurs '.$ExammanagementInstanceObj->getCourse()->fullname .' zurückgesetzt. Sie können nun ohne Eingabe eines Passwortes auf die Inhalte der Prüfungsorganisation zugreifen und falls gewünscht ein neues Passwort für die Prüfungsorganisation festlegen. <br>Viele Grüße, <br>Ihr PANDA-Team';
			$text .= '<br><br> <b>English version:</b> The PANDA support has resetted the password of the exam organization '.$ExammanagementInstanceObj->moduleinstance->name.' in course '.$ExammanagementInstanceObj->getCourse()->fullname .'. You can now access the contents of the exam organization without entering a password and, if required, define a new password for the exam organization. <br>Greetings, <br>Your PANDA Team';

			foreach($teachers as $user){
				$ExammanagementInstanceObj->sendSingleMessage($user->id, $mailsubject, $text);
			}


			$MoodleObj->redirectToOverviewPage('beforeexam', get_string('password_reset_successfull', 'mod_exammanagement'), 'success');
		} else if($resetPW == true){
			$MoodleObj->redirectToOverviewPage('beforeexam', get_string('password_reset_failed', 'mod_exammanagement'), 'error');			
		}

		// handle request of pw reset

		$idSupportUser = get_config('mod_exammanagement', 'moodleidsupportuser');
		if($MoodleObj->checkCapability('mod/exammanagement:requestpasswordreset') && $requestPWReset == true && ($idSupportUser && $idSupportUser !== 0)){

			// send mail to support adress from global settings dialog to request pw reset

			global $USER;

			$user = $idSupportUser;
			$mailsubject = "PANDA Prüfungsorganisation: Anforderung für Passwort-Reset für die Prüfungsorganisation ".$ExammanagementInstanceObj->moduleinstance->name." im Kurs ".$ExammanagementInstanceObj->getCourse()->fullname;
			$text = 'Der bzw. die PANDA Benutzerin '. $UserObj->getUserProfileLink($USER->id). ' hat das Zurücksetzen des Passwortes für die Prüfungsorganisation im Kurs '.$ExammanagementInstanceObj->getCourse()->fullname.' beantragt. <br> Durch einen Klick auf diesen <a href="'.$MoodleObj->getMoodleUrl("/mod/exammanagement/checkPassword.php", $id, 'resetPW', true).'">Link</a> können Sie als in PANDA angemeldeter Benutzer der Rollen Admin, Manager oder IMT-Kursersteller das Passwort der Prüfungsorganisation zurücksetzen. Dadurch können sämtliche Lehrenden des Kurses wieder ohne Eingabe eines Passwortes auf die Inhalt der Prüfungsorganisation zugreifen und werden darüber automatisch per Mail informiert.';
			$messageid = $ExammanagementInstanceObj->sendSingleMessage($user, $mailsubject, $text);
		
			if($messageid){
				$MoodleObj->redirectToOverviewPage('beforeexam', get_string('password_reset_request_successfull', 'mod_exammanagement'), 'success');			
			} else {
				$MoodleObj->redirectToOverviewPage('beforeexam', get_string('password_reset_request_failed', 'mod_exammanagement'), 'error');							
			}
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
			$MoodleObj->redirectToOverviewPage('beforeexam', get_string('operation_successfull', 'mod_exammanagement'), 'success');
			
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