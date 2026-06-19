<div class="internal-page">
  <div class="internal-content" style="max-width: 640px;">
    <!-- Upload Step -->
    <div id="import-upload">
      <?= ui_card(ui_file_upload('file', [
        'accept' => '.csv',
        'formats' => __('formats_csv'),
        'info' => __('scores_import_format_info'),
        'required' => true,
      ]) . '<div style="display:flex;justify-content:flex-end;gap:8px;padding-top:8px">
            ' . ui_button(__('scores_import_cancel'), 'secondary', 'md', ['href' => 'game-scores.php?id=' . $gameId . ($leaderboardId ? '&leaderboard_id=' . $leaderboardId : '')]) . '
            ' . ui_button(__('scores_import_next'), 'primary', 'md', ['icon' => 'fa fa-arrow-right', 'attrs' => ['id' => 'btn-import-next', 'disabled' => '', 'onclick' => 'parseCsv()']]) . '
          </div>', [
        'title' => __('scores_import_upload_title'),
        'footer' => '<div class="space-y-3 w-full">
          <p class="text-xs font-semibold text-[var(--text-color)] uppercase tracking-wide">' . __('scores_import_format_title') . '</p>
          <div class="text-xs text-[var(--text-color-secondary)] space-y-1.5">
            <p>' . __('scores_import_format_header') . '</p>
            <div class="overflow-x-auto"><code class="block bg-[var(--bg-color-offset)] rounded px-3 py-2 text-[var(--text-color)]">username,score,ip_encrypted,country,created_at,sign,tags,leaderboard_id,data,env</code></div>
            <p class="mt-2">' . __('scores_import_format_cols') . '</p>
            <ul class="list-disc list-inside space-y-1 pl-1">
              <li><strong>username</strong>: ' . __('scores_import_format_col_username') . '</li>
              <li><strong>score</strong>: ' . __('scores_import_format_col_score') . '</li>
              <li><strong>ip_encrypted</strong>: ' . __('scores_import_format_col_ip') . '</li>
              <li><strong>country</strong>: ' . __('scores_import_format_col_country') . '</li>
              <li><strong>created_at</strong>: ' . __('scores_import_format_col_date') . '</li>
              <li><strong>sign</strong>: ' . __('scores_import_format_col_sign') . '</li>
              <li><strong>tags</strong>: ' . __('scores_import_format_col_tags') . '</li>
              <li><strong>leaderboard_id</strong>: ' . __('scores_import_format_col_leaderboard') . '</li>
              <li><strong>data</strong>: ' . __('scores_import_format_col_data') . '</li>
              <li><strong>env</strong>: ' . __('scores_import_format_col_env') . '</li>
            </ul>
          </div>
        </div>',
        'footer_right' => true,
      ]) ?>
    </div>

    <!-- Parsing Step -->
    <div id="import-parsing" style="display:none">
      <?= ui_card('<div class="space-y-4">
          <div class="flex items-center gap-3 text-blue-600">
            <i class="fas fa-spinner fa-spin text-2xl"></i>
            <span>' . __('scores_import_parsing_text') . '</span>
          </div>
          <div id="import-parsing-bar-container" class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
            <div id="import-parsing-bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
          </div>
        </div>', [
        'title' => __('scores_import_parsing_title'),
      ]) ?>
    </div>

    <!-- Preview Step -->
    <div id="import-preview" style="display:none">
      <?= ui_card('<div class="space-y-4">
          <div id="import-preview-summary" class="grid grid-cols-3 gap-4">
            <div class="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
              <div id="import-preview-valid" class="text-2xl font-bold text-green-600">0</div>
              <div class="text-xs text-[var(--text-color-secondary)]">' . __('scores_import_preview_valid') . '</div>
            </div>
            <div class="text-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
              <div id="import-preview-errors" class="text-2xl font-bold text-red-600">0</div>
              <div class="text-xs text-[var(--text-color-secondary)]">' . __('scores_import_preview_errors') . '</div>
            </div>
            <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
              <div id="import-preview-total" class="text-2xl font-bold text-blue-600">0</div>
              <div class="text-xs text-[var(--text-color-secondary)]">' . __('scores_import_preview_total') . '</div>
            </div>
          </div>
          <div id="import-preview-table-wrapper" style="display:none">
            <p class="text-sm font-semibold text-[var(--text-color)] mb-2">' . __('scores_import_preview_sample') . '</p>
            <div class="overflow-x-auto">
              <table class="w-full text-sm">
                <thead>
                  <tr class="border-b border-[var(--border-color)]">
                    <th class="text-left py-2 px-3 text-[var(--text-color-secondary)]">#</th>
                    <th class="text-left py-2 px-3 text-[var(--text-color-secondary)]">' . __('scores_import_preview_player') . '</th>
                    <th class="text-left py-2 px-3 text-[var(--text-color-secondary)]">' . __('scores_import_preview_score') . '</th>
                    <th class="text-left py-2 px-3 text-[var(--text-color-secondary)]">' . __('scores_import_preview_country') . '</th>
                    <th class="text-left py-2 px-3 text-[var(--text-color-secondary)]">' . __('scores_import_preview_date') . '</th>
                  </tr>
                </thead>
                <tbody id="import-preview-table-body"></tbody>
              </table>
            </div>
          </div>
          <div id="import-preview-errors-wrapper" style="display:none">
            <p class="text-sm font-semibold text-red-600 mb-2"><i class="fas fa-exclamation-triangle"></i> ' . __('scores_import_preview_error_list') . '</p>
            <div id="import-preview-errors-list" class="max-h-40 overflow-y-auto text-xs text-red-600 bg-red-50 dark:bg-red-900/20 rounded-lg p-3"></div>
          </div>
          <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:8px">
            ' . ui_button(__('scores_import_back'), 'secondary', 'md', ['attrs' => ['onclick' => 'goToUpload()']]) . '
            ' . ui_button(__('scores_import_confirm'), 'primary', 'md', ['icon' => 'fa fa-cloud-upload-alt', 'attrs' => ['id' => 'btn-import-confirm', 'onclick' => 'executeImport()']]) . '
          </div>
        </div>', [
        'title' => __('scores_import_preview_title'),
      ]) ?>
    </div>

    <!-- Importing Step -->
    <div id="import-executing" style="display:none">
      <?= ui_card('<div class="space-y-4">
          <div class="flex items-center gap-3 text-blue-600">
            <i class="fas fa-spinner fa-spin text-2xl"></i>
            <span id="import-executing-text">' . __('scores_import_executing_text') . '</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
            <div id="import-executing-bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 30%"></div>
          </div>
        </div>', [
        'title' => __('scores_import_executing_title'),
      ]) ?>
    </div>

    <!-- Import Complete -->
    <div id="import-complete" style="display:none">
      <?= ui_card('<div class="space-y-4">
          <div class="flex items-center gap-3 text-green-600">
            <i class="fas fa-check-circle text-2xl"></i>
            <span>' . __('scores_import_complete_text') . '</span>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
              <div id="import-complete-imported" class="text-2xl font-bold text-green-600">0</div>
              <div class="text-xs text-[var(--text-color-secondary)]">' . __('scores_import_complete_imported') . '</div>
            </div>
            <div class="text-center p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
              <div id="import-complete-skipped" class="text-2xl font-bold text-yellow-600">0</div>
              <div class="text-xs text-[var(--text-color-secondary)]">' . __('scores_import_complete_skipped') . '</div>
            </div>
          </div>
          <div id="import-complete-errors-wrapper" style="display:none">
            <p class="text-sm font-semibold text-red-600 mb-2"><i class="fas fa-exclamation-triangle"></i> ' . __('scores_import_complete_error_list') . '</p>
            <div id="import-complete-errors-list" class="max-h-40 overflow-y-auto text-xs text-red-600 bg-red-50 dark:bg-red-900/20 rounded-lg p-3"></div>
          </div>
          <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:8px">
            ' . ui_button(__('scores_import_complete_back'), 'primary', 'md', ['href' => 'game-scores.php?id=' . $gameId . ($leaderboardId ? '&leaderboard_id=' . $leaderboardId : '')]) . '
          </div>
        </div>', [
        'title' => __('scores_import_complete_title'),
      ]) ?>
    </div>

    <!-- Import Error -->
    <div id="import-error" style="display:none">
      <?= ui_card('<div class="space-y-4">
          <div class="flex items-center gap-3 text-red-600">
            <i class="fas fa-exclamation-circle text-2xl"></i>
            <span id="import-error-text">' . __('scores_import_error_text') . '</span>
          </div>
          <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:8px">
            ' . ui_button(__('scores_import_retry'), 'primary', 'md', ['icon' => 'fa fa-redo', 'attrs' => ['onclick' => 'resetImport()']]) . '
          </div>
        </div>', [
        'title' => __('scores_import_error_title'),
      ]) ?>
    </div>
  </div>
