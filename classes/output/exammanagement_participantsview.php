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
 * Class containing data for exammanagement main page for participants
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
class exammanagement_participantsview implements renderable, templatable {

    /** @var int */
    protected $cmid;
    /** @var bool */
    protected $isparticipant;
    /** @var string */
    protected $examdate;
    /** @var string */
    protected $examtime;
    /** @var string */
    protected $room;
    /** @var string */
    protected $place;
    /** @var string */
    protected $textfield;
    /** @var string */
    protected $bonussteps;
    /** @var string */
    protected $bonuspoints;
    /** @var string */
    protected $examstate;
    /** @var string */
    protected $totalpoints;
    /** @var string */
    protected $tasktotalpoints;
    /** @var bool */
    protected $totalpointswithbonus;
    /** @var string */
    protected $examreviewtime;
    /** @var string */
    protected $examreviewroom;
    /** @var bool */
    protected $deleted;

    /**
     * Construct this renderable.
     * @param int $cmid The course module id
     * @param bool $isparticipant If user is participant
     * @param string $examdate The exam date
     * @param string $examtime The time of the exam
     * @param string $room The room
     * @param string $place The place
     * @param string $textfield The shortened textfield content
     * @param string $bonussteps The number of participants that have a bonus entered
     * @param string $bonuspoints The number of bonus points (not bonus steps) entered
     * @param string $examstate State of the exam
     * @param string $totalpoints The total points
     * @param string $tasktotalpoints Number of total points for tasks
     * @param bool $totalpointswithbonus If results are visible to participants
     * @param string $examreviewtime The time of the exam review
     * @param string $examreviewroom The room for the exam review
     * @param bool $deleted If the data is already deleted
     */
    public function __construct($cmid, $isparticipant, $examdate, $examtime, $room,
        $place, $textfield, $bonussteps, $bonuspoints, $examstate, $totalpoints,
        $tasktotalpoints, $totalpointswithbonus, $examreviewtime, $examreviewroom, $deleted) {

        $this->cmid = $cmid;
        $this->isparticipant = $isparticipant;
        $this->examdate = $examdate;
        $this->examtime = $examtime;
        $this->room = $room;
        $this->place = $place;
        $this->textfield = $textfield;
        $this->bonussteps = $bonussteps;
        $this->bonuspoints = $bonuspoints;
        $this->examstate = $examstate;
        $this->totalpoints = $totalpoints;
        $this->tasktotalpoints = $tasktotalpoints;
        $this->totalpointswithbonus = $totalpointswithbonus;
        $this->examreviewtime = $examreviewtime;
        $this->examreviewroom = $examreviewroom;
        $this->deleted = $deleted;
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
        $data->isparticipant = $this->isparticipant;
        $data->examdate = $this->examdate;
        $data->examtime = $this->examtime;
        $data->room = $this->room;
        $data->place = $this->place;
        $data->textfield = $this->textfield;
        $data->bonussteps = $this->bonussteps;
        $data->bonuspoints = $this->bonuspoints;
        $data->examstate = $this->examstate;
        $data->totalpoints = $this->totalpoints;
        $data->tasktotalpoints = $this->tasktotalpoints;
        $data->totalpointswithbonus = $this->totalpointswithbonus;
        $data->examreviewtime = $this->examreviewtime;
        $data->examreviewroom = $this->examreviewroom;
        $data->deleted = $this->deleted;
        return $data;
    }
}
