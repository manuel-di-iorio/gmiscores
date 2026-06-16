<div class="internal-page">
  <?php if (!empty($lb['is_private'])) { ?>
    <div class="private-badge"><i class="fas fa-lock"></i> Classifica privata — la lettura via API richiede un hash di autenticazione.</div>
  <?php } ?>
  <div class="internal-actions internal-actions--right">
    <?= ui_button('Inserisci punteggio', 'primary', 'md', ['icon' => 'fa fa-plus-circle', 'attrs' => ['onclick' => "openModal('modal-insert-score')"]]) ?>

    <?php if (!empty($scores)) { ?>
      <?= ui_button('Esporta', 'primary', 'md', ['icon' => 'fa fa-cloud-download-alt', 'href' => 'game-scores-export.php?id=' . $game['game_id'] . '&leaderboard_id=' . $leaderboardId, 'attrs' => ['download' => '']]) ?>
    <?php } ?>

    <?= ui_button('Importa', 'primary', 'md', ['icon' => 'fa fa-cloud-upload-alt', 'attrs' => ['onclick' => 'importPickFile()']]) ?>
    <form id="form-import" action="game-scores-import.php?id=<?= $game["game_id"] ?>&leaderboard_id=<?= $leaderboardId ?>" method="post" enctype="multipart/form-data" onsubmit="return false;" style="display:none">
      <input type='file' name="file" id="btn-import-pick-file" hidden onchange="importUploadOnChange(this)" />
    </form>

    <?php if (!empty($scores)) { ?>
      <a href="javascript:;" id="btn-delete-selected-wrapper" style="display:none">
        <?= ui_button('Elimina selezionati', 'primary', 'md', ['icon' => 'fa fa-trash', 'attrs' => ['onclick' => "openModal('modal-delete-selected-scores', onDeleteSelectedScoresModalOpen)"]]) ?>
      </a>
    <?php } ?>

    <?php if (!empty($scores)) { ?>
      <?= ui_button('Cancella tutti', 'primary', 'md', ['icon' => 'fa fa-trash', 'attrs' => ['onclick' => "openModal('modal-clear-scores')"]]) ?>
    <?php } ?>
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
      <div class="internal-empty">
        <i class="fas fa-search"></i>
        <h4>Nessun punteggio trovato</h4>
        <p>Prova ad allargare i criteri o azzerare i filtri.</p>
        <?= ui_button('Rimuovi filtri', 'primary', 'md', ['href' => htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $game['game_id'] . '&leaderboard_id=' . $leaderboardId]) ?>
      </div>
    <?php } else { ?>
      <div class="internal-empty">
        <i class="fas fa-trophy"></i>
        <h4>Non ci sono ancora punteggi</h4>
        <p>I punteggi inviati dagli utenti tramite API appariranno qui. Consulta la documentazione per iniziare.</p>
        <?= ui_button('Documentazione', 'primary', 'md', ['icon' => 'fa fa-arrow-circle-right', 'href' => 'documentation.php']) ?>
      </div>
    <?php } } ?>
  </div>
</div>

