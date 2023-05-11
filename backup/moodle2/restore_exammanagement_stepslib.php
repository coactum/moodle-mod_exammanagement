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
 * All the steps to restore mod_exammanagement are defined here.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Defines the structure step to restore one mod_exammanagement activity.
 */
class restore_exammanagement_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines the structure to be restored.
     *
     * @return restore_path_element[].
     */
    protected function define_structure() {
        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('exammanagement', '/activity/exammanagement');

        if ($userinfo) {
            $paths[] = new restore_path_element('exammanagement_participant', '/activity/exammanagement/participants/participant');
        }

        return $this->prepare_activity_structure($paths);
    }

    /**
     * Restore exammanagement.
     *
     * @param object $data data.
     */
    protected function process_exammanagement($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $newitemid = $DB->insert_record('exammanagement', $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Restore exammanagement participant.
     *
     * @param object $data data.
     */
    protected function process_exammanagement_participant($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;
        $data->courseid = $this->get_courseid();

        $data->exammanagement = $this->get_new_parentid('exammanagement');

        if (isset($data->moodleuserid)) {
            $data->moodleuserid = $this->get_mappingid('user', $data->moodleuserid);
        }

        $newitemid = $DB->insert_record('exammanagement_participants', $data);
        $this->set_mapping('exammanagement_participant', $oldid, $newitemid, true);  // Parameter true necessary for file handling.
    }

    /**
     * Defines post-execution actions like restoring files.
     */
    protected function after_execute() {
        // Add exammanagement related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_exammanagement', 'intro', null);

        return;
    }
}
