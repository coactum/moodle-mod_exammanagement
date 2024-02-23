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
 * Helper utilities for the module.
 *
 * @package   mod_exammanagement
 * @copyright 2024 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_exammanagement\local;

use mod_exammanagement\ldap\ldapmanager;

use moodle_url;
use stdClass;
use context_module;
use core\message\message;

/**
 * Utility class for the module.
 *
 * @package   mod_exammanagement
 * @copyright 2024 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Get the moodle system name from thr plugin config.
     *
     * @return string $systemname The moodle system name.
     */
    public static function getmoodlesystemname() {

        $systemname = get_config('mod_exammanagement', 'moodlesystemname');

        if ($systemname) {
            return $systemname;
        } else {
            return '';
        }
    }

    /**
     * Get the pagecount.
     *
     * @return int $pagecount The pagecount.
     */
    public static function getpagecount() {
        $pagecount = get_user_preferences('exammanagement_pagecount');

        if (!isset($pagecount) || $pagecount < 0) {
            return 10;
        } else {
            return $pagecount;
        }
    }

    /**
     * Get the exammanagement module instance.
     * @param int $cmid The course modul id.
     * @param int $e The exammanagement record id.
     *
     * @return int $moduleinstance The moduleinstance.
     */
    public static function getmoduleinstance($cmid, $e) {
        global $DB;

        if ($cmid) {
            [$course, $cm] = get_course_and_cm_from_cmid($cmid, 'exammanagement');
            $moduleinstance = $DB->get_record('exammanagement', ['id' => $cm->instance], '*', MUST_EXIST);

            return $moduleinstance;
        } else if ($e) {
            $moduleinstance = $DB->get_record('exammanagement', ['id' => $e], '*', MUST_EXIST);

            return $moduleinstance;

        } else {
            return false;
        }
    }

    /**
     * Get single moodle user.
     *
     * @param int $userid The id of thr user.
     *
     * @return object $user The user object or false.
     */
    public static function getmoodleuser($userid) {

        global $DB;

        $user = $DB->get_record('user', ['id' => $userid]);

        if ($user) {
            return $user;
        } else {
            return false;
        }
    }

    /**
     * Get array with all requested exam participants.
     * @param object $moduleinstance The module instance.
     * @param array $participantsmode Array with mode of participants requested.
     * @param array $requestedattributes Array with all requested attributes.
     * @param string $sortorder The sort order.
     * @param bool $pagination If pagination is enabled.
     * @param int $activepage The active page.
     * @param bool $conditions The conditions.

     * @return array $editoroptions Array containing the editor options.
     * @return array $attachmentoptions Array containing the attachment options.
     */
    public static function getexamparticipants($moduleinstance, $participantsmode, $requestedattributes, $sortorder = 'name',
        $pagination = false, $activepage = null, $conditions = false) {

        global $DB;

        $allparticipants = [];

        // If pagination and active page is set.
        if ($pagination && isset($activepage)) {
            $pagecount = self::getpagecount();
            $limitfrom = $pagecount * ($activepage - 1);
            $limitnum = $pagecount;
        } else {
            $limitfrom = 0;
            $limitnum = 0;
        }

        if ($participantsmode['mode'] === 'all') {
            $rs = $DB->get_recordset('exammanagement_participants', ['exammanagement' => $moduleinstance->id]);
        } else if ($participantsmode['mode'] === 'moodle') {
            $rs = $DB->get_recordset('exammanagement_participants',
                ['exammanagement' => $moduleinstance->id, 'login' => null]);
        } else if ($participantsmode['mode'] === 'nonmoodle') {
            $rs = $DB->get_recordset('exammanagement_participants',
                ['exammanagement' => $moduleinstance->id, 'moodleuserid' => null]);
        } else if ($participantsmode['mode'] === 'room') {
            $rs = $DB->get_recordset('exammanagement_participants',
                ['exammanagement' => $moduleinstance->id, 'roomid' => $participantsmode['id']]);
        } else if ($participantsmode['mode'] === 'header') {
            $rs = $DB->get_recordset('exammanagement_participants',
                ['exammanagement' => $moduleinstance->id, 'headerid' => $participantsmode['id']]);
        } else if ($participantsmode['mode'] === 'resultsafterexamreview') {
            $examreviewtime = $moduleinstance->examreviewtime;

            $select = "exammanagement =" . $moduleinstance->id;
            $select .= " AND exampoints IS NOT NULL";
            $select .= " AND examstate IS NOT NULL";
            $select .= " AND timeresultsentered IS NOT NULL";
            $select .= " AND timeresultsentered >=" . $examreviewtime;

            $rs = $DB->get_recordset_select('exammanagement_participants', $select, null);

        } else if ($participantsmode['mode'] === 'no_seats_assigned') {
            $select = "exammanagement =" . $moduleinstance->id;
            $select .= " AND roomid IS NULL";
            $select .= " AND roomname IS NULL";
            $select .= " AND place IS NULL";

            $rs = $DB->get_recordset_select('exammanagement_participants', $select, null);

        } else {
            return false;
        }

        if ($rs->valid()) {

            $courseid = $moduleinstance->course;

            foreach ($rs as $record) {

                // Add login if it is requested as attribute or needed for matrnr.
                if ((in_array('login', $requestedattributes) && isset($record->moodleuserid)) ||
                    (in_array('matrnr', $requestedattributes) && isset($record->moodleuserid))) {

                    $login = $DB->get_field('user', 'username', ['id' => $record->moodleuserid]);
                    $record->login = $login;
                }

                // Add name if it is requested as attribute or needed for sorting or profile.
                if (isset($record->moodleuserid) && (in_array('name', $requestedattributes) ||
                    in_array('profile', $requestedattributes) || $sortorder == 'name' )) {

                    $moodleuser = self::getmoodleuser($record->moodleuserid);

                    if ($moodleuser) {
                        $record->firstname = $moodleuser->firstname;
                        $record->lastname = $moodleuser->lastname;
                        if (in_array('profile', $requestedattributes)) { // Add profile if it is requested.
                                global $OUTPUT;

                                $image = $OUTPUT->user_picture($moodleuser,
                                    ['courseid' => $courseid, 'link' => true, 'includefullname' => true]);

                                $record->profile = $image;
                        }

                        if (in_array('groups', $requestedattributes)) { // Add group if it is requested.
                            $usergroups = groups_get_user_groups($courseid, $record->moodleuserid);
                            $groupnames = false;

                            foreach ($usergroups as $key => $value) {
                                if ($value) {
                                    foreach ($value as $key2 => $value2) {
                                        $url = new moodle_url('/group/index.php', ['id' => $courseid, 'group' => $value2]);
                                        if (!$groupnames) {
                                            $groupnames = '<strong><a href="' . $url .'">' .
                                                groups_get_group_name($value2) . '</a></strong>';
                                        } else {
                                            $groupnames .= ', <strong><a href="' . $url  .'">' .
                                                groups_get_group_name($value2) . '</a></strong>';
                                        }
                                    }
                                } else {
                                    $groupnames = '-';
                                    break;
                                }
                            }
                            $record->groups = $groupnames;
                        }
                    } else {
                        $systemname = self::getmoodlesystemName();
                        $record->firstname = get_string('deleted_user', 'mod_exammanagement', ['systemname' => $systemname]);
                        $record->lastname = get_string('deleted_user', 'mod_exammanagement', ['systemname' => $systemname]);

                        if (in_array('profile', $requestedattributes)) {
                            $record->profile = get_string('deleted_user', 'mod_exammanagement', ['systemname' => $systemname]);
                        }
                        if (in_array('groups', $requestedattributes)) {
                            $record->groups = '-';
                        }
                    }
                }

                array_push($allparticipants, $record);
            }

            $rs->close();

            // Add matrnr if it is requested.
            if (in_array('matrnr', $requestedattributes)) {

                require_once(__DIR__.'/../ldap/ldapmanager.php');

                $ldapmanager = ldapmanager::getinstance();

                $matriculationnumbers = [];
                $alllogins = [];

                foreach ($allparticipants as $key => $participant) { // Set logins array for ldap method.
                    array_push($alllogins, $participant->login);
                }

                $matriculationnumbers = $ldapmanager->getmatrnrsforlogins($alllogins); // Retrieve matrnrs for all logins from ldap.

                if (!empty($matriculationnumbers)) {
                    foreach ($allparticipants as $key => $participant) {
                        if (isset($participant->login) && $participant->login &&
                            array_key_exists($participant->login, $matriculationnumbers) &&
                            isset($matriculationnumbers[$participant->login]) &&
                            is_numeric($matriculationnumbers[$participant->login])) {

                            $participant->matrnr = $matriculationnumbers[$participant->login];
                        } else {
                            $participant->matrnr = '-';

                            if ($conditions == 'withmatrnr') {
                                unset($allparticipants[$key]);
                            }
                        }
                    }
                } else {
                    foreach ($allparticipants as $key => $participant) {
                        $participant->matrnr = '-';

                        if ($conditions == 'withmatrnr') {
                            unset($allparticipants[$key]);
                        }
                    }
                }
            }

            // Sort all participant sarray.
            if ($sortorder == 'name') {
                usort($allparticipants, function($a, $b) { // Sort participants array by name through custom user function.

                    $searcharr = ["Ä", "ä", "Ö", "ö", "Ü", "ü", "ß", "von ", "Von "];
                    $replacearr = ["Ae", "ae", "Oe", "oe", "Ue", "ue", "ss", ""];

                    // If lastnames are even sort by first name.
                    if (str_replace($searcharr, $replacearr,
                        ucfirst($a->lastname)) == str_replace($searcharr, $replacearr, ucfirst($b->lastname))) {

                        return strcmp(ucfirst($a->firstname), ucfirst($b->firstname));
                    } else { // Else sort by last name.
                        return strcmp(str_replace($searcharr, $replacearr,
                            ucfirst($a->lastname)) , str_replace($searcharr, $replacearr, ucfirst($b->lastname)));
                    }

                });
            } else if ($sortorder == 'matrnr') {
                usort($allparticipants, function($a, $b) { // Sort participants array by matrnr through custom user function.
                    return strnatcmp($a->matrnr, $b->matrnr); // Sort by matrnr (ascending).
                });
            } else if ($sortorder == 'random') {
                shuffle($allparticipants);
            }

            if ($pagination && isset($activepage)) {
                return array_slice($allparticipants, $limitfrom, $limitnum);
            } else {
                return $allparticipants;
            }

        } else {
            $rs->close();
            return false;
        }
    }

    /**
     * Get a single exam participant.
     * @param object $moduleinstance The module instance.
     * @param int $moodleuserid Moodle if of the user.
     * @param string $userlogin The login of thr user.
     * @param bool $id The examparticipants id of the user.

     * @return array $editoroptions Array containing the editor options.
     * @return array $attachmentoptions Array containing the attachment options.
     */
    public static function getexamparticipant($moduleinstance, $moodleuserid, $userlogin = false, $id = false) {
        global $DB;

        if ($moodleuserid !== false && $moodleuserid !== null) {
            $participants = $DB->get_record('exammanagement_participants',
                ['exammanagement' => $moduleinstance->id, 'moodleuserid' => $moodleuserid]);
        } else if ($userlogin !== false && $userlogin !== null) {
            $participants = $DB->get_record('exammanagement_participants',
                ['exammanagement' => $moduleinstance->id, 'login' => $userlogin]);
        } else if ($id !== false && $id !== null) {
            $participants = $DB->get_record('exammanagement_participants',
                ['exammanagement' => $moduleinstance->id, 'id' => $id]);
        }

        if ($participants) {
            return $participants;
        } else {
            return false;
        }
    }

    /**
     * Get ids of all participants enrolled in the course.
     * @param int $cmid The course modul id.
     *
     * @return array $courseparticipantsids Array containing the course participants ids or null.
     */
    public static function getcourseparticipantsids($cmid) {

        $context = context_module::instance($cmid);

        $courseparticipants = get_enrolled_users($context, 'mod/exammanagement:takeexams');
        $courseparticipantsids;

        foreach ($courseparticipants as $key => $value) {
            $temp = get_object_vars($value);
            $courseparticipantsids[$key] = $temp['id'];
        }

        if (isset($courseparticipantsids)) {
            return $courseparticipantsids;
        } else {
            return false;
        }
    }

    /**
     * Filter checked participants from form.
     * @param object $form The form.
     *
     * @return array $participants The participants or false.
     */
    public static function filtercheckedparticipants($form) {

        $form = get_object_vars($form);

        $allparicipants = [];

        if (isset($form['participants'])) {
            $allparicipants['participants'] = $form['participants'];
        }

        if (isset($form['deletedparticipants'])) {
            $allparicipants['deletedparticipants'] = $form['deletedparticipants'];
        }

        $participants = ['participants' => [], 'deletedparticipants' => []];

        if (!empty($allparicipants['participants'])) {
            foreach ($allparicipants['participants'] as $key => $value) {
                if ($value == 1) {
                    array_push($participants['participants'], $key);
                }
            }
        }

        if (!empty($allparicipants['deletedparticipants'])) {
            foreach ($allparicipants['deletedparticipants'] as $key => $value) {
                if ($value == 1) {
                    array_push($participants['deletedparticipants'], $key);
                }
            }
        }

        if (!empty($participants['participants']) || !empty($participants['deletedparticipants']) ) {
            return $participants;
        } else {
            return false;
        }
    }

    /**
     * Check if user is already participant.
     * @param object $moduleinstance The moduleinstance.
     * @param int $userid The userid.
     * @param string $login The login as alternative identifier.
     *
     * @return bool
     */
    public static function checkifalreadyparticipant($moduleinstance, $userid, $login = false) {

        global $DB;

        if ($userid) {
            return $DB->record_exists('exammanagement_participants',
                ['exammanagement' => $moduleinstance->id, 'moodleuserid' => $userid]);
        } else if ($login) { // This part seems wrong but is probably needed anyway.
            return $DB->record_exists('exammanagement_participants',
                ['exammanagement' => $moduleinstance->id, 'login' => $userid]);
        }
    }

    /**
     * Check if string is valid matriculation number.
     *
     * @param string $matrnr The matriculation number.
     *
     * @return bool
     */
    public static function checkifvalidmatrnr($matrnr) {
        if (!preg_match("/^\d+$/", $matrnr)) {
            return false;
        }

        if (strlen($matrnr) == 7) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete temp participants.
     *
     * @param object $moduleinstance The moduleinstance.
     *
     * @return bool
     */
    public static function deletetempparticipants($moduleinstance) {

        global $DB;

        $moduleinstance->tempimportfileheader = null;

        $DB->update_record("exammanagement", $moduleinstance);

        if ($DB->record_exists('exammanagement_temp_part', ['exammanagement' => $moduleinstance->id])) {
            $DB->delete_records('exammanagement_temp_part', ['exammanagement' => $moduleinstance->id]);
        } else {
            return false;
        }
    }

    /**
     * Check if examdata is already deleted.
     * @param object $moduleinstance The moduleinstance.
     *
     * @return bool
     */
    public static function isexamdatadeleted($moduleinstance) {
        if (isset($moduleinstance->datadeleted) && $moduleinstance->datadeleted) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if any places for the exam are assigned.
     * @param object $moduleinstance The moduleinstance.
     *
     * @return bool
     */
    public static function placesassigned($moduleinstance) {
        if (isset($moduleinstance->assignmentmode) || self::allplacesassigned($moduleinstance)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if all places for the exam are assigned.
     * @param object $moduleinstance The moduleinstance.
     *
     * @return bool
     */
    public static function allplacesassigned($moduleinstance) {
        $assignedplacescount = self::getassignedplacescount($moduleinstance);

        if ($assignedplacescount !== 0 && $assignedplacescount == self::getparticipantscount($moduleinstance)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the number of assigned places.
     * @param object $moduleinstance The moduleinstance.
     *
     * @return int $assignedplacescount The number of assigned places.
     */
    public static function getassignedplacescount($moduleinstance) {

        global $DB;

        $select = "exammanagement =" . $moduleinstance->id;
        $select .= " AND roomid IS NOT NULL";
        $select .= " AND roomname IS NOT NULL";
        $select .= " AND place IS NOT NULL";

        $assignedplacescount = $DB->count_records_select('exammanagement_participants', $select);

        return $assignedplacescount;
    }

    /**
     * Get the number of participants.
     * @param object $moduleinstance The moduleinstance.
     * @param string $mode The mode.
     * @param int $roomid If only participants in one room should be counted.
     *
     * @return int $participantscount The number of participants.
     */
    public static function getparticipantscount($moduleinstance, $mode = 'all', $roomid = false) {

        global $DB;

        $select = "exammanagement =" . $moduleinstance->id;

        if ($mode == 'moodle') {
            $select .= " AND moodleuserid IS NOT NULL";
        } else if ($mode == 'nonmoodle') {
            $select .= " AND moodleuserid IS NULL";
        } else if ($mode == 'room' && $roomid) {
            $select .= " AND roomid = '" . $roomid . "'";
        }

        $participantscount = $DB->count_records_select('exammanagement_participants', $select);

        if ($participantscount) {
            return $participantscount;
        } else {
            return false;
        }
    }

    /**
     *  Delete participant.
     * @param object $moduleinstance The moduleinstance.
     * @param int $userid The ID of the user to be deleted.
     * @param string $login The login of the user to be deleted.
     *
     */
    public static function deleteparticipant($moduleinstance, $userid, $login = false) {

        global $DB;

        if ($userid !== false &&
            $DB->record_exists('exammanagement_participants',
                ['exammanagement' => $moduleinstance->id, 'moodleuserid' => $userid])) {

            $DB->delete_records('exammanagement_participants',
                ['exammanagement' => $moduleinstance->id, 'moodleuserid' => $userid]);
        } else if ($DB->record_exists('exammanagement_participants',
            ['exammanagement' => $moduleinstance->id, 'login' => $login])) {

            $DB->delete_records('exammanagement_participants',
                ['exammanagement' => $moduleinstance->id, 'login' => $login]);
        }
    }

    /**
     * Get rooms based an parameters.
     *
     * @param object $moduleinstance The moduleinstance.
     * @param string $mode The mode.
     * @param string $sortorder The sortorder.
     * @param bool $withoutassignedplaces If assigned places should be added.
     * @param bool $pagination The pagination.
     * @param bool $activepage The active page.
     *
     * @return array $rooms The rooms or false.
     */
    public static function getrooms($moduleinstance, $mode, $sortorder = 'name',
        $withoutassignedplaces = false, $pagination = false, $activepage = null) {

        global $DB;

        $rooms = [];

        // For pagination.
        if ($pagination && isset($activepage)) {
            $pagecount = self::getpagecount();
            $limitfrom = $pagecount * ($activepage - 1);
            $limitnum = $pagecount;
        } else {
            $limitfrom = 0;
            $limitnum = 0;
        }

        if ($mode === 'examrooms') {

            $roomids = json_decode($moduleinstance->rooms ?? '');

            if (isset($roomids)) {
                $roomids = implode("', '", $roomids);

                $select = "roomid IN ('" . $roomids . "')";

                if ($sortorder == 'name') {
                    $rs = $DB->get_recordset_select('exammanagement_rooms', $select, [], 'name ASC', '*', $limitfrom, $limitnum);
                } else {
                    $rs = $DB->get_recordset_select('exammanagement_rooms', $select, [], '', '*', $limitfrom, $limitnum);
                }
            } else {
                return false;
            }

        } else if ($mode === 'defaultrooms') {

            $select = "type = 'defaultroom'";

            $rs = $DB->get_recordset_select('exammanagement_rooms', $select, [], 'name ASC', '*', $limitfrom, $limitnum);
        } else if ($mode === 'all') {

            global $USER;

            $select = "type = 'defaultroom'";
            $select .= " OR type = 'customroom' AND moodleuserid = "  . $USER->id;

            $rs = $DB->get_recordset_select('exammanagement_rooms', $select, [], 'name ASC', '*', $limitfrom, $limitnum);
        } else {
            return false;
        }

        if ($rs->valid()) {

            foreach ($rs as $record) {
                $record->places = json_decode($record->places);
                $rooms[$record->roomid] = $record;
            }

            $rs->close();

            if ($sortorder == 'places_bigtosmall' || $sortorder == 'places_smalltobig' ) {

                usort($rooms, function($a, $b) { // Sort rooms by places count through custom user function (small to big rooms).
                    $aplaces = count($a->places);
                    $bplaces = count($b->places);

                    return strnatcmp($aplaces, $bplaces); // Sort by places count.
                });

                if ($sortorder == 'places_bigtosmall') {
                    $rooms = array_reverse($rooms); // Reverse array: now big to small rooms.
                }
            }

            // If rooms should not contain places already assigned to participants.
            if ($withoutassignedplaces) {
                $assignedplaces = self::getassignedplaces($moduleinstance);

                if (isset($assignedplaces)) {
                    foreach ($rooms as $room) {
                        if (isset($assignedplaces[$room->roomid])) {
                            foreach ($room->places as $place) {
                                if (in_array($place, $assignedplaces[$room->roomid])) {
                                    if (($key = array_search($place, $room->places)) !== false) {
                                        unset($room->places[$key]);
                                    }
                                }
                            }
                        }
                    }
                }
                $room->seatingplan = null;
            }

            $rs->close();
            return $rooms;

        } else {
            $rs->close();
            return false;
        }
    }

    /**
     * Get all assigned places in the exammanagement.
     *
     * @param object $moduleinstance The moduleinstance.
     *
     * @return array $assignedplaces The assigned places.
     */
    public static function getassignedplaces($moduleinstance) {

        global $DB;

        $select = "exammanagement =" . $moduleinstance->id;
        $select .= " AND roomid IS NOT NULL";
        $select .= " AND roomname IS NOT NULL";
        $select .= " AND place IS NOT NULL";

        $assignedplaces = [];

        $rs = $DB->get_recordset_select('exammanagement_participants', $select, null, '', 'roomid, place');

        if ($rs->valid()) {

            foreach ($rs as $record) {
                if (!isset($assignedplaces[$record->roomid])) {
                    $assignedplaces[$record->roomid] = [$record->place];
                } else {
                    array_push($assignedplaces[$record->roomid], $record->place);
                }
            }

            $rs->close();
        }

        if (isset($assignedplaces) && !empty($assignedplaces)) {
            return $assignedplaces;
        } else {
            return false;
        }
    }

    /**
     * Get the number of rooms in the exammanagement.
     *
     * @param object $moduleinstance The moduleinstance.
     *
     * @return int The number of rooms or false.
     */
    public static function getroomscount($moduleinstance) {
        $rooms = $moduleinstance->rooms;

        if ($rooms) {
            $rooms = json_decode($rooms);
            return count($rooms);
        } else {
            return false;
        }
    }

    /**
     * Get the names of the choosen rooms in the exammanagement.
     *
     * @param object $moduleinstance The moduleinstance.
     *
     * @return string $rooms The choosen room names.
     */
    public static function getchoosenroomnames($moduleinstance) {

        global $DB;

        $rooms = $moduleinstance->rooms;
        $roomnames = [];

        if ($rooms) {
            $rooms = json_decode($rooms);

            foreach ($rooms as $key => $value) {
                $temp = $DB->get_record('exammanagement_rooms', ['roomid' => $value]);
                if ($temp) {
                    array_push($roomnames, $temp->name);
                } else {
                    array_push($roomnames, get_string('deleted_room', 'mod_exammanagement'));
                }
            }

            asort($roomnames);

            $rooms = implode(", ", $roomnames);

            return $rooms;

        } else {
            return false;
        }
    }

    /**
     * Get the total number of seats for all rooms in the exammanagement.
     *
     * @param object $moduleinstance The moduleinstance.
     *
     * @return int $totalseats The total number of seats.
     */
    public static function gettotalnumberofseats($moduleinstance) {

        $rooms = self::getRooms($moduleinstance, 'examrooms');
        $totalseats = 0;

        if ($rooms) {
            foreach ($rooms as $room) {

                $places = $room->places;

                if (isset($places)) {
                    $placescount = count($places);
                } else {
                    $placescount = 0;
                }

                $totalseats += $placescount;
            }
        }

        return $totalseats;

    }

    /**
     * Get the tasks in the exammanagement.
     *
     * @param object $moduleinstance The moduleinstance.
     *
     * @return int $tasks The tasks.
     */
    public static function gettasks($moduleinstance) {

        if (is_null($moduleinstance->tasks)) {
            $tasks = null;
        } else {
            $tasks = (array) json_decode($moduleinstance->tasks);
        }

        if ($tasks) {
            return $tasks;
        } else {
            return false;
        }
    }

    /**
     * Get the total points off all tasks in the exammanagement.
     *
     * @param object $moduleinstance The moduleinstance.
     *
     * @return int $totalpoints The total task points.
     */
    public static function gettasktotalpoints($moduleinstance) {

        $tasks = self::gettasks($moduleinstance);
        $totalpoints = 0;

        if ($tasks) {
            foreach ($tasks as $key => $points) {
                $totalpoints += floatval($points);
            }
            return $totalpoints;

        } else {
            return $totalpoints;
        }
    }

    /**
     * Get the number of entered bonus in the exammanagement.
     *
     * @param object $moduleinstance The moduleinstance.
     * @param string $mode If bonus points or steps or both should be counted.
     *
     * @return int $enteredbonuscount The number of entered bonus.
     */
    public static function getenteredbonuscount($moduleinstance, $mode = 'both') {

        global $DB;

        $select = "exammanagement =" . $moduleinstance->id;

        switch ($mode) {
            case 'both':
                $select .= " AND (bonussteps IS NOT NULL";
                $select .= " OR bonuspoints IS NOT NULL)";
                break;
            case 'steps':
                $select .= " AND bonussteps IS NOT NULL";
                break;
            case 'points':
                $select .= " AND bonuspoints IS NOT NULL";
                break;
            default:
                $select .= " AND (bonussteps IS NOT NULL";
                $select .= " OR bonuspoints IS NOT NULL)";
                break;
        }

        $enteredbonuscount = $DB->count_records_select('exammanagement_participants', $select);

        if ($enteredbonuscount) {
            return $enteredbonuscount;
        } else {
            return false;
        }
    }

    /**
     * Get the number of entered results in the exammanagement.
     *
     * @param object $moduleinstance The moduleinstance.
     * @param int $timestamp The timestamp when the results should be entered.
     *
     * @return int $enteredresultscount The number of entered results.
     */
    public static function getenteredresultscount($moduleinstance, $timestamp = false) {

        global $DB;

        $select = "exammanagement =" . $moduleinstance->id;
        $select .= " AND exampoints IS NOT NULL";
        $select .= " AND examstate IS NOT NULL";

        if ($timestamp) {
            $select .= " AND timeresultsentered IS NOT NULL";
            $select .= " AND timeresultsentered >=" . $timestamp;
        }

        $enteredresultscount = $DB->count_records_select('exammanagement_participants', $select);

        if ($enteredresultscount) {
            return $enteredresultscount;
        } else {
            return false;
        }
    }

    /**
     * Calculate the points for a participant.
     *
     * @param object $participant The participant.
     * @param bool $withbonus If bonus should be considered.
     *
     * @return int $points The points.
     */
    public static function calculatepoints($participant, $withbonus=false) {
        $points = 0;

        if (is_null($participant->exampoints)) {
            $allpoints = null;
        } else {
            $allpoints = json_decode($participant->exampoints);
        }

        if ($allpoints != null) {

            $examstate = self::getexamstate($participant);

            if ($examstate === 'normal') {
                foreach ($allpoints as $key => $taskpoints) {
                    $points += floatval($taskpoints);
                }

                if ($withbonus && $participant->bonuspoints) {
                    $points += floatval($participant->bonuspoints);
                }

                return number_format(floatval($points), 2);
            } else if ($examstate) {
                return get_string($examstate, "mod_exammanagement");
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    /**
     * Get the exam state for the participant.
     *
     * @param object $participant The participant.
     *
     * @return string.
     */
    public static function getexamstate($participant) {

        if (isset($participant->examstate)) {

            $state = json_decode($participant->examstate);

            foreach ($state as $key => $value) {
                if ($key == 'nt' && $value == "1") {
                    return 'nt';
                } else if ($key == 'fa' && $value == "1") {
                    return 'fa';
                } else if ($key == 'ill' && $value == "1") {
                    return 'ill';
                }
            }

            return 'normal';
        } else {
            return false;
        }
    }

    /**
     * Calculates the result of a participant based on points
     *
     * @param object $moduleinstance The moduleinstance.
     * @param int   $points  Points (or special exam state) that should be converted to result grade
     * @param string    $bonussteps  Bonus grade steps if grade should be calculated with bonus (default: false)
     *
     * @return string   $result The result.
     */
    public static function calculateresultgrade($moduleinstance, $points, $bonussteps = false) {

        $gradingscale = json_decode($moduleinstance->gradingscale ?? '');

        $lastpoints = 0;

        $result = false;

        if ($points === false || !isset($points)) { // If points are false or not set.

            $result = '-';

        } else if (!is_numeric($points)) { // Else if points indicate special exam state.
            $result = $points;

        } else if ($gradingscale) { // Else if points and gradingscale are set and should be converted into grade.

            foreach ($gradingscale as $key => $step) {

                if ($key == '1.0' && $points >= floatval($step)) {
                    $result = $key;
                } else if ($points < $lastpoints && $points >= floatval($step)) {
                    $result = $key;
                } else if ($key == '4.0' && $points < floatval($step)) {
                    $result = 5;
                }

                $lastpoints = floatval($step);
            }

            if ($bonussteps) {
                switch ($bonussteps) {
                    case '0':
                        $bonussteps = 0.3;
                        break;
                    case '1':
                        $bonussteps = 0.3;
                        break;
                    case '2':
                        $bonussteps = 0.7;
                        break;
                    case '3':
                        $bonussteps = 1.0;
                        break;
                    default: // If bonus grade steps are not entered and null.
                        $bonussteps = 0;
                        break;
                }

                if ($result === 5) {
                    return $result;
                } else if ($bonussteps == 0) {
                    return $result;
                } else {

                    $resultwithbonus = $result - $bonussteps;

                    if ($resultwithbonus <= 1.0) {
                        return '1.0';
                    }

                    $peculiarity = round($resultwithbonus - floor($resultwithbonus), 1);

                    if (0.4 == $peculiarity ) {
                        $resultwithbonus = $resultwithbonus - 0.1;
                    }

                    if (0.6 == $peculiarity ) {
                        $resultwithbonus = $resultwithbonus + 0.1;
                    }

                    return (str_pad (strval($resultwithbonus), 3, '.0'));
                }
            }
        } else { // Should be converted to grade but gradingscale is not set.
            $result = '-';
        }

        return $result;
    }

    /**
     * Convert the exam time into a human readable format (used in exported documents).
     *
     * @param object $moduleinstance The moduleinstance.
     *
     * @return string $examtime The human readable exam time.
     */
    public static function gethrexamtime($moduleinstance) {
        if (isset($moduleinstance->examtime)) {
            $examtime = userdate($moduleinstance->examtime, get_string('strftimedatetimeshort', 'core_langconfig'));
            return $examtime;
        } else {
            return false;
        }
    }

    /**
     * Convert the exam review time into a human readable format.
     *
     * @param object $moduleinstance The moduleinstance.
     *
     * @return string $examreviewtime The human readable exam review time.
     */
    public static function gethrexamreviewtime($moduleinstance) {
        if (isset($moduleinstance->examreviewtime)) {
            $examreviewtime = userdate($moduleinstance->examreviewtime, get_string('strftimedatetimeshort', 'core_langconfig'));
            return $examreviewtime;
        } else {
            return false;
        }
    }

    /**
     * Get human readable data deletion date.
     *
     * @param object $moduleinstance The moduleinstance.
     *
     * @return string $examtime The human readable examtime.
     */
    public static function getdatadeletiondate($moduleinstance) {

        if ($moduleinstance->datadeletion) {
            $datadeletiondate = userdate($moduleinstance->datadeletion, get_string('strftimedatefullshort', 'core_langconfig'));
        } else {
            return false;
        }

        return $datadeletiondate;
    }

    /**
     * Get the textfield.
     *
     * @param object $moduleinstance The moduleinstance.
     *
     * @return object $textfield The textfield as an object
     */
    public static function gettextfield($moduleinstance) {

        $textfield = $moduleinstance->textfield;

        if (is_null($textfield)) {
            return false;
        } else {
            $textfield = json_decode($moduleinstance->textfield);
            $textfield->text = format_text($textfield->text, $textfield->format, ['para' => false]);
            return $textfield;
        }
    }

    /**
     * Send a message to a single user.
     *
     * @param object $moduleinstance The moduleinstance.
     * @param int $cmid The course module id.
     * @param object $course The course.
     * @param string $moodlesystemname The moodle system name.
     * @param object $userfrom The sender.
     * @param object $userto The recipient.
     * @param string $subject The message subject.
     * @param string $text The message text.
     * @param string $type The message type.
     * @param bool $cron If called from sheduled task.
     *
     * @return int $messageid The id of the message send.
     */
    public static function sendsinglemessage($moduleinstance, $cmid, $course, $moodlesystemname, $userfrom, $userto, $subject,
        $text, $type, $cron = false) {

        $message = new message();
        $message->courseid = $course->id;
        $message->component = 'mod_exammanagement'; // The component sending the message, must exist in the table message_providers.
        $message->name = $type; // Type of message from that module (as module defines it in the message_providers).
        $message->userfrom = $userfrom; // User sending the message.
        $message->userto = $userto; // User receiving the message.
        $message->subject = $subject; // Very short one-line subject.
        $message->fullmessage = $text; // Raw text.
        $message->fullmessageformat = FORMAT_MARKDOWN; // Text format.
        $message->fullmessagehtml = $text; // HTML rendered version.
        $message->smallmessage = $text; // Useful for plugins like sms or twitter.
        $message->notification = 1;
        $message->contexturl = new moodle_url("/mod/exammanagement/view.php", ['id' => $cmid]);
        $message->contexturlname = $moduleinstance->name . ' (' . get_string('modulename', 'mod_exammanagement') . ')';
        $message->replyto = '';

        $header = '';
        $url = '<a href="' . new moodle_url("/mod/exammanagement/view.php", ['id' => $cmid]) . '" target="_blank">' .
            new moodle_url("/mod/exammanagement/view.php", ['id' => $cmid]) . '</a>';

        if ($cron == false) {
            $footer = '<br><br> --------------------------------------------------------------------- <br> ' .
                get_string('mailfooter', 'mod_exammanagement', [
                    'systemname' => $moodlesystemname,
                    'categoryname' => self::getcleancoursecategoryname(),
                    'coursename' => $course->fullname,
                    'name' => $moduleinstance->name,
                    'url' => $url,
                ]);
        } else {
            $footer = '<br><br> --------------------------------------------------------------------- <br> ' .
                get_string('mailfooter', 'mod_exammanagement',
                    ['systemname' => $moodlesystemname,
                    'categoryname' => '',
                    'coursename' => $course->fullname,
                    'name' => $moduleinstance->name,
                    'url' => $url,
                ]);
        }
        $content = ['*' => ['header' => $header, 'footer' => $footer]]; // Extra content for specific processor.

        $message->set_additional_content('email', $content);

        $messageid = message_send($message);

        return $messageid;
    }

    /**
     * Get mail adresses of all nonmoodle users.
     *
     * @param object $moduleinstance The moduleinstance.
     *
     * @return array $nonemoodleparticipantsemailadresses The emailadresses of all nonemoodleparticipants.
     */
    public static function getnonemoodleparticipantsemailadresses($moduleinstance) {

        global $DB;

        $select = "exammanagement =" . $moduleinstance->id;
        $select .= " AND moodleuserid IS NULL";

        $nonemoodleparticipantsemailadresses = $DB->get_fieldset_select('exammanagement_participants', 'email', $select);

        if (!empty($nonemoodleparticipantsemailadresses)) {
            return $nonemoodleparticipantsemailadresses;
        } else {
            return false;
        }
    }

    /**
     * Get the clean course category name.
     *
     * @return string $categoryname The clean course category name.
     */
    public static function getcleancoursecategoryname() {

        global $PAGE;

        $categoryname = substr(strtoupper(preg_replace("/[^0-9a-zA-Z]/", "", $PAGE->category->name)), 0, 20);

        if ($categoryname) {
            return $categoryname;
        } else {
            return get_string('coursecategory_name_no_semester', 'mod_exammanagement');
        }
    }

    /**
     * Format a number depending on the language for displaying.
     *
     * @param int $number The number
     * @param string $format The format
     *
     * @return int $number The number
     */
    public static function formatnumberfordisplay($number, $format='string') {
        if ($number !== false) {

            if ($format === 'string' && is_numeric($number)) {
                $lang = current_language();

                if ($lang === "de") {
                     $number = str_replace('.', ',', $number);
                } else {
                    $number = str_replace(',', '.', $number);
                }
            }

            return $number;
        } else {
            return '-';
        }
    }

    /**
     * Build checksum for exam labels.
     *
     * @param string $ean The ean.
     *
     * @return int
     */
    public static function buildchecksumexamlabels($ean) {
        $s = preg_replace("/([^\d])/", "", $ean);
        if (strlen($s) != 12) {
            return false;
        }

        $check = 0;
        for ($i = 0; $i < 12; $i++) {
            $check += (($i % 2) * 2 + 1) * $s[$i];
        }

        return (10 - ($check % 10)) % 10;
    }

    /**
     * Build the header for the participants list table.
     *
     * @return string
     */
    public static function getparticipantslisttableheader() {
        $header = "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">";
        $header .= "<thead>";
        $header .= "<tr bgcolor=\"#000000\" color=\"#FFFFFF\">";
        $header .= "<td width=\"" . WIDTH_COLUMN_NAME . "\"><b>" . get_string('lastname', 'mod_exammanagement') . "</b></td>";
        $header .= "<td width=\"" . WIDTH_COLUMN_FIRSTNAME . "\"><b>" . get_string('firstname', 'mod_exammanagement') . "</b></td>";
        $header .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\"><b>" .
            get_string('matrno', 'mod_exammanagement') . "</b></td>";
        $header .= "<td width=\"" . WIDTH_COLUMN_ROOM . "\" align=\"center\"><b>" .
            get_string('room', 'mod_exammanagement') . "</b></td>";
        $header .= "<td width=\"" . WIDTH_COLUMN_PLACE . "\" align=\"center\"><b>" .
            get_string('place', 'mod_exammanagement') . "</b></td>";
        $header .= "</tr>";
        $header .= "</thead>";

        return $header;
    }

    /**
     * Build the seating plan table.
     *
     * @param array $leftcol Content for the left column.
     * @param array $rightcol Content for the right column.
     *
     * @return string
     */
    public static function getseatingplantable($leftcol, $rightcol) {

        $fill = false;

        $table = "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">";
        $table .= "<thead>";
        $table .= "<tr bgcolor=\"#000000\" color=\"#FFFFFF\">";
        $table .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\"><b>" .
            get_string('matrno', 'mod_exammanagement') . "</b></td>";
        $table .= "<td width=\"" . WIDTH_COLUMN_ROOM . "\" align=\"center\"><b>" .
            get_string('room', 'mod_exammanagement') . "</b></td>";
        $table .= "<td width=\"" . WIDTH_COLUMN_PLACE . "\" align=\"center\"><b>" .
            get_string('place', 'mod_exammanagement') . "</b></td>";
        $table .= "<td width=\"" . WIDTH_COLUMN_MIDDLE . "\" bgcolor=\"#FFFFFF\"></td>";

        if (count($rightcol) > 0) {
            $table .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\"><b>" .
                get_string('matrno', 'mod_exammanagement') . "</b></td>";
            $table .= "<td width=\"" . WIDTH_COLUMN_ROOM . "\" align=\"center\"><b>" .
                get_string('room', 'mod_exammanagement') . "</b></td>";
            $table .= "<td width=\"" . WIDTH_COLUMN_PLACE . "\" align=\"center\"><b>" .
                get_string('place', 'mod_exammanagement') . "</b></td>";
        } else {
            $table .= "<td bgcolor=\"#FFFFFF\" width=\"" . (WIDTH_COLUMN_MATNO + WIDTH_COLUMN_ROOM + WIDTH_COLUMN_PLACE) .
                "\" colspan=\"3\"></td>";
        }

        $table .= "</tr>";
        $table .= "</thead>";

        for ($n = 0; $n < count($leftcol); $n++) {

            $table .= ($fill) ? "<tr bgcolor=\"#DDDDDD\">" : "<tr>";
            $table .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\">" . $leftcol[$n]["matrnr"] . "</td>";
            $table .= "<td width=\"" . WIDTH_COLUMN_ROOM . "\" align=\"center\">" . $leftcol[$n]["roomname"] . "</td>";
            $table .= "<td width=\"" . WIDTH_COLUMN_PLACE . "\" align=\"center\">" . $leftcol[$n]["place"] . "</td>";
            $table .= "<td width=\"" . WIDTH_COLUMN_MIDDLE . "\" bgcolor=\"#FFFFFF\"></td>";

            if ($n < count($rightcol)) {
                $table .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\">" . $rightcol[$n]["matrnr"] . "</td>";
                $table .= "<td width=\"" . WIDTH_COLUMN_ROOM . "\" align=\"center\">" . $rightcol[$n]["roomname"] . "</td>";
                $table .= "<td width=\"" . WIDTH_COLUMN_PLACE . "\" align=\"center\">" . $rightcol[$n]["place"] . "</td>";
            } else {
                $table .= "<td bgcolor=\"#FFFFFF\" width=\"" . (WIDTH_COLUMN_MATNO + WIDTH_COLUMN_ROOM + WIDTH_COLUMN_PLACE) .
                    "\" colspan=\"3\"></td>";
            }

            $table .= "</tr>";

            $fill = !$fill;
        }

        $table .= "</table>";

        return $table;
    }

    /**
     * Build the seating plan table.
     *
     * @param int $n The base number.
     *
     * @return string The adress
     */
    public static function calculatecelladdress($n) {
        if ($n <= 26) {
            return chr(64 + $n);
        } else if ($n <= 52) {
            return "A" . self::calculatecelladdress($n - 26);
        } else if ($n <= 78) {
            return "B" . self::calculatecelladdress($n - 52);
        } else if ($n <= 104) {
            return "C" . self::calculatecelladdress($n - 78);
        } else if ($n <= 130) {
            return "D" . self::calculatecelladdress($n - 104);
        } else if ($n <= 156) {
            return "E" . self::calculatecelladdress($n - 130);
        } else if ($n <= 192) {
            return "F" . self::calculatecelladdress($n - 156);
        } else if ($n <= 218) {
            return "G" . self::calculatecelladdress($n - 192);
        } else if ($n <= 244) {
            return "H" . self::calculatecelladdress($n - 218);
        } else if ($n <= 270) {
            return "I" . self::calculatecelladdress($n - 244);
        } else {
            return;
        }
    }


    /**
     * Return the excel column range.
     *
     * @param int $lower The lower value
     * @param int $upper The upper value
     */
    public static function excelcolumnrange($lower, $upper) {
        ++$upper;
        for ($i = $lower; $i !== $upper; ++$i) {
            yield $i;
        }
    }

    /**
     * Determine the opening state of a phase.
     *
     * @param object $moduleinstance The moduleinstance.
     * @param string $phase The phase
     *
     * @return bool
     */
    public static function checkphasecompletion($moduleinstance, $phase) {

        switch ($phase) {

            case "phase_one":
                if (self::getroomscount($moduleinstance) && isset($moduleinstance->examtime)
                    && self::getparticipantscount($moduleinstance) && self::gettasks($moduleinstance)) {

                    return true;
                } else {
                    return false;
                }
            case "phase_two":
                if (self::placesassigned($moduleinstance)
                    && (($moduleinstance->datetimevisible && $moduleinstance->roomvisible && $moduleinstance->placevisible)
                    || (isset($moduleinstance->examtime) && $moduleinstance->examtime < time()))) {

                    return true;
                } else {
                    return false;
                }
            case "phase_exam":
                if (isset($moduleinstance->examtime) && $moduleinstance->examtime < time()) {
                    return true;
                } else {
                    return false;
                }
            case "phase_three":
                if (self::getdatadeletiondate($moduleinstance)) {
                    return true;
                } else {
                    return false;
                }
            case "phase_four":
                if (isset($moduleinstance->examreviewtime) && isset($moduleinstance->examreviewroom)
                    && $moduleinstance->examreviewvisible) {

                    return true;
                } else {
                    return false;
                }
            case "phase_five":
                if (isset($moduleinstance->examreviewtime) && $moduleinstance->examreviewtime < time()) {
                    return true;
                } else {
                    return false;
                }
        }
    }

    /**
     * Determine the active phase.
     *
     * @param object $moduleinstance The moduleinstance.
     *
     * @return string
     */
    public static function determineactivephase($moduleinstance) {

        $phaseone = self::checkphasecompletion($moduleinstance, "phase_one");
        $phasetwo = self::checkphasecompletion($moduleinstance, "phase_two");
        $phasethree = self::checkphasecompletion($moduleinstance, "phase_three");
        $phasefour = self::checkphasecompletion($moduleinstance, "phase_four");
        $phasefive = self::checkphasecompletion($moduleinstance, "phase_five");

        $date = time();

        if (!$phaseone) {
            return 'phase_one';
        } else if (!$phasetwo) {
            return 'phase_two';
        } else if ($phasetwo && $moduleinstance->examtime > $date) {
            return 'phase_exam';
        } else if (!$phasethree && $moduleinstance->examtime < $date) {
            return 'phase_three';
        } else if ($phasethree && $moduleinstance->examtime < $date) {
            return 'phase_four';
        } else if ($phasefour && $moduleinstance->examreviewtime < $date) {
            return 'phase_four';
        }
    }
}
