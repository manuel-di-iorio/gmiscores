<div class="internal-page">
  <?php if (!empty($games)) { ?>
    <div class="internal-actions internal-actions--right">
      <?= ui_button(__('games_add_button'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'href' => 'add-game.php']) ?>
    </div>
  <?php } ?>

  <?php
    // Filters for the games table (always shown)
    $filters = [
      [ 'name' => 'name', 'label' => __('games_filter_name'), 'type' => 'text', 'placeholder' => __('games_filter_placeholder') ]
    ];
    render_table_filters($filters);

    if (!empty($games)) {
    $tableColumns = [
      [
        "label" => __('games_col_name'),
        "key" => "name",
        "sortable" => true,
        "format_callback" => function ($value, $row) {
          return '<a href="game.php?id=' . $row["game_id"] . '" class="link" data-tippy-content="' . __('games_row_tooltip') . '">' . htmlspecialchars($value) . '</a>';
        }
      ],
      ["label" => __('games_col_scores'), "key" => "_scoresCount", "sortable" => true],
      ["label" => __('games_col_players'), "key" => "_playersCount", "sortable" => true],
    ];

    $tableActions = [
      [
        "label" => __('games_action_leaderboards'),
        "icon" => "fas fa-trophy",
        "url" => function ($data) {
          return "game.php?id={$data['game_id']}&tab=leaderboards";
        },
        "class" => "btn-link"
      ],
      [
        "label" => __('games_action_delete'),
        "icon" => "fas fa-trash",
        "class" => "btn-link",
        "url" => "javascript:;",
        "onclick" => function ($data) {
          return "openModal('modal-delete-game', onDeleteGameModalOpen, { gameId: {$data['game_id']}, gameName: '" . htmlspecialchars($data['name']) . "' })";
        }
      ]
    ];

    $tableOptions = [
      "table_class" => "ui-table",
      "pagination" => [
        "current_page" => $_GET['page'] ?? 1,
        "items_per_page" => 25,
        // total_items sarà calcolato automaticamente dalla funzione se non fornito e se si passa l'array completo dei dati
      ],
      "base_url" => "games.php?",
      "primary_key" => "game_id"
    ];

    render_table($games, $tableColumns, $tableActions, $tableOptions);

    } else {
      // Se è stato applicato un filtro, mostra messaggio specifico
      $hasFilter = isset($_GET['name']) && trim($_GET['name']) !== '';
      if ($hasFilter) { ?>
        <div class="internal-empty">
          <i class="fas fa-search"></i>
          <h4><?= __('games_empty_filter_title') ?></h4>
          <p><?= __('games_empty_filter_desc') ?></p>
          <?= ui_button(__('games_empty_filter_btn'), 'primary', 'md', ['href' => htmlspecialchars($_SERVER['PHP_SELF'])]) ?>
        </div>
      <?php } else { ?>
        <div class="internal-empty">
          <i class="fas fa-gamepad"></i>
          <h4><?= __('games_empty_title') ?></h4>
          <p><?= __('games_empty_desc') ?></p>
          <?= ui_button(__('games_empty_btn'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'href' => 'add-game.php']) ?>
        </div>
      <?php } }
  ?>
</div>

<?= ui_modal('modal-delete-game', [
  'title' => __('games_modal_delete_title'),
  'content' => '<p>' . __('games_modal_delete_body') . ' <strong><span id="modal-game-name"></span></strong> ?</p><p>' . __('games_modal_delete_warning') . '</p>',
  'footer' =>
    ui_button(__('games_modal_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-delete-game', onDeleteGameModalClose)"]]) .
    ui_button(__('games_modal_confirm_delete'), 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'deleteGame()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>

<script>
  var csrfToken = '<?= csrf_token() ?>';
  const modalDivGameName = document.getElementById('modal-game-name');
  let modalSelectedGame;

  function onDeleteGameModalOpen({ gameId, gameName }) {
    modalSelectedGame = gameId;
    modalDivGameName.innerHTML = gameName;
  }

  function onDeleteGameModalClose() {
    modalDivGameName.innerHTML = "";
  }

  function deleteGame() {
    fetch("delete-game.php", {
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: "id=" + encodeURIComponent(modalSelectedGame) + "&csrf_token=" + encodeURIComponent(csrfToken)
    }).then(function() { location.reload(); });
  }

</script>
