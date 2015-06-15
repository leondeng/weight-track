(function($) {
  $(document).ready(function() {
    $("form button#form_save").click(function() {
      var goalVal = $('form input#form_goal')[0].value;
      if (goalVal) {
        $.ajax({
          type: "POST",
          url: '/app_dev.php/user/1/goal',
          data: JSON.stringify({ goal: goalVal }),
          success: function(msg) {
            $('form').replaceWith('<div class="wt_flash"><span class="success">Goal ' + msg.goal + ' set successfuly!</span></div>');
          },
          error: function(msg) {
            $('form').replaceWith('<div class="wt_flash"><span class="failed">Goal set failed!</span></div>');
          }
        });
        return false;
      }
    });
  });
})(jQuery);