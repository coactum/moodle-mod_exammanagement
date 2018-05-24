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

        $mform = $this->_form; // Don't forget the underscore!

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

        //create gradingscale input list
        $gradingscale = $ExammanagementInstanceObj->getGradingscale();
        $totalpoints = $ExammanagementInstanceObj->getTaskTotalPoints();
        $attributes = array('size'=>'1'); // length of input field

        if (!$totalpoints){
          $totalpoints = 0;
        }

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('configureGradingscale'));

        $mform->addElement('html', '<h3>'.get_string('configure_gradingscale_str', 'mod_exammanagement').'</h3>');
        $mform->addElement('html', '<div class="row"><span class="col-md-3">'.get_string('configure_gradingscale_totalpoints', 'mod_exammanagement').':</span><span class="col-md-9" id="totalpoints"><strong>'.$totalpoints.'</strong></span></div>');

        if (!$gradingscale){
          $gradingscale = array(
          "1.0" => $totalpoints,
          "1.3" => round($totalpoints/1.1),
          "1.7" => round($totalpoints/1.2),
          "2.0" => round($totalpoints/1.3),
          "2.3" => round($totalpoints/1.4),
          "2.7" => round($totalpoints/1.5),
          "3.0" => round( $totalpoints/1.6),
          "3.3" => round($totalpoints/1.7),
          "3.7" => round($totalpoints/1.8),
          "4.0" => round($totalpoints/2),
          );
        }

        //add labels for grading steps
        var_dump($gradingscale);

        $mform->addElement('html', '<div class="row"><p class="col-xs-1"></p>');

        foreach($gradingscale as $key => $points){
            var_dump($key);
            $mform->addElement('html', '<strong class="col-xs-1">'.$key.'</strong>');
        }

        $mform->addElement('html', '</div>');

        //add input fields with points
        $mform->addElement('html', '<div class="row"><p class="col-xs-1"></p>');

        foreach($gradingscale as $key => $points){
            var_dump($points);

            $key_2 = str_replace('.', '', $key);

            $mform->addElement('html', '<span class="col-xs-1">');
            $mform->addElement('text', 'gradingsteppoints['.$key.']', '', $attributes);
            $mform->addElement('html', '</span>');
            $mform->setType('gradingsteppoints['.$key.']', PARAM_INT);
            $mform->setDefault('gradingsteppoints['.$key.']', $points);
          }

        $mform->addElement('html', '</div>');

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        $PAGE->requires->js_call_amd('mod_exammanagement/remove_form_classes_col', 'remove_form_classes_col'); //call removing moodle form classes col-md for better layout

        $this->add_action_buttons();

    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
