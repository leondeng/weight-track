(function($) {
  $(document).ready(function() {
    $('#wtTab a').click(function (e) {
      e.preventDefault();
      $(this).tab('show');
    });

    $("form#form_goal button").click(function (e) {
      e.preventDefault();
      var goalVal = $('form input#goal')[0].value;
      if (goalVal) {
        $.ajax({
          type: "POST",
          url: '/app_dev.php/user/1/goal',
          data: JSON.stringify({ goal: goalVal }),
          success: function(msg) {
            $('form').prepend('<div class="alert alert-success alert-dismissible fade in" role="alert">\
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\
                <p>Goal ' + msg.goal + ' set successfully !</p>\
                </div>\
            ');
          },
          error: function(xhr) {
            $('form').prepend('<div class="alert alert-danger alert-dismissible fade in" role="alert">\
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\
                <p>Goal set failed due to: </p>\
                <p>' + xhr.responseJSON.message +'</p>\
                <p>Please try again.</p>\
                </div>\
            ');
          }
        });
      }
    });
  });
})(jQuery);