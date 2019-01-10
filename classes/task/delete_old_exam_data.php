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
 * A cron_task class for deleting old exam data to be used by Tasks API.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\task;
use mod_exammanagement\general\MoodleDB;

require_once(__DIR__.'/../general/MoodleDB.php');

class delete_old_exam_data extends \core\task\scheduled_task { 
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('delete_old_exam_data', 'mod_exammanagement');
    }
 
    /**
     * Execute the task.
     */
    public function execute() {

        $MoodleDBObj = MoodleDB::getInstance();

        // send warning mails for soon to be deleted module instances

        $warningperiodOne = strtotime("-1 month");
        $warningperiodTwo = strtotime("-7 days");
        $warningperiodThree = strtotime("-1 day");
        $deletionDate = strtotime("-3 month");



        var_dump($warningperiodOne);
        var_dump($warningperiodTwo);
        var_dump($warningperiodThree);
        var_dump($deletionDate);

        var_dump(date('d.m.y h:m', $deletionDate));

        //$select = "correctioncompletiondate > UNIX_TIMESTAMP(DATE_SUB(NOW(),INTERVAL 180 DAY)";
        $select = "correctioncompletiondate > ".$deletionDate;

        
		$Test = $MoodleDBObj->getFieldsetFromRecordsInDB('exammanagement', 'correctioncompletiondate', $select);

        var_dump($Test);

        var_dump(date('d.m.y h:m', $Test[0]));


        //$MoodleDBObj->DeleteRecordsFromDBSelect($table, $select, $params)

        // delete expired  exam data and instances
        
        \core\task\manager::clear_static_caches(); // restart cron after running the task because it made many DB updates and clear cron cache (https://docs.moodle.org/dev/Task_API#Caches)
    }
}