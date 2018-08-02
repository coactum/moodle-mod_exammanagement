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
      if(getInputId($(this)) != "matrnr"){
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

      $(".form-group input.form-control").each(function() {
        if (getInputId($(this)) != "matrnr"){
          $(this).prop( "disabled", true );
        }
      });
      $(".form-group input.checkboxgroup1").each(function() {
        $(this).prop( "disabled", true );
      });
      $("#id_submitbutton").each(function() {
        $(this).prop( "disabled", true );
      });

      $(".form-group").on("change", "input", function() {
        if (getInputId($(this)) == "matrnr"){
           var matrnr = $(this).val();
           var id = getUrlParameter('id');
           alert(matrnr);
           alert(id);

           location.href = "inputResults.php?id="+id+"?matrnr="+matrnr;

            // $.ajax({
            //    url: "ajaxCheckMatrNr.php?id="+id+"?matrnr="+matrnr,
            //    cache: false,
            //    success: function(result){
            //
            //      alert('testsuccess');
            //      alert(result);
            //
            //      // input results from db if already entered
            //
            //      // enable form
            //
            //      $(".form-group input.form-control").each(function() {
            //        if (getInputId($(this)) != "matrnr"){
            //          $(this).prop( "disabled", false );
            //        }
            //      });
            //      $(".form-group input.checkboxgroup1").each(function() {
            //        $(this).prop( "disabled", false );
            //      });
            //      $("#id_submitbutton").each(function() {
            //        $(this).prop( "disabled", false );
            //      });
            //
            //    }
            //  });
        }

        if (getInputId($(this)) != "matrnr"){
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
        }
      });
    },
    check_max_points: function() {
      $(".form-group").on("change", "input", function() {

        var id = getInputId($(this));
        if(id != "matrnr"){
          var current_points = parseInt($(this).val());
          var max_points = parseInt($("#"+"max_points_"+id).text());

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
      });
    },
  };
});
