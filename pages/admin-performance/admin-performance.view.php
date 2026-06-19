<?php require_once(__DIR__ . '/../../assets/ui-kit/kit.php'); ?>

<style>
.perf-loading { text-align: center; padding: 40px; color: var(--text-color-secondary); }
.perf-loading i { font-size: 2em; animation: spin 1s linear infinite; margin-bottom: 12px; display: block; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

.perf-table { width: 100%; border-collapse: collapse; font-size: 0.85em; }
.perf-table th { background: var(--bg-color-card, #fff); padding: 10px 12px; text-align: left; font-weight: 600; border-bottom: 2px solid var(--border-color); position: sticky; top: 0; z-index: 1; }
.perf-table td { padding: 8px 12px; border-bottom: 1px solid var(--border-color); vertical-align: top; }
.perf-table tr:hover { background: rgba(99,102,241,0.03); }

.perf-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 0.8em; font-weight: 600; }
.perf-badge--ok { background: rgba(16,185,129,0.1); color: #10b981; }
.perf-badge--warn { background: rgba(245,158,11,0.1); color: #f59e0b; }
.perf-badge--error { background: rgba(239,68,68,0.1); color: #ef4444; }
.perf-badge--info { background: rgba(99,102,241,0.1); color: #6366f1; }

.perf-index-card { background: var(--bg-color-card, #fff); border: 1px solid var(--border-color); border-radius: 10px; padding: 16px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; gap: 12px; transition: border-color 0.2s; }
.perf-index-card:hover { border-color: var(--primary-color, #6366f1); }
.perf-index-card--applied { opacity: 0.5; border-color: #10b981; }

.perf-tabs { display: flex; gap: 0; border-bottom: 2px solid var(--border-color); margin-bottom: 20px; }
.perf-tab { padding: 10px 20px; cursor: pointer; font-weight: 600; color: var(--text-color-secondary); border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; }
.perf-tab:hover { color: var(--text-color-headings); }
.perf-tab--active { color: var(--primary-color, #6366f1); border-bottom-color: var(--primary-color, #6366f1); }

.perf-panel { display: none; }
.perf-panel--active { display: block; }

.perf-explain { font-family: monospace; font-size: 0.78em; background: var(--bg-color-card, #f9fafb); border: 1px solid var(--border-color); border-radius: 6px; padding: 8px; margin-top: 4px; overflow-x: auto; }
.perf-explain table { width: 100%; font-size: 0.95em; }
.perf-explain th, .perf-explain td { padding: 3px 6px; white-space: nowrap; }

.perf-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px; }
.perf-stat { background: var(--bg-color-card, #fff); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px; }
.perf-stat__icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2em; flex-shrink: 0; }
.perf-stat__icon--primary { background: rgba(99,102,241,0.1); color: #6366f1; }
.perf-stat__icon--success { background: rgba(16,185,129,0.1); color: #10b981; }
.perf-stat__icon--warning { background: rgba(245,158,11,0.1); color: #f59e0b; }
.perf-stat__icon--pink { background: rgba(236,72,153,0.1); color: #ec4899; }
.perf-stat__value { font-size: 1.5em; font-weight: 800; color: var(--text-color-headings, #333); line-height: 1.2; }
.perf-stat__label { font-size: 0.82em; color: var(--text-color-secondary, #6b7280); }

.perf-modal-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; }
.perf-modal { background: var(--bg-color-card, #fff); border-radius: 12px; padding: 24px; max-width: 500px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
.perf-modal h3 { margin: 0 0 12px; font-size: 1.2em; }
.perf-modal pre { background: var(--bg-color-card, #f3f4f6); border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; font-size: 0.82em; overflow-x: auto; margin: 0 0 16px; white-space: pre-wrap; }
.perf-modal-actions { display: flex; gap: 8px; justify-content: flex-end; }
</style>

<div class="page-content">
  <div style="max-width:1200px;margin:0 auto">
    <div style="display:flex;align-items:center;justify-content:flex-end;margin-bottom:20px;flex-wrap:wrap;gap:12px">
      
      <div style="display:flex;gap:8px">
        <?= ui_button('Analizza Query', 'primary', 'md', ['icon' => 'fas fa-play', 'attrs' => ['id' => 'btn-analyze']]) ?>
        <?= ui_button('Trova Indici Mancanti', 'secondary', 'md', ['icon' => 'fas fa-search', 'attrs' => ['id' => 'btn-indexes']]) ?>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="perf-stats" id="stats-grid" style="display:none">
      <div class="perf-stat">
        <div class="perf-stat__icon perf-stat__icon--primary"><i class="fas fa-database"></i></div>
        <div><div class="perf-stat__value" id="stat-queries">0</div><div class="perf-stat__label">Query Analizzate</div></div>
      </div>
      <div class="perf-stat">
        <div class="perf-stat__icon perf-stat__icon--success"><i class="fas fa-check-circle"></i></div>
        <div><div class="perf-stat__value" id="stat-optimized">0</div><div class="perf-stat__label">Ottimizzate</div></div>
      </div>
      <div class="perf-stat">
        <div class="perf-stat__icon perf-stat__icon--warning"><i class="fas fa-exclamation-triangle"></i></div>
        <div><div class="perf-stat__value" id="stat-slow">0</div><div class="perf-stat__label">Lente (&gt;50ms)</div></div>
      </div>
      <div class="perf-stat">
        <div class="perf-stat__icon perf-stat__icon--pink"><i class="fas fa-plus-circle"></i></div>
        <div><div class="perf-stat__value" id="stat-missing">0</div><div class="perf-stat__label">Indici Mancanti</div></div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="perf-tabs" id="perf-tabs" style="display:none">
      <div class="perf-tab perf-tab--active" data-tab="queries"><i class="fas fa-list"></i> Analisi Query</div>
      <div class="perf-tab" data-tab="indexes"><i class="fas fa-plus-circle"></i> Indici Mancanti</div>
      <div class="perf-tab" data-tab="slow"><i class="fas fa-clock"></i> Query Lente</div>
    </div>

    <!-- Loading -->
    <div class="perf-loading" id="loading" style="display:none">
      <i class="fas fa-spinner"></i>
      <div>Analisi in corso...</div>
    </div>

    <!-- Panel: Query Analysis -->
    <div class="perf-panel perf-panel--active" id="panel-queries">
      <div id="queries-content" style="color:var(--text-color-secondary);text-align:center;padding:40px">
        <i class="fas fa-microscope" style="font-size:3em;opacity:0.2;margin-bottom:12px;display:block"></i>
        Clicca "Analizza Query" per eseguire EXPLAIN su tutte le query dei Model
      </div>
    </div>

    <!-- Panel: Missing Indexes -->
    <div class="perf-panel" id="panel-indexes">
      <div id="indexes-content" style="color:var(--text-color-secondary);text-align:center;padding:40px">
        <i class="fas fa-search" style="font-size:3em;opacity:0.2;margin-bottom:12px;display:block"></i>
        Clicca "Trova Indici Mancanti" per analizzare gli indici del database
      </div>
    </div>

    <!-- Panel: Slow Queries -->
    <div class="perf-panel" id="panel-slow">
      <div id="slow-content" style="color:var(--text-color-secondary);text-align:center;padding:40px">
        <i class="fas fa-clock" style="font-size:3em;opacity:0.2;margin-bottom:12px;display:block"></i>
        Esegui prima l'analisi per vedere le query lente
      </div>
    </div>
  </div>
</div>

<!-- Confirmation Modal -->
<div class="perf-modal-overlay" id="confirm-modal">
  <div class="perf-modal">
    <h3><i class="fas fa-exclamation-triangle" style="color:#f59e0b;margin-right:8px"></i> Conferma</h3>
    <p id="confirm-text" style="color:var(--text-color-secondary);margin:0 0 16px;font-size:0.9em"></p>
    <pre id="confirm-sql"></pre>
    <div class="perf-modal-actions">
      <button type="button" id="modal-cancel-btn" class="ui-btn inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 bg-gray-200 text-gray-900 hover:bg-gray-300 px-4 py-2 text-base gap-2">
        <span>Annulla</span>
      </button>
      <button type="button" id="modal-apply-btn" class="ui-btn inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 text-base gap-2">
        <i class="fas fa-check"></i> <span>Applica</span>
      </button>
    </div>
  </div>
</div>

<script>
var pendingSql = '';
var pendingCallback = null;

function switchTab(tab) {
  document.querySelectorAll('.perf-tab').forEach(function(t) { t.classList.remove('perf-tab--active'); });
  document.querySelector('.perf-tab[data-tab="' + tab + '"]').classList.add('perf-tab--active');
  document.querySelectorAll('.perf-panel').forEach(function(p) { p.classList.remove('perf-panel--active'); });
  document.getElementById('panel-' + tab).classList.add('perf-panel--active');
}

function perfOpenModal(text, sql, callback) {
  document.getElementById('confirm-text').textContent = text;
  document.getElementById('confirm-sql').textContent = sql;
  document.getElementById('confirm-modal').style.display = 'flex';
  pendingCallback = callback;
}

function perfCloseModal() {
  document.getElementById('confirm-modal').style.display = 'none';
  pendingCallback = null;
}

document.getElementById('modal-cancel-btn').addEventListener('click', function() { perfCloseModal(); });
document.getElementById('modal-apply-btn').addEventListener('click', function() {
  if (pendingCallback) {
    var cb = pendingCallback;
    perfCloseModal();
    cb();
  }
});

document.querySelectorAll('.perf-tab').forEach(function(tab) {
  tab.addEventListener('click', function() { switchTab(this.getAttribute('data-tab')); });
});

document.getElementById('btn-analyze').addEventListener('click', runAnalysis);
document.getElementById('btn-indexes').addEventListener('click', loadMissingIndexes);

async function runAnalysis() {
  var btn = document.getElementById('btn-analyze');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Analisi...</span>';
  document.getElementById('loading').style.display = 'block';
  document.getElementById('queries-content').innerHTML = '';
  document.getElementById('slow-content').innerHTML = '';
  document.getElementById('stats-grid').style.display = 'none';
  document.getElementById('perf-tabs').style.display = 'none';

  try {
    var res = await fetch('/admin-performance.php?action=analyze');
    var text = await res.text();
    var data = JSON.parse(text);
    renderAnalysis(data);
  } catch (e) {
    document.getElementById('queries-content').innerHTML = '<div style="color:#ef4444;padding:20px">Errore: ' + e.message + '</div>';
  }

  document.getElementById('loading').style.display = 'none';
  btn.disabled = false;
  btn.innerHTML = '<i class="fas fa-play"></i> <span>Analizza Query</span>';
}

function renderAnalysis(data) {
  var entries = Object.entries(data);
  var total = entries.length;
  var optimized = entries.filter(function(e) { return e[1].uses_index; }).length;
  var slow = entries.filter(function(e) { return e[1].elapsed_ms >= 50; }).length;

  document.getElementById('stat-queries').textContent = total;
  document.getElementById('stat-optimized').textContent = optimized;
  document.getElementById('stat-slow').textContent = slow;
  document.getElementById('stats-grid').style.display = 'grid';
  document.getElementById('perf-tabs').style.display = 'flex';

  var html = '<div style="overflow-x:auto"><table class="perf-table"><thead><tr>';
  html += '<th>Metodo</th><th>Tabelle</th><th>Accesso</th><th>Tempo</th><th>Warn</th><th>EXPLAIN</th>';
  html += '</tr></thead><tbody>';

  for (var i = 0; i < entries.length; i++) {
    var name = entries[i][0];
    var info = entries[i][1];

    var timeClass = info.elapsed_ms >= 100 ? 'color:#ef4444;font-weight:700' : info.elapsed_ms >= 50 ? 'color:#f59e0b' : '';

    var accessBadges = '';
    var uniqueTypes = [];
    for (var j = 0; j < info.access_types.length; j++) {
      if (uniqueTypes.indexOf(info.access_types[j]) === -1) uniqueTypes.push(info.access_types[j]);
    }
    if (uniqueTypes.length === 0 && info.warnings.length > 0) {
      accessBadges = '<span class="perf-badge perf-badge--info">ERRORE</span>';
    } else if (uniqueTypes.length === 0) {
      accessBadges = '<span class="perf-badge perf-badge--ok">OK</span>';
    } else {
      for (var j = 0; j < uniqueTypes.length; j++) {
        var t = uniqueTypes[j];
        var cls;
        if (t === 'ALL' || t === 'index') cls = 'perf-badge--error';
        else if (t === 'ref' || t === 'eq_ref' || t === 'const' || t === 'system') cls = 'perf-badge--ok';
        else cls = 'perf-badge--warn';
        accessBadges += '<span class="perf-badge ' + cls + '">' + t + '</span> ';
      }
    }

    var warnings = info.warnings.length ? info.warnings.join('<br>') : '<span style="color:#10b981">-</span>';

    var explainRows = '';
    for (var k = 0; k < info.explain.length; k++) {
      var row = info.explain[k];
      if (row.error) { explainRows += '<tr><td colspan="7" style="color:#ef4444">' + row.error + '</td></tr>'; continue; }
      var typeCls = (row.type === 'ALL' || row.type === 'index') ? 'style="color:#ef4444;font-weight:700"' : '';
      explainRows += '<tr>' +
        '<td ' + typeCls + '>' + (row.type || '-') + '</td>' +
        '<td>' + (row.table || '-') + '</td>' +
        '<td>' + (row.possible_keys || '-') + '</td>' +
        '<td>' + (row.key || '-') + '</td>' +
        '<td>' + (row.key_len || '-') + '</td>' +
        '<td>' + (row.rows || '-') + '</td>' +
        '<td>' + (row.Extra || '-') + '</td>' +
        '</tr>';
    }

    html += '<tr>' +
      '<td style="font-weight:600;white-space:nowrap">' + name + '</td>' +
      '<td style="white-space:nowrap">' + info.tables.join(', ') + '</td>' +
      '<td>' + accessBadges + '</td>' +
      '<td style="' + timeClass + '">' + info.elapsed_ms + 'ms</td>' +
      '<td style="font-size:0.85em">' + warnings + '</td>' +
      '<td><details><summary style="cursor:pointer;font-size:0.85em;color:var(--primary-color)">Mostra EXPLAIN</summary>' +
      '<div class="perf-explain"><table><thead><tr><th>type</th><th>table</th><th>possible_keys</th><th>key</th><th>key_len</th><th>rows</th><th>Extra</th></tr></thead>' +
      '<tbody>' + explainRows + '</tbody></table></div></details></td></tr>';
  }

  html += '</tbody></table></div>';
  document.getElementById('queries-content').innerHTML = html;

  var slowEntries = entries.filter(function(e) { return e[1].elapsed_ms >= 50; });
  if (slowEntries.length === 0) {
    document.getElementById('slow-content').innerHTML = '<div style="text-align:center;padding:40px;color:#10b981"><i class="fas fa-check-circle" style="font-size:2em;margin-bottom:8px;display:block"></i>Nessuna query lente rilevata</div>';
  } else {
    var slowHtml = '<div style="overflow-x:auto"><table class="perf-table"><thead><tr><th>Metodo</th><th>Tempo</th><th>Accesso</th><th>Tabelle</th></tr></thead><tbody>';
    for (var i = 0; i < slowEntries.length; i++) {
      var name = slowEntries[i][0];
      var info = slowEntries[i][1];
      slowHtml += '<tr>' +
        '<td style="font-weight:600">' + name + '</td>' +
        '<td style="color:#ef4444;font-weight:700">' + info.elapsed_ms + 'ms</td>' +
        '<td>' + info.access_types.join(', ') + '</td>' +
        '<td>' + info.tables.join(', ') + '</td></tr>';
    }
    slowHtml += '</tbody></table></div>';
    document.getElementById('slow-content').innerHTML = slowHtml;
  }
}

async function loadMissingIndexes() {
  var btn = document.getElementById('btn-indexes');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Ricerca...</span>';

  try {
    var res = await fetch('/admin-performance.php?action=missing_indexes');
    var data = await res.json();
    document.getElementById('stat-missing').textContent = data.length;
    renderIndexes(data);
  } catch (e) {
    document.getElementById('indexes-content').innerHTML = '<div style="color:#ef4444;padding:20px">Errore: ' + e.message + '</div>';
  }

  btn.disabled = false;
  btn.innerHTML = '<i class="fas fa-search"></i> <span>Trova Indici Mancanti</span>';
}

function renderIndexes(indexes) {
  if (indexes.length === 0) {
    document.getElementById('indexes-content').innerHTML = '<div style="text-align:center;padding:40px;color:#10b981"><i class="fas fa-check-circle" style="font-size:2em;margin-bottom:8px;display:block"></i>Tutti gli indici necessari sono già presenti</div>';
    return;
  }

  var html = '<div style="margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">' +
    '<span style="font-weight:600">' + indexes.length + ' indici mancanti trovati</span>' +
    '<button id="btn-apply-all" class="ui-btn inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-200 bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 text-base gap-2">' +
    '<i class="fas fa-magic"></i> <span>Applica Tutti</span></button></div>';

  for (var i = 0; i < indexes.length; i++) {
    var idx = indexes[i];
    var cardId = 'idx-card-' + idx.index_name;
    html += '<div class="perf-index-card" id="' + cardId + '">' +
      '<div style="flex:1">' +
      '<div style="font-weight:600;font-size:0.95em;margin-bottom:4px">' +
      '<span class="perf-badge perf-badge--warn" style="margin-right:8px">' + idx.table + '</span>' +
      idx.index_name + '</div>' +
      '<div style="font-size:0.85em;color:var(--text-color-secondary);margin-bottom:6px">' + idx.reason + '</div>' +
      '<code style="font-size:0.82em;background:var(--bg-color-card,#f3f4f6);border:1px solid var(--border-color);border-radius:4px;padding:4px 8px;display:inline-block">' + idx.sql + '</code>' +
      '</div>' +
      '<button class="ui-btn apply-index-btn inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-200 bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 text-base gap-2" data-sql="' + idx.sql.replace(/"/g, '&quot;') + '" data-card="' + cardId + '" style="flex-shrink:0">' +
      '<i class="fas fa-plus"></i> <span>Applica</span></button></div>';
  }

  document.getElementById('indexes-content').innerHTML = html;

  document.getElementById('btn-apply-all').addEventListener('click', function() {
    perfOpenModal(
      'Vuoi applicare TUTTI gli indici mancanti?',
      'Verranno eseguiti tutti i CREATE INDEX suggeriti.',
      applyAllIndexes
    );
  });

  document.querySelectorAll('.apply-index-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var sql = this.getAttribute('data-sql');
      var cardId = this.getAttribute('data-card');
      perfOpenModal('Vuoi applicare questo indice?', sql, function() { applySingleIndex(sql, cardId); });
    });
  });
}

async function applySingleIndex(sql, cardId) {
  try {
    var formData = new FormData();
    formData.append('sql', sql);
    var res = await fetch('/admin-performance.php?action=apply_index', { method: 'POST', body: formData });
    var result = await res.json();
    if (result.success) {
      var card = document.getElementById(cardId);
      if (card) {
        card.classList.add('perf-index-card--applied');
        var btn = card.querySelector('button');
        if (btn) { btn.innerHTML = '<i class="fas fa-check"></i> <span>Applicato</span>'; btn.disabled = true; }
      }
    } else {
      alert('Errore: ' + result.message);
    }
  } catch (e) {
    alert('Errore: ' + e.message);
  }
}

async function applyAllIndexes() {
  var btn = document.getElementById('btn-apply-all');
  if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Applicazione...</span>'; }
  try {
    var res = await fetch('/admin-performance.php?action=apply_all');
    var results = await res.json();
    var ok = 0, fail = 0;
    for (var i = 0; i < results.length; i++) { results[i].success ? ok++ : fail++; }
    alert('Risultato: ' + ok + ' applicati, ' + fail + ' falliti');
    loadMissingIndexes();
  } catch (e) {
    alert('Errore: ' + e.message);
  }
  if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-magic"></i> <span>Applica Tutti</span>'; }
}
</script>
