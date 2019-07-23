$(window).on("load", function () {
  $('#mail-form').on('submit', function (e) {
    e.preventDefault();
    console.log($(this).serialize());
    var form = $(this);
    $.ajax({
      method: 'GET',
      url: 'http://127.0.0.1/carlgo11/new-site/mail/mail.php',
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
