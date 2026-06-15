<div class="internal-page">
  <div class="internal-actions">
    <a href="add-game.php">
      <button type="submit" class="w3-button w3-black w3-padding-large">
        <i class="fas fa-plus-circle w3-margin-right"></i> Aggiungi un nuovo gioco
      </button>
    </a>
  </div>

  <?php
    // Filters for the games table (always shown)
    $filters = [
      [ 'name' => 'name', 'label' => 'Nome gioco', 'type' => 'text', 'placeholder' => 'Cerca per nome...' ]
    ];
    render_table_filters($filters);

    if (!empty($games)) {
    $tableColumns = [
      [
        "label" => "Nome",
        "key" => "name",
        "sortable" => true,
        "format_callback" => function ($value, $row) {
          return '<a href="game.php?id=' . $row["game_id"] . '" data-tippy-content="Visualizza gioco">' . htmlspecialchars($value) . '</a>';
        }
      ],
      ["label" => "Punteggi inviati", "key" => "_scoresCount", "sortable" => true],
      ["label" => "Giocatori", "key" => "_playersCount", "sortable" => true],
    ];

    $tableActions = [
      [
        "label" => "Classifiche",
        "icon" => "fas fa-trophy",
        "url" => function ($data) {
          return "leaderboards.php?game_id={$data['game_id']}";
        },
        "class" => "btn-link w3-margin-right"
      ],
      [
        "label" => "Mostra giocatori bannati",
        "icon" => "fas fa-user-times",
        "url" => function ($data) {
          return "game-bans.php?id={$data['game_id']}";
        },
        "class" => "btn-link w3-margin-right"
      ],
      [
        "label" => "Cancella gioco",
        "icon" => "fas fa-trash",
        "class" => "btn-link",
        "url" => "javascript:;",
        "onclick" => function ($data) {
          return "openModal('modal-delete-game', onDeleteGameModalOpen, { gameId: {$data['game_id']}, gameName: '" . htmlspecialchars($data['name']) . "' })";
        }
      ]
    ];

    $tableOptions = [
      "table_class" => "w3-table w3-striped w3-bordered w3-hoverable",
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
          <h4>Nessun gioco trovato</h4>
          <p>Prova ad azzerare i filtri.</p>
          <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="w3-button w3-black">Rimuovi filtri</a>
        </div>
      <?php } else { ?>
        <div class="internal-empty">
          <i class="fas fa-gamepad"></i>
          <h4>Non hai ancora aggiunto nessun gioco</h4>
          <p>Crea il tuo primo gioco per iniziare a utilizzare la piattaforma.</p>
          <a href="add-game.php" class="w3-button w3-black"><i class="fas fa-plus-circle w3-margin-right"></i>Aggiungi un gioco</a>
        </div>
      <?php } }
  ?>
</div>

<!-- Delete game modal -->
<div id="modal-delete-game" class="w3-modal internal-modal">
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

      <button onclick="closeModal('modal-delete-game', onDeleteGameModalClose)" type="button"
        class="w3-button w3-black">Annulla</button>
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
  window.onclick = function (event) {
    if (event.target == modalDiv) closeModal('modal-delete-game', onDeleteGameModalClose);
  }
</script>
