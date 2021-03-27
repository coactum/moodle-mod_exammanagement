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
 * Saving assign places form and fetching table fragment via ajax.
 *
 * @module      mod_exammanagement/assign_places
 * @copyright   coactum GmbH 2021
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function ($) {

  return {
    init: function(){
      // remove cols from form layout
      $('.exammanagement_table .form-group div').removeClass('col-md-3');
      $('.exammanagement_table .form-group div').removeClass('col-md-9');
    },

    toggleAvailablePlaces: function(){

      $("form.mform .fitem").on("change", "select", function () { // change available places pattern if other room is choosen
        var selectedPlacesId = $(this).children(":selected").attr("value");
        var participantId = $(this).attr("id").split('_')[2];

        // hide all placespatterns for participant
        $("#available_places_" + participantId + " .hideablepattern").each(function () {
          $(this).hide(); // hide old places pattern
        });

        var posPoint = selectedPlacesId.indexOf('.'); // make room ids with . working

        if (posPoint !== -1) {
          selectedPlacesId = selectedPlacesId.substr(0, posPoint) + '\\' + selectedPlacesId.substr(posPoint);
        }

        // show all placespatterns for participant
        $('#available_places_' + participantId + ' #' + selectedPlacesId).show(); // make correct pattern for places visible

        // reset places
        $('#id_places_' + participantId).val('');

        // change focus to places field
        $('#id_places_' + participantId).focus();

      });

    }
  };
});
