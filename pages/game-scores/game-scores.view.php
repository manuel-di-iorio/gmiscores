<div class="internal-page">
  <?php if (!empty($lb['is_private'])) { ?>
    <div class="private-badge"><i class="fas fa-lock"></i> <?= __('scores_private_badge') ?></div>
  <?php } ?>
  <div class="internal-actions internal-actions--right">
    <?= ui_button(__('scores_add_button'), 'primary', 'md', ['icon' => 'fa fa-plus-circle', 'attrs' => ['onclick' => "openModal('modal-insert-score')"]]) ?>

    <?= ui_button(__('scores_export_button'), 'primary', 'md', ['icon' => 'fa fa-cloud-download-alt', 'href' => 'game-scores-export.php?id=' . $game['game_id'] . '&leaderboard_id=' . $leaderboardId]) ?>

    <?= ui_button(__('scores_import_button'), 'primary', 'md', ['icon' => 'fa fa-cloud-upload-alt', 'href' => 'game-scores-import.php?id=' . $game['game_id'] . '&leaderboard_id=' . $leaderboardId]) ?>

    <?php if (!empty($scores)) { ?>
      <a href="javascript:;" id="btn-delete-selected-wrapper" style="display:none">
        <?= ui_button(__('scores_delete_selected'), 'primary', 'md', ['icon' => 'fa fa-trash', 'attrs' => ['onclick' => "openModal('modal-delete-selected-scores', onDeleteSelectedScoresModalOpen)"]]) ?>
      </a>
    <?php } ?>

    <?php if (!empty($scores)) { ?>
      <?= ui_button(__('scores_clear_all'), 'danger', 'md', ['icon' => 'fa fa-trash', 'attrs' => ['onclick' => "openModal('modal-clear-scores')"]]) ?>
    <?php } ?>
  </div>

  <!-- Scores list -->
  <?php
    // Filters for the scores table (always shown)
    $envOptions = [
      'production' => __('scores_env_production'),
      'test' => __('scores_env_test'),
    ];
    $scoreFilters = [
      [ 'name' => 'player', 'label' => __('scores_filter_player'), 'type' => 'text', 'placeholder' => __('scores_filter_player_placeholder') ],
      [ 'name' => 'score_min', 'label' => __('scores_filter_points_from'), 'type' => 'number', 'placeholder' => __('scores_filter_points_from_placeholder') ],
      [ 'name' => 'score_max', 'label' => __('scores_filter_points_to'), 'type' => 'number', 'placeholder' => __('scores_filter_points_to_placeholder') ],
      [ 'name' => 'ip_country', 'label' => __('scores_filter_country'), 'type' => 'text', 'placeholder' => __('scores_filter_country_placeholder') ],
      [ 'name' => 'tags', 'label' => __('scores_filter_tags'), 'type' => 'text', 'placeholder' => __('scores_filter_tags_placeholder') ],
      [ 'name' => 'env', 'label' => __('scores_filter_environment'), 'type' => 'select', 'options' => $envOptions, 'default' => 'production' ],
      [ 'name' => 'date_from', 'label' => __('scores_filter_date_from'), 'type' => 'date' ],
      [ 'name' => 'date_to', 'label' => __('scores_filter_date_to'), 'type' => 'date' ],
    ];
    render_table_filters($scoreFilters, ['reset_preserve' => ['id', 'leaderboard_id', 'sort', 'dir']]);

    if (!empty($scores)) {
    $tableColumns = [
      [
        "label" => __('scores_col_player'),
        "key" => "username",
        "sortable" => true,
        "format_callback" => function ($value, $row) {
          return htmlspecialchars($value);
        }
      ],
      [
        "label" => __('scores_col_score'),
        "key" => "score",
        "sortable" => true
      ],
      [
        "label" => __('scores_col_country'),
        "key" => "ip_country",
        "sortable" => true,
        "format_callback" => function ($value, $row) {
          return is_null($value) ? __('scores_col_country_na') : htmlspecialchars($value);
        }
      ],
      [
        "label" => __('scores_col_tags'),
        "key" => "tags",
        "sortable" => true
      ],
      [
        "label" => __('scores_col_env'),
        "key" => "env",
        "sortable" => true,
        "format_callback" => function ($value, $row) {
          $env = $value ?? 'production';
          $badgeClass = $env === 'test' ? 'tag-yellow' : 'tag-green';
          $label = $env === 'test' ? __('scores_env_test') : __('scores_env_production');
          return '<span class="tag ' . $badgeClass . ' env-tag">' . $label . '</span>';
        }
      ],
      [
        "label" => __('scores_col_date'),
        "key" => "updated_at",
        "sortable" => true,
        "format_callback" => function ($value, $row) {
          return $row["_updated_at_pretty"] ?? $value;
        }
      ],
    ];

    $tableActions = [
      [
        "label" => __('scores_action_data'),
        "icon" => "fas fa-file-alt",
        "url" => "javascript:;",
        "class" => "btn-link",
        "condition_callback" => function ($data) {
          return isset($data['data']) && $data['data'] !== null && $data['data'] !== '';
        },
        "onclick" => function ($data) {
          return "openModal('modal-view-score-data', onViewScoreDataModalOpen, { scoreId: {$data['score_id']}, playerName: '" . escapeChars($data['username']) . "', data: '" . base64_encode(escapeChars($data['data'])) . "' })";
        }
      ],
      [
        "label" => __('scores_action_ban'),
        "icon" => "fas fa-user-times",
        "url" => "javascript:;",
        "class" => "btn-link",
        "onclick" => function ($data) {
          return "openModal('modal-ban-player', onBanPlayerModalOpen, { scoreId: {$data['score_id']}, playerName: '" . escapeChars($data['username']) . "' })";
        }
      ],
      [
        "label" => __('scores_action_delete'),
        "icon" => "fas fa-trash",
        "url" => "javascript:;",
        "class" => "btn-link",
        "onclick" => function ($data) {
          return "openModal('modal-delete-score', onDeleteScoreModalOpen, { scoreId: {$data['score_id']}, playerName: '" . escapeChars($data['username']) . "' })";
        }
      ]
    ];

    $tableOptions = [
      "table_class" => "ui-table",
      "pagination" => [
        "current_page" => $page,
        "items_per_page" => 100,
        "total_items" => $scoresCount
      ],
      "base_url" => "game-scores.php?id=" . $game["game_id"] . "&leaderboard_id=" . $leaderboardId . "&",
      "primary_key" => "score_id",
      "selectable" => true
    ];

    render_table($scores, $tableColumns, $tableActions, $tableOptions);

  } else {
    // Controlla se sono stati applicati filtri
    $filtersApplied = false;
    if (isset($filters) && is_array($filters)) {
      foreach ($filters as $fv) {
        if ($fv !== null && $fv !== '') { $filtersApplied = true; break; }
      }
    }

    if ($filtersApplied) { ?>
      <div class="internal-empty">
        <i class="fas fa-search"></i>
        <h4><?= __('scores_empty_filter_title') ?></h4>
        <p><?= __('scores_empty_filter_desc') ?></p>
        <?= ui_button(__('scores_empty_filter_btn'), 'primary', 'md', ['href' => htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $game['game_id'] . '&leaderboard_id=' . $leaderboardId]) ?>
      </div>
    <?php } else { ?>
      <div class="internal-empty">
        <i class="fas fa-trophy"></i>
        <h4><?= __('scores_empty_title') ?></h4>
        <p><?= __('scores_empty_desc') ?></p>
        <?= ui_button(__('scores_empty_btn'), 'primary', 'md', ['icon' => 'fa fa-arrow-circle-right', 'href' => 'documentation.php']) ?>
      </div>
    <?php } } ?>
  </div>
