<style>
.d-inline {
  display: inline;
}

.ModalBanUserImage {
  width: 91px;
}

.env-tag {
  padding: 4px 12px !important;
  border-radius: 12px !important;
  font-size: 0.8rem !important;
  line-height: 1.4 !important;
}

.modern-table-cell {
  vertical-align: middle !important;
}

.modern-table-header-cell {
  vertical-align: middle !important;
}
</style>

<div class="w3-container w3-padding-large">
  <?php if (!empty($lb['is_private'])) { ?>
    <div class="w3-panel w3-leftbar w3-border-gray w3-pale-yellow w3-margin-bottom">
      <i class="fas fa-lock"></i> Questa classifica è <strong>privata</strong>. La lettura via API richiede un hash di autenticazione.
    </div>
  <?php } ?>
  <div class="w3-cell-row">
    <!-- <div class="w3-cell">
      <button class="w3-button w3-small w3-black w3-margin-top w3-margin-bottom w3-margin-right">
        <i class="fa fa-search w3-margin-right"></i> Filtra per leaderboard
      </button>
    </div> -->

    <div class="w3-cell w3-right-align">
      <!-- Manually add score btn -->
      <a href="javascript:;" class="btn-link">
        <button class="w3-button w3-small w3-black w3-margin-top w3-margin-bottom w3-margin-right"      
                onclick="openModal('modal-insert-score')">
          <i class="fa fa-plus-circle w3-margin-right"></i> Inserisci punteggio
        </button>
      </a>

      <!-- Export scores -->
      <?php if (!empty($scores)) { ?>
        <a href="game-scores-export.php?id=<?= $game["game_id"] ?>&leaderboard_id=<?= $leaderboardId ?>" class="btn-link" download>
          <button class="w3-button w3-small w3-black w3-margin-top w3-margin-bottom w3-margin-right"   >
            <i class="fa fa-cloud-download-alt w3-margin-right"></i> Esporta
          </button>
        </a>
      <?php } ?>

      <!-- Import scores -->
      <form id="form-import" class="d-inline" action="game-scores-import.php?id=<?= $game["game_id"] ?>&leaderboard_id=<?= $leaderboardId ?>" method="post" enctype="multipart/form-data" onsubmit="return false;">
        <input type='file' name="file" id="btn-import-pick-file" hidden onchange="importUploadOnChange(this)" />

        <button class="w3-button w3-small w3-black w3-margin-top w3-margin-bottom w3-margin-right" onclick="importPickFile()">
          <i class="fa fa-cloud-upload-alt w3-margin-right"></i> Importa
        </button>
      </form>

      <!-- Delete selected button (hidden by default) -->
      <?php if (!empty($scores)) { ?>
        <a href="javascript:;" class="btn-link" id="btn-delete-selected-wrapper" style="display:none">
          <button class="w3-button w3-small w3-black w3-margin-top w3-margin-bottom w3-margin-right"      
                  onclick="openModal('modal-delete-selected-scores', onDeleteSelectedScoresModalOpen)">
            <i class="fa fa-trash w3-margin-right"></i> Elimina selezionati
          </button>
        </a>
      <?php } ?>

      <!-- Clear scores button -->
      <?php if (!empty($scores)) { ?>
        <a href="javascript:;" class="btn-link">
          <button class="w3-button w3-small w3-black w3-margin-top w3-margin-bottom"      
                  onclick="openModal('modal-clear-scores')">
            <i class="fa fa-trash w3-margin-right"></i> Cancella tutti
          </button>
        </a>
      <?php } ?>
    </div>
  </div>

  <!-- Scores list -->
  <?php
    // Filters for the scores table (always shown)
    $envOptions = [
      'production' => 'Produzione',
      'test' => 'Test',
    ];
    $scoreFilters = [
      [ 'name' => 'player', 'label' => 'Giocatore', 'type' => 'text', 'placeholder' => 'Nome giocatore' ],
      [ 'name' => 'score_min', 'label' => 'Punti (a partire da)', 'type' => 'number', 'placeholder' => 'Min' ],
      [ 'name' => 'score_max', 'label' => 'Punti (fino a)', 'type' => 'number', 'placeholder' => 'Max' ],
      [ 'name' => 'ip_country', 'label' => 'Nazione', 'type' => 'text', 'placeholder' => 'Nazione' ],
      [ 'name' => 'tags', 'label' => 'Tags', 'type' => 'text', 'placeholder' => 'Tags' ],
      [ 'name' => 'env', 'label' => 'Ambiente', 'type' => 'select', 'options' => $envOptions, 'default' => 'production' ],
      [ 'name' => 'date_from', 'label' => 'Da', 'type' => 'date' ],
      [ 'name' => 'date_to', 'label' => 'A', 'type' => 'date' ],
    ];
    render_table_filters($scoreFilters, ['reset_preserve' => ['id', 'leaderboard_id', 'sort', 'dir']]);

    if (!empty($scores)) {
    $tableColumns = [
      [
        "label" => "Giocatore",
        "key" => "username",
        "sortable" => true,
        "format_callback" => function ($value, $row) {
          return htmlspecialchars($value);
        }
      ],
      [
        "label" => "Punteggio",
        "key" => "score",
        "sortable" => true
      ],
      [
        "label" => "Nazione",
        "key" => "ip_country",
        "sortable" => true,
        "format_callback" => function ($value, $row) {
          return is_null($value) ? "N/A" : htmlspecialchars($value);
        }
      ],
      [
        "label" => "Tags",
        "key" => "tags",
        "sortable" => true
      ],
      [
        "label" => "Ambiente",
        "key" => "env",
        "sortable" => true,
        "format_callback" => function ($value, $row) {
          $env = $value ?? 'production';
          $badgeClass = $env === 'test' ? 'w3-yellow' : 'w3-green';
          $label = $env === 'test' ? 'Test' : 'Produzione';
          return '<span class="w3-tag ' . $badgeClass . ' env-tag">' . $label . '</span>';
        }
      ],
      [
        "label" => "Data",
        "key" => "updated_at",
        "sortable" => true,
        "format_callback" => function ($value, $row) {
          return $row["_updated_at_pretty"] ?? $value;
        }
      ],
    ];

    $tableActions = [
      [
        "label" => "Mostra dati",
        "icon" => "fas fa-file-alt",
        "url" => "javascript:;",
        "class" => "btn-link w3-margin-right",
        "condition_callback" => function ($data) {
          return isset($data['data']) && $data['data'] !== null && $data['data'] !== '';
        },
        "onclick" => function ($data) {
          return "openModal('modal-view-score-data', onViewScoreDataModalOpen, { scoreId: {$data['score_id']}, playerName: '" . escapeChars($data['username']) . "', data: '" . base64_encode(escapeChars($data['data'])) . "' })";
        }
      ],
      [
        "label" => "Banna giocatore",
        "icon" => "fas fa-user-times",
        "url" => "javascript:;",
        "class" => "btn-link w3-margin-right",
        "onclick" => function ($data) {
          return "openModal('modal-ban-player', onBanPlayerModalOpen, { scoreId: {$data['score_id']}, playerName: '" . escapeChars($data['username']) . "' })";
        }
      ],
      [
        "label" => "Cancella punteggio",
        "icon" => "fas fa-trash",
        "url" => "javascript:;",
        "class" => "btn-link",
        "onclick" => function ($data) {
          return "openModal('modal-delete-score', onDeleteScoreModalOpen, { scoreId: {$data['score_id']}, playerName: '" . escapeChars($data['username']) . "' })";
        }
      ]
    ];

    $tableOptions = [
      "table_class" => "w3-table w3-striped w3-bordered w3-hoverable",
      "pagination" => [
        "current_page" => $page,
        "items_per_page" => 100,
        "total_items" => $scoresCount
      ],
      "base_url" => "game-scores.php?id=" . $game["game_id"] . "&leaderboard_id=" . $leaderboardId . "&",
      "primary_key" => "score_id",
      "selectable" => true
    ];

    echo '<div class="w3-responsive">';
    render_table($scores, $tableColumns, $tableActions, $tableOptions);
    echo '</div>';

  } else {
    // Controlla se sono stati applicati filtri
    $filtersApplied = false;
    if (isset($filters) && is_array($filters)) {
      foreach ($filters as $fv) {
        if ($fv !== null && $fv !== '') { $filtersApplied = true; break; }
      }
    }

    if ($filtersApplied) { ?>
      <h4>Nessun punteggio trovato con i filtri selezionati. Prova ad allargare i criteri o azzerare i filtri.</h4>
      <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?id=<?= $game["game_id"] ?>&leaderboard_id=<?= $leaderboardId ?>" class="btn-link">Rimuovi filtri</a>
    <?php } else { ?>
      <h4>Non ci sono ancora punteggi per questo gioco.</h4>

      <a href="documentation.php">
        <button type="submit" class="w3-button w3-black w3-padding-large w3-margin-top w3-margin-bottom">
          <i class="fa fa-arrow-circle-right w3-margin-right"></i> Documentazione
        </button>
      </a>
    <?php } } ?>
  </div>
