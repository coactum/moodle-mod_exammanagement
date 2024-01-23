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
 * Class containing all user specific methods for exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\ldap\ldapmanager;
use core\notification;
use moodle_url;

class userhandler {

    protected $id;
    protected $e;
    protected $exammanagement;

    private function __construct($id, $e, $exammanagement) {
        $this->id = $id;
        $this->e = $e;
        $this->exammanagement = $exammanagement;
    }

    // Singleton class.
    public static function getinstance($id, $e, $exammanagement) {

        static $inst = null;
        if ($inst === null) {
            $inst = new userhandler($id, $e, $exammanagement);
        }
        return $inst;

    }

    // Get array with all requested exam participants.
    public function getexamparticipants($participantsmode, $requestedattributes, $sortorder = 'name',
        $pagination = false, $activepage = null, $conditions = false) {

        global $DB;

        $exammanagementinstance = exammanagementinstance::getInstance($this->id, $this->e);

        $allparticipants = [];

        // For pagination.
        if ($pagination && isset($activepage)) {
            $limitfrom = $exammanagementinstance->pagecount * ($activepage - 1);
            $limitnum = $exammanagementinstance->pagecount;
        } else {
            $limitfrom = 0;
            $limitnum = 0;
        }

        if ($participantsmode['mode'] === 'all') {
            $rs = $DB->get_recordset('exammanagement_participants', ['exammanagement' => $this->exammanagement]);
        } else if ($participantsmode['mode'] === 'moodle') {
            $rs = $DB->get_recordset('exammanagement_participants',
                ['exammanagement' => $this->exammanagement, 'login' => null]);
        } else if ($participantsmode['mode'] === 'nonmoodle') {
            $rs = $DB->get_recordset('exammanagement_participants',
                ['exammanagement' => $this->exammanagement, 'moodleuserid' => null]);
        } else if ($participantsmode['mode'] === 'room') {
            $rs = $DB->get_recordset('exammanagement_participants',
                ['exammanagement' => $this->exammanagement, 'roomid' => $participantsmode['id']]);
        } else if ($participantsmode['mode'] === 'header') {
            $rs = $DB->get_recordset('exammanagement_participants',
                ['exammanagement' => $this->exammanagement, 'headerid' => $participantsmode['id']]);
        } else if ($participantsmode['mode'] === 'resultsafterexamreview') {
            $examreviewtime = $exammanagementinstance->getExamReviewTime();

            $select = "exammanagement =".$this->exammanagement;
            $select .= " AND exampoints IS NOT NULL";
            $select .= " AND examstate IS NOT NULL";
            $select .= " AND timeresultsentered IS NOT NULL";
            $select .= " AND timeresultsentered >=" . $examreviewtime;

            $rs = $DB->get_recordset_select('exammanagement_participants', $select, null);

        } else if ($participantsmode['mode'] === 'no_seats_assigned') {
            $select = "exammanagement =".$this->exammanagement;
            $select .= " AND roomid IS NULL";
            $select .= " AND roomname IS NULL";
            $select .= " AND place IS NULL";

            $rs = $DB->get_recordset_select('exammanagement_participants', $select, null);

        } else {
            return false;
        }

        if ($rs->valid()) {

            if (in_array('profile', $requestedattributes) || in_array('groups', $requestedattributes)) {
                $courseid = $exammanagementinstance->getCourse()->id;
            }

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

                    $moodleuser = $this->getmoodleuser($record->moodleuserid);

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
                            $usergroups = groups_get_user_groups($exammanagementinstance->getCourse()->id, $record->moodleuserid);
                            $groupnames = false;

                            foreach ($usergroups as $key => $value) {
                                if ($value) {
                                    $moodleobj = Moodle::getInstance($this->id, $this->e);

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
                        $record->firstname = get_string('deleted_user', 'mod_exammanagement',
                            ['systemname' => $exammanagementinstance->getMoodleSystemName()]);
                        $record->lastname = get_string('deleted_user', 'mod_exammanagement',
                            ['systemname' => $exammanagementinstance->getMoodleSystemName()]);

                        if (in_array('profile', $requestedattributes)) {
                            $record->profile = get_string('deleted_user', 'mod_exammanagement',
                                ['systemname' => $exammanagementinstance->getMoodleSystemName()]);
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

    // Get ids of all participants enrolled in the course.
    public function getcourseparticipantsids() {

        $exammanagementinstance = exammanagementinstance::getInstance($this->id, $this->e);

        $courseparticipants = get_enrolled_users($exammanagementinstance->getModulecontext(), 'mod/exammanagement:takeexams');
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

    // Get single exam participant.
    public function getexamparticipant($moodleuserid, $userlogin = false, $id = false) {
        global $DB;

        if ($moodleuserid !== false && $moodleuserid !== null) {
            $participants = $DB->get_record('exammanagement_participants',
                ['exammanagement' => $this->exammanagement, 'moodleuserid' => $moodleuserid]);
        } else if ($userlogin !== false && $userlogin !== null) {
            $participants = $DB->get_record('exammanagement_participants',
                ['exammanagement' => $this->exammanagement, 'login' => $userlogin]);
        } else if ($id !== false && $id !== null) {
            $participants = $DB->get_record('exammanagement_participants',
                ['exammanagement' => $this->exammanagement, 'id' => $id]);
        }

        if ($participants) {
            return $participants;
        } else {
            return false;
        }
    }

    // Get single moodle user.
    public function getmoodleuser($userid) {

        global $DB;

        $user = $DB->get_record('user', ['id' => $userid]);

        if ($user) {
            return $user;
        } else {
            return false;
        }
    }

    // Get mail adresses of all nonmoodle users.
    public function getnonemoodleparticipantsemailadresses() {

        global $DB;

        $select = "exammanagement =" . $this->exammanagement;
        $select .= " AND moodleuserid IS NULL";

        $nonemoodleparticipantsemailadresses = $DB->get_fieldset_select('exammanagement_participants', 'email', $select);

        if (!empty($nonemoodleparticipantsemailadresses)) {
            return $nonemoodleparticipantsemailadresses;
        } else {
            return false;
        }
    }

    // Filter checked participants from form.

    public function filtercheckedparticipants($form) {

        $form = get_object_vars($form);

        $allparicipants = [];

        if (isset($form["participants"])) {
            $allparicipants = $form["participants"];
        }
        $participants = [];

        if ($allparicipants) {
            foreach ($allparicipants as $key => $value) {
                if ($value == 1) {
                    array_push($participants, $key);
                }
            }
        }

        if ($participants) {
            return $participants;
        } else {
            return false;
        }
    }

    public function filtercheckeddeletedparticipants($form) {

        $form = get_object_vars($form);

        $allparicipants = [];

        if (isset($form["deletedparticipants"])) {
            $allparicipants = $form["deletedparticipants"];
        }

        $participants = [];

        if ($allparicipants) {
            foreach ($allparicipants as $key => $value) {
                if ($value == 1) {
                    array_push($participants, $key);
                }
            }
        }

        if ($participants) {
            return $participants;
        } else {
            return false;
        }
    }

    // Delete participants.

    public function deleteparticipant($userid, $login = false) {

        global $DB;

        if ($userid !== false &&
            $DB->record_exists('exammanagement_participants',
                ['exammanagement' => $this->exammanagement, 'moodleuserid' => $userid])) {

            $DB->delete_records('exammanagement_participants',
                ['exammanagement' => $this->exammanagement, 'moodleuserid' => $userid]);
        } else if ($DB->record_exists('exammanagement_participants',
            ['exammanagement' => $this->exammanagement, 'login' => $login])) {

            $DB->delete_records('exammanagement_participants',
                ['exammanagement' => $this->exammanagement, 'login' => $login]);
        }
    }

    public function deleteallparticipants() {

        global $DB;

        $exammanagementinstance = exammanagementinstance::getInstance($this->id, $this->e);

        $exammanagementinstance->moduleinstance->importfileheaders = null;
        $exammanagementinstance->moduleinstance->assignmentmode = null;
        $DB->update_record("exammanagement", $exammanagementinstance->moduleinstance);

        if ($DB->record_exists('exammanagement_participants', ['exammanagement' => $this->exammanagement])) {
            $DB->delete_records('exammanagement_participants', ['exammanagement' => $this->exammanagement]);
        }
    }

    public function deletetempparticipants() {

        global $DB;

        $exammanagementinstance = exammanagementinstance::getInstance($this->id, $this->e);

        $exammanagementinstance->moduleinstance->tempimportfileheader = null;

        $DB->update_record("exammanagement", $exammanagementinstance->moduleinstance);

        if ($DB->record_exists('exammanagement_temp_part', ['exammanagement' => $this->exammanagement])) {
            $DB->delete_records('exammanagement_temp_part', ['exammanagement' => $this->exammanagement]);
        } else {
            return false;
        }
    }

    // Results.
    public function participanthasresults($participant) {

        if ($participant->exampoints && $participant->examstate) {
            return true;
        } else {
            return false;
        }
    }

    public function getexamstate($participant) {

        if (is_null($participant->examstate)) {
            $state = null;
        } else {
            $state = json_decode($participant->examstate);
        }

        if ($state !== null) {
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

    public function calculatepoints($participant, $withbonus=false) {
        $points = 0;

        if (is_null($participant->exampoints)) {
            $allpoints = null;
        } else {
            $allpoints = json_decode($participant->exampoints);
        }

        if ($allpoints != null) {

            $examstate = $this->getexamstate($participant);

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
     * Calculates result grade of participant based on points
     *
     * @param Points   $points  Points (or special exam state) that should be converted to result grade
     * @param Bonus    $bonussteps  Bonus grade steps if grade should be calculated with bonus (default: false)
     *
     * @return Result  $result
     */

    public function calculateresultgrade($points, $bonussteps = false) {

        $exammanagementinstance = exammanagementinstance::getInstance($this->id, $this->e);

        $gradingscale = $exammanagementinstance->getGradingscale();

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

    // Checks.
    public function checkifalreadyparticipant($id, $login = false) {

        global $DB;

        if ($id) {
            return $DB->record_exists('exammanagement_participants',
                ['exammanagement' => $this->exammanagement, 'moodleuserid' => $id]);
        } else if ($login) {
            return $DB->record_exists('exammanagement_participants',
                ['exammanagement' => $this->exammanagement, 'login' => $id]);
        }
    }

    public function checkifvalidmatrnr($mnr) {
        if (!preg_match("/^\d+$/", $mnr)) {
            return false;
        }

        $first = substr($mnr, 0, 1);

        if (strlen($mnr) == 7) {
            return true;
        }
    }

    // Counts.
    public function getparticipantscount($mode = 'all', $id = false) {

        global $DB;

        $select = "exammanagement =".$this->exammanagement;

        if ($mode == 'moodle') {
            $select .= " AND moodleuserid IS NOT NULL";
        } else if ($mode == 'nonmoodle') {
            $select .= " AND moodleuserid IS NULL";
        } else if ($mode == 'room' && $id) {
            $select .= " AND roomid = '" . $id . "'";
        }

        $participantscount = $DB->count_records_select('exammanagement_participants', $select);

        if ($participantscount) {
            return $participantscount;
        } else {
            return false;
        }
    }

    public function getenteredbonuscount($mode = 'both') {

        global $DB;

        $select = "exammanagement =".$this->exammanagement;

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

    public function getenteredresultscount($timestamp = false) {

        global $DB;

        $select = "exammanagement =".$this->exammanagement;
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
}
