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
 * Toggle available places pattern and removes unneccesary cols.
 *
 * @module      mod_exammanagement/assign_places
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';

export const init = () => {
    // Remove cols from form layout.
    $('.exammanagement_table .form-group div').removeClass('col-md-3');
    $('.exammanagement_table .form-group div').removeClass('col-md-9');
};

export const toggleAvailablePlaces = () => {
    $("form.mform .fitem").on("change", "select", function() { // Change available places pattern if other room is choosen.
        var selectedPlacesId = $(this).children(":selected").attr("value");
        var participantId = $(this).attr("id").split('_')[2];

        // Hide all placespatterns for participant.
        $("#available_places_" + participantId + " .hideablepattern").each(function() {
          $(this).hide(); // Hide old places pattern.
        });

        var posPoint = selectedPlacesId.indexOf('.'); // Make room ids with . working.

        if (posPoint !== -1) {
          selectedPlacesId = selectedPlacesId.substr(0, posPoint) + '\\' + selectedPlacesId.substr(posPoint);
        }

        // Show all placespatterns for participant.
        $('#available_places_' + participantId + ' #' + selectedPlacesId).show(); // Make correct pattern for places visible.

        // Reset places.
        $('#id_places_' + participantId).val('');

        // Change focus to places field.
        $('#id_places_' + participantId).focus();

      });

};
