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
 * Ticks or unticks all checkboxes when clicking the Select all or Deselect all elements when viewing the response overview
 * and remove unnecessary col-mds.
 *
 * @module      mod_exammanagement/add_participants
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {

   return {
        enable_cb: function() {
           $("#checkboxgroup1").click(function () {
             $('input.checkboxgroup1').not(this).prop('checked', this.checked);
           });
           $("#checkboxgroup2").click(function () {
             $('input.checkboxgroup2').not(this).prop('checked', this.checked);
           });
        },
        remove_form_classes_col: function() {
           $('div.remove_col > div > div').removeClass('col-md-3');
           $('div.remove_col > div > div').removeClass('col-md-9');
        }
    };
});
