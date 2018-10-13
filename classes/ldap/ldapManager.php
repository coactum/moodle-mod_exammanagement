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
use mod_exammanagement\general\User; // only for testing without real ldap!
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

	private static $instance = NULL;

	private $markedForPreload = array();

	private $preloadedValues = array();

	private function __construct($id, $e) {
		$this->id = $id; // only for testing without real ldap!
		$this->e = $e;		 // only for testing without real ldap!
	}

	public static function getInstance($id, $e){

		static $inst = null;
			if ($inst === null) {
				$inst = new ldapManager($id, $e);
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

	public function getIMTLogin2MatriculationNumberTest($userid, $login = false){ // only for testing without real ldap!
			require_once(__DIR__.'/../general/User.php');

			$UserObj = User::getInstance($this->id, $this->e);

			if($userid !== NULL){
				$user = $UserObj->getMoodleUser($userid);
				$matrNr = 700000 . $user->id;
			} else if($login){
				$matrNr = $login;
			}

			if($matrNr){
				return $matrNr;
			} else {
				return false;
			}
	}

	public function studentid2uid($ldapConnection, $pStudentId){

		if (empty($pStudentId)) {
				throw new Exception("No parameter given");
		}

		$dn	=	LDAP_OU	.	", "	.	LDAP_O	.	", "	.	LDAP_C;
		$filter = "(&(objectclass=" . LDAP_OBJECTCLASS_STUDENT . ")(" . LDAP_ATTRIBUTE_STUDID . "=" . $pStudentId . "))";

		$search = ldap_search($ldapConnection, $dn, $filter, array(LDAP_ATTRIBUTE_UID));
		$entry = ldap_first_entry($ldapConnection, $search);

		$result = @ldap_get_values($ldapConnection, $entry, LDAP_ATTRIBUTE_UID);
    ldap_free_result($search);

		return $result[ 0 ];
	}

	public function uid2studentid($ldapConnection, $uid){

			if (empty($uid)) {
					throw new Exception("No parameter given");
			}

			$dn = LDAP_OU . ", " . LDAP_O . ", " . LDAP_C;
			$filter = "(&(objectclass=" . LDAP_OBJECTCLASS_STUDENT . ")(" . LDAP_ATTRIBUTE_UID . "=" . $uid . "))";

			$search = ldap_search($ldapConnection, $dn, $filter, array(LDAP_ATTRIBUTE_STUDID));
			$entry = ldap_first_entry($ldapConnection, $search);

			$result = @ldap_get_values($ldapConnection, $entry, LDAP_ATTRIBUTE_STUDID);
			ldap_free_result($search);

			return $result[ 0 ];
	}
}