</div>

<?= ui_modal('modal-delete-score', [
  'title' => __('scores_modal_delete_title'),
  'content' => '<p>' . __('scores_modal_delete_body') . ' <strong><span id="modal-delete-score__player-name"></span></strong> ?</p><p>' . __('scores_modal_delete_irreversible') . '</p>',
  'footer' =>
    ui_button(__('scores_modal_delete_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-delete-score', onDeleteScoreModalClose)"]]) .
    ui_button(__('scores_modal_delete_confirm'), 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'deleteScore()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>


<?= ui_modal('modal-insert-score', [
  'title' => __('scores_modal_add_title'),
  'content' => '<form id="form-add-score" style="margin-bottom:0" method="POST" action="/game-scores-add.php?id=' . $game["game_id"] . '&leaderboard_id=' . $leaderboardId . '">
    <input type="hidden" name="leaderboard_id" value="' . $leaderboardId . '">
    <div class="mb-4">
      <label class="block font-semibold mb-1.5 text-sm text-[var(--text-color)]">' . __('scores_modal_add_player') . '</label>
      <input id="input-insert-score__player" name="player" type="text" class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] disabled:bg-input-bg-disabled disabled:text-input-text-disabled disabled:cursor-not-allowed" required>
    </div>
    <div class="mb-4">
      <label class="block font-semibold mb-1.5 text-sm text-[var(--text-color)]">' . __('scores_modal_add_score') . '</label>
      <input id="input-insert-score__score" name="score" type="number" step="any" class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] disabled:bg-input-bg-disabled disabled:text-input-text-disabled disabled:cursor-not-allowed" required>
    </div>
    <h5 class="accordion" onclick="toggleAccordion(this)" style="display:block;width:100%;text-align:left;background:var(--bg-color-offset,#f1f1f1);border:none;padding:8px 16px;cursor:pointer">
      <span style="margin-right:16px">' . __('scores_modal_add_optional') . '</span>
      <small><i class="fas fa-arrow-circle-down"></i></small>
    </h5>
    <div class="accordion-content" style="display:none">
      <div class="mb-4">
        <label class="block font-semibold mb-1.5 text-sm text-[var(--text-color)]">' . __('scores_modal_add_tags') . ' <a href="/documentation.php" target="_blank" data-tippy-content="' . __('scores_modal_add_tags_help') . '"><i class="fas fa-question-circle"></i></a></label>
        <input id="input-insert-score__tags" name="tags" type="text" class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] disabled:bg-input-bg-disabled disabled:text-input-text-disabled disabled:cursor-not-allowed">
      </div>
      <div class="mb-4">
        <label class="block font-semibold mb-1.5 text-sm text-[var(--text-color)]">' . __('scores_modal_add_data') . '</label>
        <textarea id="input-insert-score__data" name="data" class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] disabled:bg-input-bg-disabled disabled:text-input-text-disabled disabled:cursor-not-allowed min-h-[80px] resize-y"></textarea>
      </div>
      <div class="mb-4">
        <label class="block font-semibold mb-1.5 text-sm text-[var(--text-color)]">' . __('scores_modal_add_mode') . ' <a href="/documentation.php" target="_blank" data-tippy-content="' . __('scores_modal_add_mode_help') . '"><i class="fas fa-question-circle"></i></a></label>
        <select class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] disabled:bg-input-bg-disabled disabled:text-input-text-disabled disabled:cursor-not-allowed" name="insertMode" required>
          <option value="higher" selected>' . __('scores_modal_add_mode_higher') . '</option>
          <option value="lower">' . __('scores_modal_add_mode_lower') . '</option>
        </select>
      </div>
      <div class="mb-4">
        <label class="block font-semibold mb-1.5 text-sm text-[var(--text-color)]">' . __('scores_modal_add_env') . '</label>
        <select class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] disabled:bg-input-bg-disabled disabled:text-input-text-disabled disabled:cursor-not-allowed" name="env">
          <option value="production">' . __('scores_env_production') . '</option>
          <option value="test">' . __('scores_env_test') . '</option>
        </select>
      </div>
    </div>
    <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:16px">
      ' . ui_button(__('scores_modal_add_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-insert-score', resetInsertScoreForm)"]]) . '
      ' . ui_button(__('scores_modal_add_submit'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'type' => 'submit']) . '
    </div>
  </form>',
]) ?>

