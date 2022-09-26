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
 * Allows teacher to convert participants of mod_exammanagement to moodle group.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\converttogroup_form;
use stdclass;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$MoodleObj = Moodle::getInstance($id, $e);
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();
$UserObj = User::getInstance($id, $e, $ExammanagementInstanceObj->getCm()->instance);

if ($MoodleObj->checkCapability('mod/exammanagement:viewinstance')) {

	if ($ExammanagementInstanceObj->isExamDataDeleted()) {
        $MoodleObj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
	} else if (!$UserObj->getParticipantsCount()) {
		$MoodleObj->redirectToOverviewPage('forexam', get_string('no_participants_added', 'mod_exammanagement'), 'error');
	} else {

		if (!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))) { // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

			$moodleParticipants = $UserObj->getExamParticipants(array('mode'=>'moodle'), array('matrnr', 'profile', 'groups'));

			$noneMoodleParticipants = $UserObj->getExamParticipants(array('mode'=>'nonmoodle'), array('matrnr'));

			# Instantiate form #
			$mform = new converttogroup_form(null, array('id'=>$id, 'e'=>$e, 'moodleParticipants' => $moodleParticipants, 'noneMoodleParticipants' => $noneMoodleParticipants));

			// Form processing and displaying is done here
			if ($mform->is_cancelled()) {
				// Handle form cancel operation, if cancel button is present on form

				redirect ($ExammanagementInstanceObj->getExammanagementUrl('viewParticipants', $ExammanagementInstanceObj->getCm()->id), get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

			} else if ($fromform = $mform->get_data()) {
			// In this case you process validated data. $mform->get_data() returns data posted in form.

				$participantsIds = $UserObj->filterCheckedParticipants($fromform);

				$countSuccess = 0;
				$countFailed = 0;
				$newgroupid = false;

				if (!empty($participantsIds)) {
					require_once($CFG->dirroot.'/group/lib.php');

					if ($fromform->groups === 'new_group') {

						$data = new stdClass();
						$data->courseid = $ExammanagementInstanceObj->getCourse()->id;
						$data->name = $fromform->groupname;
						$data->description = $fromform->groupdescription;
						$data->descriptionformat = FORMAT_HTML;

						$newgroupid = groups_create_group($data);

						foreach ($participantsIds as $moodleuserid) {
							if (groups_add_member($newgroupid, $moodleuserid)) {
							$countSuccess +=1;
							} else {
								$countFailed +=1;
							}
						}
					} else {
						foreach ($participantsIds as $moodleuserid) {
							if (groups_add_member($fromform->groups, $moodleuserid)) {
							$countSuccess +=1;
							} else {
								$countFailed +=1;
							}
						}
					}

				}

				# redirect #
				if ((($fromform->groups === 'new_group' && $newgroupid) || $fromform->groups !== 'new_group') && $countFailed ===0 && $countSuccess>0) {
					redirect ($ExammanagementInstanceObj->getExammanagementUrl('viewParticipants', $id), get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
				} else {
					redirect ($ExammanagementInstanceObj->getExammanagementUrl('viewParticipants', $id), get_string('alteration_failed', 'mod_exammanagement'), null, 'error');
				}
			} else {
			// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
			// or on the first display of the form.

				# set data if checkboxes should be checked (setDefault in the form is much more time consuming for big amount of participants) #
				$default_values = array('id'=>$id);

				if (isset($moodleParticipants)) {
					foreach ($moodleParticipants as $participant) {
						$default_values['participants['.$participant->moodleuserid.']'] = true;
					}
				}

				//Set default data (if any)
				$mform->set_data($default_values);

				$MoodleObj->setPage('convertToGroup');
				$MoodleObj->outputPageHeader();

				$mform->display();

				$MoodleObj->outputFooter();

			}

		} else { // if user hasnt entered correct password for this session: show enterPasswordPage
			redirect ($ExammanagementInstanceObj->getExammanagementUrl('checkpassword', $ExammanagementInstanceObj->getCm()->id), null, null, null);
		}
	}
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}