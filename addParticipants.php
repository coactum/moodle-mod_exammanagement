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
 * Allows teachers to add participants from text file to an exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_exammanagement\ldap\ldapmanager;
use mod_exammanagement\local\helper;

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// Course_module ID, or ...
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module.
$e = optional_param('e', 0, PARAM_INT);

// If temporary participants should be deleted.
$dtp = optional_param('dtp', 0, PARAM_INT);

// Set the basic variables $course, $cm and $moduleinstance.
if ($id) {
    [$course, $cm] = get_course_and_cm_from_cmid($id, 'exammanagement');
    $moduleinstance = $DB->get_record('exammanagement', ['id' => $cm->instance], '*', MUST_EXIST);
} else {
    throw new moodle_exception('missingparameter');
}

// Check if course module, course and course section exist.
if (!$cm) {
    throw new moodle_exception(get_string('incorrectmodule', 'exammanagement'));
} else if (!$course) {
    throw new moodle_exception(get_string('incorrectcourseid', 'exammanagement'));
} else if (!$coursesections = $DB->get_record("course_sections", ["id" => $cm->section])) {
    throw new moodle_exception(get_string('incorrectmodule', 'exammanagement'));
}

// Check login and capability.
require_login($course, true, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/exammanagement:viewinstance', $context);

// Get global and construct helper objects.
global $OUTPUT, $PAGE;

$ldapmanager = ldapmanager::getinstance();

// If user has not entered the correct password: redirect to check password page.
if (isset($moduleinstance->password) &&
    (!isset($SESSION->loggedInExamOrganizationId) || $SESSION->loggedInExamOrganizationId !== $id)) {

    redirect(new moodle_url('/mod/exammanagement/checkpassword.php', ['id' => $id]), null, null, null);
}

// Check if requirements are met.
if (helper::isexamdatadeleted($moduleinstance)) {
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('err_examdata_deleted', 'mod_exammanagement'), null, 'error');
} else if (!$ldapmanager->isldapenabled()) {
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('importmatrnrnotpossible', 'mod_exammanagement') . ' ' .
        get_string('ldapnotenabled', 'mod_exammanagement'), null, 'error');
} else if (!$ldapmanager->isldapconfigured()) {
    redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
        get_string('importmatrnrnotpossible', 'mod_exammanagement') . ' ' .
        get_string('ldapnotconfigured', 'mod_exammanagement'), null, 'error');
}

if ($dtp) {
    helper::deletetempparticipants($moduleinstance);
}

// Get all participants that are already readed in and saved as temp participants.
$tempparticipants = $DB->get_records('exammanagement_temp_part', ['exammanagement' => $moduleinstance->id]);

