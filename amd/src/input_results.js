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
 * functions for tracking changes of input fields, changing focus and checking matr_nr
 *
 * @module      mod_exammanagement/input_results
 * @copyright   coactum GmbH 2018
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/notification'], function($) {

  var getInputId = function (element){
    var id = element.attr('id').split('_').pop();

    return id;
  };

  var getTotalpoints = function() {
    var totalpoints = 0;

    $(".form-group input.form-control").each(function() {
      if(getInputId($(this)) != "matrnr" && $(this).val()){
          totalpoints += parseInt($(this).val());
      }
    });

    return totalpoints;
  };

  var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
  };

  return {
    init: function() {

      var matrnr = $('#id_matrnr').val();

      if(matrnr){ // initial disabling of field matrnr if it already exists (ggf. entfernen wenn fokus klappt)
        $('#id_matrnr').prop( "disabled", true );
      }

      $(".form-group input.checkboxgroup1").each(function() { // initial disabling point fields if some checkbox is already checked
          if ($(this).prop('checked')){
            $(".form-group input.form-control").each(function() {
              if (getInputId($(this)) != "matrnr"){
                $(this).prop( "disabled", true );
              }
            });
          }
      });

      $(":checkbox").change(function() { //if some checkbox is checked/unchecked
          var checked = false;
          var changedId = $(this).prop('id'); // get id of changed checkbox

          $(".form-group input.checkboxgroup1").each(function() { // check if some checkbox is now checked
            if($(this).prop('checked')){
                checked = true;
            }
          });

          if (checked){ // if some checkbox is now checked: uncheck all other checkboxes
            if (changedId == "id_state_nt"){
                $('#id_state_fa').prop('checked', false);
                $('#id_state_ill').prop('checked', false);
            } else if (changedId == "id_state_fa"){
                $('#id_state_nt').prop('checked', false);
                $('#id_state_ill').prop('checked', false);
            } else if (changedId == "id_state_ill"){
                $('#id_state_nt').prop('checked', false);
                $('#id_state_fa').prop('checked', false);
            }

            $(".form-group input.form-control").each(function() { // disable all point-fields and set their value to 0
              if (getInputId($(this)) != "matrnr"){
                $(this).prop( "disabled", true );
                $(this).val(0);

              }
            });
          } else {  // if no checkbix is now checked
            $(".form-group input.form-control").each(function() { // enable all point-fields
              if (getInputId($(this)) != "matrnr"){
                $(this).prop( "disabled", false );
              }
            });
          }
      });

      $(".form-group").on("change", "input", function() { // if some input field changes
        if (getInputId($(this)) != "matrnr"){ // and it is not the field for matrnr

          // check for bad input
          var bad_input = $(this).val().search(/^[0-9]+(\.[0-9]){0,1}$/);

          if (bad_input != -1){
            $("#totalpoints").text(getTotalpoints());
          } else {
            $(this).val(0);

            require(['core/notification'], function(notification) {
             notification.addNotification({
               message: "Ungültige Punktzahl",
               type: "error"
             });
            });
          }

          // check if max points are exceeded
          var current_points = parseInt($(this).val());
          var max_points = parseInt($("#"+"max_points_" + getInputId($(this))).text());

          if(current_points > max_points){
            $(this).val(max_points);
            require(['core/notification'], function(notification) {
             notification.addNotification({
               message: "Höchstpunktzahl überschritten",
               type: "error"
             });
           });
          }
        }

        $('#id_matrnr').blur(function() { // reload page if matrnr is entered

           var matrnr = $(this).val();
           var id = getUrlParameter('id');

           if (matrnr.match(/^\d+$/)){
              location.href = "inputResults.php?id="+id+"&matrnr="+matrnr;
           } else {
             $(this).val('');
             require(['core/notification'], function(notification) {
              notification.addNotification({
                message: "Keine gültiges Matrikelnummernformat",
                type: "error"
              });
            });
           }

        });
      });

      $("#totalpoints").text(getTotalpoints()); // change totalpoints

      if($("input[name='matrval']").val() == 1){
          $("#id_matrnr").focus();
      } else {
          $("#id_points_1").focus();
      }

      $('#id_submitbutton').click(function() {  // if submittbutton is presses enable complete form (for moodle purposes)
        $("#id_matrnr").prop( "disabled", false );
      });
    },
  };
});
