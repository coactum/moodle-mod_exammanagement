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
 * Plugin capabilities are defined here.
 *
 * The capabilities are loaded into the database table when the module is
 * installed or updated. Whenever the capability definitions are updated,
 * the module version number should be bumped up.
 *
 * The system has four possible values for a capability:
 * CAP_ALLOW, CAP_PREVENT, CAP_PROHIBIT, and inherit (not set).
 *
 * It is important that capability names are unique. The naming convention
 * for capabilities that are specific to modules and blocks is as follows:
 *   [mod/block]/<plugin_name>:<capabilityname>
 *
 * component_name should be the same as the directory name of the mod or block.
 *
 * Core moodle capabilities are defined thus:
 *    moodle/<capabilityclass>:<capabilityname>
 *
 * Examples: mod/forum:viewpost
 *           block/recent_activity:view
 *           moodle/site:deleteuser
 *
 * The variable name for the capability definitions array is $capabilities
 *
 * @package     mod_exammanagement
 * @category    access
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = [

	'mod/exammanagement:addinstance' => [
        'riskbitmask' => RISK_XSS, RISK_SPAM,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [
            'manager' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'student' => CAP_PROHIBIT,
            'guest' => CAP_PROHIBIT,
        ],
    ],

    'mod/exammanagement:viewinstance' => [
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'manager' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'student' => CAP_PROHIBIT,
            'guest' => CAP_PROHIBIT,
        ],
    ],

		'mod/exammanagement:viewparticipantspage' => [
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'manager' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'student' => CAP_ALLOW,
            'guest' => CAP_PROHIBIT,
        ],
    ],

    'mod/exammanagement:takeexams'=> [
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'manager' => CAP_PROHIBIT,
            'coursecreator' => CAP_PROHIBIT,
            'teacher' => CAP_PROHIBIT,
            'editingteacher' => CAP_PROHIBIT,
            'student' => CAP_ALLOW,
            'guest' => CAP_PROHIBIT,
        ],
    ],
    'mod/exammanagement:sendgroupmessage'=> [
        'riskbitmask' => RISK_SPAM,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'manager' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'student' => CAP_PROHIBIT,
            'guest' => CAP_PROHIBIT,
        ],
    ],
		'mod/exammanagement:adddefaultrooms'=> [
        'riskbitmask' => RISK_XSS, RISK_SPAM,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'manager' => CAP_ALLOW,
            'coursecreator' => CAP_PROHIBIT,
            'teacher' => CAP_PROHIBIT,
            'editingteacher' => CAP_PROHIBIT,
            'student' => CAP_PROHIBIT,
            'guest' => CAP_PROHIBIT,
        ],
    ],
        'mod/exammanagement:resetpassword'=> [
        'riskbitmask' => RISK_PERSONAL, RISK_SPAM,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'manager' => CAP_ALLOW,
            'coursecreator' => CAP_PROHIBIT,
            'teacher' => CAP_PROHIBIT,
            'editingteacher' => CAP_PROHIBIT,
            'student' => CAP_PROHIBIT,
            'guest' => CAP_PROHIBIT,
        ],
    ],
];
