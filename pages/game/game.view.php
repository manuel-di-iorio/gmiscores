<style>
.code-block {
  font-size: 14px;
  background-color: var(--bg-color-code, #f8f8f8);
  border: 1px solid var(--border-color, #e0e0e0);
  padding: .7rem 1rem;
  border-bottom-left-radius: 4px;
  border-bottom-right-radius: 4px;
  overflow-x: auto;
  line-height: 1.6;
  margin-top: 0 !important;
}

.input-group {
  position: relative;
  margin-bottom: 1.2rem;
}

.input-secret-eye-btn {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  transition: color .2s;
  cursor: pointer;
  padding: 0.5rem;
  color: var(--text-color-secondary, #777);
}

.input-regenerate-secret-btn {
  position: absolute;
  right: 4rem;
  top: 50%;
  transform: translateY(-50%);
  transition: color .2s;
  cursor: pointer;
  padding: 0.5rem;
  color: var(--text-color-secondary, #777);
}

.input-secret-eye-btn:hover,
.input-regenerate-secret-btn:hover {
  color: var(--text-color, #000);
}


.section-header {
  border-bottom: 2px solid var(--border-color, #e0e0e0);
  padding-bottom: 0.8rem;
  margin-top: 2rem;
  margin-bottom: 1.8rem;
  font-size: 1.6rem;
  color: var(--text-color-headings, #222);
  font-weight: 500;
}
.section-header:first-of-type {
    margin-top: 0.5rem;
}
.code-block-header {
  background: var(--navbar-bg, #333);
  color: var(--navbar-text-color, #fff);
  padding: 0.6rem 1.2rem;
  border-top-left-radius: 4px;
  border-top-right-radius: 4px;
  font-weight: bold;
  margin-top: 1.5rem;
}
.form-label {
  font-weight: 600;
  margin-bottom: 0.6rem;
  display: block;
  color: var(--text-color-headings, #444);
}
</style>

<div class="internal-page">

  <div class="internal-actions internal-actions--right">
    <?= ui_button('Vedi classifiche', 'primary', 'md', ['icon' => 'fas fa-trophy', 'href' => 'leaderboards.php?game_id=' . $game['game_id']]) ?>
    <?= ui_button('Giocatori bannati', 'primary', 'md', ['icon' => 'fas fa-user-times', 'href' => 'game-bans.php?id=' . $game['game_id']]) ?>
    <?= ui_button('Elimina gioco', 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => "openModal('modal-delete-game', onDeleteGameModalOpen, { gameId: {$game['game_id']}, gameName: '" . escapeChars($game['name']) . "' })"]]) ?>
  </div>

  <h2 class="section-header"><i class="fas fa-gamepad" style="margin-right:16px"></i>Configurazione</h2>

  <div style="display:flex;gap:20px;flex-wrap:wrap">
    <div style="flex:1;min-width:300px">
      <div class="internal-card">
        <div class="internal-card__title"><i class="fas fa-cog"></i> Dettagli gioco</div>
        <label class="form-label">ID del Gioco</label>
        <div class="input-group">
          <input id="input-gameid" class="ui-input" value="<?= $game["game_id"] ?>" disabled style="background:var(--bg-color-sidebar,#f0f0f0)!important">
        </div>

        <label class="form-label" style="margin-top:16px">Secret del Gioco</label>
        <div style="color:var(--text-muted,#666);font-size:0.875em;margin-bottom:12px">Questo token viene usato per aumentare la sicurezza dell'invio dei punteggi.</div>
        <div class="input-group">
          <input id="input-secret" type="password" class="ui-input" value="<?= $game["client_secret"] ?>" disabled>
          <i class="input-regenerate-secret-btn fas fa-sync" onclick="openModal('modal-regenerate-secret')" data-tippy-content="Ottieni un nuovo secret"></i>
          <i class="input-secret-eye-btn fas fa-eye" onclick="toggleSecretVisibility(this)" data-tippy-content="Mostra o nasconde il secret del gioco"></i>
        </div>
      </div>
    </div>

    <div style="flex:1;min-width:300px">
      <div class="internal-card">
        <div class="internal-card__title"><i class="fas fa-edit"></i> Modifica nome</div>
        <form method="POST" action="/game-rename.php?id=<?= $game["game_id"] ?>">
          <div class="input-group">
            <input id="input-game-name" name="name" type="text" class="ui-input" value="<?= htmlspecialchars($game["name"]) ?>" required>
          </div>
          <?= ui_button('Modifica Nome', 'primary', 'md', ['icon' => 'fa fa-edit', 'type' => 'submit', 'class' => 'mt-2']) ?>
        </form>
      </div>
    </div>
  </div>

  <hr style="margin-top:16px;margin-bottom:16px">

  <h3 class="section-header"><i class="fab fa-steam-symbol" style="margin-right:16px"></i>Integrazione con Game Maker</h3>

  <div class="internal-card">
    <div class="code-block-header">Invio di un punteggio:</div>
    <div class="code-block jsHigh">
  var points = 100; // Punti del giocatore<br/>
  var player = "Harry"; // Nome del giocatore<br/>
  var data = "game=<?= $game["game_id"] ?>&leaderboard_id=ID_CLASSIFICA&score=" + string(points) + "&player=" + base64_encode(player);<br/>
  var secret = "SECRET_DEL_GIOCO"; // Mettere il secret del gioco qui<br/>
  var hash = "&hash=" + sha1_string_utf8(data + secret);<br/>
  http_post_string("<?= $baseApiPath ?>/add.php", data + hash);
    </div>
  </div>

  <div class="internal-card">
    <div class="code-block-header">Lista punteggi (Evento Create):</div>
    <div class="code-block jsHigh">
  // Da mettere nell'evento 'Create' di un oggetto.<br/>
  // Questo effettua la richiesta per prendere i punteggi<br/>
  scores = noone;<br/>
  getScores = http_get("<?= $baseApiPath ?>/list.php?game=<?= $game["game_id"] ?>&leaderboard_id=ID_CLASSIFICA");
    </div>
  </div>

  <div class="internal-card">
    <div class="code-block-header">Lista punteggi (Evento Async - HTTP):</div>
    <div class="code-block jsHigh">
  // Da mettere nell'evento 'Async - HTTP' dello stesso oggetto.<br/>
  if (async_load[? "id"] == getScores && async_load[? "status"] == 0) {<br/>
  &nbsp;&nbsp;var result = json_decode(async_load[? "result"]);<br/>
  &nbsp;&nbsp;scores = result[? "scores"];<br/>
  }
    </div>
  </div>

  <div class="internal-card">
    <div class="code-block-header">Esempio di disegno della classifica:</div>
    <div class="code-block jsHigh">
  draw_text(20, 20, "Classifica:");<br/><br/>
  if (scores != noone) {<br/>
  &nbsp;&nbsp;for (var i=0; i&lt;ds_list_size(scores); i++) {<br/>
  &nbsp;&nbsp;&nbsp;&nbsp;var player = scores[| i];<br/>
  &nbsp;&nbsp;&nbsp;&nbsp;draw_text(20, 50+i*20, player[? "username"] + " - " + string(player[? "score"]));<br/>
  &nbsp;&nbsp;}<br/>
  }
    </div>
  </div>

  <hr style="margin-top:16px;margin-bottom:16px">

  <h3 class="section-header"><i class="fas fa-book" style="margin-right:16px"></i>Documentazione API</h3>
  <p style="margin-bottom:16px">Consulta la documentazione completa per scoprire tutte le funzionalità dell'API.</p>
  <?= ui_button('Vai alla Documentazione', 'primary', 'md', ['icon' => 'fa fa-arrow-circle-right', 'href' => 'documentation.php']) ?>
</div>

<?= ui_modal('modal-regenerate-secret', [
  'title' => 'Conferma rigenerazione secret',
  'content' => '<p>Sei sicuro di voler ottenere un nuovo secret del gioco?</p>
    <div style="background:#fff8e1;border-left:4px solid #ffc107;padding:16px;border-radius:8px;margin-top:16px">
      <p><i class="fas fa-exclamation-triangle" style="margin-right:8px"></i><strong>Attenzione:</strong> Se il tuo gioco sta già utilizzando il secret attuale, dovrai aggiornarlo nel codice del gioco e rilasciare una nuova versione per continuare a inviare i punteggi correttamente.</p>
      <p>Questa operazione non è reversibile.</p>
    </div>',
  'footer' =>
    ui_button('Annulla', 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-regenerate-secret')"]]) .
    ui_button('Genera nuovo secret', 'danger', 'md', ['icon' => 'fas fa-sync', 'attrs' => ['onclick' => 'regenerateSecret()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>

<?= ui_modal('modal-delete-game', [
  'title' => 'Conferma eliminazione',
  'content' => '<p>Sei sicuro di voler cancellare il gioco <strong><span id="modal-game-name"></span></strong> ?</p><p>L\'operazione non è reversibile.</p>',
  'footer' =>
    ui_button('Annulla', 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-delete-game', onDeleteGameModalClose)"]]) .
    ui_button('Elimina gioco', 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'deleteGame()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>

<script>
const modalGameDiv = document.getElementById('modal-game-name');
let modalSelectedGameId;

function onDeleteGameModalOpen({ gameId, gameName }) {
  modalSelectedGameId = gameId;
  modalGameDiv.innerHTML = gameName;
}

function onDeleteGameModalClose() {
  modalGameDiv.innerHTML = "";
}

function deleteGame() {
  location.href = "delete-game.php?id=" + modalSelectedGameId;
}

</script>

<?php require_once("game.view.script.php"); ?>