</div>

<!-- Delete score modal -->
<div id="modal-delete-score" class="w3-modal">
  <div class="w3-modal-content w3-animate-top">
    <!-- Modal content -->
    <div class="w3-container ModalContent">
      <h4>
        Sei sicuro di voler cancellare il punteggio di 
        <strong><span id="modal-delete-score__player-name"></span></strong> ?
      </h4>
      <div>L'operazione non è reversibile</div>
    </div>

    <!-- Modal footer -->
    <footer class="w3-container w3-light-grey w3-padding-16 w3-right-align">
      <a href="javascript:;" onclick="deleteScore()" class="btn-link ModalFooterLink w3-text-red">
        <i class="fas fa-trash"></i>
        Elimina punteggio
      </a>

      <button onclick="closeModal('modal-delete-score', onDeleteScoreModalClose)" type="button" 
              class="w3-button w3-black">Annulla</button>
    </footer>
  </div>
</div>


<!-- Modal insert score -->
<div id="modal-insert-score" class="w3-modal">
  <div class="w3-modal-content w3-animate-top">
    <!-- Modal content -->
    <div class="w3-container ModalContent">
      <h4>Inserisci manualmente un punteggio</h4>
    </div>

    <form id="form-add-score" class="w3-container" method="POST" action="/game-scores-add.php?id=<?= $game["game_id"] ?>&leaderboard_id=<?= $leaderboardId ?>">
      <input type="hidden" name="leaderboard_id" value="<?= $leaderboardId ?>">
      <div class="w3-section">
        <label><b>Nome del giocatore</b></label>
        <input id="input-insert-score__player" name="player" type="text" class="w3-input w3-border w3-round w3-margin-bottom" required>

        <label><b>Punteggio</b></label>
        <input id="input-insert-score__score" name="score" type="number" step="any" class="w3-input w3-border w3-round w3-margin-bottom" required>        

        <br />

        <h5 class="accordion w3-button w3-light-grey w3-block w3-left-align w3-margin-bottom" onclick="toggleAccordion(this)">
          <span class="w3-margin-right">Campi opzionali</span>
          <small><i class="fas fa-arrow-circle-down"></i></small>
        </h5>

        <div class="w3-hide w3-margin-bottom">
          <label><b>Tags <a href="/documentation.php" target="_blank" data-tippy-content="Vedi documentazione"><i class="fas fa-question-circle"></i></a></b></label>
          <input id="input-insert-score__tags" name="tags" type="text" class="w3-input w3-border w3-round w3-margin-bottom" value="">

          <!-- <label>
            <b>Firma con chiave privata <a href="/documentation.php" target="_blank" data-tippy-content="Vedi documentazione"><i class="fas fa-question-circle"></i></a></b>
          </label>
          <input id="input-insert-score__sign" name="sign" type="text" class="w3-input w3-border w3-round w3-margin-bottom"> -->

          <label><b>Dati (una stringa associata al punteggio, max 64kb)</b></label>
          <textarea id="input-insert-score__data" name="data" class="w3-input w3-border w3-round w3-margin-bottom"></textarea>

          <label><b>Modalità di inserimento del punteggio <a href="/documentation.php" target="_blank" data-tippy-content="Vedi documentazione"><i class="fas fa-question-circle"></i></a></b></label>
          <select class="w3-select" name="insertMode" required>
            <option value="higher" selected>Solo se maggiore del precedente (higher)</option>
            <option value="lower">Solo se minore del precedente (lower)</option>
            <option value="all">In ogni caso (all)</option>
          </select>

          <label class="w3-margin-top" style="display:block"><b>Ambiente</b></label>
          <select class="w3-select" name="env">
            <option value="production">Produzione</option>
            <option value="test">Test</option>
          </select>
        </div> <!-- /w3-hide -->
      </div> <!-- /.w3-section -->

      <!-- Modal footer -->
      <footer class="w3-container w3-light-grey w3-padding-16 w3-right-align">
        <button type="submit" class="w3-button ModalFooterLink"><i class="fas fa-plus-circle"></i>
          Inserisci
        </button>

        <button onclick="closeModal('modal-insert-score', resetInsertScoreForm)" type="button" class="w3-button w3-black">Annulla</button>
      </footer>
     
    </form>
  </div>
