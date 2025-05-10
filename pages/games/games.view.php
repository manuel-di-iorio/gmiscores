<div class="w3-container w3-padding-large w3-margin-bottom">
  <div class="w3-margin-bottom">
    <a href="add-game.php">
      <button type="submit" class="w3-button w3-black w3-padding-large w3-margin-top">
        <i class="fas fa-plus-circle w3-margin-right"></i> Aggiungi un nuovo gioco
      </button>
    </a>
  </div>

  <!-- Transparent separator -->
  <div>&nbsp;</div>

  <!-- Games list -->
  <?php if (!empty($games)) { ?>
  <div class="w3-responsive">
    <table class="w3-table">
      <tr>
        <th>Nome</th>
        <th>Punteggi inviati</th>
        <th>Giocatori</th>
        <th></th>
      </tr>
      
      <?php foreach ($games as $game) { ?>
      <tr>
        <td>
          <a href="game.php?id=<?= $game["game_id"]; ?>" data-tippy-content="Visualizza gioco">
            <?= htmlspecialchars($game["name"]) ?>
          </a>
        </td>
        <td><?= $game["_scoresCount"] ?></td>
        <td><?= $game["_playersCount"] ?></td>
        <td>
          <!-- View scores -->
          <a class="btn-link w3-margin-right" href="game-scores.php?id=<?= $game["game_id"]; ?>" data-tippy-content="Mostra punteggi">
            <li class="fas fa-list-ol"></li>
          </a>

          <!-- View banned players -->
          <a class="btn-link w3-margin-right" href="game-bans.php?id=<?= $game["game_id"]; ?>" data-tippy-content="Mostra giocatori bannati">
            <li class="fas fa-user-times"></li>
          </a>

          <!-- Delete game -->
          <a href="javascript:;" data-tippy-content="Cancella gioco">
            <li class="fas fa-trash" onclick="openModal('modal-delete-game', onDeleteGameModalOpen, {
                gameId: <?= $game['game_id'] ?>, gameName: '<?= escapeChars($game['name']) ?>'} )"></li>
          </a>
          
        </td>
      </tr>  
      <?php } ?>    
    </table>
    <?php } ?>
  </div>
</div>

<!-- Delete game modal -->
<div id="modal-delete-game" class="w3-modal">
  <div class="w3-modal-content w3-animate-top">
    <!-- Modal content -->
    <div class="w3-container ModalContent">
      <h4>Sei sicuro di voler cancellare il gioco <strong><span id="modal-game-name"></span></strong> ?</h4>
      <div>L'operazione non è reversibile</div>
    </div>

    <!-- Modal footer -->
    <footer class="w3-container w3-light-grey w3-padding-16 w3-right-align">
      <a href="javascript:;" onclick="deleteGame()" class="btn-link ModalFooterLink w3-text-red">
        <i class="fas fa-trash"></i>
        Elimina gioco
      </a>

      <button onclick="closeModal('modal-delete-game', onDeleteGameModalClose)" type="button" class="w3-button w3-black">Annulla</button>
    </footer>
  </div>
</div>

<script>
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
  location.href = "delete-game.php?id=" + modalSelectedGame;
}

// When the user clicks anywhere outside of the modal, close it
const modalDiv = document.getElementById('modal-delete-game');
window.onclick = function(event) {
  if (event.target == modalDiv) closeModal('modal-delete-game', onDeleteGameModalClose);
}
</script>
