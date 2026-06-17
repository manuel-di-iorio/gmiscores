<?php
/**
 * Renders a single admin tab's content.
 * Expects $activeTab and all required data variables to be set.
 */

switch ($activeTab) {
  case 'users':
    $searchValue = htmlspecialchars($search ?? "");

    $html = '
<div class="search-form">
  <form method="GET" action="/admin.php" style="display:flex;gap:8px;align-items:center;flex:1;flex-wrap:wrap">
    <input type="hidden" name="tab" value="users">
    ' . ($pendingOnly ? '<input type="hidden" name="pending" value="1">' : '') . '
    <input type="text" name="search" class="ui-input" placeholder="' . __('admin_search_placeholder') . '" value="' . $searchValue . '" style="max-width:220px">
    ' . ui_button(__('filter_apply'), 'primary', 'sm', ['icon' => 'fas fa-search', 'type' => 'submit']) . '
    ' . (($search || $pendingOnly) ? ui_button(__('filter_reset'), 'secondary', 'sm', ['icon' => 'fas fa-times', 'href' => '/admin.php?tab=users']) : '') . '
    <a href="/admin.php?tab=users' . ($pendingOnly ? '' : '&pending=1') . ($search ? '&search=' . urlencode($search) : '') . '" class="pending-filter-btn ' . ($pendingOnly ? 'pending-filter-btn--active' : 'pending-filter-btn--inactive') . '">
      <i class="fas fa-clock"></i> ' . __('admin_pending_only') . '
      ' . ($unapprovedCount > 0 ? ui_badge((string)$unapprovedCount, 'warning', ['pill' => true]) : '') . '
    </a>
  </form>
  <div style="font-size:0.85em;color:var(--text-color-secondary,#6b7280);white-space:nowrap">
    ' . __('admin_total_users') . ': ' . $totalUsers . '
    ' . ($unapprovedCount > 0 && !$pendingOnly ? '<span style="color:#ef4444;margin-left:8px">(' . $unapprovedCount . ' ' . __('admin_pending') . ')</span>' : '') . '
  </div>
</div>';

    if (empty($users)) {
      $html .= '<div style="text-align:center;padding:40px 20px;color:var(--text-color-secondary,#6b7280)"><i class="fas fa-users" style="font-size:2.5em;opacity:0.3;margin-bottom:12px;display:block"></i>' . __('table_empty') . '</div>';
    } else {
      $html .= '<div class="ui-table-container"><table class="ui-table"><thead class="ui-table-header"><tr>
        <th class="ui-table-header-cell">ID</th>
        <th class="ui-table-header-cell">' . __('admin_col_username') . '</th>
        <th class="ui-table-header-cell">' . __('admin_col_discord') . '</th>
        <th class="ui-table-header-cell">' . __('admin_col_approved') . '</th>
        <th class="ui-table-header-cell">' . __('admin_col_admin') . '</th>
        <th class="ui-table-header-cell">' . __('table_actions') . '</th>
      </tr></thead><tbody class="ui-table-body">';

      foreach ($users as $u) {
        $isUserApproved = (int)$u["approved"] === 1;
        $isUserAdmin = (int)($u["admin"] ?? 0) === 1;

        $toggleUrl = "/admin-users-toggle.php?id=" . (int)$u["id"];
        $toggleParams = [];
        if ($search) $toggleParams[] = "search=" . urlencode($search);
        if ($pendingOnly) $toggleParams[] = "pending=1";
        if ($page > 0) $toggleParams[] = "page=" . $page;
        if ($toggleParams) $toggleUrl .= "&" . implode("&", $toggleParams);

        $html .= '<tr class="ui-table-row">
          <td class="ui-table-cell">' . (int)$u["id"] . '</td>
          <td class="ui-table-cell">' . htmlspecialchars($u["username"]) . '</td>
          <td class="ui-table-cell"><code style="font-size:0.85em">' . htmlspecialchars($u["discord_user_id"]) . '</code></td>
          <td class="ui-table-cell">' . ($isUserApproved
            ? ui_badge(__('admin_approved_yes'), 'success', ['icon' => 'fas fa-check-circle'])
            : ui_badge(__('admin_approved_no'), 'danger', ['icon' => 'fas fa-times-circle'])) . '</td>
          <td class="ui-table-cell">' . ($isUserAdmin
            ? '<span style="color:#6366f1"><i class="fas fa-crown"></i></span>'
            : '') . '</td>
          <td class="ui-table-cell actions-cell">
            ' . ui_toggle($isUserApproved, $toggleUrl, ['labelOn' => __('admin_disable'), 'labelOff' => __('admin_enable'), 'size' => 'md']) . '
          </td>
        </tr>';
      }

      $html .= '</tbody></table></div>';

      $totalPages = ceil($totalUsers / $perPage) - 1;
      if ($totalPages > 0) {
        $urlParams = $_GET;
        unset($urlParams['page'], $urlParams['ajax']);
        $baseQuery = http_build_query($urlParams);
        $urlPattern = $baseQuery ? '/admin.php?' . $baseQuery . '&page={page}' : '/admin.php?page={page}';
        $html .= '<div style="text-align:center;margin-top:16px">' .
          ui_paginator($page, $totalPages, [
            'url' => $urlPattern,
            'prevLabel' => __('table_prev'),
            'nextLabel' => __('table_next'),
          ]) . '</div>';
      }
    }

    echo $html;
    break;

  case 'players':
    $playersSearchValue = htmlspecialchars($playersSearch ?? "");

    $html = '
