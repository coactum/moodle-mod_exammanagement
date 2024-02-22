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
 * Class containing data for exammanagement main page
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_exammanagement\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Class containing data for exammanagement_overview
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exammanagement_overview implements renderable, templatable {

    /** @var int */
    protected $cmid;
    /** @var object */
    protected $phases;
    /** @var string */
    protected $helptexticon;
    /** @var string */
    protected $additionalressourceslink;
    /** @var string */
    protected $examtime;
    /** @var int */
    protected $taskcount;
    /** @var string */
    protected $tasktotalpoints;
    /** @var string */
    protected $textfield;
    /** @var int */
    protected $participants;
    /** @var int */
    protected $rooms;
    /** @var string */
    protected $roomnames;
    /** @var int */
    protected $totalseats;
    /** @var bool */
    protected $placesassigned;
    /** @var bool */
    protected $allplacesassigned;
    /** @var int */
    protected $assignedplacescount;
    /** @var bool */
    protected $datetimevisible;
    /** @var bool */
    protected $roomvisible;
    /** @var bool */
    protected $placevisible;
    /** @var int */
    protected $bonuscount;
    /** @var int */
    protected $bonuspointsentered;
    /** @var bool */
    protected $bonusvisible;
    /** @var object */
    protected $gradingscale;
    /** @var int */
    protected $resultscount;
    /** @var bool */
    protected $resultvisible;
    /** @var string */
    protected $datadeletiondate;
    /** @var string */
    protected $examreviewtime;
    /** @var string */
    protected $examreviewroom;
    /** @var bool */
    protected $examreviewvisible;
    /** @var int */
    protected $resultsenteredafterexamreview;
    /** @var bool */
    protected $deleted;
    /** @var bool */
    protected $ldapavailable;

    /**
     * Construct this renderable.
     * @param int $cmid The course module id
     * @param object $phases The phases
     * @param string $helptexticon The help text icon
     * @param string $additionalressourceslink The link to the additional ressources
     * @param string $examtime The time of the exam
     * @param int $taskcount The number of tasks
     * @param string $tasktotalpoints The total amount of points
     * @param string $textfield The shortened textfield content
     * @param int $participants The number of participants
     * @param int $rooms The number of rooms
     * @param string $roomnames The room names
     * @param int $totalseats The total seats count
     * @param bool $placesassigned If places are assigned
     * @param bool $allplacesassigned If all places are assigned
     * @param int $assignedplacescount The number of places assigned
     * @param bool $datetimevisible If examdate and time are visible to participants
     * @param bool $roomvisible If exam rooms are visible to participants
     * @param bool $placevisible If places are visible to participants
     * @param int $bonuscount The number of participants that have a bonus entered
     * @param int $bonuspointsentered The number of bonus points (not bonus steps) entered
     * @param bool $bonusvisible If bonus is visible to participants
     * @param object $gradingscale The grading scale
     * @param int $resultscount Number of results entered
     * @param bool $resultvisible If results are visible to participants
     * @param string $datadeletiondate The date when the data will be deleted
     * @param string $examreviewtime The time of the exam review
     * @param string $examreviewroom The room for the exam review
     * @param bool $examreviewvisible If exam review information are visible to participants
     * @param int $resultsenteredafterexamreview The number of results entered after the exam review
     * @param bool $deleted If the data is already deleted
     * @param bool $ldapavailable If ldap is available
     */
    public function __construct($cmid, $phases, $helptexticon, $additionalressourceslink, $examtime, $taskcount,
        $tasktotalpoints, $textfield, $participants, $rooms, $roomnames, $totalseats, $placesassigned, $allplacesassigned,
        $assignedplacescount, $datetimevisible, $roomvisible, $placevisible, $bonuscount, $bonuspointsentered, $bonusvisible,
        $gradingscale, $resultscount, $resultvisible, $datadeletiondate, $examreviewtime, $examreviewroom, $examreviewvisible,
        $resultsenteredafterexamreview, $deleted, $ldapavailable) {

        $this->cmid = $cmid;
        $this->phases = $phases;
        $this->helptexticon = $helptexticon;
        $this->additionalressourceslink = $additionalressourceslink;
        $this->examtime = $examtime;
        $this->taskcount = $taskcount;
        $this->tasktotalpoints = $tasktotalpoints;
        $this->textfield = $textfield;
        $this->participants = $participants;
        $this->rooms = $rooms;
        $this->roomnames = $roomnames;
        $this->totalseats = $totalseats;
        $this->placesassigned = $placesassigned;
        $this->allplacesassigned = $allplacesassigned;
        $this->assignedplacescount = $assignedplacescount;
        $this->datetimevisible = $datetimevisible;
        $this->roomvisible = $roomvisible;
        $this->placevisible = $placevisible;
        $this->bonuscount = $bonuscount;
        $this->bonuspointsentered = $bonuspointsentered;
        $this->bonusvisible = $bonusvisible;
        $this->gradingscale = $gradingscale;
        $this->resultscount = $resultscount;
        $this->resultvisible = $resultvisible;
        $this->datadeletiondate = $datadeletiondate;
        $this->examreviewtime = $examreviewtime;
        $this->examreviewroom = $examreviewroom;
        $this->examreviewvisible = $examreviewvisible;
        $this->resultsenteredafterexamreview = $resultsenteredafterexamreview;
        $this->deleted = $deleted;
        $this->ldapavailable = $ldapavailable;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer base.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->cmid = $this->cmid;
        $data->phases = $this->phases;
        $data->helptexticon = $this->helptexticon;
        $data->additionalressourceslink = $this->additionalressourceslink;
        $data->examtime = $this->examtime;
        $data->taskcount = $this->taskcount;
        $data->tasktotalpoints = $this->tasktotalpoints;
        $data->textfield = $this->textfield;
        $data->participants = $this->participants;
        $data->rooms = $this->rooms;
        $data->roomnames = $this->roomnames;
        $data->totalseats = $this->totalseats;
        $data->placesassigned = $this->placesassigned;
        $data->allplacesassigned = $this->allplacesassigned;
        $data->assignedplacescount = $this->assignedplacescount;
        $data->datetimevisible = $this->datetimevisible;
        $data->roomvisible = $this->roomvisible;
        $data->placevisible = $this->placevisible;
        $data->bonuscount = $this->bonuscount;
        $data->bonuspointsentered = $this->bonuspointsentered;
        $data->bonusvisible = $this->bonusvisible;
        $data->gradingscale = $this->gradingscale;
        $data->resultscount = $this->resultscount;
        $data->resultvisible = $this->resultvisible;
        $data->datadeletiondate = $this->datadeletiondate;
        $data->examreviewtime = $this->examreviewtime;
        $data->examreviewroom = $this->examreviewroom;
        $data->examreviewvisible = $this->examreviewvisible;
        $data->resultsenteredafterexamreview = $this->resultsenteredafterexamreview;
        $data->deleted = $this->deleted;
        $data->ldapavailable = $this->ldapavailable;
        $data->sesskey = sesskey();

        return $data;
    }
}
