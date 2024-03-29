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
 * Library of interface functions and constants.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('EXAMMANAGEMENT_EVENT_TYPE_EXAMTIME', 'examtime');
define('EXAMMANAGEMENT_EVENT_TYPE_EXAMREVIEWTIME', 'examreviewtime');

/**
 * Indicates API features that the plugin supports.
 *
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_SHOW_DESCRIPTION
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE__BACKUP_MOODLE2
 * @uses FEATURE_MOD_PURPOSE
 * @param string $feature Constant representing the feature.
 * @return mixed True if the feature is supported, null otherwise.
 */
function exammanagement_supports($feature) {
    // Adding support for FEATURE_MOD_PURPOSE (MDL-71457) and providing backward compatibility (pre-v4.0).
    if (defined('FEATURE_MOD_PURPOSE') && $feature === FEATURE_MOD_PURPOSE) {
        return MOD_PURPOSE_ASSESSMENT;
    }

    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;

        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_exammanagement into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $moduleinstance An object from the form.
 * @param mod_exammanagement_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function exammanagement_add_instance($moduleinstance, $mform = null) {
    global $DB, $PAGE;

    $moduleinstance->timecreated = time();
    $moduleinstance->categoryid = $PAGE->category->id; // Set course category.

    if (isset($mform->get_data()->newpassword) && $mform->get_data()->newpassword !== '') {
        $moduleinstance->password = base64_encode(password_hash($mform->get_data()->newpassword, PASSWORD_DEFAULT));
    } else {
        $moduleinstance->password = null;
    }

    // Check if mode export_grades.
    $misc = new stdclass;
    if ($mform->get_data()->exportgrades) {
        $misc->mode = 'export_grades';
    }

    // Set phase and steps deselections.
    if ($mform->get_data()->deselectphaseexamreview) {
        $misc->configoptions = ['noexamreview'];
    }

    if (isset($misc->mode) || isset($misc->configoptions)) {
        $moduleinstance->misc = json_encode($misc);
    } else {
        $moduleinstance->misc = null;
    }

    $moduleinstance->id = $DB->insert_record('exammanagement', $moduleinstance);

    // Unset opening state of the exam phases saved in the user preferences.
    unset_user_preference('exammanagement_phase_one');
    unset_user_preference('exammanagement_phase_two');
    unset_user_preference('exammanagement_phase_exam');
    unset_user_preference('exammanagement_phase_three');
    unset_user_preference('exammanagement_phase_four');
    unset_user_preference('exammanagement_phase_five');

    return $moduleinstance->id;
}

/**
 * Updates an instance of the mod_exammanagement in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $moduleinstance An object from the form in mod_form.php.
 * @param mod_exammanagement_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function exammanagement_update_instance($moduleinstance, $mform = null) {
    global $DB, $PAGE;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;
    $moduleinstance->categoryid = $PAGE->category->id; // Set course category.

    if (isset($mform->get_data()->newpassword) && $mform->get_data()->newpassword !== '') {

        $existingpw = base64_decode($DB->get_record('exammanagement', ['id' => $moduleinstance->instance])->password);

        if (!isset($existingpw) || $existingpw == '' ||
            (isset($existingpw) && isset($mform->get_data()->oldpassword) &&
            password_verify($mform->get_data()->oldpassword, $existingpw))) {

                $moduleinstance->password = base64_encode(password_hash($mform->get_data()->newpassword, PASSWORD_DEFAULT));

        } else {
            throw new Exception(get_string('incorrect_password_change', 'mod_exammanagement'));
        }
    }

    // Check if mode export_grades.
    $misc = new stdclass;
    if ($mform->get_data()->exportgrades) {
        $misc->mode = 'export_grades';
    }

    // Set phase and steps deselections.
    if ($mform->get_data()->deselectphaseexamreview) {
        $misc->configoptions = ['noexamreview'];
    }

    if (isset($misc->mode) || isset($misc->configoptions)) {
        $moduleinstance->misc = json_encode($misc);
    } else {
        $moduleinstance->misc = null;
    }

    return $DB->update_record('exammanagement', $moduleinstance);
}

/**
 * Removes an instance of the mod_exammanagement from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function exammanagement_delete_instance($id) {
    global $DB;

    // Delete participants.
    if ($DB->record_exists('exammanagement_participants', ['exammanagement' => $id])) {
        $DB->delete_records('exammanagement_participants', ['exammanagement' => $id]);
    }

    // Delete temporary participants.
    if ($DB->record_exists('exammanagement_temp_part', ['exammanagement' => $id])) {
        $DB->delete_records('exammanagement_temp_part', ['exammanagement' => $id]);
    }

    // Delete plugin instance.
    if ($DB->record_exists('exammanagement', ['id' => $id])) {
        $DB->delete_records('exammanagement', ['id' => $id]);
        return true;
    } else {
        return false;
    }
}

/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the exammanagement.
 *
 * @param object $mform Form passed by reference.
 */
function exammanagement_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'exammanagementheader', get_string('modulenameplural', 'exammanagement'));
    $mform->addElement('checkbox', 'reset_exammanagement_data', get_string('deleteallexamdata', 'exammanagement'));
    $mform->disabledif ('reset_exammanagement_data', 'reset_exammanagement_participantsdata', 'checked');

    $mform->addElement('checkbox', 'reset_exammanagement_participantsdata',
        get_string('deleteexamparticipantsdata', 'exammanagement'));
    $mform->disabledif ('reset_exammanagement_participantsdata', 'reset_exammanagement_data', 'checked');

}

