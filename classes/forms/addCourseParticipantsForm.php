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
use mod_exammanagement\general\User;

use moodleform;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/User.php');

class addCourseParticipantsForm extends moodleform{

    //Add elements to form
    public function definition(){
        global $PAGE;

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e'], $ExammanagementInstanceObj->moduleinstance->categoryid);

        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'remove_form_classes_col'); //call removing moodle form classes col-md for better layout
        $PAGE->requires->js_call_amd('mod_exammanagement/add_participants', 'enable_cb'); //call jquery for checking all checkboxes via following checkbox

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('html', '<div class="row"><div class="col-xs-6">');
        $mform->addElement('html', '<h3>'.get_string("import_course_participants", "mod_exammanagement").'</h3>');
        $mform->addElement('html', '</div><div class="col-xs-2"><a class="helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');

        $mform->addElement('html', '</div>');

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('addCourseParticipants'));

        $mform->addElement('html', '<p>'.get_string("view_added_and_course_partipicants", "mod_exammanagement").'</p>');

        ###### display all exam participants that are no course participants and will be deleted######
        $moodleParticipantsArr = array();
        $noneMoodleParticipantsArr = array();

        $moodleParticipantsArr = $UserObj->getAllMoodleExamParticipants();
        $noneMoodleParticipantsArr = $UserObj->getAllNoneMoodleExamParticipants();
        $courseParticipantsIDsArr = $UserObj->getCourseParticipantsIDs();

        $alreadyParticipantsArr = array();

        if($moodleParticipantsArr && $courseParticipantsIDsArr){ // handle participants that are course participants and already exam participants

          foreach ($moodleParticipantsArr as $key => $participant) {
              if(in_array($participant->moodleuserid, $courseParticipantsIDsArr)){
                  if(($removekey = array_search($participant->moodleuserid, $courseParticipantsIDsArr)) !== false){
                    unset($courseParticipantsIDsArr[$removekey]);
                    array_push($alreadyParticipantsArr, $participant->moodleuserid);
                    unset($moodleParticipantsArr[$key]);
                  }
              }
          }
        }

        $mform->addElement('html', '<div class="exammanagement_overview">');

        if($moodleParticipantsArr || $noneMoodleParticipantsArr){

            $deletedcount = count($moodleParticipantsArr) + count($noneMoodleParticipantsArr);

            $mform->addElement('html', '<div class="panel panel-success">');
            $mform->addElement('html', '<div class="panel-heading text-danger"><h3 class="panel-title">' . $deletedcount . ' ' . get_string("deletedmatrnr", "mod_exammanagement"). '</h3></div>');
            $mform->addElement('html', '<div class="panel-body">');

            $mform->addElement('html', '<div class="row"><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');
            $mform->addElement('html', '<div class="row"><div class="col-xs-3 remove_col pl-4">');
            $mform->addElement('advcheckbox', 'checkall_deleted', 'Alle aus-/abwählen', null, array('group' => 1, 'id' => 'checkboxgroup1'));
            $mform->setDefault('checkall_deleted', true);
            $mform->addElement('html', '</div><div class="col-xs-3"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');

            #### with moodle account ######
            if($moodleParticipantsArr){

              foreach ($moodleParticipantsArr as $key => $participantObj) {

                  $matrnr = $UserObj->getUserMatrNr($participantObj->moodleuserid);

                  $mform->addElement('html', '<div class="row text-danger"><div class="col-xs-3 remove_col pl-4">');

                  $mform->addElement('advcheckbox', 'deletedparticipants[mid_'.$participantObj->moodleuserid.']', ' '.$UserObj->getUserPicture($participantObj->moodleuserid).' '.$UserObj->getUserProfileLink($participantObj->moodleuserid), null, array('group' => 1));

                  $mform->setDefault('deletedparticipants[mid_'.$participantObj->moodleuserid.']', true);

                  $mform->addElement('html', '</div><div class="col-xs-3">'.$matrnr.'</div><div class="col-xs-3">');

                  $mform->addElement('html', $UserObj->getParticipantsGroupNames($participantObj->moodleuserid));

                  $mform->addElement('html', '</div><div class="col-xs-3">'.get_string("state_deletedmatrnr", "mod_exammanagement").'</div></div>');

              }
            }

            ###### without moodle account  ######

            if($noneMoodleParticipantsArr){

              foreach ($noneMoodleParticipantsArr as $key => $participantObj) {

                  $matrnr = $UserObj->getUserMatrNr(false, $participantObj->imtlogin);

                  $mform->addElement('html', '<div class="row text-danger"><div class="col-xs-3 remove_col pl-4">');

                  $mform->addElement('advcheckbox', 'deletedparticipants[matrnr_'.$participantObj->imtlogin.']', ' '. $participantObj->firstname .' '.$participantObj->lastname, null, array('group' => 1));

                  $mform->setDefault('deletedparticipants[matrnr_'.$participantObj->imtlogin.']', true);

                  $mform->addElement('html', '</div><div class="col-xs-3">'.$matrnr.'</div><div class="col-xs-3">');

                  $mform->addElement('html', '-');

                  $mform->addElement('html', '</div><div class="col-xs-3">'.get_string("state_deletedmatrnr", "mod_exammanagement").'</div></div>');
              }
            }

            $mform->addElement('html', '</div></div>');
        }

        ###### display course participants already added as exam participants ######

        if($alreadyParticipantsArr){

          $mform->addElement('html', '<div class="panel panel-info">');
          $mform->addElement('html', '<div class="panel-heading text-info"><h3 class="panel-title">' . count($alreadyParticipantsArr) . ' ' . get_string("existingmatrnr", "mod_exammanagement"). '</h3></div>');
          $mform->addElement('html', '<div class="panel-body">');

          $mform->addElement('html', '<div class="row"><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');

          foreach ($alreadyParticipantsArr as $key => $value) {
                $matrnr = $UserObj->getUserMatrNr($value);

                $mform->addElement('html', '<div class="row"><div class="col-xs-3"> ' . $UserObj->getUserPicture($value).' '.$UserObj->getUserProfileLink($value) . ' </div>');
                $mform->addElement('html', '<div class="col-xs-3">'.$matrnr.'</div>');
                $mform->addElement('html', '<div class="col-xs-3">'.$UserObj->getParticipantsGroupNames($value).'</div>');
                $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_courseparticipant", "mod_exammanagement").'</div></div>');
          }

          $mform->addElement('html', '</div></div>');

        }

        ###### display course participants not yet added as exam participants ######

        if($courseParticipantsIDsArr){

          $mform->addElement('html', '<div class="panel panel-success">');
          $mform->addElement('html', '<div class="panel-heading text-success"><h3 class="panel-title">' . count($courseParticipantsIDsArr) . ' ' . get_string("newmatrnr", "mod_exammanagement"). '</h3></div>');
          $mform->addElement('html', '<div class="panel-body">');

          $mform->addElement('html', '<div class="row"><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');
          $mform->addElement('html', '<div class="row"><div class="col-xs-3 remove_col pl-4">');
          $mform->addElement('advcheckbox', 'checkall_new', 'Alle aus-/abwählen', null, array('group' => 2, 'id' => 'checkboxgroup2'));
          $mform->setDefault('checkall_new', true);
          $mform->addElement('html', '</div><div class="col-xs-3"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');

          foreach ($courseParticipantsIDsArr as $key => $value) {
                $matrnr = $UserObj->getUserMatrNr($value);

                $mform->addElement('html', '<div class="row"><div class="col-xs-3 remove_col pl-4">');
                $mform->addElement('advcheckbox', 'participants['.$value.']', ' '.$UserObj->getUserPicture($value).' '.$UserObj->getUserProfileLink($value), null, array('group' => 2));
                $mform->setDefault('participants['.$value.']', true);
                $mform->addElement('html', '</div><div class="col-xs-3">'.$matrnr.'</div>');
                $mform->addElement('html', '<div class="col-xs-3">'.$UserObj->getParticipantsGroupNames($value).'</div>');
                $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_courseparticipant", "mod_exammanagement").'</div></div>');
          }

          $mform->addElement('html', '</div></div>');

        }

        if ($courseParticipantsIDsArr){
            if($ExammanagementInstanceObj->isStateofPlacesCorrect()){
                $mform->addElement('html', '<p><b>Achtung:</b> Es wurden bereits Sitzplätze zugewiesen. Diese Zuweisung wird durch das Hinzufügen der Teilnehmer gelöscht und muss dann neu durchgeführt werden.</p>');
            }
            $this->add_action_buttons(true, get_string("add_to_exam", "mod_exammanagement"));
        } else {
            $mform->addElement('html', '<div class="row"><p class="col-xs-12 text-xs-center">'.get_string("no_participants_added", "mod_exammanagement").'</p></div>');
            $mform->addElement('html', '<div class="row"><span class="col-sm-5"></span><a href="'.$ExammanagementInstanceObj->getExammanagementUrl("view", $this->_customdata['id']).'" class="btn btn-primary">'.get_string("cancel", "mod_exammanagement").'</a></div>');
        }

        $mform->addElement('html', '</div>');

        $mform->disable_form_change_checker();
    }

    //Custom validation should be added here
    public function validation($data, $files){

        $errors = array();

        if (isset($data['participants'])) {
            foreach ($data['participants'] as $participantid => $checked) {
                if (!preg_match("/^[a-zA-Z0-9_\-]+$/", $participantid)) {
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
