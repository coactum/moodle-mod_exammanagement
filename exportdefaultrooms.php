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
 * Outputs the default exam rooms as a text file.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_exammanagement\local\helper;

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// Course_module ID, or ...
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module.
$e = optional_param('e', 0, PARAM_INT);

// Set the basic variables $course, $cm and $moduleinstance.
if ($id) {
    [$course, $cm] = get_course_and_cm_from_cmid($id, 'exammanagement');
    $moduleinstance = $DB->get_record('exammanagement', ['id' => $cm->instance], '*', MUST_EXIST);
} else {
    throw new moodle_exception('missingparameter');
}

// Check if course module, course and course section exist.
if (!$cm) {
    throw new moodle_exception(get_string('incorrectmodule', 'exammanagement'));
} else if (!$course) {
    throw new moodle_exception(get_string('incorrectcourseid', 'exammanagement'));
} else if (!$coursesections = $DB->get_record("course_sections", ["id" => $cm->section])) {
    throw new moodle_exception(get_string('incorrectmodule', 'exammanagement'));
}

// Check login and capability.
require_login($course, true, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/exammanagement:importdefaultrooms', $context);

// If user has not entered the correct password: redirect to check password page.
if (isset($moduleinstance->password) &&
    (!isset($SESSION->loggedInExamOrganizationId) || $SESSION->loggedInExamOrganizationId !== $id)) {

    redirect(new moodle_url('/mod/exammanagement/checkpassword.php', ['id' => $id]), null, null, null);
}

define("SEPARATOR", chr(42)); // Comma.
define("NEWLINE", "\r\n");

$alldefaultrooms = helper::getrooms($moduleinstance, 'defaultrooms');

if (!isset($alldefaultrooms) || $alldefaultrooms == false) {
    redirect(new moodle_url('/mod/exammanagement/chooserooms.php', ['id' => $id]),
        get_string('no_default_rooms', 'mod_exammanagement'), null, 'error');
}

global $CFG;

$textfile = '';

foreach ($alldefaultrooms as $room) {
    $textfile .= $room->roomid . SEPARATOR . $room->name . SEPARATOR . $room->description . SEPARATOR .
        json_encode($room->places) . SEPARATOR;

    if (isset($room->seatingplan) && $room->seatingplan !== '') {
        $textfile .= str_replace(["\r\n" , "\r" , "\n"], '', base64_decode($room->seatingplan));
    }

    if ($room !== end($alldefaultrooms)) {
        $textfile .= NEWLINE;
    }
}

// Generate filename without umlaute.
$umlaute = ["/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/"];
$replace = ["ae", "oe", "ue", "Ae", "Oe", "Ue", "ss"];
$filenameumlaute = get_string("default_exam_rooms", "mod_exammanagement") . '_' . date('d_m_y') . '.txt';
$filename = preg_replace($umlaute, $replace, $filenameumlaute);

// Return content as text file.
header("Content-Type: application/force-download; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
header("Content-Length: ". strlen($textfile));
echo $textfile;
