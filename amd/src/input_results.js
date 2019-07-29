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
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/notification'], function ($) {

  var getInputId = function (element) {
    var id = element.attr('id').split('_').pop();

    return id;
  };

  var getTotalpoints = function () {
    var totalpoints = 0;

    $(".form-group input.form-control").each(function () {
      if (getInputId($(this)) != "matrnr" && $(this).val()) {
        totalpoints += parseFloat($(this).val().replace(',', '.'));
      }
    });

    return String(totalpoints.toFixed(2)).replace('.', ',');
  };

  var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
      sURLVariables = sPageURL.split('&'),
      sParameterName,
      i;

    for (i = 0; i < sURLVariables.length; i++) {
      sParameterName = sURLVariables[i].split('=');

      if (sParameterName[0] === sParam) {
        return sParameterName[1] === undefined ? true : sParameterName[1];
      }
    }
  };

  return {
    init: function () {

      var matrnr = $('#id_matrnr').val();

      if (matrnr) { // initial disabling of field matrnr if it already exists
        $('#id_matrnr').prop("disabled", true);
      }

      // create input type number elements
      $("input[type=text]:not(#id_matrnr)").attr("type", "number");

      var styles = {
        "-webkit-appearance": "textfield",
        "-moz-appearance": "textfield",
        "margin": "0px",
        "width": "70px"
      };

      $("input[type=number]:not(#id_matrnr)").css(styles);
      $("input[type=number]:not(#id_matrnr)").attr("step", "0.01");
      $("input[type=number]:not(#id_matrnr)").attr("min", "0");

      $("input[type=number]:not(#id_matrnr)").each(function () {
        $(this).attr("max", parseFloat($("#" + "max_points_" + getInputId($(this))).text().replace(/,/g, '.')));
      });

      $(".form-group input.checkboxgroup1").each(function () { // initial disabling point fields if some checkbox is already checked
        if ($(this).prop('checked')) {
          $(".form-group input.form-control").each(function () {
            if (getInputId($(this)) != "matrnr") {
              $(this).prop("disabled", true);
            }
          });
        }
      });

      $("#totalpoints").text(getTotalpoints()); // initial set totalpoints

      $(":checkbox").change(function () { //if some checkbox is checked/unchecked
        var checked = false;
        var changedId = $(this).prop('id'); // get id of changed checkbox

        $(".form-group input.checkboxgroup1").each(function () { // check if some checkbox is now checked
          if ($(this).prop('checked')) {
            checked = true;
          }
        });

        if (checked) { // if some checkbox is now checked: uncheck all other checkboxes
          if (changedId == "id_state_nt") {
            $('#id_state_fa').prop('checked', false);
            $('#id_state_ill').prop('checked', false);
          } else if (changedId == "id_state_fa") {
            $('#id_state_nt').prop('checked', false);
            $('#id_state_ill').prop('checked', false);
          } else if (changedId == "id_state_ill") {
            $('#id_state_nt').prop('checked', false);
            $('#id_state_fa').prop('checked', false);
          }

          $(".form-group input.form-control").each(function () { // disable all point-fields and set their value to 0
            if (getInputId($(this)) != "matrnr") {
              $(this).prop("disabled", true);
              $(this).val(0);

            }
          });
        } else {  // if no checkbix is now checked
          $(".form-group input.form-control").each(function () { // enable all point-fields
            if (getInputId($(this)) != "matrnr") {
              $(this).prop("disabled", false);
            }
          });
        }
      });

      $(".form-group").on("change", "input", function () { // if some input field changes
        $("#totalpoints").text(getTotalpoints()); // change totalpoints
      });

      $('#id_matrnr').change(function () { // reload page if matrnr is entered

        var matrnr = $(this).val();
        var id = getUrlParameter('id');

        if (matrnr.match(/^\d+$/)) {
          location.href = "inputResults.php?id=" + id + "&matrnr=" + matrnr;
        } else {
          $(this).val('');
          require(['core/notification'], function (notification) {
            notification.addNotification({
              message: "Keine gültiges Matrikelnummernformat",
              type: "error"
            });
          });
        }
      });

      if ($("input[name='matrval']").val() == 1) { //set focus
        $("#id_matrnr").focus();
      } else {
        $("#id_points_1").focus();
      }

      $('#id_submitbutton').click(function () {  // if submittbutton is presses enable complete form (for moodle purposes)
        $("#id_matrnr").prop("disabled", false);
      });

      $('#id_cancel').val('Zurück zur Prüfungsorganisation');
    },
  };
});
