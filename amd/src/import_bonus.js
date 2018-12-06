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
 * functions for adding and removing bonus steps and creating input type number fields
 *
 * @module      mod_exammanagement/import_bonus
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function ($) {

  var getBonusstepCount = function () {
    var bonusstepcount = 0;

    $(".form-group input.form-control").not('#id_idfield, #id_pointsfield').each(function () {
      bonusstepcount += 1;
    });

    return bonusstepcount;
  };

  return {
    init: function () {

      // create input type number elements
      $("input[type=text]").not('#id_idfield, #id_pointsfield').attr("type", "number");

      var styles = {
        "-webkit-appearance": "textfield",
        "-moz-appearance": "textfield",
        "margin": "0px",
        "width": "70px"
      };

      $("input[type=number]").css(styles);
      $("input[type=number]").attr("step", "0.01");
      $("input[type=number]").attr("min", "0");
    },
    addbonusstep: function () { //add new tasks

      $("#id_add_bonusstep").click(function () {

        var bonusstepcount = getBonusstepCount();
        var newbonusstepcount = bonusstepcount + 1;
        var pointsofnewbonusstep = '';

        var temp = '<div class="form-group  fitem  ">';
        temp += '<label class="col-form-label sr-only" for="id_bonussteppoints_' + newbonusstepcount + '"></label><span data-fieldtype="text">';
        temp += '<input class="form-control" name="bonussteppoints[' + newbonusstepcount + ']" id="id_bonussteppoints_' + newbonusstepcount + '" value="';
        temp += pointsofnewbonusstep + '" size="1" type="number" style="-webkit-appearance: textfield; -moz-appearance:textfield; ';
        temp += 'margin: 0px; width: 70px;" min="0" step="0.01"></span><div class="form-control-feedback" id="id_error_bonussteppoints[';
        temp += newbonusstepcount + ']" style="display: none;"></div></div> ';

        $(".form-group:nth-of-type(5) .col-md-9").append('<span class="task_spacing"><strong>' + newbonusstepcount + '</strong></span>');
        $(".form-group:nth-of-type(6) .col-md-9").append(temp);

        $("input[name=bonusstepcount]").val(parseInt($("input[name=bonusstepcount]").val()) + 1);

      });
    },
    removebonusstep: function () { //remove task

      $("#id_remove_bonusstep").click(function () {

        var bonusstepcount = getBonusstepCount();

        if (bonusstepcount > 1) {
          $(".form-group:nth-of-type(5) .col-md-9 span:last").remove();
          $(".form-group:nth-of-type(6) .col-md-9 .form-group:last").remove();

          $("input[name=bonusstepcount]").val(parseInt($("input[name=bonusstepcount]").val() - 1));
        }
      });
    }
  };
});
