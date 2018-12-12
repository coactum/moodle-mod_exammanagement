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
 * Imports bonus points for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\importBonusForm;
use PHPExcel_IOFactory;
use PHPExcel_Reader_IReadFilter;
use stdclass;
use core\output\notification;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once("$CFG->libdir/phpexcel/PHPExcel.php");

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$bonusstepcount  = optional_param('bonusstepcount', 0, PARAM_INT);

$MoodleObj = Moodle::getInstance($id, $e);
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance($id, $e);
$UserObj = User::getInstance($id, $e, $ExammanagementInstanceObj->moduleinstance->categoryid);

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

	$MoodleObj->setPage('importBonus');
	
	if(!$ExammanagementInstanceObj->isStateOfPlacesCorrect() || $ExammanagementInstanceObj->isStateOfPlacesError()){
		$MoodleObj->redirectToOverviewPage('aftercorrection', 'Noch keine Sitzplätze zugewiesen. Bonuspunkteimport noch nicht möglich', 'error');
	} else if (!$UserObj->getParticipantsCount()) {
		$MoodleObj->redirectToOverviewPage('aftercorrection', 'Noch keine Teilnehmer ausgewählt. Bonuspunkteimport noch nicht möglich', 'error');
	  }

    $MoodleObj->outputPageHeader();

    //Instantiate form
    $mform = new importBonusForm(null, array('id'=>$id, 'e'=>$e, 'bonusstepcount'=>$bonusstepcount));

    //Form processing and displaying is done here
    if ($mform->is_cancelled()) {
        //Handle form cancel operation, if cancel button is present on form

        $MoodleObj->redirectToOverviewPage('aftercorrection', 'Vorgang abgebrochen', 'warning');

    } else if ($fromform = $mform->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.	

        if ($fromform->bonuspoints_list){

			if((isset($fromform->bonussteppoints[2]) && $fromform->bonussteppoints[1]>=$fromform->bonussteppoints[2]) || (isset($fromform->bonussteppoints[3]) && $fromform->bonussteppoints[2]>=$fromform->bonussteppoints[3])){
				redirect($ExammanagementInstanceObj->getExammanagementUrl('importBonus', $id), 'Punkte für Bonusschritte ungültig', null, notification::NOTIFY_ERROR);
			}
		   
			// retrieve Files from form
			$file = $mform->get_file_content('bonuspoints_list');
			$filename = $mform->get_new_filename('bonuspoints_list');
			
			$tempfile = tempnam(sys_get_temp_dir(), 'bonuslist_');
			rename($tempfile, $tempfile .= $filename);

			$handle = fopen($tempfile, "w");
			fwrite($handle, $file);

			$ExcelReaderWrapper = PHPExcel_IOFactory::createReaderForFile($tempfile);
			$ExcelReaderWrapper->setReadDataOnly(true);

			class MyReadFilter implements PHPExcel_Reader_IReadFilter {

				public function __construct($columnID, $columnPoints) {
					$this->columnID = $columnID;
					$this->columnPoints = $columnPoints;
				}
			
				public function readCell($column, $row, $worksheetName = '') { 
					if ($column >= $this->columnID && $column >= $this->columnPoints) { 
						return true; 
					} 
					return false; 
				}
			}

			$ExcelReaderWrapper->setReadFilter( new MyReadFilter() );
			$readerObj = $ExcelReaderWrapper->load($tempfile);

			$worksheetObj = $readerObj->getActiveSheet();
			$highestRow = $worksheetObj->getHighestRow(); // e.g. 10

			$userIDsArr = $worksheetObj->rangeToArray($fromform->idfield.'2:'.$fromform->idfield.$highestRow);
			$pointsArr = $worksheetObj->rangeToArray($fromform->pointsfield.'2:'.$fromform->pointsfield.$highestRow);

			foreach($userIDsArr as $key => $uid){

				$participantObj = false;

				if(is_numeric($uid[0])){

					$uid = $MoodleDBObj->getFieldFromDB('user', 'id', array('idnumber'=>$uid[0]));

					if($UserObj->checkIfAlreadyParticipant($uid)){
						$participantObj = $UserObj->getExamParticipantObj($uid);
					}

				} else {
					$participantObj = $UserObj->getExamParticipantObj(null, $uid[0]);
				}

				if($participantObj && isset($pointsArr[$key][0]) && $pointsArr[$key][0] !== '-'){

					foreach($fromform->bonussteppoints as $step => $points){
						
						if(floatval($pointsArr[$key][0]) >= $points){
							$participantObj->bonuspoints = $step; // change to detect bonus step
						} else {
							break;
						}
					}

					$update = $MoodleDBObj->UpdateRecordInDB('exammanagement_part_'.$ExammanagementInstanceObj->moduleinstance->categoryid, $participantObj);

				}
			}

			fclose($handle);
			unlink($tempfile);	
			
		}

		if($update){
			$MoodleObj->redirectToOverviewPage('aftercorrection', 'Bonuspunkte importiert', 'success');
		} else {
			$MoodleObj->redirectToOverviewPage('aftercorrection', 'Bonuspunkte konnten nicht importiert werden', 'error');
		}

    } else {
    // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.

    //Set default data (if any)
    //$mform->set_data(array('participants'=>$this->getCourseParticipantsIDs(), 'id'=>$this->id));
    $mform->set_data(array('id'=>$id));

    //displays the form
    $mform->display();
    }

    $MoodleObj->outputFooter();
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
