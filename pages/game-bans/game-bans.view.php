<style>
.d-inline {
  display: inline;
}
</style>

<div class="internal-page">
  <?php
    // Filters for banned players (always shown)
    $filters = [
      [ 'name' => 'player', 'label' => 'Giocatore', 'type' => 'text', 'placeholder' => 'Nome giocatore' ]
    ];
    render_table_filters($filters);

    if (!empty($records)) { ?>
  <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse;margin-bottom:16px">
      <tr>
        <th>Giocatore</th>
        <th>Bannato il</th>
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
          <a href="javascript:;" data-tippy-content="Revoca ban" class="btn-link">
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
          <h4>Nessun ban trovato</h4>
          <p>Prova ad azzerare i filtri.</p>
          <?= ui_button('Rimuovi filtri', 'primary', 'md', ['href' => htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $game['game_id']]) ?>
        </div>
      <?php } else { ?>
        <div class="internal-empty">
          <i class="fas fa-user-check"></i>
          <h4>Non ci sono ban attivi</h4>
          <p>Nessun giocatore è stato bannato per questo gioco.</p>
        </div>
      <?php } } ?>
  </div>
</div>

<?= ui_modal('modal-delete-ban', [
  'title' => 'Conferma revoca ban',
  'content' => '<p>Sei sicuro di voler revocare il ban di <strong><span id="modal-delete-ban__player-name"></span></strong> ?</p>',
  'footer' =>
    ui_button('Annulla', 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-delete-ban', onDeleteBanModalClose)"]]) .
    ui_button('Revoca ban', 'danger', 'md', ['icon' => 'fas fa-user-check', 'attrs' => ['onclick' => 'deleteBan()'], 'class' => 'ui-destructive']),
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
