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
 * Disabling room id field if room is edited.
 *
 * @module      mod_exammanagement/edit_defaultroom
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';

export const init = () => {
  var roomid = $('#id_roomid').val();
  var existingroom = $('input[name=existingroom]').val();

  if (roomid && existingroom == true) { // Initial disabling of field roomid if room already exists.
    $('#id_roomid').prop("disabled", true);
  }

  $('#id_submitbutton').click(function() { // If submit button is pressed enable complete form.
    $("#id_roomid").prop("disabled", false);
  });
};