</div>

<!-- Modal delete selected scores -->
<div id="modal-delete-selected-scores" class="w3-modal">
  <div class="w3-modal-content w3-animate-top">
    <div class="w3-container ModalContent">
      <h4>Sei sicuro di voler cancellare i <strong><span id="modal-delete-selected-scores__count"></span></strong> punteggi selezionati?</h4>
      <div>L'operazione non è reversibile</div>
    </div>

    <footer class="w3-container w3-light-grey w3-padding-16 w3-right-align">
      <a href="javascript:;" onclick="deleteSelectedScores()" class="btn-link ModalFooterLink w3-text-red">
        <i class="fas fa-trash"></i>
        Elimina selezionati
      </a>

      <button onclick="closeModal('modal-delete-selected-scores')" type="button"
              class="w3-button w3-black">Annulla</button>
    </footer>
  </div>
</div>

<!-- Modal clear scores -->
<div id="modal-clear-scores" class="w3-modal">
  <div class="w3-modal-content w3-animate-top">
    <!-- Modal content -->
    <div class="w3-container ModalContent">
      <h4>Sei sicuro di voler cancellare <strong>tutti</strong> i punteggi ?</h4>
      <div>L'operazione non è reversibile</div>
    </div>

    <!-- Modal footer -->
    <footer class="w3-container w3-light-grey w3-padding-16 w3-right-align">
      <a href="javascript:;" onclick="clearScores()" class="btn-link ModalFooterLink w3-text-red">
        <i class="fas fa-trash"></i>
        Elimina tutti i punteggi
      </a>

      <button onclick="closeModal('modal-clear-scores')" type="button" class="w3-button w3-black">Annulla</button>
    </footer>
  </div>
