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
 * The form for importing bonus steps and points for participants for an exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_exammanagement\local\helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

/**
 * The form for importing bonus steps and points for participants for an exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_exammanagement_importbonus_form extends moodleform {

    /**
     * Define the form - called by parent constructor.
     */
    public function definition() {
        global $CFG, $PAGE, $OUTPUT;

        // Call jquery for tracking input value change events and creating input type number fields.
        $PAGE->requires->js_call_amd('mod_exammanagement/import_bonus', 'init');
        // Call jquery for adding tasks.
        $PAGE->requires->js_call_amd('mod_exammanagement/import_bonus', 'addbonusstep');
        // Call jquery for removing tasks.
        $PAGE->requires->js_call_amd('mod_exammanagement/import_bonus', 'removebonusstep');

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        if ($this->_customdata['bonuscount']) {
            $mform->addElement('html', '<div class="alert alert-warning alert-block fade in " role="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>' .
                get_string("bonus_already_entered", "mod_exammanagement",
                ['bonuscount' => $this->_customdata['bonuscount']]).'</div>');
        }

        $mform->addElement('html', '<h4>' . get_string('choose_bonus_import_mode', 'mod_exammanagement') . '</h4>');

        if (!isset($this->_customdata['mode'])) {
            $select = $mform->addElement('select', 'bonusmode', get_string('bonus_import_mode', 'mod_exammanagement'),
                ['steps' => get_string('mode_bonussteps', 'mod_exammanagement'),
                'points' => get_string('mode_bonuspoints', 'mod_exammanagement'),
            ]);
            $select->setSelected('steps');
        } else {
            $select = $mform->addElement('select', 'bonusmode', get_string('bonus_import_mode', 'mod_exammanagement'), [
                'points' => get_string('grades', 'mod_exammanagement'),
            ]);
            $select->setSelected('points');
        }

        $mform->addElement('html', '<hr>');

        $mform->addElement('html', '<p id="import_bonuspoints_text">' .
            get_string('import_bonuspoints_text', 'mod_exammanagement') . '</p>');

        $attributes = ['size' => '1']; // Length of input field.

        if (!isset($this->_customdata['mode'])) {
            // Set bonus grade steps.
            $mform->addElement('html', '<div id="set_bonussteps"><h4>' .
                get_string('set_bonussteps', 'mod_exammanagement') . '</h4>');

            // Group for add and remove bonusstep buttons.
            $bonusstepbuttons = [];
            array_push($bonusstepbuttons,
                $mform->createElement('button', 'add_bonusstep', '<i class="fa fa-plus" aria-hidden="true"></i>'));
            array_push($bonusstepbuttons,
                $mform->createElement('button', 'remove_bonusstep', '<i class="fa fa-minus" aria-hidden="true"></i>'));
            $mform->addGroup($bonusstepbuttons, 'bonusstep_buttonarray', get_string('add_remove_bonusstep', 'mod_exammanagement'),
                [' '], false);

            // Create list of bonussteps.

            $bonussstepnumbers = [];
            $bonussteps = [];
            $count = 1;
            $bonusstepcount = $this->_customdata['bonusstepcount'];

            // Add tasks from db.
            if ($bonusstepcount) {

                for ($count; $count <= $bonusstepcount; $count++) {

                    // Number of bonusstep.
                    array_push($bonussstepnumbers,
                        $mform->createElement('html', '<span class="exammanagement_task_spacing"><strong>' .
                        $count . '</strong></span>'));

                    // Input field with points needed for bonus grade step.
                    array_push($bonussteps, $mform->createElement('text', 'bonussteppoints[' . $count . ']', '', $attributes));
                    $mform->setType('bonussteppoints[' . $count . ']', PARAM_FLOAT);
                    $mform->setDefault('bonussteppoints[' . $count . ']', '');
                    $mform->hideif ('bonussteppoints[' . $count . ']', 'bonusmode', 'eq', 'points');

                }

                $mform->addElement('hidden', 'bonusstepcount', $bonusstepcount);
                $mform->setType('bonusstepcount', PARAM_FLOAT);

            } else {
                array_push($bonussstepnumbers,
                    $mform->createElement('html', '<span class="exammanagement_task_spacing"><strong>1</strong></span>'));

                array_push($bonussteps, $mform->createElement('text', 'bonussteppoints[1]', '', $attributes));
                $mform->setType('bonussteppoints[1]', PARAM_FLOAT);
                $mform->setDefault('bonussteppoints[1]', '');
                $mform->hideif ('bonussteppoints[1]', 'bonusmode', 'eq', 'points');

                $mform->addElement('hidden', 'bonusstepcount', 1);
                $mform->setType('bonusstepcount', PARAM_FLOAT);
            }

            $mform->addGroup($bonussstepnumbers, 'bonussstepnumbers_array',
                get_string('bonusstep', 'mod_exammanagement'), '', false);
            $mform->addGroup($bonussteps, 'bonussteppoints_array',
                get_string('required_points', 'mod_exammanagement'), ' ', false);

            $mform->addElement('html', '</div>');

            $mform->addElement('html', '<hr>');
        }

        // Add bonuspoints from file.
        $mform->addElement('html', '<h4>' . get_string('configure_fileimport', 'mod_exammanagement') . '</h4>');

        $select = $mform->addElement('select', 'importmode', get_string('import_mode', 'mod_exammanagement'), [
            'me' => get_string('moodle_export', 'mod_exammanagement', ['systemname' => helper::getmoodlesystemname()]),
            'i' => get_string('individual', 'mod_exammanagement'),
        ]);
        $select->setSelected('me');

        $mform->addElement('text', 'idfield', get_string('idfield', 'mod_exammanagement', [
            'systemname' => helper::getmoodlesystemname(),
        ]), $attributes);
        $mform->setType('idfield', PARAM_TEXT);
        $mform->setDefault('idfield', 'F');
        $mform->disabledif ('idfield', 'importmode', 'eq', 'me');

        $mform->addElement('text', 'pointsfield', get_string('pointsfield', 'mod_exammanagement'), $attributes);
        $mform->setType('pointsfield', PARAM_TEXT);
        $mform->addRule('pointsfield', get_string('err_filloutfield', 'mod_exammanagement'), 'required', 'client');

        $maxbytes = $CFG->maxbytes;

        $mform->addElement('filepicker', 'bonuspoints_list', get_string("import_bonus_from_file", "mod_exammanagement",
            ['systemname' => helper::getmoodlesystemname()]), null,
            ['maxbytes' => $maxbytes, 'accepted_types' => ['.xlsx', '.ods']]);
        $mform->addRule('bonuspoints_list', get_string('err_nofile', 'mod_exammanagement'), 'required', 'client');

        $this->add_action_buttons(true, get_string("read_file", "mod_exammanagement"));
        $mform->disable_form_change_checker();
    }

    /**
     * Custom validation for the form.
     *
     * @param object $data The data from the form.
     * @param object $files The files from the form.
     * @return object $errors The errors.
     */
    public function validation($data, $files) {
        $errors = [];

        if (isset($data['bonussteppoints'])) {
            foreach ($data['bonussteppoints'] as $key => $bonussteppoints) {

                $floatval = floatval($bonussteppoints);
                $isnumeric = is_numeric($bonussteppoints);

                if ($data['bonusmode'] === 'steps' && (($bonussteppoints && !$floatval) || !$isnumeric)) {
                    $errors['bonussteppoints[' . $key . ']'] = get_string('err_novalidinteger', 'mod_exammanagement');
                } else if ($data['bonusmode'] === 'steps' && $bonussteppoints < 0) {
                    $errors['bonussteppoints[' . $key . ']'] = get_string('err_underzero', 'mod_exammanagement');
                } else if (!preg_match('/^[A-Z]+$/', $data['idfield'])) {
                    $errors['idfield'] = get_string('err_noalphanumeric', 'mod_exammanagement');
                } else if (!preg_match('/^[A-Z]+$/', $data['pointsfield'])) {
                    $errors['pointsfield'] = get_string('err_noalphanumeric', 'mod_exammanagement');
                }
            }
        }

        return $errors;
    }
}
