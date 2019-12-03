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
 * class containing all ldap methods for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
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

	//private static $instance = NULL;

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

	public function isLDAPenabled(){
		if(get_config('mod_exammanagement', 'enableldap')){
			return true;
		} else {
			return false;
		}
	}

	public function isLDAPconfigured(){
		$config = get_config('auth_ldap');

		if($config->host_url && $config->ldap_version && $config->user_type && $config->bind_dn && $config->bind_pw && $config->opt_deref){
			return true;
		} else {
			return false;
		}
	}

	public function getMatriculationNumber2ImtLoginTest($matrNr){ // only for testing without real ldap!

		global $SESSION;
		
		if(isset($SESSION->ldaptest) && $SESSION->ldaptest === true){
			$MoodleDBObj = MoodleDB::getInstance();

			$temp = explode('_', $matrNr);

			$imtlogin = substr($temp[0], -3);

			$imtlogin = str_pad(intval($imtlogin), 3, "0", STR_PAD_LEFT);

			$moodleuserid = $MoodleDBObj->getFieldFromDB('user','id', array('username' => 'tool_generator_000'.$imtlogin));
			
			if($moodleuserid){
				return $moodleuserid;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function getMatriculationNumber2ImtLoginNoneMoodleTest($matrNr){ // only for testing without real ldap!

		global $SESSION;

		if(isset($SESSION->ldaptest) && $SESSION->ldaptest === true){

			$temp = explode('_', $matrNr);

			$imtlogin = 'tool_generator_000'.substr($temp[0], -3);

			if($imtlogin){
				return $imtlogin;
			} else {
				return false;
			}
		} else {
			return false;
		}

	}

	public function getIMTLogin2MatriculationNumberTest($userid, $login = false){ // only for testing without real ldap!

		global $SESSION;
			
		if(isset($SESSION->ldaptest) && $SESSION->ldaptest === true){
		
				require_once(__DIR__.'/../general/User.php');

				if($userid !== NULL && $userid !== false){
					$matrNr = str_pad($userid, 6, "0", STR_PAD_LEFT);
					$matrNr = 7 . $matrNr;
				} else if($login){
					$login = explode('_', $login);
					$imtlogin = substr($login[2], -3);
					$matrNr = str_pad($imtlogin, 6, "0", STR_PAD_LEFT);
					$matrNr = 7 . $matrNr;
				}

				if($matrNr == '7000057' || $matrNr == '7000082' || $matrNr == '7000088'){
					return false;
				}else if($matrNr){
					return $matrNr;
				} else {
					return false;
				}
		} else {
			return false;
		}
	}

	public function getLoginForMatrNr($ldapConnection, $pStudentId){ // matrnr to imtlogin
		if (empty($pStudentId)) {
				throw new Exception(get_string('no_param_given', 'mod_exammanagement'));
		}

		$dn	=	LDAP_OU	.	", "	.	LDAP_O	.	", "	.	LDAP_C;
		$filter = "(&(objectclass=" . LDAP_OBJECTCLASS_STUDENT . ")(" . LDAP_ATTRIBUTE_STUDID . "=" . $pStudentId . "))";

		$search = ldap_search($ldapConnection, $dn, $filter, array(LDAP_ATTRIBUTE_UID));
		$entry = ldap_first_entry($ldapConnection, $search);

		$result = @ldap_get_values($ldapConnection, $entry, LDAP_ATTRIBUTE_UID);
    	ldap_free_result($search);

		return $result[ 0 ];
	}

	public function getMatriculationNumbersForLogins($ldapConnection, $loginsArray){ // get matriculation numbers for array of user logins

		$resultArr = array();

		// build ldap query string with all user logins
		$filterString = "";
		$filterStringFirst = true;
		
		if(isset($loginsArray)){
			foreach($loginsArray as $login){
				if ($filterStringFirst){ // first participant
						$filterString = "(".LDAP_ATTRIBUTE_UID."=".$login.")";
				} else { // all other participants
					$filterString = "(|".$filterString."(".LDAP_ATTRIBUTE_UID."=".$login."))";
				}
				$filterStringFirst = false;
			}

			$dn = LDAP_OU . ", " . LDAP_O . ", " . LDAP_C;
			$search = ldap_search( $ldapConnection, $dn, $filterString, array(LDAP_ATTRIBUTE_UID, LDAP_ATTRIBUTE_STUDID));

			//get ldap attributes
			for ($entryID = ldap_first_entry($ldapConnection, $search); $entryID != false; $entryID = ldap_next_entry($ldapConnection, $entryID)){

				$login = @ldap_get_values($ldapConnection, $entryID, LDAP_ATTRIBUTE_UID);
				$matrnr = @ldap_get_values($ldapConnection, $entryID, LDAP_ATTRIBUTE_STUDID);

				$resultArr[$login[ 0 ]] = $matrnr[ 0 ];
			}

			ldap_free_result($search);

			if(isset($resultArr)){
				return $resultArr;
			} else {
				return false;
			}
		}
	}

	public function getLDAPAttributesForMatrNrs($ldapConnection, $matrNrsArray, $attributes, $externalIdentifier = false){ // get matriculation numbers for array of user logins

		// matrnr and identifier in ldap method before array_values

		$matrNrsArray = array_values($matrNrsArray);
		$externalIdentifier = array_values($externalIdentifier);

		// matrnr and identifier in ldap method after array_values

		$resultArr = array();
		$i = 0;

		// build ldap query string with all user matrnrs
		$filterString = "";
		$filterStringFirst = true;
		
		if(isset($matrNrsArray)){
			foreach($matrNrsArray as $matrnr){
				if ($filterStringFirst){ // first participant
						$filterString = "(".LDAP_ATTRIBUTE_STUDID."=".$matrnr.")";
				} else { // all other participants
					$filterString = "(|".$filterString."(".LDAP_ATTRIBUTE_STUDID."=".$matrnr."))";
				}
				$filterStringFirst = false;
			}

			$dn = LDAP_OU . ", " . LDAP_O . ", " . LDAP_C;
			$search = ldap_search( $ldapConnection, $dn, $filterString, $attributes);

			//get ldap attributes
			for ($entryID = ldap_first_entry($ldapConnection, $search); $entryID != false; $entryID = ldap_next_entry($ldapConnection,$entryID)){
				foreach( $attributes as $attribute ){
					$value = ldap_get_values( $ldapConnection, $entryID, $attribute );

					switch ($attribute){

						case "uid":
							$result['login'] = $value[ 0 ];
							break;
						case "sn":
							$result['lastname'] = $value[ 0 ];
							break;
						case "givenName":
							$result['firstname'] = $value[ 0 ];
							break;
						case "upbMailPreferredAddress":
							$result['email'] = $value[ 0 ];
							break;
					}
				}
				
				$matrnr = @ldap_get_values($ldapConnection, $entryID, LDAP_ATTRIBUTE_STUDID)[0];

				if(!isset($externalIdentifier) || !$externalIdentifier){
					$resultArr[$matrnr] = $result;
				} else {
					$result['matrnr'] = $matrnr;

					$resultArr[$externalIdentifier[array_search($matrnr, $matrNrsArray)]] = $result;
				}

				$i++;
			}

			// results at the end of ldap method

			ldap_free_result($search);

			if(isset($resultArr)){
				return $resultArr;
			} else {
				return false;
			}
		}
	}
}