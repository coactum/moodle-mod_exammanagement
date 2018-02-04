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
 * @module      mod_exammanagement/switch_importmode
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {

   return {
        switch_mode: function() {
            $(".file").hide();

/*
            $("#switch_importmode").click(function(){ //not working (maybe because of Ajax?)
                $(".course").toggle();
                $(".file").toggle();

            });
 */

            $("#switch_to_file").click(function(){
                $(".course").hide();
                $(".file").show();
            });
            $("#switch_to_course").click(function(){
                $(".file").hide();
                $(".course").show();
            });
        }
    };

});