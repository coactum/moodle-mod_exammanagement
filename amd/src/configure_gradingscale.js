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
 * functions for tracking changes of input fields
 *
 * @module      mod_exammanagement/configure_gradingscale
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/notification'], function($) {

  return {
    init: function() {

      var max_points = $("#totalpoints strong").text();

      $(".form-group").on("change", "input", function() {
        var value = $(this).val();
        var bad_input = value.search(/^[0-9]+(\.[0-9]){0,1}$/);

        if (bad_input == -1){
          $(this).val(0);

          require(['core/notification'], function(notification) {
           notification.addNotification({
             message: "Ungültige Punktzahl",
             type: "error"
           });
          });
        }
        if (bad_input != -1 && parseInt(value) > max_points){
          $(this).val(max_points);

          require(['core/notification'], function(notification) {
           notification.addNotification({
             message: "Höchstpunktzahl überschritten",
             type: "error"
           });
          });
        }
      });

      $("#id_submitbutton").click(function(e){

          var inputValue10;
          var inputValue13;
          var inputValue17;
          var inputValue20;
          var inputValue23;
          var inputValue27;
          var inputValue30;
          var inputValue33;
          var inputValue37;
          var inputValue40;

          inputValue10 = parseInt($("#id_gradingsteppoints_10").val());
          inputValue13 = parseInt($("#id_gradingsteppoints_13").val());
          inputValue17 = parseInt($("#id_gradingsteppoints_17").val());
          inputValue20 = parseInt($("#id_gradingsteppoints_20").val());
          inputValue23 = parseInt($("#id_gradingsteppoints_23").val());
          inputValue27 = parseInt($("#id_gradingsteppoints_27").val());
          inputValue30 = parseInt($("#id_gradingsteppoints_30").val());
          inputValue33 = parseInt($("#id_gradingsteppoints_33").val());
          inputValue37 = parseInt($("#id_gradingsteppoints_37").val());
          inputValue40 = parseInt($("#id_gradingsteppoints_40").val());

          if(inputValue10<=inputValue13 || inputValue13 <= inputValue17
            || inputValue17 <= inputValue20 || inputValue20 <= inputValue23
            || inputValue23 <= inputValue27 || inputValue27 <= inputValue30
            || inputValue30 <= inputValue33 || inputValue33 <= inputValue37
            || inputValue37 <= inputValue40){

              e.preventDefault();

              require(['core/notification'], function(notification) {
               notification.addNotification({
                 message: "Punktzahl kann nicht höher oder gleich dem letzten Notenschritt sein",
                 type: "error"
               });
              });
          } else{
            $("form").submit();
          }
      });
    },
  };
});
