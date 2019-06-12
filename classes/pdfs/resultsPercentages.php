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
 * class for resultsPercentages PDF for exammanagement
 *
 * @package     mod_exammanagement
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\pdfs;
use TCPDF;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/pdflib.php');

// Extend the TCPDF class to create custom Header and Footer
class resultsPercentages extends TCPDF {

  public function Footer() {
		$this->SetY(-16); // 1.6 cm from bottom
		$this->SetFont('helvetica', 'BI', 12);
		$this->Cell(0, 12, $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages(), 0, 0, 'C');
	}
}