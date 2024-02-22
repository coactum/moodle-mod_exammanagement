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
 * The form for converting exam participants to a moodle group for an exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_exammanagement\local\helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

/**
 * The form for converting exam participants to a moodle group for an exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_exammanagement_converttogroup_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        global $PAGE, $OUTPUT;

        // Remove col-md classes for better layout.
        $PAGE->requires->js_call_amd('mod_exammanagement/remove_cols', 'removecols');
        // Call jquery for updating count if checkboxes are checked.
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'init');
        // Call jquery for checking all checkboxes via following checkbox.
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'enablecb');
        // Call jquery for toogling sections.
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'togglesection');

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        if (isset($this->_customdata['moodleparticipants'])) {
            $moodleparticipants = $this->_customdata['moodleparticipants'];
        } else {
            $moodleparticipants = false;
        }

        if (isset($this->_customdata['nonemoodleparticipants'])) {
            $nonemoodleparticipants = $this->_customdata['nonemoodleparticipants'];
        } else {
            $nonemoodleparticipants = false;
        }

        $courseid = $this->_customdata['courseid'];
        $courseparticipantids = helper::getcourseparticipantsids($this->_customdata['id']);

        if ($moodleparticipants) {

            if ($courseparticipantids) {
                foreach ($moodleparticipants as $key => $participant) {
                    if (!in_array($participant->moodleuserid, $courseparticipantids)) {
                        $participant->nocourse = true;
                        unset($moodleparticipants[$key]);

                        if (!$nonemoodleparticipants) {
                            $nonemoodleparticipants = [];
                        }
                        array_push($nonemoodleparticipants, $participant);
                    }
                }
            } else {
                if ($nonemoodleparticipants) {
                    $nonemoodleparticipants = array_merge($nonemoodleparticipants, $moodleparticipants);
                } else {
                    $nonemoodleparticipants = $moodleparticipants;
                }

                $moodleparticipants = false;
            }
        }

        if ($moodleparticipants || $nonemoodleparticipants) {

            // Determine if course groups are set.
            $groups = groups_get_all_groups($courseid);

            if (count($groups) > 0) {
                $coursegroups = true;
                $bigcol = 4;
                $col = 3;
                $littlecol = 2;
            } else {
                $coursegroups = false;
                $bigcol = 5;
                $col = 4;
                $littlecol = 3;
            }
            $selectoptions = ['new_group' => get_string('new_group', 'mod_exammanagement')];

            // Output participants.

            $mform->addElement('html', '<div class="exammanagement_overview">');

            if ($moodleparticipants) {

                if ($coursegroups) {
                    foreach ($groups as $group) {
                        $selectoptions[$group->id] = $group->name;
                    }
                }

                $select = $mform->addElement('select', 'groups', get_string('group', 'mod_exammanagement'), $selectoptions);
                $select->setSelected('new_group');

                $attributes = ['size' => '25'];
                $mform->addElement('text', 'groupname', get_string('groupname', 'mod_exammanagement'), $attributes);
                $mform->setType('groupname', PARAM_TEXT);
                $mform->hideif ('groupname', 'groups', 'neq', 'new_group');

                $attributes = ['size' => '40'];
                $mform->addElement('text', 'groupdescription', get_string('groupdescription', 'mod_exammanagement'), $attributes);
                $mform->setType('groupdescription', PARAM_TEXT);
                $mform->hideif ('groupdescription', 'groups', 'neq', 'new_group');

                $mform->addElement('html', '<div class="panel panel-success exammanagement_panel">');
                $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="new">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">
                    <span id="selectedGroupOneCount" class="exammanagement_pure">' .
                    count($moodleparticipants) . '</span>/' . count($moodleparticipants) . ' ' .
                    get_string("participants_convertable", "mod_exammanagement") . '</h3>');
                $mform->addElement('html', '<span class="collapse.show new_minimize float-right" title="' .
                    get_string("minimize_phase", "mod_exammanagement") . '" aria-label="' .
                    get_string("minimize_phase", "mod_exammanagement") . '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
                $mform->addElement('html', '<span class="collapse new_maximize float-right" title="' .
                    get_string("maximize_phase", "mod_exammanagement"). '" aria-label="' .
                    get_string("maximize_phase", "mod_exammanagement") .
                    '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');

                $mform->addElement('html', '<div class="panel-body new_body">');

                $mform->addElement('html', '<div class="row"><div class="col-' . $col . '"><h4>' .
                    get_string("participants", "mod_exammanagement") .
                    '</h4></div><div class="col-' . $littlecol . '"><h4>' .
                    get_string("matriculation_number", "mod_exammanagement") . '</h4></div>');

                if ($coursegroups) {
                    $mform->addElement('html', '<div class="col-' . $bigcol . '"><h4>' .
                        get_string("course_groups", "mod_exammanagement") . '</h4></div>');
                }

                $mform->addElement('html', '<div class="col-' . $col . '"><h4>' .
                    get_string("import_state", "mod_exammanagement") . '</h4></div></div>');

                $mform->addElement('html', '<div class="row"><div class="col-' . $col . '">');
                $mform->addElement('advcheckbox', 'checkall', get_string("select_deselect_all", "mod_exammanagement"), null,
                    ['group' => 1, 'id' => 'checkboxgroup1']);
                $mform->setDefault('checkall', true);
                $mform->addElement('html', '</div><div class="col-' . $littlecol . '"></div><div class="col-' . $col .
                    '"></div><div class="col-' . $col . '"></div></div>');

                foreach ($moodleparticipants as $participant) {

                    $mform->addElement('html', '<div class="row text-success">');
                    $mform->addElement('html', '<div class="col-' . $col . '">');

                    $moodleuser = helper::getmoodleuser($participant->moodleuserid);

                    $image = $OUTPUT->user_picture($moodleuser,
                        ['courseid' => $courseid, 'link' => true, 'includefullname' => true]);

                    $mform->addElement('advcheckbox', 'participants[' . $participant->moodleuserid . ']', $image, null,
                        ['group' => 1]);

                    $mform->addElement('html', '</div><div class="col-' . $littlecol . '">' . $participant->matrnr . '</div>');

                    if ($coursegroups) {
                        if ($participant->moodleuserid) {
                            $usergroups = groups_get_user_groups($courseid, $participant->moodleuserid);
                            $groupnames = false;

                            foreach ($usergroups as $groupskey => $value) {
                                if ($value) {
                                    foreach ($value as $groupskey2 => $groupid) {
                                        $url = new moodle_url('/group/index.php', ['id' => $courseid, 'group' => $groupid]);
                                        if (!$groupnames) {
                                            $groupnames = '<strong><a href="' . $url . '">' .
                                                groups_get_group_name($groupid) . '</a></strong>';
                                        } else {
                                            $groupnames .= ', <strong><a href="' . $url . '">' .
                                                groups_get_group_name($groupid) . '</a></strong> ';
                                        }
                                    }
                                } else {
                                    $groupnames = '-';
                                    break;
                                }
                            }
                        }

                        $mform->addElement('html', '<div class="col-' . $bigcol . '">' . $groupnames . '</div>');
                    }

                    $mform->addElement('html', '<div class="col-' . $col . '">' .
                        get_string('state_convertable_group', "mod_exammanagement") . '</div></div>');
                }

                $mform->addElement('html', '</div></div>');

            }

            if ($nonemoodleparticipants) {

                $count = count($nonemoodleparticipants);

                $mform->addElement('html', '<div class="panel panel-info exammanagement_panel">');
                $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="existing">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . $count .
                    ' ' . get_string("participants_not_convertable", "mod_exammanagement",
                    ['systemname' => helper::getmoodlesystemname()]) . '</h3>');
                $mform->addElement('html', '<span class="collapse.show existing_minimize float-right" title="' .
                    get_string("minimize_phase", "mod_exammanagement") . '" aria-label="' .
                    get_string("minimize_phase", "mod_exammanagement") .
                    '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
                $mform->addElement('html', '<span class="collapse existing_maximize float-right" title="' .
                    get_string("maximize_phase", "mod_exammanagement") . '" aria-label="' .
                    get_string("maximize_phase", "mod_exammanagement") .
                    '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');

                $mform->addElement('html', '<div class="panel-body existing_body">');

                $mform->addElement('html', '<div class="row"><div class="col-' . $col . '"><h4>' .
                    get_string("participants", "mod_exammanagement") . '</h4></div><div class="col-' .
                    $littlecol . '"><h4>' . get_string("matriculation_number", "mod_exammanagement") .
                    '</h4></div>');

                if ($coursegroups) {
                    $mform->addElement('html', '<div class="col-' . $bigcol . '"><h4>' .
                        get_string("course_groups", "mod_exammanagement") . '</h4></div>');
                }

                $mform->addElement('html', '<div class="col-' . $col . '"><h4>' .
                    get_string("import_state", "mod_exammanagement") . '</h4></div></div>');

                foreach ($nonemoodleparticipants as $participant) {

                    $mform->addElement('html', '<div class="row text-info">');

                    $mform->addElement('html', '<div class="col-' . $col . '">' .
                        $participant->firstname . ' ' . $participant->lastname . '</div>');

                    if ($participant->matrnr) {
                        $mform->addElement('html', '<div class="col-' . $littlecol . '">' .
                            $participant->matrnr . '</div>');
                    } else {
                        $mform->addElement('html', '<div class="col-' . $littlecol . '">-</div>');
                    }

                    if ($coursegroups) {

                        if ($participant->moodleuserid) {
                            $usergroups = groups_get_user_groups($courseid, $participant->moodleuserid);
                            $groupnames = false;

                            foreach ($usergroups as $groupskey => $value) {
                                if ($value) {
                                    foreach ($value as $groupskey2 => $groupid) {
                                        $url = new moodle_url('/group/index.php', ['id' => $courseid, 'group' => $groupid]);

                                        if (!$groupnames) {
                                            $groupnames = '<strong><a href="' . $url . '">' .
                                                groups_get_group_name($groupid) . '</a></strong>';
                                        } else {
                                            $groupnames .= ', <strong><a href="' . $url . '">' .
                                                groups_get_group_name($groupid) . '</a></strong> ';
                                        }
                                    }
                                } else {
                                    $groupnames = '-';
                                    break;
                                }
                            }

                            $mform->addElement('html', '<div class="col-' . $bigcol . '">' . $groupnames . '</div>');
                        } else if ($participant->matrnr) {
                            $mform->addElement('html', '<div class="col-' . $bigcol . '"> - </div>');
                        }

                    }

                    if (isset($participant->nocourse) || !$courseparticipantids) {
                        $state = get_string('state_not_convertable_group_course', "mod_exammanagement");
                    } else {
                        $state = get_string('state_not_convertable_group_moodle', "mod_exammanagement",
                            ['systemname' => helper::getmoodlesystemname()]);
                    }

                    $mform->addElement('html', '<div class="col-' . $col . '">' . $state . '</div></div>');
                }

                $mform->addElement('html', '</div></div>');
            }

            if ($moodleparticipants) {
                $this->add_action_buttons(true, get_string("convert_to_group", "mod_exammanagement"));
            } else {
                $mform->addElement('html', '<div class="row"><span class="col-sm-5"></span><a href="' .
                    new moodle_url('/mod/exammanagement/view.php', ['id' => $this->_customdata['id']]) .
                    '" class="btn btn-primary">' . get_string("cancel", "mod_exammanagement") . '</a></div>');
            }

            $mform->addElement('html', '</div>');

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

        $errors = [];

        if (isset($data['participants'])) {
            foreach ($data['participants'] as $participantid => $checked) {

                if (!preg_match("/^[a-zA-Z0-9_\-]+$/", $participantid)) {
                    $errors['participants[' . $participantid . ']'] =
                        get_string('err_invalidcheckboxid_participants', 'mod_exammanagement');
                }
            }
        }

        if ($data['groups'] === 'new_group' && !$data['groupname']) {
            $errors['groupname'] = get_string('err_filloutfield', 'mod_exammanagement');
        }

        if ($data['groups'] === 'new_group') {
            if ($coursegroups = groups_get_all_groups($this->_customdata['courseid'])) {

                $groupnametaken = array_filter($coursegroups, function($group) use ($data) {
                    return $group->name == $data['groupname'];
                });

                if ($groupnametaken) {
                    $errors['groupname'] = get_string('err_groupname_taken', 'mod_exammanagement');
                }
            }
        }

        return $errors;
    }
}
