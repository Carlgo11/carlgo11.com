$(window).on('load', function () {
  $('#mail-form').on('submit', function (e) {
    e.preventDefault();
    $('#form-sending').show(), $('#mail-form').hide(), $.ajax({
      method: 'POST',
      url: 'http://127.0.0.1:5491/',
      dataType: 'text',
      cache: !1,
      data: $(this).serialize(),
      success: function () {
        $('#form-sending').hide(), $('#form-success').fadeIn().alert()
      },
      error: function () {
        $('#form-sending').hide(), $('#form-error').fadeIn().alert()
      }
    })
  })
})
