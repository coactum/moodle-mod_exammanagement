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
    protected $thirdphasecompleted;
    protected $fourthphasecompleted;
    protected $day;
    protected $month;
    protected $year;
    protected $hour;
    protected $minute;


    /**
     * Construct this renderable.
     * @param int $courseid The course record for this page.
     */
    public function __construct($cmid, $firstphasecompleted, $secondphasecompleted, $thirdphasecompleted, $fourthphasecompleted, $day, $month, $year, $hour, $minute) {
        $this->cmid = $cmid;
        $this->firstphasecompleted = json_encode($firstphasecompleted);
        $this->secondphasecompleted = $secondphasecompleted;
        $this->thirdphasecompleted = $thirdphasecompleted;
        $this->fourthphasecompleted = $fourthphasecompleted;
        $this->day = $day;
        $this->month = $month;
        $this->year = $year;
        $this->hour = $hour;
        $this->minute = $minute;
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
        $data->thirdphasecompleted = $this->thirdphasecompleted;
        $data->fourthphasecompleted = $this->fourthphasecompleted;
        $data->day = $this->day;
        $data->month = $this->month;
        $data->year = $this->year;
        $data->hour = $this->hour;
        $data->minute = $this->minute;
        var_dump($data->firstphasecompleted.' renderer');
        return $data;
    }

}
