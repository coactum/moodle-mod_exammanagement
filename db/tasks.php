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
 * Tasks for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @category    tasks
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
         'classname' => 'mod_exammanagement\task\delete_temp_participants',
         'blocking' => 0,
         'minute' => '50',
         'hour' => '23',
         'day' => '*',
         'month' => '*',
         'dayofweek' => '*',
    ],
    [
        'classname' => 'mod_exammanagement\task\check_participants_without_moodle_account',
        'blocking' => 0,
        'minute' => '52',
        'hour' => '23',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
    [
        'classname' => 'mod_exammanagement\task\delete_old_exam_data',
        'blocking' => 0,
        'minute' => '59',
        'hour' => '23',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
    [
        'classname' => 'mod_exammanagement\task\delete_unassigned_custom_rooms',
        'blocking' => 0,
        'minute' => '48',
        'hour' => '23',
        'day' => '1',
        'month' => '*',
        'dayofweek' => '*',
    ],
];
