<script>
$(function( {
  $("button#submit").click(function() {
    $.ajax({
      type: "POST",
      url: $('from').action(),
      data: $('form').serialize(),
      success: function(msg) {
        $('form').html(msg);
      },
      error: function() {
        alert("failure!");
      }
    });
  });
});
</script>