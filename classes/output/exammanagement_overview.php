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
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_exammanagement\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Class containing data for exammanagement_overview
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exammanagement_overview implements renderable, templatable {

    protected $cmid;
    protected $firstphasecompleted;
    protected $secondphasecompleted;
    protected $examphasecompleted;
    protected $thirdphasecompleted;
    protected $fourthphasecompleted;
    protected $fifthphasecompleted;
    protected $firstphaseactive;
    protected $secondphaseactive;
    protected $examphaseactive;
    protected $thirdphaseactive;
    protected $fourthphaseactive;
    protected $fifthphaseactive;
    protected $hrexamtime;
    protected $textfield;
    protected $taskcount;
    protected $tasktotalpoints;
    protected $participants;
    protected $rooms;
    protected $roomnames;
    protected $totalseats;
    protected $allplacesassigned;
    protected $assignedplacescount;
	protected $datetimevisible;
	protected $roomvisible;
  protected $placevisible;
  protected $bonuscount;
  protected $gradingscale;
  protected $resultscount;
  protected $datadeletiondate;
  protected $examreviewtime;
  protected $examreviewroom;
  protected $examreviewvisible;
  protected $resultsenteredafterexamreview;
  protected $deleted;

    /**
     * Construct this renderable.
     * @param int $courseid The course record for this page.
     */
    public function __construct($cmid, $firstphasecompleted, $secondphasecompleted, $examphasecompleted, $thirdphasecompleted, $fourthphasecompleted, $fifthphasecompleted, $firstphaseactive, $secondphaseactive, $examphaseactive, $thirdphaseactive, $fourthphaseactive, $fifthphaseactive, $hrexamtime, $taskcount, $tasktotalpoints, $textfield, $participants, $rooms, $roomnames, $totalseats, $allplacesassigned, $assignedplacescount, $datetimevisible, $roomvisible, $placevisible, $bonuscount, $gradingscale, $resultscount, $datadeletiondate, $examreviewtime, $examreviewroom, $examreviewvisible, $resultsenteredafterexamreview, $deleted) {
        $this->cmid = $cmid;
        $this->firstphasecompleted = $firstphasecompleted;
        $this->secondphasecompleted = $secondphasecompleted;
        $this->examphasecompleted = $examphasecompleted;
        $this->thirdphasecompleted = $thirdphasecompleted;
        $this->fourthphasecompleted = $fourthphasecompleted;
        $this->fifthphasecompleted = $fifthphasecompleted;
        $this->firstphaseactive = $firstphaseactive;
        $this->secondphaseactive = $secondphaseactive;
        $this->examphaseactive = $examphaseactive;
        $this->thirdphaseactive = $thirdphaseactive;
        $this->fourthphaseactive = $fourthphaseactive;
        $this->fifthphaseactive = $fifthphaseactive;
        $this->hrexamtime = $hrexamtime;
        $this->taskcount = $taskcount;
        $this->tasktotalpoints = $tasktotalpoints;
        $this->textfield = $textfield;
        $this->participants = $participants;
        $this->rooms = $rooms;
        $this->roomnames = $roomnames;
        $this->totalseats = $totalseats;
        $this->allplacesassigned = $allplacesassigned;
        $this->assignedplacescount = $assignedplacescount;
        $this->datetimevisible = $datetimevisible;
        $this->roomvisible = $roomvisible;
        $this->placevisible = $placevisible;
        $this->bonuscount = $bonuscount;
        $this->gradingscale = $gradingscale;
        $this->resultscount = $resultscount;
        $this->datadeletiondate = $datadeletiondate;
        $this->examreviewtime = $examreviewtime;
        $this->examreviewroom = $examreviewroom;
        $this->examreviewvisible = $examreviewvisible;
        $this->resultsenteredafterexamreview = $resultsenteredafterexamreview;
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
        $data->firstphasecompleted = $this->firstphasecompleted;
        $data->secondphasecompleted = $this->secondphasecompleted;
        $data->examphasecompleted = $this->examphasecompleted;
        $data->thirdphasecompleted = $this->thirdphasecompleted;
        $data->fourthphasecompleted = $this->fourthphasecompleted;
        $data->fifthphasecompleted = $this->fifthphasecompleted;
        $data->firstphaseactive = $this->firstphaseactive;
        $data->secondphaseactive = $this->secondphaseactive;
        $data->examphaseactive = $this->examphaseactive;
        $data->thirdphaseactive = $this->thirdphaseactive;
        $data->fourthphaseactive = $this->fourthphaseactive;
        $data->fifthphaseactive = $this->fifthphaseactive;
        $data->hrexamtime = $this->hrexamtime;
        $data->taskcount = $this->taskcount;
        $data->tasktotalpoints = $this->tasktotalpoints;
        $data->textfield = $this->textfield;
        $data->participants = $this->participants;
        $data->rooms = $this->rooms;
        $data->roomnames = $this->roomnames;
        $data->totalseats = $this->totalseats;
        $data->allplacesassigned = $this->allplacesassigned;
        $data->assignedplacescount = $this->assignedplacescount;
        $data->datetimevisible = $this->datetimevisible;
        $data->roomvisible = $this->roomvisible;
        $data->placevisible = $this->placevisible;
        $data->bonuscount = $this->bonuscount;
        $data->gradingscale = $this->gradingscale;
        $data->resultscount = $this->resultscount;
        $data->datadeletiondate = $this->datadeletiondate;
        $data->examreviewtime = $this->examreviewtime;
        $data->examreviewroom = $this->examreviewroom;
        $data->examreviewvisible = $this->examreviewvisible;
        $data->resultsenteredafterexamreview = $this->resultsenteredafterexamreview;
        $data->deleted = $this->deleted;
        return $data;
    }

}
