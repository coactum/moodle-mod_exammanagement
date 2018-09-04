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
    protected $firstphaseactive;
    protected $secondphaseactive;
    protected $examphaseactive;
    protected $thirdphaseactive;
    protected $fourthphaseactive;
    protected $hrexamtime;
    protected $textfield;
    protected $taskcount;
    protected $tasktotalpoints;
    protected $participants;
    protected $rooms;
    protected $roomnames;
    protected $stateofplacescorrect;
    protected $stateofplaceserror;
	protected $datetimevisible;
	protected $roomvisible;
  protected $placevisible;
  protected $gradingscale;
  protected $resultscount;

    /**
     * Construct this renderable.
     * @param int $courseid The course record for this page.
     */
    public function __construct($cmid, $firstphasecompleted, $secondphasecompleted, $examphasecompleted, $thirdphasecompleted, $fourthphasecompleted, $firstphaseactive, $secondphaseactive, $examphaseactive, $thirdphaseactive, $fourthphaseactive, $hrexamtime, $taskcount, $tasktotalpoints, $textfield, $participants, $rooms, $roomnames, $stateofplacescorrect, $stateofplaceserror, $datetimevisible, $roomvisible, $placevisible, $gradingscale, $resultscount) {
        $this->cmid = $cmid;
        $this->firstphasecompleted = $firstphasecompleted;
        $this->secondphasecompleted = $secondphasecompleted;
        $this->examphasecompleted = $examphasecompleted;
        $this->thirdphasecompleted = $thirdphasecompleted;
        $this->fourthphasecompleted = $fourthphasecompleted;
        $this->firstphaseactive = $firstphaseactive;
        $this->secondphaseactive = $secondphaseactive;
        $this->examphaseactive = $examphaseactive;
        $this->thirdphaseactive = $thirdphaseactive;
        $this->fourthphaseactive = $fourthphaseactive;
        $this->hrexamtime = $hrexamtime;
        $this->taskcount = $taskcount;
        $this->tasktotalpoints = $tasktotalpoints;
        $this->textfield = $textfield;
        $this->participants = $participants;
        $this->rooms = $rooms;
        $this->roomnames = $roomnames;
        $this->stateofplacescorrect = $stateofplacescorrect;
        $this->datetimevisible = $datetimevisible;
        $this->roomvisible = $roomvisible;
        $this->placevisible = $placevisible;
        $this->gradingscale = $gradingscale;
        $this->resultscount = $resultscount;
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
        $data->firstphaseactive = $this->firstphaseactive;
        $data->secondphaseactive = $this->secondphaseactive;
        $data->examphaseactive = $this->examphaseactive;
        $data->thirdphaseactive = $this->thirdphaseactive;
        $data->fourthphaseactive = $this->fourthphaseactive;
        $data->hrexamtime = $this->hrexamtime;
        $data->taskcount = $this->taskcount;
        $data->tasktotalpoints = $this->tasktotalpoints;
        $data->textfield = $this->textfield;
        $data->participants = $this->participants;
        $data->rooms = $this->rooms;
        $data->roomnames = $this->roomnames;
        $data->stateofplacescorrect = $this->stateofplacescorrect;
        $data->stateofplaceserror = $this->stateofplaceserror;
        $data->datetimevisible = $this->datetimevisible;
        $data->roomvisible = $this->roomvisible;
        $data->placevisible = $this->placevisible;
        $data->gradingscale = $this->gradingscale;
        $data->resultscount = $this->resultscount;
        return $data;
    }

}
