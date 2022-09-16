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
 * A cron_task class for migrating plugininstanceid db field for the upb to be used by Tasks API.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\task;
use mod_exammanagement\general\MoodleDB;

require_once(__DIR__.'/../general/MoodleDB.php');

class upb_migrate_plugininstanceid_to_exammanagement extends \core\task\scheduled_task {
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('upb_migrate_plugininstanceid_to_exammanagement', 'mod_exammanagement');
    }

    /**
     * Execute the task.
     */
    public function execute() {

        mtrace('Starting scheduled task ' . get_string('upb_migrate_plugininstanceid_to_exammanagement', 'mod_exammanagement'));

        $MoodleDBObj = MoodleDB::getInstance();

        if ($rs = $MoodleDBObj->getRecordset('exammanagement_participants', array('exammanagement' => '0'))) {

            mtrace('Participants without value for exammanagement found');

            $count = 0;

            if($rs->valid()){

                mtrace('Migrating field plugininstanceid to exammanagement ...');

                foreach ($rs as $record) {

                    if(isset($record->plugininstanceid) && $record->plugininstanceid !== 0){
                        $cm = get_coursemodule_from_id('exammanagement', $record->plugininstanceid, 0, false, MUST_EXIST);

                        $exammanagement = $MoodleDBObj->getRecordFromDB('exammanagement', array('id' => $cm->instance), '*', MUST_EXIST);

                        $record->exammanagement = $exammanagement->id;

                        $MoodleDBObj->UpdateRecordInDB("exammanagement_participants", $record);

                        $count += 1;
                    } else {
                        mtrace('Error: Invalid field plugininstanceid for ' . $record);
                    }
                }

                $rs->close();

            } else {
                mtrace('Error: Invalid record set');
            }

            mtrace($count . ' participants successfully migrated');

            // delete whole temp_part table
            if($MoodleDBObj->checkIfRecordExists("exammanagement_temp_part", array())){

                mtrace('Deleting temp_participants ...');

                $MoodleDBObj->setFieldInDB('exammanagement', 'tempimportfileheader', NULL, array());

                $MoodleDBObj->DeleteRecordsFromDB("exammanagement_temp_part", array());

                mtrace('Deletion finished');

            }

        } else {
            mtrace('No participants without value for exammanagement found');
        }

        mtrace('Task finished');

        \core\task\manager::clear_static_caches(); // restart cron after running the task because it made many DB updates and clear cron cache (https://docs.moodle.org/dev/Task_API#Caches)
    }
}