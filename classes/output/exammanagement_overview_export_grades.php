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
 * Class containing data for exammanagement main page (mode export grades)
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
 * Class containing data for exammanagement_overview_export_grades
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exammanagement_overview_export_grades implements renderable, templatable {

    protected $cmid;
    protected $helptexticon;
    protected $additionalressourceslink;
    protected $participants;
    protected $bonuspointsentered;
    protected $gradingscale;
    protected $resultscount;
    protected $datadeletiondate;
    protected $deleted;
    protected $ldapavailable;

    /**
     * Construct this renderable.
     * @param int $courseid The course record for this page.
     */
    public function __construct($cmid, $helptexticon, $additionalressourceslink, $participants,
        $bonuspointsentered, $gradingscale, $resultscount, $datadeletiondate, $deleted, $ldapavailable) {

        $this->cmid = $cmid;
        $this->helptexticon = $helptexticon;
        $this->additionalressourceslink = $additionalressourceslink;
        $this->participants = $participants;
        $this->bonuspointsentered = $bonuspointsentered;
        $this->gradingscale = $gradingscale;
        $this->resultscount = $resultscount;
        $this->datadeletiondate = $datadeletiondate;
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
        $data->helptexticon = $this->helptexticon;
        $data->additionalressourceslink = $this->additionalressourceslink;
        $data->participants = $this->participants;
        $data->bonuspointsentered = $this->bonuspointsentered;
        $data->gradingscale = $this->gradingscale;
        $data->resultscount = $this->resultscount;
        $data->datadeletiondate = $this->datadeletiondate;
        $data->deleted = $this->deleted;
        $data->ldapavailable = $this->ldapavailable;
        return $data;
    }
}
