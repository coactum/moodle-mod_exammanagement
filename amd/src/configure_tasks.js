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
 * functions for tracking changes of input fields, adding and removing tasks
 *
 * @module      mod_exammanagement/configure_tasks
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {

  var getTotalpoints = function() {
    var totalpoints = 0;
    var newval;

    $(".form-group input.form-control").each(function() {
        newval = parseFloat($(this).val().replace(",","."));
        if (newval){
            totalpoints += newval;
        }
    });

    return totalpoints;
  };

  var getTaskCount = function() {
    var taskcount = 0;

    $(".form-group input.form-control").each(function() {
      taskcount += 1;
    });

    return taskcount;
  };

  return {
    init: function() {

      // create input type number elements
      $("input[type=text]").attr("type", "number");

      var styles = {
          "-webkit-appearance": "textfield",
          "-moz-appearance":"textfield",
          "margin": "0px",
          "width": "60px"
      };

      $("input[type=number]").css(styles);
      $("input[type=number]").attr("step", "0.01");
      $("input[type=number]").attr("min", "0");

      $(".form-group").on("change", "input", function() { // update totalpoints if some field changes

        var totalpoints = getTotalpoints();
        $("#totalpoints").text(String(totalpoints.toFixed(2)).replace('.', ','));
      });
    },
    addtask: function() { //add new tasks

      $("#id_add_task").click(function() {

        var taskcount = getTaskCount();
        var newtaskcount = taskcount + 1;
        var pointsofnewtask = 10;

        var temp = '<div class="form-group  fitem  ">';
        temp += '<label class="col-form-label sr-only" for="id_task_' + newtaskcount + '"></label><span data-fieldtype="text">';
        temp += '<input class="form-control" name="task[' + newtaskcount + ']" id="id_task_' + newtaskcount + '" value="';
        temp += pointsofnewtask + '" size="1" type="number" style="-webkit-appearance: textfield; -moz-appearance:textfield; ';
        temp += 'margin: 0px; width: 60px;" step="0.01" min="0"></span><div class="form-control-feedback" id="id_error_task[';
        temp += newtaskcount + ']" style="display: none;"></div></div> ';

        $(".form-group:nth-of-type(5) .col-md-9").append('<span class="task_spacing"><strong>' + newtaskcount + '</strong></span>');
        $(".form-group:nth-of-type(6) .col-md-9").append(temp);

        var totalpoints = getTotalpoints();
        $("#totalpoints").text(String(totalpoints.toFixed(2)).replace('.', ','));

        $("input[name=newtaskcount]").val(parseInt($("input[name=newtaskcount]").val())+1);

      });
    },
    removetask: function() { //remove task

      $("#id_remove_task").click(function() {

        var taskcount = getTaskCount();

        if (taskcount > 1) {
          $(".form-group:nth-of-type(5) .col-md-9 span:last").remove();
          $(".form-group:nth-of-type(6) .col-md-9 .form-group:last").remove();

          var totalpoints = getTotalpoints();
          $("#totalpoints").text(String(totalpoints.toFixed(2)).replace('.', ','));

          $("input[name=newtaskcount]").val(parseInt($("input[name=newtaskcount]").val()-1));
        }
      });
    }
  };
});