<div class="search-form">
  <form method="GET" action="/admin.php" style="display:flex;gap:8px;align-items:center;flex:1;flex-wrap:wrap">
    <input type="hidden" name="tab" value="players">
    <input type="text" name="players_search" class="ui-input" placeholder="' . __('admin_search_placeholder') . '" value="' . $playersSearchValue . '" style="max-width:220px">
    ' . ui_button(__('filter_apply'), 'primary', 'sm', ['icon' => 'fas fa-search', 'type' => 'submit']) . '
    ' . ($playersSearch ? ui_button(__('filter_reset'), 'secondary', 'sm', ['icon' => 'fas fa-times', 'href' => '/admin.php?tab=players']) : '') . '
  </form>
  <div style="font-size:0.85em;color:var(--text-color-secondary,#6b7280);white-space:nowrap">
    ' . __('admin_total_players') . ': ' . $totalPlayers . '
  </div>
</div>';

    if (empty($players)) {
      $html .= '<div style="text-align:center;padding:40px 20px;color:var(--text-color-secondary,#6b7280)"><i class="fas fa-user-friends" style="font-size:2.5em;opacity:0.3;margin-bottom:12px;display:block"></i>' . __('table_empty') . '</div>';
    } else {
      $html .= '<div class="ui-table-container"><table class="ui-table"><thead class="ui-table-header"><tr>
        <th class="ui-table-header-cell">ID</th>
        <th class="ui-table-header-cell">' . __('admin_col_username') . '</th>
        <th class="ui-table-header-cell">' . __('admin_col_top_score') . '</th>
        <th class="ui-table-header-cell">' . __('admin_col_game') . '</th>
        <th class="ui-table-header-cell">' . __('admin_col_banned') . '</th>
        <th class="ui-table-header-cell">' . __('table_actions') . '</th>
      </tr></thead><tbody class="ui-table-body">';

      foreach ($players as $p) {
        $isBanned = (int)($p["has_bans"] ?? 0) === 1;
        $toggleUrl = "/admin-players-toggle.php?id=" . (int)$p["player_id"];
        $toggleParams = [];
        if ($playersSearch) $toggleParams[] = "search=" . urlencode($playersSearch);
        if ($playersPage > 0) $toggleParams[] = "page=" . $playersPage;
        if ($toggleParams) $toggleUrl .= "&" . implode("&", $toggleParams);

        $html .= '<tr class="ui-table-row">
          <td class="ui-table-cell">' . (int)$p["player_id"] . '</td>
          <td class="ui-table-cell">' . htmlspecialchars($p["username"]) . '</td>
          <td class="ui-table-cell">' . (isset($p["top_score"]) ? number_format((float)$p["top_score"], 2) : '<span style="color:var(--text-color-secondary,#6b7280)">-</span>') . '</td>
          <td class="ui-table-cell">' . ($p["top_game"] ? htmlspecialchars($p["top_game"]) : '<span style="color:var(--text-color-secondary,#6b7280)">-</span>') . '</td>
          <td class="ui-table-cell">' . ($isBanned
            ? ui_badge(__('admin_yes'), 'danger', ['icon' => 'fas fa-ban'])
            : ui_badge(__('admin_no'), 'default', ['icon' => 'fas fa-check'])) . '</td>
          <td class="ui-table-cell actions-cell">
            ' . ui_toggle($isBanned, $toggleUrl, ['labelOn' => __('admin_unban'), 'labelOff' => __('admin_ban'), 'size' => 'md']) . '
          </td>
        </tr>';
      }

      $html .= '</tbody></table></div>';

      $playersTotalPages = ceil($totalPlayers / $playersPerPage) - 1;
      if ($playersTotalPages > 0) {
        $urlParams = $_GET;
        unset($urlParams['players_page'], $urlParams['ajax']);
        $baseQuery = http_build_query($urlParams);
        $urlPattern = $baseQuery ? '/admin.php?' . $baseQuery . '&players_page={page}' : '/admin.php?players_page={page}';
        $html .= '<div style="text-align:center;margin-top:16px">' .
          ui_paginator($playersPage, $playersTotalPages, [
            'url' => $urlPattern,
            'prevLabel' => __('table_prev'),
            'nextLabel' => __('table_next'),
          ]) . '</div>';
      }
    }

    echo $html;
    break;

  case 'analytics':
    $chartDays = [];
    $chartCounts = [];
    $scoreDataByDay = [];
    foreach ($globalScoresOverTime as $row) {
      $scoreDataByDay[$row["day"]] = (int)$row["count"];
    }
    for ($i = 29; $i >= 0; $i--) {
      $date = date('Y-m-d', strtotime("-$i days"));
      $chartDays[] = date('d/m', strtotime("-$i days"));
      $chartCounts[] = $scoreDataByDay[$date] ?? 0;
    }

    $gameNames = [];
    $gameCounts = [];
    foreach ($globalScoresByGame as $row) {
      $gameNames[] = addslashes($row["name"]);
      $gameCounts[] = (int)$row["count"];
    }

    $countryLabels = [];
    $countryCounts = [];
    $countryLabelsAll = [];
    $countryCountsAll = [];
    foreach ($globalCountriesList as $i => $row) {
      if (!$row["ip_country"]) continue;
      $countryLabelsAll[] = addslashes($row["ip_country"]);
      $countryCountsAll[] = (int)$row["count"];
      if ($i < 30) {
        $countryLabels[] = addslashes($row["ip_country"]);
        $countryCounts[] = (int)$row["count"];
      }
    }
    $countryCountVal = count($countryLabelsAll);

    $html = '
