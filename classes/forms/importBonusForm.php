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
use moodleform;
use stdclass;

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/User.php');

class importBonusForm extends moodleform{

    //Add elements to form
    public function definition(){
        global $PAGE, $CFG, $OUTPUT;

        $ExammanagementInstanceObj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $UserObj = User::getInstance($this->_customdata['id'], $this->_customdata['e'], $ExammanagementInstanceObj->getCm()->instance);

        $PAGE->requires->js_call_amd('mod_exammanagement/import_bonus', 'init'); //call jquery for tracking input value change events and creating input type number fields
        $PAGE->requires->js_call_amd('mod_exammanagement/import_bonus', 'addbonusstep'); //call jquery for adding tasks
        $PAGE->requires->js_call_amd('mod_exammanagement/import_bonus', 'removebonusstep'); //call jquery for removing tasks        

        $mform = $this->_form; // Don't forget the underscore!

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<h3>'.get_string("importBonus", "mod_exammanagement"));
        
        if($helptextsenabled){
            $mform->addElement('html', $OUTPUT->help_icon('importBonus', 'mod_exammanagement', ''));
        }

        $mform->addElement('html', '</h3>');

        $mform->addElement('html', '<p>'.get_string('import_bonus_text', 'mod_exammanagement').'</p>');

        $mform->addElement('hidden', 'id', 'dummy');
        $mform->setType('id', PARAM_INT);

        $bonuscount = $UserObj->getEnteredBonusCount();
        if($bonuscount){
            $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>'.get_string("bonus_already_entered", "mod_exammanagement", ['bonuscount' => $bonuscount]).'</div>');
        }

        ###### set bonus steps #####
        $mform->addElement('html', '<h3>'.get_string('set_bonussteps', 'mod_exammanagement').'</h3>');

        //group for add and remove bonusstep buttons
        $bonusstep_buttonarray = array();
        array_push($bonusstep_buttonarray, $mform->createElement('button', 'add_bonusstep', '<i class="fa fa-plus" aria-hidden="true"></i>'));
        array_push($bonusstep_buttonarray, $mform->createElement('button', 'remove_bonusstep', '<i class="fa fa-minus" aria-hidden="true"></i>'));
        $mform->addGroup($bonusstep_buttonarray, 'bonusstep_buttonarray', get_string('add_remove_bonusstep', 'mod_exammanagement'), array(' '), false);

        //create list of bonussteps

        $bonussstepnumbers_array = array();
        $bonussteps_array = array();
        $attributes = array('size'=>'1'); // length of input field
        $count = 1;
        $bonusstepcount = $this->_customdata['bonusstepcount'];

        //add tasks from DB
        if ($bonusstepcount){

          for($count; $count <= $bonusstepcount; $count++){

              //number of bonusstep
              array_push($bonussstepnumbers_array, $mform->createElement('html', '<span class="exammanagement_task_spacing"><strong>'.$count.'</strong></span>'));

              //input field with points needed for bonus step
              array_push($bonussteps_array, $mform->createElement('text', 'bonussteppoints['.$count.']', '', $attributes));
              $mform->setType('bonussteppoints['.$count.']', PARAM_FLOAT);
              $mform->setDefault('bonussteppoints['.$count.']', '');

          }

          $mform->addElement('hidden', 'bonusstepcount', $bonusstepcount);
          $mform->setType('bonusstepcount', PARAM_FLOAT);

        } else {

          array_push($bonussstepnumbers_array, $mform->createElement('html', '<span class="exammanagement_task_spacing"><strong>1</strong></span>'));
          
          array_push($bonussteps_array, $mform->createElement('text', 'bonussteppoints[1]', '', $attributes));
          $mform->setType('bonussteppoints[1]', PARAM_FLOAT);
          $mform->setDefault('bonussteppoints[1]', '');

          $mform->addElement('hidden', 'bonusstepcount', 1);
          $mform->setType('bonusstepcount', PARAM_FLOAT);
        }

        $mform->addGroup($bonussstepnumbers_array, 'bonussstepnumbers_array', get_string('bonusstep', 'mod_exammanagement'), '', false);
        $mform->addGroup($bonussteps_array, 'bonussteppoints_array', get_string('required_points', 'mod_exammanagement'), ' ', false);

        $mform->addElement('html', '<hr>');

        ###### add bonuspoints from file ######

        $mform->addElement('html', '<h3>'.get_string('configure_fileimport', 'mod_exammanagement').'</h3>');

        $select = $mform->addElement('select', 'importmode', get_string('import_mode', 'mod_exammanagement'), array('me' => get_string('moodle_export', 'mod_exammanagement', ['systemname' => $ExammanagementInstanceObj->getMoodleSystemName()]), 'i' => get_string('individual', 'mod_exammanagement'))); 
        $select->setSelected('me');

        $mform->addElement('text', 'idfield', get_string('idfield', 'mod_exammanagement', ['systemname' => $ExammanagementInstanceObj->getMoodleSystemName()]), $attributes);
        $mform->setType('idfield', PARAM_TEXT);
        $mform->setDefault('idfield', 'F');
        $mform->disabledIf('idfield', 'importmode', 'eq', 'me');

        $mform->addElement('text', 'pointsfield', get_string('pointsfield', 'mod_exammanagement'), $attributes);
        $mform->setType('pointsfield', PARAM_TEXT);
        $mform->addRule('pointsfield', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');
        
        $maxbytes=$CFG->maxbytes;

        $mform->addElement('filepicker', 'bonuspoints_list', get_string("import_bonus_from_file", "mod_exammanagement", ['systemname' => $ExammanagementInstanceObj->getMoodleSystemName()]), null, array('maxbytes' => $maxbytes, 'accepted_types' => array('.xlsx', '.ods')));
        $mform->addRule('bonuspoints_list', get_string('err_nofile', 'mod_exammanagement'), 'required', 'client');
        
        $this->add_action_buttons(true, get_string("read_file", "mod_exammanagement"));
        $mform->disable_form_change_checker();
    }

    //Custom validation should be added here
    public function validation($data, $files){
        $errors= array();
  
        foreach($data['bonussteppoints'] as $key => $bonussteppoints){
  
            $floatval = floatval($bonussteppoints);
            $isnumeric = is_numeric($bonussteppoints);
  
            if(($bonussteppoints && !$floatval) || !$isnumeric){
                $errors['bonussteppoints['.$key.']'] = get_string('err_novalidinteger', 'mod_exammanagement');
            } else if($bonussteppoints<0) {
                $errors['bonussteppoints['.$key.']'] = get_string('err_underzero', 'mod_exammanagement');
            } else if(!preg_match('/^[A-Z]+$/', $data['idfield'])){
                $errors['idfield'] = get_string('err_noalphanumeric', 'mod_exammanagement');
            }  else if(!preg_match('/^[A-Z]+$/', $data['pointsfield'])){
                $errors['pointsfield'] = get_string('err_noalphanumeric', 'mod_exammanagement');
            }
        }
  
        return $errors;
    }
}
