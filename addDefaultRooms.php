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
 * adds default rooms for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use stdClass;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$MoodleObj = Moodle::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();

if($MoodleObj->checkCapability('mod/exammanagement:adddefaultrooms')){

  	$defaultRoomsFile = file($MoodleObj->getMoodleUrl('/mod/exammanagement/data/rooms.txt'));

  	foreach ($defaultRoomsFile as $key => $roomstr){

  			$roomParameters = explode('+', $roomstr);

  			$roomObj = new stdClass();
  			$roomObj->roomid = $roomParameters[0];
  			$roomObj->name = $roomParameters[1];
   			$roomObj->description = $roomParameters[2];

  			$svgStr = base64_encode($roomParameters[3]);

   			$roomObj->seatingplan = $svgStr;
   			$roomObj->places = $roomParameters[4];
  			$roomObj->type = 'defaultroom';
   			$roomObj->misc = NULL;

   			//array_push($records, $roomObj);

  			$MoodleDBObj->InsertRecordInDB('exammanagement_rooms', $roomObj); // bulkrecord insert too big
  		}

  		$MoodleObj->redirectToOverviewPage('beforeexam', 'Standardräume angelegt', 'success');

} else {
    $MoodleObj->redirectToOverviewPage('', get_string('nopermissions', 'mod_exammanagement'), 'error');
}
