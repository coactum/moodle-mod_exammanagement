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
        $field = new xmldb_field('results', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Conditionally launch add field for exammanagement.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Exammanagement savepoint reached.
        upgrade_mod_savepoint(true, 2018081700, 'lesson');

    }

    return true;
}
