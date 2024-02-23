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
 * Tracking changes of input fields, changing focus and checking matrnr.
 *
 * @module      mod_exammanagement/input_results
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';

export const init = (lang) => {

  var matrnr = $('#id_matrnr').val();

  if (matrnr) { // Initial disabling of field matrnr if it already exists.
    $('#id_matrnr').prop("disabled", true);
  }

  // Create input type number elements.
  $("form.mform input[type=text]:not(#id_matrnr)").attr("type", "number");

  var styles = {
    "-webkit-appearance": "textfield",
    "-moz-appearance": "textfield",
    "margin": "0px",
    "width": "70px"
  };

  $(".tasksarea input[type=number]:not(#id_matrnr)").css(styles);
  $(".tasksarea input[type=number]:not(#id_matrnr)").attr("step", "0.01");
  $(".tasksarea nput[type=number]:not(#id_matrnr)").attr("min", "0");

  // Remove cols from form layout.
  $('.exammanagement_task_spacing .form-group div').removeClass('col-md-3');
  $('.exammanagement_task_spacing .form-group div').removeClass('col-md-9');
  $('.exammanagement_task_spacing .form-group').removeClass('row');

  $(".tasksarea input[type=number]:not(#id_matrnr)").each(function() {
    $(this).attr("max", parseFloat($("#" + "max_points_" + getInputId($(this))).text().replace(/,/g, '.')));
  });

  $("form.mform .form-group input.checkboxgroup1").each(function() { // Initial disabling point fields if checkbox already checked.
    if ($(this).prop('checked')) {
      $("form.mform .form-group input.form-control").each(function() {
        if (getInputId($(this)) != "matrnr") {
          $(this).prop("disabled", true);
        }
      });
    }
  });

  $("#totalpoints").text(getTotalpoints(lang)); // Initial set totalpoints.

  $("form.mform :checkbox").change(function() { // If some checkbox is checked/unchecked.
    var checked = false;
    var changedId = $(this).prop('id'); // Get id of changed checkbox.

    $("form.mform .form-group input.checkboxgroup1").each(function() { // Check if some checkbox is now checked.
      if ($(this).prop('checked')) {
        checked = true;
      }
    });

    if (checked) { // If some checkbox is now checked: uncheck all other checkboxes.
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

      $("form.mform .form-group input.form-control").each(function() { // Disable all point-fields and set their value to 0.
        if (getInputId($(this)) != "matrnr") {
          $(this).prop("disabled", true);
          $(this).val(0);

        }
      });
    } else { // If no checkbix is now checked.
      $("form.mform .form-group input.form-control").each(function() { // Enable all point-fields.
        if (getInputId($(this)) != "matrnr") {
          $(this).prop("disabled", false);
        }
      });
    }
  });

  $("form.mform .form-group").on("change", "input", function() { // If some input field changes.
    $("#totalpoints").text(getTotalpoints(lang)); // Change totalpoints.
  });

  $('#id_matrnr').change(function() { // Reload page if matrnr is entered.

    var matrnr = $(this).val();
    var id = getUrlParameter('id');

    if (matrnr.match(/^\d+$/)) {
      location.href = "inputresults.php?id=" + id + "&matrnr=" + matrnr;
    } else {
      $(this).val('');
      require(['core/notification'], function(notification) {
        notification.addNotification({
          message: invalidMatrnrFormatStr,
          type: "error"
        });
      });
    }
  });

  if ($("input[name='matrval']").val() == 1) { // Set focus.
    $("#id_matrnr").focus();
  } else {
    $("#id_points_1").focus();
  }

  $('#id_submitbutton').click(function() { // If submittbutton is presses enable complete form (for moodle purposes).
    $("#id_matrnr").prop("disabled", false);
  });

  var lastpointsfield = $('input[name="lastkeypoints"]').val();

  $('form.mform input[name^="points["]').keypress(function(e) {

    if (e.which == 13 || e.which == 3) {

      var name = $(this).attr('name');

      var newfieldnumber = parseInt(name.match(/\d+/)) + 1;

      e.preventDefault();
      $('input[name="points[' + newfieldnumber + ']"]').focus();
    }
  });

  $('input[name="points[' + lastpointsfield + ']"]').keydown(function(e) {
    if (e.which == 9 || e.which == 13 || e.which == 3) {
      $("#id_matrnr").prop("disabled", false);
      $('form.mform').submit();

      return false;
    }
    return true;
  });

};

var invalidMatrnrFormatStr = false;

require(['core/str'], function(str) {
  var localizedStrings = [
    {
      key: 'invalid_matrnr_format',
      component: 'mod_exammanagement'
    },
    {
      key: 'cancel',
      component: 'mod_exammanagement'
    },
  ];
  str.get_strings(localizedStrings).then(function(results) {
    invalidMatrnrFormatStr = results[0];
    $("#id_cancel").val(results[1]);
    return;
  }).catch();
});

var getInputId = function(element) {
  var id = element.attr('id').split('_').pop();
  return id;
};

var getTotalpoints = function(lang) {
  var totalpoints = 0;

  $("form.mform .form-group input.form-control").each(function() {
    if (getInputId($(this)) != "matrnr" && $(this).val()) {
      totalpoints += parseFloat($(this).val());
    }
  });

  totalpoints = totalpoints.toLocaleString(lang);

  return totalpoints;
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
  return false;
};
