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
 * class containing all db wrapper functions for moodle
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

defined('MOODLE_INTERNAL') || die();

class MoodleDB{

	#### singleton class ######

	public static function getInstance(){

		static $inst = null;
			if ($inst === null) {
				$inst = new MoodleDB();
			}
			return $inst;

	}

	#### wrapped Moodle DB functions #####

	 public function getFieldFromDB($table, $fieldname, $condition){
	 	global $DB;

	 	$field = $DB->get_field($table, $fieldname, $condition, '*', MUST_EXIST);

	 	return $field;
	 }

	public function getRecordFromDB($table, $condition){
		global $DB;

		$record = $DB->get_record($table, $condition);

		return $record;
	}

	public function getRecordsFromDB($table, $condition){
		global $DB;

		$records = $DB->get_records($table, $condition);

		return $records;
	}

	public function UpdateRecordInDB($table, $obj){
		global $DB;

		return $DB->update_record($table, $obj);
	}

	public function InsertRecordInDB($table, $dataobject){
		global $DB;

		return $DB->insert_record($table, $dataobject, $returnid=true, $bulk=false);
	}

	// public function InsertBulkRecordsInDB($table, $dataobjects){ // not used at the moment
	// 	global $DB;
	//
	// 	$DB->insert_records($table, $dataobjects);
	// }

}