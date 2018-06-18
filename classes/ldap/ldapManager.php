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

use mod_exammanagement\general\MoodleDB; // only for testing without real ldap!
use mod_exammanagement\general\exammanagementInstance; // only for testing without real ldap!

defined('MOODLE_INTERNAL') || die();

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

	private function __clone() {}

	//caching functions

	public function markForPreload($matriculationNumber){
		$this->markedForPreload[]=$matriculationNumber;
	}

	//fire up an big ldap request
	public function preload(){
		//add values to local db
		$matriculationNumberSetsTmp = $this->getLdapDataForMatriculationNumbers($this->markedForPreload);
		foreach ($matriculationNumberSetsTmp as $mnrSet){
			$index = $mnrSet["upbStudentID"];
			$value = $mnrSet;
			$this->preloadedValues[$index] = $value;
		}

		$this->markedForPreload = array();
	}

	public function isInCache($mnr){
		if (isset($this->preloadedValues[$mnr])){
			return TRUE;
		}
		return FALSE;
	}

	public function getCacheData($mnr){
		if (isset($this->preloadedValues[$mnr])){
			$value = $this->preloadedValues[$mnr];
			return $value;
		}
		return FALSE;
	}

	public function matriculationNumber2imtLogin($matNr){
		if ($this->isInCache($matNr)){
			$dataSet = $this->getCacheData($matNr);
			return $dataSet["uid"];
		} else {
			$this->markForPreload($matNr);
			$this->preload();
		}
		//no cache workflow

		$oldErrorReporting = error_reporting(); //temporary disable error reporting
		error_reporting(0);
		$result = $this->getLdapData($matNr);
		if ($result==FALSE) return "LGN".$matNr; //dummy data
		error_reporting($oldErrorReporting);
		return $result["imtLogin"];
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

	// public function matriculationNumber2firstName($matNr){
	// 	if ($this->isInCache($matNr)){
	// 		$dataSet = $this->getCacheData($matNr);
	// 		return $dataSet["givenName"];
	// 	} else {
	// 		$this->markForPreload($matNr);
	// 		$this->preload();
	// 	}
	// 	//no cache workflow
	//
	// 	$oldErrorReporting = error_reporting(); //temporary disable error reporting
	// 	error_reporting(0);
	// 	$result = $this->getLdapData($matNr);
	// 	if ($result==FALSE) return "FN".$matNr; //dummy data
	// 	error_reporting($oldErrorReporting);
	// 	return $result["firstname"];
	// }
	//
	// public function matriculationNumber2lastName($matNr){
	// 	if ($this->isInCache($matNr)){
	// 		$dataSet = $this->getCacheData($matNr);
	// 		return $dataSet["sn"];
	// 	} else {
	// 		$this->markForPreload($matNr);
	// 		$this->preload();
	// 	}
	// 	//no cache workflow
	//
	// 	$oldErrorReporting = error_reporting(); //temporary disable error reporting
	// 	error_reporting(0);
	// 	$result = $this->getLdapData($matNr);
	// 	if ($result==FALSE) return "LN".$matNr; //dummy data
	// 	error_reporting($oldErrorReporting);
	// 	return $result["lastname"];
	// }
	//
	// public function getFullName($matNr){
	// 	return $this->matriculationNumber2firstName($matNr)." ".$this->matriculationNumber2lastName($matNr);
	// }
	//
	//
	// /*
	//  * get mail address from ldap
	//  */
	// public function matriculationNumber2mail($matNr){
	// 	/*
	// 	if ($this->isInCache($matNr)){
	// 		$dataSet = $this->getCacheData($matNr);
	// 		return $dataSet["upbMailPreferredAddress"];
	// 	} else {
	// 		$this->markForPreload($matNr);
	// 		$this->preload();
	// 	}
	// 	*/
	// 	//no cache workflow
	//
	// 	$oldErrorReporting = error_reporting(); //temporary disable error reporting
	// 	error_reporting(0);
	// 	$result = $this->getLdapData($matNr);
	//
	// 	if ($result==FALSE) return "MAIL".$matNr; //dummy data
	// 	error_reporting($oldErrorReporting);
	// 	return $result["upbMailPreferredAddress"];
	// }


	/*
	 * get ldap data from ldap server and save it (for cache) to the database
	 *
	 * @matriculationNumber
	 * @writeInDataBase write in database for caching
	 *
	 * @return an array containing [matnr][imtLogin][LastName][FirstName]
	 */
	public function getLdapData($matriculationNumber){
		$user = array(); //[matnr][imtLogin][LastName][FirstName]
		try {
		  $lms_ldap = new lms_ldap();
		  $lms_ldap->bind( LDAP_LOGIN, LDAP_PASSWORD );
		}
		catch ( Exception $e ) {
			//paul_sync_log("PAUL_SYNC\t" . $e->getMessage(), PAUL_SYNC_LOGLEVEL_ERROR );
			return FALSE;
		}

		$uid = $lms_ldap->studentid2uid($matriculationNumber);
		$user["imtLogin"]=$uid;
		$ldap_attributes = $lms_ldap->get_ldap_attribute( array( "sn", "givenName", "upbMailPreferredAddress" ), $uid );

		$user["upbMailPreferredAddress"]=$ldap_attributes["upbMailPreferredAddress"];
		$user["firstname"]=$ldap_attributes["givenName"];
		$user["lastname"]=$ldap_attributes["sn"];
		$user["mnr"]=$matriculationNumber;
		if(!isset($user["firstname"])) return FALSE;
		if(!isset($user["lastname"])) return FALSE;
		if(!isset($user["mnr"])) return FALSE;

		//return ldap data
		return $user;
	}



	/*
	 * get ldap data from ldap server and save it (for cache) to the database
	 *
	 * @$arrayOfMatriculationNumbers
	 *
	 * @return an array containing [matnr][imtLogin][LastName][FirstName]??
	 */
	private function getLdapDataForMatriculationNumbers($arrayOfMatriculationNumbers=TRUE){
		if ($arrayOfMatriculationNumbers==FALSE) return FALSE;

		$filterString = "";
		$filterStringFirst=TRUE;
		//build a filter string
		foreach ($arrayOfMatriculationNumbers as $matriculationNumber){
			if ($filterStringFirst){
				$filterString = "(upbStudentID=".$matriculationNumber.")";
			} else {
				$filterString = "(|".$filterString."(upbStudentID=".$matriculationNumber."))";
			}
			$filterStringFirst=FALSE;
		}

		try {
		  $lms_ldap = new lms_ldap();
		  $lms_ldap->bind( LDAP_LOGIN, LDAP_PASSWORD );
		}
		catch ( Exception $e ) {
			return FALSE;
		}

		$ldap_attributes = $lms_ldap->get_ldap_attribute_for_various_data( array( "sn", "givenName", "upbStudentID", "uid", "upbMailPreferredAddress") , $filterString );
		return $ldap_attributes;
	}



}

?>
