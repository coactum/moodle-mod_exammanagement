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
 * functions for disabling roomid field when room is edited
 *
 * @module      mod_exammanagement/edit_defaultroom
 * @copyright   coactum GmbH 2019
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/notification'], function ($) {

  return {
    init: function () {

      var roomid = $('#id_roomid').val();
      var existingroom = $('input[name=existingroom]').val();

      if (roomid && existingroom == true) { // initial disabling of field roomid if room already exists
        $('#id_roomid').prop("disabled", true);
      }

      $('#id_submitbutton').click(function () {  // if submittbutton is presses enable complete form (for moodle purposes)
        $("#id_roomid").prop("disabled", false);
      });
    },
  };
});