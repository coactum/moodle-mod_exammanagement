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
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function ($) {

  var getBonusstepCount = function () {
    var bonusstepcount = 0;

    $("form.mform .form-group input.form-control").not('#id_idfield, #id_pointsfield').each(function () {
      bonusstepcount += 1;
    });

    return bonusstepcount;
  };

  return {
    init: function () {

      // create input type number elements
      $("form.mform input[type=text]").not('#id_idfield, #id_pointsfield').attr("type", "number");

      var styles = {
        "-webkit-appearance": "textfield",
        "-moz-appearance": "textfield",
        "margin": "0px",
        "width": "70px"
      };

      $("form.mform input[type=number]").css(styles);
      $("form.mform input[type=number]").attr("step", "0.01");
      $("form.mform input[type=number]").attr("min", "0");

      $("#import_bonuspoints_text").hide();

      $('#id_bonusmode').change(function(){
        if (this.value == 'points'){
          $("#set_bonussteps").hide();
          $("#import_bonuspoints_text").show();
        } else if(this.value == 'steps'){
          $("#set_bonussteps").show();
          $("#import_bonuspoints_text").hide();
        }
      });

    },
    addbonusstep: function () { //add new tasks

      $("#id_add_bonusstep").click(function () {

        var bonusstepcount = getBonusstepCount();
        var newbonusstepcount = bonusstepcount + 1;
        var pointsofnewbonusstep = '';

        if (bonusstepcount < 3) {

          var temp = '<div class="form-group fitem">';
          temp += '<label class="col-form-label sr-only" for="id_bonussteppoints_' + newbonusstepcount + '"></label><span data-fieldtype="text">';
          temp += '<input class="form-control" name="bonussteppoints[' + newbonusstepcount + ']" id="id_bonussteppoints_' + newbonusstepcount + '" value="';
          temp += pointsofnewbonusstep + '" size="1" type="number" style="-webkit-appearance: textfield; -moz-appearance:textfield; ';
          temp += 'margin: 0px; width: 70px;" min="0" step="0.01"></span><div class="form-control-feedback" id="id_error_bonussteppoints[';
          temp += newbonusstepcount + ']" style="display: none;"></div></div> ';

          $("div[data-groupname='bonussstepnumbers_array'] .col-md-9 fieldset div.d-flex").append('<span class="exammanagement_task_spacing"><strong>' + newbonusstepcount + '</strong></span>');
          $("div[data-groupname='bonussteppoints_array']  .col-md-9 fieldset div.d-flex").append(temp);

          $("input[name=bonusstepcount]").val(parseInt($("input[name=bonusstepcount]").val()) + 1);
        }

      });
    },
    removebonusstep: function () { //remove task

      $("#id_remove_bonusstep").click(function () {

        var bonusstepcount = getBonusstepCount();

        if (bonusstepcount > 1) {
          $("div[data-groupname='bonussstepnumbers_array'] .col-md-9 fieldset div.d-flex span:last").remove();
          $("div[data-groupname='bonussteppoints_array'] .col-md-9 fieldset div.d-flex .form-group:last").remove();

          $("input[name=bonusstepcount]").val(parseInt($("input[name=bonusstepcount]").val() - 1));
        }
      });
    }
  };
});