<?= ui_modal('modal-delete-score', [
  'title' => 'Conferma eliminazione',
  'content' => '<p>Sei sicuro di voler cancellare il punteggio di <strong><span id="modal-delete-score__player-name"></span></strong> ?</p><p>L\'operazione non è reversibile.</p>',
  'footer' =>
    ui_button('Annulla', 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-delete-score', onDeleteScoreModalClose)"]]) .
    ui_button('Elimina punteggio', 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'deleteScore()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>


<?= ui_modal('modal-insert-score', [
  'title' => 'Inserisci manualmente un punteggio',
  'content' => '<form id="form-add-score" method="POST" action="/game-scores-add.php?id=' . $game["game_id"] . '&leaderboard_id=' . $leaderboardId . '">
    <input type="hidden" name="leaderboard_id" value="' . $leaderboardId . '">
    <div class="ui-input-group">
      <label class="ui-label">Nome del giocatore</label>
      <input id="input-insert-score__player" name="player" type="text" class="ui-input" required>
    </div>
    <div class="ui-input-group">
      <label class="ui-label">Punteggio</label>
      <input id="input-insert-score__score" name="score" type="number" step="any" class="ui-input" required>
    </div>
    <h5 class="accordion w3-button w3-light-grey w3-block w3-left-align w3-margin-bottom" onclick="toggleAccordion(this)">
      <span class="w3-margin-right">Campi opzionali</span>
      <small><i class="fas fa-arrow-circle-down"></i></small>
    </h5>
    <div class="w3-hide w3-margin-bottom">
      <div class="ui-input-group">
        <label class="ui-label">Tags <a href="/documentation.php" target="_blank" data-tippy-content="Vedi documentazione"><i class="fas fa-question-circle"></i></a></label>
        <input id="input-insert-score__tags" name="tags" type="text" class="ui-input">
      </div>
      <div class="ui-input-group">
        <label class="ui-label">Dati (una stringa associata al punteggio, max 64kb)</label>
        <textarea id="input-insert-score__data" name="data" class="ui-input"></textarea>
      </div>
      <div class="ui-input-group">
        <label class="ui-label">Modalit&agrave; di inserimento del punteggio <a href="/documentation.php" target="_blank" data-tippy-content="Vedi documentazione"><i class="fas fa-question-circle"></i></a></label>
        <select class="ui-select" name="insertMode" required>
          <option value="higher" selected>Solo se maggiore del precedente (higher)</option>
          <option value="lower">Solo se minore del precedente (lower)</option>
          <option value="all">In ogni caso (all)</option>
        </select>
      </div>
      <div class="ui-input-group">
        <label class="ui-label">Ambiente</label>
        <select class="ui-select" name="env">
          <option value="production">Produzione</option>
          <option value="test">Test</option>
        </select>
      </div>
    </div>
    <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:16px" class="ui-modal__footer">
      ' . ui_button('Annulla', 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-insert-score', resetInsertScoreForm)"]]) . '
      ' . ui_button('Inserisci', 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'type' => 'submit']) . '
    </div>
  </form>',
]) ?>

<?= ui_modal('modal-delete-selected-scores', [
  'title' => 'Conferma eliminazione',
  'content' => '<p>Sei sicuro di voler cancellare i <strong><span id="modal-delete-selected-scores__count"></span></strong> punteggi selezionati?</p><p>L\'operazione non è reversibile.</p>',
  'footer' =>
    ui_button('Annulla', 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-delete-selected-scores')"]]) .
    ui_button('Elimina selezionati', 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'deleteSelectedScores()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>

<?= ui_modal('modal-clear-scores', [
  'title' => 'Conferma cancellazione',
  'content' => '<p>Sei sicuro di voler cancellare <strong>tutti</strong> i punteggi ?</p><p>L\'operazione non è reversibile.</p>',
  'footer' =>
    ui_button('Annulla', 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-clear-scores')"]]) .
    ui_button('Elimina tutti', 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'clearScores()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>

<?= ui_modal('modal-ban-player', [
  'title' => 'Conferma ban',
  'content' => '<p>Vuoi bannare <strong><span id="modal-ban-player__player-name"></span></strong> ?</p>
    <p>Tutti i suoi punteggi inviati su questo gioco verranno rimossi e non potr&agrave; inviarne di nuovi.</p>
    <p>Potrai rimuovere il ban in seguito ma i punteggi rimossi non potranno essere recuperati.</p>
    <p>I ban non influiscono su altri tuoi giochi.</p>',
  'footer' =>
    ui_button('Annulla', 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-ban-player', onBanPlayerModalClose)"]]) .
    ui_button('Banna giocatore', 'danger', 'md', ['icon' => 'fas fa-user-times', 'attrs' => ['onclick' => 'banPlayer()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>

<?= ui_modal('modal-view-score-data', [
  'title' => 'Dati associati al punteggio',
  'content' => '<p>Dati associati al punteggio #<span id="modal-view-score-data__score-id"></span> di <strong><span id="modal-view-score-data__player-name"></span></strong></p>
    <textarea id="modal-view-score-data__data" class="ui-input" style="min-height:120px"></textarea>',
  'footer' => ui_button('Chiudi', 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-view-score-data')"]]),
  'footer_right' => true,
]) ?>

<?php require_once("game-scores.script.php"); ?>
