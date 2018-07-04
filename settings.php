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
 * Plugin administration pages are defined here.
 *
 * @package     mod_exammanagement
 * @category    admin
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

      $settings->add(new admin_setting_heading('mod_exammanagement/pluginname', '', new lang_string('pluginadministration', 'mod_exammanagement')));

      // ...
      $settings->add(new admin_setting_heading('auth_ldap/ldapserversettings',
                new lang_string('auth_ldap_server_settings', 'auth_ldap'), ''));

      // ...
      $settings->add(new admin_setting_configtext('auth_ldap/host_url',
                get_string('auth_ldap_host_url_key', 'auth_ldap'),
                get_string('auth_ldap_host_url', 'auth_ldap'), '', PARAM_RAW_TRIMMED));

      // ...
      $settings->add(new admin_setting_button('auth_ldap/host_url',
                  get_string('auth_ldap_host_url_key', 'auth_ldap'),
                          get_string('auth_ldap_host_url', 'auth_ldap'), '', PARAM_RAW_TRIMMED));
}
