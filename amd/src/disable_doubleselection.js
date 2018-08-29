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
 * Disables double selection of same rooms.
 *
 * @module      mod_exammanagement/disable_doubleselection
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {

   return {
        disable_doubleselection: function() {
          $('input:checked').each(function() { //check all input checkboxes fields if one is checked and disable its siblings
              $("label:contains('"+$.trim($(this).parent().text())+"') input:not(:checked)").prop( "disabled", true);
          });
          $("label").on("change", "input", function() { //if input checkbox changes state
            var label = $.trim($(this).parent().text());


             if($("label:contains('"+ label +"') input").prop('disabled')){
               $("label:contains('"+ label +"') input:not(:checked)").prop( "disabled", false);
             } else {
               $("label:contains('"+ label +"') input:not(:checked)").prop( "disabled", true);
             }
          });
        }
    };

});
