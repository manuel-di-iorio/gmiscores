<style>
.d-inline {
  display: inline;
}
</style>

<div class="internal-page">
  <?php
    // Filters for banned players (always shown)
    $filters = [
      [ 'name' => 'player', 'label' => __('bans_filter_player'), 'type' => 'text', 'placeholder' => __('bans_filter_player_placeholder') ]
    ];
    render_table_filters($filters);

    if (!empty($records)) { ?>
  <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse;margin-bottom:16px">
      <tr>
        <th><?= __('bans_col_player') ?></th>
        <th><?= __('bans_col_banned_on') ?></th>
        <th></th>
      </tr>
      
      <?php foreach ($records as $record) { ?>
      <tr>
        <!-- Player name -->
        <td><?= htmlspecialchars($record["player_name"]); ?></td>
        
        <!-- Created at -->
        <td><?= $record["_created_at_pretty"]; ?></td>
        
        <td>
          <!-- Delete ban -->
          <a href="javascript:;" data-tippy-content="<?= __('bans_action_revoke') ?>" class="btn-link">
            <li class="fas fa-user-check"
                onclick="openModal('modal-delete-ban', onDeleteBanModalOpen,
                { banId: <?= $record['ban_id'] ?>,
                  playerName: '<?= escapeChars($record['player_name']) ?>' })"></li>
          </a>
        </td>
      </tr>  
      <?php } ?>    
    </table>

    <?php } else {
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
      <?php } } ?>
  </div>
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
/* MODALS */
let modalSelectedBan;

// MODAL: Delete ban
const modalDivBanPlayerName = document.getElementById('modal-delete-ban__player-name');

function onDeleteBanModalOpen({ banId, playerName }) {
  modalSelectedBan = banId;
  modalDivBanPlayerName.innerHTML = playerName;
}

function onDeleteBanModalClose() {
  modalDivBanPlayerName.innerHTML = "";
}

function deleteBan() {
  location.href = "game-bans-remove.php?id=" + modalSelectedBan + "&game=<?= $game["game_id"]; ?>";
}

</script>
