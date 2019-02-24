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
 * class containing textfieldForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use mod_exammanagement\general\exammanagementInstance;
use moodleform;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__.'/../general/exammanagementInstance.php');

class configureGradingscaleForm extends moodleform {

    //Add elements to form
    public function definition() {

        global $PAGE;

        $PAGE->requires->js_call_amd('mod_exammanagement/remove_form_classes_col', 'remove_form_classes_col'); //call removing moodle form classes col-md for better layout
        $PAGE->requires->js_call_amd('mod_exammanagement/configure_gradingscale', 'init'); //creating input type number fields

        $mform = $this->_form; // Don't forget the underscore!

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

        //create gradingscale input list
        $gradingscale = $ExammanagementInstanceObj->getGradingscale();
        $totalpoints = $ExammanagementInstanceObj->getTaskTotalPoints();
        $attributes = array('size'=>'1'); // length of input field

        if (!$totalpoints){
          $totalpoints = 0;
        }

        $mform->addElement('html', '<div class="row"><h3 class="col-xs-10">'.get_string('configure_gradingscale_str', 'mod_exammanagement').'</h3>');
        $mform->addElement('html', '<div class="col-xs-2"><a class="pull-right helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;" title="'.get_string("helptext_open", "mod_exammanagement").'"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');
        $mform->addElement('html', '</div>');

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('configureGradingscale'));

        $mform->addElement('html', '<div class="row"><span class="col-md-3">'.get_string('configure_gradingscale_totalpoints', 'mod_exammanagement').':</span><span class="col-md-9" id="totalpoints"><strong>'.str_replace( '.', ',', $totalpoints).'</strong></span></div>');

        if (!$gradingscale){
          $gradingscale = array(
          "1.0" => 0,
          "1.3" => 0,
          "1.7" => 0,
          "2.0" => 0,
          "2.3" => 0,
          "2.7" => 0,
          "3.0" => 0,
          "3.3" => 0,
          "3.7" => 0,
          "4.0" => 0,
          );
        }

        //add labels for grading steps
        $mform->addElement('html', '<div class="row"><p class="col-xs-1"></p>');

        foreach($gradingscale as $key => $points){
            $mform->addElement('html', '<strong class="col-xs-1">'.str_replace('.', ',',$key).'</strong>');
        }

        $mform->addElement('html', '</div>');

        //add input fields with points
        $mform->addElement('html', '<div class="row remove_col"><p class="col-xs-1"></p>');

        foreach($gradingscale as $key => $points){

            $key_2 = str_replace('.', '', $key);

            $mform->addElement('html', '<span class="col-xs-1">');
            $mform->addElement('text', 'gradingsteppoints['.$key.']', '', $attributes);
            $mform->addElement('html', '</span>');
            $mform->setType('gradingsteppoints['.$key.']', PARAM_FLOAT);
            $mform->setDefault('gradingsteppoints['.$key.']', $points);
          }

        $mform->addElement('html', '</div>');

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons();

        $mform->disable_form_change_checker();

    }

    //Custom validation should be added here
    function validation($data, $files) {

      $errors= array();

      $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
      $maxpoints = $ExammanagementInstanceObj->getTaskTotalPoints();

      foreach($data['gradingsteppoints'] as $key => $gradingsteppoints){

          $floatval = floatval($gradingsteppoints);
          $isnumeric = is_numeric($gradingsteppoints);

          if(($gradingsteppoints && !$floatval) || !$isnumeric){
              $errors['gradingsteppoints['.$key.']'] = get_string('err_novalidinteger', 'mod_exammanagement');
          } else if($gradingsteppoints<0) {
              $errors['gradingsteppoints['.$key.']'] = get_string('err_underzero', 'mod_exammanagement');
          } else if($gradingsteppoints > $maxpoints){
              $errors['gradingsteppoints['.$key.']'] = get_string('err_overmaxpoints', 'mod_exammanagement');
          } else if($key!=='1.0' && !array_key_exists('gradingsteppoints['.$lastgradingstepkey.']', $errors) && ($lastgradingsteppoints <= $gradingsteppoints)){
              $errors['gradingsteppoints['.$key.']'] = get_string('err_gradingstepsnotcorrect', 'mod_exammanagement');
              $errors['gradingsteppoints['.$lastgradingstepkey.']'] = get_string('err_gradingstepsnotcorrect', 'mod_exammanagement');

          }

          $lastgradingsteppoints = $gradingsteppoints;
          $lastgradingstepkey = $key;
      }

      return $errors;
    }
}
