define(['jquery'], function($) {
  enable_cb();
  $("#checkboxgroup1").click(enable_cb);
});

function enable_cb() { 
  if (this.checked) {
    $("input.checkboxgroup1").removeAttr("checked");
  } else {
    $("input.checkboxgroup1").attr("checked", true);
  }
}