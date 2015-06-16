(function($) {
  $(document).ready(function() {
    $('#wtTab a').click(function (e) {
      e.preventDefault();
      switch( $(this).attr('aria-controls')) {
        case 'history':
          reloadHistory();
          break;
        case 'home':
          reloadTrend();
          break;
        case 'goal':
        default:
          break;
      }
      
      $(this).tab('show');
    });
    
    $('table#history_table').on('click', '.rowDelButton', function (e) {
      e.preventDefault();
      if (confirm('Are you sure you want to delete this item?')) {
        $.ajax({
          url: $(this).attr('url'),
          type: 'DELETE',
          success: function(result) {
            $(this).closest('tr').remove();
          },
          error: function(xhr) {
            $('div#history').append('<div class="alert alert-danger alert-dismissible fade in" role="alert">\
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\
              <p>Delete track failed due to: </p>\
              <p>' + xhr.responseJSON.message +'</p>\
              </div>\
            ');
          }
        });
      }
    });
    
    $('table#history_table').on('click', '.rowEditButton', function (e) {
      alert($(this).attr('url'));
      e.preventDefault();
    });
    
    reloadHistory = function() {
      $.ajax({
        type: "GET",
        url: '/app_dev.php/user/' + wt_userId + '/tracks',
        success: function(msg) {
          //console.log(msg);
          $.each(msg, function ( index, obj) {
            var date = new Date(obj.date);
            var day = date.getDate();
            var monthIndex = date.getMonth() + 1;
            if (monthIndex < 10) monthIndex = '0' + monthIndex;
            var year = date.getFullYear();
            var dateString = day + '/' + monthIndex + '/' + year;
            
            var action_url = '/app_dev.php/user/' + wt_userId + '/track/' + year + '-' + monthIndex + '-' + day;
            var del = '<button type="button" class="btn btn-default btn-sm rowDelButton" url="'+action_url+'">Delete</button>';
            var edit = '<button type="button" class="btn btn-default btn-sm rowEditButton" url="'+action_url+'">Edit</button>';
            
            $('<tr>\
              <td>' + dateString + '</td>\
              <td>' + obj.weight + '</td>\
              <td>' + edit + del + '</td>\
              </tr>\
            ').appendTo($('table#history_table tbody'));
          });
        },
        error: function(xhr) {
          $('div#history').append('<div class="alert alert-danger alert-dismissible fade in" role="alert">\
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\
              <p>History load failed due to: </p>\
              <p>' + xhr.responseJSON.message +'</p>\
              </div>\
          ');
        }
      });
    }

    $("form#form_goal button").click(function (e) {
      e.preventDefault();
      var goalVal = $('form input#goal')[0].value;
      if (goalVal) {
        $.ajax({
          type: "POST",
          url: '/app_dev.php/user/' + wt_userId + '/goal',
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