</div>

<script>
var importTmpFile = null;
var csrfToken = '<?= csrf_token() ?>';

document.addEventListener('DOMContentLoaded', function() {
  var fileInput = document.getElementById('file-input');
  if (fileInput) {
    fileInput.addEventListener('change', function() {
      document.getElementById('btn-import-next').disabled = !fileInput.files || !fileInput.files[0];
    });
  }
});

function showStep(stepId) {
  ['import-upload', 'import-parsing', 'import-preview', 'import-executing', 'import-complete', 'import-error'].forEach(function(id) {
    document.getElementById(id).style.display = id === stepId ? 'block' : 'none';
  });
}

function parseCsv() {
  var fileInput = document.getElementById('file-input');
  if (!fileInput.files || !fileInput.files[0]) return;

  showStep('import-parsing');
  document.getElementById('import-parsing-bar').style.width = '30%';

  var formData = new FormData();
  formData.append('action', 'parse');
  formData.append('file', fileInput.files[0]);
  formData.append('csrf_token', csrfToken);

  fetch('game-scores-import.php?id=<?= $gameId ?><?= $leaderboardId ? '&leaderboard_id=' . $leaderboardId : '' ?>', {
    method: 'POST',
    body: formData
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    document.getElementById('import-parsing-bar').style.width = '100%';

    if (data.success) {
      importTmpFile = data.tmp_file;
      setTimeout(function() { showPreview(data); }, 300);
    } else {
      showImportError(data.error);
    }
  })
  .catch(function() {
    showImportError(<?= json_encode(__('scores_network_error')) ?>);
  });
}

