<div class="internal-page">
  <?php
    $filters = [
      [ 'name' => 'player', 'label' => __('bans_filter_player'), 'type' => 'text', 'placeholder' => __('bans_filter_player_placeholder') ]
    ];
    render_table_filters($filters);

    if (!empty($records)) {
      $tableColumns = [
        [
          "label" => __('bans_col_player'),
          "key" => "player_name",
        ],
        [
          "label" => __('bans_col_banned_on'),
          "key" => "_created_at_pretty",
        ],
      ];

      $tableActions = [
        [
          "label" => __('bans_action_revoke'),
          "icon" => "fas fa-user-check",
          "class" => "btn-link",
          "url" => "javascript:;",
          "onclick" => function ($data) {
            return "openModal('modal-delete-ban', onDeleteBanModalOpen, { banId: {$data['ban_id']}, playerName: '" . htmlspecialchars($data['player_name']) . "' })";
          }
        ]
      ];

      $tableOptions = [
        "table_class" => "ui-table",
        "primary_key" => "ban_id",
        "base_url" => "game-bans.php?id=" . $game["game_id"] . "&",
      ];

      render_table($records, $tableColumns, $tableActions, $tableOptions);

    } else {
      $hasFilter = isset($_GET['player']) && trim($_GET['player']) !== '';
      if ($hasFilter) { ?>
        <div class="internal-empty">
          <i class="fas fa-search"></i>
          <h4><?= __('bans_empty_filter_title') ?></h4>
          <p><?= __('bans_empty_filter_desc') ?></p>
          <?= ui_button(__('bans_empty_filter_btn'), 'primary', 'md', ['href' => htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $game['game_id']]) ?>
        </div>
      <?php } else { ?>
        <div class="internal-empty">
          <i class="fas fa-user-check"></i>
          <h4><?= __('bans_empty_title') ?></h4>
          <p><?= __('bans_empty_desc') ?></p>
        </div>
      <?php }
    } ?>
</div>

<?= ui_modal('modal-delete-ban', [
  'title' => __('bans_modal_revoke_title'),
  'content' => '<p>' . __('bans_modal_revoke_body') . ' <strong><span id="modal-delete-ban__player-name"></span></strong> ?</p>',
  'footer' =>
    ui_button(__('bans_modal_revoke_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-delete-ban', onDeleteBanModalClose)"]]) .
    ui_button(__('bans_modal_revoke_confirm'), 'danger', 'md', ['icon' => 'fas fa-user-check', 'attrs' => ['onclick' => 'deleteBan()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>

<script>
let modalSelectedBan;
const modalDivBanPlayerName = document.getElementById('modal-delete-ban__player-name');

function onDeleteBanModalOpen({ banId, playerName }) {
  modalSelectedBan = banId;
  modalDivBanPlayerName.innerHTML = playerName;
}

function onDeleteBanModalClose() {
  modalDivBanPlayerName.innerHTML = "";
}

function deleteBan() {
  fetch("game-bans-remove.php", {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: "id=" + encodeURIComponent(modalSelectedBan) + "&game=<?= $game["game_id"]; ?>&csrf_token=" + encodeURIComponent("<?= csrf_token() ?>")
  }).then(function() { location.reload(); });
}
</script>
