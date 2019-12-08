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
 * class containing addParticipantsForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
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

class addParticipantsForm extends moodleform{

    //Add elements to form
    public function definition(){
        global $PAGE, $CFG, $OUTPUT;

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $MoodleObj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $MoodleDBObj = MoodleDB::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'remove_form_classes_col'); //call removing moodle form classes col-md for better layout
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'enable_cb'); //call jquery for checking all checkboxes via following checkbox
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'togglesection'); //call jquery for toogling sections

        $mform = $this->_form; // Don't forget the underscore!

        if(isset($this->_customdata['allParticipants'])){
            $allParticipants = $this->_customdata['allParticipants'];
        } else {
            $allParticipants = false;
        }

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<div class="row"><div class="col-xs-8"><h3>');

        if($allParticipants){
            $mform->addElement('html', get_string("addParticipants", "mod_exammanagement"));
        } else {
            $mform->addElement('html', get_string("import_participants_from_file", "mod_exammanagement"));
        }
            
        if($helptextsenabled){
            $mform->addElement('html', $OUTPUT->help_icon('addParticipants', 'mod_exammanagement', ''));
        }
    
        $mform->addElement('html', '</h3></div><div class="col-xs-4">');

        if($allParticipants){
            $mform->addElement('html', '<a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/addParticipants.php', $this->_customdata['id'], 'dtp', true).'" role="button" class="btn btn-primary pull-right" title="'.get_string("import_new_participants", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("import_new_participants", "mod_exammanagement").'</span><i class="fa fa-repeat d-lg-none" aria-hidden="true"></i></a>');
        }

        $mform->addElement('html', '</div></div>');

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        if($allParticipants){

            if($ExammanagementInstanceObj->allPlacesAssigned()){
                $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>'.get_string("places_already_assigned_participants", "mod_exammanagement").'</div>');
            }

            # determine if course groups are set #
            $courseGroups = groups_get_all_groups($ExammanagementInstanceObj->getCourse()->id);

            if(count($courseGroups) > 0){
                $courseGroups = true;
                $col = 3;
                $littlecol = 2;
            } else {
                $courseGroups = false;    
                $col = 4; 
                $littlecol = 3;             
            }

            # output participants #

            $mform->addElement('html', '<div class="exammanagement_overview">');

            if($allParticipants['badMatriculationNumbers']){ // invalid or doubled matriculation numbers

                $mform->addElement('html', '<div class="panel panel-danger exammanagement_panel">');
                $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="invalid">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . count($allParticipants['badMatriculationNumbers']) . ' ' . get_string("badmatrnr", "mod_exammanagement"). '</h3>');
                $mform->addElement('html', '<span class="collapse.show invalid_minimize pull-right" title="' . get_string("minimize_phase", "mod_exammanagement"). '" aria-label="' . get_string("minimize_phase", "mod_exammanagement"). '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
                $mform->addElement('html', '<span class="collapse invalid_maximize pull-right" title="' . get_string("maximize_phase", "mod_exammanagement"). '" aria-label="' . get_string("maximize_phase", "mod_exammanagement"). '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');
                                    
                $mform->addElement('html', '<div class="panel-body invalid_body">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-'.$col.'"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-'.$littlecol.'"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div>');
                
                if($courseGroups){
                    $mform->addElement('html', '<div class="col-xs-'.$col.'"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div>');
                }

                $mform->addElement('html', '<div class="col-xs-'.$col.'"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');

                foreach ($allParticipants['badMatriculationNumbers'] as $invalidUser) { // bad or double matrnr
                    $mform->addElement('html', '<div class="row text-danger">');
                    $mform->addElement('html', '<div class="col-xs-1"> # '.$invalidUser->line);
                    $mform->addElement('html', '</div><div class="col-xs-'.$col.'"> - </div>');
                    $mform->addElement('html', '<div class="col-xs-'.$littlecol.'">'.$invalidUser->matrnr.'</div>');

                    if($courseGroups){
                        $mform->addElement('html', '<div class="col-xs-'.$col.'"> - </div>');
                    }

                    $mform->addElement('html', '<div class="col-xs-'.$col.'">'.get_string($invalidUser->state, "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');
            }

            if($allParticipants['oddParticipants']){ // moodle users that are no course participants or users that have no moodle account

                $count = count($allParticipants['oddParticipants']);

                $mform->addElement('html', '<div class="panel panel-warning exammanagement_panel">');
                $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="odd">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . $count . ' ' . get_string("oddmatrnr", "mod_exammanagement"). '</h3>');
                $mform->addElement('html', '<span class="collapse.show odd_minimize pull-right" title="' . get_string("minimize_phase", "mod_exammanagement"). '" aria-label="' . get_string("minimize_phase", "mod_exammanagement"). '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
                $mform->addElement('html', '<span class="collapse odd_maximize pull-right" title="' . get_string("maximize_phase", "mod_exammanagement"). '" aria-label="' . get_string("maximize_phase", "mod_exammanagement"). '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');
                                    
                $mform->addElement('html', '<div class="panel-body odd_body">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-'.$col.'"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-'.$littlecol.'"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div>');
                
                if($courseGroups){
                    $mform->addElement('html', '<div class="col-xs-'.$col.'"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div>');
                }

                $mform->addElement('html', '<div class="col-xs-'.$col.'"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');
                
                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-'.$col.' remove_col">');
                $mform->addElement('advcheckbox', 'checkall_odds', get_string("select_deselect_all", "mod_exammanagement"), null, array('group' => 1, 'id' => 'checkboxgroup1'));
                $mform->addElement('html', '</div><div class="col-xs-'.$littlecol.'"></div><div class="col-xs-'.$col.'"></div><div class="col-xs-'.$col.'"></div></div>');

                foreach ($allParticipants['oddParticipants'] as $oddUser) {
                        
                    $mform->addElement('html', '<div class="row text-warning">');
                    $mform->addElement('html', '<div class="col-xs-1"> # '.$oddUser->line);
                    $mform->addElement('html', '</div><div class="col-xs-'.$col.' remove_col">');

                    if($oddUser->state == 'state_no_courseparticipant'){

                        global $OUTPUT;

                        $moodleUser = $UserObj->getMoodleUser($oddUser->moodleuserid);

                        $courseid = $ExammanagementInstanceObj->getCourse()->id;
                
                        $image = $OUTPUT->user_picture($moodleUser, array('courseid' => $courseid, 'link' => true));
                        $link = '<strong><a href="'.$MoodleObj->getMoodleUrl('/user/view.php', $oddUser->moodleuserid, 'course', $courseid).'">'.$moodleUser->firstname.' '.$moodleUser->lastname.'</a></strong>';
                        
                        $mform->addElement('advcheckbox', 'participants[mid_'.$oddUser->moodleuserid.']', $image.' '.$link, null, array('group' => 1));
                    } else if($oddUser->state == 'state_nonmoodle'){
                        $mform->addElement('advcheckbox', 'participants[matrnr_'.$oddUser->matrnr.']', '', null, array('group' => 1));
                    }

                    $mform->addElement('html', '</div><div class="col-xs-'.$littlecol.'">'.$oddUser->matrnr.'</div>');

                    if($courseGroups){
                        $mform->addElement('html', '<div class="col-xs-'.$col.'"> - </div>');
                    }
                    $mform->addElement('html', '<div class="col-xs-'.$col.'">'.get_string($oddUser->state, "mod_exammanagement",['systemname' => $ExammanagementInstanceObj->getMoodleSystemName()]).'</div></div>');  
                }

                $mform->addElement('html', '</div></div>');
                
            }

            if($allParticipants['deletedParticipants']){ // users that should be deleted because they are already read in from file with same header but not in this file

                $count = count($allParticipants['deletedParticipants']);

                $mform->addElement('html', '<div class="panel panel-success exammanagement_panel">');
                $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="deleted">');
                $mform->addElement('html', '<div class="panel-heading text-danger"><h3 class="panel-title">' . $count . ' ' . get_string("deletedmatrnr", "mod_exammanagement"). '</h3>');
                $mform->addElement('html', '<span class="collapse.show deleted_minimize pull-right" title="' . get_string("minimize_phase", "mod_exammanagement"). '" aria-label="' . get_string("minimize_phase", "mod_exammanagement"). '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
                $mform->addElement('html', '<span class="collapse deleted_maximize pull-right" title="' . get_string("maximize_phase", "mod_exammanagement"). '" aria-label="' . get_string("maximize_phase", "mod_exammanagement"). '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');
                                    
                $mform->addElement('html', '<div class="panel-body deleted_body">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-'.$col.'"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-'.$littlecol.'"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div>');
                
                if($courseGroups){
                    $mform->addElement('html', '<div class="col-xs-'.$col.'"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div>');
                }

                $mform->addElement('html', '<div class="col-xs-'.$col.'"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');
                
                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-'.$col.' remove_col pl-4">');
                $mform->addElement('advcheckbox', 'checkall_deleted', get_string("select_deselect_all", "mod_exammanagement"), null, array('group' => 2, 'id' => 'checkboxgroup2'));
                $mform->setDefault('checkall_deleted', true);
                $mform->addElement('html', '</div><div class="col-xs-'.$littlecol.'"></div><div class="col-xs-'.$col.'"></div><div class="col-xs-'.$col.'"></div></div>');
                
                foreach ($allParticipants['deletedParticipants'] as $deletedUser) {

                    $mform->addElement('html', '<div class="row text-danger"><div class="col-xs-1"></div><div class="col-xs-'.$col.' remove_col pl-4">');

                    if($deletedUser->moodleuserid){
                        global $OUTPUT;

                        $moodleUser = $UserObj->getMoodleUser($deletedUser->moodleuserid);

                        $courseid = $ExammanagementInstanceObj->getCourse()->id;
                
                        $image = $OUTPUT->user_picture($moodleUser, array('courseid' => $courseid, 'link' => true));
                        $link = '<strong><a href="'.$MoodleObj->getMoodleUrl('/user/view.php', $deletedUser->moodleuserid, 'course', $courseid).'">'.$moodleUser->firstname.' '.$moodleUser->lastname.'</a></strong>';
                        
                        $mform->addElement('advcheckbox', 'deletedparticipants[mid_'.$deletedUser->moodleuserid.']', $image.' '.$link, null, array('group' => 2));

                    } else if ($deletedUser->matrnr){
                        $mform->addElement('advcheckbox', 'deletedparticipants[matrnr_'.$deletedUser->matrnr.']', ' '. $deletedUser->firstname .' '.$deletedUser->lastname, null, array('group' => 2));
                    }

                    //$mform->setDefault('deletedparticipants[mid_'.$deletedUser->moodleuserid.']', true); // use of setData in form definition is the better solution because setDefault takes more time to handle large amounts of participants 

                    $mform->addElement('html', '</div><div class="col-xs-'.$littlecol.'">'.$deletedUser->matrnr.'</div>');

                    if($courseGroups){
                        if($deletedUser->moodleuserid){

                            $courseid = $ExammanagementInstanceObj->getCourse()->id;

                            $userGroups = groups_get_user_groups($courseid, $deletedUser->moodleuserid);
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

                            $mform->addElement('html', '<div class="col-xs-'.$col.'">'.$groupnames.'</div>');
                        } else {
                            $mform->addElement('html', '<div class="col-xs-'.$col.'">-</div>');
                        }
                    }

                    if($deletedUser->moodleuserid){
                        $mform->addElement('html', '<div class="col-xs-'.$col.'">'.get_string('state_to_be_deleted', "mod_exammanagement"). ' (' .get_string('state_not_in_file_anymore', "mod_exammanagement") .')</div></div>');
                    } else if ($deletedUser->matrnr){
                        $mform->addElement('html', '<div class="col-xs-'.$col.'">'.get_string('state_to_be_deleted', "mod_exammanagement"). ' (' .get_string('state_not_in_file_anymore', "mod_exammanagement") .')</div></div>');
                    }


                }

                $mform->addElement('html', '</div></div>');

            }

            if($allParticipants['existingParticipants']){

                $count = count($allParticipants['existingParticipants']);

                $mform->addElement('html', '<div class="panel panel-info exammanagement_panel">');
                $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="existing">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . $count . ' ' . get_string("existingmatrnr", "mod_exammanagement"). '</h3>');
                $mform->addElement('html', '<span class="collapse.show existing_minimize pull-right" title="' . get_string("minimize_phase", "mod_exammanagement"). '" aria-label="' . get_string("minimize_phase", "mod_exammanagement"). '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
                $mform->addElement('html', '<span class="collapse existing_maximize pull-right" title="' . get_string("maximize_phase", "mod_exammanagement"). '" aria-label="' . get_string("maximize_phase", "mod_exammanagement"). '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');
                                    
                $mform->addElement('html', '<div class="panel-body existing_body">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-'.$col.'"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-'.$littlecol.'"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div>');
                
                if($courseGroups){
                    $mform->addElement('html', '<div class="col-xs-'.$col.'"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div>');
                }

                $mform->addElement('html', '<div class="col-xs-'.$col.'"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');
                
                foreach ($allParticipants['existingParticipants'] as $existingUser) {
        
                    $mform->addElement('html', '<div class="row text-info">');
                    $mform->addElement('html', '<div class="col-xs-1"> # '.$existingUser->line);

                    if($existingUser->moodleuserid){

                        global $OUTPUT;

                        $moodleUser = $UserObj->getMoodleUser($existingUser->moodleuserid);

                        $courseid = $ExammanagementInstanceObj->getCourse()->id;
                
                        $image = $OUTPUT->user_picture($moodleUser, array('courseid' => $courseid, 'link' => true));
                        $link = '<strong><a href="'.$MoodleObj->getMoodleUrl('/user/view.php', $existingUser->moodleuserid, 'course', $courseid).'">'.$moodleUser->firstname.' '.$moodleUser->lastname.'</a></strong>';
                        
                        $mform->addElement('html', '</div><div class="col-xs-'.$col.'"> '. $image.' '.$link.' </div>');

                    } else if($existingUser->matrnr){
                        $mform->addElement('html', '</div><div class="col-xs-'.$col.'"></div>');
                    }

                    $mform->addElement('html', '<div class="col-xs-'.$littlecol.'">'.$existingUser->matrnr.'</div>');
                      
                    if($courseGroups){

                        if($existingUser->moodleuserid){
                            $courseid = $ExammanagementInstanceObj->getCourse()->id;

                            $userGroups = groups_get_user_groups($courseid, $existingUser->moodleuserid);
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

                            $mform->addElement('html', '<div class="col-xs-'.$col.'">'.$groupnames.'</div>');
                        } else if($existingUser->matrnr){
                            $mform->addElement('html', '<div class="col-xs-'.$col.'"> - </div>');
                        }

                    }

                    $mform->addElement('html', '<div class="col-xs-'.$col.'">'.get_string($existingUser->state, "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');
            }

            if($allParticipants['newMoodleParticipants']){

                $mform->addElement('html', '<div class="panel panel-success exammanagement_panel">');
                $mform->addElement('html', '<a aria-expanded="false" class="toggable" id="new">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . count($allParticipants['newMoodleParticipants']) . ' ' . get_string("newmatrnr", "mod_exammanagement"). '</h3>');
                $mform->addElement('html', '<span class="collapse.show new_minimize pull-right" title="' . get_string("minimize_phase", "mod_exammanagement"). '" aria-label="' . get_string("minimize_phase", "mod_exammanagement"). '"><i class="fa fa-minus" aria-hidden="true"></i></span>');
                $mform->addElement('html', '<span class="collapse new_maximize pull-right" title="' . get_string("maximize_phase", "mod_exammanagement"). '" aria-label="' . get_string("maximize_phase", "mod_exammanagement"). '"><i class="fa fa-plus" aria-hidden="true"></i></span></a></div>');
                                    
                $mform->addElement('html', '<div class="panel-body new_body">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-'.$col.'"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-'.$littlecol.'"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div>');
                
                if($courseGroups){
                    $mform->addElement('html', '<div class="col-xs-'.$col.'"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div>');
                }

                $mform->addElement('html', '<div class="col-xs-'.$col.'"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');
                
                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-'.$col.' remove_col">');
                $mform->addElement('advcheckbox', 'checkall_new', get_string("select_deselect_all", "mod_exammanagement"), null, array('group' => 3, 'id' => 'checkboxgroup3'));
                $mform->setDefault('checkall_new', true);
                $mform->addElement('html', '</div><div class="col-xs-'.$littlecol.'"></div><div class="col-xs-'.$col.'"></div><div class="col-xs-'.$col.'"></div></div>');

                foreach ($allParticipants['newMoodleParticipants'] as $newMoodleUser) { // new moodle

                    $mform->addElement('html', '<div class="row text-success">');
                    $mform->addElement('html', '<div class="col-xs-1"> # '.$newMoodleUser->line);
                    $mform->addElement('html', '</div><div class="col-xs-'.$col.' remove_col">');

                    global $OUTPUT;

                    $moodleUser = $UserObj->getMoodleUser($newMoodleUser->moodleuserid);

                    $courseid = $ExammanagementInstanceObj->getCourse()->id;
                
                    $image = $OUTPUT->user_picture($moodleUser, array('courseid' => $courseid, 'link' => true));
                    $link = '<strong><a href="'.$MoodleObj->getMoodleUrl('/user/view.php', $newMoodleUser->moodleuserid, 'course', $courseid).'">'.$moodleUser->firstname.' '.$moodleUser->lastname.'</a></strong>';
                        
                    $mform->addElement('advcheckbox', 'participants[mid_'.$newMoodleUser->moodleuserid.']', $image.' '.$link, null, array('group' => 3));
                    
                    //$mform->setDefault('participants[mid_'.$moodleuserid.']', true); // use of setData in form definition is the better solution because setDefault takes more time to handle large amounts of participants 
                    
                    $mform->addElement('html', '</div><div class="col-xs-'.$littlecol.'">'.$newMoodleUser->matrnr.'</div>');

                    $courseid = $ExammanagementInstanceObj->getCourse()->id;

                    if($courseGroups){
                        if($newMoodleUser->moodleuserid){
                            $userGroups = groups_get_user_groups($courseid, $newMoodleUser->moodleuserid);
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

                        $mform->addElement('html', '<div class="col-xs-'.$col.'">'.$groupnames.'</div>');
                    }

                    $mform->addElement('html', '<div class="col-xs-'.$col.'">'.get_string('state_newmatrnr', "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');

            }

            if ($allParticipants['newMoodleParticipants'] || $allParticipants['oddParticipants'] || $allParticipants['deletedParticipants']){

                $maxbytes=$CFG->maxbytes;

                $mform->addElement('html', '<div class="hidden">');
                $mform->addElement('filepicker', 'participantslist_text', get_string("import_from_text_file", "mod_exammanagement"), null, array('maxbytes' => $maxbytes, 'accepted_types' => '.txt'));
                $mform->addElement('html', '</div>');

                $this->add_action_buttons(true, get_string("add_to_exam", "mod_exammanagement"));
            } else {
              $mform->addElement('html', '<div class="row"><span class="col-sm-5"></span><a href="'.$ExammanagementInstanceObj->getExammanagementUrl("view", $this->_customdata['id']).'" class="btn btn-primary">'.get_string("cancel", "mod_exammanagement").'</a></div>');
            }

            $mform->addElement('html', '</div>');

        } else {

            ###### add Participants from File ######

            $maxbytes=$CFG->maxbytes;

            $mform->addElement('filepicker', 'participantslist_text', get_string("import_from_text_file", "mod_exammanagement"), null, array('maxbytes' => $maxbytes, 'accepted_types' => '.txt'));
            $mform->addRule('participantslist_text', get_string('err_nofile', 'mod_exammanagement'), 'required', 'client');

            $this->add_action_buttons(true, get_string("read_file", "mod_exammanagement"));
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

        if(isset($data['deletedparticipants'])){
            foreach($data['deletedparticipants'] as $participantid => $checked){

                if(!preg_match("/^[a-zA-Z0-9_\-]+$/", $participantid)){
                    $errors['deletedparticipants['.$participantid.']'] = get_string('err_invalidcheckboxid_participants', 'mod_exammanagement');
                }
            }
        }

        return $errors;
    }
}
