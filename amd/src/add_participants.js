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
 * Ticks or unticks all checkboxes when clicking the select all or deselect all element on viewparticipants.
 *
 * @module      mod_exammanagement/add_participants
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';

export const init = () => {
    $('.mform input[type="checkbox"]').click(function() {
        $('#selectedGroupOneCount').text($('input[type="checkbox"]:checked.checkboxgroup1').not("#checkboxgroup1").length);
        $('#selectedGroupTwoCount').text($('input[type="checkbox"]:checked.checkboxgroup2').not("#checkboxgroup2").length);
        $('#selectedGroupThreeCount').text($('input[type="checkbox"]:checked.checkboxgroup3').not("#checkboxgroup3").length);
    });
};

export const enablecb = () => {
      $("#checkboxgroup1").click(function() {
        $('input.checkboxgroup1').prop('checked', this.checked);
        $('#selectedGroupOneCount').text($('input[type="checkbox"]:checked.checkboxgroup1').not("#checkboxgroup1").length);
      });
      $("#checkboxgroup2").click(function() {
        $('input.checkboxgroup2').not(this).prop('checked', this.checked);
        $('#selectedGroupTwoCount').text($('input[type="checkbox"]:checked.checkboxgroup2').not("#checkboxgroup2").length);
      });
      $("#checkboxgroup3").click(function() {
        $('input.checkboxgroup3').not(this).prop('checked', this.checked);
        $('#selectedGroupThreeCount').text($('input[type="checkbox"]:checked.checkboxgroup3').not("#checkboxgroup3").length);
      });
};

export const togglesection = () => {
    $('.toggable').click(function() {
        $('.' + $(this).attr('id') + '_body').slideToggle("slow");
        $('.' + $(this).attr('id') + '_maximize').toggle();
        $('.' + $(this).attr('id') + '_minimize').toggle();
    });
};
