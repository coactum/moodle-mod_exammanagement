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
 * @module      mod_exammanagement/select_all_choices
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {

  return {
      init: function() {
          $( ".form-group" ).on( "change", "input", function() {
              $("#totalpoints").text(getTotalpoints());
          });
      }
  };

  return {
      addtask: function(){

          $("#add_task").click(function () {

          		taskcount = getTaskCount();
          		newtaskcount = taskcount+1;
          		pointsofnewtask = 10;

          		$(".form-group:nth-of-type(4) .col-md-9").append('<span class="task_spacing">' + newtaskcount + '</span>');
          		$(".form-group:nth-of-type(5) .col-md-9").append('<div class="form-group  fitem  "><label class="col-form-label sr-only" for="id_task_' + newtaskcount + '"></label><span data-fieldtype="text"><input class="form-control" name="task[' + newtaskcount + ']" id="id_task_' + newtaskcount + '" value="' + pointsofnewtask + '" size="1" type="text"></span><div class="form-control-feedback" id="id_error_task[' + newtaskcount + ']" style="display: none;"></div></div> ');

          		totalpoints = getTotalpoints();
          		$("#totalpoints").text(totalpoints);
          }
      }
  };

  return {
      removetask: function(){

          $("#add_task").click(function () {

              taskcount = getTaskCount();

            	if (taskcount > 1){
            			$(".form-group:nth-of-type(4) .col-md-9 :last-child").remove();
            			$(".form-group:nth-of-type(5) .col-md-9 .form-group:last").remove();

            			totalpoints = getTotalpoints();
            			$("#totalpoints").text(totalpoints);
            	}
          }
      }
  };

    var getTotalpoints = function(){
    	var totalpoints = 0;

    	$(".form-group input.form-control").each(function () {
    				totalpoints += parseInt($(this).val());
    		});

    	return totalpoints;
    }

    var getTaskCount = function(){
    	var taskcount = 0;

    	$(".form-group input.form-control").each(function () {
    				taskcount += 1;
    		});

    	return taskcount;
    }

});
