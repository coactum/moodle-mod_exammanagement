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
 * Class containing data for exammanagement main page (mode export grades).
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
 * Class containing data for exammanagement_overview_export_grades.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exammanagement_overview_export_grades implements renderable, templatable {

    /** @var int */
    protected $cmid;
    /** @var string */
    protected $helptexticon;
    /** @var string */
    protected $additionalressourceslink;
    /** @var int */
    protected $participants;
    /** @var int */
    protected $bonuspointsentered;
    /** @var object */
    protected $gradingscale;
    /** @var bool */
    protected $resultscount;
    /** @var string */
    protected $datadeletiondate;
    /** @var bool */
    protected $deleted;
    /** @var bool */
    protected $ldapavailable;

    /**
     * Construct this renderable.
     * @param int $cmid The course module id
     * @param string $helptexticon The help text icon
     * @param string $additionalressourceslink The link to the additional ressources
     * @param int $participants The number of participants
     * @param int $bonuspointsentered The number of participants that have bonus points entered
     * @param object $gradingscale The grading scale
     * @param bool $resultscount If results are entered
     * @param string $datadeletiondate The date when the data will be deleted
     * @param bool $deleted If the data is already deleted
     * @param bool $ldapavailable If ldap is available
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
        $data->sesskey = sesskey();

        return $data;
    }
}
