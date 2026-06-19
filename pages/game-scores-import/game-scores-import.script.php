<script>
// Drag and drop support
document.addEventListener('DOMContentLoaded', function() {
  var dropzone = document.getElementById('import-dropzone');
  if (!dropzone) return;

  ['dragenter', 'dragover'].forEach(function(evt) {
    dropzone.addEventListener(evt, function(e) {
      e.preventDefault();
      e.stopPropagation();
      dropzone.classList.add('border-blue-500', 'bg-blue-50');
    });
  });

  ['dragleave', 'drop'].forEach(function(evt) {
    dropzone.addEventListener(evt, function(e) {
      e.preventDefault();
      e.stopPropagation();
      dropzone.classList.remove('border-blue-500', 'bg-blue-50');
    });
  });

  dropzone.addEventListener('drop', function(e) {
    var files = e.dataTransfer.files;
    if (files && files[0]) {
      var input = document.getElementById('import-file-input');
      var dt = new DataTransfer();
      dt.items.add(files[0]);
      input.files = dt.files;
      onFileSelected(input);
    }
  });
});
</script>
