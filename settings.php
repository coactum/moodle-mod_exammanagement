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
 * Plugin administration page settings are defined here.
 *
 * @package     mod_exammanagement
 * @category    admin
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

      $settings->add(new admin_setting_heading('mod_exammanagement/pluginname', '',
            new lang_string('pluginadministration', 'mod_exammanagement')));

      // Enter name of moodle system to be displayed in the plugin (e.g. in help texts).
      $settings->add(new admin_setting_configtext('mod_exammanagement/moodlesystemname',
            get_string('moodlesystemname', 'mod_exammanagement'),
            get_string('moodlesystemname_help', 'mod_exammanagement'), 'Moodle', PARAM_TEXT));

      // Enable and set additional global admin message.
      $settings->add(new admin_setting_configcheckbox('mod_exammanagement/enableglobalmessage',
            get_string('enableglobalmessage', 'mod_exammanagement'),
            get_string('enableglobalmessage_help', 'mod_exammanagement'), 0));

      $settings->add(new admin_setting_configtext('mod_exammanagement/globalmessage',
            get_string('globalmessage', 'mod_exammanagement'),
            get_string('globalmessage_help', 'mod_exammanagement'), '', PARAM_TEXT));

      // Enable additional help texts in plugin and configure link to additional ressources.
      $settings->add(new admin_setting_configcheckbox('mod_exammanagement/enablehelptexts',
            get_string('enablehelptexts', 'mod_exammanagement'),
            get_string('enablehelptexts_help', 'mod_exammanagement'), 0));

      $settings->add(new admin_setting_configtext('mod_exammanagement/additionalressources',
            get_string('additionalressources', 'mod_exammanagement'),
            get_string('additionalressources_help', 'mod_exammanagement'), 'https://docs.moodle.org/en/mod/exammanagement',
            PARAM_TEXT));

      // Acitvate possibility to request password reset by moodle admins.
      $settings->add(new admin_setting_configcheckbox('mod_exammanagement/enablepasswordresetrequest',
            get_string('enablepasswordresetrequest', 'mod_exammanagement'),
            get_string('enablepasswordresetrequest_help', 'mod_exammanagement'), 0));

      // Configure ldap for fetching matriculation numbers.
      $settings->add(new admin_setting_configcheckbox('mod_exammanagement/enableldap',
            get_string('enableldap', 'mod_exammanagement'),
            get_string('enableldap_help', 'mod_exammanagement'), 0));

      $settings->add(new admin_setting_configtext('mod_exammanagement/ldapdn',
            get_string('ldapdn', 'mod_exammanagement'),
            get_string('ldapdn_help', 'mod_exammanagement'), '', PARAM_TEXT));

      $settings->add(new admin_setting_configtext('mod_exammanagement/ldap_objectclass_student',
            get_string('ldap_objectclass_student', 'mod_exammanagement'),
            get_string('ldap_objectclass_student_help', 'mod_exammanagement'), '', PARAM_TEXT));

      $settings->add(new admin_setting_configtext('mod_exammanagement/ldap_field_map_username',
            get_string('ldap_field_map_username', 'mod_exammanagement'),
            get_string('ldap_field_map_username_help', 'mod_exammanagement'), '', PARAM_TEXT));

      $settings->add(new admin_setting_configtext('mod_exammanagement/ldap_field_map_matriculationnumber',
            get_string('ldap_field_map_matriculationnumber', 'mod_exammanagement'),
            get_string('ldap_field_map_matriculationnumber_help', 'mod_exammanagement'), '', PARAM_TEXT));

      $settings->add(new admin_setting_configtext('mod_exammanagement/ldap_field_map_firstname',
            get_string('ldap_field_map_firstname', 'mod_exammanagement'),
            get_string('ldap_field_map_firstname_help', 'mod_exammanagement'), '', PARAM_TEXT));

      $settings->add(new admin_setting_configtext('mod_exammanagement/ldap_field_map_lastname',
            get_string('ldap_field_map_lastname', 'mod_exammanagement'),
      get_string('ldap_field_map_lastname_help', 'mod_exammanagement'), '', PARAM_TEXT));

      $settings->add(new admin_setting_configtext('mod_exammanagement/ldap_field_map_mail',
            get_string('ldap_field_map_mail', 'mod_exammanagement'),
            get_string('ldap_field_map_mail_help', 'mod_exammanagement'), '', PARAM_TEXT));
}
