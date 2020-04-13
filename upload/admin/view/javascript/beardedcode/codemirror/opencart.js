$(document).ready(function () {
  $('.summernote').each(function () {
    var editor = CodeMirror.fromTextArea(this, {
      height: "350px",
      lineNumbers: true,
      lineWrapping: true,
      mode:  "htmlmixed",
      htmlMode: true,
      theme: "monokai",
      autoRefresh: true
    });
  });
});