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

        $PAGE->requires->js_call_amd('mod_exammanagement/select_all_choices', 'enable_cb'); //call jquery for checking all checkboxes via following checkbox

        $mform = $this->_form; // Don't forget the underscore!

        $tempParticipantsIDs = $ExammanagementInstanceObj->getTempParticipants();

        if($tempParticipantsIDs){
            $badmatriculationnumbers = array_pop($tempParticipantsIDs);
        } else {
            $badmatriculationnumbers = false;
        }

        $mform->addElement('html', '<div class="row"><div class="col-xs-6">');

        if($tempParticipantsIDs || $badmatriculationnumbers){
            $mform->addElement('html', '<h3>'.get_string("import_participants", "mod_exammanagement").'</h3>');
        } else {
            $mform->addElement('html', '<h3>'.get_string("add_participants_from_file", "mod_exammanagement").'</h3>');
        }
        $mform->addElement('html', '</div><div class="col-xs-2"><a type="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');

        if($tempParticipantsIDs || $badmatriculationnumbers){
            $mform->addElement('html', '<a href="'.$ExammanagementInstanceObj->getExammanagementUrl("addParticipants", $this->_customdata['id']).'" role="button" class="btn btn-primary pull-right" title="'.get_string("import_new_participants", "mod_exammanagement").'"><span class="hidden-sm-down">'.get_string("import_new_participants", "mod_exammanagement").'</span><i class="fa fa-plus hidden-md-up" aria-hidden="true"></i></a>');
        }

        $mform->addElement('html', '</div></div>');

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('addParticipants'));

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        if(!$tempParticipantsIDs && !$badmatriculationnumbers){
          ###### add Participants from File ######

          $maxbytes=$CFG->maxbytes;

          $mform->addElement('html', '<h4>'.get_string("paul_file", "mod_exammanagement").'</h4>');
          $mform->addElement('filepicker', 'participantslist_paul', get_string("import_from_paul_file", "mod_exammanagement"), null, array('maxbytes' => $maxbytes, 'accepted_types' => '.txt'));

          $mform->addElement('html', '<div class="hidden"><h4>'.get_string("excel_file", "mod_exammanagement").'</h4>');
          $mform->addElement('filepicker', 'participantslist_excel', get_string("import_from_excel_file", "mod_exammanagement"), null, array('maxbytes' => $maxbytes, 'accepted_types' => '.csv, .xlsx, .ods, .xls'));
          $mform->addElement('html', '</div>');

          //for testing
          $test = $this->_customdata['test'];
          $mform->addElement('hidden', 'test', $test);
          $mform->setType('test', PARAM_INT);

          $this->add_action_buttons(true, get_string("read_file", "mod_exammanagement"));

        } else {

          ###### view all temporary imported participants ######
          $mform->addElement('html', '<div class="row"><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');
          $mform->addElement('html', '<div class="row"><div class="col-xs-3">');
          $mform->addElement('advcheckbox', 'checkall', 'Alle aus-/abwählen', null, array('group' => 1, 'id' => 'checkboxgroup1',));
          $mform->addElement('html', '</div><div class="col-xs-3"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');

          // $participantsIDs = $ExammanagementInstanceObj->getSavedParticipants();
          // if($participantsIDs){
          //     usort($participantsIDs, function($a, $b){ //sort participants ids by name (custom function)
          //       $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
          //       $aFirstname = $ExammanagementInstanceObj->getMoodleUser($a)->firstname;
          //       $aLastname = $ExammanagementInstanceObj->getMoodleUser($a)->lastname;
          //       $bFirstname = $ExammanagementInstanceObj->getMoodleUser($b)->firstname;
          //       $bLastname = $ExammanagementInstanceObj->getMoodleUser($b)->lastname;
          //
          //       if ($aLastname == $bLastname) { //if names are even sort by first name
          //           return strcmp($aFirstname, $bFirstname);
          //       } else{
          //           return strcmp($aLastname, $bLastname); // else sort by last name
          //       }
          //     });
          //
          //     if($LdapManagerObj->is_LDAP_config()){
          //         $LdapManagerObj->connect_ldap();
          //     }
          //
          //     foreach ($participantsIDs as $key => $value) {
          //
          //       $matrnr = $ExammanagementInstanceObj->getUserMatrNr($value);
          //
          //       $mform->addElement('html', '<div class="row"><div class="col-xs-3">');
          //       $mform->addElement('advcheckbox', 'participants['.$value.']', ' '.$ExammanagementInstanceObj->getUserPicture($value).' '.$ExammanagementInstanceObj->getUserProfileLink($value), null, array('group' => 1));
          //       $mform->addElement('html', '</div><div class="col-xs-3">'.$matrnr.'</div>');
          //       $mform->addElement('html', '<div class="col-xs-3">'.$ExammanagementInstanceObj->getParticipantsGroupNames($value).'</div>');
          //       $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_added_to_exam", "mod_exammanagement").'</div></div>');
          //
          //       $mform->setDefault('participants['.$value.']', true);
          //   }
          // }

          if($tempParticipantsIDs){

            if($LdapManagerObj->is_LDAP_config()){
                $LdapManagerObj->connect_ldap();
            }

            foreach ($tempParticipantsIDs as $key => $value) {
                $matrnr = $ExammanagementInstanceObj->getUserMatrNr($value);

                $mform->addElement('html', '<div class="row"><div class="col-xs-3">');
                $mform->addElement('advcheckbox', 'participants['.$value.']', ' '.$ExammanagementInstanceObj->getUserPicture($value).' '.$ExammanagementInstanceObj->getUserProfileLink($value), null, array('group' => 1));
                $mform->addElement('html', '</div><div class="col-xs-3">'.$matrnr.'</div>');
                $mform->addElement('html', '<div class="col-xs-3">'.$ExammanagementInstanceObj->getParticipantsGroupNames($value).'</div>');
                $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_temporary", "mod_exammanagement").'</div></div>');
            }
        } else {
            $mform->addElement('html', '<div class="row"><p class="col-xs-12 text-xs-center">'.get_string("no_participants_added", "mod_exammanagement").'</p></div>');
        }

        if($badmatriculationnumbers){
            $mform->addElement('html', '<hr />');
            $mform->addElement('html', get_string("badmatrnr", "mod_exammanagement"));

            foreach ((array) $badmatriculationnumbers as $key => $value) {
              $mform->addElement('html', '<div class="row"><div class="col-xs-3">');
              $mform->addElement('html', '</div><div class="col-xs-3 badmatrnr">'.$value.'</div>');
              $mform->addElement('html', '<div class="col-xs-3"></div>');
              $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_badmatrnr", "mod_exammanagement").'</div></div>');
            }
        }

          if ($tempParticipantsIDs){
              $this->add_action_buttons(true, get_string("add_to_exam", "mod_exammanagement"));
          }

          $mform->addElement('html', '</div>');

        }
    }

    //Custom validation should be added here
    public function validation($data, $files){
        return array();
    }
}
