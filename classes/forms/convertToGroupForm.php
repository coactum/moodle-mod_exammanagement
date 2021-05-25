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
 * class containing convertToGroupForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2020
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;

use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\User;
use mod_exammanagement\general\Moodle;
use mod_exammanagement\general\MoodleDB;
use moodleform;
use stdclass;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/User.php');
require_once(__DIR__.'/../general/Moodle.php');
require_once(__DIR__.'/../general/MoodleDB.php');

class convertToGroupForm extends moodleform{

    //Add elements to form
    public function definition(){
        global $PAGE, $CFG, $OUTPUT;

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e'], $ExammanagementInstanceObj->getCm()->instance);
        $MoodleObj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $MoodleDBObj = MoodleDB::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $PAGE->requires->js_call_amd('mod_exammanagement/remove_cols', 'remove_cols'); //remove col-md classes for better layout
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'init'); //call jquery for updating count if checkboxes are checked
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'enable_cb'); //call jquery for checking all checkboxes via following checkbox
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'togglesection'); //call jquery for toogling sections

        $mform = $this->_form; // Don't forget the underscore!

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<h3>'. get_string("convertToGroup", "mod_exammanagement"));

        if($helptextsenabled){
            $mform->addElement('html', $OUTPUT->help_icon('convertToGroup', 'mod_exammanagement', ''));
        }

        $mform->addElement('html', '</h3>');

        $mform->addElement('html', '<p>'.get_string('convert_to_group_str', 'mod_exammanagement', ['systemname' => $ExammanagementInstanceObj->getMoodleSystemName()]).'</p>');

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        if(isset($this->_customdata['moodleParticipants'])){
            $moodleParticipants = $this->_customdata['moodleParticipants'];
        } else {
            $moodleParticipants = false;
        }

        if(isset($this->_customdata['noneMoodleParticipants'])){
            $noneMoodleParticipants = $this->_customdata['noneMoodleParticipants'];
        } else {
            $noneMoodleParticipants = false;
        }

        $courseParticipantsIDs = $UserObj->getCourseParticipantsIDs();

        if($moodleParticipants){

            if($courseParticipantsIDs){
                foreach ($moodleParticipants as $key => $participant) {
                    if(!in_array($participant->moodleuserid, $courseParticipantsIDs)){
                        $participant->nocourse = true;
                        unset($moodleParticipants[$key]);

                        if(!$noneMoodleParticipants){
                            $noneMoodleParticipants = array();
                        }
                        array_push($noneMoodleParticipants, $participant);
                    }
                }
            } else {
                if($noneMoodleParticipants){
                    $noneMoodleParticipants = array_merge($noneMoodleParticipants, $moodleParticipants);
                } else {
                    $noneMoodleParticipants = $moodleParticipants;
                }

                $moodleParticipants = false;
            }
        }

        if($moodleParticipants || $noneMoodleParticipants){

            # determine if course groups are set #
            $groups = groups_get_all_groups($ExammanagementInstanceObj->getCourse()->id);

            if(count($groups) > 0){
                $courseGroups = true;
                $bigcol = 4;
                $col = 3;
                $littlecol = 2;
            } else {
                $courseGroups = false;
                $bigcol = 5;
                $col = 4;
                $littlecol = 3;
            }
            $selectOptions = array('new_group' => get_string('new_group', 'mod_exammanagement'));

            # output participants #

            $mform->addElement('html', '<div class="exammanagement_overview">');

            if($moodleParticipants){

                if($courseGroups){
                    foreach ($groups as $group){
                        $selectOptions[$group->id] = $group->name;
                    }
                }

                $select = $mform->addElement('select', 'groups', get_string('group', 'mod_exammanagement'), $selectOptions);
                $select->setSelected('new_group');

                $attributes = array('size'=>'25');
                $mform->addElement('text', 'groupname', get_string('groupname', 'mod_exammanagement'), $attributes);
                $mform->setType('groupname', PARAM_TEXT);
                $mform->hideIf('groupname', 'groups', 'neq', 'new_group');

                $attributes = array('size'=>'40');
                $mform->addElement('text', 'groupdescription', get_string('groupdescription', 'mod_exammanagement'), $attributes);
                $mform->setType('groupdescription', PARAM_TEXT);
                $mform->hideIf('groupdescription', 'groups', 'neq', 'new_group');

                $mform->addElement('html', '<div class="panel panel-success exammanagement_panel">');
                $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="new">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title"><span id="selectedGroupOneCount" class="exammanagement_pure">'.count($moodleParticipants).'</span>/'. count($moodleParticipants) . ' ' . get_string("participants_convertable", "mod_exammanagement"). '</h3>');
                $mform->addElement('html', '<span class="collapse.show new_minimize pull-right" title="' . get_string("minimize_phase", "mod_exammanagement"). '" aria-label="' . get_string("minimize_phase", "mod_exammanagement"). '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
                $mform->addElement('html', '<span class="collapse new_maximize pull-right" title="' . get_string("maximize_phase", "mod_exammanagement"). '" aria-label="' . get_string("maximize_phase", "mod_exammanagement"). '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');

                $mform->addElement('html', '<div class="panel-body new_body">');

                $mform->addElement('html', '<div class="row"><div class="col-'.$col.'"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-'.$littlecol.'"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div>');

                if($courseGroups){
                    $mform->addElement('html', '<div class="col-'.$bigcol.'"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div>');
                }

                $mform->addElement('html', '<div class="col-'.$col.'"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');

                $mform->addElement('html', '<div class="row"><div class="col-'.$col.'">');
                $mform->addElement('advcheckbox', 'checkall', get_string("select_deselect_all", "mod_exammanagement"), null, array('group' => 1, 'id' => 'checkboxgroup1'));
                $mform->setDefault('checkall', true);
                $mform->addElement('html', '</div><div class="col-'.$littlecol.'"></div><div class="col-'.$col.'"></div><div class="col-'.$col.'"></div></div>');

                foreach ($moodleParticipants as $participant) {

                    $mform->addElement('html', '<div class="row text-success">');
                    $mform->addElement('html', '<div class="col-'.$col.'">');

                    global $OUTPUT;

                    $moodleUser = $UserObj->getMoodleUser($participant->moodleuserid);

                    $courseid = $ExammanagementInstanceObj->getCourse()->id;

                    $image = $OUTPUT->user_picture($moodleUser, array('courseid' => $courseid, 'link' => true));
                    $link = '<strong><a href="'.$MoodleObj->getMoodleUrl('/user/view.php', $participant->moodleuserid, 'course', $courseid).'">'.$moodleUser->firstname.' '.$moodleUser->lastname.'</a></strong>';

                    $mform->addElement('advcheckbox', 'participants['.$participant->moodleuserid.']', $image.' '.$link, null, array('group' => 1));

                    $mform->addElement('html', '</div><div class="col-'.$littlecol.'">'.$participant->matrnr.'</div>');

                    $courseid = $ExammanagementInstanceObj->getCourse()->id;

                    if($courseGroups){
                        if($participant->moodleuserid){
                            $userGroups = groups_get_user_groups($courseid, $participant->moodleuserid);
                            $groupnames = false;

                            foreach ($userGroups as $groupskey => $value){
                                if ($value){
                                    foreach ($value as $groupskey2 => $groupid){
                                        if(!$groupnames){
                                            $groupnames = '<strong><a href="'.$MoodleObj->getMoodleUrl('/group/index.php', $courseid, 'group', $groupid).'">'.groups_get_group_name($groupid).'</a></strong>';
                                        } else {
                                            $groupnames .= ', <strong><a href="'.$MoodleObj->getMoodleUrl('/group/index.php', $courseid, 'group', $groupid).'">'.groups_get_group_name($groupid).'</a></strong> ';
                                        }
                                    }
                                } else{
                                    $groupnames = '-';
                                    break;
                                }
                            }
                        }

                        $mform->addElement('html', '<div class="col-'.$bigcol.'">'.$groupnames.'</div>');
                    }

                    $mform->addElement('html', '<div class="col-'.$col.'">'.get_string('state_convertable_group', "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');

            }

            if($noneMoodleParticipants){

                $count = count($noneMoodleParticipants);

                $mform->addElement('html', '<div class="panel panel-info exammanagement_panel">');
                $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="existing">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . $count . ' ' . get_string("participants_not_convertable", "mod_exammanagement", ['systemname' => $ExammanagementInstanceObj->getMoodleSystemName()]). '</h3>');
                $mform->addElement('html', '<span class="collapse.show existing_minimize pull-right" title="' . get_string("minimize_phase", "mod_exammanagement"). '" aria-label="' . get_string("minimize_phase", "mod_exammanagement"). '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
                $mform->addElement('html', '<span class="collapse existing_maximize pull-right" title="' . get_string("maximize_phase", "mod_exammanagement"). '" aria-label="' . get_string("maximize_phase", "mod_exammanagement"). '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');

                $mform->addElement('html', '<div class="panel-body existing_body">');

                $mform->addElement('html', '<div class="row"><div class="col-'.$col.'"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-'.$littlecol.'"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div>');

                if($courseGroups){
                    $mform->addElement('html', '<div class="col-'.$bigcol.'"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div>');
                }

                $mform->addElement('html', '<div class="col-'.$col.'"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');

                foreach ($noneMoodleParticipants as $participant) {

                    $mform->addElement('html', '<div class="row text-info">');

                    $mform->addElement('html', '<div class="col-'.$col.'">'.$participant->firstname.' '.$participant->lastname.'</div>');

                    if($participant->matrnr){
                        $mform->addElement('html', '<div class="col-'.$littlecol.'">'.$participant->matrnr.'</div>');
                    } else {
                        $mform->addElement('html', '<div class="col-'.$littlecol.'">'-'</div>');
                    }

                    if($courseGroups){

                        if($participant->moodleuserid){
                            $courseid = $ExammanagementInstanceObj->getCourse()->id;

                            $userGroups = groups_get_user_groups($courseid, $participant->moodleuserid);
                            $groupnames = false;

                            foreach ($userGroups as $groupskey => $value){
                                if ($value){
                                    foreach ($value as $groupskey2 => $groupid){
                                        if(!$groupnames){
                                            $groupnames = '<strong><a href="'.$MoodleObj->getMoodleUrl('/group/index.php', $courseid, 'group', $groupid).'">'.groups_get_group_name($groupid).'</a></strong>';
                                        } else {
                                            $groupnames .= ', <strong><a href="'.$MoodleObj->getMoodleUrl('/group/index.php', $courseid, 'group', $groupid).'">'.groups_get_group_name($groupid).'</a></strong> ';
                                        }
                                    }
                                } else{
                                    $groupnames = '-';
                                    break;
                                }
                            }

                            $mform->addElement('html', '<div class="col-'.$bigcol.'">'.$groupnames.'</div>');
                        } else if($participant->matrnr){
                            $mform->addElement('html', '<div class="col-'.$bigcol.'"> - </div>');
                        }

                    }

                    if(isset($participant->nocourse) || !$courseParticipantsIDs){
                        $state = get_string('state_not_convertable_group_course', "mod_exammanagement");
                    } else {
                        $state = get_string('state_not_convertable_group_moodle', "mod_exammanagement", ['systemname' => $ExammanagementInstanceObj->getMoodleSystemName()]);
                    }

                    $mform->addElement('html', '<div class="col-'.$col.'">'.$state.'</div></div>');
                }

                $mform->addElement('html', '</div></div>');
            }

            if ($moodleParticipants){
                $this->add_action_buttons(true, get_string("convert_to_group", "mod_exammanagement"));
            } else {
                $mform->addElement('html', '<div class="row"><span class="col-sm-5"></span><a href="'.$ExammanagementInstanceObj->getExammanagementUrl("view", $this->_customdata['id']).'" class="btn btn-primary">'.get_string("cancel", "mod_exammanagement").'</a></div>');
            }

            $mform->addElement('html', '</div>');

        }
    }

    //Custom validation should be added here
    public function validation($data, $files){

        $errors = array();

        if(isset($data['participants'])){
            foreach($data['participants'] as $participantid => $checked){

                if(!preg_match("/^[a-zA-Z0-9_\-]+$/", $participantid)){
                    $errors['participants['.$participantid.']'] = get_string('err_invalidcheckboxid_participants', 'mod_exammanagement');
                }
            }
        }

        if($data['groups'] === 'new_group' && !$data['groupname']){
            $errors['groupname'] = get_string('err_filloutfield', 'mod_exammanagement');
        }

        if($data['groups'] === 'new_group'){
            $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

            if($courseGroups = groups_get_all_groups($ExammanagementInstanceObj->getCourse()->id)){

                $groupname_taken = array_filter($courseGroups, function($group) use ($data){
                    return $group->name == $data['groupname'];
                });

                if($groupname_taken){
                    $errors['groupname'] = get_string('err_groupname_taken', 'mod_exammanagement');
                }
            }
        }

        return $errors;
    }
}
