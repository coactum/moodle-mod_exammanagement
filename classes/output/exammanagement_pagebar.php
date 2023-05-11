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
 * Class containing data for the pagebar for exammanagement forms
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
 * Class containing data for the pagebar for exammanagement forms
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exammanagement_pagebar implements renderable, templatable {
    /** @var int */
    protected $cmid;
    /** @var string */
    protected $url;
    /** @var int */
    protected $sesskey;
    /** @var object */
    protected $pagebar;
    /** @var object */
    protected $pagecountoptions;
    /** @var int */
    protected $itemscount;
    /** @var int */
    protected $allitemscount;

    /**
     * Construct this renderable.
     * @param int $cmid The course module id
     * @param int $url The url
     * @param int $sesskey The session key
     * @param object $pagebar The object for constructing the pagebar
     * @param object $pagecountoptions The object with the pagecount options
     * @param int $itemscount The count of the displayed items
     * @param int $allitemscount The count of all items
     */
    public function __construct($cmid, $url, $sesskey, $pagebar, $pagecountoptions, $itemscount, $allitemscount) {

        $this->cmid = $cmid;
        $this->url = $url;
        $this->sesskey = $sesskey;
        $this->pagebar = $pagebar;
        $this->pagecountoptions = $pagecountoptions;
        $this->itemscount = $itemscount;
        $this->allitemscount = $allitemscount;
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
        $data->url = $this->url;
        $data->sesskey = $this->sesskey;
        $data->pagebar = $this->pagebar;
        $data->pagecountoptions = $this->pagecountoptions;
        $data->itemscount = $this->itemscount;
        $data->allitemscount = $this->allitemscount;
        return $data;
    }
}
