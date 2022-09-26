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
 * Prints participantsOverview form for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\participantsoverview_form;
use mod_exammanagement\ldap\ldapManager;
use stdClass;
use core\output\notification;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$pne  = optional_param('pne', 1, PARAM_INT);
$bpne  = optional_param('bpne', 1, PARAM_INT);

$epm = optional_param('epm', 0, PARAM_INT);

// Active page.
$pagenr  = optional_param('page', 1, PARAM_INT);

$moodledbobj = MoodleDB::getInstance();
$moodleobj = Moodle::getInstance($id, $e);
$exammanagementinstanceobj = exammanagementInstance::getInstance($id, $e);
$userobj = User::getInstance($id, $e, $exammanagementinstanceobj->getCm()->instance);
$ldapmanagerobj = ldapManager::getInstance();

if ($moodleobj->checkCapability('mod/exammanagement:viewinstance')) {

    if ($exammanagementinstanceobj->isExamDataDeleted()) {
        $moodleobj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
    } else {
        if (!isset($exammanagementinstanceobj->moduleinstance->password) || (isset($exammanagementinstanceobj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))) { // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

            if (!$userobj->getParticipantsCount()) {
                $moodleobj->redirectToOverviewPage('beforeexam', get_string('no_participants_added', 'mod_exammanagement'), 'error');
            }

            // Instantiate Form.
            if ($epm) {
                $mform = new participantsoverview_form(null, array('id' => $id, 'e' => $e, 'epm' => $epm, 'pagenr' => $pagenr));
            } else {
                $mform = new participantsoverview_form(null, array('id' => $id, 'e' => $e, 'pagenr' => $pagenr));
            }

            // Form processing and displaying is done here.
            if ($mform->is_cancelled()) {
                // Handle form cancel operation, if cancel button is present on form
                redirect ($exammanagementinstanceobj->getExammanagementUrl('participantsOverview', $exammanagementinstanceobj->getCm()->id), get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

            } else if ($fromform = $mform->get_data()) {
                // In this case you process validated data. $mform->get_data() returns data posted in form.

                $participants = $userobj->getExamParticipants(array('mode' => 'all'), array());
                $updatedcount = 0;

                foreach ($participants as $participant) {
                    if (isset($fromform->state[$participant->id]) || isset($fromform->bonuspoints[$participant->id]) || isset($fromform->bonussteps[$participant->id]) || isset($fromform->bonuspoints_entered[$participant->id])) {
                        if ($exammanagementinstanceobj->moduleinstance->misc === null) {
                            if (isset($fromform->state[$participant->id]) && $fromform->state[$participant->id] !== 'not_set') {
                                switch ($fromform->state[$participant->id]) {
                                    case 'normal':
                                        $examstate = new stdClass;
                                        $examstate->nt = "0";
                                        $examstate->fa = "0";
                                        $examstate->ill = "0";
                                        break;
                                    case 'nt':
                                        $examstate = new stdClass;
                                        $examstate->nt = "1";
                                        $examstate->fa = "0";
                                        $examstate->ill = "0";
                                        break;
                                    case 'fa':
                                        $examstate = new stdClass;
                                        $examstate->nt = "0";
                                        $examstate->fa = "1";
                                        $examstate->ill = "0";
                                        break;
                                    case 'ill':
                                        $examstate = new stdClass;
                                        $examstate->nt = "0";
                                        $examstate->fa = "0";
                                        $examstate->ill = "1";
                                        break;
                                    default:
                                        break;
                                }

                                $participant->examstate = json_encode($examstate);

                                if ($fromform->points[$participant->id]) { // if participants points were not empty
                                    $participant->exampoints = json_encode($fromform->points[$participant->id]);
                                }

                                $participant->timeresultsentered = time();

                            } else {
                                $participant->examstate = null;
                                $participant->exampoints = null;
                            }

                            if ($fromform->bonussteps[$participant->id] !== '-') {
                                $participant->bonussteps = $fromform->bonussteps[$participant->id];
                                $participant->bonuspoints = null;
                            } else if ($fromform->bonuspoints_entered[$participant->id] === 1 && $fromform->bonuspoints[$participant->id] !== 0) {
                                $participant->bonussteps = null;
                                $participant->bonuspoints = $fromform->bonuspoints[$participant->id];
                            } else {
                                $participant->bonussteps = null;
                                $participant->bonuspoints = null;
                            }

                        } else {
                            if ($fromform->bonuspoints_entered[$participant->id] === 1 && isset($fromform->bonuspoints[$participant->id])) {
                                $participant->timeresultsentered = time();
                                $participant->bonuspoints = $fromform->bonuspoints[$participant->id];
                            }
                        }

                        if (isset($participant->moodleuserid)) {
                            $participant->login = null;
                            $participant->firstname = null;
                            $participant->lastname = null;
                        }

                        unset($participant->matrnr);

                        if ($moodledbobj->UpdateRecordInDB('exammanagement_participants', $participant)) {
                            $updatedcount += 1;
                        }
                    }
                }

                if ($updatedcount) {
                    $moodledbobj->UpdateRecordInDB("exammanagement", $exammanagementinstanceobj->moduleinstance);
                    redirect ($exammanagementinstanceobj->getExammanagementUrl('participantsOverview', $id), get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
                } else {
                    redirect ($exammanagementinstanceobj->getExammanagementUrl('participantsOverview', $id), get_string('alteration_failed', 'mod_exammanagement'), null, notification::NOTIFY_ERROR);
                }

            } else {
                // This branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
                // or on the first display of the form.

                $mform->set_data(array('id' => $id));

                $moodleobj->setPage('participantsOverview');
                $moodleobj->outputPageHeader();

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
