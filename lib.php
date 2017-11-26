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
 * All the core Moodle functions, neeeded to allow the module to work 
 * integrated in Moodle should be placed here.
 * All the newmodule specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
*
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function exammanagement_supports($feature) {
    switch ($feature) {
    	case FEATURE_MOD_INTRO:
            return true;
    	case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
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
    global $DB;

    $moduleinstance->timecreated = time();

    $moduleinstance->id = $DB->insert_record('exammanagement', $moduleinstance);
    
    $moduleinstance->id = $DB->insert_record('exammanagement_data', $moduleinstance);

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
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

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

    $exists = $DB->get_record('exammanagement', array('id' => $id));
    if (!$exists) {
        return false;
    }

    $DB->delete_records('exammanagement', array('id' => $id));
    
    $exists = $DB->get_record('exammanagement_data', array('id' => $id));
    if (!$exists) {
        return false;
    }

    $DB->delete_records('exammanagement_data', array('id' => $id));

    return true;
}

/**
 * Is a given scale used by the instance of mod_exammanagement?
 *
 * This function returns if a scale is being used by one mod_exammanagement
 * if it has support for grading and scales.
 *
 * @param int $moduleinstanceid ID of an instance of this module.
 * @param int $scaleid ID of the scale.
 * @return bool True if the scale is used by the given mod_exammanagement instance.
 */
function exammanagement_scale_used($moduleinstanceid, $scaleid) {
    global $DB;

    if ($scaleid && $DB->record_exists('exammanagement', array('id' => $moduleinstanceid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of mod_exammanagement.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param int $scaleid ID of the scale.
 * @return bool True if the scale is used by any mod_exammanagement instance.
 */
function exammanagement_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('exammanagement', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the given mod_exammanagement instance.
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $moduleinstance Instance object with extra cmidnumber and modname property.
 * @param bool $reset Reset grades in the gradebook.
 * @return void.
 */
function exammanagement_grade_item_update($moduleinstance, $reset=false) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $item = array();
    $item['itemname'] = clean_param($moduleinstance->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;

    if ($moduleinstance->grade > 0) {
        $item['gradetype'] = GRADE_TYPE_VALUE;
        $item['grademax']  = $moduleinstance->grade;
        $item['grademin']  = 0;
    } else if ($moduleinstance->grade < 0) {
        $item['gradetype'] = GRADE_TYPE_SCALE;
        $item['scaleid']   = -$moduleinstance->grade;
    } else {
        $item['gradetype'] = GRADE_TYPE_NONE;
    }
    if ($reset) {
        $item['reset'] = true;
    }

    grade_update('/mod/exammanagement', $moduleinstance->course, 'mod', 'exammanagement', $moduleinstance->id, 0, null, $item);
}

/**
 * Delete grade item for given mod_exammanagement instance.
 *
 * @param stdClass $moduleinstance Instance object.
 * @return grade_item.
 */
function exammanagement_grade_item_delete($moduleinstance) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('/mod/exammanagement', $moduleinstance->course, 'mod', 'exammanagement',
                        $moduleinstance->id, 0, null, array('deleted' => 1));
}

/**
 * Update mod_exammanagement grades in the gradebook.
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $moduleinstance Instance object with extra cmidnumber and modname property.
 * @param int $userid Update grade of specific user only, 0 means all participants.
 */
function exammanagement_update_grades($moduleinstance, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    // Populate array of grade objects indexed by userid.
    $grades = array();
    grade_update('/mod/exammanagement', $moduleinstance->course, 'mod', 'exammanagement', $moduleinstance->id, 0, $grades);
}

/**
 * Returns the lists of all browsable file areas within the given module context.
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}.
 *
 * @package     mod_exammanagement
 * @category    files
 *
 * @param stdClass $course.
 * @param stdClass $cm.
 * @param stdClass $context.
 * @return string[].
 */
function exammanagement_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * File browsing support for mod_exammanagement file areas.
 *
 * @package     mod_exammanagement
 * @category    files
 *
 * @param file_browser $browser.
 * @param array $areas.
 * @param stdClass $course.
 * @param stdClass $cm.
 * @param stdClass $context.
 * @param string $filearea.
 * @param int $itemid.
 * @param string $filepath.
 * @param string $filename.
 * @return file_info Instance or null if not found.
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
function exammanagement_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options = array()) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);
    send_file_not_found();
}

/**
 * Extends the global navigation tree by adding mod_exammanagement nodes if there is a relevant content.
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $exammanagementnode An object representing the navigation tree node.
 * @param  stdClass $course Course object
 * @param  context_course $coursecontext Course context
*/

function exammanagement_extend_navigation_course($exammanagementnode, $course, $coursecontext) {
		$modinfo = get_fast_modinfo($course); // get mod_fast_modinfo from $course
		$index = 1;	//set index
		foreach ($modinfo->get_cms() as $cmid => $cm) { //search existing course modules for this course
			if ($cm->modname=="exammanagement" && $cm->uservisible && $cm->available) { //look if module (in this case exammanegement) exists, is uservisible and available
				$url = new moodle_url("/mod/" . $cm->modname . "/view.php", array("id" => $cmid)); //set url for the link in the navigation node
				$node = navigation_node::create($cm->name.' ('.get_string('modulename', 'exammanagement').')', $url, navigation_node::TYPE_CUSTOM);
				$exammanagementnode->add_node($node);
				}
			$index++;
		}
}

/**
 * Extends the settings navigation with the mod_exammanagement settings.
 *
 * This function is called when the context for the page is a mod_exammanagement module.
 * This is not called by AJAX so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $exammanagementnode {@link navigation_node}
 */
function exammanagement_extend_settings_navigation($settingsnav, $exammanagementnode = null) {
}
