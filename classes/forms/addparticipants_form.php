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
 * The form for adding participants to the exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;

use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\userhandler;
use mod_exammanagement\general\Moodle;
use moodleform;
use stdclass;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/userhandler.php');
require_once(__DIR__.'/../general/Moodle.php');

/**
 * The form for adding participants to the exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class addparticipants_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        global $PAGE, $CFG, $OUTPUT;

        $exammanagementinstanceobj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $userobj = userhandler::getinstance($this->_customdata['id'], $this->_customdata['e'], $exammanagementinstanceobj->getCm()->instance);
        $moodleobj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $PAGE->requires->js_call_amd('mod_exammanagement/remove_cols', 'remove_cols'); // Remove col-md classes from moodle form layout for better layout.
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'init'); // Updating participants count if checkboxes are checked.
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'enable_cb'); // Checking all checkboxes via group checkbox.
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'togglesection'); // Toogling sections.

        $mform = $this->_form;

        if (isset($this->_customdata['allParticipants'])) {
            $allparticipants = $this->_customdata['allParticipants'];
        } else {
            $allparticipants = false;
        }

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<div class="row"><div class="col-8"><h3>');

        if ($allparticipants) {
            $mform->addElement('html', get_string("addParticipants", "mod_exammanagement"));
        } else {
            $mform->addElement('html', get_string("import_participants_from_file", "mod_exammanagement"));
        }

        if ($helptextsenabled) {
            $mform->addElement('html', $OUTPUT->help_icon('addParticipants', 'mod_exammanagement', ''));
        }

        $mform->addElement('html', '</h3></div><div class="col-4">');

        if ($allparticipants) {
            $mform->addElement('html', '<a href="' . new moodle_url('/mod/exammanagement/addParticipants.php',
                ['id' => $this->_customdata['id'], 'dtp' => true]) . '" role="button" class="btn btn-primary pull-right" title="'
                . get_string("import_new_participants", "mod_exammanagement").'"><span class="d-none d-lg-block">' .
                get_string("import_new_participants", "mod_exammanagement") .
                '</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>');
        }

        $mform->addElement('html', '</div></div>');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        if ($allparticipants) {

            if ($exammanagementinstanceobj->placesAssigned()) {
                $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>'.get_string("places_already_assigned_participants", "mod_exammanagement").'</div>');
            }

            // Determine if course groups are set.
            $coursegroups = groups_get_all_groups($exammanagementinstanceobj->getCourse()->id);

            if (count($coursegroups) > 0) {
                $coursegroups = true;
                $col = 3;
                $littlecol = 2;
            } else {
                $coursegroups = false;
                $col = 4;
                $littlecol = 3;
            }

            $systemname = $exammanagementinstanceobj->getMoodleSystemName();

            // Output participants.
            $mform->addElement('html', '<div class="exammanagement_overview">');

            if ($allparticipants['badMatriculationNumbers']) { // Invalid or doubled matriculation numbers.

                $mform->addElement('html', '<div class="panel panel-danger exammanagement_panel">');
                $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="invalid">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . count($allparticipants['badMatriculationNumbers']) . ' ' . get_string("badmatrnr", "mod_exammanagement"). '</h3>');
                $mform->addElement('html', '<span class="collapse.show invalid_minimize pull-right" title="' . get_string("minimize_phase", "mod_exammanagement"). '" aria-label="' . get_string("minimize_phase", "mod_exammanagement"). '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
                $mform->addElement('html', '<span class="collapse invalid_maximize pull-right" title="' . get_string("maximize_phase", "mod_exammanagement"). '" aria-label="' . get_string("maximize_phase", "mod_exammanagement"). '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');

                $mform->addElement('html', '<div class="panel-body invalid_body">');

                $mform->addElement('html', '<div class="row"><div class="col-1"></div><div class="col-'.$col.'"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-'.$littlecol.'"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div>');

                if ($coursegroups) {
                    $mform->addElement('html', '<div class="col-'.$col.'"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div>');
                }

                $mform->addElement('html', '<div class="col-'.$col.'"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');

                foreach ($allparticipants['badMatriculationNumbers'] as $invaliduser) { // Bad or double matrnr.
                    $mform->addElement('html', '<div class="row text-danger">');
                    $mform->addElement('html', '<div class="col-1"> # '.$invaliduser->line);
                    $mform->addElement('html', '</div><div class="col-'.$col.'"> - </div>');
                    $mform->addElement('html', '<div class="col-'.$littlecol.'">'.$invaliduser->matrnr.'</div>');

                    if ($coursegroups) {
                        $mform->addElement('html', '<div class="col-'.$col.'"> - </div>');
                    }

                    $mform->addElement('html', '<div class="col-'.$col.'">'.get_string($invaliduser->state, "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');
            }

            if ($allparticipants['oddParticipants']) { // Moodle users that are no course participants or users that have no moodle account.

                $count = count($allparticipants['oddParticipants']);

                $mform->addElement('html', '<div class="panel panel-warning exammanagement_panel">');
                $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="odd">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title"><span id="selectedGroupOneCount" class="exammanagement_pure">0</span>/'. $count . ' ' . get_string("oddmatrnr", "mod_exammanagement"). '</h3>');
                $mform->addElement('html', '<span class="collapse.show odd_minimize pull-right" title="' . get_string("minimize_phase", "mod_exammanagement"). '" aria-label="' . get_string("minimize_phase", "mod_exammanagement"). '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
                $mform->addElement('html', '<span class="collapse odd_maximize pull-right" title="' . get_string("maximize_phase", "mod_exammanagement"). '" aria-label="' . get_string("maximize_phase", "mod_exammanagement"). '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');

                $mform->addElement('html', '<div class="panel-body odd_body">');

                $mform->addElement('html', '<div class="row"><div class="col-1"></div><div class="col-'.$col.'"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-'.$littlecol.'"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div>');

                if ($coursegroups) {
                    $mform->addElement('html', '<div class="col-'.$col.'"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div>');
                }

                $mform->addElement('html', '<div class="col-'.$col.'"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');

                $mform->addElement('html', '<div class="row"><div class="col-1"></div><div class="col-'.$col.'">');
                $mform->addElement('advcheckbox', 'checkall_odds', get_string("select_deselect_all", "mod_exammanagement"), null, array('group' => 1, 'id' => 'checkboxgroup1'));
                $mform->addElement('html', '</div><div class="col-'.$littlecol.'"></div><div class="col-'.$col.'"></div><div class="col-'.$col.'"></div></div>');

                foreach ($allparticipants['oddParticipants'] as $odduser) {

                    $mform->addElement('html', '<div class="row text-warning">');
                    $mform->addElement('html', '<div class="col-1"> # '.$odduser->line);
                    $mform->addElement('html', '</div><div class="col-'.$col.'">');

                    if ($odduser->state == 'state_no_courseparticipant') {

                        global $OUTPUT;

                        $moodleuser = $userobj->getmoodleuser($odduser->moodleuserid);

                        $courseid = $exammanagementinstanceobj->getCourse()->id;

                        $image = $OUTPUT->user_picture($moodleuser, array('courseid' => $courseid, 'link' => false, 'includefullname' => true));

                        $mform->addElement('advcheckbox', 'participants[mid_'.$odduser->moodleuserid.'-'.$odduser->headerid.']', $image, null, array('group' => 1));
                    } else if ($odduser->state == 'state_nonmoodle') {
                        $mform->addElement('advcheckbox', 'participants[matrnr_'.$odduser->matrnr.'-'.$odduser->headerid.']', '', null, array('group' => 1));
                    }

                    $mform->addElement('html', '</div><div class="col-'.$littlecol.'">'.$odduser->matrnr.'</div>');

                    if ($coursegroups) {
                        $mform->addElement('html', '<div class="col-'.$col.'"> - </div>');
                    }
                    $mform->addElement('html', '<div class="col-'.$col.'">'.get_string($odduser->state, "mod_exammanagement", ['systemname' => $systemname]).' '.$OUTPUT->help_icon($odduser->state, 'mod_exammanagement', '').'</div></div>');
                }

                $mform->addElement('html', '</div></div>');

            }

            if ($allparticipants['deletedParticipants']) { // Users that should be deleted because they are already read in from file with same header but not in this file.

                $count = count($allparticipants['deletedParticipants']);

                $mform->addElement('html', '<div class="panel panel-success exammanagement_panel">');
                $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="deleted">');
                $mform->addElement('html', '<div class="panel-heading text-danger"><h3 class="panel-title"><span id="selectedGroupTwoCount" class="exammanagement_pure">'. $count . '</span>/'. $count . ' ' . get_string("deletedmatrnr", "mod_exammanagement"). '</h3>');
                $mform->addElement('html', '<span class="collapse.show deleted_minimize pull-right" title="' . get_string("minimize_phase", "mod_exammanagement"). '" aria-label="' . get_string("minimize_phase", "mod_exammanagement"). '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
                $mform->addElement('html', '<span class="collapse deleted_maximize pull-right" title="' . get_string("maximize_phase", "mod_exammanagement"). '" aria-label="' . get_string("maximize_phase", "mod_exammanagement"). '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');

                $mform->addElement('html', '<div class="panel-body deleted_body">');

                $mform->addElement('html', '<div class="row"><div class="col-1"></div><div class="col-'.$col.'"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-'.$littlecol.'"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div>');

                if ($coursegroups) {
                    $mform->addElement('html', '<div class="col-'.$col.'"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div>');
                }

                $mform->addElement('html', '<div class="col-'.$col.'"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');

                $mform->addElement('html', '<div class="row"><div class="col-1"></div><div class="col-'.$col.' pl-4">');
                $mform->addElement('advcheckbox', 'checkall_deleted', get_string("select_deselect_all", "mod_exammanagement"), null, array('group' => 2, 'id' => 'checkboxgroup2'));
                $mform->setDefault('checkall_deleted', true);
                $mform->addElement('html', '</div><div class="col-'.$littlecol.'"></div><div class="col-'.$col.'"></div><div class="col-'.$col.'"></div></div>');

                foreach ($allparticipants['deletedParticipants'] as $deleteduser) {

                    $mform->addElement('html', '<div class="row text-danger"><div class="col-1"></div><div class="col-'.$col.' pl-4">');

                    if ($deleteduser->moodleuserid) {
                        global $OUTPUT;

                        $moodleuser = $userobj->getmoodleuser($deleteduser->moodleuserid);

                        $courseid = $exammanagementinstanceobj->getCourse()->id;

                        $image = $OUTPUT->user_picture($moodleuser, array('courseid' => $courseid, 'link' => true, 'includefullname' => true));

                        $mform->addElement('advcheckbox', 'deletedparticipants[mid_'.$deleteduser->moodleuserid.']', $image, null, array('group' => 2));

                    } else if ($deleteduser->matrnr) {
                        $mform->addElement('advcheckbox', 'deletedparticipants[matrnr_'.$deleteduser->matrnr.']', ' '. $deleteduser->firstname .' '.$deleteduser->lastname, null, array('group' => 2));
                    }

                    $mform->addElement('html', '</div><div class="col-'.$littlecol.'">'.$deleteduser->matrnr.'</div>');

                    if ($coursegroups) {
                        if ($deleteduser->moodleuserid) {

                            $courseid = $exammanagementinstanceobj->getCourse()->id;

                            $usergroups = groups_get_user_groups($courseid, $deleteduser->moodleuserid);
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

                            $mform->addElement('html', '<div class="col-' . $col . '">' . $groupnames . '</div>');
                        } else {
                            $mform->addElement('html', '<div class="col-' . $col . '">-</div>');
                        }
                    }

                    if ($deleteduser->moodleuserid) {
                        $mform->addElement('html', '<div class="col-' . $col . '">' .
                            get_string('state_to_be_deleted', "mod_exammanagement") . ' (' .
                            get_string('state_not_in_file_anymore', "mod_exammanagement") . ')</div></div>');
                    } else if ($deleteduser->matrnr) {
                        $mform->addElement('html', '<div class="col-' . $col . '">' .
                            get_string('state_to_be_deleted', "mod_exammanagement") . ' (' .
                            get_string('state_not_in_file_anymore', "mod_exammanagement") . ')</div></div>');
                    }

                }

                $mform->addElement('html', '</div></div>');

            }

            if ($allparticipants['existingParticipants']) {

                $count = count($allparticipants['existingParticipants']);

                $mform->addElement('html', '<div class="panel panel-info exammanagement_panel">');
                $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="existing">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . $count . ' ' . get_string("existingmatrnr", "mod_exammanagement"). '</h3>');
                $mform->addElement('html', '<span class="collapse.show existing_minimize pull-right" title="' . get_string("minimize_phase", "mod_exammanagement"). '" aria-label="' . get_string("minimize_phase", "mod_exammanagement"). '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
                $mform->addElement('html', '<span class="collapse existing_maximize pull-right" title="' . get_string("maximize_phase", "mod_exammanagement"). '" aria-label="' . get_string("maximize_phase", "mod_exammanagement"). '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');

                $mform->addElement('html', '<div class="panel-body existing_body">');

                $mform->addElement('html', '<div class="row"><div class="col-1"></div><div class="col-'.$col.'"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-'.$littlecol.'"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div>');

                if ($coursegroups) {
                    $mform->addElement('html', '<div class="col-'.$col.'"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div>');
                }

                $mform->addElement('html', '<div class="col-'.$col.'"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');

                foreach ($allparticipants['existingParticipants'] as $existinguser) {

                    $mform->addElement('html', '<div class="row text-info">');
                    $mform->addElement('html', '<div class="col-1"> # '.$existinguser->line);

                    if ($existinguser->moodleuserid) {

                        global $OUTPUT;

                        $moodleuser = $userobj->getmoodleuser($existinguser->moodleuserid);

                        $courseid = $exammanagementinstanceobj->getCourse()->id;

                        if ($existinguser->state === 'state_existingmatrnr') {
                            $image = $OUTPUT->user_picture($moodleuser, array('courseid' => $courseid, 'link' => true, 'includefullname' => true));
                            $mform->addElement('html', '</div><div class="col-'.$col.'">' . $image . ' </div>');
                        } else {
                            $image = $OUTPUT->user_picture($moodleuser, array('courseid' => $courseid, 'link' => true, 'includefullname' => true));
                            $mform->addElement('html', '</div><div class="col-'.$col.'"> ' . $image . ' </div>');
                        }

                    } else if ($existinguser->matrnr) {
                        $mform->addElement('html', '</div><div class="col-'.$col.'">'.$existinguser->firstname.' '.$existinguser->lastname.'</div>');
                    }

                    $mform->addElement('html', '<div class="col-'.$littlecol.'">'.$existinguser->matrnr.'</div>');

                    if ($coursegroups) {

                        if ($existinguser->moodleuserid) {
                            $courseid = $exammanagementinstanceobj->getCourse()->id;

                            $usergroups = groups_get_user_groups($courseid, $existinguser->moodleuserid);
                            $groupnames = false;

                            foreach ($usergroups as $groupskey => $value) {
                                if ($value) {
                                    foreach ($value as $groupskey2 => $groupid) {
                                        if (!$groupnames) {
                                            $groupnames = '<strong><a href="' . new moodle_url('/group/index.php',
                                                ['id' => $courseid, 'group' => $groupid]) . '">' . groups_get_group_name($groupid) .
                                                '</a></strong>';
                                        } else {
                                            $groupnames .= ', <strong><a href="' . new moodle_url('/group/index.php',
                                                ['id' => $courseid, 'group' => $groupid]) . '">' . groups_get_group_name($groupid) .
                                                '</a></strong> ';
                                        }
                                    }
                                } else {
                                    $groupnames = '-';
                                    break;
                                }
                            }

                            $mform->addElement('html', '<div class="col-' . $col . '">' . $groupnames . '</div>');
                        } else if ($existinguser->matrnr) {
                            $mform->addElement('html', '<div class="col-' . $col . '"> - </div>');
                        }

                    }

                    $mform->addElement('html', '<div class="col-' . $col . '">' .
                        get_string($existinguser->state, "mod_exammanagement", ['systemname' => $systemname]) . '</div></div>');
                }

                $mform->addElement('html', '</div></div>');
            }

            if ($allparticipants['newMoodleParticipants']) {

                $mform->addElement('html', '<div class="panel panel-success exammanagement_panel">');
                $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="new">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title"><span id="selectedGroupThreeCount" class="exammanagement_pure">'.count($allparticipants['newMoodleParticipants']).'</span>/'. count($allparticipants['newMoodleParticipants']) . ' ' . get_string("newmatrnr", "mod_exammanagement"). '</h3>');
                $mform->addElement('html', '<span class="collapse.show new_minimize pull-right" title="' . get_string("minimize_phase", "mod_exammanagement"). '" aria-label="' . get_string("minimize_phase", "mod_exammanagement"). '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
                $mform->addElement('html', '<span class="collapse new_maximize pull-right" title="' . get_string("maximize_phase", "mod_exammanagement"). '" aria-label="' . get_string("maximize_phase", "mod_exammanagement"). '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');

                $mform->addElement('html', '<div class="panel-body new_body">');

                $mform->addElement('html', '<div class="row"><div class="col-1"></div><div class="col-'.$col.'"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-'.$littlecol.'"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div>');

                if ($coursegroups) {
                    $mform->addElement('html', '<div class="col-'.$col.'"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div>');
                }

                $mform->addElement('html', '<div class="col-'.$col.'"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');

                $mform->addElement('html', '<div class="row"><div class="col-1"></div><div class="col-'.$col.'">');
                $mform->addElement('advcheckbox', 'checkall_new', get_string("select_deselect_all", "mod_exammanagement"), null, array('group' => 3, 'id' => 'checkboxgroup3'));
                $mform->setDefault('checkall_new', true);
                $mform->addElement('html', '</div><div class="col-'.$littlecol.'"></div><div class="col-'.$col.'"></div><div class="col-'.$col.'"></div></div>');

                foreach ($allparticipants['newMoodleParticipants'] as $newmoodleuser) { // New moodle user.

                    $mform->addElement('html', '<div class="row text-success">');
                    $mform->addElement('html', '<div class="col-1"> # '.$newmoodleuser->line);
                    $mform->addElement('html', '</div><div class="col-'.$col.'">');

                    global $OUTPUT;

                    $moodleuser = $userobj->getmoodleuser($newmoodleuser->moodleuserid);

                    $courseid = $exammanagementinstanceobj->getCourse()->id;

                    $image = $OUTPUT->user_picture($moodleuser, array('courseid' => $courseid, 'link' => true, 'includefullname' => true));

                    $mform->addElement('advcheckbox', 'participants[mid_' . $newmoodleuser->moodleuserid . '-' . $newmoodleuser->headerid.']', $image, null, array('group' => 3));

                    $mform->addElement('html', '</div><div class="col-'.$littlecol.'">'.$newmoodleuser->matrnr.'</div>');

                    $courseid = $exammanagementinstanceobj->getCourse()->id;

                    if ($coursegroups) {
                        if ($newmoodleuser->moodleuserid) {
                            $usergroups = groups_get_user_groups($courseid, $newmoodleuser->moodleuserid);
                            $groupnames = false;

                            foreach ($usergroups as $groupskey => $value) {
                                if ($value) {
                                    foreach ($value as $groupskey2 => $groupid) {
                                        if (!$groupnames) {
                                            $groupnames = '<strong><a href="' . new moodle_url('/group/index.php', ['id' => $courseid, 'group' => $groupid]).'">' .
                                                groups_get_group_name($groupid).'</a></strong>';
                                        } else {
                                            $groupnames .= ', <strong><a href="' . new moodle_url('/group/index.php', ['id' => $courseid, 'group' => $groupid]).'">' .
                                                groups_get_group_name($groupid).'</a></strong> ';
                                        }
                                    }
                                } else {
                                    $groupnames = '-';
                                    break;
                                }
                            }
                        }

                        $mform->addElement('html', '<div class="col-'.$col.'">'.$groupnames.'</div>');
                    }

                    $mform->addElement('html', '<div class="col-'.$col.'">'.get_string('state_newmatrnr', "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');

            }

            if ($allparticipants['newMoodleParticipants'] || $allparticipants['oddParticipants'] || $allparticipants['deletedParticipants']) {

                $maxbytes = $CFG->maxbytes;

                $mform->addElement('html', '<div class="hidden">');
                $mform->addElement('filepicker', 'participantslist_text', get_string("import_from_text_file", "mod_exammanagement"), null, array('maxbytes' => $maxbytes, 'accepted_types' => '.txt'));
                $mform->addElement('html', '</div>');

                $this->add_action_buttons(true, get_string("add_to_exam", "mod_exammanagement"));
            } else {
                $mform->addElement('html', '<div class="row"><span class="col-sm-5"></span><a href="'.new moodle_url('/mod/exammanagement/view.php', ['id' => $this->_customdata['id']]).'" class="btn btn-primary">'.get_string("cancel", "mod_exammanagement").'</a></div>');
            }

            $mform->addElement('html', '</div>');

        } else {

            // Add Participants from File.
            $maxbytes = $CFG->maxbytes;

            $mform->addElement('filemanager', 'participantslists', get_string('import_from_text_file', 'mod_exammanagement'), null,
                    array('subdirs' => 0, 'maxbytes' => $maxbytes, 'areamaxbytes' => 10485760, 'maxfiles' => 10,
                          'accepted_types' => '.txt', 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL));
            $mform->addRule('participantslists', get_string('err_nofile', 'mod_exammanagement'), 'required', 'client');

            $this->add_action_buttons(true, get_string("read_file", "mod_exammanagement"));
        }
    }

    /**
     * Custom validation for the form.
     *
     * @param object $data The data from the form.
     * @param object $files The files from the form.
     * @return object $errors The errors.
     */
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
