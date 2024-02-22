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
 * The form for adding course participants to the exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_exammanagement\local\helper;
use mod_exammanagement\ldap\ldapmanager;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../local/helper.php');
require_once(__DIR__.'/../ldap/ldapmanager.php');

/**
 * The form for adding course participants to the exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_exammanagement_addcourseparticipants_form extends moodleform {

    /**
     * Define the form - called by parent constructor.
     */
    public function definition() {
        global $DB, $OUTPUT, $PAGE;

        $ldapmanager = ldapmanager::getinstance();

        $moduleinstance = helper::getmoduleinstance($this->_customdata['id'], $this->_customdata['e']);

        // Remove col-md classes from moodle form layout for better layout.
        $PAGE->requires->js_call_amd('mod_exammanagement/remove_cols', 'removecols');
        // Updating participants count if checkboxes are checked.
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'init');
        // Checking all checkboxes via group checkbox.
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'enablecb');
        // Toogling sections.
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'togglesection');

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Get all moodle examparticipants.
        $moodleparticipants = helper::getexamparticipants(
            $moduleinstance,
            ['mode' => 'moodle'],
            ['matrnr', 'profile', 'groups'],
        );

        // Get all moodle examparticipants.
        $nonmoodleparticipants = helper::getexamparticipants(
            $moduleinstance,
            ['mode' => 'nonmoodle'],
            ['matrnr'],
        );

        $courseparticipantsids = helper::getcourseparticipantsids($this->_customdata['id']);

        // All course participants and that can be choosen as future exam participants.
        $courseparticipants = [];

        // All moodle users that are already participants.
        $alreadyparticipants = [];

         // Handle participants that are course participants and already exam participants.
        if ($moodleparticipants && $courseparticipantsids) {

            foreach ($moodleparticipants as $key => $participant) {
                if (in_array($participant->moodleuserid, $courseparticipantsids)) {
                    if (($removekey = array_search($participant->moodleuserid, $courseparticipantsids)) !== false) {
                        unset($courseparticipantsids[$removekey]);
                        array_push($alreadyparticipants, $participant);
                        unset($moodleparticipants[$key]);
                    }
                }
            }
        }

        // Determine if course groups are set.
        $courseid = $moduleinstance->course;
        $coursegroups = groups_get_all_groups($courseid);

        if (count($coursegroups) > 0) {
            $coursegroups = true;
            $col = 3;
        } else {
            $coursegroups = false;
            $col = 4;
        }

        $mform->addElement('html', '<div class="exammanagement_overview">');

        if ($moodleparticipants || $nonmoodleparticipants) {

            $deletedcount = 0;

            if ($moodleparticipants) {
                $deletedcount += count($moodleparticipants);
            }

            if ($nonmoodleparticipants) {
                $deletedcount += count($nonmoodleparticipants);
            }

            // Display all exam participants that are no course participants and will be deleted.

            $mform->addElement('html', '<div class="panel panel-success exammanagement_panel">');
            $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="deleted">');
            $mform->addElement('html', '<div class="panel-heading text-danger">');
            $mform->addElement('html',
                '<h3 class="panel-title"><span id="selectedGroupOneCount" class="exammanagement_pure">0</span>/' .
                $deletedcount . ' ' . get_string("deletedmatrnr_no_course", "mod_exammanagement"). '</h3>');
            $mform->addElement('html',
                '<span class="collapse.show deleted_minimize float-right" title="' .
                get_string("minimize_phase", "mod_exammanagement") . '" aria-label="' .
                get_string("minimize_phase", "mod_exammanagement") . '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
            $mform->addElement('html', '<span class="collapse deleted_maximize float-right" title="' .
                get_string("maximize_phase", "mod_exammanagement"). '" aria-label="' .
                get_string("maximize_phase", "mod_exammanagement") .
                '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');

            $mform->addElement('html', '<div class="panel-body deleted_body">');

            $mform->addElement('html', '<div class="row"><div class="col-' . $col . '"><h4>' .
                get_string("participants", "mod_exammanagement") . '</h4></div><div class="col-' . $col . '"><h4>' .
                get_string("matriculation_number", "mod_exammanagement").'</h4></div>');

            if ($coursegroups) {
                $mform->addElement('html', '<div class="col-' . $col . '"><h4>' .
                    get_string("course_groups", "mod_exammanagement").'</h4></div>');
            }

            $mform->addElement('html', '<div class="col-' . $col . '"><h4>' . get_string("import_state", "mod_exammanagement") .
                '</h4></div></div>');
            $mform->addElement('html', '<div class="row"><div class="col-' . $col . ' pl-4">');
            $mform->addElement('advcheckbox', 'checkall_deleted', get_string("select_deselect_all", "mod_exammanagement"),
                null, ['group' => 1, 'id' => 'checkboxgroup1']);
            $mform->addElement('html', '</div><div class="col-' . $col . '"></div><div class="col-' . $col .
                '"></div><div class="col-' . $col . '"></div></div>');

            // With moodle account.
            if ($moodleparticipants) { // Now contains only moodle users that should be deleted.

                foreach ($moodleparticipants as $key => $participant) {

                    $mform->addElement('html', '<div class="row text-danger"><div class="col-' . $col . ' pl-4">');

                    $mform->addElement('advcheckbox', 'deletedparticipants[mid_' . $participant->moodleuserid . ']', ' ' .
                        $participant->profile, null, ['group' => 1]);

                    $mform->addElement('html', '</div><div class="col-' . $col . '">' . $participant->matrnr . '</div>');

                    if ($coursegroups) {

                        $mform->addElement('html', '<div class="col-' . $col . '">' . $participant->groups . '</div>');
                    }

                    $mform->addElement('html', '<div class="col-' . $col . '">' .
                        get_string("state_to_be_deleted", "mod_exammanagement") . ' (' .
                        get_string("state_no_courseparticipant", "mod_exammanagement") . ')</div></div>');

                }
            }

            // Without moodle account.
            if ($nonmoodleparticipants) {

                // Contains all non moodle users (that are marked to be deleted because they are no course participants).
                foreach ($nonmoodleparticipants as $key => $participant) {

                    $mform->addElement('html', '<div class="row text-danger"><div class="col-' . $col . ' pl-4">');

                    $mform->addElement('advcheckbox', 'deletedparticipants[matrnr_' . $participant->login . ']', ' '.
                        $participant->firstname . ' ' . $participant->lastname, null, ['group' => 1]);

                    $mform->addElement('html', '</div><div class="col-' . $col . '">' . $participant->matrnr . '</div>');

                    if ($coursegroups) {
                        $mform->addElement('html', '<div class="col-' . $col . '">-</div>');
                    }
                    $mform->addElement('html', '<div class="col-' . $col . '">' .
                        get_string("state_to_be_deleted", "mod_exammanagement") . ' (' .
                        get_string("state_nonmoodle", "mod_exammanagement",
                        ['systemname' => helper::getmoodlesystemname()]) . ')</div></div>');
                }
            }

            $mform->addElement('html', '</div></div>');
        }

        // Display course participants already added as exam participants.
        if ($alreadyparticipants) {

            $mform->addElement('html', '<div class="panel panel-info exammanagement_panel">');
            $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="already">');
            $mform->addElement('html', '<div class="panel-heading text-info"><h3 class="panel-title">' .
                count($alreadyparticipants) . ' ' . get_string("existingmatrnr_course", "mod_exammanagement"). '</h3>');
            $mform->addElement('html', '<span class="collapse.show already_minimize float-right" title="' .
                get_string("minimize_phase", "mod_exammanagement"). '" aria-label="' .
                get_string("minimize_phase", "mod_exammanagement"). '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
            $mform->addElement('html', '<span class="collapse already_maximize float-right" title="' .
                get_string("maximize_phase", "mod_exammanagement"). '" aria-label="' .
                get_string("maximize_phase", "mod_exammanagement").
                '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');

            $mform->addElement('html', '<div class="panel-body already_body">');

            $mform->addElement('html', '<div class="row"><div class="col-' . $col . '"><h4>' .
                get_string("participants", "mod_exammanagement").'</h4></div><div class="col-' . $col . '"><h4>' .
                get_string("matriculation_number", "mod_exammanagement").'</h4></div>');

            if ($coursegroups) {
                $mform->addElement('html', '<div class="col-' . $col . '"><h4>' .
                    get_string("course_groups", "mod_exammanagement").'</h4></div>');
            }

            $mform->addElement('html', '<div class="col-' . $col . '"><h4>' .
                get_string("import_state", "mod_exammanagement").'</h4></div></div>');

            foreach ($alreadyparticipants as $key => $participant) { // Contains all moodle users that are already participants.
                $mform->addElement('html', '<div class="row"><div class="col-' . $col . '"> ' . $participant->profile . ' </div>');
                $mform->addElement('html', '<div class="col-' . $col . '">' . $participant->matrnr . '</div>');

                if ($coursegroups) {
                    $mform->addElement('html', '<div class="col-' . $col . '">' . $participant->groups . '</div>');
                }

                $mform->addElement('html', '<div class="col-' . $col . '">' .
                    get_string("state_existingmatrnr", "mod_exammanagement") . '</div></div>');
            }

            $mform->addElement('html', '</div></div>');
        }

        // Display course participants not yet added as exam participants.
        if ($courseparticipantsids) {

            $mform->addElement('html', '<div class="panel panel-success exammanagement_panel">');
            $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="course">');
            $mform->addElement('html', '<div class="panel-heading text-success"><h3 class="panel-title">
                <span id="selectedGroupTwoCount" class="exammanagement_pure">' . count($courseparticipantsids) . '</span>/' .
                count($courseparticipantsids) . ' ' . get_string("newmatrnr", "mod_exammanagement") . '</h3>');
            $mform->addElement('html', '<span class="collapse.show course_minimize float-right" title="' .
                get_string("minimize_phase", "mod_exammanagement") . '" aria-label="' .
                get_string("minimize_phase", "mod_exammanagement") . '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
            $mform->addElement('html', '<span class="collapse course_maximize float-right" title="' .
                get_string("maximize_phase", "mod_exammanagement") . '" aria-label="' .
                get_string("maximize_phase", "mod_exammanagement") .
                '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');

            $mform->addElement('html', '<div class="panel-body course_body">');

            $mform->addElement('html', '<div class="row"><div class="col-' . $col . '"><h4>' .
                get_string("participants", "mod_exammanagement").'</h4></div><div class="col-' . $col . '"><h4>' .
                get_string("matriculation_number", "mod_exammanagement").'</h4></div>');

            if ($coursegroups) {
                $mform->addElement('html', '<div class="col-' . $col . '"><h4>' .
                    get_string("course_groups", "mod_exammanagement").'</h4></div>');
            }

            $mform->addElement('html', '<div class="col-' . $col . '"><h4>' .
                get_string("import_state", "mod_exammanagement").'</h4></div></div>');

            $mform->addElement('html', '<div class="row"><div class="col-' . $col . ' pl-4">');
            $mform->addElement('advcheckbox', 'checkall_new', get_string("select_deselect_all", "mod_exammanagement"), null,
                ['group' => 2, 'id' => 'checkboxgroup2']);
            $mform->setDefault('checkall_new', true);

            $mform->addElement('html', '</div><div class="col-' . $col . '"></div><div class="col-' . $col .
                '"></div><div class="col-' . $col . '"></div></div>');

            $alllogins = []; // Needed for method gettiing all matrnr from ldap.

            $matriculationnumbers = []; // Will contain matriculation numbers for all course participants.

            foreach ($courseparticipantsids as $key => $id) {

                global $OUTPUT;

                $courseparticipant = new stdclass;
                $courseparticipant->moodleuserid = $id;
                $courseparticipant->login = $DB->get_field('user', 'username', ['id' => $id]);

                array_push($alllogins, $courseparticipant->login);

                $moodleuser = helper::getmoodleuser($id);

                $image = $OUTPUT->user_picture($moodleuser,
                    ['courseid' => $courseid, 'link' => true, 'includefullname' => true]);

                $courseparticipant->profile = $image;

                if ($coursegroups) {
                    $usergroups = groups_get_user_groups($courseid, $id);
                    $groupnames = false;

                    foreach ($usergroups as $groupskey => $value) {
                        if ($value) {
                            foreach ($value as $groupskey2 => $groupid) {
                                if (!$groupnames) {
                                    $groupnames = '<strong><a href="' . new moodle_url('/group/index.php',
                                        ['id' => $courseid, 'group' => $groupid]) . '">' .
                                        groups_get_group_name($groupid) . '</a></strong>';
                                } else {
                                    $groupnames .= ', <strong><a href="' . new moodle_url('/group/index.php',
                                        ['id' => $courseid, 'group' => $groupid]) . '">' .
                                        groups_get_group_name($groupid) . '</a></strong>';
                                }
                            }
                        } else {
                            $groupnames = '-';
                            break;
                        }
                    }
                    $courseparticipant->groups = $groupnames;
                }

                $courseparticipants[$key] = $courseparticipant;
            }

            $matriculationnumbers = $ldapmanager->getmatrnrsforlogins($alllogins); // Retrieve matrnrs for all logins from ldap.

            if (!empty($courseparticipants)) {
                foreach ($courseparticipants as $key => $participant) {

                    if (!empty($matriculationnumbers)) {

                        if (isset($participant->login) &&
                            array_key_exists($participant->login, $matriculationnumbers) &&
                            $matriculationnumbers[$participant->login] !== false) {

                            $matrnr = $matriculationnumbers[$participant->login];
                        } else {
                            $matrnr = '-';
                        }
                    } else {
                        $matrnr = '-';
                    }

                    $mform->addElement('html', '<div class="row"><div class="col-' . $col . ' pl-4">');
                    $mform->addElement('advcheckbox', 'participants[' . $participant->moodleuserid .
                        ']', ' '.$participant->profile, null, ['group' => 2]);

                    $mform->addElement('html', '</div><div class="col-' . $col . '">'.$matrnr.'</div>');

                    if ($coursegroups) {
                        $mform->addElement('html', '<div class="col-' . $col . '">' . $participant->groups . '</div>');
                    }

                    $mform->addElement('html', '<div class="col-' . $col . '">' .
                        get_string("state_courseparticipant", "mod_exammanagement") . '</div></div>');
                }

                $mform->addElement('html', '</div></div>');
            }
        }

        $this->add_action_buttons(true, get_string("add_to_exam", "mod_exammanagement"));

        $mform->addElement('html', '</div>');

        $mform->disable_form_change_checker();
    }

    /**
     * Custom validation for the form.
     *
     * @param object $data The data from the form.
     * @param object $files The files from the form.
     * @return object $errors The errors.
     */
    public function validation($data, $files) {

        $errors = [];

        if (isset($data['participants'])) {
            foreach ($data['participants'] as $participantid => $checked) {
                if (!preg_match("/^[a-zA-Z0-9_\-]+$/", $participantid)) {
                    $errors['participants[' . $participantid . ']'] =
                        get_string('err_invalidcheckboxid_participants', 'mod_exammanagement');
                }
            }
        }

        if (isset($data['deletedparticipants'])) {

            foreach ($data['deletedparticipants'] as $participantid => $checked) {

                if (!preg_match("/^[a-zA-Z0-9_\-]+$/", $participantid)) {
                    $errors['deletedparticipants[' . $participantid . ']'] =
                        get_string('err_invalidcheckboxid_participants', 'mod_exammanagement');
                }
            }
        }

        return $errors;
    }
}
