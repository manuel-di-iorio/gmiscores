<?php
/**
 * Expects $activeTab, $gameId, $game, and analytics data variables.
 */

switch ($activeTab) {
  case 'analytics':
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
      $lbNames[] = $row["name"];
      $lbCounts[] = (int)$row["count"];
    }

    $gameCountryLabels = [];
    $gameCountryCounts = [];
    foreach ($gameCountries as $row) {
      $gameCountryLabels[] = $row["ip_country"];
      $gameCountryCounts[] = (int)$row["count"];
    }

    echo '
<div class="game-stats-grid">
  <div class="game-stat-card">
    <div class="game-stat-card__icon game-stat-card__icon--primary"><i class="fas fa-star"></i></div>
    <div>
      <div class="game-stat-card__value">' . number_format($gameTotalScores) . '</div>
      <div class="game-stat-card__label">' . __('game_stat_scores') . '</div>
    </div>
  </div>
  <div class="game-stat-card">
    <div class="game-stat-card__icon game-stat-card__icon--success"><i class="fas fa-users"></i></div>
    <div>
      <div class="game-stat-card__value">' . number_format($gameUniquePlayers) . '</div>
      <div class="game-stat-card__label">' . __('game_stat_players') . '</div>
    </div>
  </div>
  <div class="game-stat-card">
    <div class="game-stat-card__icon game-stat-card__icon--info"><i class="fas fa-globe"></i></div>
    <div>
      <div class="game-stat-card__value">' . $gameCountryCount . '</div>
      <div class="game-stat-card__label">' . __('game_stat_countries') . '</div>
    </div>
  </div>
  <div class="game-stat-card">
    <div class="game-stat-card__icon game-stat-card__icon--purple"><i class="fas fa-trophy"></i></div>
    <div>
      <div class="game-stat-card__value">' . $gameLeaderboardCount . '</div>
      <div class="game-stat-card__label">' . __('game_stat_leaderboards') . '</div>
    </div>
  </div>
</div>';

    if ($gameTotalScores > 0) {
      echo '
<div class="chart-grid">
  <div class="bg-surface-card border border-border-color rounded-xl shadow-sm overflow-hidden flex flex-col h-[360px]">
    <div class="p-5 flex-1 flex flex-col">
      <div class="font-semibold text-headings mb-3">
        <i class="fas fa-chart-line text-primary-color mr-2"></i>' . __('game_chart_30days') . '
      </div>
      <div class="chart-container flex-1 min-h-[200px]">
        <canvas id="chartGameScoresOverTime"></canvas>
      </div>
    </div>
  </div>
  <div class="bg-surface-card border border-border-color rounded-xl shadow-sm overflow-hidden flex flex-col h-[360px]">
    <div class="p-5 flex-1 flex flex-col">
      <div class="font-semibold text-headings mb-3">
        <i class="fas fa-chart-bar text-primary-color mr-2"></i>' . __('game_chart_by_lb') . '
      </div>
      <div class="chart-container flex-1 min-h-[200px]">
        <canvas id="chartGameScoresByLb"></canvas>
      </div>
    </div>
  </div>
</div>';

      if (count($gameCountryLabels) > 0) {
        echo '
<div style="margin-top:20px">
  <div class="bg-surface-card border border-border-color rounded-xl shadow-sm overflow-hidden flex flex-col">
    <div class="p-5 flex-1 flex flex-col">
      <div class="font-semibold text-headings mb-3">
        <i class="fas fa-globe text-primary-color mr-2"></i>' . __('game_chart_countries') . '
      </div>
      <div class="chart-container flex-1 min-h-[200px]" style="max-height:350px">
        <canvas id="chartGameCountries"></canvas>
      </div>
    </div>
  </div>
</div>';
      }
    } else {
      echo '<div style="text-align:center;padding:40px 20px;color:var(--text-color-secondary,#6b7280)"><i class="fas fa-chart-bar" style="font-size:2.5em;opacity:0.3;margin-bottom:12px;display:block"></i>' . __('game_analytics_empty') . '</div>';
    }

    // Chart init script
    if ($gameTotalScores > 0) {
      echo '<script>
(function () {
  var isDark = document.body.classList.contains("dark-theme");
  var textColor = isDark ? "#cbd5e1" : "#64748b";
  var gridColor = isDark ? "rgba(255,255,255,0.06)" : "rgba(0,0,0,0.06)";

  function createLineCtx(id, labels, data, label) {
    var el = document.getElementById(id);
    if (!el) return;
    new Chart(el, {
      type: "line",
      data: {
        labels: labels,
        datasets: [{
          label: label,
          data: data,
          borderColor: "#6366f1",
          backgroundColor: "rgba(99,102,241,0.08)",
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
      type: "bar",
      data: {
        labels: labels,
        datasets: [{
          label: label,
          data: data,
          backgroundColor: [
            "rgba(99,102,241,0.7)", "rgba(16,185,129,0.7)", "rgba(245,158,11,0.7)",
            "rgba(236,72,153,0.7)", "rgba(59,130,246,0.7)", "rgba(168,85,247,0.7)",
            "rgba(239,68,68,0.7)", "rgba(34,211,238,0.7)"
          ],
          borderColor: "#6366f1",
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
      type: "doughnut",
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: [
            "rgba(99,102,241,0.8)", "rgba(16,185,129,0.8)", "rgba(245,158,11,0.8)",
            "rgba(236,72,153,0.8)", "rgba(59,130,246,0.8)", "rgba(168,85,247,0.8)",
            "rgba(239,68,68,0.8)", "rgba(34,211,238,0.8)", "rgba(251,191,36,0.8)",
            "rgba(52,211,153,0.8)"
          ],
          borderWidth: 0,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "right",
            labels: { color: textColor, boxWidth: 12, padding: 12 }
          }
        }
      }
    });
  }

  createLineCtx("chartGameScoresOverTime", ' . json_encode($gameChartDays) . ', ' . json_encode($gameChartCounts) . ', "' . __('game_stat_scores') . '");
  createBarCtx("chartGameScoresByLb", ' . json_encode($lbNames) . ', ' . json_encode($lbCounts) . ', "' . __('game_stat_scores') . '");
  createDoughnutCtx("chartGameCountries", ' . json_encode($gameCountryLabels) . ', ' . json_encode($gameCountryCounts) . ');
})();
</script>';
    }
    break;
}