</div>

<!-- Modal ban player -->
<div id="modal-ban-player" class="w3-modal">
  <div class="w3-modal-content w3-animate-top">
    <!-- Modal content -->
    <div class="w3-container ModalContent">
      <h4 class="w3-margin-bottom">Vuoi bannare <strong><span id="modal-ban-player__player-name"></span></strong> ?</h4>

      <div class="w3-opacity">
        <div>Tutti i suoi punteggi inviati su questo gioco verranno rimossi e non potrà inviarne di nuovi.</div>
        <div>Potrai rimuovere il ban in seguito ma i punteggi rimossi non potranno essere recuperati.</div>
        <div>I ban non influiscono su altri tuoi giochi.</div>
      </div>
    </div>

    <!-- Modal footer -->
    <footer class="w3-container w3-light-grey w3-padding-16">
      <div class="w3-left">
        <img src="assets/images/thor-hammer.gif" class="ModalBanUserImage" data-tippy-content="Thor's Ban Hammer">
      </div>

      <div class="w3-right">
        <a href="javascript:;" onclick="banPlayer()" class="btn-link ModalFooterLink w3-text-red">
          <i class="fas fa-user-times"></i>
          Banna il giocatore
        </a>

        <button onclick="closeModal('modal-ban-player', onBanPlayerModalClose)" type="button"
                class="w3-button w3-black">Annulla</button>
      </div>
    </footer>
  </div>
</div>

<!-- Modal view score data -->
<div id="modal-view-score-data" class="w3-modal">
  <div class="w3-modal-content w3-animate-top">
    <!-- Modal content -->
    <div class="w3-container ModalContent">
      <h4>Dati associati al punteggio #<span id="modal-view-score-data__score-id"></span> di <strong><span id="modal-view-score-data__player-name"></span></strong></h4>
      
      <textarea id="modal-view-score-data__data" class="w3-input w3-border w3-round w3-margin-bottom"></textarea>
    </div>

    <!-- Modal footer -->
    <footer class="w3-container w3-light-grey w3-padding-16 w3-right-align">
      <button onclick="closeModal('modal-view-score-data')" type="button" class="w3-button w3-black">Chiudi</button>
    </footer>
  </div>
</div>

<?php require_once("game-scores.script.php"); ?>