/**
 * Course reset form defaults.
 *
 * @param object $course
 * @return array
 */
function exammanagement_reset_course_form_defaults($course) {
    return ['reset_exammanagement_data' => 1, 'reset_exammanagement_participantsdata' => 0];
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * This function will remove all userdata from the specified exammanagement.
 *
 * @param object $data The data submitted from the reset course.
 * @return array $status Status array.
 */
function exammanagement_reset_userdata($data) {
    global $CFG, $DB;

    require_once($CFG->libdir . '/filelib.php');

    $modulename = get_string('modulenameplural', 'exammanagement');
    $status = [];

    // Get exammanagements in course that should be resetted.
    $sql = "SELECT e.id
                FROM {exammanagement} e
                WHERE e.course = ?";

    $params = [$data->courseid];

    $exammanagements = $DB->get_records_sql($sql, $params);

    // Delete all exammanagement data.
    if (!empty($data->reset_exammanagement_data)) {

        $DB->set_field_select('exammanagement', 'password', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'rooms', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'examtime', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'importfileheaders', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'tempimportfileheader', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'tasks', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'textfield', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'assignmentmode', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'datetimevisible', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'roomvisible', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'placevisible', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'bonusvisible', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'resultvisible', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'gradingscale', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'datadeletion', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'deletionwarningmailids', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'examreviewtime', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'examreviewroom', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'examreviewvisible', null, 'course = ?', $params);
        $DB->set_field_select('exammanagement', 'datadeleted', null, 'course = ?', $params);

        $status[] = [
            'component' => $modulename,
            'item' => get_string('allexamdatadeleted', 'exammanagement'),
            'error' => false,
        ];
    }

    // Delete exam participants data.
    if (!empty($data->reset_exammanagement_data) || !empty($data->reset_exammanagement_participantsdata) ) {
        foreach ($exammanagements as $eid => $unused) {
            if (!$cm = get_coursemodule_from_instance('exammanagement', $eid)) {
                continue;
            }

            $DB->delete_records('exammanagement_participants', ['exammanagement' => $eid]);
        }

        $status[] = ['component' => $modulename,
            'item' => get_string('examparticipantsdatadeleted', 'exammanagement'), 'error' => false];
    }

    // Updating dates - shift may be negative too.
    if ($data->timeshift) {
        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.
        shift_course_mod_dates('exammanagement', [], $data->timeshift, $data->courseid);
        $status[] = ['component' => $modulename, 'item' => get_string('datechanged'), 'error' => false];
    }

    return $status;
}

/**
 * Returns the lists of all browsable file areas within the given module context.
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@see file_browser::get_file_info_context_module()}.
 *
 * @package     mod_exammanagement
 * @category    files
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return string[]
 */
function exammanagement_get_file_areas($course, $cm, $context) {
    return [];
}

/**
 * File browsing support for mod_exammanagement file areas.
 *
 * @package     mod_exammanagement
 * @category    files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info Instance or null if not found
 */
function exammanagement_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the mod_exammanagement file areas.
 *
 * @package     mod_exammanagement
 * @category    files
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The course module object.
 * @param stdClass $context The mod_exammanagement's context.
 * @param string $filearea The name of the file area.
 * @param array $args Extra arguments (itemid, path).
 * @param bool $forcedownload Whether or not force download.
 * @param array $options Additional options affecting the file serving.
 */
function exammanagement_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options = []) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);
    send_file_not_found();
}

/**
 * Update the calendar entries for this exammanagement activity.
 *
 * @param stdClass $exammanagement the row from the database table exammanagement.
 * @param int $cmid The coursemodule id
 * @return bool
 */
