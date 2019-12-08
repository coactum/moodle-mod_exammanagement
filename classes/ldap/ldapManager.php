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
use core\notification;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/ldaplib.php');

class ldapManager{

	protected $id; // only for testing without real ldap!
	protected $e; // only for testing without real ldap!
	
	protected $dn;
	protected $ldapobjectclassstudent;
	protected $ldapfieldmatriculationnumber;
	protected $ldapfieldusername;
	protected $ldapfieldfirstname;
	protected $ldapfieldlastname;
	protected $ldapfieldemailadress;

	protected $missingconfig;

	private function __construct($id, $e) {
		$this->id = $id; // only for testing without real ldap!
		$this->e = $e;		 // only for testing without real ldap!

		$pluginconfig = get_config('mod_exammanagement');
		$ldapconfig = get_config('auth_ldap');

		// check if all required config is set in plugin settings or moodle ldap settings and set save missing elements in property
		$this->missingconfig = array();

		if($pluginconfig->ldapdn){
			$this->dn = $pluginconfig->ldapdn;
		} else if ($ldapconfig->contexts){
			$this->dn = $ldapconfig->contexts;
		} else {
			array_push($this->missingconfig, 'ldapdn');
		}

		if($pluginconfig->ldap_objectclass_student){
			$this->ldapobjectclassstudent = $pluginconfig->ldap_objectclass_student;
		} else {
			array_push($this->missingconfig, 'ldap_objectclass_student');
		}
		
		if($pluginconfig->ldap_field_map_matriculationnumber){
			$this->ldapfieldmatriculationnumber = $pluginconfig->ldap_field_map_matriculationnumber;
		} else {
			array_push($this->missingconfig, 'ldap_field_map_matriculationnumber');
		}

		if($pluginconfig->ldap_field_map_username){
			$this->ldapfieldusername = $pluginconfig->ldap_field_map_username;
		} else if ($ldapconfig->field_map_idnumber){
			$this->ldapfieldusername = $ldapconfig->field_map_idnumber;
		} else {
			array_push($this->missingconfig, 'ldap_field_map_username');
		}

		if($pluginconfig->ldap_field_map_firstname){
			$this->ldapfieldfirstname = $pluginconfig->ldap_field_map_firstname;
		} else if ($ldapconfig->field_map_firstname){
			$this->ldapfieldfirstname = $ldapconfig->field_map_firstname;
		} else {
			array_push($this->missingconfig, 'ldap_field_map_firstname');
		}

		if($pluginconfig->ldap_field_map_lastname){
			$this->ldapfieldlastname = $pluginconfig->ldap_field_map_lastname;
		} else if ($ldapconfig->field_map_lastname){
			$this->ldapfieldlastname = $ldapconfig->field_map_lastname;
		} else {
			array_push($this->missingconfig, 'ldap_field_map_lastname');
		}

		if($pluginconfig->ldap_field_map_mail){
			$this->ldapfieldemailadress = $pluginconfig->ldap_field_map_mail;
		} else if ($ldapconfig->field_map_email){
			$this->ldapfieldemailadress = $ldapconfig->field_map_email;
		} else {
			array_push($this->missingconfig, 'ldap_field_map_mail');
		}
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

		// if($config->host_url && $config->ldap_version && $config->user_type && $config->bind_dn && $config->bind_pw && $config->opt_deref){
			return true;
		// } else {
		// 	return false;
		// }
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

	public function getLoginForMatrNr($username, $disabledfeature){ // matrnr to imtlogin

		if($this->isLDAPenabled()){
			if($this->isLDAPconfigured()){
				$ldapConnection = $this->connect_ldap();

				if($ldapConnection){

					// if some required config is missing display error message and end method
					if(in_array('ldap_objectclass_student', $this->missingconfig) || in_array('ldap_field_map_matriculationnumber', $this->missingconfig) || in_array('ldap_field_map_username', $this->missingconfig)){
						$missingconfigstr = '';

						if(in_array('ldap_objectclass_student', $this->missingconfig)){
							$missingconfigstr .= 'ldap_objectclass_student, ';
						}

						if(in_array('ldap_field_map_matriculationnumber', $this->missingconfig)){
							$missingconfigstr .= 'ldap_field_map_matriculationnumber, ';
						}

						if(in_array('ldap_field_map_username', $this->missingconfig)){
							$missingconfigstr .= 'ldap_field_map_username';
						}

						if($disabledfeature){
							notification::error(get_string($disabledfeature, 'mod_exammanagement') . get_string('ldapconfigmissing', 'mod_exammanagement') . $missingconfigstr, 'error');
						} else {
							notification::error(get_string('ldapconfigmissing', 'mod_exammanagement') . $missingconfigstr, 'error');
						}
						
						return false;
					}

					$dn	=	$this->dn;
					$filter = "(&(objectclass=" . $this->ldapobjectclassstudent . ")(" . $this->ldapfieldmatriculationnumber . "=" . $username . "))";

					$search = ldap_search($ldapConnection, $dn, $filter, array($this->ldapfieldusername));
					$entry = ldap_first_entry($ldapConnection, $search);

					$result = @ldap_get_values($ldapConnection, $entry, $this->ldapfieldusername);
					ldap_free_result($search);

					return $result[ 0 ];
				} else {
					if($disabledfeature){
						notification::error(get_string($disabledfeature, 'mod_exammanagement') . get_string('ldapconnectionfailed', 'mod_exammanagement'), 'error');
					} else {
						notification::error(get_string('ldapconnectionfailed', 'mod_exammanagement'), 'error');
					}
					return false;
				}
			} else {
				if($disabledfeature){
					notification::error(get_string($disabledfeature, 'mod_exammanagement') . get_string('ldapnotconfigured', 'mod_exammanagement'), 'error');
				} else {
					notification::error(get_string('ldapnotconfigured', 'mod_exammanagement'), 'error');
				}
				return false;
			}
		} else {
			if($disabledfeature){
				notification::error(get_string($disabledfeature, 'mod_exammanagement') . get_string('ldapnotenabled', 'mod_exammanagement'), 'error');
			} else {
				notification::error(get_string('ldapnotenabled', 'mod_exammanagement'), 'error');
			}
			return false;
		}
	}

	public function getMatriculationNumbersForLogins($loginsArray){ // get matriculation numbers for array of user logins

		if($this->isLDAPenabled()){
			if($this->isLDAPconfigured()){
				$ldapConnection = $this->connect_ldap();

				if($ldapConnection){
		
					// if some required config is missing display error message and end method
					if(in_array('ldap_field_map_matriculationnumber', $this->missingconfig) || in_array('ldap_field_map_username', $this->missingconfig)){
						$missingconfigstr = '';

						if(in_array('ldap_field_map_matriculationnumber', $this->missingconfig)){
							$missingconfigstr .= 'ldap_field_map_matriculationnumber, ';
						}

						if(in_array('ldap_field_map_username', $this->missingconfig)){
							$missingconfigstr .= 'ldap_field_map_username';
						}

						notification::error(get_string('nomatrnravailable', 'mod_exammanagement'). ' ' .get_string('ldapconfigmissing', 'mod_exammanagement') . $missingconfigstr , 'error');
						return false;
					}

					$resultArr = array();

					// build ldap query string with all user logins
					$filterString = "";
					$filterStringFirst = true;
					
					if(isset($loginsArray)){
						foreach($loginsArray as $login){
							if ($filterStringFirst){ // first participant
									$filterString = "(".$this->ldapfieldusername."=".$login.")";
							} else { // all other participants
								$filterString = "(|".$filterString."(".$this->ldapfieldusername."=".$login."))";
							}
							$filterStringFirst = false;
						}

						$dn = $this->dn;
						$search = ldap_search( $ldapConnection, $dn, $filterString, array($this->ldapfieldusername, $this->ldapfieldmatriculationnumber));

						//get ldap attributes
						for ($entryID = ldap_first_entry($ldapConnection, $search); $entryID != false; $entryID = ldap_next_entry($ldapConnection, $entryID)){

							$login = @ldap_get_values($ldapConnection, $entryID, $this->ldapfieldusername);
							$matrnr = @ldap_get_values($ldapConnection, $entryID, $this->ldapfieldmatriculationnumber);

							$resultArr[$login[ 0 ]] = $matrnr[ 0 ];
						}

						ldap_free_result($search);

						if(isset($resultArr)){
							return $resultArr;
						} else {
							return false;
						}
					}
				} else {
					notification::error(get_string('nomatrnravailable', 'mod_exammanagement'). ' ' . get_string('ldapconnectionfailed', 'mod_exammanagement'), 'error');
					return false;
				}
			} else {
				notification::error(get_string('nomatrnravailable', 'mod_exammanagement'). ' ' . get_string('ldapnotconfigured', 'mod_exammanagement'), 'error');
				return false;
			}
		} else {
			notification::error(get_string('nomatrnravailable', 'mod_exammanagement'). ' ' . get_string('ldapnotenabled', 'mod_exammanagement'), 'error');
			return false;
		}
	}

	public function getLDAPAttributesForMatrNrs($matrNrsArray, $attributes, $externalIdentifier = false){ // get matriculation numbers for array of user logins

		if($this->isLDAPenabled()){
			if($this->isLDAPconfigured()){
				$ldapConnection = $this->connect_ldap();

				if($ldapConnection){

					// if some required config is missing display error message and end method
					if($attributes == 'usernames_and_matriculationnumbers'){ // if only matrnr and username is needed
						if(in_array('ldap_field_map_matriculationnumber', $this->missingconfig) || in_array('ldap_field_map_username', $this->missingconfig)){
							$missingconfigstr = '';
				
							if(in_array('ldap_field_map_matriculationnumber', $this->missingconfig)){
								$missingconfigstr .= 'ldap_field_map_matriculationnumber, ';
							}
				
							if(in_array('ldap_field_map_username', $this->missingconfig)){
								$missingconfigstr .= 'ldap_field_map_username';
							}
				
							notification::error(get_string('importmatrnrnotpossible', 'mod_exammanagement') . ' ' . get_string('ldapconfigmissing', 'mod_exammanagement') . $missingconfigstr , 'error');
							return false;
						} else {
							$attributes = array($this->ldapfieldusername, $this->ldapfieldmatriculationnumber);
						}
					} else if($attributes == 'all_attributes'){ // if matrnr, username, firstname, lastname and email is needed
						if(in_array('ldap_field_map_matriculationnumber', $this->missingconfig) || in_array('ldap_field_map_username', $this->missingconfig) || in_array('ldap_field_map_firstname', $this->missingconfig) || in_array('ldap_field_map_lastname', $this->missingconfig) || in_array('ldap_field_map_mail', $this->missingconfig)){
							$missingconfigstr = '';
				
							if(in_array('ldap_field_map_matriculationnumber', $this->missingconfig)){
								$missingconfigstr .= 'ldap_field_map_matriculationnumber, ';
							}
				
							if(in_array('ldap_field_map_username', $this->missingconfig)){
								$missingconfigstr .= 'ldap_field_map_username';
							}

							if(in_array('ldap_field_map_firstname', $this->missingconfig)){
								$missingconfigstr .= 'ldap_field_map_firstname';
							}

							if(in_array('ldap_field_map_lastname', $this->missingconfig)){
								$missingconfigstr .= 'ldap_field_map_lastname';
							}

							if(in_array('ldap_field_map_mail', $this->missingconfig)){
								$missingconfigstr .= 'ldap_field_map_mail';
							}
				
							notification::error(get_string('importmatrnrnotpossible', 'mod_exammanagement') . ' ' . get_string('ldapconfigmissing', 'mod_exammanagement') . $missingconfigstr , 'error');
							return false;
						} else {
							$attributes = array($this->ldapfieldusername, $this->ldapfieldmatriculationnumber, $this->ldapfieldlastname, $this->ldapfieldfirstname, $this->ldapfieldemailadress);
						}
					}
					
					$matrNrsArray = array_values($matrNrsArray);
					$externalIdentifier = array_values($externalIdentifier);

					$resultArr = array();
					$i = 0;

					// build ldap query string with all user matrnrs
					$filterString = "";
					$filterStringFirst = true;
					
					if(isset($matrNrsArray)){
						foreach($matrNrsArray as $matrnr){
							if ($filterStringFirst){ // first participant
									$filterString = "(".$this->ldapfieldmatriculationnumber."=".$matrnr.")";
							} else { // all other participants
								$filterString = "(|".$filterString."(".$this->ldapfieldmatriculationnumber."=".$matrnr."))";
							}
							$filterStringFirst = false;
						}

						$dn	=	$this->dn;
						$search = ldap_search( $ldapConnection, $dn, $filterString, $attributes);

						//get ldap attributes
						for ($entryID = ldap_first_entry($ldapConnection, $search); $entryID != false; $entryID = ldap_next_entry($ldapConnection,$entryID)){
							foreach( $attributes as $attribute ){
								$value = ldap_get_values( $ldapConnection, $entryID, $attribute );

								switch ($attribute){

									case $this->ldapfieldusername:
										$result['login'] = $value[ 0 ];
										break;
									case $this->ldapfieldlastname:
										$result['lastname'] = $value[ 0 ];
										break;
									case $this->ldapfieldfirstname:
										$result['firstname'] = $value[ 0 ];
										break;
									case $this->ldapfieldemailadress:
										$result['email'] = $value[ 0 ];
										break;
								}
							}
							
							$matrnr = @ldap_get_values($ldapConnection, $entryID, $this->ldapfieldmatriculationnumber)[0];

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
				} else {
					notification::error(get_string('importmatrnrnotpossible', 'mod_exammanagement'). ' ' . get_string('ldapconnectionfailed', 'mod_exammanagement'), 'error');
					return false;
				}
			} else {
				notification::error(get_string('importmatrnrnotpossible', 'mod_exammanagement'). ' ' . get_string('ldapnotconfigured', 'mod_exammanagement'), 'error');
				return false;
			}
		} else {
			notification::error(get_string('importmatrnrnotpossible', 'mod_exammanagement'). ' ' . get_string('ldapnotenabled', 'mod_exammanagement'), 'error');
			return false;
		}
	}
}