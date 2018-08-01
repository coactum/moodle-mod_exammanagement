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
 * functions for tracking changes of input fields
 *
 * @module      mod_exammanagement/configure_gradingscale
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/notification'], function($) {

  return {
    init: function() {

      var max_points = $("#totalpoints strong").text();

      $(".form-group").on("change", "input", function() {
        var value = $(this).val();
        var bad_input = value.search(/^[0-9]+(\.[0-9]){0,1}$/);

        if (bad_input == -1){
          $(this).val(0);

          require(['core/notification'], function(notification) {
           notification.addNotification({
             message: "Ungültige Punktzahl",
             type: "error"
           });
          });
        }
        if (bad_input != -1 && parseInt(value) > max_points){
          $(this).val(max_points);

          require(['core/notification'], function(notification) {
           notification.addNotification({
             message: "Höchstpunktzahl überschritten",
             type: "error"
           });
          });
        }
      });
    },
  };
});