<div class="admin-stats-grid">
  <div class="admin-stat-card">
    <div class="admin-stat-card__icon admin-stat-card__icon--primary"><i class="fas fa-star"></i></div>
    <div>
      <div class="admin-stat-card__value">' . number_format($globalTotalScores) . '</div>
      <div class="admin-stat-card__label">' . __('admin_stat_scores') . '</div>
    </div>
  </div>
  <div class="admin-stat-card">
    <div class="admin-stat-card__icon admin-stat-card__icon--success"><i class="fas fa-gamepad"></i></div>
    <div>
      <div class="admin-stat-card__value">' . $globalTotalGames . '</div>
      <div class="admin-stat-card__label">' . __('admin_stat_games') . '</div>
    </div>
  </div>
  <div class="admin-stat-card">
    <div class="admin-stat-card__icon admin-stat-card__icon--info"><i class="fas fa-users"></i></div>
    <div>
      <div class="admin-stat-card__value">' . number_format($globalTotalPlayers) . '</div>
      <div class="admin-stat-card__label">' . __('admin_stat_players') . '</div>
    </div>
  </div>
  <div class="admin-stat-card">
    <div class="admin-stat-card__icon admin-stat-card__icon--purple"><i class="fas fa-user-friends"></i></div>
    <div>
      <div class="admin-stat-card__value">' . number_format($totalUsers) . '</div>
      <div class="admin-stat-card__label">' . __('admin_stat_users') . '</div>
    </div>
  </div>
  <div class="admin-stat-card">
    <div class="admin-stat-card__icon admin-stat-card__icon--warning"><i class="fas fa-play-circle"></i></div>
    <div>
      <div class="admin-stat-card__value">' . $globalActiveGames . '</div>
      <div class="admin-stat-card__label">' . __('admin_stat_active_games') . '</div>
    </div>
  </div>
  <div class="admin-stat-card">
    <div class="admin-stat-card__icon admin-stat-card__icon--pink"><i class="fas fa-globe"></i></div>
    <div>
      <div class="admin-stat-card__value">' . $countryCountVal . '</div>
      <div class="admin-stat-card__label">' . __('admin_stat_countries') . '</div>
    </div>
  </div>
