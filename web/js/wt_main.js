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
      $this = $(this);
      if (confirm('Are you sure you want to delete this item?')) {
        $.ajax({
          url: $(this).attr('url'),
          type: 'DELETE',
          success: function(result) {
            $this.closest('tr').remove();
          },
          error: function(xhr) {
            flash_err('div#history', 'Delete track failed!');
          }
        });
      }
    });
    
    flash_success = function (container, msg) {
      $(container).append('<div class="alert alert-success alert-dismissible fade in" role="alert">\
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\
        <p>' + msg + '</p>\
        </div>\
      ');
    };
      
    flash_err = function (container, msg) {
      $(container).append('<div class="alert alert-danger alert-dismissible fade in" role="alert">\
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\
        <p>' + msg + '</p>\
        </div>\
      ');
    };
    
    $('#editTrackModal').on('show.bs.modal', function (e) {
      //console.log(e.relatedTarget);
      $btn = $(e.relatedTarget);
      var weightVal = $btn.closest('tr').children().eq(1).html();
      var dateVal = $btn.closest('tr').children().eq(0).html();
      $('input#newWeight').val(weightVal);
      $('input#newDate').val(dateVal);
    })
    
    reloadHistory = function () {
      url = baseUrl + 'user/' + wt_userId + '/tracks/' + curPage;
      
      $.ajax({
        type: "GET",
        url: url,
        success: function(msg) {
          //console.log(msg);

          var tracks = msg.tracks; 
          $('table#history_table tbody tr').remove();
          
          $.each(tracks, function ( index, obj) {
            var date = new Date(obj.date);
            var day = date.getDate();
            var monthIndex = date.getMonth() + 1;
            if (monthIndex < 10) monthIndex = '0' + monthIndex;
            var year = date.getFullYear();
            var dateString = day + '/' + monthIndex + '/' + year;
            
            var action_url = baseUrl + 'user/' + wt_userId + '/track/' + year + '-' + monthIndex + '-' + day;
            var del = '<button type="button" class="btn btn-default btn-sm rowDelButton" url="'+action_url+'">Delete</button>';
            var edit = '<button type="button" class="btn btn-default btn-sm rowEditButton" url="'+action_url+'" \
              data-toggle="modal" data-target="#editTrackModal">Edit</button>';
            
            $('<tr>\
              <td>' + dateString + '</td>\
              <td>' + obj.weight + '</td>\
              <td>' + edit + del + '</td>\
              </tr>\
            ').appendTo($('table#history_table tbody'));
          });
          
          $('ul.pagination li').remove();
          var pageCnt = msg.pagination.count;
          //var curPage = msg.pagination.current;
          
          var $pre = $('<li>\
            <a href="#" aria-label="Previous">\
              <span aria-hidden="true">&laquo;</span>\
            </a>\
          </li>\
          ');
          if (curPage == 1) {
            $pre.addClass('disabled');
          }
          $pre.appendTo($('ul.pagination'));
          
          for (i=1; i<=pageCnt; i++) {
            $pg = $('<li><a href="#">'+i+'</a></li>');
            if (curPage == i) {
              $pg.addClass('active');
            }
            $pg.appendTo($('ul.pagination'));
          }
          
          var $next = $('<li>\
            <a href="#" aria-label="Next">\
              <span aria-hidden="true">&raquo;</span>\
            </a>\
          </li>\
          ');
          if (curPage == pageCnt) {
            $next.addClass('disabled');
          }
          $next.appendTo($('ul.pagination'));
        },
        error: function(xhr) {
          flash_err('div#history', 'History reload failed!');
        }
      });
    }

    $("form#form_goal button").click(function (e) {
      e.preventDefault();
      var goalVal = $('form input#goal')[0].value;
      if (goalVal) {
        $.ajax({
          type: "POST",
          url: baseUrl + 'user/' + wt_userId + '/goal',
          data: JSON.stringify({ goal: goalVal }),
          success: function(msg) {
            flash_success('form#form_goal', 'Goal ' + msg.goal + ' set successfully!');
          },
          error: function(xhr) {
            flash_err('form#form_goal', 'Goal set failed!');
          }
        });
      }
    });
    
    // new track
    $('#newTrackButton').click(function (e) {
      var weightVal = $('form#form_new_track input#weight')[0].value;
      var dateVal = $('form#form_new_track input#date')[0].value;
      if (weightVal && dateVal) {
        var dateAr = dateVal.split('/');
        var date = new Date(dateAr[2] + '-' + dateAr[1] + '-' + dateAr[0]);
        //console.log(weightVal);
        //console.log(JSON.stringify({ weight: weightVal, date: date }));
        
        $.ajax({
            type: "POST",
            url: baseUrl + 'user/' + wt_userId + '/track',
            data: JSON.stringify({ weight: weightVal, date: date }),
            success: function(msg) {
              flash_success('div#history', dateVal +' weight ' + msg.weight + ' KG tracked successfully!');
              reloadHistory();
            },
            error: function(xhr) {
              flash_err('div#history', 'Track create failed, please try again.');
            }
          });
        
          $('#newTrackModal').modal('hide');
      }
    });

    // update track
    $('#editTrackButton').click(function (e) {
      var weightVal = $('form#form_edit_track input#newWeight')[0].value;
      var dateVal = $('form#form_edit_track input#newDate')[0].value;
      if (weightVal && dateVal) {
        var dateAr = dateVal.split('/');
        var date = new Date(dateAr[2] + '-' + dateAr[1] + '-' + dateAr[0]);
        //console.log(weightVal);
        //console.log(JSON.stringify({ weight: weightVal, date: date }));
        
        $.ajax({
            type: "PUT",
            url: baseUrl + 'user/' + wt_userId + '/track/' + date,
            data: JSON.stringify({ weight: weightVal }),
            success: function(msg) {
              flash_success('div#history', dateVal +' weight updated to' + msg.weight + ' KG successfully!');
              reloadHistory();
            },
            error: function(xhr) {
              flash_err('div#history', 'Track update failed, please try again.');
            }
          });
        
          $('#editTrackModal').modal('hide');
      }
    });
    
    $('.pagination').on('click', 'a', function (e) {
      e.preventDefault();
      curPage = $(this).html();
      reloadHistory();
    });
  });
})(jQuery);