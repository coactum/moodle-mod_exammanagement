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
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\checkpassword_form;
use context_course;
use context_system;
use moodle_url;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e = optional_param('e', 0, PARAM_INT);

$resetpw = optional_param('resetPW', 0, PARAM_INT);
$requestpwreset = optional_param('requestPWReset', 0, PARAM_INT);

$moodleobj = Moodle::getInstance($id, $e);
$exammanagementinstanceobj = exammanagementInstance::getInstance($id, $e);
$userobj = userhandler::getinstance($id, $e, $exammanagementinstanceobj->getCm()->instance);

if ($moodleobj->checkCapability('mod/exammanagement:viewinstance')) {

    global $DB, $OUTPUT;

    // Reset password.
    if ($moodleobj->checkCapability('mod/exammanagement:resetpassword') && $resetpw == true && isset($exammanagementinstanceobj->moduleinstance->password)) {

        $exammanagementinstanceobj->moduleinstance->password = null;
        $DB->update_record("exammanagement", $exammanagementinstanceobj->moduleinstance);

        // Send mail to all teachers to inform them about password reset.
        $role = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $courseid = $exammanagementinstanceobj->getCourse()->id;
        $coursecontext = context_course::instance($courseid);
        $teachers = get_role_users($role->id, $coursecontext);

        $mailsubject = get_string('password_reset_mailsubject', 'mod_exammanagement', ['systemname' => $exammanagementinstanceobj->getMoodleSystemName(), 'name' => $exammanagementinstanceobj->moduleinstance->name, 'coursename' => $exammanagementinstanceobj->getCourse()->fullname]);
        $text = get_string('password_reset_mailtext', 'mod_exammanagement', ['systemname' => $exammanagementinstanceobj->getMoodleSystemName(), 'name' => $exammanagementinstanceobj->moduleinstance->name, 'coursename' => $exammanagementinstanceobj->getCourse()->fullname]);

        foreach ($teachers as $user) {
            $exammanagementinstanceobj->sendSingleMessage($user->id, $mailsubject, $text, 'passwordresetmessage');
        }

        redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
            get_string('password_reset_successfull', 'mod_exammanagement',
            ['systemname' => $exammanagementinstanceobj->getMoodleSystemName()]), null, 'success');
    } else if ($resetpw == true) {
        redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
            get_string('password_reset_failed', 'mod_exammanagement'), null, 'error');
    }

    // Handle password reset request.
    if ($moodleobj->checkCapability('mod/exammanagement:requestpasswordreset') && $requestpwreset == true && get_config('mod_exammanagement', 'enablepasswordresetrequest') === '1' && isset($exammanagementinstanceobj->moduleinstance->password)) {

        require_sesskey();

        // Send message to users with global manager role to request password reset.
        $systemcontext = context_system::instance();
        $supportusers = get_users_by_capability($systemcontext, 'mod/exammanagement:resetpassword');

        global $USER;

        $mailsubject = get_string('password_reset_request_mailsubject', 'mod_exammanagement', ['systemname' => $exammanagementinstanceobj->getMoodleSystemName(), 'name' => $exammanagementinstanceobj->moduleinstance->name, 'coursename' => $exammanagementinstanceobj->getCourse()->fullname]);

        $url = new moodle_url('/user/view.php', array('id' => $USER->id, 'course' => $exammanagementinstanceobj->getCourse()->id));
        $profilelink = '<strong><a href="' . $url . '">' . $USER->firstname . ' ' . $USER->lastname . '</a></strong>';

        $urlstr = strval(new moodle_url('/mod/exammanagement/checkpassword.php', array('id' => $id, 'resetPW' => true)));
        $text = get_string('password_reset_request_mailtext', 'mod_exammanagement', ['systemname' => $exammanagementinstanceobj->getMoodleSystemName(), 'user' => $profilelink, 'coursename' => $exammanagementinstanceobj->getCourse()->fullname, 'url' => $urlstr]);

        foreach ($supportusers as $user) {
            $messageid = $exammanagementinstanceobj->sendSingleMessage($user, $mailsubject, $text, 'passwordresetrequest');
        }

        if (isset($messageid)) {
            redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
                get_string('password_reset_request_successfull', 'mod_exammanagement',
                ['systemname' => $exammanagementinstanceobj->getMoodleSystemName()]), null, 'success');
        } else {
            redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
                get_string('password_reset_request_failed', 'mod_exammanagement'), null, 'error');
        }
    }

    if (!isset($exammanagementinstanceobj->moduleinstance->password)) {
        redirect(new moodle_url('/mod/exammanagement/view.php', ['id' => $id]), null, null, null);
    }

    // Instantiate form.
    $mform = new checkpassword_form(null, array('id' => $id, 'e' => $e));

    // Form processing and displaying is done here.
    if ($mform->is_cancelled()) {
        // Handle form cancel operation, if cancel button is present on form.
        redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
            get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

    } else if ($fromform = $mform->get_data()) {
        // In this case you process validated data.

        $password = $fromform->password;
        $passwordhash = base64_decode($exammanagementinstanceobj->moduleinstance->password);

        if (password_verify($password, $passwordhash)) { // Check if password is correct.

            if (password_needs_rehash($passwordhash, PASSWORD_DEFAULT)) { // Check if passwords needs rehash because of new hash algorithm.

                // If so update saved password hash.
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $exammanagementinstanceobj->moduleinstance->password = $hash;

                $DB->update_record("exammanagement", $exammanagementinstanceobj->moduleinstance);

            }

            global $SESSION;

            // Remember login and redirect.
            $SESSION->loggedInExamOrganizationId = $id;

            redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
                get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
        } else { // If password is not correct.
            redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
                get_string('wrong_password', 'mod_exammanagement'), null, 'error');
        }

    } else {
        // This branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
        // or on the first display of the form.

        // Set default data.
        $mform->set_data(array('id' => $id));

        $moodleobj->setPage('checkpassword');
        $moodleobj->outputPageHeader();

        $mform->display();

        // Finish the page.
        echo $OUTPUT->footer();
    }

} else {
    redirect(new moodle_url('/mod/exammanagement/view.php', ['id' => $id]),
        get_string('nopermissions', 'mod_exammanagement'), null, 'error');
}
