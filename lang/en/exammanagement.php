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
 * Plugin strings are defined here.
 *
 * @package     mod_exammanagement
 * @category    string
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//exammanagement_overview.mustache
$string['maintitle'] = 'Exam management';
$string['overview'] = 'Overview';

//dateTimeForm.php

$string['resetDateTime'] = 'Reset appointment';

//helptexts
$string['helptext_str'] = 'Help text';
$string['helptext_link'] = 'A detailed explanation of the mentioned elements and functions can be found at the "IMT HilfeWiki" under the following link:';
$string['helptext_overview']= 'Here goes the description of this feature site.';
$string['helptext_addRooms']= 'Here goes the description of this feature site.';
$string['helptext_setDateTime']= 'Here goes the description of this feature site.';
$string['helptext_addParticipants']= 'Here goes the description of this feature site.';
$string['helptext_setTextfield']= 'Here goes the description of this feature site.';
$string['helptext_sendGroupmessages']= 'Here goes the description of this feature site.';

//errors and permissions
$string['missingidandcmid'] = 'Course id missing (missingcmid)';
$string['nopermissions'] = 'You have no permissions to do this. Action denied.';

//universal
$string['modulename'] = 'Exam management';
$string['modulenameplural'] = 'Exam managements';
$string['pluginname'] = 'Exam management';

//addnewinstance
$string['modulename_help'] = 'The PANDA exammanagement allows you easy organizing the exams for your course and makes it possible to manage even large exams with many participants.';
$string['exammanagementname'] = 'Exam Management';
$string['exammanagement:enable exam management'] = 'enable exam management';
$string['messageprovider:exam management messages'] = 'exam management messages';
$string['pluginadministration'] = 'exam management administration';

//capabilities
$string['exammanagement:addinstance'] = 'Add new exam organization';
$string['exammanagement:viewinstance'] = 'View exam organization';
$string['exammanagement:viewparticipantspage'] = 'View participants page';
$string['exammanagement:takeexams'] = 'Take exams';
$string['exammanagement:sendgroupmessage'] = 'Send group message to participants';
$string['exammanagement:addDefaultRooms'] = 'Import default rooms';
