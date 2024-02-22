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

use mod_exammanagement\local\helper;

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
    /** @var moodle_url */
    protected $url;
    /** @var array */
    protected $allitems;
    /** @var int */
    protected $itemscount;
    /** @var string */
    protected $activepage;

    /**
     * Construct this renderable.
     * @param int $cmid The course module id
     * @param moodle_url $url The moodle url
     * @param array $allitems The allitems for the pagebar.
     * @param int $itemscount The count of the items on the current page
     * @param string $activepage The active page.
     */
    public function __construct($cmid, $url, $allitems, $itemscount, $activepage) {

        $this->cmid = $cmid;
        $this->url = $url->__ToString();
        $this->allitems = $allitems;
        $this->itemscount = $itemscount;
        $this->activepage = $activepage;
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
        $data->sesskey = sesskey();

        // Set pagebar.
        $pagecount = helper::getpagecount();

        $itemsgroupedbypage = array_chunk($this->allitems, $pagecount, true);
        array_unshift($itemsgroupedbypage, "temp");
        unset($itemsgroupedbypage[0]);

        if (isset($itemsgroupedbypage[2])) {
            $pagebar = [];
            foreach ($itemsgroupedbypage as $pagenr => $itemgroup) {
                $obj = new stdClass();

                if (isset($itemgroup[array_key_first($itemgroup)]->name)) {
                    $first = mb_substr($itemgroup[array_key_first($itemgroup)]->name, 0, 1, 'utf-8');
                    $last = mb_substr($itemgroup[array_key_last($itemgroup)]->name, 0, 1, 'utf-8');
                } else if (isset($itemgroup[array_key_first($itemgroup)]->lastname)) {
                    $first = mb_substr($itemgroup[array_key_first($itemgroup)]->lastname, 0, 1, 'utf-8');
                    $last = mb_substr($itemgroup[array_key_last($itemgroup)]->lastname, 0, 1, 'utf-8');
                }

                $obj->nr = $pagenr;

                if ($first && $last && ($first !== $last)) {
                    $displaystr = $first . '-' . $last;
                } else if ($first && $last && ($first == $last)) {
                    $displaystr = $first;
                } else {
                    $displaystr = $pagenr;
                }

                if ($pagenr == $this->activepage) {
                    $obj->display = '<strong>' . $displaystr . '</strong>';
                } else {
                    $obj->display = $displaystr;
                }

                array_push($pagebar, $obj);
            }

            $data->pagebar = $pagebar;
        } else {
            $data->pagebar = false;
        }

        // Set page count options.
        $pagecountoptions = [5, 10, 100, 1000];

        foreach ($pagecountoptions as $key => $number) {
            $obj = new stdClass();

            if ($number == $pagecount) {
                $obj->display = '<strong>' . $number . '</strong>';
                $obj->nr = $number;
            } else {
                $obj->display = $number;
                $obj->nr = $number;
            }

            $pagecountoptions[$key] = $obj;
        }

        $data->pagecountoptions = $pagecountoptions;

        $data->itemscount = $this->itemscount;
        $data->allitemscount = count($this->allitems);

        return $data;
    }
}
