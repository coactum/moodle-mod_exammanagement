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
 * Outputs pdf file for mod_exammanagement.
 *
 * @package     mod_exammanagement
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_exammanagement\general;

use mod_exammanagement\pdfs\participantsList;
use moodle_url;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or ...
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id - should be named as the first character of the module
$e = optional_param('e', 0, PARAM_INT);

$exammanagementinstance = exammanagementInstance::getInstance($id, $e);
$userhandler = userhandler::getinstance($id, $e, $exammanagementinstance->getCm()->instance);
$moodlecontainer = Moodle::getInstance($id, $e);

if ($moodlecontainer->checkCapability('mod/exammanagement:viewinstance')) {

    if ($exammanagementinstance->isExamDataDeleted()) {
        redirect(new moodle_url('/mod/exammanagement/view.php#beforeexam', ['id' => $id]),
          get_string('err_examdata_deleted', 'mod_exammanagement'), null, 'error');
    } else {

        // If no password for moduleinstance is set or if user already entered correct password in this session: show main page.
        if (!isset($exammanagementinstance->moduleinstance->password) || (isset($exammanagementinstance->moduleinstance->password) && (isset($SESSION->loggedInExamOrganizationId)&&$SESSION->loggedInExamOrganizationId == $id))) {

            global $CFG;

            if (!$exammanagementinstance->getRoomsCount()) {
                redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
                    get_string('no_rooms_added', 'mod_exammanagement'), null, 'error');
            } else if (!$userhandler->getparticipantscount()) {
                redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
                    get_string('no_participants_added', 'mod_exammanagement'), null, 'error');
            } else if (!$exammanagementinstance->placesAssigned()) {
                redirect(new moodle_url('/mod/exammanagement/view.php#forexam', ['id' => $id]),
                    get_string('no_places_assigned', 'mod_exammanagement'), null, 'error');
            }

            // Include pdf.
            require_once(__DIR__.'/classes/pdfs/participantsList.php');

            define("WIDTH_COLUMN_NAME", 200);
            define("WIDTH_COLUMN_FIRSTNAME", 150);
            define("WIDTH_COLUMN_MATNO", 60);
            define("WIDTH_COLUMN_ROOM", 90);
            define("WIDTH_COLUMN_PLACE", 70);

            // Include the main TCPDF library (search for installation path).
            require_once(__DIR__.'/../../config.php');
            require_once($CFG->libdir.'/pdflib.php');

            // Create new PDF document.
            $pdf = new participantsList(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Set document information.
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($exammanagementinstance->getMoodleSystemName());
            $pdf->SetTitle(get_string('participantslist_places', 'mod_exammanagement') . ': ' .
                $exammanagementinstance->getCourse()->fullname . ', '. $exammanagementinstance->moduleinstance->name);
            $pdf->SetSubject(get_string('participantslist_places', 'mod_exammanagement'));
            $pdf->SetKeywords(get_string('participantslist_places', 'mod_exammanagement') . ', ' .
                $exammanagementinstance->getCourse()->fullname . ', ' . $exammanagementinstance->moduleinstance->name);

            // Set default monospaced font.
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // Set default header data.
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

            // Set margins.
            $pdf->SetMargins(25, 55, 25);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // Set auto page breaks.
            $pdf->SetAutoPageBreak(true, 19);

            // Set image scale factor.
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // ---------------------------------------------------------

            // Set font
            // dejavusans is a UTF-8 Unicode font, if you only need to
            // print standard ASCII chars, you can use core fonts like
            // helvetica or times to reduce file size.
            $pdf->SetFont('freeserif', '', 10);

            // Add a page
            // This method has several options, check the source code documentation for more information.
            $pdf->AddPage();

            // Get users and construct content for document.
            $roomids = json_decode($exammanagementinstance->moduleinstance->rooms);
            $fill = false;
            $previousroom;
            $tbl = $exammanagementinstance->getParticipantsListTableHeader();

            foreach ($roomids as $roomid) {
                $currentroom = $exammanagementinstance->getRoomObj($roomid);

                $participants = $userhandler->getexamparticipants(array('mode' => 'room', 'id' => $roomid), array('matrnr'));

                if ($participants) {
                    if (!empty($previousroom) && $currentroom != $previousroom) {
                        // New room - finish and print current table and begin new page.
                        $tbl .= "</table>";
                        $pdf->writeHTML($tbl, true, false, false, false, '');
                        $pdf->AddPage();
                        $fill = false;
                        $tbl = $exammanagementinstance->getParticipantsListTableHeader();
                    }

                    usort($participants, function($a, $b) { // Sort array by custom user function.
                        return strnatcmp($a->place, $b->place); // Sort by place.
                    });

                    foreach ($participants as $participant) {

                        $tbl .= ($fill) ? "<tr bgcolor=\"#DDDDDD\">" : "<tr>";
                        $tbl .= "<td width=\"" . WIDTH_COLUMN_NAME . "\">" . $participant->lastname . "</td>";
                        $tbl .= "<td width=\"" . WIDTH_COLUMN_FIRSTNAME . "\">" . $participant->firstname . "</td>";
                        $tbl .= "<td width=\"" . WIDTH_COLUMN_MATNO . "\" align=\"center\">" . $participant->matrnr . "</td>";
                        $tbl .= "<td width=\"" . WIDTH_COLUMN_ROOM . "\" align=\"center\">" . $participant->roomname . "</td>";
                        $tbl .= "<td width=\"" . WIDTH_COLUMN_PLACE . "\" align=\"center\">" . $participant->place . "</td>";
                        $tbl .= "</tr>";

                        $fill = !$fill;
                    }

                    $previousroom = $currentroom;
                }
            }

            $tbl .= "</table>";

            // Print text using writeHTMLCell().

            $pdf->writeHTML($tbl, true, false, false, false, '');

            // Generate filename without umlaute.
            $umlaute = Array("/ä/", "/ö/", "/ü/", "/Ä/", "/Ö/", "/Ü/", "/ß/");
            $replace = Array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
            $filenameumlaute = get_string("participantslist_places", "mod_exammanagement") . '_' . $exammanagementinstance->getCleanCourseCategoryName() . '_' . $exammanagementinstance->getCourse()->fullname . '_' . $exammanagementinstance->moduleinstance->name . '.pdf';
            $filename = preg_replace($umlaute, $replace, $filenameumlaute);

            // ---------------------------------------------------------

            // Close and output PDF document
            // This method has several options, check the source code documentation for more information.
            $pdf->Output($filename, 'D');

        } else { // If user hasnt entered correct password for this session: show enterPasswordPage.
            redirect (new moodle_url('/mod/exammanagement/checkPassword.php', ['id' => $id]), null, null, null);
        }
    }

} else {
    redirect(new moodle_url('/mod/exammanagement/view.php', ['id' => $id]),
        get_string('nopermissions', 'mod_exammanagement'), null, 'error');
}
