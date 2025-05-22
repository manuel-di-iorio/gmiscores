<style>
.w3-code {
  font-size: 14px; /* Slightly larger for readability */
  background-color: #f8f8f8; /* Very light grey for code background */
  border: 1px solid #e0e0e0; /* Lighter border */
  padding: .7rem 1rem;
  border-bottom-left-radius: 4px;
  border-bottom-right-radius: 4px;
  overflow-x: auto; /* Ensure horizontal scroll for long lines */
  line-height: 1.6; /* Improved line height */
  margin-top: 0 !important;
}

.input-group {
  position: relative;
  margin-bottom: 1.2rem; /* Increased margin */
}

.input-secret-eye-btn {
  position: absolute;
  right: 1rem; /* Adjusted for padding */
  top: 50%; /* Center vertically */
  transform: translateY(-50%); /* Center vertically */
  transition: color .2s;
  cursor: pointer;
  padding: 0.5rem; /* Added padding for better click area */
  color: #777; /* Slightly lighter icon color */
}

.input-regenerate-secret-btn {
  position: absolute;
  right: 4rem; /* Adjusted for padding and spacing from eye icon */
  top: 50%; /* Center vertically */
  transform: translateY(-50%); /* Center vertically */
  transition: color .2s;
  cursor: pointer;
  padding: 0.5rem; /* Added padding for better click area */
  color: #777; /* Slightly lighter icon color */
}

.input-secret-eye-btn:hover,
.input-regenerate-secret-btn:hover {
  color: #000; /* Black on hover */
}

.w3-card-4 {
  box-shadow: 0 6px 12px rgba(0,0,0,0.15), 0 4px 10px rgba(0,0,0,0.12); /* Enhanced shadow */
}
.w3-round-large {
  border-radius: 8px;
}
.section-header {
  border-bottom: 2px solid #e0e0e0; /* Lighter border */
  padding-bottom: 0.8rem; /* Increased padding */
  margin-top: 2rem; /* Added top margin for better separation */
  margin-bottom: 1.8rem; /* Increased margin */
  font-size: 1.6rem; /* Slightly larger */
  color: #222; /* Darker text */
  font-weight: 500; /* Medium weight */
}
.section-header:first-of-type {
    margin-top: 0.5rem;
}
.code-block-header {
  background-color: #333; /* Darker header for code blocks */
  color: #fff; /* White text for dark header */
  padding: 0.6rem 1.2rem;
  border-top-left-radius: 4px;
  border-top-right-radius: 4px;
  font-weight: bold;
  margin-top: 1.5rem;
}
.form-label {
  font-weight: 600; /* Bolder label */
  margin-bottom: 0.6rem;
  display: block;
  color: #444; /* Darker label color */
}
.w3-input.w3-border {
    border-color: #ccc !important; /* Ensure border color is applied */
}
</style>

