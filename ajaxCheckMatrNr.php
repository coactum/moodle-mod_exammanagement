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
 * checks matrnr for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use stdClass;
use mod_exammanagement\ldap\ldapManager;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once(__DIR__.'/../ldap/ldapManager.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e  = optional_param('e', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$matrnr  = optional_param('matrnr', 0, PARAM_INT);

$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);
$MoodleObj = Moodle::getInstance($id, $e);
$MoodleDBObj = MoodleDB::getInstance();
$LdapManagerObj = ldapManager::getInstance($this->id, $this->e);

echo "test";

if($MoodleObj->checkCapability('mod/exammanagement:viewinstance')){

  echo "test";

  if($ExammanagementInstanceObj->checkIfValidMatrNr($matrnr)){
      $userid = $LdapManagerObj->getMatriculationNumber2ImtLoginTest($matrnr);

      $participantsIds = json_decode($ExammanagementInstanceObj->moduleinstance->participants);

      if(in_array($userid, $participantsIds)){
        $results = json_decode($ExammanagementInstanceObj->moduleinstance->results);

        foreach($results as $key => $resultObj){
          if ($resultObj->uid == $userid){
              echo $resultObj;
              break;
          }
        }
        echo true;
      } else {
        echo false;
      }
  } else {
     echo false;
  }
}
