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

namespace mod_exammanagement\forms;

use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\User;
use mod_exammanagement\general\MoodleDB;
use mod_exammanagement\general\Moodle;
use mod_exammanagement\ldap\ldapManager;

use moodleform;
use stdclass;
use notification;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/User.php');
require_once(__DIR__.'/../general/MoodleDB.php');
require_once(__DIR__.'/../general/Moodle.php');
require_once(__DIR__.'/../ldap/ldapManager.php');

/**
 * The form for adding course participants to the exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class addcourseparticipants_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        global $PAGE, $OUTPUT;

        $exammanagementinstanceobj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $userobj = User::getInstance($this->_customdata['id'], $this->_customdata['e'], $exammanagementinstanceobj->getCm()->instance);
        $moodledbobj = MoodleDB::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $moodleobj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $ldapmanagerobj = ldapManager::getInstance();

        $PAGE->requires->js_call_amd('mod_exammanagement/remove_cols', 'remove_cols'); // Remove col-md classes from moodle form layout for better layout.
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'init'); // Updating participants count if checkboxes are checked.
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'enable_cb'); // Checking all checkboxes via group checkbox.
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'togglesection'); // Toogling sections.

        $mform = $this->_form;

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<h3>'.get_string("addCourseParticipants", "mod_exammanagement"));

        if ($helptextsenabled) {
            $mform->addElement('html', $OUTPUT->help_icon('addCourseParticipants', 'mod_exammanagement', ''));
        }

        $mform->addElement('html', '</h3>');

        $mform->addElement('html', '<p>'.get_string("view_added_and_course_partipicants", "mod_exammanagement").'</p>');

        $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>'.get_string("course_participant_import_preventing_text_export", "mod_exammanagement").'</div>');

        if ($exammanagementinstanceobj->placesAssigned()) {
            $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>'.get_string("places_already_assigned_participants", "mod_exammanagement").'</div>');
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Get all nedded user data.
        $moodleparticipants = $userobj->getExamParticipants(array('mode' => 'moodle'), array('matrnr', 'profile', 'groups'));

        $nonmoodleparticipants = $userobj->getExamParticipants(array('mode' => 'nonmoodle'), array('matrnr'));

        $courseparticipantsids = $userobj->getCourseParticipantsIDs();

        $courseparticipants = array(); // Will contain all moodle users that are course participants and that can be choosen as future exam participants.

        $alreadyparticipants = array(); // Will contain all moodle users that are already participants.

        if ($moodleparticipants && $courseparticipantsids) { // Handle participants that are course participants and already exam participants.

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
        $coursegroups = groups_get_all_groups($exammanagementinstanceobj->getCourse()->id);

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
            $mform->addElement('html', '<h3 class="panel-title"><span id="selectedGroupOneCount" class="exammanagement_pure">0</span>/'.$deletedcount . ' ' . get_string("deletedmatrnr_no_course", "mod_exammanagement"). '</h3>');
            $mform->addElement('html', '<span class="collapse.show deleted_minimize pull-right" title="' . get_string("minimize_phase", "mod_exammanagement"). '" aria-label="' . get_string("minimize_phase", "mod_exammanagement"). '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
            $mform->addElement('html', '<span class="collapse deleted_maximize pull-right" title="' . get_string("maximize_phase", "mod_exammanagement"). '" aria-label="' . get_string("maximize_phase", "mod_exammanagement"). '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');

            $mform->addElement('html', '<div class="panel-body deleted_body">');

            $mform->addElement('html', '<div class="row"><div class="col-'.$col.'"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-'.$col.'"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div>');

            if ($coursegroups) {
                $mform->addElement('html', '<div class="col-'.$col.'"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div>');
            }

            $mform->addElement('html', '<div class="col-'.$col.'"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');
            $mform->addElement('html', '<div class="row"><div class="col-'.$col.' pl-4">');
            $mform->addElement('advcheckbox', 'checkall_deleted', get_string("select_deselect_all", "mod_exammanagement"), null, array('group' => 1, 'id' => 'checkboxgroup1'));
            $mform->addElement('html', '</div><div class="col-'.$col.'"></div><div class="col-'.$col.'"></div><div class="col-'.$col.'"></div></div>');

            // With moodle account.
            if ($moodleparticipants) { // Now contains only moodle users that should be deleted.

                foreach ($moodleparticipants as $key => $participant) {

                    $mform->addElement('html', '<div class="row text-danger"><div class="col-'.$col.' pl-4">');

                    $mform->addElement('advcheckbox', 'deletedparticipants[mid_'.$participant->moodleuserid.']', ' '.$participant->profile, null, array('group' => 1));

                    $mform->addElement('html', '</div><div class="col-'.$col.'">'.$participant->matrnr.'</div>');

                    if ($coursegroups) {

                        $mform->addElement('html', '<div class="col-'.$col.'">'.$participant->groups.'</div>');
                    }

                    $mform->addElement('html', '<div class="col-'.$col.'">' . get_string("state_to_be_deleted", "mod_exammanagement").' ('.get_string("state_no_courseparticipant", "mod_exammanagement").')</div></div>');

                }
            }

            // Without moodle account.
            if ($nonmoodleparticipants) {

                foreach ($nonmoodleparticipants as $key => $participant) { // Contains all non moodle users (that are marked to be deleted because they are no course participants).

                    $mform->addElement('html', '<div class="row text-danger"><div class="col-'.$col.' pl-4">');

                    $mform->addElement('advcheckbox', 'deletedparticipants[matrnr_'.$participant->login.']', ' '. $participant->firstname .' '.$participant->lastname, null, array('group' => 1));

                    $mform->addElement('html', '</div><div class="col-'.$col.'">'.$participant->matrnr.'</div>');

                    if ($coursegroups) {
                        $mform->addElement('html', '<div class="col-'.$col.'">-</div>');
                    }
                    $mform->addElement('html', '<div class="col-'.$col.'">'. get_string("state_to_be_deleted", "mod_exammanagement"). ' (' .get_string("state_nonmoodle", "mod_exammanagement", ['systemname' => $exammanagementinstanceobj->getMoodleSystemName()]).')</div></div>');
                }
            }

            $mform->addElement('html', '</div></div>');
        }

        // Display course participants already added as exam participants.
        if ($alreadyparticipants) {

            $mform->addElement('html', '<div class="panel panel-info exammanagement_panel">');
            $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="already">');
            $mform->addElement('html', '<div class="panel-heading text-info"><h3 class="panel-title">' . count($alreadyparticipants) . ' ' . get_string("existingmatrnr_course", "mod_exammanagement"). '</h3>');
            $mform->addElement('html', '<span class="collapse.show already_minimize pull-right" title="' . get_string("minimize_phase", "mod_exammanagement"). '" aria-label="' . get_string("minimize_phase", "mod_exammanagement"). '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
            $mform->addElement('html', '<span class="collapse already_maximize pull-right" title="' . get_string("maximize_phase", "mod_exammanagement"). '" aria-label="' . get_string("maximize_phase", "mod_exammanagement"). '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');

            $mform->addElement('html', '<div class="panel-body already_body">');

            $mform->addElement('html', '<div class="row"><div class="col-'.$col.'"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-'.$col.'"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div>');

            if ($coursegroups) {
                $mform->addElement('html', '<div class="col-'.$col.'"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div>');
            }

            $mform->addElement('html', '<div class="col-'.$col.'"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');

            foreach ($alreadyparticipants as $key => $participant) { // Contains all moodle users that are already participants.
                $mform->addElement('html', '<div class="row"><div class="col-'.$col.'"> ' . $participant->profile. ' </div>');
                $mform->addElement('html', '<div class="col-'.$col.'">'.$participant->matrnr.'</div>');

                if ($coursegroups) {
                    $mform->addElement('html', '<div class="col-'.$col.'">'.$participant->groups.'</div>');
                }

                $mform->addElement('html', '<div class="col-'.$col.'">'.get_string("state_existingmatrnr", "mod_exammanagement").'</div></div>');
            }

            $mform->addElement('html', '</div></div>');
        }

        // Display course participants not yet added as exam participants.
        if ($courseparticipantsids) {

            $mform->addElement('html', '<div class="panel panel-success exammanagement_panel">');
            $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="course">');
            $mform->addElement('html', '<div class="panel-heading text-success"><h3 class="panel-title"><span id="selectedGroupTwoCount" class="exammanagement_pure">'.count($courseparticipantsids).'</span>/'.count($courseparticipantsids) . ' ' . get_string("newmatrnr", "mod_exammanagement"). '</h3>');
            $mform->addElement('html', '<span class="collapse.show course_minimize pull-right" title="' . get_string("minimize_phase", "mod_exammanagement"). '" aria-label="' . get_string("minimize_phase", "mod_exammanagement"). '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
            $mform->addElement('html', '<span class="collapse course_maximize pull-right" title="' . get_string("maximize_phase", "mod_exammanagement"). '" aria-label="' . get_string("maximize_phase", "mod_exammanagement"). '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');

            $mform->addElement('html', '<div class="panel-body course_body">');

            $mform->addElement('html', '<div class="row"><div class="col-'.$col.'"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-'.$col.'"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div>');

            if ($coursegroups) {
                $mform->addElement('html', '<div class="col-'.$col.'"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div>');
            }

            $mform->addElement('html', '<div class="col-'.$col.'"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');

            $mform->addElement('html', '<div class="row"><div class="col-'.$col.' pl-4">');
            $mform->addElement('advcheckbox', 'checkall_new', get_string("select_deselect_all", "mod_exammanagement"), null, array('group' => 2, 'id' => 'checkboxgroup2'));
            $mform->setDefault('checkall_new', true);

            $mform->addElement('html', '</div><div class="col-'.$col.'"></div><div class="col-'.$col.'"></div><div class="col-'.$col.'"></div></div>');

            $alllogins = array(); // Needed for method gettiing all matrnr from ldap.

            $matriculationnumbers = array(); // Will contain matriculation numbers for all course participants.

            foreach ($courseparticipantsids as $key => $id) {

                global $OUTPUT;

                $courseparticipant = new stdclass;
                $courseparticipant->moodleuserid = $id;
                $courseparticipant->login = $moodledbobj->getFieldFromDB('user', 'username', array('id' => $id));

                array_push($alllogins, $courseparticipant->login);

                $moodleuser = $userobj->getMoodleUser($id);

                $courseid = $exammanagementinstanceobj->getCourse()->id;

                $image = $OUTPUT->user_picture($moodleuser, array('courseid' => $courseid, 'link' => true, 'includefullname' => true));

                $courseparticipant->profile = $image;

                if ($coursegroups) {
                    $usergroups = groups_get_user_groups($courseid, $id);
                    $groupnames = false;

                    foreach ($usergroups as $groupskey => $value) {
                        if ($value) {
                            foreach ($value as $groupskey2 => $groupid) {
                                if (!$groupnames) {
                                    $groupnames = '<strong><a href="'.$moodleobj->getMoodleUrl('/group/index.php', $courseid, 'group', $groupid).'">'.groups_get_group_name($groupid).'</a></strong>';
                                } else {
                                    $groupnames .= ', <strong><a href="'.$moodleobj->getMoodleUrl('/group/index.php', $courseid, 'group', $groupid).'">'.groups_get_group_name($groupid).'</a></strong> ';
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

            $matriculationnumbers = $ldapmanagerobj->getMatriculationNumbersForLogins($alllogins); // Retrieve matrnrs for all logins from ldap.

            if (!empty($courseparticipants)) {
                foreach ($courseparticipants as $key => $participant) {

                    if (!empty($matriculationnumbers)) {

                        if (isset($participant->login) && array_key_exists($participant->login, $matriculationnumbers) && $matriculationnumbers[$participant->login] !== false) {
                            $matrnr = $matriculationnumbers[$participant->login];
                        } else {
                            $matrnr = '-';
                        }
                    } else {
                        $matrnr = '-';
                    }

                    $mform->addElement('html', '<div class="row"><div class="col-'.$col.' pl-4">');
                    $mform->addElement('advcheckbox', 'participants['.$participant->moodleuserid.']', ' '.$participant->profile, null, array('group' => 2));

                    $mform->addElement('html', '</div><div class="col-'.$col.'">'.$matrnr.'</div>');

                    if ($coursegroups) {
                        $mform->addElement('html', '<div class="col-'.$col.'">'.$participant->groups.'</div>');
                    }

                    $mform->addElement('html', '<div class="col-'.$col.'">'.get_string("state_courseparticipant", "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');
            }
        }

        $this->add_action_buttons(true, get_string("add_to_exam", "mod_exammanagement"));

        $mform->addElement('html', '</div>');

        $mform->disable_form_change_checker();
    }

    // Custom validation should be added here.
    public function validation($data, $files) {

        $errors = array();

        if (isset($data['participants'])) {
            foreach ($data['participants'] as $participantid => $checked) {
                if (!preg_match("/^[a-zA-Z0-9_\-]+$/", $participantid)) {
                    $errors['participants['.$participantid.']'] = get_string('err_invalidcheckboxid_participants', 'mod_exammanagement');
                }
            }
        }

        if (isset($data['deletedparticipants'])) {

            foreach ($data['deletedparticipants'] as $participantid => $checked) {

                if (!preg_match("/^[a-zA-Z0-9_\-]+$/", $participantid)) {
                    $errors['deletedparticipants['.$participantid.']'] = get_string('err_invalidcheckboxid_participants', 'mod_exammanagement');
                }
            }
        }

        return $errors;
    }
}
