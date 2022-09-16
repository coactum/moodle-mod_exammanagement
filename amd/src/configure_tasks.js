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
 * functions for tracking changes of input fields, adding and removing tasks and creating input type number fields
 *
 * @module      mod_exammanagement/configure_tasks
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function ($) {

  var getTotalpoints = function (lang) {
    var totalpoints = 0;
    var newval;

    $(".form-group input.form-control").each(function () {
      newval = parseFloat($(this).val());
      if (newval) {
        totalpoints += newval;
      }
    });

    totalpoints = totalpoints.toLocaleString(lang);

    return totalpoints;
  };

  var getTaskCount = function () {
    var taskcount = 0;

    $("form.mform .form-group input.form-control").each(function () {
      taskcount += 1;
    });

    return taskcount;
  };

  return {
    init: function (lang) {

      // create input type number elements
      $("form.mform input[type=text]").attr("type", "number");

      var styles = {
        "-webkit-appearance": "textfield",
        "-moz-appearance": "textfield",
        "margin": "0px",
        "width": "70px"
      };

      $("form.mform input[type=number]").css(styles);
      $("form.mform input[type=number]").attr("step", "0.01");
      $("form.mform input[type=number]").attr("min", "0.01");
      $("form.mform input[type=number]").attr("max", "100");

      $("form.mform .form-group").on("change", "input", function () { // update totalpoints if some field changes

        var totalpoints = getTotalpoints(lang);
        $("#totalpoints").text(totalpoints);
      });
    },
    addtask: function (lang) { //add new tasks

      $("#id_add_task").click(function () {

        var taskcount = getTaskCount();
        var newtaskcount = taskcount + 1;
        var pointsofnewtask = 10;

        if (taskcount <= 44) {

          var temp = '<div class="form-group  fitem  ">';
          temp += '<label class="col-form-label sr-only" for="id_task_' + newtaskcount + '"></label><span data-fieldtype="text">';
          temp += '<input class="form-control" name="task[' + newtaskcount + ']" id="id_task_' + newtaskcount + '" value="';
          temp += pointsofnewtask + '" size="1" type="number" style="-webkit-appearance: textfield; -moz-appearance:textfield; ';
          temp += 'margin: 0px; width: 70px;" step="0.01" min="0" max="100"></span><div class="form-control-feedback" id="id_error_task[';
          temp += newtaskcount + ']" style="display: none;"></div></div> ';

          $("div[data-groupname='tasknumbers_array'] .col-md-9 fieldset div.d-flex").append('<span class="exammanagement_task_spacing"><strong>' + newtaskcount + '</strong></span>');
          $("div[data-groupname='tasks_array'] .col-md-9 fieldset div.d-flex").append(temp);

          var totalpoints = getTotalpoints(lang);
          $("#totalpoints").text(totalpoints);

          $("input[name=newtaskcount]").val(parseInt($("input[name=newtaskcount]").val()) + 1);
        }

      });
    },
    removetask: function (lang) { //remove task

      $("#id_remove_task").click(function () {

        var taskcount = getTaskCount();

        if (taskcount > 1) {
          $("div[data-groupname='tasknumbers_array'] .col-md-9 fieldset div.d-flex span:last").remove();
          $("div[data-groupname='tasks_array'] .col-md-9 fieldset div.d-flex .form-group:last").remove();

          var totalpoints = getTotalpoints(lang);
          $("#totalpoints").text(totalpoints);

          $("input[name=newtaskcount]").val(parseInt($("input[name=newtaskcount]").val() - 1));
        }
      });
    }
  };
});