function exammanagement_update_calendar(stdClass $exammanagement, $cmid) {
    global $DB, $CFG;

    require_once($CFG->dirroot.'/calendar/lib.php');

    // Get CMID if not sent as part of $exammanagement.
    if (! isset($exammanagement->coursemodule)) {
        $cm = get_coursemodule_from_instance('exammanagement', $exammanagement->id, $exammanagement->course);
        $exammanagement->coursemodule = $cm->id;
    }

    // Exam time calendar events.
    $event = new stdClass();
    $event->eventtype = EXAMMANAGEMENT_EVENT_TYPE_EXAMTIME;
    $event->type = CALENDAR_EVENT_TYPE_STANDARD;

    if ($event->id = $DB->get_field('event', 'id', [
        'modulename' => 'exammanagement',
        'instance' => $exammanagement->id,
        'eventtype' => $event->eventtype,
    ])) {

        if ((! empty($exammanagement->examtime)) && ($exammanagement->examtime > 0)) {
            // Calendar event exists so update it.
            $event->name = get_string('examtime_calendarevent', 'exammanagement', $exammanagement->name);
            $event->description = format_module_intro('exammanagement', $exammanagement, $cmid);
            $event->timestart = $exammanagement->examtime;
            $event->timesort = $exammanagement->examtime;
            $event->visible = instance_is_visible('exammanagement', $exammanagement);
            $event->timeduration = 0;

            $calendarevent = calendar_event::load($event->id);
            $calendarevent->update($event, false);
        } else {
            // Calendar event is no longer needed.
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->delete();
        }
    } else {
        // Event doesn't exist so create one.
        if ((! empty($exammanagement->examtime)) && ($exammanagement->examtime > 0)) {
            $event->name = get_string('examtime_calendarevent', 'exammanagement', $exammanagement->name);
            $event->description = format_module_intro('exammanagement', $exammanagement, $cmid);
            $event->courseid = $exammanagement->course;
            $event->groupid = 0;
            $event->userid = 0;
            $event->modulename = 'exammanagement';
            $event->instance = $exammanagement->id;
            $event->timestart = $exammanagement->examtime;
            $event->timesort = $exammanagement->examtime;
            $event->visible = instance_is_visible('exammanagement', $exammanagement);
            $event->timeduration = 0;

            calendar_event::create($event, false);
        }
    }

    // Exam review time calendar events.
    $event = new stdClass();
    $event->type = CALENDAR_EVENT_TYPE_STANDARD;
    $event->eventtype = EXAMMANAGEMENT_EVENT_TYPE_EXAMREVIEWTIME;
    if ($event->id = $DB->get_field('event', 'id', [
        'modulename' => 'exammanagement',
        'instance' => $exammanagement->id,
        'eventtype' => $event->eventtype,
    ])) {
        if ((! empty($exammanagement->examreviewtime)) && ($exammanagement->examreviewtime > 0)) {
            // Calendar event exists so update it.
            $event->name = get_string('examreviewtime_calendarevent', 'exammanagement', $exammanagement->name);
            $event->description = format_module_intro('exammanagement', $exammanagement, $cmid);
            $event->timestart = $exammanagement->examreviewtime;
            $event->timesort = $exammanagement->examreviewtime;
            $event->visible = instance_is_visible('exammanagement', $exammanagement);
            $event->timeduration = 0;

            $calendarevent = calendar_event::load($event->id);
            $calendarevent->update($event, false);
        } else {
            // Calendar event is on longer needed.
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->delete();
        }
    } else {
        // Event doesn't exist so create one.
        if ((! empty($exammanagement->examreviewtime)) && ($exammanagement->examreviewtime > 0)) {
            $event->name = get_string('examreviewtime_calendarevent', 'exammanagement', $exammanagement->name);
            $event->description = format_module_intro('exammanagement', $exammanagement, $cmid);
            $event->courseid = $exammanagement->course;
            $event->groupid = 0;
            $event->userid = 0;
            $event->modulename = 'exammanagement';
            $event->instance = $exammanagement->id;
            $event->timestart = $exammanagement->examreviewtime;
            $event->timesort = $exammanagement->examreviewtime;
            $event->visible = instance_is_visible('exammanagement', $exammanagement);
            $event->timeduration = 0;

            calendar_event::create($event, false);
        }
    }
    return true;
}

/**
 * Map icons for font-awesome themes.
 */
function exammanagement_get_fontawesome_icon_map() {
    return [
        'mod_exammanagement:barcode' => 'fa-barcode',
    ];
}
