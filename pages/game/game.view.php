<style>
.code-block {
  font-size: 14px;
  background-color: var(--bg-color-code, #f8f8f8);
  border: 1px solid var(--border-color, #e0e0e0);
  padding: .7rem 1rem;
  border-bottom-left-radius: 4px;
  border-bottom-right-radius: 4px;
  overflow-x: auto;
  line-height: 1.6;
  margin-top: 0 !important;
}

.input-group {
  position: relative;
  margin-bottom: 1.2rem;
}

.input-secret-eye-btn {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  transition: color .2s;
  cursor: pointer;
  padding: 0.5rem;
  color: var(--text-color-secondary, #777);
}

.input-regenerate-secret-btn {
  position: absolute;
  right: 4rem;
  top: 50%;
  transform: translateY(-50%);
  transition: color .2s;
  cursor: pointer;
  padding: 0.5rem;
  color: var(--text-color-secondary, #777);
}

.input-secret-eye-btn:hover,
.input-regenerate-secret-btn:hover {
  color: var(--text-color, #000);
}

.section-header {
  border-bottom: 2px solid var(--border-color, #e0e0e0);
  padding-bottom: 0.8rem;
  margin-top: 2rem;
  margin-bottom: 1.8rem;
  font-size: 1.6rem;
  color: var(--text-color-headings, #222);
  font-weight: 500;
}
.section-header:first-of-type {
    margin-top: 0.5rem;
}
.code-block-header {
  background: var(--navbar-bg, #333);
  color: var(--navbar-text-color, #fff);
  padding: 0.6rem 1.2rem;
  border-top-left-radius: 4px;
  border-top-right-radius: 4px;
  font-weight: bold;
  margin-top: 1.5rem;
}
.form-label {
  font-weight: 600;
  margin-bottom: 0.6rem;
  display: block;
  color: var(--text-color-headings, #444);
}

.game-stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.game-stat-card {
  background: var(--bg-color-card, #fff);
  border: 1px solid var(--border-color, #e5e7eb);
  border-radius: 12px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  transition: box-shadow 0.2s, border-color 0.2s;
}

.game-stat-card:hover {
  border-color: var(--glass-border-hover, rgba(99,102,241,0.3));
  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.game-stat-card__icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2em;
  flex-shrink: 0;
}

.game-stat-card__icon--primary { background: rgba(99,102,241,0.1); color: #6366f1; }
.game-stat-card__icon--success { background: rgba(16,185,129,0.1); color: #10b981; }
.game-stat-card__icon--info { background: rgba(59,130,246,0.1); color: #3b82f6; }
.game-stat-card__icon--purple { background: rgba(168,85,247,0.1); color: #a855f7; }

.game-stat-card__value {
  font-size: 1.5em;
  font-weight: 800;
  color: var(--text-color-headings, #333);
  line-height: 1.2;
  font-variant-numeric: tabular-nums;
}

.game-stat-card__label {
  font-size: 0.82em;
  color: var(--text-color-secondary, #6b7280);
}

.chart-container {
  position: relative;
  width: 100%;
  max-height: 300px;
}

.chart-container canvas {
  max-height: 300px;
}

.chart-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-top: 20px;
}

.chart-grid > .ui-card {
  display: flex;
  flex-direction: column;
  height: 360px;
}

.chart-grid > .ui-card > .ui-card__body {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.chart-grid > .ui-card .chart-container {
  flex: 1;
  min-height: 200px;
  max-height: none;
}

.chart-grid > .ui-card .chart-container canvas {
  max-height: none;
}

.chart-grid .ui-card + .ui-card {
  margin-top: 0;
}

@media (max-width: 768px) {
  .chart-grid {
    grid-template-columns: 1fr;
  }
  .game-stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  }
}
</style>

<?php
// Configuration tab content
$configContent = '
  <div class="internal-actions internal-actions--right" style="margin-bottom:20px">
    ' . ui_button('Vedi classifiche', 'primary', 'md', ['icon' => 'fas fa-trophy', 'href' => 'leaderboards.php?game_id=' . $gameId]) . '
    ' . ui_button('Giocatori bannati', 'primary', 'md', ['icon' => 'fas fa-user-times', 'href' => 'game-bans.php?id=' . $gameId]) . '
    ' . ui_button('Elimina gioco', 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => "openModal('modal-delete-game', onDeleteGameModalOpen, { gameId: $gameId, gameName: '" . escapeChars($game['name']) . "' })"]]) . '
  </div>

  <div style="display:flex;gap:20px;flex-wrap:wrap">
    <div style="flex:1;min-width:300px">
      <div class="internal-card">
        <div class="internal-card__title"><i class="fas fa-cog"></i> Dettagli gioco</div>
        <label class="form-label">ID del Gioco</label>
        <div class="input-group">
          <input id="input-gameid" class="ui-input" value="' . $gameId . '" disabled style="background:var(--bg-color-sidebar,#f0f0f0)!important">
        </div>

        <label class="form-label" style="margin-top:16px">Secret del Gioco</label>
        <div style="color:var(--text-muted,#666);font-size:0.875em;margin-bottom:12px">Questo token viene usato per aumentare la sicurezza dell\'invio dei punteggi.</div>
        <div class="input-group">
          <input id="input-secret" type="password" class="ui-input" value="' . $game["client_secret"] . '" disabled>
          <i class="input-regenerate-secret-btn fas fa-sync" onclick="openModal(\'modal-regenerate-secret\')" data-tippy-content="Ottieni un nuovo secret"></i>
          <i class="input-secret-eye-btn fas fa-eye" onclick="toggleSecretVisibility(this)" data-tippy-content="Mostra o nasconde il secret del gioco"></i>
        </div>
      </div>
    </div>

    <div style="flex:1;min-width:300px">
      <div class="internal-card">
        <div class="internal-card__title"><i class="fas fa-edit"></i> Modifica nome</div>
        <form method="POST" action="/game-rename.php?id=' . $gameId . '">
          <div class="input-group">
            <input id="input-game-name" name="name" type="text" class="ui-input" value="' . htmlspecialchars($game["name"]) . '" required>
          </div>
          ' . ui_button('Modifica Nome', 'primary', 'md', ['icon' => 'fa fa-edit', 'type' => 'submit', 'class' => 'mt-2']) . '
        </form>
      </div>
    </div>
  </div>

  <h3 class="section-header" style="margin-top:1rem"><i class="fab fa-steam-symbol" style="margin-right:16px"></i>Integrazione con Game Maker</h3>

  <div class="internal-card">
    <div class="code-block-header">Invio di un punteggio:</div>
    <div class="code-block jsHigh">
  var points = 100; // Punti del giocatore<br/>
  var player = "Harry"; // Nome del giocatore<br/>
  var data = "game=' . $gameId . '&leaderboard_id=ID_CLASSIFICA&score=" + string(points) + "&player=" + base64_encode(player);<br/>
  var secret = "SECRET_DEL_GIOCO"; // Mettere il secret del gioco qui<br/>
  var hash = "&hash=" + sha1_string_utf8(data + secret);<br/>
  http_post_string("' . $baseApiPath . '/add.php", data + hash);
    </div>
  </div>

  <div class="internal-card">
    <div class="code-block-header">Lista punteggi (Evento Create):</div>
    <div class="code-block jsHigh">
  // Da mettere nell\'evento \'Create\' di un oggetto.<br/>
  // Questo effettua la richiesta per prendere i punteggi<br/>
  scores = noone;<br/>
  getScores = http_get("' . $baseApiPath . '/list.php?game=' . $gameId . '&leaderboard_id=ID_CLASSIFICA");
    </div>
  </div>

  <div class="internal-card">
    <div class="code-block-header">Lista punteggi (Evento Async - HTTP):</div>
    <div class="code-block jsHigh">
  // Da mettere nell\'evento \'Async - HTTP\' dello stesso oggetto.<br/>
  if (async_load[? "id"] == getScores && async_load[? "status"] == 0) {<br/>
  &nbsp;&nbsp;var result = json_decode(async_load[? "result"]);<br/>
  &nbsp;&nbsp;scores = result[? "scores"];<br/>
  }
    </div>
  </div>

  <div class="internal-card">
    <div class="code-block-header">Esempio di disegno della classifica:</div>
    <div class="code-block jsHigh">
  draw_text(20, 20, "Classifica:");<br/><br/>
  if (scores != noone) {<br/>
  &nbsp;&nbsp;for (var i=0; i&lt;ds_list_size(scores); i++) {<br/>
  &nbsp;&nbsp;&nbsp;&nbsp;var player = scores[| i];<br/>
  &nbsp;&nbsp;&nbsp;&nbsp;draw_text(20, 50+i*20, player[? "username"] + " - " + string(player[? "score"]));<br/>
  &nbsp;&nbsp;}<br/>
  }
    </div>
  </div>

  <h3 class="section-header"><i class="fas fa-book" style="margin-right:16px"></i>Documentazione API</h3>
  <p style="margin-bottom:16px">Consulta la documentazione completa per scoprire tutte le funzionalità dell\'API.</p>
  ' . ui_button('Vai alla Documentazione', 'primary', 'md', ['icon' => 'fa fa-arrow-circle-right', 'href' => 'documentation.php']) . '
';

// Analytics tab content
$gameChartDays = [];
$gameChartCounts = [];
$scoreDataByDay = [];
foreach ($gameScoresOverTime as $row) {
  $scoreDataByDay[$row["day"]] = (int)$row["count"];
}
for ($i = 29; $i >= 0; $i--) {
  $date = date('Y-m-d', strtotime("-$i days"));
  $gameChartDays[] = date('d/m', strtotime("-$i days"));
  $gameChartCounts[] = $scoreDataByDay[$date] ?? 0;
}

$lbNames = [];
$lbCounts = [];
foreach ($gameScoresByLb as $row) {
  $lbNames[] = addslashes($row["name"]);
  $lbCounts[] = (int)$row["count"];
}

$gameCountryLabels = [];
$gameCountryCounts = [];
foreach ($gameCountries as $row) {
  $gameCountryLabels[] = addslashes($row["ip_country"]);
  $gameCountryCounts[] = (int)$row["count"];
}

$analyticsContent = '
<div class="game-stats-grid">
  <div class="game-stat-card">
    <div class="game-stat-card__icon game-stat-card__icon--primary"><i class="fas fa-star"></i></div>
    <div>
      <div class="game-stat-card__value">' . number_format($gameTotalScores) . '</div>
      <div class="game-stat-card__label">Punteggi</div>
    </div>
  </div>
  <div class="game-stat-card">
    <div class="game-stat-card__icon game-stat-card__icon--success"><i class="fas fa-users"></i></div>
    <div>
      <div class="game-stat-card__value">' . number_format($gameUniquePlayers) . '</div>
      <div class="game-stat-card__label">Giocatori unici</div>
    </div>
  </div>
  <div class="game-stat-card">
    <div class="game-stat-card__icon game-stat-card__icon--info"><i class="fas fa-globe"></i></div>
    <div>
      <div class="game-stat-card__value">' . $gameCountryCount . '</div>
      <div class="game-stat-card__label">Paesi</div>
    </div>
  </div>
  <div class="game-stat-card">
    <div class="game-stat-card__icon game-stat-card__icon--purple"><i class="fas fa-trophy"></i></div>
    <div>
      <div class="game-stat-card__value">' . $gameLeaderboardCount . '</div>
      <div class="game-stat-card__label">Classifiche</div>
    </div>
  </div>
</div>';

if ($gameTotalScores > 0) {
  $analyticsContent .= '
<div class="chart-grid">
  <div class="ui-card ui-card--padding-md">
    <div class="ui-card__body">
      <div style="font-weight:600;font-size:1em;color:var(--text-color-headings,#333);margin-bottom:12px">
        <i class="fas fa-chart-line" style="color:var(--primary-color,#6366f1);margin-right:8px"></i>Punteggi ultimi 30 giorni
      </div>
      <div class="chart-container">
        <canvas id="chartGameScoresOverTime"></canvas>
      </div>
    </div>
  </div>
  <div class="ui-card ui-card--padding-md">
    <div class="ui-card__body">
      <div style="font-weight:600;font-size:1em;color:var(--text-color-headings,#333);margin-bottom:12px">
        <i class="fas fa-chart-bar" style="color:var(--primary-color,#6366f1);margin-right:8px"></i>Punteggi totali per classifica
      </div>
      <div class="chart-container">
        <canvas id="chartGameScoresByLb"></canvas>
      </div>
    </div>
  </div>
</div>';

  if (count($gameCountryLabels) > 0) {
    $analyticsContent .= '
<div style="margin-top:20px">
  <div class="ui-card ui-card--padding-md">
    <div class="ui-card__body">
      <div style="font-weight:600;font-size:1em;color:var(--text-color-headings,#333);margin-bottom:12px">
        <i class="fas fa-globe" style="color:var(--primary-color,#6366f1);margin-right:8px"></i>Paesi
      </div>
      <div class="chart-container" style="max-height:350px">
        <canvas id="chartGameCountries"></canvas>
      </div>
    </div>
  </div>
</div>';
  }
} else {
  $analyticsContent .= '<div style="text-align:center;padding:40px 20px;color:var(--text-color-secondary,#6b7280)"><i class="fas fa-chart-bar" style="font-size:2.5em;opacity:0.3;margin-bottom:12px;display:block"></i>Nessun punteggio inviato per questo gioco.</div>';
}

echo ui_tabs([
  ["id" => "config", "label" => "Configurazione", "icon" => "fas fa-cog", "content" => $configContent],
  ["id" => "analytics", "label" => "Analytics", "icon" => "fas fa-chart-pie", "content" => $analyticsContent],
]);
?>

<?= ui_modal('modal-regenerate-secret', [
  'title' => 'Conferma rigenerazione secret',
  'content' => '<p>Sei sicuro di voler ottenere un nuovo secret del gioco?</p>
    <div style="background:#fff8e1;border-left:4px solid #ffc107;padding:16px;border-radius:8px;margin-top:16px">
      <p><i class="fas fa-exclamation-triangle" style="margin-right:8px"></i><strong>Attenzione:</strong> Se il tuo gioco sta già utilizzando il secret attuale, dovrai aggiornarlo nel codice del gioco e rilasciare una nuova versione per continuare a inviare i punteggi correttamente.</p>
      <p>Questa operazione non è reversibile.</p>
    </div>',
  'footer' =>
    ui_button('Annulla', 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-regenerate-secret')"]]) .
    ui_button('Genera nuovo secret', 'danger', 'md', ['icon' => 'fas fa-sync', 'attrs' => ['onclick' => 'regenerateSecret()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>

<?= ui_modal('modal-delete-game', [
  'title' => 'Conferma eliminazione',
  'content' => '<p>Sei sicuro di voler cancellare il gioco <strong><span id="modal-game-name"></span></strong> ?</p><p>L\'operazione non è reversibile.</p>',
  'footer' =>
    ui_button('Annulla', 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-delete-game', onDeleteGameModalClose)"]]) .
    ui_button('Elimina gioco', 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'deleteGame()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>

<script>
const modalGameDiv = document.getElementById('modal-game-name');
let modalSelectedGameId;

function onDeleteGameModalOpen({ gameId, gameName }) {
  modalSelectedGameId = gameId;
  modalGameDiv.innerHTML = gameName;
}

function onDeleteGameModalClose() {
  modalGameDiv.innerHTML = "";
}

function deleteGame() {
  location.href = "delete-game.php?id=" + modalSelectedGameId;
}
</script>

<?php if ($gameTotalScores > 0) { ?>
<script>
function initGameCharts() {
  if (window._gameChartsCreated) return;
  window._gameChartsCreated = true;

  var isDark = document.body.classList.contains('dark-theme') || <?= $theme === 'dark' ? 'true' : 'false' ?>;
  var textColor = isDark ? '#cbd5e1' : '#64748b';
  var gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';

  function createLineCtx(id, labels, data, label) {
    var el = document.getElementById(id);
    if (!el) return;
    new Chart(el, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: label,
          data: data,
          borderColor: '#6366f1',
          backgroundColor: 'rgba(99,102,241,0.08)',
          borderWidth: 2,
          fill: true,
          tension: 0.3,
          pointRadius: 2,
          pointHoverRadius: 5,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { ticks: { color: textColor, maxTicksLimit: 10 }, grid: { color: gridColor } },
          y: { ticks: { color: textColor }, grid: { color: gridColor }, beginAtZero: true }
        }
      }
    });
  }

  function createBarCtx(id, labels, data, label) {
    var el = document.getElementById(id);
    if (!el) return;
    new Chart(el, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: label,
          data: data,
          backgroundColor: [
            'rgba(99,102,241,0.7)', 'rgba(16,185,129,0.7)', 'rgba(245,158,11,0.7)',
            'rgba(236,72,153,0.7)', 'rgba(59,130,246,0.7)', 'rgba(168,85,247,0.7)',
            'rgba(239,68,68,0.7)', 'rgba(34,211,238,0.7)'
          ],
          borderColor: '#6366f1',
          borderWidth: 1,
          borderRadius: 4,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { ticks: { color: textColor }, grid: { display: false } },
          y: { ticks: { color: textColor }, grid: { color: gridColor }, beginAtZero: true }
        }
      }
    });
  }

  function createDoughnutCtx(id, labels, data) {
    var el = document.getElementById(id);
    if (!el) return;
    new Chart(el, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: [
            'rgba(99,102,241,0.8)', 'rgba(16,185,129,0.8)', 'rgba(245,158,11,0.8)',
            'rgba(236,72,153,0.8)', 'rgba(59,130,246,0.8)', 'rgba(168,85,247,0.8)',
            'rgba(239,68,68,0.8)', 'rgba(34,211,238,0.8)', 'rgba(251,191,36,0.8)',
            'rgba(52,211,153,0.8)'
          ],
          borderWidth: 0,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right',
            labels: { color: textColor, boxWidth: 12, padding: 12 }
          }
        }
      }
    });
  }

  createLineCtx('chartGameScoresOverTime', <?= json_encode($gameChartDays) ?>, <?= json_encode($gameChartCounts) ?>, 'Punteggi');
  createBarCtx('chartGameScoresByLb', <?= json_encode($lbNames) ?>, <?= json_encode($lbCounts) ?>, 'Punteggi');
  createDoughnutCtx('chartGameCountries', <?= json_encode($gameCountryLabels) ?>, <?= json_encode($gameCountryCounts) ?>);
}

var analyticsPanel = document.getElementById('panel-analytics');
if (analyticsPanel && analyticsPanel.classList.contains('is-active')) {
  initGameCharts();
} else if (analyticsPanel) {
  analyticsPanel.addEventListener('tabshown', initGameCharts);
}
</script>
<?php } ?>

<?php require_once("game.view.script.php"); ?>
