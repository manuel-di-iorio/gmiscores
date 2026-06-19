<script>
document.addEventListener('DOMContentLoaded', function() {
  var envSelect = document.getElementById('export-env');
  if (envSelect) {
    envSelect.value = <?= json_encode($envFilter ?: '') ?>;
  }
});
</script>
