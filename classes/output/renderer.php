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
 * Renderer class for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;
use renderable;

/**
 * Renderer class for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

     /**
     * Defer to template.
     *
     * @param exammanagement_overview $page
     *
     * @return string html for the page
     */
    public function render_exammanagement_overview(exammanagement_overview $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('mod_exammanagement/exammanagement_overview', $data);
    }
	public function render_exammanagement_debug_infos(exammanagement_debug_infos $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('mod_exammanagement/exammanagement_debug_infos', array());
    }
    public function render_quick_test_page() {
        return parent::render_from_template('mod_exammanagement/quick_test_page', array());
    }

}