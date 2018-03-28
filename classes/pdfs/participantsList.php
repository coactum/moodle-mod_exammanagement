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
 * class for participantsListsNames PDF for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2017
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\pdfs;
use mod_exammanagement;
use TCPDF;

defined('MOODLE_INTERNAL') || die();

// Extend the TCPDF class to create custom Header and Footer
class participantsList extends TCPDF {

  public function Header() {

    // Course_module ID, or
    $id = optional_param('id', 0, PARAM_INT);

    // ... module instance id - should be named as the first character of the module
    $e  = optional_param('e', 0, PARAM_INT);

    $ExammanagementInstanceObj = exammanagementInstance::getInstance($id,$e);

    $this->ImageEps(__DIR__.'/../../data/upb_logo_full.ai', 25, 12, 70);
    $this->SetFont('freeserif', 'B', 22);
    $this->MultiCell(70, 10, get_string('participantslist', 'mod_exammanagement'), 0, 'C', 0, 0, 115, 17);
    $this->SetTextColor(255, 0, 0);
    $this->SetFont('freeserif', 'B', 10);
    $this->MultiCell(80, 3, "- " . get_string('internal_use', 'mod_exammanagement') . " -", 0, 'C', 0, 0, 110, 28);
    $this->SetTextColor(0, 0, 0);
    $this->SetFont('freeserif', '', 14);
    $this->MultiCell(130, 50, $ExammanagementInstanceObj->getModuleinstance()->categoryid . ' / ' . $ExammanagementInstanceObj->getCourse()->fullname . ' ('. $ExammanagementInstanceObj->getModuleinstance()->name .')', 0, 'L', 0, 0, 25, 40);
    $this->MultiCell(26, 50, $ExammanagementInstanceObj->getHrExamtime(), 0, 'R', 0, 0, 159, 40);
  }

  public function Footer() {
    $this->SetY(-16); // 1.6 cm from bottom
    $this->SetFont('freeserif', 'BI', 12);
    $this->Cell(0, 12, $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages(), 0, 0, 'C');
  }
}
