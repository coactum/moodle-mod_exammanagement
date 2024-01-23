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
 * The form for importing default rooms for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\Moodle;

use moodleform;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/Moodle.php');

/**
 * The form for importing default rooms for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class importdefaultrooms_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {

        global $OUTPUT;

        $exammanagementinstanceobj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);

        $mform = $this->_form;

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<h3>'.get_string("importDefaultRooms", "mod_exammanagement"));

        if ($helptextsenabled) {
            $mform->addElement('html', $OUTPUT->help_icon('importDefaultRooms', 'mod_exammanagement', ''));
        }

        $mform->addElement('html', '</h3>');

        $mform->addElement('html', '<p>'.get_string("import_default_rooms_str", "mod_exammanagement").'</p>');

        if ($exammanagementinstanceobj->countDefaultRooms()) {
            $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert"><button type="button" class="close" data-dismiss="alert">×</button>'.get_string("default_rooms_already_exists", "mod_exammanagement").'</div>');
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('filepicker', 'defaultrooms_list', get_string("default_rooms_file_structure", "mod_exammanagement"), null, array('accepted_types' => '.txt'));
        $mform->addRule('defaultrooms_list', get_string('err_nofile', 'mod_exammanagement'), 'required', 'client');

        $this->add_action_buttons(true, get_string("read_file", "mod_exammanagement"));

        $mform->disable_form_change_checker();

    }
}
