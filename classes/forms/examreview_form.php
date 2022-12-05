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
 * The form for setting the date and room for the exam review for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use moodleform;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

/**
 * The form for setting the date and room for the exam review for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class examreview_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {

        $mform = $this->_form;

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<h3>'.get_string("examreview", "mod_exammanagement"));

        if ($helptextsenabled) {
            global $OUTPUT;

            $mform->addElement('html', $OUTPUT->help_icon('examreview', 'mod_exammanagement', ''));
        }

        $mform->addElement('html', '</h3>');

        $mform->addElement('html', '<p>'.get_string('examreviewstr', 'mod_exammanagement').'</p>');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('date_time_selector', 'examreviewtime', get_string('examreviewdate', 'mod_exammanagement'));

        $attributes = array('size' => '20');

        $mform->addElement('text', 'examreviewroom', get_string('examreviewroom', 'mod_exammanagement'), $attributes);
        $mform->setType('examreviewroom', PARAM_TEXT);
        $mform->addRule('examreviewroom', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

        $this->add_action_buttons();

        $mform->disable_form_change_checker();

    }
}
