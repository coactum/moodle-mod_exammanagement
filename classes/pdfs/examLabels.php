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
 * Class for examlabels PDF for exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\pdfs;
use TCPDF;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/pdflib.php');

/**
 * Extend the base TCPDF class to create custom header and footer for the document.
 *
 * @package   mod_exammanagement
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class examLabels extends TCPDF {

    /**
     * Override the footer of the base class.
     */
    public function footer() {
        $this->SetFont('helvetica', 'BI', 10);
        $this->SetXY(10, -15); // 1.5 cm from bottom.

        $this->Cell(0, 12, get_string("required_label_type", "mod_exammanagement") . " Avery Zweckform L4744", 0, 0, 'L');
        $this->SetX(200);

        $this->Cell(0, 12, $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages(), 0, 0, 'C');
    }

}
