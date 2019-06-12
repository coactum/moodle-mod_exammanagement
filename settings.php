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
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

      $settings->add(new admin_setting_heading('mod_exammanagement/pluginname', '', new lang_string('pluginadministration', 'mod_exammanagement')));

      $settings->add(new admin_setting_configtext('mod_exammanagement/moodleidsupportuser', get_string('moodleid_supportuser', 'mod_exammanagement'),
                       get_string('moodleid_supportuser_help', 'mod_exammanagement'), '', PARAM_INT));
}