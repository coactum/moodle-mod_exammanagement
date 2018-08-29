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
 * class containing all build forms methods for moodle
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\ldap;

use mod_exammanagement\general\MoodleDB;
use mod_exammanagement\general\exammanagementInstance; // only for testing without real ldap!
use Exception;

defined('MOODLE_INTERNAL') || die();

defined("LDAP_OU") or define("LDAP_OU", "ou=People");
defined("LDAP_O") or define("LDAP_O", "o=upb");
defined("LDAP_C") or define("LDAP_C", "c=de");
defined("LDAP_OBJECTCLASS_STUDENT") or define("LDAP_OBJECTCLASS_STUDENT", "upbStudent");
defined("LDAP_ATTRIBUTE_STUDID") or define("LDAP_ATTRIBUTE_STUDID", "upbStudentID");
defined("LDAP_ATTRIBUTE_UID") or define("LDAP_ATTRIBUTE_UID", "uid");

global $CFG;
require_once($CFG->libdir.'/ldaplib.php');

class ldapManager{

	protected $id; // only for testing without real ldap!
	protected $e; // only for testing without real ldap!
	protected $test; //only for testing ldap

	private static $instance = NULL;

	private $markedForPreload = array();

	private $preloadedValues = array();

	private function __construct($id, $e, $test) {
		$this->id = $id; // only for testing without real ldap!
		$this->e = $e;		 // only for testing without real ldap!
		$this->test = $test;		 // only for testing without real ldap!
	}

	public static function getInstance($id, $e, $test = false){

		static $inst = null;
			if ($inst === null) {
				$inst = new ldapManager($id, $e, $test);
			}
			return $inst;

	}

	public function connect_ldap(){
			$config = get_config('auth_ldap');

			return $connection = ldap_connect_moodle(
				$config->host_url,
				$config->ldap_version,
				$config->user_type,
				$config->bind_dn,
				$config->bind_pw,
				$config->opt_deref,
				$debuginfo,
				$config->start_tls
			);

	}

	public function is_LDAP_config(){ //for local testing, should be deleted
			$config = get_config('auth_ldap');

			if($config->host_url){
					return true;
			} else {
					return false;
			}
	}

	public function getMatriculationNumber2ImtLoginTest($matrNr){ // only for testing without real ldap!
			require_once(__DIR__.'/../general/MoodleDB.php');

			$MoodleDBObj = MoodleDB::getInstance($this->id, $this->e);

			$temp = explode('_', $matrNr);

			$imtlogin = substr($temp[0], -3);

			$moodleuserid = $MoodleDBObj->getFieldFromDB('user','id', array('username' => 'tool_generator_000'.$imtlogin));

			return $moodleuserid;
	}

	public function getIMTLogin2MatriculationNumberTest($userid){ // only for testing without real ldap!
			require_once(__DIR__.'/../general/exammanagementInstance.php');

			$exammanagementInstanceObj = exammanagementInstance::getInstance($this->id, $this->e);

			$user = $exammanagementInstanceObj->getMoodleUser($userid);

			// constructing test MatrN., later needs to be readed from csv-File

			$matrNr = 70 . $user->id;;

			$array = str_split($user->firstname);

			$matrNr .= ord($array[0]);
			$matrNr .= ord($array[2]);

			$matrNr = substr($matrNr, 0, 6);

			return $matrNr;

	}

	public function studentid2uid($ldapConnection, $pStudentId){
		require_once(__DIR__.'/../general/MoodleDB.php');

		$MoodleDBObj = MoodleDB::getInstance($this->id, $this->e);

			if (empty($pStudentId)) {
					throw new Exception("No parameter given");
			}

		$dn	=	LDAP_OU	.	", "	.	LDAP_O	.	", "	.	LDAP_C;
		$filter = "(&(objectclass=" . LDAP_OBJECTCLASS_STUDENT . ")(" . LDAP_ATTRIBUTE_STUDID . "=" . $pStudentId . "))";

		$search = ldap_search($ldapConnection, $dn, $filter, array(LDAP_ATTRIBUTE_UID));
		$entry = ldap_first_entry($ldapConnection, $search);

		$result = @ldap_get_values($ldapConnection, $entry, LDAP_ATTRIBUTE_UID);
    ldap_free_result($search);

		if($this->test){

			if($ldapConnection){
				var_dump("Verbindung zum LDAP wurde aufgebaut");
				var_dump("Folgende Matrikelnummer wird ans LDAP gesendet:");
				var_dump($pStudentId);
			} else {
				var_dump("Keine Verbindung zum LDAP möglich");
			}
		}

		if(isset($result[ 0 ])){

				if($this->test&&$ldapConnection){
						var_dump("Für diese Matrikelnummer wurde folgender IMT-Benutzername gefunden:");
						var_dump($result[ 0 ]);
				}

				$moodleuserid = $MoodleDBObj->getFieldFromDB('user','id', array('username' => $result[ 0 ]));

				if($this->test&&$ldapConnection){
						var_dump("Für diesen Benutzernamen wird in der PANDA-Datenbank ein Benutzer mit der folgenden ID gefunden:");
						var_dump($moodleuserid);
				}

				return $moodleuserid;
		} else{
				if($this->test&&$ldapConnection){
						var_dump("Für diese Matrikelnummer wurde kein IMT-Benutzername.");
				}
			 	return false;
		}
	}

	public function uid2studentid($ldapConnection, $moodleuserid){
			require_once(__DIR__.'/../general/MoodleDB.php');

			$MoodleDBObj = MoodleDB::getInstance($this->id, $this->e);

			if($this->test){
					if($ldapConnection){
						var_dump("Verbindung zum LDAP wurde aufgebaut");
						var_dump("Folgende Moodleuserid wird verarbeitet:");
						var_dump($moodleuserid);
					} else {
						var_dump("Keine Verbindung zum LDAP möglich");
					}
			}

			$pUId = $MoodleDBObj->getFieldFromDB('user','username', array('id' => $moodleuserid));

			if($this->test&&$ldapConnection){
					var_dump("Folgender Benutzername soll am LDAP überprüft werden:");
					var_dump($pUId);
			}

			if (empty($pUId)) {
					throw new Exception("No parameter given");
			}

			$dn = LDAP_OU . ", " . LDAP_O . ", " . LDAP_C;
			$filter = "(&(objectclass=" . LDAP_OBJECTCLASS_STUDENT . ")(" . LDAP_ATTRIBUTE_UID . "=" . $pUId . "))";

			$search = ldap_search($ldapConnection, $dn, $filter, array(LDAP_ATTRIBUTE_STUDID));
			$entry = ldap_first_entry($ldapConnection, $search);

			$result = @ldap_get_values($ldapConnection, $entry, LDAP_ATTRIBUTE_STUDID);
			ldap_free_result($search);

			if($this->test&&$ldapConnection){
					var_dump("Für diesen Benutzernamen wurde folgende Matrikelnummer gefunden:");
					var_dump($result[ 0 ]);
			}

			return $result[ 0 ];
	}
}
