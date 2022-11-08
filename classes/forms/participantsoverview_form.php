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
 * The form for the participants overview for mod_exammanagement.
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
use mod_exammanagement\output\exammanagement_pagebar;

use moodleform;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/User.php');
require_once(__DIR__.'/../general/MoodleDB.php');
require_once(__DIR__.'/../general/Moodle.php');

/**
 * The form for the participants overview for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class participantsoverview_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {

        global $PAGE, $OUTPUT;

        $exammanagementinstanceobj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $userobj = User::getInstance($this->_customdata['id'], $this->_customdata['e'], $exammanagementinstanceobj->getCm()->instance);
        $moodleobj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $moodledbobj = MoodleDB::getInstance();

        if ($exammanagementinstanceobj->getTaskCount()) {
            $tasks = $exammanagementinstanceobj->getTasks();
        } else {
            $tasks = false;
        }

        $jsargs = array('tasks' => (array) $tasks);

        $PAGE->requires->js_call_amd('mod_exammanagement/participants_overview', 'init', $jsargs); // Call jquery for tracking input value changes and creating input type number

        $mform = $this->_form;

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<div class="d-flex justify-content-between"><h3>'.get_string("participantsOverview", "mod_exammanagement"));

        if ($helptextsenabled) {
            if ($exammanagementinstanceobj->moduleinstance->misc === null) {
                $mform->addElement('html', $OUTPUT->help_icon('participantsOverview', 'mod_exammanagement', ''));
            } else {
                $mform->addElement('html', $OUTPUT->help_icon('participantsOverview_grades', 'mod_exammanagement', ''));
            }
        }

        $mform->addElement('html', '</h3><div>');

        if (!isset($this->_customdata['epm'])) {
            if ($exammanagementinstanceobj->moduleinstance->misc === null) {
                $mform->addElement('html', '<a href="participantsOverview.php?id=' . $this->_customdata['id'] . '&epm=' . true . '&page=' . $this->_customdata['pagenr'] . '" class="btn btn-primary pull-right" title="'.get_string("edit_results_and_boni", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("edit_results_and_boni", "mod_exammanagement").'</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>');
            } else {
                $mform->addElement('html', '<a href="participantsOverview.php?id=' . $this->_customdata['id'] . '&epm=' . true . '&page=' . $this->_customdata['pagenr'] . '" class="btn btn-primary pull-right" title="'.get_string("edit_grades", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("edit_grades", "mod_exammanagement").'</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>');
            }
        }

        $mform->addElement('html', '</div></div>');

        $mform->addElement('html', '<p>'.get_string("participants_overview_text", "mod_exammanagement").'</p>');

        $allparticipants = $userobj->getExamParticipants(array('mode' => 'all'), array('matrnr'));
        $participants = $userobj->getExamParticipants(array('mode' => 'all'), array('matrnr'), 'name', true, $this->_customdata['pagenr']);
        $examrooms = json_decode($exammanagementinstanceobj->moduleinstance->rooms);
        $gradingscale = $exammanagementinstanceobj->getGradingscale();

        $pagebar = new exammanagement_pagebar($this->_customdata['id'], 'participantsOverview.php?id=' . $this->_customdata['id'], sesskey(), $exammanagementinstanceobj->get_pagebar($allparticipants, $this->_customdata['pagenr']), $exammanagementinstanceobj->get_pagecountoptions(),  count($participants), count($allparticipants));
        $mform->addElement('html', $OUTPUT->render($pagebar));

        $mform->addElement('html', '<table class="table table-striped exammanagement_table" id="0">');

        $mform->addElement('html', '<thead class="exammanagement_tableheader exammanagement_brand_backgroundcolor">');
        $mform->addElement('html', '<th scope="col">#</th><th scope="col">'.get_string("firstname", "mod_exammanagement").'</th><th scope="col">'.get_string("lastname", "mod_exammanagement").'</th><th scope="col">'.get_string("matriculation_number", "mod_exammanagement").'</th>');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        if (!isset($this->_customdata['epm'])) { // Participants can not be edited.
            $mform->addElement('hidden', 'epm', false);
            $mform->setType('epm', PARAM_INT);

            if ($exammanagementinstanceobj->moduleinstance->misc === null) {

                $mform->addElement('html', '<th scope="col" class="exammanagement_table_width_room">'.get_string("room", "mod_exammanagement").'</th><th scope="col" class="exammanagement_table_width_place">'.get_string("place", "mod_exammanagement").'</th>');

                $mform->addElement('html', '<th scope="col">'.get_string("points", "mod_exammanagement").'</th><th scope="col">'.get_string("totalpoints", "mod_exammanagement").'</th>');

                $mform->addElement('html', '<th scope="col">'.get_string("bonuspoints", "mod_exammanagement").'</th>');

                $mform->addElement('html', '<th scope="col">'.get_string("totalpoints_with_bonuspoints", "mod_exammanagement").'</th>');

                $mform->addElement('html', '<th scope="col">'.get_string("result", "mod_exammanagement").'</th>');

                $mform->addElement('html', '<th scope="col">'.get_string("bonussteps", "mod_exammanagement").'</th>');

                $mform->addElement('html', '<th scope="col">'.get_string("resultwithbonus", "mod_exammanagement").'</th>');

            } else {
                $mform->addElement('html', '<th scope="col">'.get_string("grading_points", "mod_exammanagement").'</th>');

                if ($exammanagementinstanceobj->getGradingscale()) {
                    $mform->addElement('html', '<th scope="col">'.get_string("result_based_of_grades", "mod_exammanagement").'</th>');
                }
            }

        } else { // Participants can be edited.
            $mform->addElement('hidden', 'epm', true);
            $mform->setType('epm', PARAM_INT);

            $mform->addElement('hidden', 'page');
            $mform->setType('page', PARAM_INT);
            $mform->setDefault('page', $this->_customdata['pagenr']);

            if ($exammanagementinstanceobj->moduleinstance->misc === null) {
                $mform->addElement('html', '<th scope="col">'.get_string("points", "mod_exammanagement").'</th>');

                $mform->addElement('html', '<th scope="col">'.get_string("bonuspoints", "mod_exammanagement").'</th>');

                $mform->addElement('html', '<th scope="col">'.get_string("bonussteps", "mod_exammanagement").'</th>');

                $mform->addElement('html', '<th scope="col">'.get_string("exam_state", "mod_exammanagement").'</th>');

            } else {
                $mform->addElement('html', '<th scope="col">'.get_string("grading_points", "mod_exammanagement").'</th>');
            }

        }

        $mform->addElement('html', '</thead>');

        $mform->addElement('html', '<tbody>');

        if ($participants) {

            $i = $exammanagementinstanceobj->pagecount * ($this->_customdata['pagenr'] - 1) + 1;

            foreach ($participants as $key => $participant) {

                if (isset($participant->roomname)) {
                    $room = $participant->roomname;
                } else {
                    $room = '-';
                }

                if (isset($participant->place)) {
                    $place = $participant->place;
                } else {
                    $place = '-';
                }

                $totalpoints = false;

                $state = $userobj->getExamState($participant);

                if (!$state) {
                    $state = 'not_set';
                }

                $exampoints = array_values((array) json_decode($participant->exampoints));

                $totalpoints = $userobj->calculatePoints($participant);
                $totalpointsdisplay = $exammanagementinstanceobj->formatNumberForDisplay($totalpoints);
                $totalpointswithbonus = $userobj->calculatePoints($participant, true);
                $totalpointswithbonusdisplay = $exammanagementinstanceobj->formatNumberForDisplay($totalpointswithbonus);

                if (!isset($this->_customdata['epm']) || $this->_customdata['epm'] === 0 ) { // Show users.
                    $mform->addElement('html', '<tr>');
                    $mform->addElement('html', '<th scope="row" id="'.$i.'">'.$i.'</th>');
                    $mform->addElement('html', '<td>'.$participant->firstname.'</td>');
                    $mform->addElement('html', '<td>'.$participant->lastname.'</td>');
                    $mform->addElement('html', '<td>'.$participant->matrnr.'</td>');

                    if ($exammanagementinstanceobj->moduleinstance->misc === null) {
                        $mform->addElement('html', '<td>'.$room.'</td>');
                        $mform->addElement('html', '<td>'.$place.'</td>');

                        $mform->addElement('html', '<td>');

                        if ($tasks) {
                            $mform->addElement('html', '<table class="table-sm"><tr>');

                            foreach ($tasks as $tasknumber => $taskmaxpoints) {
                                $mform->addElement('html', '<th class="exammanagement_table_with">'.$tasknumber.'</th>');
                            }

                            $mform->addElement('html', '</tr><tr>');

                            foreach ($tasks as $tasknumber => $taskmaxpoints) {
                                if (isset($exampoints[$tasknumber - 1])) {
                                    $mform->addElement('html', '<td>'.$exammanagementinstanceobj->formatNumberForDisplay($exampoints[$tasknumber - 1]).'</td>');
                                } else {
                                    $mform->addElement('html', '<td> - </td>');
                                }
                            }

                            $mform->addElement('html', '</tr></table>');
                        } else {
                            $mform->addElement('html', '<a href="configureTasks.php?id='.$this->_customdata['id'].'" title="'.get_string("configure_tasks", "mod_exammanagement").'"><i class="fa fa-2x fa-info-circle text-warning"></i></a>');
                        }

                        $mform->addElement('html', '</td>');

                        // totalpoints
                        $mform->addElement('html', '<td>'.$totalpointsdisplay.'</td>');

                        // bonuspoints
                        if ($userobj->getEnteredBonusCount('points')) {
                            if (isset($participant->bonuspoints)) {
                                $mform->addElement('html', '<td>'.$exammanagementinstanceobj->formatNumberForDisplay(number_format($participant->bonuspoints, 2)).'</td>');
                            } else {
                                $mform->addElement('html', '<td>-</td>');
                            }
                        } else {
                            $mform->addElement('html', '<td>-</td>');
                        }

                        // totalpoints with bonuspoints
                        if ($userobj->getEnteredBonusCount('points')) {
                            $mform->addElement('html', '<td>'. $totalpointswithbonusdisplay .'</td>');
                        } else {
                            $mform->addElement('html', '<td>-</td>');
                        }

                        // result
                        if ($gradingscale) {
                            $result = $userobj->calculateResultGrade($totalpointswithbonus);
                            $mform->addElement('html', '<td>'.$exammanagementinstanceobj->formatNumberForDisplay($result).'</td>');
                        } else {
                            $mform->addElement('html', '<td><a href="configureGradingscale.php?id='.$this->_customdata['id'].'" title="'.get_string("configure_gradingscale", "mod_exammanagement").'"><i class="fa fa-2x fa-info-circle text-warning"></i></a></td>');
                        }

                        if ($userobj->getEnteredBonusCount('steps')) {
                            if (isset($participant->bonussteps)) {
                                $mform->addElement('html', '<td>'.$participant->bonussteps);

                                if (current_language() === 'de') {
                                    $separator = ',';
                                } else {
                                    $separator = '.';
                                }

                                switch ($participant->bonussteps) {

                                    case 0:
                                        break;
                                    case 1:
                                        $mform->addElement('html', ' (= 0'.$separator.'3)');
                                        break;
                                    case 2:
                                        $mform->addElement('html', ' (= 0'.$separator.'7)');
                                        break;
                                    case 3:
                                        $mform->addElement('html', ' (= 1'.$separator.'0)');
                                        break;
                                }

                                $mform->addElement('html', '</td>');
                            } else {
                                $mform->addElement('html', '<td>-</td>');
                            }
                        } else {
                            $mform->addElement('html', '<td>-</td>');
                        }

                        if ($gradingscale) {
                            $mform->addElement('html', '<td>'.$exammanagementinstanceobj->formatNumberForDisplay($userobj->calculateResultGrade($totalpointswithbonus, $participant->bonussteps)).'</td>');
                        } else {
                            $mform->addElement('html', '<td><a href="configureGradingscale.php?id='.$this->_customdata['id'].'" title="'.get_string("configure_gradingscale", "mod_exammanagement").'"><i class="fa fa-2x fa-info-circle text-warning"></i></a></td>');
                        }
                    } else {
                        // bonuspoints
                        if ($userobj->getEnteredBonusCount('points')) {
                            if (isset($participant->bonuspoints)) {
                                $mform->addElement('html', '<td>'.$exammanagementinstanceobj->formatNumberForDisplay(number_format($participant->bonuspoints, 2)).'</td>');
                            } else {
                                $mform->addElement('html', '<td>-</td>');
                            }
                        } else {
                            $mform->addElement('html', '<td>-</td>');
                        }

                        // result
                        if ($gradingscale) {
                            $result = $userobj->calculateResultGrade($participant->bonuspoints);
                            $mform->addElement('html', '<td>'.$exammanagementinstanceobj->formatNumberForDisplay($result).'</td>');
                        }
                    }

                } else if (isset($this->_customdata['epm']) && $this->_customdata['epm'] != 0) { // Users can be edited.
                    $mform->addElement('html', '<tr>');
                    $mform->addElement('html', '<th scope="row" id="'.$i.'">'.$i.'</th>');
                    $mform->addElement('html', '<td>'.$participant->firstname.'</td>');
                    $mform->addElement('html', '<td>'.$participant->lastname.'</td>');
                    $mform->addElement('html', '<td>'.$participant->matrnr.'</td>');

                    if ($exammanagementinstanceobj->moduleinstance->misc === null) {

                        $mform->addElement('html', '<td class="p-1">');

                        if ($tasks) {

                            $mform->addElement('html', '<table class="table-sm exammanagement_table_edit_tasks"><tr>');

                            $mform->addElement('html', '<th>'.get_string("nr", "mod_exammanagement").'</th>');

                            foreach ($tasks as $tasknumber => $taskmaxpoints) {
                                $mform->addElement('html', '<th>'.$tasknumber.'</th>');
                            }

                            $mform->addElement('html', '</tr><tr>');

                            $mform->addElement('html', '<td class="exammanagement_vertical_align_middle"><strong>'.get_string("points", "mod_exammanagement").'</strong></td>');

                            foreach ($tasks as $tasknumber => $taskmaxpoints) {

                                $mform->addElement('html', '<td>');
                                $mform->addElement('text', 'points['.$participant->id.']['.$tasknumber.']', '');
                                $mform->setType('points['.$participant->id.']['.$tasknumber.']', PARAM_FLOAT);

                                if (isset($exampoints[$tasknumber - 1])) {
                                    $mform->setDefault('points['.$participant->id.']['.$tasknumber.']', $exampoints[$tasknumber - 1]);
                                }
                                $mform->addElement('html', '</td>');
                            }

                            $mform->addElement('html', '</tr><tr>');

                            $mform->addElement('html', '<td class="p-0 text-center"><strong>'.get_string("max", "mod_exammanagement").'</strong></td>');

                            foreach ($tasks as $tasknumber => $taskmaxpoints) {

                                $mform->addElement('html', '<td class="p-0 text-center">');
                                $mform->addElement('html', $exammanagementinstanceobj->formatNumberForDisplay($taskmaxpoints));
                                $mform->addElement('html', '</td>');
                            }

                            $mform->addElement('html', '</tr></table>');
                        } else {
                            $mform->addElement('html', '<a href="configureTasks.php?id='.$this->_customdata['id'].'" title="'.get_string("configure_tasks", "mod_exammanagement").'"><i class="fa fa-2x fa-info-circle text-warning"></i></a>');
                        }

                        $mform->addElement('html', '</td>');

                        $mform->addElement('html', '<td>');
                        $mform->addElement('text', 'bonuspoints['.$participant->id.']', '');
                        $mform->setType('bonuspoints['.$participant->id.']', PARAM_FLOAT);
                        $mform->addElement('html', '</td>');

                        if (isset($participant->bonuspoints)) {
                            $mform->setDefault('bonuspoints['.$participant->id.']', $participant->bonuspoints);
                        } else {
                            $mform->setDefault('bonuspoints['.$participant->id.']', null);
                        }

                        $mform->addElement('html', '<td>');
                        $select = $mform->addElement('select', 'bonussteps['.$participant->id.']', '', array('-' => '-', '0' => 0, '1' => 1, '2' => 2, '3' => 3));
                        $mform->addElement('html', '</td>');

                        if (isset($participant->bonussteps)) {
                            $select->setSelected($participant->bonussteps);
                        }

                        if ($exammanagementinstanceobj->getTaskCount()) {
                            $mform->addElement('html', '<td>');
                            $select = $mform->addElement('select', 'state['.$participant->id.']', '', array('not_set' => '-', 'normal' => get_string('normal', 'mod_exammanagement'), 'nt' => get_string('nt', 'mod_exammanagement'), 'fa' => get_string('fa', 'mod_exammanagement'), 'ill' => get_string('ill', 'mod_exammanagement')));
                            $select->setSelected($state);
                            $mform->addElement('html', '</td>');
                        } else {
                            $mform->addElement('html', '<td>-</td>');
                        }
                    } else {
                        $mform->addElement('html', '<td>');
                        $mform->addElement('text', 'bonuspoints['.$participant->id.']', '');
                        $mform->setType('bonuspoints['.$participant->id.']', PARAM_FLOAT);
                        $mform->addElement('html', '</td>');
                    }

                    $mform->addElement('hidden', 'bonuspoints_entered['.$participant->id.']', '');
                    $mform->setType('bonuspoints_entered['.$participant->id.']', PARAM_INT);

                    if (isset($participant->bonuspoints)) {
                        $mform->setDefault('bonuspoints['.$participant->id.']', $participant->bonuspoints);
                        $mform->setDefault('bonuspoints_entered['.$participant->id.']', 1);
                    } else {
                        $mform->setDefault('bonuspoints['.$participant->id.']', null);
                        $mform->setDefault('bonuspoints_entered['.$participant->id.']', 0);
                    }
                }

                $mform->addElement('html', '</tr>');

                $i++;

            }
        } else {
            $mform->addElement('html', get_string("no_participants_added", "mod_exammanagement"));
        }

        $mform->addElement('html', '</tbody></table>');

        if (isset($this->_customdata['epm'])) {
            $this->add_action_buttons(true, get_string("save_changes", "mod_exammanagement"));
        } else {
            $mform->addElement('html', '<div class="row"><span class="col-sm-5"></span><a href="'.$exammanagementinstanceobj->getExammanagementUrl("view", $this->_customdata['id']).'" class="btn btn-primary">'.get_string("cancel", "mod_exammanagement").'</a></div>');
        }

    }

    // Custom validation should be added here.
    public function validation($data, $files) {
        $errors = array();
        return $errors;
    }
}
