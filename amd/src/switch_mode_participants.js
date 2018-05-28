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
 * switching between course and file import
 *
 * @module      mod_exammanagement/switch_mode_participants
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {

   return {
        switch_mode_participants: function() {

            $("#switchmode_to_import").click(function(){
                $(".viewparticipants").toggle();
                $(".importparticipants").toggle();

            });
            $("#switchmode_to_view").click(function(){
                $(".viewparticipants").toggle();
                $(".importparticipants").toggle();

            });
        }
    };

});
