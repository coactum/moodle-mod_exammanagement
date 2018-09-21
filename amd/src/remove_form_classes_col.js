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
 * Removes form classes col-md 3 and 9 for better layout
 *
 * @module      mod_exammanagement/remove_form_classes_col
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {

   return {
        remove_form_classes_col: function() {
           $('div').removeClass('col-md-3');
           $('div').removeClass('col-md-9');

           $('form > .form-group > div:first-child').addClass('col-md-3');
           $('form > .form-group > div:last-child').addClass('col-md-9');
          }
    };

});
