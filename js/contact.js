$(window).on('load', function () {
  $('#mail-form').on('submit', function (e) {
    e.preventDefault();
    $('#form-sending').show(), $('#mail-form').hide(), $.ajax({
      method: 'POST',
      url: 'https://submit.carlgo11.com/',
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
