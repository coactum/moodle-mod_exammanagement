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

    // updates for old versions
    if ($oldversion < 2018081700) {

        // Define table exammanagement to be created.
        $table = new xmldb_table('exammanagement');
        $field = new xmldb_field('results', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Conditionally launch add field for exammanagement.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Exammanagement savepoint reached.
        upgrade_mod_savepoint(true, 2018081700, 'exammanagement');

    }

    if ($oldversion < 2018083000) {

       // Define field importfileheaders to be added to exammanagement.
       $table = new xmldb_table('exammanagement');
       $field = new xmldb_field('importfileheaders', XMLDB_TYPE_TEXT, null, null, null, null, null);

       // Conditionally launch add field for exammanagement.
       if (!$dbman->field_exists($table, $field)) {
          $dbman->add_field($table, $field);
       }

       $field = new xmldb_field('tempimportfileheader', XMLDB_TYPE_TEXT, null, null, null, null, null, 'importfileheaders');
       // Conditionally launch add field for exammanagement.
       if (!$dbman->field_exists($table, $field)) {
          $dbman->add_field($table, $field);
       }

       // Exammanagement savepoint reached.
       upgrade_mod_savepoint(true, 2018083000, 'exammanagement');
    }

    if ($oldversion < 2018101000) {

       // Define field importfileheaders to be added to exammanagement.
       $table = new xmldb_table('exammanagement');
       $field = new xmldb_field('correctioncompletiondate', XMLDB_TYPE_TEXT, null, null, null, null, null);

       // Conditionally launch add field for exammanagement.
       if (!$dbman->field_exists($table, $field)) {
          $dbman->add_field($table, $field);
       }

       // Exammanagement savepoint reached.
       upgrade_mod_savepoint(true, 2018101000, 'exammanagement');
    }

    if ($oldversion < 2018121500) { // remove termbased participants table and replacve it with 

        // Define field importfileheaders to be added to exammanagement.
        $table = new xmldb_table('exammanagement_temp_part');
        $field = new xmldb_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
 
        // Conditionally launch add field for exammanagement.
        if (!$dbman->field_exists($table, $field)) {
           $dbman->add_field($table, $field);
        }

          // Define table exammanagement_participants to be created.
          $table = new xmldb_table('exammanagement_participants');

          // Adding fields to table exammanagement_participants.
          $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
          $table->add_field('plugininstanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
          $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
          $table->add_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
          $table->add_field('moodleuserid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
          $table->add_field('imtlogin', XMLDB_TYPE_CHAR, '25', null, null, null, null);
          $table->add_field('firstname', XMLDB_TYPE_CHAR, '50', null, null, null, null);
          $table->add_field('lastname', XMLDB_TYPE_CHAR, '50', null, null, null, null);
          $table->add_field('email', XMLDB_TYPE_CHAR, '100', null, null, null, null);
          $table->add_field('headerid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
          $table->add_field('roomid', XMLDB_TYPE_CHAR, '25', null, null, null, null);
          $table->add_field('roomname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
          $table->add_field('place', XMLDB_TYPE_CHAR, '25', null, null, null, null);
          $table->add_field('exampoints', XMLDB_TYPE_TEXT, null, null, null, null, null);
          $table->add_field('examstate', XMLDB_TYPE_TEXT, null, null, null, null, null);
          $table->add_field('timeresultsentered', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
          $table->add_field('bonus', XMLDB_TYPE_CHAR, '25', null, null, null, null);
  
          // Adding keys to table exammanagement_participants.
          $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
  
          // Adding indexes to table exammanagement_participants.
          $table->add_index('plugininstanceid', XMLDB_INDEX_NOTUNIQUE, array('plugininstanceid'));
  
          // Conditionally launch create table for exammanagement_participants.
          if (!$dbman->table_exists($table)) {
              $dbman->create_table($table);
          }
 
        // Exammanagement savepoint reached.
        upgrade_mod_savepoint(true, 2018121500, 'exammanagement');
    }

    if ($oldversion < 2019010700) { // remove termbased participants table and replacve it with 

        $table = new xmldb_table('exammanagement');
        $field = new xmldb_field('correctioncompletiondate', XMLDB_TYPE_TEXT, null, null, null, null, null, 'gradingscale');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'datadeletion');
        }

        // Define field deletionwarningmailids to be added to exammanagement.
        $table = new xmldb_table('exammanagement');
        $field = new xmldb_field('deletionwarningmailids', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Conditionally launch add field for exammanagement.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Exammanagement savepoint reached.
        upgrade_mod_savepoint(true, 2019010700, 'exammanagement');
    }

    if ($oldversion < 2019061700) { // added column for check if all data is deleted
        var_dump($oldversion);
        // Define field importfileheaders to be added to exammanagement.
        $table = new xmldb_table('exammanagement');
        $field = new xmldb_field('datadeleted', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
 
        // Conditionally launch add field for exammanagement.
        if (!$dbman->field_exists($table, $field)) {
           $dbman->add_field($table, $field);
        }

        // Exammanagement savepoint reached.
        upgrade_mod_savepoint(true, 2019061700, 'exammanagement');
    }

    return true;
}
