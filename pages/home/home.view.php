<style>
  .home-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
  }

  .home-stat-card {
    background: var(--bg-color-card, #fff);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: box-shadow 0.2s, border-color 0.2s;
  }

  .home-stat-card:hover {
    border-color: var(--glass-border-hover, rgba(99,102,241,0.3));
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  }

  .home-stat-card__icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3em;
    flex-shrink: 0;
  }

  .home-stat-card__icon--primary { background: rgba(99,102,241,0.1); color: #6366f1; }
  .home-stat-card__icon--success { background: rgba(16,185,129,0.1); color: #10b981; }
  .home-stat-card__icon--warning { background: rgba(245,158,11,0.1); color: #f59e0b; }
  .home-stat-card__icon--info { background: rgba(59,130,246,0.1); color: #3b82f6; }
  .home-stat-card__icon--pink { background: rgba(236,72,153,0.1); color: #ec4899; }
  .home-stat-card__icon--purple { background: rgba(168,85,247,0.1); color: #a855f7; }

  .home-stat-card__value {
    font-size: 1.6em;
    font-weight: 800;
    color: var(--text-color-headings, #333);
    line-height: 1.2;
    font-variant-numeric: tabular-nums;
  }

  .home-stat-card__label {
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

  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-color-secondary, #6b7280);
  }

  .empty-state i {
    font-size: 3em;
    margin-bottom: 16px;
    opacity: 0.3;
  }

  .empty-state h3 {
    font-size: 1.2em;
    font-weight: 600;
    color: var(--text-color-headings, #333);
    margin-bottom: 8px;
  }

  .chart-grid .ui-card + .ui-card {
    margin-top: 0;
  }

  @media (max-width: 768px) {
    .chart-grid {
      grid-template-columns: 1fr;
    }
    .home-stats-grid {
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
  }
</style>

<?php if ($totalGames === 0) { ?>
  <div class="empty-state">
    <i class="fas fa-chart-pie"></i>
    <h3>Nessun dato disponibile</h3>
    <p>Non hai ancora aggiunto giochi. Aggiungi il tuo primo gioco per vedere le statistiche!</p>
    <div style="margin-top:20px">
      <a href="add-game.php" class="CtaButton CtaButton--primary" style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;text-decoration:none;border-radius:12px;font-weight:600;">
        <i class="fas fa-plus"></i> Aggiungi gioco
      </a>
    </div>
  </div>
<?php } else { ?>

<div class="home-stats-grid">
  <div class="home-stat-card">
    <div class="home-stat-card__icon home-stat-card__icon--primary"><i class="fas fa-star"></i></div>
    <div>
      <div class="home-stat-card__value"><?= number_format($totalScores) ?></div>
      <div class="home-stat-card__label">Punteggi inviati</div>
    </div>
  </div>
  <div class="home-stat-card">
    <div class="home-stat-card__icon home-stat-card__icon--success"><i class="fas fa-users"></i></div>
    <div>
      <div class="home-stat-card__value"><?= number_format($totalPlayers) ?></div>
      <div class="home-stat-card__label">Giocatori unici</div>
    </div>
  </div>
  <div class="home-stat-card">
    <div class="home-stat-card__icon home-stat-card__icon--info"><i class="fas fa-gamepad"></i></div>
    <div>
      <div class="home-stat-card__value"><?= $totalGames ?></div>
      <div class="home-stat-card__label">Giochi</div>
    </div>
  </div>
  <div class="home-stat-card">
    <div class="home-stat-card__icon home-stat-card__icon--warning"><i class="fas fa-calendar-day"></i></div>
    <div>
      <div class="home-stat-card__value"><?= number_format($scoresToday) ?></div>
      <div class="home-stat-card__label">Punteggi oggi</div>
    </div>
  </div>
</div>

<?php
$chartDays = [];
$chartCounts = [];
$scoreDataByDay = [];
foreach ($scoresOverTime as $row) {
  $scoreDataByDay[$row["day"]] = (int)$row["count"];
}
for ($i = 29; $i >= 0; $i--) {
  $date = date('Y-m-d', strtotime("-$i days"));
  $chartDays[] = date('d/m', strtotime("-$i days"));
  $chartCounts[] = $scoreDataByDay[$date] ?? 0;
}

$gameNames = [];
$gameCounts = [];
foreach ($scoresByGame as $row) {
  $gameNames[] = addslashes($row["name"]);
  $gameCounts[] = (int)$row["count"];
}

$countryLabels = [];
$countryCounts = [];
foreach ($countries as $row) {
  $c = $row["ip_country"];
  if (!$c) continue;
  $countryLabels[] = addslashes($c);
  $countryCounts[] = (int)$row["count"];
}
?>

<div class="chart-grid">
  <div class="ui-card ui-card--padding-md">
    <div class="ui-card__body">
      <div style="font-weight:600;font-size:1em;color:var(--text-color-headings,#333);margin-bottom:12px">
        <i class="fas fa-chart-line" style="color:var(--primary-color,#6366f1);margin-right:8px"></i>Punteggi negli ultimi 30 giorni
      </div>
      <div class="chart-container">
        <canvas id="chartScoresOverTime"></canvas>
      </div>
    </div>
  </div>
  <div class="ui-card ui-card--padding-md">
    <div class="ui-card__body">
      <div style="font-weight:600;font-size:1em;color:var(--text-color-headings,#333);margin-bottom:12px">
        <i class="fas fa-chart-bar" style="color:var(--primary-color,#6366f1);margin-right:8px"></i>Punteggi totali per gioco
      </div>
      <div class="chart-container">
        <canvas id="chartScoresByGame"></canvas>
      </div>
    </div>
  </div>
</div>

<?php if (count($countryLabels) > 0) { ?>
<div style="margin-top:20px">
  <div class="ui-card ui-card--padding-md">
    <div class="ui-card__body">
      <div style="font-weight:600;font-size:1em;color:var(--text-color-headings,#333);margin-bottom:12px">
        <i class="fas fa-globe" style="color:var(--primary-color,#6366f1);margin-right:8px"></i>Paesi
      </div>
      <div class="chart-container" style="max-height:350px">
        <canvas id="chartCountries"></canvas>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
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

  createLineCtx('chartScoresOverTime', <?= json_encode($chartDays) ?>, <?= json_encode($chartCounts) ?>, 'Punteggi');
  createBarCtx('chartScoresByGame', <?= json_encode($gameNames) ?>, <?= json_encode($gameCounts) ?>, 'Punteggi');
  createDoughnutCtx('chartCountries', <?= json_encode($countryLabels) ?>, <?= json_encode($countryCounts) ?>);
});
</script>

<?php } ?>
