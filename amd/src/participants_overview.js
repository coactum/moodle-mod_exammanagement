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
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function ($) {

  return {
    init: function () {

      // create input type number elements
      $("input[type=text]").not('#id_room, #id_place').attr("type", "number");

      var styles = {
        "-webkit-appearance": "textfield",
        "-moz-appearance": "textfield",
        "margin-left": "5px",
        "width": "45px"
      };

      $("input[type=number]").css(styles);
      $("input[type=number]").attr("step", "0.01");
      $("input[type=number]").attr("min", "0");

      $('div').removeClass('col-md-3');
      $('div').removeClass('col-md-9');
    },
  };
});
