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
 * functions for tracking changes of input fields, changing focus and checking matr_nr
 *
 * @module      mod_exammanagement/input_results
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {

  var getTotalpoints = function() {
    var totalpoints = 0;
    var count = 0;

    $(".form-group input.form-control").each(function() {
      if(count !=1){
          count = 1;
      } else {
          totalpoints += parseInt($(this).val());
      }
    });

    return totalpoints;
  };
  return {
    init: function() {
      $(".form-group").on("change", "input", function() {
        $("#totalpoints").text(getTotalpoints());
      });
    },
    check_max_points: function() {
      $(".form-group").on("change", "input", function() {

        var id = 'max_points_'+$(this).attr('id').split('_').pop();

        if(id != "max_points_matrnr"){
          var current_points = parseInt($(this).val());
          var max_points = parseInt($("#"+id).text());

          if(current_points > max_points){
            $(this).val(max_points);
          }
        }
      });
    },
  };
});
