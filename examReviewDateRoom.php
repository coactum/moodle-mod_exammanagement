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
 * Enter Date and room for examReview for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\forms\examReviewDateRoomForm;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$MoodleObj = Moodle::getInstance($id, $e);
$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

  if(!isset($ExammanagementInstanceObj->moduleinstance->password) || (isset($ExammanagementInstanceObj->moduleinstance->password) && $SESSION->loggedInExamOrganizationId == $id)){ // if no password for moduleinstance is set or if user already entered correct password in this session: show main page

      $MoodleObj->setPage('examReviewDateRoom');
      
      if (!$ExammanagementInstanceObj->getDataDeletionDate()){
        $MoodleObj->redirectToOverviewPage('afterexam', 'Korrektur noch nicht abgeschloßen.', 'error');
      }

  		$MoodleObj-> outputPageHeader();

      //Instantiate form
      $mform = new examReviewDateRoomForm();

      //Form processing and displaying is done here
      if ($mform->is_cancelled()) {
        //Handle form cancel operation, if cancel button is present on form
        $MoodleObj->redirectToOverviewPage('beforeexam', 'Vorgang abgebrochen', 'warning');

      } else if ($fromform = $mform->get_data()) {
        //In this case you process validated data. $mform->get_data() returns data posted in form.

          $ExammanagementInstanceObj->moduleinstance->examreviewtime = $fromform->examreviewtime;

          if($fromform->examreviewroom){
            $ExammanagementInstanceObj->moduleinstance->examreviewroom = json_encode($fromform->examreviewroom);
          } else {
            $ExammanagementInstanceObj->moduleinstance->examreviewroom = NULL;            
          }
  
          $update = $MoodleDBObj->UpdateRecordInDB("exammanagement", $ExammanagementInstanceObj->moduleinstance);
          if($update){
            $MoodleObj->redirectToOverviewPage('beforeexam', 'Datum und Raum erfolgreich gesetzt', 'success');
          } else {
            $MoodleObj->redirectToOverviewPage('beforeexam', 'Datum und Raum konnten nicht gesetzt werden', 'error');
          }

      } else {
        // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
        // or on the first display of the form.

        //Set default data (if any)
        $mform->set_data(array('examreviewtime'=>$ExammanagementInstanceObj->getExamReviewTime(), 'examreviewroom'=>$ExammanagementInstanceObj->getExamReviewRoom(), 'id'=>$id));

        //displays the form
        $mform->display();
      }

      $MoodleObj->outputFooter();
  } else { // if user hasnt entered correct password for this session: show enterPasswordPage
    redirect ($ExammanagementInstanceObj->getExammanagementUrl('checkPassword', $ExammanagementInstanceObj->getCm()->id), null, null, null);
  }
} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
