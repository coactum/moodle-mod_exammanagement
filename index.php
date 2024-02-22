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
 * Display information about all the exammanagements in the requested course.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$id = required_param('id', PARAM_INT); // ID of the course.

if ($id) {
    if (!$course = $DB->get_record('course', ['id' => $id])) {
        throw new moodle_exception('invalidcourseid');
    }
} else {
    $course = get_site();
}

require_course_login($course);

$coursecontext = context_course::instance($course->id);

// Trigger course_module_instance_list_viewed event.
$event = \mod_exammanagement\event\course_module_instance_list_viewed::create([
    'context' => $coursecontext,
]);
$event->add_record_snapshot('course', $course);
$event->trigger();

// Set page navigation.
$modulenameplural = get_string('modulenameplural', 'mod_exammanagement');

$PAGE->set_pagelayout('incourse');

$PAGE->set_url('/mod/exammanagement/index.php', ['id' => $id]);

$PAGE->set_context($coursecontext);

$PAGE->navbar->add($modulenameplural);

$PAGE->set_title($course->shortname . ': ' . $modulenameplural);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($modulenameplural);

// Build table with all instances.
$modinfo = get_fast_modinfo($course);
$exammanagements = get_all_instances_in_course('exammanagement', $course);

// Sections.
$usesections = course_format_uses_sections($course->format);
if ($usesections) {
    $sections = $modinfo->get_section_info_all();
}

if (empty($exammanagements)) {
    notice(get_string('nonewmodules', 'mod_exammanagement'), new moodle_url('/course/view.php', ['id' => $course->id]));
}

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';
$table->head = [];
$table->align = [];
if ($usesections) {
    // Add column heading based on the course format. e.g. Week, Topic.
    $table->head[] = get_string('sectionname', 'format_' . $course->format);
    $table->align[] = 'left';
}
// Add activity, Name, and activity, Description, headings.
$table->head[] = get_string('name');
$table->align[] = 'left';
$table->head[] = get_string('description');
$table->align[] = 'left';

$currentsection = '';
$i = 0;

foreach ($exammanagements as $exammanagement) {

    $context = context_module::instance($exammanagement->coursemodule);

    // Section.
    $printsection = '';
    if ($exammanagement->section !== $currentsection) {
        if ($exammanagement->section) {
            $printsection = get_section_name($course, $sections[$exammanagement->section]);
        }
        if ($currentsection !== '') {
            $table->data[$i] = 'hr';
            $i ++;
        }
        $currentsection = $exammanagement->section;
    }
    if ($usesections) {
        $table->data[$i][] = $printsection;
    }

    // Link.
    $exammanagementname = format_string($exammanagement->name, true, [
        'context' => $context,
    ]);
    if (! $exammanagement->visible) {
        // Show dimmed if the mod is hidden.
        $table->data[$i][] = "<a class=\"dimmed\" href=\"view.php?id=$exammanagement->coursemodule\">" .
            $exammanagementname . "</a>";
    } else {
        // Show normal if the mod is visible.
        $table->data[$i][] = "<a href=\"view.php?id=$exammanagement->coursemodule\">" . $exammanagementname . "</a>";
    }

    // Description.
    $table->data[$i][] = format_module_intro('exammanagement', $exammanagement, $exammanagement->coursemodule);

    $i ++;
}

echo html_writer::table($table);

echo $OUTPUT->footer();
