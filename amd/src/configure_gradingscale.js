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
 * Creating input type number elements and remove cols from moodle form group layout.
 *
 * @module      mod_exammanagement/configure_gradingscale
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';

export const init = (totalpoints) => {
    // Create input type number elements.
    $("form.mform input[type=text]").attr("type", "number");

    var styles = {
      "-webkit-appearance": "textfield",
      "-moz-appearance": "textfield",
      "margin": "0px"
    };

    $("form.mform input[type=number]").css(styles);
    $("form.mform input[type=number]").attr("step", "0.1");
    $("form.mform input[type=number]").attr("min", "0");
    $("form.mform input[type=number]").attr("max", totalpoints);

    // Remove cols from form layout.
    $('.exammanagement_gradingscale_steps_spacing .form-group div').removeClass('col-md-3');
    $('.exammanagement_gradingscale_steps_spacing .form-group div').removeClass('col-md-9');
    $('.exammanagement_gradingscale_steps_spacing .form-group').removeClass('row');

};
