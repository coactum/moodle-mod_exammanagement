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
 * Participants overview page.
 *
 * @module      mod_exammanagement/import_bonus
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';

export const init = (tasks) => {

  // Create input type number elements.
  $("form.mform input[type=text]").attr("type", "number");

  var styles = {
    "-webkit-appearance": "textfield",
    "-moz-appearance": "textfield",
    "margin-left": "10px",
    "width": "70px"
  };

  $("form.mform input[type=number]").css(styles);
  $("form.mform input[type=number]").attr("step", "0.01");
  $("form.mform input[type=number]").attr("min", "0");

  if ($("[id^=id_points]").length !== 0) {
    $("[id^=id_points]").first().focus();
  } else {
    $("[id^=id_bonuspoints]").first().focus();
  }

  $("[id^=id_points]").each(function() {

    var participantid = $(this).attr("id").split('_')[2];
    var count = $(this).attr("id").split('_')[3];

    $(this).attr("max", tasks[count]); // Set maximum points for tasks.

    if ($("#id_state_" + participantid).val() !== 'normal' && $("#id_state_" + participantid).val() !== 'not_set') {
      $(this).prop("disabled", true); // Initial disabling point fields if examstate is not normal or not set.
    }
  });

  $("[id^=id_bonuspoints]").each(function() {

    var participantid = $(this).attr("id").split('_')[2];

    if ($("#id_bonussteps_" + participantid).val() && $("#id_bonussteps_" + participantid).val() !== '-') {
      $(this).prop("disabled", true); // Initial disabling bonuspoint fields if bonusstep is set.
    }
  });

  // If exam bonus steps change.
  $("[id^=id_bonussteps]").change(function() {
    var participantid = $(this).attr("id").split('_')[2];

    if ($(this).val() !== '-') {
      $("#id_bonuspoints_" + participantid).val('-'); // Reset bonus points.
      $("#id_bonuspoints_" + participantid).prop("disabled", true); // Disable bonuspoints.
    } else {
      $("#id_bonuspoints_" + participantid).prop("disabled", false); // Enable bonuspoints.
    }

    $("#id_state_" + participantid).focus(); // Move focus.

    $('input[name="bonuspoints_entered[' + participantid + ']"]').val(0); // Set bonuspoints entered to true.
  });

  // If exam bonus points change.
  $("[id^=id_bonuspoints]").change(function() {
    var participantid = $(this).attr("id").split('_')[2];

    if ($(this).val()) {
      $("#id_bonussteps_" + participantid).val('-'); // Reset bonus steps.
      $("#id_state_" + participantid).focus(); // Move focus.
      $('input[name="bonuspoints_entered[' + participantid + ']"]').val(1); // Set bonuspoints entered to true.
    }
  });

  // If exam points are entered.
  $("[id^=id_points]").change(function() {
    var participantid = $(this).attr("id").split('_')[2];
    $("#id_state_" + participantid).val('normal');
  });

  // If examstate changes.
  $("[id^=id_state]").change(function() {
    var participantid = $(this).attr("id").split('_')[2];

    // If examstate is now not normal or not set: disable all point-fields and set their value to 0.
    if ($(this).val() !== 'normal' && $(this).val() !== 'not_set') {
      $("[id^=id_points_" + participantid + "]").each(function() {
        $(this).prop("disabled", true);
        $(this).val(0);
      });
    } else if ($(this).val() === 'normal') { // If examstate is now normal.
      $("[id^=id_points_" + participantid + "]").each(function() { // Enable all point fields.
        $(this).prop("disabled", false);
        $("[id^=id_points_" + participantid + "").first().focus();
      });
    } else if ($(this).val() == 'not_set') {
      $("[id^=id_points_" + participantid + "]").each(function() { // Enable all point fields.
        $(this).val('');
        $(this).prop("disabled", false);
        $("[id^=id_points_" + participantid + "").first().focus();
      });
    }
  });

  // Remove cols from form layout.
  $('.exammanagement_table div').removeClass('col-md-3');
  $('.exammanagement_table div').removeClass('col-md-9');

  $('form.mform #id_submitbutton').click(function() { // If submittbutton is pressed enable complete form (for moodle purposes).

    $("form.mform .form-group input.form-control").not("[id^=id_bonuspoints]").each(function() { // Enable all point fields.
      $(this).prop("disabled", false);
    });
  });
};
