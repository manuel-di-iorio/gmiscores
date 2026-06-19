<div class="internal-page">
  <div class="internal-content" style="max-width: 640px;">
    <!-- Export Options -->
    <div id="export-options">
      <?= ui_card('<div class="space-y-4">
          <div>
            <label class="block font-semibold mb-1.5 text-sm text-[var(--text-color)]">' . __('scores_export_option_env') . '</label>
            <select id="export-env" class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)]">
              <option value="">' . __('scores_export_option_env_all') . '</option>
              <option value="production">' . __('scores_env_production') . '</option>
              <option value="test">' . __('scores_env_test') . '</option>
            </select>
          </div>
          <div id="export-summary" class="text-sm text-[var(--text-color-secondary)]" style="display:none">
            <i class="fas fa-info-circle"></i> <span id="export-summary-text"></span>
          </div>
          <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:8px">
            ' . ui_button(__('scores_export_cancel'), 'secondary', 'md', ['href' => 'game-scores.php?id=' . $gameId . ($leaderboardId ? '&leaderboard_id=' . $leaderboardId : '')]) . '
            ' . ui_button(__('scores_export_start'), 'primary', 'md', ['icon' => 'fa fa-cloud-download-alt', 'attrs' => ['id' => 'btn-start-export', 'onclick' => 'startExport()']]) . '
          </div>
        </div>', [
        'title' => __('scores_export_options_title'),
      ]) ?>
    </div>

    <!-- Export Progress -->
    <div id="export-progress" style="display:none">
      <?= ui_card('<div class="space-y-4">
          <div id="export-progress-bar-container" class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
            <div id="export-progress-bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
          </div>
          <div id="export-progress-text" class="text-sm text-[var(--text-color-secondary)]">
            <i class="fas fa-spinner fa-spin"></i> ' . __('scores_export_progress_initializing') . '
          </div>
          <div id="export-progress-details" class="text-xs text-[var(--text-color-secondary)]" style="display:none">
            <span id="export-progress-count">0</span> / <span id="export-progress-total">0</span> ' . __('scores_export_progress_rows') . '
          </div>
        </div>', [
        'title' => __('scores_export_progress_title'),
      ]) ?>
    </div>

    <!-- Export Complete -->
    <div id="export-complete" style="display:none">
      <?= ui_card('<div class="space-y-4">
          <div class="flex items-center gap-3 text-green-600">
            <i class="fas fa-check-circle text-2xl"></i>
            <span id="export-complete-text">' . __('scores_export_complete_text') . '</span>
          </div>
          <div class="text-sm text-[var(--text-color-secondary)]">
            <span id="export-complete-count">0</span> ' . __('scores_export_complete_rows') . '
          </div>
          <div id="export-complete-actions" style="display:flex;justify-content:flex-end;gap:8px;padding-top:8px">
            ' . ui_button(__('scores_export_back'), 'secondary', 'md', ['href' => 'game-scores.php?id=' . $gameId . ($leaderboardId ? '&leaderboard_id=' . $leaderboardId : '')]) . '
            ' . ui_button(__('scores_export_download'), 'primary', 'md', ['icon' => 'fa fa-download', 'attrs' => ['id' => 'btn-download-export']]) . '
          </div>
        </div>', [
        'title' => __('scores_export_complete_title'),
      ]) ?>
    </div>

    <!-- Export Error -->
    <div id="export-error" style="display:none">
      <?= ui_card('<div class="space-y-4">
          <div class="flex items-center gap-3 text-red-600">
            <i class="fas fa-exclamation-circle text-2xl"></i>
            <span id="export-error-text">' . __('scores_export_error_text') . '</span>
          </div>
          <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:8px">
            ' . ui_button(__('scores_export_retry'), 'primary', 'md', ['icon' => 'fa fa-redo', 'attrs' => ['onclick' => 'resetExport()']]) . '
          </div>
        </div>', [
        'title' => __('scores_export_error_title'),
      ]) ?>
    </div>
  </div>
</div>

<script>
var csvData = null;

function startExport() {
  document.getElementById('export-options').style.display = 'none';
  document.getElementById('export-progress').style.display = 'block';
  document.getElementById('export-error').style.display = 'none';

  var env = document.getElementById('export-env').value;

  var formData = new FormData();
  formData.append('action', 'export');
  formData.append('env', env);

  fetch('game-scores-export.php?id=<?= $gameId ?><?= $leaderboardId ? '&leaderboard_id=' . $leaderboardId : '' ?>', {
    method: 'POST',
    body: formData
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success) {
      var total = data.total;
      var rows = data.rows;
      var processed = 0;

      document.getElementById('export-progress-total').textContent = total;
      document.getElementById('export-progress-details').style.display = 'block';

      var header = ['username','score','ip_encrypted','country','created_at','sign','tags','leaderboard_id','data','env'];
      var csvLines = [header.join(',')];

      var chunkSize = 1000;
      var idx = 0;

      function processChunk() {
        var end = Math.min(idx + chunkSize, rows.length);
        for (var i = idx; i < end; i++) {
          var row = rows[i];
          var line = row.map(function(cell) {
            if (cell === null || cell === undefined) return '';
            var s = String(cell);
            if (s.indexOf(',') !== -1 || s.indexOf('"') !== -1 || s.indexOf('\n') !== -1) {
              return '"' + s.replace(/"/g, '""') + '"';
            }
            return s;
          });
          csvLines.push(line.join(','));
        }
        processed = end;
        idx = end;

        var pct = total > 0 ? Math.round((processed / total) * 100) : 100;
        document.getElementById('export-progress-bar').style.width = pct + '%';
        document.getElementById('export-progress-count').textContent = processed;

        if (idx < rows.length) {
          document.getElementById('export-progress-text').innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?= __('scores_export_progress_processing') ?>';
          setTimeout(processChunk, 10);
        } else {
          csvData = csvLines.join('\n');
          document.getElementById('export-progress-text').innerHTML = '<i class="fas fa-check"></i> <?= __('scores_export_progress_done') ?>';
          setTimeout(showExportComplete, 500);
        }
      }

      processChunk();
    } else {
      showExportError(data.error || <?= json_encode(__('scores_export_error_text')) ?>);
    }
  })
  .catch(function(err) {
    showExportError(<?= json_encode(__('scores_network_error')) ?>);
  });
}

function showExportComplete() {
  document.getElementById('export-progress').style.display = 'none';
  document.getElementById('export-complete').style.display = 'block';
  document.getElementById('export-complete-count').textContent = document.getElementById('export-progress-total').textContent;

  var btn = document.getElementById('btn-download-export');
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    downloadCsv();
  });
}

function showExportError(msg) {
  document.getElementById('export-progress').style.display = 'none';
  document.getElementById('export-error').style.display = 'block';
  document.getElementById('export-error-text').textContent = msg;
}

function resetExport() {
  document.getElementById('export-options').style.display = 'block';
  document.getElementById('export-progress').style.display = 'none';
  document.getElementById('export-complete').style.display = 'none';
  document.getElementById('export-error').style.display = 'none';
  document.getElementById('export-progress-bar').style.width = '0%';
  document.getElementById('export-progress-details').style.display = 'none';
}

function downloadCsv() {
  if (!csvData) return;
  var blob = new Blob([csvData], { type: 'text/csv;charset=utf-8;' });
  var url = URL.createObjectURL(blob);
  var a = document.createElement('a');
  a.href = url;
  a.download = 'gmiscores-<?= $gameId ?>.csv';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}
</script>

<?php require_once("pages/game-scores-export/game-scores-export.script.php"); ?>
