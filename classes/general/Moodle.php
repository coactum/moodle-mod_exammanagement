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
 * Renderer class for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

defined('MOODLE_INTERNAL') || die();

/**
 * class containing all db wrapper functions for moodle
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Moodle{

	#### singleton class ######

	public static function getInstance(){

		static $inst = null;
			if ($inst === null) {
				$inst = new Moodle();
			}
			return $inst;

	}

	#### wrapped general moodle functions #####

	public function setPage($substring, $id, $e){
		global $PAGE;

		$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);

		// Print the page header.
		$PAGE->set_url($ExammanagementInstanceObj->getExammanagementUrl($substring, $ExammanagementInstanceObj->getCm()->id));
		$PAGE->set_title(get_string('modulename','mod_exammanagement').': '.format_string($ExammanagementInstanceObj->getModuleinstance()->name));
		$PAGE->set_heading(format_string($ExammanagementInstanceObj->getCourse()->fullname));
		$PAGE->set_context($ExammanagementInstanceObj->getModulecontext());

		/*
		 * Other things you may want to set - remove if not needed.
		 * $PAGE->set_cacheable(false);
		 * $PAGE->set_focuscontrol('some-html-id');
		 * $PAGE->add_body_class('newmodule-'.$somevar);
		 */

	}

	public function outputPageHeader($id, $e){
		global $OUTPUT;

		$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);

		echo $OUTPUT->header();

		// set basic content (to be moved to renderer that has to define which usecas it is (e.g. overview, subpage, debug infos etc.)
		echo $OUTPUT->heading(get_string('maintitle', 'mod_exammanagement'));

		// Conditions to show the intro can change to look for own settings or whatever.
 		if ($ExammanagementInstanceObj->getModuleinstance()->intro) {
     		echo $OUTPUT->box(format_module_intro('exammanagement', $ExammanagementInstanceObj->getModuleinstance(), $ExammanagementInstanceObj->getCm()->id), 'generalbox mod_introbox', 'newmoduleintro');
 		}
 	}

 	public function outputFooter(){

 		global $OUTPUT;

		// Finish the page.
		echo $OUTPUT->footer();

 	}

	public function getMoodleUrl($url, $id = '', $param = '', $value = ''){

 		$url = new \moodle_url($url, array('id' => $id, $param => $value));

 		return $url;
 	}

 	public function redirectToOverviewPage($id, $e, $anchor, $message, $type){

		$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);

		$url = $ExammanagementInstanceObj->getExammanagementUrl('view', $ExammanagementInstanceObj->getCm()->id);

		if ($anchor){
				$url .= '#'.$anchor;
		}

		switch ($type) {
    		case 'success':
        		redirect ($url, $message, null, \core\output\notification::NOTIFY_SUCCESS);
        		break;
    		case 'warning':
        		redirect ($url, $message, null, \core\output\notification::NOTIFY_WARNING);
        		break;
    		case 'error':
        		redirect ($url, $message, null, \core\output\notification::NOTIFY_ERROR);
        		break;
        	case 'info':
        		redirect ($url, $message, null, \core\output\notification::NOTIFY_INFO);
        		break;
        	default:
        		redirect ($url, $message, null, \core\output\notification::NOTIFY_ERROR);
        		break;
		}
	}

	public function checkCapability($capname, $id, $e){
			$ExammanagementInstanceObj = exammanagementInstance::getInstance($id, $e);

			if (has_capability($capname, $ExammanagementInstanceObj->getModulecontext())){
					return true;
			} else {
					return false;
			}
	}

}
