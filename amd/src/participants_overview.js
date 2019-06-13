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
 * functions for participants overview page
 *
 * @module      mod_exammanagement/import_bonus
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function ($) {

  var getTotalpoints = function () {
    var totalpoints = 0;

    $(".form-group input.form-control").not("#id_place").each(function () {
      if ($(this).val() != '-') {
        totalpoints += parseFloat($(this).val().replace(',', '.'));
      }
    });

    return String(totalpoints.toFixed(2)).replace('.', ',');
  };

  return {
    init: function () {

      // create input type number elements
      $("input[type=text]").not('#id_room, #id_place').attr("type", "number");

      var styles = {
        "-webkit-appearance": "textfield",
        "-moz-appearance": "textfield",
        "margin-left": "10px",
        "width": "65px"
      };

      $("input[type=number]").css(styles);
      $("input[type=number]").attr("step", "0.01");
      $("input[type=number]").attr("min", "0");

      // initial disabling point fields if examstate is not normal
      if ($("#id_state").val() !== 'normal') {
        $(".form-group input.form-control").not("#id_place").each(function () {
          $(this).prop("disabled", true);
        });
      }

      var id;
      var posPoint;

      // show available places pattern
      id = $("#id_room").children(":selected").attr("value");

      $(".hiddenpattern").each(function () { // for removing the initial set hidden class
        $(this).prop("class", 'hideablepattern');
      });

      $(".hideablepattern").each(function () {
        $(this).hide(); // hide old places pattern
      });

      posPoint = id.indexOf('.'); // make room ids with . working

      if (posPoint !== -1) {
        id = id.substr(0, posPoint) + '\\' + id.substr(posPoint);
      }

      $('#' + id).show(); // make correct pattern for places visible

      // if examstate changes
      $("#id_state").change(function () {

        if ($("#id_state").val() !== 'normal') { // if examstate is now not normal: disable all point-fields and set their value to 0

          $(".form-group input.form-control").not("#id_place").each(function () {
            $(this).prop("disabled", true);
            $(this).val(0);
            $("#totalpoints").text($("#id_state option:selected").text()); // change totalpoints

          });
        } else {  // if examstate is now normal
          $(".form-group input.form-control").not("#id_place").each(function () { // enable all point-fields
            $(this).prop("disabled", false);
          });
          $("#totalpoints").text(getTotalpoints()); // change totalpoints
        }
      });

      $(".form-group").not("#fitem_id_place").on("change", "input", function () { // if some input field changes
        $("#totalpoints").text(getTotalpoints()); // change totalpoints
      });

      $("#fitem_id_room").on("change", "select", function () { // change avilable places pattern if other room is choosen
        id = $(this).children(":selected").attr("value");

        $(".hideablepattern").each(function () {
          $(this).hide(); // hide old places pattern
        });

        posPoint = id.indexOf('.'); // make room ids with . working

        if (posPoint !== -1) {
          id = id.substr(0, posPoint) + '\\' + id.substr(posPoint);
        }

        $('#' + id).show(); // make correct pattern for places visible
      });

      // remove cols from form layout
      $('div').removeClass('col-md-3');
      $('div').removeClass('col-md-9');

      $('form > .form-group > div:first-child').addClass('col-md-3');
      $('form > .form-group > div:last-child').addClass('col-md-9');

      $('#id_submitbutton').click(function () {  // if submittbutton is presses enable complete form (for moodle purposes)
        $(".form-group input.form-control").not("#id_place").each(function () { // enable all point-fields
          $(this).prop("disabled", false);
        });

        $(".form-group input.form-control").not("#id_place").each(function () { // if some input point field has values
          if ($(this).val()) {
            $("input[name='pne']").val(false); // set points not entered param to false
          }
        });

      });
    }
  };
});
