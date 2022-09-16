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
 * A cron_task class for deleting unassigned custom exam rooms to be used by Tasks API.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\task;
use mod_exammanagement\general\MoodleDB;

require_once(__DIR__.'/../general/MoodleDB.php');

class delete_unassigned_custom_rooms extends \core\task\scheduled_task {
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('delete_unassigned_custom_rooms', 'mod_exammanagement');
    }

    /**
     * Execute the task.
     */
    public function execute() {

        $MoodleDBObj = MoodleDB::getInstance();

        if ($rs = $MoodleDBObj->getRecordsetSelect("exammanagement_rooms", "type = 'customroom'")) {

            if($rs->valid()){

                foreach ($rs as $record) {

                    if(!$MoodleDBObj->checkIfRecordExists('user', array('id' => $record->moodleuserid))){
                        $MoodleDBObj->DeleteRecordsFromDB("exammanagement_rooms", array('id' => $record->id));
                    }

                }

                $rs->close();
            }

        }

        \core\task\manager::clear_static_caches(); // restart cron after running the task because it made many DB updates and clear cron cache (https://docs.moodle.org/dev/Task_API#Caches)
    }
}