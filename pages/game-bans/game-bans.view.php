<style>
.d-inline {
  display: inline;
}
</style>

<div class="w3-container w3-padding-large">
  <!-- Records list -->
  <?php if (!empty($records)) { ?>
  <div class="w3-responsive">
    <table class="w3-table w3-margin-bottom">
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

    <?php } else { ?>
    <h4>Non ci sono ban attivi.</h4>
    <?php } ?>
  </div>
</div>

<!-- Delete ban modal -->
<div id="modal-delete-ban" class="w3-modal">
  <div class="w3-modal-content w3-animate-top">
    <!-- Modal content -->
    <div class="w3-container ModalContent">
      <h4>
        Sei sicuro di voler revocare il ban di 
        <strong><span id="modal-delete-ban__player-name"></span></strong> ?
      </h4>
    </div>

    <!-- Modal footer -->
    <footer class="w3-container w3-light-grey w3-padding-16 w3-right-align">
      <a href="javascript:;" onclick="deleteBan()" class="btn-link ModalFooterLink w3-text-red">
        <i class="fas fa-user-check"></i>
        Revoca ban
      </a>

      <button onclick="closeModal('modal-delete-ban', onDeleteBanModalClose)" type="button" 
              class="w3-button w3-black">Annulla</button>
    </footer>
  </div>
</div>

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

// When the user clicks anywhere outside of the modal, close it
const modalDeleteBanDiv = document.getElementById('modal-delete-ban');

window.onclick = (event) => {
  switch (event.target) {
    case modalDeleteBanDiv: closeModal('modal-delete-ban', onDeleteBanModalClose); break;
  }
}
</script>
