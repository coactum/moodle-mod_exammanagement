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
 * Allows teacher to add participants from text file to mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\addparticipants_form;
use mod_exammanagement\ldap\ldapManager;
use stdclass;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or.
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$dtp  = optional_param('dtp', 0, PARAM_INT);

$moodleobj = Moodle::getInstance($id, $e);
$exammanagementinstanceobj = exammanagementInstance::getInstance($id, $e);
$ldapmanagerobj = ldapManager::getInstance();
$moodledbobj = MoodleDB::getInstance();
$userobj = User::getInstance($id, $e, $exammanagementinstanceobj->getCm()->instance);

if ($moodleobj->checkCapability('mod/exammanagement:viewinstance')) {

    if ($exammanagementinstanceobj->isExamDataDeleted()) {
        $moodleobj->redirectToOverviewPage('beforeexam', get_string('err_examdata_deleted', 'mod_exammanagement'), 'error');
    } else if (!$ldapmanagerobj->isLDAPenabled()) {
        $moodleobj->redirectToOverviewPage('beforeexam', get_string('importmatrnrnotpossible', 'mod_exammanagement') . ' ' . get_string('ldapnotenabled', 'mod_exammanagement'), 'error');
    } else if (!$ldapmanagerobj->isLDAPconfigured()) {
        $moodleobj->redirectToOverviewPage('beforeexam', get_string('importmatrnrnotpossible', 'mod_exammanagement') . ' ' . get_string('ldapnotconfigured', 'mod_exammanagement'), 'error');
    } else {

        // If no password for moduleinstance is set or if user already entered correct password in this session: show main page.
        if (!isset($exammanagementinstanceobj->moduleinstance->password) || (isset($exammanagementinstanceobj->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))) {

            $moodleobj->setPage('addParticipants');
            $moodleobj->outputPageHeader();

            if ($dtp) {
                $userobj->deleteTempParticipants();
            }

            // Define participants for form.
            $tempparticipants = $moodledbobj->getRecordsFromDB('exammanagement_temp_part', array('exammanagement' => $exammanagementinstanceobj->getCm()->instance)); // Get all participants that are already readed in and saved as temnp participants.

            if ($tempparticipants) {

                $allparticipants = array(); // Will contain all participants ordered in their respective sub array.

                $moodleusers = array(); // Will contain all moodle users for further classification.
                $nonmoodleusers = array(); // Will contain all nonmoodle users for further classification.

                $badmatriculationnumbers = array(); // Will contain all invalid or doubled identifier.
                $oddparticipants = array(); // Will contain all users that are no course members or have no moodle account but can still be added as exam participants.
                $deletedparticipants = array(); // Will contain all users that are already read in from file with same header but not in this file and should therefore be deleted.
                $existingparticipants = array(); // Will contain all user that are already exam participants.
                $newmoodleparticipants = array(); // Will contain all valid moodle participants that can be added.

                $tempids = array(); // Will contain moodleids and logins of all valid temp participants for checking for deleted users.
                $allpotentialidentifiers = array(); // Will contain all potential identifiers from file to check for double entries.

                $courseparticipantsids = $userobj->getCourseParticipantsIDs(); // contains moodle user ids of all course participants.

                // Sort out bad matriculation numbers to badmatrnr array.
                foreach ($tempparticipants as $key => $participant) { // Filter invalid / bad matrnr.

                    if (!$userobj->checkIfValidMatrNr($participant->identifier)) {
                        $tempuserobj = new stdclass;
                        $tempuserobj->line = $participant->line;
                        $tempuserobj->matrnr = $participant->identifier;
                        $tempuserobj->state = 'state_badmatrnr';

                        array_push($badmatriculationnumbers, $tempuserobj);
                        unset($tempparticipants[$key]);
                    } else if (in_array($participant->identifier, $allpotentialidentifiers)) {
                        $tempuserobj = new stdclass;
                        $tempuserobj->line = $participant->line;
                        $tempuserobj->matrnr = $participant->identifier;
                        $tempuserobj->state = 'state_doubled';

                        array_push($badmatriculationnumbers, $tempuserobj);
                        unset($tempparticipants[$key]);
                    } else {
                        array_push($allpotentialidentifiers, $participant->identifier);
                    }
                }

                // Construct arrays with all users (moodle and nonmoodle) with all needed data.

                // Check if headers are already saved and find new headerid.
                $tempfileheaders = json_decode($exammanagementinstanceobj->moduleinstance->tempimportfileheader);
                $savedfileheadersarr = json_decode($exammanagementinstanceobj->moduleinstance->importfileheaders);
                $converttempheaders = false;

                if ($savedfileheadersarr) {
                    $savedheaderscount = count($savedfileheadersarr);
                } else {
                    $savedheaderscount = 0;
                }

                if ($savedfileheadersarr && $tempfileheaders) { // If headers are already saved.

                    foreach ($tempfileheaders as $tempheaderkey => $tempfileheader) {

                        $saved = false;

                        foreach ($savedfileheadersarr as $savedheaderkey => $header) { // If new header is already saved.

                            if ($tempfileheader == $header) {
                                $saved = $savedheaderkey;
                            }

                            if (!$saved) {
                                $converttempheaders[$tempheaderkey + 1] = $saved + 1;
                            }
                        }
                    }
                }

                // Temp participants from stored in db that should get ldap attributes.
                foreach ($tempparticipants as $key => $participant) { // Construct helper arrays needed for ldap method.
                    $allmatriculationnumbers[$key] = $participant->identifier;
                    $alllines[$key] = $participant->line;
                }

                $users = $ldapmanagerobj->getLDAPAttributesForMatrNrs($allmatriculationnumbers, 'usernames_and_matriculationnumbers', $alllines); // Get data for all remaining matriculation numbers from ldap.

                if ($users) {
                    ksort($users);

                    // Users from ldap.
                    foreach ($users as $line => $login) {
                        $moodleuserid = $moodledbobj->getFieldFromDB('user', 'id', array('username' => $login['login'])); // Get moodle id for user.

                        $temp = array_filter($tempparticipants, function($tempparticipant) use ($login) {
                            return $tempparticipant->identifier == $login['matrnr'];
                        });

                        $headerid = reset($temp)->headerid;

                        if ($converttempheaders && array_key_exists($headerid, $converttempheaders)) {
                                  $headerid = $converttempheaders[$headerid];
                        } else {
                               $headerid += $savedheaderscount;
                        }

                        if ($moodleuserid) { // If moodle user.
                            $moodleusers[$line] = array('matrnr' => $login['matrnr'], 'login' => $login['login'], 'moodleuserid' => $moodleuserid, 'headerid' => $headerid); // Add to array.
                        } else { // If not a moodle user.
                            $nonmoodleusers[$line] = array('matrnr' => $login['matrnr'], 'login' => $login['login'], 'moodleuserid' => false, 'headerid' => $headerid); // Add to array.
                        }
                    }
                }

                // Check moodle users and classify them to array according to case.
                foreach ($moodleusers as $line => $data) {

                    if (isset($data['moodleuserid']) && $data['moodleuserid']) {
                        $tempuserobj = new stdclass;
                        $tempuserobj->line = $line;
                        $tempuserobj->moodleuserid = $data['moodleuserid'];
                        $tempuserobj->matrnr = $data['matrnr'];
                        $tempuserobj->login = $data['login'];
                        $tempuserobj->headerid = $data['headerid'];

                        if ($userobj->checkIfAlreadyParticipant($data['moodleuserid'])) {    // If user is already saved for instance.
                            if ($courseparticipantsids && !in_array($data['moodleuserid'], $courseparticipantsids)) {
                                $tempuserobj->state = 'state_existingmatrnrnocourse';
                            } else {
                                $tempuserobj->state = 'state_existingmatrnr';
                            }
                            array_push($existingparticipants, $tempuserobj);
                            array_push($tempids, $data['moodleuserid']);    // For finding deleted users.
                        } else if (!$courseparticipantsids || !in_array($data['moodleuserid'], $courseparticipantsids)) {     // If user is not in course.
                            $tempuserobj->state = 'state_no_courseparticipant';
                            array_push($oddparticipants, $tempuserobj);
                            array_push($tempids, $data['moodleuserid']);    // For finding deleted users.
                        } else {    // If user is a valid new moodle participant.
                            array_push($newmoodleparticipants, $tempuserobj);
                            array_push($tempids, $data['moodleuserid']);    // For finding deleted users.
                        }

                        foreach ($tempparticipants as $key => $participant) {    // Unset user from original tempuser array.
                            if ($participant->identifier == $data['matrnr']) {
                                unset($tempparticipants[$key]);
                                break;
                            }
                        }
                    }
                }

                // Check nonmoodle users and classify them to array according to case.
                foreach ($nonmoodleusers as $line => $data) {

                    if (isset($data['login']) && $data['login']) {
                        $tempuserobj = new stdclass;
                        $tempuserobj->line = $line;
                        $tempuserobj->moodleuserid = false;
                        $tempuserobj->matrnr = $data['matrnr'];
                        $tempuserobj->login = $data['login'];
                        $tempuserobj->headerid = $data['headerid'];

                        if ($userobj->checkIfAlreadyParticipant(false, $data['login'])) { // If user is already saved as participant.
                               $existingparticipant = $userobj->getExamParticipantObj(false, $data['login']);
                               $tempuserobj->firstname = $existingparticipant->firstname;
                               $tempuserobj->lastname = $existingparticipant->lastname;
                               $tempuserobj->state = 'state_existingmatrnrnomoodle';
                               array_push($existingparticipants, $tempuserobj);
                               array_push($tempids, $data['login']);    // For finding deleted users.
                        } else {    // If user is a valid new nonmoodle participant.
                            $tempuserobj->state = 'state_nonmoodle';
                            array_push($oddparticipants, $tempuserobj);
                            array_push($tempids, $data['login']);    // For finding deleted users.
                        }

                        foreach ($tempparticipants as $key => $participant) {    // Unset user from original tempuser array.
                            if ($participant->identifier == $data['matrnr']) {
                                unset($tempparticipants[$key]);
                                break;
                            }
                        }
                    }
                }

                // Push all remaining matriculation numbers that could not be resolved by ldap into the bad matriculationnumbers array.
                foreach ($tempparticipants as $key => $participant) {
                    $tempuserobj = new stdclass;
                    $tempuserobj->line = $participant->line;
                    $tempuserobj->matrnr = $participant->identifier;
                    $tempuserobj->state = 'state_badmatrnr';

                    array_push($badmatriculationnumbers, $tempuserobj);
                    unset($tempparticipants[$key]);
                }

                // Check if users should be deleted.
                $oldparticipants = array();

                foreach ($tempfileheaders as $tempfileheaderkey => $tempfileheader) {

                    $tempfileheaderkeyincreased = $tempfileheaderkey + 1;

                    $oldparticipantstemp = $userobj->getExamParticipants(array('mode' => 'header', 'id' => $tempfileheaderkeyincreased), array('matrnr'));

                    if (!empty($oldparticipantstemp)) {
                        $oldparticipants = $oldparticipants + $oldparticipantstemp;
                    }
                }

                if ($oldparticipants) {

                    foreach ($oldparticipants as $key => $participant) {
                        if ($participant->moodleuserid && !in_array($participant->moodleuserid, $tempids)) { // Moodle participant that is not readed in again and should therefore be deleted.

                               $deletedmatrnrobj = new stdclass;
                               $deletedmatrnrobj->moodleuserid = $participant->moodleuserid;
                               $deletedmatrnrobj->matrnr = $participant->matrnr;
                               $deletedmatrnrobj->firstname = false;
                               $deletedmatrnrobj->lastname = false;
                               $deletedmatrnrobj->line = '';

                               array_push($deletedparticipants, $deletedmatrnrobj);

                        } else if ($participant->moodleuserid === null && $participant->login && !in_array($participant->login, $tempids)) {  // Moodle participant that is not readed in again and should therefore be deleted.
                            $deletedmatrnrobj = new stdclass;
                            $deletedmatrnrobj->moodleuserid = false;
                            $deletedmatrnrobj->matrnr = $participant->matrnr;
                            $deletedmatrnrobj->firstname = $participant->firstname;
                            $deletedmatrnrobj->lastname = $participant->lastname;
                            $deletedmatrnrobj->line = '';

                            array_push($deletedparticipants, $deletedmatrnrobj);
                        }
                    }
                }

                $allparticipants['badMatriculationNumbers'] = $badmatriculationnumbers;
                $allparticipants['deletedParticipants'] = $deletedparticipants;
                $allparticipants['oddParticipants'] = $oddparticipants;
                $allparticipants['existingParticipants'] = $existingparticipants;
                $allparticipants['newMoodleParticipants'] = $newmoodleparticipants;

                // Instantiate form.
                $mform = new addparticipants_form(null, array('id' => $id, 'e' => $e, 'allParticipants' => $allparticipants));

            } else {
                // Instantiate form.
                $mform = new addparticipants_form(null, array('id' => $id, 'e' => $e));
            }

            // Form processing and displaying is done here.
            if ($mform->is_cancelled()) {
                // Handle form cancel operation, if cancel button is present on form.

                redirect ($exammanagementinstanceobj->getExammanagementUrl('viewParticipants', $exammanagementinstanceobj->getCm()->id), get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');

            } else if ($fromform = $mform->get_data()) {
                // In this case you process validated data. $mform->get_data() returns data posted in form.

                $draftid = file_get_submitted_draft_itemid('participantslists');

                if (!$draftid) { // If no import file and exam participants should be saved in db.

                    // Get checked userids from form.
                    $participantsidsarr = $userobj->filterCheckedParticipants($fromform);
                    $nonemoodleparticipantsmatrnrarr = array();
                    $deletedparticipantsidsarr = $userobj->filterCheckedDeletedParticipants($fromform);
                    $tempparticipants = $moodledbobj->getRecordsFromDB('exammanagement_temp_part', array('exammanagement' => $exammanagementinstanceobj->getCm()->instance)); // Get all participants that are already readed in and saved as temnp participants.

                    if ($participantsidsarr != false || $deletedparticipantsidsarr != false) {

                        // Get headers and temp file header.
                        $tempfileheaders = json_decode($exammanagementinstanceobj->moduleinstance->tempimportfileheader);
                        $savedfileheadersarr = json_decode($exammanagementinstanceobj->moduleinstance->importfileheaders);

                        if (!$savedfileheadersarr && $tempfileheaders) { // If there are no saved headers by now.
                            // Save new file header.
                            $savedfileheadersarr = $tempfileheaders;
                        } else if ($savedfileheadersarr && $tempfileheaders) {
                            foreach ($tempfileheaders as $tempheaderkey => $tempfileheader) {

                                $saved = false;

                                foreach ($savedfileheadersarr as $savedheaderkey => $header) { // If new header is already saved.

                                    if ($tempfileheader == $header) {
                                        $saved = true;
                                    }
                                }

                                if (!$saved) {
                                    array_push($savedfileheadersarr, $tempfileheader);
                                }
                            }
                        }

                        $exammanagementinstanceobj->moduleinstance->importfileheaders = json_encode($savedfileheadersarr);

                        // Add new participants.
                        if ($participantsidsarr) {
                            $userobjarr = array();

                            foreach ($participantsidsarr as $key => $tempidentifier) {

                                $tempheaderid = explode('-', $tempidentifier)[1];

                                $identifier = explode('_', explode('-', $tempidentifier)[0]);

                                if ($identifier[0] == 'mid') { // If participant is moodle user.
                                    $user = new stdClass();
                                    $user->exammanagement = $exammanagementinstanceobj->getCm()->instance;
                                    $user->courseid = $exammanagementinstanceobj->getCourse()->id;
                                    $user->categoryid = $exammanagementinstanceobj->moduleinstance->categoryid;
                                    $user->moodleuserid = $identifier[1];
                                    $user->login = null;
                                    $user->firstname = null;
                                    $user->lastname = null;
                                    $user->email = null;
                                    $user->headerid = $tempheaderid;

                                    $user->plugininstanceid = 0; // For deprecated old version db version, should be removed.

                                    array_push($userobjarr, $user);

                                    unset($participantsidsarr[$key]);

                                } else {
                                    array_push($nonemoodleparticipantsmatrnrarr, $identifier[1]);
                                }
                            }

                            if (!empty($nonemoodleparticipantsmatrnrarr)) {
                                $nonemoodleparticipantsarr = $ldapmanagerobj->getLDAPAttributesForMatrNrs($nonemoodleparticipantsmatrnrarr, 'all_attributes');

                                foreach ($participantsidsarr as $key => $identifier) { // Now only contains participants that have no moodle account.

                                    $tempheaderid = explode('-', $identifier)[1];

                                    $matrnr = explode('_', explode('-', $identifier)[0])[1];

                                    $user = new stdClass();
                                    $user->exammanagement = $exammanagementinstanceobj->getCm()->instance;
                                    $user->courseid = $exammanagementinstanceobj->getCourse()->id;
                                    $user->categoryid = $exammanagementinstanceobj->moduleinstance->categoryid;
                                    $user->moodleuserid = null;

                                    $login = $nonemoodleparticipantsarr[$matrnr]['login'];
                                    if ($login) {
                                        $user->login = $login;
                                    } else {
                                        $user->login = null;
                                    }

                                    $firstname = $nonemoodleparticipantsarr[$matrnr]['firstname'];
                                    if ($firstname) {
                                        $user->firstname = $firstname;
                                    } else {
                                        $user->firstname = null;
                                    }

                                    $lastname = $nonemoodleparticipantsarr[$matrnr]['lastname'];
                                    if ($lastname) {
                                        $user->lastname = $lastname;
                                    } else {
                                        $user->lastname = null;
                                    }

                                    $email = $nonemoodleparticipantsarr[$matrnr]['email'];
                                    if ($email) {
                                        $user->email = $email;
                                    } else {
                                        $user->email = null;
                                    }

                                    $user->headerid = $tempheaderid;

                                    $user->plugininstanceid = 0; // For deprecated old version db version, should be removed.

                                    array_push($userobjarr, $user);
                                }
                            }

                            // Insert records of new participants.
                            $moodledbobj->InsertBulkRecordsInDB('exammanagement_participants', $userobjarr);

                        }

                        // Delete participants that should be deleted.
                        if ($deletedparticipantsidsarr) {
                            foreach ($deletedparticipantsidsarr as $identifier) {
                                $temp = explode('_', $identifier);

                                if ($temp[0] == 'mid') { // Delete moodle participant.
                                    $userobj->deleteParticipant($temp[1], false);
                                } else { // Delete participant without moodle account.

                                    $userlogin = false;

                                    $userlogin = $ldapmanagerobj->getLoginForMatrNr($temp[1], 'importmatrnrnotpossible');

                                    if ($userlogin) {
                                        $userobj->deleteParticipant(false, $userlogin);
                                    }
                                }
                            }
                        }

                        // Delete temp file header and update saved file headers.
                        $exammanagementinstanceobj->moduleinstance->tempimportfileheader = null;

                        $moodledbobj->UpdateRecordInDB("exammanagement", $exammanagementinstanceobj->moduleinstance);

                        // Delete temp participants.
                        $userobj->deleteTempParticipants();

                        // Redirect.
                        redirect ($exammanagementinstanceobj->getExammanagementUrl('viewParticipants', $id), get_string('operation_successfull', 'mod_exammanagement'), null, 'success');

                    } else {
                        redirect ($exammanagementinstanceobj->getExammanagementUrl('viewParticipants', $id), get_string('alteration_failed', 'mod_exammanagement'), null, 'error');
                    }

                } else if ($draftid) { // If participants are readed in from import file and should be saved as temporary participants.

                    $userobj->deleteTempParticipants();

                    $fs = get_file_storage();
                    $context = \context_user::instance($USER->id);
                    $files = $fs->get_area_files($context->id, 'user', 'draft', $draftid, 'id DESC', false);

                    $tempfileheaders = array();
                    $usersobjarr = array();

                    $filecounter = 1;

                    foreach ($files as $file) {

                        // Get matriculation numbers from text file as an array.
                        $filecontentarr = explode(PHP_EOL, $file->get_content()); // Separate lines.

                        if ($filecontentarr) {
                               $fileheader = $filecontentarr[0]."\r\n".$filecontentarr[1];

                               unset($filecontentarr[0]);
                               unset($filecontentarr[1]);

                            foreach ($filecontentarr as $key => $row) {
                                $potentialmatriculationnumbersarr = explode("	", $row); // From 2nd line: get all potential numbers.

                                if ($potentialmatriculationnumbersarr) {
                                    foreach ($potentialmatriculationnumbersarr as $key2 => $pmatrnr) { // Create temp user obj.

                                        $identifier = str_replace('"', '', $pmatrnr);
                                        if (preg_match('/\\d/', $identifier) !== 0 && ctype_alnum($identifier) && strlen($identifier) <= 20) { // If identifier contains numbers and only alpha numerical signs and is not to long.
                                            $tempuserobj = new stdclass;
                                            $tempuserobj->exammanagement = $exammanagementinstanceobj->getCm()->instance;
                                            $tempuserobj->courseid = $exammanagementinstanceobj->getCourse()->id;
                                            $tempuserobj->categoryid = $exammanagementinstanceobj->moduleinstance->categoryid;
                                            $tempuserobj->identifier = $identifier;
                                            $tempuserobj->line = $key + 1 .'(' . $filecounter.')';
                                            $tempuserobj->plugininstanceid = 0; // For deprecated old version db version, should be removed.
                                            $tempuserobj->headerid = $filecounter;

                                            array_push($usersobjarr, $tempuserobj);

                                        }
                                    }
                                }
                            }

                            $fileheader = strip_tags($fileheader);

                            if (mb_detect_encoding($fileheader, mb_detect_order(), true) !== "UTF-8") {
                                $fileheader = utf8_encode($fileheader);
                            }

                            array_push($tempfileheaders, $fileheader);

                        }

                        $filecounter += 1;
                    }

                    $exammanagementinstanceobj->moduleinstance->tempimportfileheader = json_encode($tempfileheaders);

                    $moodledbobj->UpdateRecordInDB("exammanagement", $exammanagementinstanceobj->moduleinstance);

                    $moodledbobj->InsertBulkRecordsInDB('exammanagement_temp_part', $usersobjarr);

                    redirect ($exammanagementinstanceobj->getExammanagementUrl('addParticipants', $id), get_string('operation_successfull', 'mod_exammanagement') , null, 'success');
                }

            } else {
                // This branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
                // or on the first display of the form.

                // Set data if checkboxes should be checked (setDefault in the form is much more time consuming for big amount of participants).
                $defaultvalues = array('id' => $id);

                if (isset($newmoodleparticipants)) {
                    foreach ($newmoodleparticipants as $participant) {
                        $defaultvalues['participants[mid_'.$participant->moodleuserid.'-'.$participant->headerid.']'] = true;
                    }
                }

                if (isset($deletedparticipants)) {
                    foreach ($deletedparticipants as $participant) {
                        if ($participant->moodleuserid) {
                               $defaultvalues['deletedparticipants[mid_'.$participant->moodleuserid.']'] = true;
                        } else if ($participant->matrnr) {
                            $defaultvalues['deletedparticipants[matrnr_'.$participant->matrnr.']'] = true;
                        }
                    }
                }

                // Set default data (if any).
                $mform->set_data($defaultvalues);

                $mform->display();

                $moodleobj->outputFooter();
            }


        } else { // If user has not entered correct password for this session: show enterPasswordPage.
            redirect ($exammanagementinstanceobj->getExammanagementUrl('checkpassword', $exammanagementinstanceobj->getCm()->id), null, null, null);
        }
    }
} else {
    $moodleobj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
