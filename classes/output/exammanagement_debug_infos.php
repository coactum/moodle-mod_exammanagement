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
class exammanagement_debug_infos implements renderable, templatable {

    protected $id;
    protected $cm;
    protected $course;
    protected $moduleinstance;

    /**
     * Construct this renderable.
     * @param int $... The course record for this page.
     */
    public function __construct($id, $cm, $course, $moduleinstance) {
        $this->id = $id;
        $this->cm = $cm;
        $this->course = $course;
        $this->moduleinstance = $moduleinstance;
        var_dump($id);
        var_dump($cm);
        var_dump($course);
        var_dump($moduleinstance);
    }
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer base.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->id = $this->id;
        $data->cm = $this->cm;
        $data->course = $this->course;
        $data->moduleinstance = $this->moduleinstance;
        return $data;
    }

}
