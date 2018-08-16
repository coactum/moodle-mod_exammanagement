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
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/upgradelib.php');

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
    // https://docs.moodle.org/dev/XMLDB_editor

    if ($oldversion < 2018081700) {

        // Define table exammanagement to be created.
        $table = new xmldb_table('exammanagement');

        // Adding fields to table exammanagement.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('intro', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('categoryid', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('examtime', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('participants', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('tmpparticipants', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('tasks', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('textfield', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('rooms', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('stateofplaces', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('assignedplaces', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('datetimevisible', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('roomvisible', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('placevisible', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('gradingscale', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('results', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('misc', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table exammanagement.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table exammanagement.
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));

        // Conditionally launch create table for exammanagement.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Exammanagement savepoint reached.
        upgrade_mod_savepoint(true, XXXXXXXXXX, 'exammanagement');
    }

    return true;
}
