<style>
.d-inline {
  display: inline;
}

.ModalBanUserImage {
  width: 91px;
}
</style>

<div class="w3-container w3-padding-large">
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
        <a href="game-scores-export.php?id=<?= $game["game_id"] ?>" class="btn-link" download>
          <button class="w3-button w3-small w3-black w3-margin-top w3-margin-bottom w3-margin-right"   >
            <i class="fa fa-cloud-download-alt w3-margin-right"></i> Esporta
          </button>
        </a>
      <?php } ?>

      <!-- Import scores -->
      <form id="form-import" class="d-inline" action="game-scores-import.php?id=<?= $game["game_id"] ?>" method="post" enctype="multipart/form-data" onsubmit="return false;">
        <input type='file' name="file" id="btn-import-pick-file" hidden onchange="importUploadOnChange(this)" />

        <button class="w3-button w3-small w3-black w3-margin-top w3-margin-bottom w3-margin-right" onclick="importPickFile()">
          <i class="fa fa-cloud-upload-alt w3-margin-right"></i> Importa
        </button>
      </form>

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
  <?php if (!empty($scores)) { ?>
  <div class="w3-responsive">
    <table class="w3-table w3-margin-bottom">
      <tr>
        <th>Giocatore</th>
        <th>Punteggio</th>
        <th>Nazione</th>
        <th>Tags (Leaderboard)</th>
        <th>Data</th>
        <th></th>
      </tr>
      
      <?php foreach ($scores as $score) { ?>
      <tr>
        <!-- Player name -->
        <td><?= htmlspecialchars($score["username"]); ?></td>

        <!-- Score -->
        <td><?= $score["score"]; ?></td>

        <!-- Country -->
        <td><?= is_null($score["ip_country"]) ? "N/A" : htmlspecialchars($score["ip_country"]); ?></td>

        <!-- Tags -->
        <td><?= htmlspecialchars($score["leaderboard_id"]); ?></td>
        
        <!-- Created at -->
        <td><?= $score["_updated_at_pretty"]; ?></td>
        
        <td class="w3-right-align">
          <!-- View score data -->
          <?php if (isset($score['data'])) { ?>
          <a class="btn-link w3-margin-right" href="javascript:;" data-tippy-content="Visualizza dati associati al punteggio">
            <li class="fas fa-file-alt"
                onclick="openModal('modal-view-score-data', onViewScoreDataModalOpen,
                { 
                  scoreId: <?= $score['score_id'] ?>,
                  playerName: '<?= escapeChars($score['username']) ?>',
                  data: '<?= base64_encode(escapeChars($score['data'])) ?>'
                })"></li>
          </a>
          <?php } ?>

          <!-- Ban player button -->
          <a class="btn-link w3-margin-right" href="javascript:;" data-tippy-content="Banna giocatore">
            <li class="fas fa-user-times"
                onclick="openModal('modal-ban-player', onBanPlayerModalOpen,
                { scoreId: <?= $score['score_id'] ?>,
                  playerName: '<?= escapeChars($score['username']) ?>' })"></li>
          </a>
          
          <!-- Delete score -->
          <a href="javascript:;" data-tippy-content="Cancella punteggio">
            <li class="fas fa-trash"
                onclick="openModal('modal-delete-score', onDeleteScoreModalOpen,
                { scoreId: <?= $score['score_id'] ?>,
                  playerName: '<?= escapeChars($score['username']) ?>' })"></li>
          </a>
        </td>
      </tr>  
      <?php } ?>    
    </table>

    <?php } else { ?>
    <h4>Non ci sono ancora punteggi per questo gioco.</h4>

    <a href="documentation.php">
      <button type="submit" class="w3-button w3-black w3-padding-large w3-margin-top w3-margin-bottom">
        <i class="fa fa-arrow-circle-right w3-margin-right"></i> Documentazione
      </button>
    </a>
    <?php } ?>

    <!-- Pagination component -->
    <?php if ($pagesCount > 1) { ?>
    <div class="w3-right">
      <div class="w3-bar">
        <a href="<?= $paginationArrowPrevLink ?>" data-tippy-content="Pagina precedente">
          <button <?php if (!$page) { ?> disabled <?php } ?> class="w3-bar-item w3-button">
            <i class="fas fa-angle-left"></i>
          </button>
        </a>
        
        <?php for ($i=0; $i<$pagesCount; $i++) { ?>
          <a href="game-scores.php?id=<?= $game["game_id"] ?>&page=<?= $i ?>" 
              class="<?php if ($page === $i) { echo "w3-blue w3-hover-blue"; } ?> w3-bar-item w3-button">
            <?= ($i+1) ?>
          </a>
        <?php } ?>

        <a href="<?= $paginationArrowNextLink ?>" data-tippy-content="Pagina successiva">
          <button <?php if ($page >= $pagesCount-1) { ?> disabled <?php } ?> class="w3-bar-item w3-button">
            <i class="fas fa-angle-right"></i>
          </button>
        </a>
      </div>
    </div>
    <?php } ?>
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

    <form id="form-add-score" class="w3-container" method="POST" action="/game-scores-add.php?id=<?= $game["game_id"] ?>">
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
          <label><b>ID Leaderboard <a href="/documentation.php" target="_blank" data-tippy-content="Vedi documentazione"><i class="fas fa-question-circle"></i></a></b></label>
          <input id="input-insert-score__player" name="leaderboard" type="text" class="w3-input w3-border w3-round w3-margin-bottom" required value="default">

          <label>
            <b>Firma con chiave privata <a href="/documentation.php" target="_blank" data-tippy-content="Vedi documentazione"><i class="fas fa-question-circle"></i></a></b>
          </label>
          <input id="input-insert-score__sign" name="sign" type="text" class="w3-input w3-border w3-round w3-margin-bottom">

          <label><b>Dati (una stringa associata al punteggio, max 64kb)</b></label>
          <textarea id="input-insert-score__data" name="data" class="w3-input w3-border w3-round w3-margin-bottom"></textarea>

          <label><b>Modalità di inserimento del punteggio <a href="/documentation.php" target="_blank" data-tippy-content="Vedi documentazione"><i class="fas fa-question-circle"></i></a></label>
          <select class="w3-select" name="insertMode" required>
            <option value="higher" selected>Solo se maggiore del precedente (higher)</option>
            <option value="lower">Solo se minore del precedente (lower)</option>
            <option value="all">In ogni caso (all)</option>
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
