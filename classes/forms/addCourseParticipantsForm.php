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
        global $PAGE, $CFG;

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e']);

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

        $mform->addElement('html', '<div class="row"><div class="col-xs-3"><h4>'.get_string("participants", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("matriculation_number", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("course_groups", "mod_exammanagement").'</h4></div><div class="col-xs-3"><h4>'.get_string("import_state", "mod_exammanagement").'</h4></div></div>');

        $mform->addElement('html', '<div class="row"><div class="col-xs-3">');
        $mform->addElement('advcheckbox', 'checkall', 'Alle aus-/abwählen', null, array('group' => 1, 'id' => 'checkboxgroup1',));
        $mform->addElement('html', '</div><div class="col-xs-3"></div><div class="col-xs-3"></div><div class="col-xs-3"></div></div>');

        ###### display exam participants with moodle account ######
        $moodleParticipantsArr = $UserObj->getAllMoodleExamParticipants();

        if($moodleParticipantsArr){

          foreach ($moodleParticipantsArr as $key => $participantObj) {

              $matrnr = $UserObj->getUserMatrNr($participantObj->moodleuserid);

              $mform->addElement('html', '<div class="row"><div class="col-xs-3">');

              $mform->addElement('advcheckbox', 'participants['.$participantObj->moodleuserid.']', ' '.$UserObj->getUserPicture($participantObj->moodleuserid).' '.$UserObj->getUserProfileLink($participantObj->moodleuserid), null, array('group' => 1));

              $mform->addElement('html', '</div><div class="col-xs-3">'.$matrnr.'</div><div class="col-xs-3">');

              $mform->addElement('html', $UserObj->getParticipantsGroupNames($participantObj->moodleuserid));

              $mform->addElement('html', '</div><div class="col-xs-3">'.get_string("state_added_to_exam", "mod_exammanagement").'</div></div>');

              $mform->setDefault('participants['.$participantObj->moodleuserid.']', true);
          }
        }

        $mform->addElement('html', '<hr />');

        ###### display exam participants without moodle account  ######

        $noneMoodleParticipantsArr = $UserObj->getAllNoneMoodleExamParticipants();

        if($noneMoodleParticipantsArr){

          foreach ($noneMoodleParticipantsArr as $key => $participantObj) {

              $matrnr = $UserObj->getUserMatrNr(false, $participantObj->imtlogin);

              $mform->addElement('html', '<div class="row"><div class="col-xs-3">');

              $mform->addElement('advcheckbox', 'participants['.$participantObj->imtlogin.']', ' '. $participantObj->firstname .' '.$participantObj->lastname, null, array('group' => 1));

              $mform->addElement('html', '</div><div class="col-xs-3">'.$matrnr.'</div><div class="col-xs-3">');

              $mform->addElement('html', '-');

              $mform->addElement('html', '</div><div class="col-xs-3">'.get_string("state_added_to_exam_no_moodle", "mod_exammanagement").'</div></div>');

              $mform->setDefault('participants['.$participantObj->imtlogin.']', true);
          }
        }

        $mform->addElement('html', '<hr />');

        ###### display course participants not yet added as exam participants ######
        $courseParticipantsIDs = $UserObj->getCourseParticipantsIDs();

        if($courseParticipantsIDs){

          foreach ($courseParticipantsIDs as $key => $value) {
              if(!$UserObj->checkIfAlreadyParticipant($value)){
                $matrnr = $UserObj->getUserMatrNr($value);

                $mform->addElement('html', '<div class="row"><div class="col-xs-3">');
                $mform->addElement('advcheckbox', 'participants['.$value.']', ' '.$UserObj->getUserPicture($value).' '.$UserObj->getUserProfileLink($value), null, array('group' => 1));
                $mform->addElement('html', '</div><div class="col-xs-3">'.$matrnr.'</div>');
                $mform->addElement('html', '<div class="col-xs-3">'.$UserObj->getParticipantsGroupNames($value).'</div>');
                $mform->addElement('html', '<div class="col-xs-3">'.get_string("state_courseparticipant", "mod_exammanagement").'</div></div>');
              }
          }
        }

        $mform->addElement('html', '</div>');

        if ($moodleParticipantsArr || $noneMoodleParticipantsArr || $courseParticipantsIDs){
            $mform->addElement('html', '<p> <b>Hinweis:</b> Durch das Hinzufügen der Kursteilnehmer werden alle bisher gespeicherten Prüfungsteilnehmer überschrieben!</p>');

            $this->add_action_buttons(true, get_string("add_to_exam", "mod_exammanagement"));
        } else {
            $mform->addElement('html', '<div class="row"><p class="col-xs-12 text-xs-center">'.get_string("no_participants_added", "mod_exammanagement").'</p></div>');
        }

        $mform->disable_form_change_checker();
    }

    //Custom validation should be added here
    public function validation($data, $files){
        return array();
    }
}
