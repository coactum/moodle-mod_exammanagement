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
 * Allows teacher to configure gradingscale for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\configureGradingscaleForm;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or.
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$moodleobj = Moodle::getInstance($id, $e);
$moodledbobj = MoodleDB::getInstance();
$exammanagementinstanceobj = exammanagementInstance::getInstance($id, $e);

if ($moodleobj->checkCapability('mod/exammanagement:viewinstance')) {

    if ($exammanagementinstanceobj->isExamDataDeleted()) {
        $moodleobj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
    } else {

        // If no password for moduleinstance is set or if user already entered correct password in this session: show main page.
        if (!isset($exammanagementinstanceobj->moduleinstance->password) ||
            (isset($exammanagementinstanceobj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))) {

            if ($exammanagementinstanceobj->moduleinstance->misc === null && !$exammanagementinstanceobj->getTaskCount()) {
                $moodleobj->redirectToOverviewPage('aftercorrection', get_string('no_tasks_configured', 'mod_exammanagement'), 'error');
            }

            // Instantiate form.
            $mform = new configureGradingscaleForm(null, array('id' => $id, 'e' => $e));

            // Form processing and displaying is done here.
            if ($mform->is_cancelled()) {
                // Handle form cancel operation, if cancel button is present on form.
                $moodleobj->redirectToOverviewPage('beforeexam', get_string('operation_canceled', 'mod_exammanagement'), 'warning');

            } else if ($fromform = $mform->get_data()) {
                // In this case you process validated data. $mform->get_data() returns data posted in form.

                $gradingscale = json_encode($fromform->gradingsteppoints);
                $exammanagementinstanceobj->moduleinstance->gradingscale = $gradingscale;

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

                $moodleobj->setPage('configureGradingscale');

                $moodleobj->outputPageHeader();

                // Displays the form.
                $mform->display();

                $moodleobj->outputFooter();
            }

        } else { // If user hasnt entered correct password for this session: show enterPasswordPage.
            redirect ($exammanagementinstanceobj->getExammanagementUrl('checkpassword', $exammanagementinstanceobj->getCm()->id), null, null, null);
        }
    }
} else {
    $moodleobj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
