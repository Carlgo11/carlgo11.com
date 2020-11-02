$(window).on("load", function () {

  /* On form submit */
  $('#mail-form').on('submit', function (e) {
    e.preventDefault();
    var form = $(this);

$('#form-sending').show();
$('#mail-form').hide();

    /* Send form data */
    $.ajax({
      method: 'POST',
      url: 'https://submit.carlgo11.com/',
      dataType: "text",
      cache: false,
      data: form.serialize(),

      success: function () {
        $('#form-sending').hide();
        $('#form-success').fadeIn().alert();
      },

      error: function () {
        $('#form-sending').hide();
        $('#form-error').fadeIn().alert();
      }
    });

  });

});
