$(window).on("load", function () {
  $('#mail-form').on('submit', function (e) {
    e.preventDefault();
    console.log(e);
  });
});
