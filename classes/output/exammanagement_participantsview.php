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
class exammanagement_participantsview implements renderable, templatable {

    protected $cmid;
    protected $isparticipant;
    protected $examdate;
    protected $examtime;
	protected $datetimevisible;
	protected $roomplacevisible;

    /**
     * Construct this renderable.
     * @param int $courseid The course record for this page.
     */
    public function __construct($cmid, $isparticipant, $examdate, $examtime, $datetimevisible, $room, $place, $roomplacevisible) {
        $this->cmid = $cmid;
        $this->isparticipant = $isparticipant;
        $this->examdate = $examdate;
        $this->examtime = $examtime;
        $this->datetimevisible = $datetimevisible;
        $this->room = $room;
        $this->place = $place;
        $this->roomplacevisible = $roomplacevisible;
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
        $data->datetimevisible = $this->datetimevisible;
        $data->room = $this->room;
        $data->place = $this->place;
        $data->roomplacevisible = $this->roomplacevisible;
        var_dump($data);
        return $data;
    }

}
