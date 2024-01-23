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
 * Class containing all ldap methods for exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\ldap;

use core\notification;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/ldaplib.php');

/**
 * Class containing all ldap methods for exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ldapmanager {

    /** @var string */
    protected $dn;
    /** @var string */
    protected $ldapobjectclass;
    /** @var string */
    protected $ldapfieldmatriculationnumber;
    /** @var string */
    protected $ldapfieldusername;
    /** @var string */
    protected $ldapfieldfirstname;
    /** @var string */
    protected $ldapfieldlastname;
    /** @var string */
    protected $ldapfieldemailadress;
    /** @var string */
    protected $missingconfig;

    /**
     * Constructs the ldap manager.
     */
    private function __construct() {

        $pluginconfig = get_config('mod_exammanagement');
        $ldapconfig = get_config('auth_ldap');

        // Check if all required config is set in plugin settings or moodle ldap settings and set save missing elements in property.
        $this->missingconfig = [];

        if ($pluginconfig->ldapdn) {
            $this->dn = $pluginconfig->ldapdn;
        } else if ($ldapconfig->contexts) {
            $this->dn = $ldapconfig->contexts;
        } else {
            array_push($this->missingconfig, 'ldapdn');
        }

        if ($pluginconfig->ldap_objectclass_student) {
            $this->ldapobjectclass = $pluginconfig->ldap_objectclass;
        }

        if ($pluginconfig->ldap_field_map_matriculationnumber) {
            $this->ldapfieldmatriculationnumber = $pluginconfig->ldap_field_map_matriculationnumber;
        } else {
            array_push($this->missingconfig, 'ldap_field_map_matriculationnumber');
        }

        if ($pluginconfig->ldap_field_map_username) {
            $this->ldapfieldusername = $pluginconfig->ldap_field_map_username;
        } else if ($ldapconfig->field_map_idnumber) {
            $this->ldapfieldusername = $ldapconfig->field_map_idnumber;
        } else {
            array_push($this->missingconfig, 'ldap_field_map_username');
        }

        if ($pluginconfig->ldap_field_map_firstname) {
            $this->ldapfieldfirstname = $pluginconfig->ldap_field_map_firstname;
        } else if ($ldapconfig->field_map_firstname) {
            $this->ldapfieldfirstname = $ldapconfig->field_map_firstname;
        } else {
            array_push($this->missingconfig, 'ldap_field_map_firstname');
        }

        if ($pluginconfig->ldap_field_map_lastname) {
               $this->ldapfieldlastname = $pluginconfig->ldap_field_map_lastname;
        } else if ($ldapconfig->field_map_lastname) {
               $this->ldapfieldlastname = $ldapconfig->field_map_lastname;
        } else {
            array_push($this->missingconfig, 'ldap_field_map_lastname');
        }

        if ($pluginconfig->ldap_field_map_mail) {
            $this->ldapfieldemailadress = $pluginconfig->ldap_field_map_mail;
        } else if ($ldapconfig->field_map_email) {
            $this->ldapfieldemailadress = $ldapconfig->field_map_email;
        } else {
            array_push($this->missingconfig, 'ldap_field_map_mail');
        }
    }

    /**
     * Method for getting the singleton class.
     *
     * @return object $inst The singleton class.
     */
    public static function getinstance() {

        static $inst = null;
        if ($inst === null) {
            $inst = new ldapmanager();
        }
        return $inst;
    }

    /**
     * Connecting to ldap.
     *
     * @return object $connection The connection.
     */
    private function connect_ldap() {
        $config = get_config('auth_ldap');

        $connection = ldap_connect_moodle(
         $config->host_url,
         $config->ldap_version,
         $config->user_type,
         $config->bind_dn,
         $config->bind_pw,
         $config->opt_deref,
         $debuginfo,
         $config->start_tls
        );
        return $connection;
    }

    /**
     * Check if ldap is enabled
     *
     * @return bool
     */
    public function isldapenabled() {
        if (get_config('mod_exammanagement', 'enableldap')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if ldap is configured
     *
     * @return bool
     */
    public function isldapconfigured() {
        $config = get_config('auth_ldap');

        if ($config->host_url && $config->bind_dn && $config->bind_pw) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get login for matriculation number
     *
     * @param string $username The username
     * @param bool $disabledfeature The help text icon
     * @return object
     */
    public function getloginformatrnr($username, $disabledfeature) {

        if ($this->isldapenabled()) {
            if ($this->isldapconfigured()) {
                $connection = $this->connect_ldap();

                if ($connection) {

                    // If some required config is missing display error message and end method.
                    if (in_array('ldap_objectclass', $this->missingconfig) ||
                        in_array('ldap_field_map_matriculationnumber', $this->missingconfig) ||
                        in_array('ldap_field_map_username', $this->missingconfig)) {

                        $missingconfigstr = '';

                        if (in_array('ldap_field_map_matriculationnumber', $this->missingconfig)) {
                            $missingconfigstr .= 'ldap_field_map_matriculationnumber, ';
                        }

                        if (in_array('ldap_field_map_username', $this->missingconfig)) {
                            $missingconfigstr .= 'ldap_field_map_username';
                        }

                        if ($disabledfeature) {
                            notification::error(get_string($disabledfeature, 'mod_exammanagement') . ' ' .
                                get_string('ldapconfigmissing', 'mod_exammanagement') . ' ' . $missingconfigstr, 'error');
                        } else {
                            notification::error(get_string('ldapconfigmissing', 'mod_exammanagement') . $missingconfigstr, 'error');
                        }

                        return false;
                    }

                    $dn = $this->dn;

                    if ($this->ldapobjectclass) {
                        $filter = "(&(objectclass=" . $this->ldapobjectclass . ")(" . $this->ldapfieldmatriculationnumber .
                            "=" . $username . "))";
                    } else {
                        $filter = "(" . $this->ldapfieldmatriculationnumber . "=" . $username . ")";
                    }
                        $search = ldap_search($connection, $dn, $filter, [$this->ldapfieldusername]);

                    if ($search) {
                        $entry = ldap_first_entry($connection, $search);

                        $result = @ldap_get_values($connection, $entry, $this->ldapfieldusername);
                        ldap_free_result($search);

                        return $result[0];
                    } else {
                        return false;
                    }
                } else {
                    if ($disabledfeature) {
                        notification::error(get_string($disabledfeature, 'mod_exammanagement') . ' ' .
                            get_string('connectionfailed', 'mod_exammanagement'), 'error');
                    } else {
                        notification::error(get_string('connectionfailed', 'mod_exammanagement'), 'error');
                    }
                    return false;
                }
            } else {
                if ($disabledfeature) {
                    notification::error(get_string($disabledfeature, 'mod_exammanagement') . ' ' .
                        get_string('ldapnotconfigured', 'mod_exammanagement'), 'error');
                } else {
                    notification::error(get_string('ldapnotconfigured', 'mod_exammanagement'), 'error');
                }
                return false;
            }
        } else {
            if ($disabledfeature) {
                notification::error(get_string($disabledfeature, 'mod_exammanagement') . ' ' .
                    get_string('ldapnotenabled', 'mod_exammanagement'), 'error');
            } else {
                notification::error(get_string('ldapnotenabled', 'mod_exammanagement'), 'error');
            }
            return false;
        }
    }

    /**
     * Get matriculation numbers for an array of user logins.
     *
     * @param array $logins The user logins
     * @return object
     */
    public function getmatrnrsforlogins($logins) {

        if ($this->isldapenabled()) {
            if ($this->isldapconfigured()) {
                $connection = $this->connect_ldap();

                if ($connection) {

                    // If some required config is missing display error message and end method.
                    if (in_array('ldap_field_map_matriculationnumber', $this->missingconfig) ||
                        in_array('ldap_field_map_username', $this->missingconfig)) {
                        $missingconfigstr = '';

                        if (in_array('ldap_field_map_matriculationnumber', $this->missingconfig)) {
                            $missingconfigstr .= 'ldap_field_map_matriculationnumber, ';
                        }

                        if (in_array('ldap_field_map_username', $this->missingconfig)) {
                            $missingconfigstr .= 'ldap_field_map_username';
                        }

                        notification::error(get_string('nomatrnravailable', 'mod_exammanagement') . ' ' .
                            get_string('ldapconfigmissing', 'mod_exammanagement') . $missingconfigstr , 'error');
                        return false;
                    }

                    $results = [];

                    // Build ldap query string with all user logins.
                    $filterstring = "";
                    $filterstringfirst = true;

                    if (isset($logins)) {
                        foreach ($logins as $login) {
                            if ($filterstringfirst) { // First participant.
                                $filterstring = "(".$this->ldapfieldusername."=".$login.")";
                            } else { // All other participants.
                                $filterstring = "(|".$filterstring."(".$this->ldapfieldusername."=".$login."))";
                            }
                            $filterstringfirst = false;
                        }

                        $dn = $this->dn;

                        $search = ldap_search( $connection, $dn, $filterstring,
                            [$this->ldapfieldusername, $this->ldapfieldmatriculationnumber]);

                        if ($search) {
                            // Get ldap attributes.
                            for ($entryid = ldap_first_entry($connection, $search); $entryid != false; $entryid =
                                ldap_next_entry($connection, $entryid)) {

                                $login = @ldap_get_values($connection, $entryid, $this->ldapfieldusername);
                                $matrnr = @ldap_get_values($connection, $entryid, $this->ldapfieldmatriculationnumber);

                                $results[$login[0]] = $matrnr[0];
                            }

                            ldap_free_result($search);

                            if (isset($results)) {
                                return $results;
                            } else {
                                return false;
                            }
                        } else {
                            return false;
                        }
                    }
                } else {
                    notification::error(get_string('nomatrnravailable', 'mod_exammanagement'). ' ' .
                        get_string('connectionfailed', 'mod_exammanagement'), 'error');
                    return false;
                }
            } else {
                notification::error(get_string('nomatrnravailable', 'mod_exammanagement'). ' ' .
                    get_string('ldapnotconfigured', 'mod_exammanagement'), 'error');
                return false;
            }
        } else {
            notification::error(get_string('nomatrnravailable', 'mod_exammanagement'). ' ' .
                get_string('ldapnotenabled', 'mod_exammanagement'), 'error');
            return false;
        }
    }


    /**
     * Get matriculation numbers for an array of user logins.
     *
     * @param array $matrnrs The matriculation numbers
     * @param array $attributes The attributes requested
     * @param array $externalidentifier Array with an external identifier
     * @return object
     */
    public function getldapattributesformatrnrs($matrnrs, $attributes, $externalidentifier = false) {

        if ($this->isldapenabled()) {
            if ($this->isldapconfigured()) {
                $connection = $this->connect_ldap();

                if ($connection) {

                    // If some required config is missing display error message and end method.
                    if ($attributes == 'usernames_and_matriculationnumbers') { // If only matrnr and username is needed.
                        if (in_array('ldap_field_map_matriculationnumber', $this->missingconfig) ||
                            in_array('ldap_field_map_username', $this->missingconfig)) {

                            $missingconfigstr = '';

                            if (in_array('ldap_field_map_matriculationnumber', $this->missingconfig)) {
                                $missingconfigstr .= 'ldap_field_map_matriculationnumber, ';
                            }

                            if (in_array('ldap_field_map_username', $this->missingconfig)) {
                                $missingconfigstr .= 'ldap_field_map_username';
                            }

                            notification::error(get_string('importmatrnrnotpossible', 'mod_exammanagement') . ' ' .
                                get_string('ldapconfigmissing', 'mod_exammanagement') . $missingconfigstr , 'error');
                            return false;
                        } else {
                            $attributes = [$this->ldapfieldusername, $this->ldapfieldmatriculationnumber];
                        }
                    } else if ($attributes == 'all_attributes') { // If matrnr, username, firstname, lastname and email is needed.
                        if (in_array('ldap_field_map_matriculationnumber', $this->missingconfig) ||
                            in_array('ldap_field_map_username', $this->missingconfig) ||
                            in_array('ldap_field_map_firstname', $this->missingconfig) ||
                            in_array('ldap_field_map_lastname', $this->missingconfig) ||
                            in_array('ldap_field_map_mail', $this->missingconfig)) {

                            $missingconfigstr = '';

                            if (in_array('ldap_field_map_matriculationnumber', $this->missingconfig)) {
                                $missingconfigstr .= 'ldap_field_map_matriculationnumber, ';
                            }

                            if (in_array('ldap_field_map_username', $this->missingconfig)) {
                                $missingconfigstr .= 'ldap_field_map_username';
                            }

                            if (in_array('ldap_field_map_firstname', $this->missingconfig)) {
                                $missingconfigstr .= 'ldap_field_map_firstname';
                            }

                            if (in_array('ldap_field_map_lastname', $this->missingconfig)) {
                                $missingconfigstr .= 'ldap_field_map_lastname';
                            }

                            if (in_array('ldap_field_map_mail', $this->missingconfig)) {
                                $missingconfigstr .= 'ldap_field_map_mail';
                            }

                            notification::error(get_string('importmatrnrnotpossible', 'mod_exammanagement') . ' ' .
                                get_string('ldapconfigmissing', 'mod_exammanagement') . $missingconfigstr , 'error');
                            return false;
                        } else {
                            $attributes = [
                                $this->ldapfieldusername,
                                $this->ldapfieldmatriculationnumber,
                                $this->ldapfieldlastname,
                                $this->ldapfieldfirstname,
                                $this->ldapfieldemailadress,
                            ];
                        }
                    }

                    $matrnrs = array_values($matrnrs);
                    if ($externalidentifier) {
                        $externalidentifier = array_values($externalidentifier);
                    }

                    $results = [];
                    $i = 0;

                    // Build ldap query string with all user matrnrs.
                    $filterstring = "";
                    $filterstringfirst = true;

                    if (isset($matrnrs)) {
                        foreach ($matrnrs as $matrnr) {
                            if ($filterstringfirst) { // First participant.
                                          $filterstring = "(".$this->ldapfieldmatriculationnumber."=".$matrnr.")";
                            } else { // All other participants.
                                $filterstring = "(|".$filterstring."(".$this->ldapfieldmatriculationnumber."=".$matrnr."))";
                            }
                            $filterstringfirst = false;
                        }

                        $dn = $this->dn;

                        $search = ldap_search( $connection, $dn, $filterstring, $attributes);

                        if ($search) {
                            // Get ldap attributes.
                            for ($entryid = ldap_first_entry($connection, $search); $entryid != false; $entryid =
                                ldap_next_entry($connection, $entryid)) {

                                foreach ($attributes as $attribute) {
                                    $value = ldap_get_values( $connection, $entryid, $attribute );

                                    switch ($attribute) {

                                        case $this->ldapfieldusername:
                                            $result['login'] = $value[0];
                                                  break;
                                        case $this->ldapfieldlastname:
                                            $result['lastname'] = $value[0];
                                            break;
                                        case $this->ldapfieldfirstname:
                                            $result['firstname'] = $value[0];
                                            break;
                                        case $this->ldapfieldemailadress:
                                            $result['email'] = $value[0];
                                            break;
                                    }
                                }

                                $matrnr = @ldap_get_values($connection, $entryid, $this->ldapfieldmatriculationnumber)[0];

                                if (!isset($externalidentifier) || !$externalidentifier) {
                                    $results[$matrnr] = $result;
                                } else {
                                    $result['matrnr'] = $matrnr;

                                    $results[$externalidentifier[array_search($matrnr, $matrnrs)]] = $result;
                                }

                                $i++;
                            }

                            // Results at the end of ldap method.
                            ldap_free_result($search);

                            if (isset($results)) {
                                return $results;
                            } else {
                                return false;
                            }
                        } else {
                            return false;
                        }

                    }
                } else {
                    notification::error(get_string('importmatrnrnotpossible', 'mod_exammanagement'). ' ' .
                        get_string('connectionfailed', 'mod_exammanagement'), 'error');
                    return false;
                }
            } else {
                notification::error(get_string('importmatrnrnotpossible', 'mod_exammanagement'). ' ' .
                    get_string('ldapnotconfigured', 'mod_exammanagement'), 'error');
                return false;
            }
        } else {
            notification::error(get_string('importmatrnrnotpossible', 'mod_exammanagement'). ' ' .
                get_string('ldapnotenabled', 'mod_exammanagement'), 'error');
            return false;
        }
    }
}
