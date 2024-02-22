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
 * The form for sending group messages to all participants in an exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

/**
 * The form for sending group messages to all participants in an exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_exammanagement_sendgroupmessage_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('textarea', 'groupmessages_subject', '<strong>' .
            get_string('subject', 'mod_exammanagement') . '</strong>', 'wrap="virtual" rows="1" cols="50"');
        $mform->setType('groupmessages_subject', PARAM_TEXT);
        $mform->addRule('groupmessages_subject', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');
        $mform->addElement('textarea', 'groupmessages_content', '<strong>' .
            get_string('content', 'mod_exammanagement') . '</strong>', 'wrap="virtual" rows="10" cols="50"');
        $mform->setType('groupmessages_content', PARAM_TEXT);
        $mform->addRule('groupmessages_content', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

        $this->add_action_buttons(true, get_string('send_message', 'mod_exammanagement'));
    }
}