<div class="w3-container w3-padding-large">

  <h2 class="section-header"><i class="fas fa-gamepad w3-margin-right"></i>Configurazione</h2>

  <div class="w3-row-padding">
    <div class="w3-half w3-margin-bottom">
      <div class="w3-card-4 w3-round-large w3-padding-large">
        <label for="input-gameid" class="form-label">ID del Gioco</label>
        <div class="input-group">
          <input id="input-gameid" class="w3-input w3-border w3-round" value="<?= $game["game_id"] ?>" disabled>
        </div>

        <label for="input-secret" class="form-label">Secret del Gioco</label>
        <div class="w3-text-grey w3-small w3-margin-bottom">Questo token viene usato per aumentare la sicurezza dell'invio dei punteggi.</div>
        <div class="input-group">
          <input id="input-secret" type="password" class="w3-input w3-border w3-round" value="<?= $game["client_secret"] ?>" disabled>
          <i class="input-regenerate-secret-btn fas fa-sync w3-hover-text-black" onclick="openModal('modal-regenerate-secret')" data-tippy-content="Ottieni un nuovo secret"></i>
          <i class="input-secret-eye-btn fas fa-eye w3-hover-text-black" onclick="toggleSecretVisibility(this)" data-tippy-content="Mostra o nasconde il secret del gioco"></i>
        </div>
      </div>
    </div>

    <div class="w3-half w3-margin-bottom">
      <div class="w3-card-4 w3-round-large w3-padding-large">
        <label for="input-game-name" class="form-label">Modifica Nome Gioco</label>
        <form method="POST" action="/game-rename.php?id=<?= $game["game_id"] ?>">
          <div class="input-group">
            <input id="input-game-name" name="name" type="text" class="w3-input w3-border w3-round" value="<?= htmlspecialchars($game["name"]) ?>" required>
          </div>
          <button type="submit" class="w3-button w3-black w3-round-large w3-hover-opacity">
            <i class="fa fa-edit w3-margin-right"></i>Modifica Nome
          </button>
        </form>
      </div>
    </div>
  </div>

  <hr class="w3-margin-top w3-margin-bottom">

  <h3 class="section-header"><i class="fab fa-steam-symbol w3-margin-right"></i>Integrazione con Game Maker Studio 2</h3>

  <div class="w3-card-4 w3-round-large w3-margin-bottom">
    <div class="code-block-header">Invio di un punteggio:</div>
    <div class="w3-code jsHigh">
  var points = 100; // Punti del giocatore<br/>
  var player = "Harry"; // Nome del giocatore<br/>
  var data = "game=<?= $game["game_id"] ?>&score=" + string(points) + "&player=" + base64_encode(player);<br/>
  var secret = "SECRET_DEL_GIOCO"; // Mettere il secret del gioco qui<br/>
  var hash = "&hash=" + sha1_string_utf8(data + secret);<br/>
  http_post_string("<?= $baseApiPath ?>/add.php", data + hash);
    </div>
  </div>

  <div class="w3-card-4 w3-round-large w3-margin-bottom">
    <div class="code-block-header">Lista punteggi (Evento Create):</div>
    <div class="w3-code jsHigh">
  // Da mettere nell'evento 'Create' di un oggetto.<br/>
  // Questo effettua la richiesta per prendere i punteggi<br/>
  scores = noone;<br/>
  getScores = http_get("<?= $baseApiPath ?>/list.php?game=<?= $game["game_id"] ?>");
    </div>
  </div>

  <div class="w3-card-4 w3-round-large w3-margin-bottom">
    <div class="code-block-header">Lista punteggi (Evento Async - HTTP):</div>
    <div class="w3-code jsHigh">
  // Da mettere nell'evento 'Async - HTTP' dello stesso oggetto.<br/>
  if (async_load[? "id"] == getScores && async_load[? "status"] == 0) {<br/>
  &nbsp;&nbsp;var result = json_decode(async_load[? "result"]);<br/>
  &nbsp;&nbsp;scores = result[? "scores"];<br/>
  }
    </div>
  </div>

  <div class="w3-card-4 w3-round-large w3-margin-bottom">
    <div class="code-block-header">Esempio di disegno della classifica:</div>
    <div class="w3-code jsHigh">
  draw_text(20, 20, "Classifica:");<br/><br/>
  if (scores != noone) {<br/>
  &nbsp;&nbsp;for (var i=0; i&lt;ds_list_size(scores); i++) {<br/>
  &nbsp;&nbsp;&nbsp;&nbsp;var player = scores[| i];<br/>
  &nbsp;&nbsp;&nbsp;&nbsp;draw_text(20, 50+i*20, player[? "username"] + " - " + string(player[? "score"]));<br/>
  &nbsp;&nbsp;}<br/>
  }
    </div>
  </div>

  <hr class="w3-margin-top w3-margin-bottom">

  <h3 class="section-header"><i class="fas fa-book w3-margin-right"></i>Documentazione API</h3>
  <p class="w3-margin-bottom">Consulta la documentazione completa per scoprire tutte le funzionalità dell'API.</p>
  <a href="documentation.php" class="w3-button w3-black w3-round-large w3-hover-opacity w3-padding-large">
    <i class="fa fa-arrow-circle-right w3-margin-right"></i>Vai alla Documentazione
  </a>
</div>

<!-- Modal regenerate secret -->
<div id="modal-regenerate-secret" class="w3-modal">
  <div class="w3-modal-content w3-animate-top w3-card-4 w3-round-large">
    <header class="w3-container w3-black w3-round-top-large">
      <span onclick="closeModal('modal-regenerate-secret')" class="w3-button w3-display-topright w3-hover-red w3-round-top-right">&times;</span>
      <h4>Conferma Rigenerazione Secret</h4>
    </header>
    <div class="w3-container w3-padding-large">
      <p class="w3-large">Sei sicuro di voler ottenere un nuovo secret del gioco?</p>
      <div class="w3-panel w3-pale-yellow w3-leftbar w3-border-yellow w3-padding-16 w3-round-large w3-margin-top">
        <p><i class="fas fa-exclamation-triangle w3-margin-right"></i><strong>Attenzione:</strong> Se il tuo gioco sta già utilizzando il secret attuale, dovrai aggiornarlo nel codice del gioco e rilasciare una nuova versione per continuare a inviare i punteggi correttamente.</p>
        <p class="w3-text-dark-grey">Questa operazione non è reversibile.</p>
      </div>
    </div>
    <footer class="w3-container w3-light-grey w3-padding w3-right-align w3-round-bottom-large">
      <button type="button" class="w3-button w3-text-grey w3-hover-dark-grey w3-round w3-margin-right" onclick="closeModal('modal-regenerate-secret')">Annulla</button>
      <button type="button" onclick="regenerateSecret()" class="w3-button w3-red w3-round w3-hover-opacity">
        <i class="fas fa-sync w3-margin-right"></i>Genera nuovo secret
      </button>
    </footer>
  </div>
</div>

<?php require_once("game.view.script.php"); ?>
