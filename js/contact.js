$(window).on("load", function () {
  $('#mail-form').on('submit', function (e) {
    e.preventDefault();
    console.log($(this).serialize());
    var form = $(this);
    $.ajax({
      method: 'GET',
      url: 'https://submit.carlgo11.com/',
      dataType: "text",
      cache: false,
      data: form.serialize(),
      success: function () {
        $('#form-success').fadeIn().alert();
        $('#mail-form').hide();

      },
      error: function () {
        $('#form-error').fadeIn().alert();
      }
    });
  });
});