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
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;

use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\ldap\ldapManager;
use mod_exammanagement\general\Moodle;
use moodleform;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../ldap/ldapManager.php');
require_once(__DIR__.'/../general/Moodle.php');

class addParticipantsForm extends moodleform{

    //Add elements to form
    public function definition(){
        global $PAGE, $CFG;

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $LdapManagerObj = ldapManager::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $MoodleObj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'remove_form_classes_col'); //call removing moodle form classes col-md for better layout
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'enable_cb'); //call jquery for checking all checkboxes via following checkbox

        $mform = $this->_form; // Don't forget the underscore!

        $tempParticipantsArr = $ExammanagementInstanceObj->getTempParticipants();

        $mform->addElement('html', '<div class="row"><div class="col-xs-6">');

        if($tempParticipantsArr){
            $mform->addElement('html', '<h3>'.get_string("import_participants", "mod_exammanagement").'</h3>');
        } else {
            $mform->addElement('html', '<h3>'.get_string("add_participants_from_file", "mod_exammanagement").'</h3>');
        }
        $mform->addElement('html', '</div><div class="col-xs-2"><a type="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');

        if($tempParticipantsArr){
            $mform->addElement('html', '<div class="col-xs-4"><a href="'.$MoodleObj->getMoodleUrl('/mod/exammanagement/addParticipants.php', $this->_customdata['id'], 'dtp', true).'" role="button" class="btn btn-primary pull-right" title="'.get_string("import_new_participants", "mod_exammanagement").'"><span class="d-none d-lg-block">'.get_string("import_new_participants", "mod_exammanagement").'</span><i class="fa fa-plus d-lg-none" aria-hidden="true"></i></a></div>');
        }

        $mform->addElement('html', '</div></div>');

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('addParticipants'));

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        if($tempParticipantsArr){

            $newParticipantsArr = $tempParticipantsArr[0];
            $badMatriculationnumbersArr = $tempParticipantsArr[1];
            $oddMatriculationnumbersArr = $tempParticipantsArr[2];
            $existingMatriculationnumbersArr = $tempParticipantsArr[3];
            $deletedMatriculationnumbersArr = $tempParticipantsArr[4];

            ###### view all temporary imported participants ######

            if($badMatriculationnumbersArr){ // show all invalid matrnr

                $mform->addElement('html', '<div class="panel panel-danger">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . count($badMatriculationnumbersArr) . ' ' . get_string("badmatrnr", "mod_exammanagement"). '</h3></div>');
                $mform->addElement('html', '<div class="panel-body">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-2"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');
                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3"></div><div class="col-xs-2"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');

                foreach ((array) $badMatriculationnumbersArr as $key => $userObj) {
                  $matrnr = $userObj->matrnr;

                  $mform->addElement('html', '<div class="row text-danger">');
                  $mform->addElement('html', '<div class="col-xs-1"> # '.$userObj->row);
                  $mform->addElement('html', '</div><div class="col-xs-3"> - </div>');
                  $mform->addElement('html', '<div class="col-xs-2">'.$matrnr.'</div>');
                  $mform->addElement('html', '<div class="col-xs-3"> - </div>');
                  $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_badmatrnr", "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');
            }

            if($oddMatriculationnumbersArr){ // show all invalid matrnr

                $mform->addElement('html', '<div class="panel panel-warning">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . count($oddMatriculationnumbersArr) . ' ' . get_string("oddmatrnr", "mod_exammanagement"). '</h3></div>');
                $mform->addElement('html', '<div class="panel-body">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-2"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');
                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3 remove_col">');
                $mform->addElement('advcheckbox', 'checkall', 'Alle aus-/abwählen', null, array('group' => 1, 'id' => 'checkboxgroup1',));
                $mform->addElement('html', '</div><div class="col-xs-2"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');

                foreach ((array) $oddMatriculationnumbersArr as $key => $userObj) {
                  $moodleid = $userObj->moodleid;
                  $matrnr = $userObj->matrnr;

                  $mform->addElement('html', '<div class="row text-warning">');
                  $mform->addElement('html', '<div class="col-xs-1"> # '.$userObj->row);
                  $mform->addElement('html', '</div><div class="col-xs-3 remove_col">');
                  $mform->addElement('advcheckbox', 'participants['.$moodleid.']', ' '.$ExammanagementInstanceObj->getUserPicture($moodleid).' '.$ExammanagementInstanceObj->getUserProfileLink($moodleid), null, array('group' => 1));
                  $mform->addElement('html', '</div><div class="col-xs-2">'.$matrnr.'</div>');
                  $mform->addElement('html', '<div class="col-xs-3"> - </div>');
                  $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_oddmatrnr_nocourseparticipant", "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');
            }

            if($existingMatriculationnumbersArr){ // show all invalid matrnr

                $mform->addElement('html', '<div class="panel panel-info">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . count($existingMatriculationnumbersArr) . ' ' . get_string("existingmatrnr", "mod_exammanagement"). '</h3></div>');
                $mform->addElement('html', '<div class="panel-body">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-2"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');
                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3"></div><div class="col-xs-2"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');

                foreach ((array) $existingMatriculationnumbersArr as $key => $userObj) {

                  $moodleid = $userObj->moodleid;
                  $matrnr = $userObj->matrnr;

                  $mform->addElement('html', '<div class="row text-info">');
                  $mform->addElement('html', '<div class="col-xs-1"> # '.$userObj->row);
                  $mform->addElement('html', '</div><div class="col-xs-3"> ' . $ExammanagementInstanceObj->getUserPicture($moodleid).' '.$ExammanagementInstanceObj->getUserProfileLink($moodleid) . ' </div>');
                  $mform->addElement('html', '<div class="col-xs-2">'.$matrnr.'</div>');
                  $mform->addElement('html', '<div class="col-xs-3">'.$ExammanagementInstanceObj->getParticipantsGroupNames($moodleid).'</div>');
                  $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_existingmatrnr", "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');
            }

            if($newParticipantsArr){ // show valid new participants

                $mform->addElement('html', '<div class="panel panel-success">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . count($newParticipantsArr) . ' ' . get_string("newmatrnr", "mod_exammanagement"). '</h3></div>');
                $mform->addElement('html', '<div class="panel-body">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-2"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');
                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3 remove_col">');
                $mform->addElement('advcheckbox', 'checkall', 'Alle aus-/abwählen', null, array('group' => 2, 'id' => 'checkboxgroup2',));
                $mform->addElement('html', '</div><div class="col-xs-2"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');

                foreach ($newParticipantsArr as $key => $userObj) {
                    $moodleid = $userObj->moodleid;
                    $matrnr = $userObj->matrnr;

                    $mform->addElement('html', '<div class="row text-success">');
                    $mform->addElement('html', '<div class="col-xs-1"> # '.$userObj->row);
                    $mform->addElement('html', '</div><div class="col-xs-3 remove_col">');
                    $mform->addElement('advcheckbox', 'participants['.$moodleid.']', ' '.$ExammanagementInstanceObj->getUserPicture($moodleid).' '.$ExammanagementInstanceObj->getUserProfileLink($moodleid), null, array('group' => 2));
                    $mform->addElement('html', '</div><div class="col-xs-2">'.$matrnr.'</div>');
                    $mform->addElement('html', '<div class="col-xs-3">'.$ExammanagementInstanceObj->getParticipantsGroupNames($moodleid).'</div>');
                    $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_newmatrnr", "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');

            }

            if($deletedMatriculationnumbersArr){ // show valid new participants

                $mform->addElement('html', '<div class="panel panel-danger">');
                $mform->addElement('html', '<div class="panel-heading"><h3 class="panel-title">' . count($deletedMatriculationnumbersArr) . ' ' . get_string("deletedmatrnr", "mod_exammanagement"). '</h3></div>');
                $mform->addElement('html', '<div class="panel-body">');

                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-2"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');
                $mform->addElement('html', '<div class="row"><div class="col-xs-1"></div><div class="col-xs-3"></div><div class="col-xs-2"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');


                foreach ($deletedMatriculationnumbersArr as $key => $userObj) {

                    $moodleid = $userObj->moodleid;
                    $matrnr = $userObj->matrnr;

                    $mform->addElement('html', '<div class="row text-danger">');
                    $mform->addElement('html', '<div class="col-xs-1"> # '.$userObj->row);
                    $mform->addElement('html', '</div><div class="col-xs-3">'.$ExammanagementInstanceObj->getUserPicture($moodleid).' '.$ExammanagementInstanceObj->getUserProfileLink($moodleid), null, array('group' => 1));
                    $mform->addElement('html', '</div><div class="col-xs-2">'.$matrnr.'</div>');
                    $mform->addElement('html', '<div class="col-xs-3">'.$ExammanagementInstanceObj->getParticipantsGroupNames($moodleid).'</div>');
                    $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_deletedmatrnr", "mod_exammanagement").'</div></div>');
                }

                $mform->addElement('html', '</div></div>');

            }

            if ($newParticipantsArr || $oddMatriculationnumbersArr || $deletedMatriculationnumbersArr){
                  $this->add_action_buttons(true, get_string("add_to_exam", "mod_exammanagement"));
            }

            $mform->addElement('html', '</div>');

        } else {

            ###### add Participants from File ######

            $maxbytes=$CFG->maxbytes;

            $mform->addElement('html', '<h4>'.get_string("paul_file", "mod_exammanagement").'</h4>');
            $mform->addElement('filepicker', 'participantslist_paul', get_string("import_from_paul_file", "mod_exammanagement"), null, array('maxbytes' => $maxbytes, 'accepted_types' => '.txt'));

            $mform->addElement('html', '<div class="hidden"><h4>'.get_string("excel_file", "mod_exammanagement").'</h4>');
            $mform->addElement('filepicker', 'participantslist_excel', get_string("import_from_excel_file", "mod_exammanagement"), null, array('maxbytes' => $maxbytes, 'accepted_types' => '.csv, .xlsx, .ods, .xls'));
            $mform->addElement('html', '</div>');

            $this->add_action_buttons(true, get_string("read_file", "mod_exammanagement"));
          }
    }

    //Custom validation should be added here
    public function validation($data, $files){
        return array();
    }
}
