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
 * Code to be executed after the plugin's database scheme has been installed is defined here.
 *
 * @package     mod_exammanagement
 * @category    upgrade
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Custom code to be run on installing the plugin.
 */
function xmldb_exammanagement_install() {

  global $DB;

  $dbman = $DB->get_manager();

  //create new tables for new terms (category ids)

  $terms = $DB->get_fieldset_select('course_categories', 'name', '');

  if($terms){
    $year = false;

    foreach($terms as $termname){

      $cleanTermname = preg_replace("/[^0-9a-zA-Z]/", "",$termname);

      $cleanTermname = substr($cleanTermname, 0, 6);

      $dbname = 'exammanagement_part_'.strtolower($cleanTermname);

      if($dbname){

        // Define table exammanagement_participants to be created.
        $table = new xmldb_table($dbname);

        // Adding fields to table exammanagement_participants.
         $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
         $table->add_field('plugininstanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
         $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
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
         $table->add_field('bonuspoints', XMLDB_TYPE_CHAR, '25', null, null, null, null);

        // Adding keys to table exammanagement_participants.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table exammanagement_participants.
        $table->add_index('plugininstanceid', XMLDB_INDEX_NOTUNIQUE, array('plugininstanceid'));

        // Conditionally launch create table for exammanagement_participants.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
      }
    }
  }

  return true;
}
