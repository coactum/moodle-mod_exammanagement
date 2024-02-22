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
 * Tracking changes of input fields, adding and removing tasks and creating input type number fields.
 *
 * @module      mod_exammanagement/configure_tasks
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';

export const init = (lang) => {
  // Create input type number elements.
  $("form.mform input[type=text]").attr("type", "number");

  var styles = {
    "-webkit-appearance": "textfield",
    "-moz-appearance": "textfield",
    "margin": "0px"
  };

  $("form.mform input[type=number]").css(styles);
  $("form.mform input[type=number]").attr("step", "0.1");
  $("form.mform input[type=number]").attr("min", "0.1");
  $("form.mform input[type=number]").attr("max", "100");

  // Remove cols from form layout.
  $('.exammanagement_task_spacing .form-group div').removeClass('col-md-3');
  $('.exammanagement_task_spacing .form-group div').removeClass('col-md-9');
  $('.exammanagement_task_spacing .form-group').removeClass('row');

  $("form.mform .form-group").on("change", "input", function() { // Update total points if some field changes.

    var totalpoints = getTotalpoints(lang);
    $("#totalpoints").text(totalpoints);
  });
};

export const addtask = (lang) => { // Add new tasks.
  $("#id_add_task").click(function() {

    var taskcount = getTaskCount();
    var newtaskcount = taskcount + 1;
    var pointsofnewtask = 10;

    if (taskcount <= 99) {

      var temp = '<span class="exammanagement_task_spacing">';
      temp += '<strong>' + newtaskcount + '</strong><div id="fitem_id_task_1" class="form-group fitem femptylabel">';
      temp += '<div class="col-form-label d-flex pb-0 pr-md-0">';
      temp += '<div class="ml-1 ml-md-auto d-flex align-items-center align-self-start"></div></div>';
      temp += '<div class="form-inline align-items-start felement" data-fieldtype="text">';
      temp += '<input type="number" class="form-control" name="task[' + newtaskcount + ']" id="id_task_' + newtaskcount + '" ';
      temp += 'value="' + pointsofnewtask + '" style="appearance: textfield; margin: 0px;" step="0.1" min="0.1" max="100">';
      temp += '<div class="form-control-feedback invalid-feedback" id="id_error_task_' + newtaskcount + '">';
      temp += '</div></div></div></span>';

      $(".mform .tasksarea").append(temp);

      var totalpoints = getTotalpoints(lang);
      $("#totalpoints").text(totalpoints);

      $("input[name=newtaskcount]").val(parseInt($("input[name=newtaskcount]").val()) + 1);
    }

  });
};

export const removetask = (lang) => { // Remove task.
  $("#id_remove_task").click(function() {

    var taskcount = getTaskCount();

    if (taskcount > 1) {
      $(".mform .tasksarea span:last").remove();

      var totalpoints = getTotalpoints(lang);
      $("#totalpoints").text(totalpoints);

      $("input[name=newtaskcount]").val(parseInt($("input[name=newtaskcount]").val() - 1));
    }
  });
};

var getTotalpoints = function(lang) {
  var totalpoints = 0;
  var newval;

  $(".form-group input.form-control").each(function() {
    newval = parseFloat($(this).val());
    if (newval) {
      totalpoints += newval;
    }
  });

  totalpoints = totalpoints.toLocaleString(lang);

  return totalpoints;
};

var getTaskCount = function() {
  var taskcount = 0;

  $("form.mform .exammanagement_task_spacing").each(function() {
    taskcount += 1;
  });

  return taskcount;
};
