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
 * Plugin upgrade steps are defined here.
 *
 * @package     mod_exammanagement
 * @category    upgrade
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute mod_exammanagement upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_exammanagement_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // For further information please read the Upgrade API documentation:
    // https://docs.moodle.org/dev/Upgrade_API
    //
    // You will also have to create the db/install.xml file by using the XMLDB Editor.
    // Documentation for the XMLDB Editor can be found at:
    // https://docs.moodle.org/dev/XMLDB_editor.

    if ($oldversion < 2024022900) { // Remove legacy field for plugininstanceid.

        // Define field plugininstanceid to be removed to exammanagement_participants.
        $table = new xmldb_table('exammanagement_participants');
        $field = new xmldb_field('plugininstanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        $index = new xmldb_index('plugininstanceid', XMLDB_INDEX_NOTUNIQUE, ['plugininstanceid']);

        // Conditionally launch remove index.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Conditionally launch remove field.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field plugininstanceid to be removed to exammanagement.
        $table = new xmldb_table('exammanagement_temp_part');

        // Conditionally launch remove index.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Conditionally launch remove field.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2024022900, 'exammanagement');
    }

    return true;
}
