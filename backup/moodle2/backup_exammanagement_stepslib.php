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
 * Backup steps for mod_exammanagement are defined here.
 *
 * @package     mod_exammanagement
 * @category    backup
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the complete structure for backup, with file and id annotations.
 */
class backup_exammanagement_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the structure of the resulting xml file.
     *
     * @return backup_nested_element The structure wrapped by the common 'activity' element.
     */
    protected function define_structure() {
        $userinfo = $this->get_setting_value('userinfo');

        // Replace with the attributes and final elements that the element will handle.
        $exammanagement = new backup_nested_element('exammanagement', array('id'), array(
            'name', 'intro', 'introformat', 'timecreated', 'timemodified', 'categoryid',
            'password', 'rooms', 'examtime', 'importfileheaders', 'tempimportfileheader',
            'tasks', 'textfield', 'assignmentmode', 'datetimevisible', 'roomvisible', 'placevisible',
            'bonusvisible', 'resultvisible', 'gradingscale', 'datadeletion', 'deletionwarningmailids',
            'examreviewtime', 'examreviewroom', 'examreviewvisible', 'datadeleted', 'misc'));

        $participants = new backup_nested_element('participants');
        $participant = new backup_nested_element('participant', array('id'), array(
            'courseid', 'categoryid', 'moodleuserid', 'login', 'firstname', 'lastname', 'email',
            'headerid', 'roomid', 'roomname', 'place', 'exampoints', 'examstate', 'timeresultsentered',
            'bonussteps', 'bonuspoints'));

        // Build the tree with these elements with $root as the root of the backup tree.
        $exammanagement->add_child($participants);
        $participants->add_child($participant);

        // Define the source tables for the elements.
        $exammanagement->set_source_table('exammanagement', array('id' => backup::VAR_ACTIVITYID));

        if ($userinfo) {
            $participant->set_source_table('exammanagement_participants', array('exammanagement' => backup::VAR_PARENTID));
        }

        // Define id annotations.
        if ($userinfo) {
            $participant->annotate_ids('user', 'moodleuserid');
        }

        // Define file annotations.
        // component, filearea, elementname.
        $exammanagement->annotate_files('mod_exammanagement', 'intro', null); // This file area has no itemid.

        return $this->prepare_activity_structure($exammanagement);
    }
}
