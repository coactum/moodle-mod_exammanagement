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
 * Helper utilities for the module.
 *
 * @package   mod_exammanagement
 * @copyright 2024 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_exammanagement\local;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Custom read filter for phpspreadsheet.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class readfilter implements IReadFilter {

    /** @var array */
    private $columns = [];

    /**
     * Constructor.
     * @param int $tocolumn The to column
     */
    public function __construct($tocolumn) {
        foreach (helper::excelcolumnrange('A', $tocolumn) as $value) {
            array_push($this->columns, $value);
        }
    }

    /**
     * If cell should be readed in.
     * Not working in the way intended (read only cells with relevant data).
     *
     * @param int $column The column
     * @param string $row The row
     * @param string $worksheetname The worksheet name
     * @return bool
     */
    public function readcell($column, $row, $worksheetname = '') {
        if (in_array($column, $this->columns)) {
            return true;
        }
        return false;
    }
}
