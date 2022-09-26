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
 * The form for choosing exam rooms for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\forms;
use mod_exammanagement\general\exammanagementInstance;
use mod_exammanagement\general\User;
use mod_exammanagement\general\Moodle;
use mod_exammanagement\general\MoodleDB;
use mod_exammanagement\output\exammanagement_pagebar;

use moodleform;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

require_once(__DIR__.'/../general/exammanagementInstance.php');
require_once(__DIR__.'/../general/User.php');
require_once(__DIR__.'/../general/Moodle.php');
require_once(__DIR__.'/../general/MoodleDB.php');
require_once(__DIR__.'/../output/exammanagement_pagebar.php');

/**
 * The form for choosing exam rooms for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chooserooms_form extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {

        global $PAGE, $OUTPUT;

        $exammanagementinstanceobj = exammanagementInstance::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $userobj = User::getInstance($this->_customdata['id'], $this->_customdata['e'], $exammanagementinstanceobj->getCm()->instance);
        $moodleobj = Moodle::getInstance($this->_customdata['id'], $this->_customdata['e']);
        $moodledbobj = MoodleDB::getInstance();

        $PAGE->requires->js_call_amd('mod_exammanagement/remove_cols', 'remove_cols'); // Remove col-md classes for better layout

        $mform = $this->_form;

        $helptextsenabled = get_config('mod_exammanagement', 'enablehelptexts');

        $mform->addElement('html', '<div class="d-flex justify-content-between"><div><h3>' . get_string('chooseRooms', 'mod_exammanagement'));

        if ($helptextsenabled) {
            $mform->addElement('html', $OUTPUT->help_icon('chooseRooms', 'mod_exammanagement', ''));
        }

        $mform->addElement('html', '</h3></div><div>');

        $allrooms = $exammanagementinstanceobj->getRooms('all');

        $displayrooms = $exammanagementinstanceobj->getRooms('all', 'name', false, true, $this->_customdata['pagenr']);

        $examrooms = $exammanagementinstanceobj->getRooms('examrooms');

        $i = 10 * ($this->_customdata['pagenr'] - 1) + 1;

        if ($moodleobj->checkCapability('mod/exammanagement:importdefaultrooms', $this->_customdata['id'], $this->_customdata['e'])) {
            if ($allrooms) {
                $mform->addElement('html', '<a href="' . $exammanagementinstanceobj->getExammanagementUrl("exportDefaultRooms", $this->_customdata['id']) . '" class="btn btn-primary" title="' . get_string("export_default_rooms", "mod_exammanagement").'"><span class="d-none d-lg-block">' . get_string("export_default_rooms", "mod_exammanagement") . '</span><i class="fa fa-download d-lg-none" aria-hidden="true"></i></a>');
            }
            $mform->addElement('html', '<a href="' . $exammanagementinstanceobj->getExammanagementUrl("addDefaultRooms", $this->_customdata['id']) . '" class="btn btn-primary m-l-1" title="' . get_string("import_default_rooms", "mod_exammanagement").'"><span class="d-none d-lg-block">' . get_string("import_default_rooms", "mod_exammanagement") . '</span><i class="fa fa-file-text d-lg-none" aria-hidden="true"></i></a>');
        }

        if ($moodleobj->checkCapability('mod/exammanagement:importdefaultrooms', $this->_customdata['id'], $this->_customdata['e'])) {
            $mform->addElement('html', '<a href="' . $exammanagementinstanceobj->getExammanagementUrl("editDefaultRoom", $this->_customdata['id']) . '" class="btn btn-primary m-l-1" title="' . get_string("add_default_room", "mod_exammanagement").'"><span class="d-none d-lg-block">' . get_string("add_default_room", "mod_exammanagement") . '</span><i class="fa fa-plus d-lg-none" aria-hidden="true"></i></a></div>');
        } else {
            $mform->addElement('html', '<a href="' . $exammanagementinstanceobj->getExammanagementUrl("addCustomRoom", $this->_customdata['id']) . '" class="btn btn-primary m-l-1" title="' . get_string("add_custom_room", "mod_exammanagement").'"><span class="d-none d-lg-block">' . get_string("add_custom_room", "mod_exammanagement") . '</span><i class="fa fa-plus d-lg-none" aria-hidden="true"></i></a></div>');
        }

        $mform->addElement('html', '</div>');

        $mform->addElement('html', '<p>'.get_string('choose_rooms_str', 'mod_exammanagement').'</p>');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'page');
        $mform->setType('page', PARAM_INT);
        $mform->setDefault('page', $this->_customdata['pagenr']);

        if ($displayrooms) {
            $pagebar = new exammanagement_pagebar($this->_customdata['id'], 'chooseRooms.php?id=' . $this->_customdata['id'], sesskey(), $exammanagementinstanceobj->get_pagebar($allrooms, 10, $this->_customdata['pagenr']), $exammanagementinstanceobj->get_pagecountoptions(),  count($displayrooms), count($allrooms));
            $mform->addElement('html', $OUTPUT->render($pagebar));

            $mform->addElement('html', '<div class="table-responsive">');
            $mform->addElement('html', '<table class="table table-striped exammanagement_table">');

            if ($moodleobj->checkCapability('mod/exammanagement:importdefaultrooms', $this->_customdata['id'], $this->_customdata['e'])) {
                $mform->addElement('html', '<thead class="exammanagement_tableheader exammanagement_brand_backgroundcolor"><th scope="col">#</th><th scope="col">' . get_string("roomid", "mod_exammanagement") . '</th><th scope="col">'.get_string("exam_room", "mod_exammanagement") . '</th><th scope="col">' . get_string("description", "mod_exammanagement") . '</th><th scope="col">' . get_string("customroom_placescount", "mod_exammanagement") . '</th><th scope="col">' . get_string("seatingplan", "mod_exammanagement") . '</th><th scope="col">' . get_string("room_type", "mod_exammanagement") . '</th><th scope="col" class="exammanagement_table_whiteborder_left"></th></thead>');
            } else {
                $mform->addElement('html', '<thead class="exammanagement_tableheader exammanagement_brand_backgroundcolor"><th scope="col">#</th><th scope="col">' . get_string("exam_room", "mod_exammanagement") . '</th><th scope="col">'.get_string("description", "mod_exammanagement") . '</th><th scope="col">' . get_string("customroom_placescount", "mod_exammanagement") . '</th><th scope="col">' . get_string("seatingplan", "mod_exammanagement") . '</th><th scope="col">' . get_string("room_type", "mod_exammanagement") . '</th><th scope="col" class="exammanagement_table_whiteborder_left"></th></thead>');
            }

            $mform->addElement('html', '<tbody>');

            foreach ($displayrooms as $room) {

                $isroomfilled = $userobj->getParticipantsCount('room', $room->roomid);

                $similiarrooms = $moodledbobj->getRecordsFromDB('exammanagement_rooms', array('name' => $room->name));;
                $disablename = explode('_', $room->roomid);

                $mform->addElement('html', '<tr>');

                $mform->addElement('html', '<th scope="row" id="'.$i.'">'.$i.'</th>');

                if ($moodleobj->checkCapability('mod/exammanagement:importdefaultrooms', $this->_customdata['id'], $this->_customdata['e'])) {

                    $mform->addElement('html', '<td>');

                    $mform->addElement('html', $room->roomid);
                    $mform->addElement('html', '</td>');
                }

                $mform->addElement('html', '<td>');

                $mform->addElement('advcheckbox', 'rooms['.$room->roomid.']', $room->name, null, array('group' => 1));

                foreach ($similiarrooms as $similiarroomobj) { // code for preventing selection of multiple versions of the same room

                    if (strpos($similiarroomobj->roomid, $disablename[0]) !== false) {
                        $mform->disabledif ('rooms['.$room->roomid.']', 'rooms['.$similiarroomobj->roomid.']', 'checked');
                    }
                }

                $mform->addElement('html', '</td><td> '.$room->description.' </td>');

                $mform->addElement('html', '<td> '.count($room->places).' </td>');

                $mform->addElement('html', '<td>');
                if ($room->seatingplan) {

                    $svgstr = base64_decode($room->seatingplan);

                    $mform->addElement('html', '<a id="show" class="pointer"><i class="fa fa-2x fa-info-circle"></i></a><div class="exammanagement_rooms_svg collapse">'.$svgstr.'</div>');

                } else {
                    $mform->addElement('html', get_string('no_seatingplan_available', 'mod_exammanagement'));
                }
                $mform->addElement('html', '</td><td>');

                if ($room->type == 'defaultroom') {
                    if ($moodleobj->checkCapability('mod/exammanagement:importdefaultrooms')) {
                        $mform->addElement('html', get_string('default_room', 'mod_exammanagement'));
                        $mform->addElement('html', '</td><td class="exammanagement_brand_bordercolor_left">');
                        $mform->addElement('html', '<a href="editDefaultRoom.php?id=' . $this->_customdata['id'] . '&roomid=' . $room->roomid.'" title="'.get_string("change_room", "mod_exammanagement").'"><i class="fa fa-2x fa-edit"></i></a>');
                        $mform->addElement('html', ' <a href="chooseRooms.php?id=' . $this->_customdata['id'] . '&deletedefaultroomid=' . $room->roomid . '&sesskey=' . sesskey() . '" onClick="javascript:return confirm(\''.get_string("delete_defaultroom_confirm", "mod_exammanagement").'\');" title="'.get_string("delete_defaultroom_confirm", "mod_exammanagement").'"><i class="fa fa-2x fa-trash"></i></a>');

                    } else {
                        $mform->addElement('html', get_string('default_room', 'mod_exammanagement').'</td><td class="exammanagement_brand_bordercolor_left"></td>');
                    }

                } else {

                    $mform->addElement('html', get_string('custom_room', 'mod_exammanagement'));
                    $mform->addElement('html', '</td><td class="exammanagement_brand_bordercolor_left">');
                    $mform->addElement('html', '<a href="addCustomRoom.php?id=' . $this->_customdata['id'] . '&roomid='.$room->roomid.'" title="'.get_string("change_room", "mod_exammanagement").'"><i class="fa fa-2x fa-edit"></i></a>');
                    $mform->addElement('html', ' <a href="chooseRooms.php?id=' . $this->_customdata['id'] . '&deletecustomroomid=' . $room->roomid  . '&sesskey=' . sesskey() . '" onClick="javascript:return confirm(\''.get_string("delete_room_confirm", "mod_exammanagement").'\');" title="'.get_string("delete_room", "mod_exammanagement").'"><i class="fa fa-2x fa-trash"></i></a>');
                }

                if ($examrooms) {
                    foreach ($examrooms as $room2) {
                        if ($room->roomid == $room2->roomid) {
                              $mform->setDefault('rooms['.$room->roomid.']', true);
                        }
                    }
                }

                $mform->addElement('html', '</tr>');

                if ($isroomfilled) {

                    $colspan = 7;

                    if ($moodleobj->checkCapability('mod/exammanagement:importdefaultrooms', $this->_customdata['id'], $this->_customdata['e'])) {
                        $colspan += 1;
                    }

                    $mform->addElement('html', '<tr><td colspan="'.$colspan.'"><div class="alert alert-warning alert-block fade in " role="alert">'.get_string("places_already_assigned_rooms", "mod_exammanagement").'</div></td></tr>');
                }
                $i++;
            }

            $mform->addElement('html', '<tr></tr></tbody></table></div>');

            $this->add_action_buttons(true, get_string('choose_rooms', 'mod_exammanagement'));

        } else {
            $mform->addElement('html', '<p>'.get_string('no_rooms_found', 'mod_exammanagement').'</p>');

        }

        $mform->disable_form_change_checker();

    }

    // Custom validation should be added here.
    public function validation($data, $files) {
        $errors = array();

        foreach ($data['rooms'] as $roomid => $checked) {

            if ($checked == "1") {
                $moodledbobj = MoodleDB::getInstance();

                $roomname = explode('_', $roomid);
                $similiarrooms = $moodledbobj->getRecordsFromDB('exammanagement_rooms', array('name' => $roomname[0]));

                foreach ($similiarrooms as $key => $similiarroomobj) {
                    if (isset($data['rooms'][$similiarroomobj->roomid]) && is_string($data['rooms'][$similiarroomobj->roomid]) && $data['rooms'][$similiarroomobj->roomid] !== "0"
                        && is_string($data['rooms'][$roomid]) && $similiarroomobj->roomid !== $roomid) {

                        $errors['rooms['.$roomid.']'] = get_string('err_roomsdoubleselected', 'mod_exammanagement');
                    }
                }
            }

            if (!preg_match("/^[a-zA-Z0-9_\-. ]+$/", $roomid)) {
                $errors['rooms['.$roomid.']'] = get_string('err_invalidcheckboxid_rooms', 'mod_exammanagement');
            }
        }

        return $errors;
    }
}