</div>';

    if ($globalTotalScores > 0) {
      $html .= '
<div class="admin-stats-grid" style="grid-template-columns:1fr 1fr;margin-top:0">
  <div class="admin-stat-card">
    <div class="admin-stat-card__icon admin-stat-card__icon--primary"><i class="fas fa-trophy"></i></div>
    <div>
      <div class="admin-stat-card__value">' . htmlspecialchars($globalTopGame["name"] ?? "N/A") . '</div>
      <div class="admin-stat-card__label">' . __('admin_stat_top_game') . ' (' . ($globalTopGame["count"] ?? 0) . ' ' . __('admin_stat_scores') . ')</div>
    </div>
  </div>
  <div class="admin-stat-card">
    <div class="admin-stat-card__icon admin-stat-card__icon--success"><i class="fas fa-crown"></i></div>
    <div>
      <div class="admin-stat-card__value">' . htmlspecialchars($globalTopPlayer["username"] ?? "N/A") . '</div>
      <div class="admin-stat-card__label">' . __('admin_stat_top_player') . ' (' . ($globalTopPlayer["count"] ?? 0) . ' ' . __('admin_stat_scores') . ')</div>
    </div>
  </div>
</div>';

      $html .= '
<div class="chart-grid">
  <div class="ui-card ui-card--padding-md">
    <div class="ui-card__body">
      <div style="font-weight:600;font-size:1em;color:var(--text-color-headings,#333);margin-bottom:12px">
        <i class="fas fa-chart-line" style="color:var(--primary-color,#6366f1);margin-right:8px"></i>' . __('admin_chart_30days') . '
      </div>
      <div class="chart-container">
        <canvas id="chartAdminScoresOverTime"></canvas>
      </div>
    </div>
  </div>
  <div class="ui-card ui-card--padding-md">
    <div class="ui-card__body">
      <div style="font-weight:600;font-size:1em;color:var(--text-color-headings,#333);margin-bottom:12px">
        <i class="fas fa-chart-bar" style="color:var(--primary-color,#6366f1);margin-right:8px"></i>' . __('admin_chart_by_game') . '
      </div>
      <div class="chart-container">
        <canvas id="chartAdminScoresByGame"></canvas>
      </div>
    </div>
  </div>
</div>';

      if (count($countryLabelsAll) > 0) {
        $moreCountries = count($countryLabelsAll) - 30;
        $html .= '
<div style="margin-top:20px">
  <div class="ui-card ui-card--padding-md">
    <div class="ui-card__body">
      <div style="font-weight:600;font-size:1em;color:var(--text-color-headings,#333);margin-bottom:12px">
        <i class="fas fa-globe" style="color:var(--primary-color,#6366f1);margin-right:8px"></i>' . __('admin_chart_countries') . '
        ' . ($moreCountries > 0 ? '<span style="font-weight:400;font-size:0.8em;color:var(--text-color-secondary,#6b7280);margin-left:8px">(' . __('admin_chart_top30', ['more' => $moreCountries]) . ')</span>' : '') . '
      </div>
      <div class="chart-container" style="max-height:350px">
        <canvas id="chartAdminCountries"></canvas>
      </div>
    </div>
  </div>
</div>';
      }
    } else {
      $html .= '<div style="text-align:center;padding:40px 20px;color:var(--text-color-secondary,#6b7280)"><i class="fas fa-chart-bar" style="font-size:2.5em;opacity:0.3;margin-bottom:12px;display:block"></i>' . __('admin_analytics_empty') . '</div>';
    }

    echo $html;

    // Output inline chart init script
    if ($globalTotalScores > 0) {
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

  createLineCtx("chartAdminScoresOverTime", ' . json_encode($chartDays) . ', ' . json_encode($chartCounts) . ', "' . __('admin_stat_scores') . '");
  createBarCtx("chartAdminScoresByGame", ' . json_encode($gameNames) . ', ' . json_encode($gameCounts) . ', "' . __('admin_stat_scores') . '");
  createDoughnutCtx("chartAdminCountries", ' . json_encode($countryLabels) . ', ' . json_encode($countryCounts) . ');
})();
</script>';
    }
    break;

  case 'migrate':
    $html = '