function showPreview(data) {
  showStep('import-preview');

  document.getElementById('import-preview-valid').textContent = data.valid;
  document.getElementById('import-preview-errors').textContent = data.errors_count;
  document.getElementById('import-preview-total').textContent = data.total;

  if (data.preview && data.preview.length > 0) {
    document.getElementById('import-preview-table-wrapper').style.display = 'block';
    var tbody = document.getElementById('import-preview-table-body');
    tbody.innerHTML = '';
    data.preview.forEach(function(row) {
      var tr = document.createElement('tr');
      tr.className = 'border-b border-[var(--border-color)]';
      tr.innerHTML = '<td class="py-2 px-3 text-[var(--text-color-secondary)]">' + row.row + '</td>' +
        '<td class="py-2 px-3 text-[var(--text-color)]">' + escapeHtml(row.player) + '</td>' +
        '<td class="py-2 px-3 text-[var(--text-color)]">' + escapeHtml(String(row.score)) + '</td>' +
        '<td class="py-2 px-3 text-[var(--text-color-secondary)]">' + escapeHtml(row.country || '-') + '</td>' +
        '<td class="py-2 px-3 text-[var(--text-color-secondary)]">' + escapeHtml(row.date || '-') + '</td>';
      tbody.appendChild(tr);
    });
  }

  if (data.errors_count > 0 && data.errors && data.errors.length > 0) {
    document.getElementById('import-preview-errors-wrapper').style.display = 'block';
    var errList = document.getElementById('import-preview-errors-list');
    errList.innerHTML = '';
    data.errors.forEach(function(err) {
      var div = document.createElement('div');
      div.className = 'mb-1';
      div.textContent = 'Riga ' + err.row + ': ' + err.error;
      errList.appendChild(div);
    });
  } else {
    document.getElementById('import-preview-errors-wrapper').style.display = 'none';
  }

  if (data.valid === 0) {
    document.getElementById('btn-import-confirm').disabled = true;
  }
}

function executeImport() {
  showStep('import-executing');
  document.getElementById('import-executing-bar').style.width = '30%';

  var formData = new FormData();
  formData.append('action', 'import');
  formData.append('tmp_file', importTmpFile);
  formData.append('csrf_token', csrfToken);

  fetch('game-scores-import.php?id=<?= $gameId ?><?= $leaderboardId ? '&leaderboard_id=' . $leaderboardId : '' ?>', {
    method: 'POST',
    body: formData
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    document.getElementById('import-executing-bar').style.width = '100%';

    if (data.success) {
      setTimeout(function() { showImportComplete(data); }, 300);
    } else {
      showImportError(data.error);
    }
  })
  .catch(function() {
    showImportError(<?= json_encode(__('scores_network_error')) ?>);
  });
}

function showImportComplete(data) {
  showStep('import-complete');
  document.getElementById('import-complete-imported').textContent = data.imported;
  document.getElementById('import-complete-skipped').textContent = data.skipped;

  if (data.errors && data.errors.length > 0) {
    document.getElementById('import-complete-errors-wrapper').style.display = 'block';
    var errList = document.getElementById('import-complete-errors-list');
    errList.innerHTML = '';
    data.errors.forEach(function(err) {
      var div = document.createElement('div');
      div.className = 'mb-1';
      div.textContent = 'Riga ' + err.row + ': ' + err.error;
      errList.appendChild(div);
    });
  }
}

function showImportError(msg) {
  showStep('import-error');
  document.getElementById('import-error-text').textContent = msg;
}

function goToUpload() {
  showStep('import-upload');
  uiFileUploadClear('file');
  document.getElementById('btn-import-next').disabled = true;
}

function resetImport() {
  importTmpFile = null;
  showStep('import-upload');
  uiFileUploadClear('file');
  document.getElementById('btn-import-next').disabled = true;
}

function escapeHtml(str) {
  var div = document.createElement('div');
  div.appendChild(document.createTextNode(str));
  return div.innerHTML;
}
</script>

<?= ui_file_upload_js() ?>
