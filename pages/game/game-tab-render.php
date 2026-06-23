<?php
/**
 * Expects $activeTab, $gameId, $game, and tab-specific data variables.
 */

switch ($activeTab) {
  case 'players':
    require_once("models/Player.php");
    require_once("models/Ban.php");
    require_once("includes/table.php");
    require_once("includes/table-filters.php");

    $playerSearch = isset($_GET['player']) ? trim($_GET['player']) : null;
    $showBanned = isset($_GET['banned']) && $_GET['banned'] === '1';

    $players = Player::listByGameWithBanStatus($gameId, $playerSearch, $showBanned);
    $playersData = [];
    while ($row = $players->fetch_assoc()) {
      $playersData[] = $row;
    }

    $baseUrl = "game.php?id=$gameId&tab=players&ajax=1";

    echo '<div class="internal-page">';

    $playerFilters = [
      ['name' => 'player', 'label' => __('bans_filter_player'), 'type' => 'text', 'placeholder' => __('bans_filter_player_placeholder')],
      ['name' => 'banned', 'label' => __('game_players_filter_banned'), 'type' => 'select', 'options' => ['0' => __('game_players_filter_banned_no'), '1' => __('game_players_filter_banned_yes')]],
    ];
    render_table_filters($playerFilters, ['reset_preserve' => ['sort', 'dir']]);

    if (!empty($playersData)) {
      $tableColumns = [
        [
          "label" => __('bans_col_player'),
          "key" => "username",
        ],
        [
          "label" => __('game_players_col_login'),
          "key" => "user_id",
          "format_callback" => function ($value) {
            if ($value) {
              return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400"><i class="fab fa-discord"></i> Discord</span>';
            }
            return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-800/30 dark:text-gray-400"><i class="fas fa-user"></i> Guest</span>';
          }
        ],
        [
          "label" => __('game_players_col_banned'),
          "key" => "is_banned",
          "format_callback" => function ($value) {
            return $value ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400"><i class="fas fa-ban"></i> ' . __('game_players_badge_banned') . '</span>' : '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400"><i class="fas fa-check-circle"></i> ' . __('game_players_badge_active') . '</span>';
          }
        ],
      ];

      $tableActions = [
        [
          "label" => __('game_players_action_ban'),
          "icon" => "fas fa-user-times",
          "class" => "btn-link",
          "url" => "javascript:;",
          "onclick" => function ($data) {
            $action = $data['is_banned'] ? 'unban' : 'ban';
            $name = addslashes($data['username']);
            return "playerAction({id:{$data['player_id']},name:'{$name}',action:'{$action}'})";
          }
        ]
      ];

      $tableOptions = [
        "table_class" => "ui-table",
        "primary_key" => "player_id",
        "base_url" => $baseUrl,
        "default_sort_column" => "username",
        "default_sort_direction" => "asc",
      ];

      render_table($playersData, $tableColumns, $tableActions, $tableOptions);
    } else {
      $hasFilter = ($playerSearch && $playerSearch !== '') || $showBanned;
      if ($hasFilter) { ?>
        <div class="internal-empty">
          <i class="fas fa-search"></i>
          <h4><?= __('game_players_empty_filter_title') ?></h4>
          <p><?= __('game_players_empty_filter_desc') ?></p>
          <?= ui_button(__('game_players_empty_filter_btn'), 'primary', 'md', ['attrs' => ['onclick' => "window.location.href='game.php?id=$gameId&tab=players'"]]) ?>
        </div>
      <?php } else { ?>
        <div class="internal-empty">
          <i class="fas fa-users"></i>
          <h4><?= __('game_players_empty_title') ?></h4>
          <p><?= __('game_players_empty_desc') ?></p>
        </div>
      <?php }
    }

    echo '</div>';

    echo '<script>
(function() {
  var filterForm = document.querySelector("#panel-players form");
  if (!filterForm) return;
  filterForm.addEventListener("submit", function(e) {
    e.preventDefault();
    var params = new URLSearchParams(new FormData(filterForm));
    params.set("id", "' . $gameId . '");
    params.set("tab", "players");
    params.set("ajax", "1");
    var panel = document.getElementById("panel-players");
    if (panel) {
      panel.setAttribute("data-url", "game.php?" + params.toString());
      panel.setAttribute("data-loaded", "false");
      panel.innerHTML = "";
      var url = "game.php?" + params.toString();
      fetch(url).then(function(r){ return r.text(); }).then(function(html) {
        panel.innerHTML = html;
        panel.setAttribute("data-loaded", "true");
        panel.querySelectorAll("script").forEach(function(s) {
          var ns = document.createElement("script");
          if (s.src) { ns.src = s.src; } else { ns.textContent = s.textContent; }
          s.parentNode.replaceChild(ns, s);
        });
        if (typeof tippy === "function") tippy(panel.querySelectorAll("[data-tippy-content]"));
      });
    }
  });
})();
</script>';

    echo ui_modal('modal-player-action', [
      'title' => __('game_players_modal_ban_title'),
      'content' => '<p id="modal-player-action-body"></p>',
      'footer' =>
        ui_button(__('game_players_modal_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-player-action')"]]) .
        ui_button(__('game_players_modal_ban_confirm'), 'danger', 'md', ['icon' => 'fas fa-user-times', 'attrs' => ['id' => 'btn-player-action-confirm', 'onclick' => 'performPlayerAction()'], 'class' => 'ui-destructive']),
      'footer_right' => true,
    ]);

    echo '<script>
var csrfToken = \'' . csrf_token() . '\';
var currentPlayerAction = {};

function playerAction(data) {
  currentPlayerAction = data;
  var isBan = data.action === "ban";
  var titleEl = document.querySelector("#modal-player-action .ui-modal-title, #modal-player-action h3");
  var bodyEl = document.getElementById("modal-player-action-body");
  var btnEl = document.getElementById("btn-player-action-confirm");

  if (titleEl) titleEl.textContent = isBan ? ' . json_encode(__('game_players_modal_ban_title')) . ' : ' . json_encode(__('game_players_modal_unban_title')) . ';
  if (bodyEl) bodyEl.innerHTML = isBan
    ? ' . json_encode(__('game_players_modal_ban_body')) . ' + " <strong>" + data.name + "</strong> ?<br><br>" + ' . json_encode(__('game_players_modal_ban_warning')) . '
    : ' . json_encode(__('game_players_modal_unban_body')) . ' + " <strong>" + data.name + "</strong> ?";
  if (btnEl) {
    btnEl.innerHTML = isBan
      ? "<i class=\\"fas fa-user-times\\"></i> " + ' . json_encode(__('game_players_modal_ban_confirm')) . '
      : "<i class=\\"fas fa-user-check\\"></i> " + ' . json_encode(__('game_players_modal_unban_confirm')) . ';
  }
  openModal("modal-player-action");
}

function performPlayerAction() {
  var url = currentPlayerAction.action === "ban" ? "game-ban-add.php" : "game-bans-remove.php";
  var body = "player_id=" + encodeURIComponent(currentPlayerAction.id)
    + "&game_id=' . $gameId . '"
    + "&csrf_token=" + encodeURIComponent(csrfToken);
  fetch(url, {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: body
  }).then(function() {
    closeModal("modal-player-action");
    location.reload();
  });
}
</script>';
    break;

  case 'leaderboards':
    require_once("models/Leaderboard.php");
    require_once("includes/table.php");
    require_once("includes/table-filters.php");

    $lbNameFilter = isset($_GET['name']) ? trim($_GET['name']) : null;
    $leaderboards = Leaderboard::listByGame($gameId, ['name' => $lbNameFilter]);

    echo '<div class="internal-page">';
    echo '<div class="internal-actions internal-actions--right">';
    echo ui_button(__('leaderboards_create_btn'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'href' => 'add-leaderboard.php?game_id=' . $gameId]);
    echo '</div>';

    if (!empty($leaderboards)) {
      foreach ($leaderboards as &$row) {
        $row["_created_at_pretty"] = date("H:i:s - d/m/Y", strtotime($row["created_at"]));
      }
      unset($row);

      $lbFilters = [
        ['name' => 'name', 'label' => __('leaderboards_filter_name'), 'type' => 'text', 'placeholder' => __('leaderboards_filter_placeholder')],
      ];
      $lbBaseUrl = "game.php?id=$gameId&tab=leaderboards&ajax=1";
      render_table_filters($lbFilters, ['reset_preserve' => ['sort', 'dir']]);

      $tableColumns = [
        [
          "label" => __('leaderboards_col_id'),
          "key" => "leaderboard_id",
          "sortable" => true,
          "format_callback" => function ($value, $row) use ($gameId) {
            return '<a href="game-scores.php?id=' . $gameId . '&leaderboard_id=' . $row['leaderboard_id'] . '" class="link" data-tippy-content="' . __('leaderboards_row_tooltip') . '">' . htmlspecialchars($value) . '</a>';
          }
        ],
        [
          "label" => __('leaderboards_col_name'),
          "key" => "name",
          "sortable" => true,
          "format_callback" => function ($value, $row) {
            $icon = !empty($row['is_private']) ? ' <i class="fas fa-lock text-gray" title="' . __('leaderboards_col_private') . '"></i>' : '';
            return htmlspecialchars($value) . $icon;
          }
        ],
        [
          "label" => __('leaderboards_col_description'),
          "key" => "description",
          "sortable" => false,
          "format_callback" => function ($value) {
            return htmlspecialchars($value ?? __('leaderboards_col_description_na'));
          }
        ],
        [
          "label" => __('leaderboards_col_scores'),
          "key" => "score_count",
          "sortable" => true
        ],
        [
          "label" => __('leaderboards_col_created'),
          "key" => "created_at",
          "sortable" => true,
          "format_callback" => function ($value, $row) {
            return $row["_created_at_pretty"] ?? $value;
          }
        ]
      ];

      $tableActions = [
        [
          "label" => __('leaderboards_action_view'),
          "icon" => "fas fa-list-ol",
          "url" => function ($row) use ($gameId) {
            return "game-scores.php?id=" . $gameId . "&leaderboard_id=" . $row['leaderboard_id'];
          },
          "class" => "btn-link"
        ],
        [
          "label" => __('leaderboards_action_edit'),
          "icon" => "fas fa-edit",
          "url" => function ($row) {
            return "edit-leaderboard.php?leaderboard_id=" . $row['leaderboard_id'];
          },
          "class" => "btn-link"
        ]
      ];

      $tableActions[] = [
        "label" => __('leaderboards_action_delete'),
        "icon" => "fas fa-trash",
        "url" => "javascript:;",
        "class" => "btn-link",
        "onclick" => function ($row) use ($gameId) {
          $leaderboard_id = $row['leaderboard_id'] ?? 'null';
          $leaderboard_name = escapeChars($row['name'] ?? '');
          return "openModal('modal-delete-leaderboard', onDeleteLeaderboardModalOpen, { leaderboardId: " . $leaderboard_id . ", leaderboardName: '" . $leaderboard_name . "', gameId: " . $gameId . " })";
        }
      ];

      $tableOptions = [
        "table_id" => "leaderboardsTable",
        "table_class" => "ui-table",
        "primary_key" => "leaderboard_id",
        "base_url" => $lbBaseUrl,
        "default_sort_column" => "name",
        "default_sort_direction" => "asc",
      ];

      render_table($leaderboards, $tableColumns, $tableActions, $tableOptions);
    } else { ?>
      <div class="internal-empty">
        <i class="fas fa-trophy"></i>
        <h4><?= __('leaderboards_empty_title') ?></h4>
        <p><?= __('leaderboards_empty_desc') ?></p>
        <?= ui_button(__('leaderboards_empty_btn'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'href' => 'add-leaderboard.php?game_id=' . $gameId]) ?>
      </div>
    <?php }
    echo '</div>';

    echo '<script>
(function() {
  var filterForm = document.querySelector("#panel-leaderboards form");
  if (!filterForm) return;
  filterForm.addEventListener("submit", function(e) {
    e.preventDefault();
    var params = new URLSearchParams(new FormData(filterForm));
    params.set("id", "' . $gameId . '");
    params.set("tab", "leaderboards");
    params.set("ajax", "1");
    var panel = document.getElementById("panel-leaderboards");
    if (panel) {
      panel.setAttribute("data-url", "game.php?" + params.toString());
      panel.setAttribute("data-loaded", "false");
      panel.innerHTML = "";
      var url = "game.php?" + params.toString();
      fetch(url).then(function(r){ return r.text(); }).then(function(html) {
        panel.innerHTML = html;
        panel.setAttribute("data-loaded", "true");
        panel.querySelectorAll("script").forEach(function(s) {
          var ns = document.createElement("script");
          if (s.src) { ns.src = s.src; } else { ns.textContent = s.textContent; }
          s.parentNode.replaceChild(ns, s);
        });
        if (typeof tippy === "function") tippy(panel.querySelectorAll("[data-tippy-content]"));
      });
    }
  });
})();
</script>';

    echo ui_modal('modal-delete-leaderboard', [
      'title' => __('leaderboards_modal_delete_title'),
      'content' => '<p>' . __('leaderboards_modal_delete_body') . ' <strong id="modal-delete-leaderboard__name"></strong>?</p><p>' . __('leaderboards_modal_delete_warning') . '</p>',
      'footer' =>
        ui_button(__('leaderboards_modal_delete_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-delete-leaderboard')"]]) .
        ui_button(__('leaderboards_modal_delete_confirm'), 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'deleteLeaderboard()'], 'class' => 'ui-destructive']),
      'footer_right' => true,
    ]);

    echo '<script>
var csrfToken = \'' . csrf_token() . '\';
let deleteLeaderboardData = {};

function onDeleteLeaderboardModalOpen(params) {
  document.getElementById("modal-delete-leaderboard__name").textContent = params.leaderboardName;
  deleteLeaderboardData = { leaderboard_id: params.leaderboardId, game_id: params.gameId };
}

function deleteLeaderboard() {
  if (deleteLeaderboardData.leaderboard_id) {
    var body = "leaderboard_id=" + encodeURIComponent(deleteLeaderboardData.leaderboard_id)
      + "&game_id=" + encodeURIComponent(deleteLeaderboardData.game_id)
      + "&csrf_token=" + encodeURIComponent(csrfToken);
    fetch("delete-leaderboard.php", {
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: body
    }).then(function() { location.reload(); });
  }
}
</script>';
    break;

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