<div style="margin-bottom:16px">
  <p style="color:var(--text-color-secondary,#6b7280);margin-bottom:16px">' . __('migrate_desc') . '</p>';

    if (!empty($migrateOutput)) {
      $html .= '<div class="migrate-output">';
      foreach ($migrateOutput as $line) {
        $cls = 'ok';
        if (strpos($line, 'ERROR') === 0) $cls = 'error';
        elseif (strpos($line, 'FAIL') === 0) $cls = 'fail';
        $html .= '<div class="' . $cls . '">' . htmlspecialchars($line) . '</div>';
      }
      $html .= '</div>';
    }

    if (empty($migrations)) {
      $html .= '<div style="text-align:center;padding:40px 20px;color:var(--text-color-secondary,#6b7280)"><i class="fas fa-database" style="font-size:2.5em;opacity:0.3;margin-bottom:12px;display:block"></i>' . __('migrate_empty') . '</div>';
    } else {
      $html .= '
<div class="ui-table-container"><table class="ui-table"><thead class="ui-table-header"><tr>
  <th class="ui-table-header-cell">' . __('migrate_col_file') . '</th>
  <th class="ui-table-header-cell">' . __('migrate_col_description') . '</th>
  <th class="ui-table-header-cell">' . __('migrate_col_status') . '</th>
  <th class="ui-table-header-cell">' . __('migrate_col_date') . '</th>
</tr></thead><tbody class="ui-table-body">';

      foreach ($migrations as $m) {
        $statusLabel = $m['is_applied'] ? __('migrate_status_applied') : __('migrate_status_pending');
        $statusBadge = $m['is_applied']
          ? ui_badge($statusLabel, 'success', ['icon' => 'fas fa-check'])
          : ui_badge($statusLabel, 'warning', ['icon' => 'fas fa-clock']);
        $appliedDate = $m['is_applied'] ? htmlspecialchars($applied[$m['name']]) : '-';

        $html .= '<tr class="ui-table-row">
          <td class="ui-table-cell"><code>' . htmlspecialchars($m['name']) . '</code></td>
          <td class="ui-table-cell">' . htmlspecialchars($m['description']) . '</td>
          <td class="ui-table-cell">' . $statusBadge . '</td>
          <td class="ui-table-cell">' . $appliedDate . '</td>
        </tr>';
      }

      $html .= '</tbody></table></div>';

      if ($pendingMigrateCount > 0) {
        $html .= '
        <form method="POST" action="/admin.php?tab=migrate" style="margin-top:16px">
          <input type="hidden" name="run" value="1">
          ' . ui_button(__('migrate_button', ['count' => $pendingMigrateCount]), 'primary', 'md', ['icon' => 'fas fa-play', 'type' => 'submit']) . '
        </form>';
      } else {
        $html .= '<div style="margin-top:16px;color:var(--text-color-secondary,#6b7280)"><i class="fas fa-check-circle" style="color:#10b981;margin-right:8px"></i>' . __('migrate_all_applied') . '</div>';
      }
    }

    $html .= '</div>';
    echo $html;
    break;
}
