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
 * Class for seatingplan PDF for exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\pdfs;
use mod_exammanagement\general\exammanagementInstance;
use TCPDF;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/pdflib.php');
require_once(__DIR__.'/../general/exammanagementInstance.php');

/**
 * Extend the base TCPDF class to create custom header and footer for the document.
 *
 * @package   mod_exammanagement
 * @copyright 2022 coactum GmbH
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class seatingPlan extends TCPDF {

    /**
     * Override the header of the base class.
     */
    public function header() {

        // Course_module ID, or ...
        $id = optional_param('id', 0, PARAM_INT);

        // ... module instance id - should be named as the first character of the module.
        $e = optional_param('e', 0, PARAM_INT);

        $exammanagementinstance = exammanagementInstance::getInstance($id, $e);

        if (file_exists(__DIR__.'/../../data/logo_full.ai')) {
            $this->ImageEps(__DIR__.'/../../data/logo_full.ai', 25, 12, 70);
            $this->SetFont('helvetica', 'B', 22);
            $this->MultiCell(70, 10, get_string('seatingplan', 'mod_exammanagement'), 0, 'C', 0, 0, 115, 17);
        } else {
            $this->SetFont('helvetica', 'B', 22);
            $this->MultiCell(70, 10, get_string('seatingplan', 'mod_exammanagement'), 0, 'C', 0, 0, 70, 17);
        }

        $this->SetFont('helvetica', 'B', 14);
        $this->MultiCell(130, 5, strtoupper($exammanagementinstance->getCleanCourseCategoryName()) . ' / ' .
            $exammanagementinstance->getCourse()->fullname . ' ('. $exammanagementinstance->getModuleinstance()->name .')',
            0, 'L', 0, 0, 25, 40);
        $this->MultiCell(26, 5, $exammanagementinstance->getHrExamtime(), 0, 'R', 0, 0, 159, 40);
    }

    /**
     * Override the footer of the base class.
     */
    public function footer() {
        $this->SetY(-16); // 1.6 cm from bottom.
        $this->SetFont('helvetica', 'BI', 12);
        $this->Cell(0, 12, $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}
