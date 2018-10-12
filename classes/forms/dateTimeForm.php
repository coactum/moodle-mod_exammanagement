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
 * class containing dateForm for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\User; // for testing

use moodleform;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/User.php');

class dateTimeForm extends moodleform {

    //Add elements to form
    public function definition() {

        $mform = $this->_form; // Don't forget the underscore!

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $mform->addElement('html', '<div class="row"><h3 class="col-xs-10">'.get_string('set_date_time', 'mod_exammanagement').'</h3>');
        $mform->addElement('html', '<div class="col-xs-2"><a class="pull-right helptext-button" role="button" aria-expanded="false" onclick="toogleHelptextPanel(); return true;"><span class="label label-info">'.get_string("help", "mod_exammanagement").' <i class="fa fa-plus helptextpanel-icon collapse.show"></i><i class="fa fa-minus helptextpanel-icon collapse"></i></span></a></div>');
        $mform->addElement('html', '</div>');

        $mform->addElement('html', $ExammanagementInstanceObj->ConcatHelptextStr('setDateTime'));

        $mform->addElement('date_time_selector', 'examtime', '');
        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);
        $this->add_action_buttons();



        function int_to_words($x) {
          $nwords = array( "zero", "one", "two", "three", "four", "five", "six", "seven",
                             "eight", "nine", "ten", "eleven", "twelve", "thirteen",
                             "fourteen", "fifteen", "sixteen", "seventeen", "eighteen",
                             "nineteen", "twenty", 30 => "thirty", 40 => "forty",
                             50 => "fifty", 60 => "sixty", 70 => "seventy", 80 => "eighty",
                             90 => "ninety" );

           if(!is_numeric($x))
              $w = '#';
           else if(fmod($x, 1) != 0)
              $w = '#';
           else {
              if($x < 0) {
                 $w = 'minus ';
                 $x = -$x;
              } else
                 $w = '';
              // ... now $x is a non-negative integer.

              if($x < 21)   // 0 to 20
                 $w .= $nwords[$x];
              else if($x < 100) {   // 21 to 99
                 $w .= $nwords[10 * floor($x/10)];
                 $r = fmod($x, 10);
                 if($r > 0)
                    $w .= '-'. $nwords[$r];
              } else if($x < 1000) {   // 100 to 999
                 $w .= $nwords[floor($x/100)] .' hundred';
                 $r = fmod($x, 100);
                 if($r > 0)
                    $w .= ' and '. int_to_words($r);
              } else if($x < 1000000) {   // 1000 to 999999
                 $w .= int_to_words(floor($x/1000)) .' thousand';
                 $r = fmod($x, 1000);
                 if($r > 0) {
                    $w .= ' ';
                    if($r < 100)
                       $w .= 'and ';
                    $w .= int_to_words($r);
                 }
              } else {    //  millions
                 $w .= int_to_words(floor($x/1000000)) .' million';
                 $r = fmod($x, 1000000);
                 if($r > 0) {
                    $w .= ' ';
                    if($r < 100)
                       $word .= 'and ';
                    $w .= int_to_words($r);
                 }
              }
           }
           return $w;
        }

        //test
        var_dump('coursecategorienames');
        global $DB;
        $terms = $DB->get_fieldset_select('course_categories', 'name', '');
        var_dump($terms);

        var_dump(int_to_words('asd'));

        foreach($terms as $termname){
          var_dump('exammanagement_'.$termname);

          $cleanTermname = preg_replace("/[^0-9a-zA-Z]/", "",$termname);
          var_dump('exammanagement_'.$cleanTermname);
        }

        $UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e']);

        var_dump($UserObj->getAllExamParticipantsIds());

    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
