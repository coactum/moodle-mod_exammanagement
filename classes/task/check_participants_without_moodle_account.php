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
 * A cron_task class for checking if participants without moodle account now have an account to be used by Tasks API.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\task;

/**
 * A cron_task class for checking if participants without moodle account now have an account to be used by Tasks API.
 *
 * @package   mod_exammanagement
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check_participants_without_moodle_account extends \core\task\scheduled_task {
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('check_participants_without_moodle_account', 'mod_exammanagement');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB;

        // Get all participants without moodle account.
        if ($DB->record_exists("exammanagement_participants", ['moodleuserid' => null])) {

            $nonemoodleparticipants = $DB->get_records("exammanagement_participants", ['moodleuserid' => null]);

            foreach ($nonemoodleparticipants as $participant) {

                // Check if some of the nonemoodle participants have a moodle account now.
                if ($DB->record_exists("user", ['username' => $participant->login])) {

                    // If this is the case set moodle id instead of username and email.
                    $user = $DB->get_records('user', ['username' => $participant->login]);

                    $participant->moodleuserid = $user->id;
                    $participant->login = null;
                    $participant->firstname = null;
                    $participant->lastname = null;
                    $participant->email = null;

                    $DB->update_record("exammanagement_participants", $participant);
                }
            }

            // Restart cron after running the task because it made many DB updates and clear cron cache
            // (https://docs.moodle.org/dev/Task_API#Caches).
            \core\task\manager::clear_static_caches();

        }
    }
}