if ($tempparticipants) {

    $allparticipants = []; // Will contain all participants ordered in their respective sub array.

    $moodleusers = []; // Will contain all moodle users for further classification.
    $nonmoodleusers = []; // Will contain all nonmoodle users for further classification.

    $badmatriculationnumbers = []; // Will contain all invalid or doubled identifier.
    // Will contain all users that are no course members or have no moodle account but can still be added as exam participants.
    $oddparticipants = [];
    // Will contain users that are already read in from file with same header but not in this file and should therefore be deleted.
    $deletedparticipants = [];
    $existingparticipants = []; // Will contain all user that are already exam participants.
    $newmoodleparticipants = []; // Will contain all valid moodle participants that can be added.

    $tempids = []; // Will contain moodleids and logins of all valid temp participants for checking for deleted users.
    $allpotentialidentifiers = []; // Will contain all potential identifiers from file to check for double entries.

    $courseparticipantsids = helper::getcourseparticipantsids($id); // Contains moodle user ids of all course participants.

    // Sort out bad matriculation numbers to badmatrnr array.
    foreach ($tempparticipants as $key => $participant) { // Filter invalid / bad matrnr.

        if (!helper::checkifvalidmatrnr($participant->identifier)) {
            $tempuser = new stdclass;
            $tempuser->line = $participant->line;
            $tempuser->matrnr = $participant->identifier;
            $tempuser->state = 'state_badmatrnr';

            array_push($badmatriculationnumbers, $tempuser);
            unset($tempparticipants[$key]);
        } else if (in_array($participant->identifier, $allpotentialidentifiers)) {
            $tempuser = new stdclass;
            $tempuser->line = $participant->line;
            $tempuser->matrnr = $participant->identifier;
            $tempuser->state = 'state_doubled';

            array_push($badmatriculationnumbers, $tempuser);
            unset($tempparticipants[$key]);
        } else {
            array_push($allpotentialidentifiers, $participant->identifier);
        }
    }

    // Construct arrays with all users (moodle and nonmoodle) with all needed data.

    // Check if headers are already saved and find new headerid.
    $tempfileheaders = json_decode($moduleinstance->tempimportfileheader);

    $savedfileheaders = json_decode($moduleinstance->importfileheaders ?? '');

    $converttempheaders = [];

    if (isset($savedfileheaders)) {
        $savedheaderscount = count($savedfileheaders);
    } else {
        $savedheaderscount = 0;
    }

    if (isset($savedfileheaders) && $tempfileheaders) { // If headers are already saved.

        foreach ($tempfileheaders as $tempheaderkey => $tempfileheader) {

            $saved = false;

            foreach ($savedfileheaders as $savedheaderkey => $header) { // If new header is already saved.

                if ($tempfileheader == $header) {
                    $saved = $savedheaderkey;
                }

                if (!$saved) {
                    $converttempheaders[$tempheaderkey + 1] = $saved + 1;
                }
            }
        }
    }

    // Temp participants that should get ldap attributes.
    foreach ($tempparticipants as $key => $participant) { // Construct helper arrays needed for ldap method.
        $allmatriculationnumbers[$key] = $participant->identifier;
        $alllines[$key] = $participant->line;
    }

    // Get data for all remaining matriculation numbers from ldap.
    $users = $ldapmanager->getldapattributesformatrnrs($allmatriculationnumbers, 'usernames_and_matriculationnumbers', $alllines);

    if ($users) {
        ksort($users);

        // Users from ldap.
        foreach ($users as $line => $login) {
            $moodleuserid = $DB->get_field('user', 'id', ['username' => $login['login']]); // Get moodle id for user.

            $temp = array_filter($tempparticipants, function($tempparticipant) use ($login) {
                return $tempparticipant->identifier == $login['matrnr'];
            });

            $headerid = reset($temp)->headerid;

            if (!empty($converttempheaders) && array_key_exists($headerid, $converttempheaders)) {
                        $headerid = $converttempheaders[$headerid];
            } else {
                   $headerid += $savedheaderscount;
            }

            if ($moodleuserid) { // If moodle user.
                $moodleusers[$line] = [
                    'matrnr' => $login['matrnr'],
                    'login' => $login['login'],
                    'moodleuserid' => $moodleuserid,
                    'headerid' => $headerid,
                ];
            } else { // If not a moodle user.
                $nonmoodleusers[$line] = [
                    'matrnr' => $login['matrnr'],
                    'login' => $login['login'],
                    'moodleuserid' => false,
                    'headerid' => $headerid,
                ];
            }
        }
    }

    // Check moodle users and classify them to array according to case.
    foreach ($moodleusers as $line => $data) {

        if (isset($data['moodleuserid']) && $data['moodleuserid']) {
            $tempuser = new stdclass;
            $tempuser->line = $line;
            $tempuser->moodleuserid = $data['moodleuserid'];
            $tempuser->matrnr = $data['matrnr'];
            $tempuser->login = $data['login'];
            $tempuser->headerid = $data['headerid'];

            // If user is already saved for instance.
            if (helper::checkifalreadyparticipant($moduleinstance, $data['moodleuserid'])) {
                if ($courseparticipantsids && !in_array($data['moodleuserid'], $courseparticipantsids)) {
                    $tempuser->state = 'state_existingmatrnrnocourse';
                } else {
                    $tempuser->state = 'state_existingmatrnr';
                }
                array_push($existingparticipants, $tempuser);
                array_push($tempids, $data['moodleuserid']);    // For finding deleted users.
            } else if (!$courseparticipantsids || !in_array($data['moodleuserid'], $courseparticipantsids)) { // If not in course.
                $tempuser->state = 'state_no_courseparticipant';
                array_push($oddparticipants, $tempuser);
                array_push($tempids, $data['moodleuserid']);    // For finding deleted users.
            } else {    // If user is a valid new moodle participant.
                array_push($newmoodleparticipants, $tempuser);
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
            $tempuser = new stdclass;
            $tempuser->line = $line;
            $tempuser->moodleuserid = false;
            $tempuser->matrnr = $data['matrnr'];
            $tempuser->login = $data['login'];
            $tempuser->headerid = $data['headerid'];

            // If user is already saved as participant.
            if (helper::checkifalreadyparticipant($moduleinstance, false, $data['login'])) {
                $existingparticipant = helper::getexamparticipant(false, $data['login']);
                $tempuser->firstname = $existingparticipant->firstname;
                $tempuser->lastname = $existingparticipant->lastname;
                $tempuser->state = 'state_existingmatrnrnomoodle';
                array_push($existingparticipants, $tempuser);
                array_push($tempids, $data['login']);    // For finding deleted users.
            } else {    // If user is a valid new nonmoodle participant.
                $tempuser->state = 'state_nonmoodle';
                array_push($oddparticipants, $tempuser);
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
        $tempuser = new stdclass;
        $tempuser->line = $participant->line;
        $tempuser->matrnr = $participant->identifier;
        $tempuser->state = 'state_badmatrnr';

        array_push($badmatriculationnumbers, $tempuser);
        unset($tempparticipants[$key]);
    }

    // Check if users should be deleted.
    $oldparticipants = [];

    foreach ($tempfileheaders as $tempfileheaderkey => $tempfileheader) {

        $tempfileheaderkeyincreased = $tempfileheaderkey + 1;

        $oldparticipantstemp = helper::getexamparticipants($moduleinstance,
            ['mode' => 'header', 'id' => $tempfileheaderkeyincreased], ['matrnr']);

        if (!empty($oldparticipantstemp)) {
            $oldparticipants = $oldparticipants + $oldparticipantstemp;
        }
    }

    if ($oldparticipants) {

        foreach ($oldparticipants as $key => $participant) {

            // Moodle participant that is not readed in again and should therefore be deleted.
            if ($participant->moodleuserid && !in_array($participant->moodleuserid, $tempids)) {

                $deletedmatrnr = new stdclass;
                $deletedmatrnr->moodleuserid = $participant->moodleuserid;
                $deletedmatrnr->matrnr = $participant->matrnr;
                $deletedmatrnr->firstname = false;
                $deletedmatrnr->lastname = false;
                $deletedmatrnr->line = '';

                array_push($deletedparticipants, $deletedmatrnr);

            } else if ($participant->moodleuserid === null && $participant->login && !in_array($participant->login, $tempids)) {
                // Moodle participant that is not readed in again and should therefore be deleted.

                $deletedmatrnr = new stdclass;
                $deletedmatrnr->moodleuserid = false;
                $deletedmatrnr->matrnr = $participant->matrnr;
                $deletedmatrnr->firstname = $participant->firstname;
                $deletedmatrnr->lastname = $participant->lastname;
                $deletedmatrnr->line = '';

                array_push($deletedparticipants, $deletedmatrnr);
            }
        }
    }

    $allparticipants['badMatriculationNumbers'] = $badmatriculationnumbers;
    $allparticipants['deletedParticipants'] = $deletedparticipants;
    $allparticipants['oddParticipants'] = $oddparticipants;
    $allparticipants['existingParticipants'] = $existingparticipants;
    $allparticipants['newMoodleParticipants'] = $newmoodleparticipants;
    $tempparticipants = true;
}

// Instantiate form.
require_once($CFG->dirroot . '/mod/exammanagement/classes/forms/addparticipants_form.php');
if ($tempparticipants) {
    $mform = new mod_exammanagement_addparticipants_form(null, ['id' => $id, 'e' => $e, 'allparticipants' => $allparticipants]);
} else {
    $mform = new mod_exammanagement_addparticipants_form(null, ['id' => $id, 'e' => $e]);
}

// Form processing and displaying is done here.
if ($mform->is_cancelled()) { // Handle form cancel operation, if cancel button is present on form.
    redirect(new moodle_url('/mod/exammanagement/viewparticipants.php', ['id' => $id]),
        get_string('operation_canceled', 'mod_exammanagement'), null, 'warning');
} else if ($fromform = $mform->get_data()) { // In this case you process validated data.

    $draftid = file_get_submitted_draft_itemid('participantslists');

    if (!$draftid) { // If no import file and exam participants should be saved.

        // Get IDs of (deleted) participants.
        $participantids = helper::filtercheckedparticipants($fromform);

        if (isset($participantids['participants'])) {
            $newparticipantsids = $participantids['participants'];
        } else {
            $newparticipantsids = false;
        }

        if (isset($participantids['deletedparticipants'])) {
            $deletedparticipantsids = $participantids['deletedparticipants'];
        } else {
            $deletedparticipantsids = false;
        }

        $nonemoodleparticipantsmatrnrarr = [];

        // Get all participants that are already readed in and saved as temnp participants.
        $tempparticipants = $DB->get_records('exammanagement_temp_part', ['exammanagement' => $moduleinstance->id]);

        if ($newparticipantsids != false || $deletedparticipantsids != false) {

            // Get headers and temp file header.
            $savedfileheaders = json_decode($moduleinstance->importfileheaders ?? '');

            $tempfileheaders = json_decode($moduleinstance->tempimportfileheader ?? '');

            if (!isset($savedfileheaders) && isset($tempfileheaders)) { // If there are no saved headers by now.
                // Save new file header.
                $savedfileheaders = $tempfileheaders;
            } else if (isset($savedfileheaders) && isset($tempfileheaders)) {
                foreach ($tempfileheaders as $tempheaderkey => $tempfileheader) {

                    $saved = false;

                    foreach ($savedfileheaders as $savedheaderkey => $header) { // If new header is already saved.

                        if ($tempfileheader == $header) {
                            $saved = true;
                        }
                    }

                    if (!$saved) {
                        array_push($savedfileheaders, $tempfileheader);
                    }
                }
            }

            $moduleinstance->importfileheaders = json_encode($savedfileheaders);

            // Add new participants.
            if ($newparticipantsids) {
                $users = [];

                foreach ($newparticipantsids as $key => $tempidentifier) {

                    $tempheaderid = explode('-', $tempidentifier)[1];

                    $identifier = explode('_', explode('-', $tempidentifier)[0]);

                    if ($identifier[0] == 'mid') { // If participant is moodle user.
                        $user = new stdClass();
                        $user->exammanagement = $moduleinstance->id;
                        $user->courseid = $course->id;
                        $user->categoryid = $moduleinstance->categoryid;
                        $user->moodleuserid = $identifier[1];
                        $user->login = null;
                        $user->firstname = null;
                        $user->lastname = null;
                        $user->email = null;
                        $user->headerid = $tempheaderid;

                        // To be removed.
                        $dbman = $DB->get_manager();
                        $table = new xmldb_table('exammanagement_participants');
                        $field = new xmldb_field('plugininstanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
                        if ($dbman->field_exists($table, $field)) {
                            $user->plugininstanceid = 0; // For deprecated old version db version, should be removed.
                        }

                        array_push($users, $user);

                        unset($newparticipantsids[$key]);

                    } else {
                        array_push($nonemoodleparticipantsmatrnrarr, $identifier[1]);
                    }
                }

                if (!empty($nonemoodleparticipantsmatrnrarr)) {
                    $nonemoodleparticipantsarr = $ldapmanager->getldapattributesformatrnrs(
                        $nonemoodleparticipantsmatrnrarr, 'all_attributes');

                    foreach ($newparticipantsids as $key => $identifier) { // Now only contains participants without moodle account.

                        $tempheaderid = explode('-', $identifier)[1];

                        $matrnr = explode('_', explode('-', $identifier)[0])[1];

                        $user = new stdClass();
                        $user->exammanagement = $moduleinstance->id;
                        $user->courseid = $course->id;
                        $user->categoryid = $moduleinstance->categoryid;
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

                        // To be removed.
                        $dbman = $DB->get_manager();
                        $table = new xmldb_table('exammanagement_participants');
                        $field = new xmldb_field('plugininstanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
                        if ($dbman->field_exists($table, $field)) {
                            $user->plugininstanceid = 0; // For deprecated old version db version, should be removed.
                        }
                        array_push($users, $user);
                    }
                }

                // Insert records of new participants.
                $DB->insert_records('exammanagement_participants', $users);

            }

            // Delete participants that should be deleted.
            if ($deletedparticipantsids) {
                foreach ($deletedparticipantsids as $identifier) {
                    $temp = explode('_', $identifier);

                    if ($temp[0] == 'mid') { // Delete moodle participant.
                        helper::deleteparticipant($temp[1], false);
                    } else { // Delete participant without moodle account.

                        $userlogin = false;

                        $userlogin = $ldapmanager->getloginformatrnr($temp[1], 'importmatrnrnotpossible');

                        if ($userlogin) {
                            helper::deleteparticipant(false, $userlogin);
                        }
                    }
                }
            }

            // Delete temp file header and update saved file headers.
            $moduleinstance->tempimportfileheader = null;

            $DB->update_record("exammanagement", $moduleinstance);

            // Delete temp participants.
            helper::deletetempparticipants($moduleinstance);

            // Redirect.
            redirect(new moodle_url('/mod/exammanagement/viewparticipants.php', ['id' => $id]),
                get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
        } else {
            redirect(new moodle_url('/mod/exammanagement/viewparticipants.php', ['id' => $id]),
                get_string('alteration_failed', 'mod_exammanagement'), null, 'error');
        }

    } else if ($draftid) { // If participants are readed in from import file and should be saved as temporary participants.

        helper::deletetempparticipants($moduleinstance);

        $fs = get_file_storage();
        $context = context_user::instance($USER->id);
        $files = $fs->get_area_files($context->id, 'user', 'draft', $draftid, 'id DESC', false);

        $tempfileheaders = [];
        $users = [];

        $filecounter = 1;

        foreach ($files as $file) {

            // Get matriculation numbers from text file as an array.
            $filecontent = explode(PHP_EOL, $file->get_content()); // Separate lines.

            if ($filecontent) {
                $fileheader = $filecontent[0] . "\r\n".$filecontent[1];

                unset($filecontent[0]);
                unset($filecontent[1]);

                foreach ($filecontent as $key => $row) {
                    $potentialmatriculationnumbersarr = explode("	", $row); // From 2nd line: get all potential numbers.

                    if ($potentialmatriculationnumbersarr) {
                        foreach ($potentialmatriculationnumbersarr as $key2 => $pmatrnr) { // Create temp user.

                            $identifier = str_replace('"', '', $pmatrnr);
                             // If identifier contains numbers and only alpha numerical signs and is not to long.
                            if (preg_match('/\\d/', $identifier) !== 0 && ctype_alnum($identifier) && strlen($identifier) <= 20) {
                                $tempuser = new stdclass;
                                $tempuser->exammanagement = $moduleinstance->id;
                                $tempuser->courseid = $course->id;
                                $tempuser->categoryid = $moduleinstance->categoryid;
                                $tempuser->identifier = $identifier;
                                $tempuser->line = $key + 1 .'(' . $filecounter.')';

                                // To be removed.
                                $dbman = $DB->get_manager();
                                $table = new xmldb_table('exammanagement_temp_part');
                                $field = new xmldb_field('plugininstanceid',
                                    XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
                                if ($dbman->field_exists($table, $field)) {
                                    $tempuser->plugininstanceid = 0; // For deprecated old version db version, should be removed.
                                }

                                $tempuser->headerid = $filecounter;

                                array_push($users, $tempuser);

                            }
                        }
                    }
                }

                $fileheader = strip_tags($fileheader);

                if (mb_detect_encoding($fileheader, mb_detect_order(), true) !== "UTF-8") {
                    $fileheader = mb_convert_encoding($fileheader, "UTF-8");
                }

                array_push($tempfileheaders, $fileheader);

            }

            $filecounter += 1;
        }

        $moduleinstance->tempimportfileheader = json_encode($tempfileheaders);

        $DB->update_record("exammanagement", $moduleinstance);

        $DB->insert_records('exammanagement_temp_part', $users);

        redirect(new moodle_url('/mod/exammanagement/addparticipants.php', ['id' => $id]),
            get_string('operation_successfull', 'mod_exammanagement'), null, 'success');
    }

} else {
    // This branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.

    // Set $PAGE.
    $plugintype = get_string('modulename', 'mod_exammanagement');
    $modulename = format_string($moduleinstance->name, true, [
        'context' => $context,
    ]);

    if (isset($allparticipants)) {
        $title = get_string('addparticipants', 'mod_exammanagement');
    } else {
        $title = get_string('import_participants_from_file', 'mod_exammanagement');
    }

    $PAGE->set_url('/mod/exammanagement/addparticipants.php', ['id' => $id]);
    $PAGE->navbar->add($title);
    $PAGE->set_title($plugintype . ': ' . $modulename . ' - ' . $title);
    $PAGE->set_heading($course->fullname);
    if ($CFG->branch < 400) {
        $PAGE->force_settings_menu();
    }

    // Output header.
    echo $OUTPUT->header();

    if ($CFG->branch < 400) {
        echo $OUTPUT->heading($modulename);

        if ($moduleinstance->intro) {
            echo $OUTPUT->box(format_module_intro('exammanagement', $moduleinstance, $cm->id), 'generalbox', 'intro');
        }
    }

    // Output heading.
    if (get_config('mod_exammanagement', 'enablehelptexts')) {
        echo $OUTPUT->heading($title . ' ' . $OUTPUT->help_icon('addparticipants', 'mod_exammanagement', ''), 4);
    } else {
        echo $OUTPUT->heading($title, 4);
    }

    // Output buttons.
    if (isset($allparticipants)) {
        echo '<p><a href="' . new moodle_url('/mod/exammanagement/addparticipants.php',
            ['id' => $id, 'dtp' => true]) . '" role="button" class="btn btn-primary float-right" title="'
            . get_string("import_new_participants", "mod_exammanagement").'"><span class="d-none d-lg-block">' .
            get_string("import_new_participants", "mod_exammanagement") .
            '</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a></p><br><br>';
    }

    // Output description.

    // Output alerts.
    if (helper::placesassigned($moduleinstance)) {
        echo '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close"
            data-dismiss="alert">×</button>' . get_string("places_already_assigned_participants", "mod_exammanagement") . '</div>';
    }

    // Set default data.

    // Set data if checkboxes should be checked.
    // SetDefault in the form is much more time consuming for big amount of participants.
    $defaultvalues = ['id' => $id];

    if (isset($newmoodleparticipants)) {
        foreach ($newmoodleparticipants as $participant) {
            $defaultvalues['participants[mid_' . $participant->moodleuserid . '-' . $participant->headerid . ']'] = true;
        }
    }

    if (isset($deletedparticipants)) {
        foreach ($deletedparticipants as $participant) {
            if ($participant->moodleuserid) {
                $defaultvalues['deletedparticipants[mid_' . $participant->moodleuserid . ']'] = true;
            } else if ($participant->matrnr) {
                $defaultvalues['deletedparticipants[matrnr_' . $participant->matrnr . ']'] = true;
            }
        }
    }

    if (isset($courseparticipantsids)) {
        foreach ($courseparticipantsids as $id) {
            $defaultvalues['participants[' . $id . ']'] = true;
        }
    }

    $mform->set_data($defaultvalues);

    // Display form.
    $mform->display();

    // Finish the page.
    echo $OUTPUT->footer();
}