<?= ui_modal('modal-delete-selected-scores', [
  'title' => __('scores_modal_delete_selected_title'),
  'content' => '<p>' . __('scores_modal_delete_selected_body') . ' <strong><span id="modal-delete-selected-scores__count"></span></strong> ' . __('scores_modal_delete_selected_body2') . '</p><p>' . __('scores_modal_delete_selected_irreversible') . '</p>',
  'footer' =>
    ui_button(__('scores_modal_delete_selected_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-delete-selected-scores')"]]) .
    ui_button(__('scores_modal_delete_selected_confirm'), 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'deleteSelectedScores()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>

<?= ui_modal('modal-clear-scores', [
  'title' => __('scores_modal_clear_title'),
  'content' => '<p>' . __('scores_modal_clear_body') . '</p><p>' . __('scores_modal_clear_irreversible') . '</p>',
  'footer' =>
    ui_button(__('scores_modal_clear_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-clear-scores')"]]) .
    ui_button(__('scores_modal_clear_confirm'), 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'clearScores()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>

<?= ui_modal('modal-ban-player', [
  'title' => __('scores_modal_ban_title'),
  'content' => '<p>' . __('scores_modal_ban_body1') . ' <strong><span id="modal-ban-player__player-name"></span></strong> ?</p>
    <p>' . __('scores_modal_ban_body2') . '</p>
    <p>' . __('scores_modal_ban_body3') . '</p>
    <p>' . __('scores_modal_ban_body4') . '</p>',
  'footer' =>
    ui_button(__('scores_modal_ban_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-ban-player', onBanPlayerModalClose)"]]) .
    ui_button(__('scores_modal_ban_confirm'), 'danger', 'md', ['icon' => 'fas fa-user-times', 'attrs' => ['onclick' => 'banPlayer()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>

<?= ui_modal('modal-view-score-data', [
  'title' => __('scores_modal_data_title'),
  'content' => '<p>' . __('scores_modal_data_body') . '<span id="modal-view-score-data__score-id"></span> ' . __('scores_modal_data_of') . ' <strong><span id="modal-view-score-data__player-name"></span></strong></p>
    <textarea id="modal-view-score-data__data" class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] disabled:bg-input-bg-disabled disabled:text-input-text-disabled disabled:cursor-not-allowed min-h-[80px] resize-y" style="min-height:120px"></textarea>',
  'footer' => ui_button(__('scores_modal_data_close'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-view-score-data')"]]),
  'footer_right' => true,
]) ?>

<?php require_once("game-scores.script.php"); ?>
