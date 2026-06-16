<style>
.code-block {
  font-size: 13px;
}

.inline-code {
  font-size: 14px;
}
</style>

<div class="internal-page documentation-page">

  <div class="documentation-section">
    <p class="documentation-text">Base URL: <code class="inline-code"><?= $config["host"] ?>/api/v1</code></p>
    <p class="documentation-text">Request content-type: <code class="inline-code">application/x-www-form-urlencoded</code></p>
    <p class="documentation-text">Response content-type: <code class="inline-code">application/json</code></p>
  </div>

  <div class="documentation-section">
    <h5 class="documentation-subtitle"><strong>Lista API</strong></h5>

    <div class="accordion-container">
      <button class="accordion-header" style="display:block;width:100%;text-align:left;background:var(--bg-color-offset,#f1f1f1);border:none;padding:8px 16px;cursor:pointer">
        <span style="margin-right:16px">Invio di un punteggio</span>
        <i class="fas fa-chevron-down accordion-icon"></i>
      </button>
      <div class="accordion-content" style="display:none">
        <div class="code-block jsHigh">
          POST /add.php<br/><br/>

          Parametri (x-www-form-urlencoded):<br/>
          &nbsp;&nbsp;game {int} = ID del gioco<br/>
          &nbsp;&nbsp;leaderboard_id {int} = ID della leaderboard (obbligatorio)<br/>
          &nbsp;&nbsp;tags {string} = Tags associati al punteggio (opzionale, "default" è usato come valore predefinito)<br/>
          &nbsp;&nbsp;score {float} = Punteggio da inviare<br/>
          &nbsp;&nbsp;player {string} = Nome del giocatore in base64<br/>
          &nbsp;&nbsp;hash {string} = sha1("game=" + game_id + "&leaderboard_id=" + leaderboard_id + "&score=" + score + "&player=" + player + secret)<br/>
          &nbsp;&nbsp;insertMode {string} = Modalità di inserimento punteggi ("higher" [default], "lower", "all"). Ad esempio, usando 'higher', il punteggio migliore del giocatore aggiornerà quello precedente, piuttosto che inserirlo in ogni caso.<br/>
          &nbsp;&nbsp;data {string} = Stringa opzionale. Per salvare dati custom associati al punteggio (max 64kb)<br/>
          &nbsp;&nbsp;env {string} = Ambiente ("production" [default] o "test"). I punteggi di test non compaiono nella classifica di produzione.<br/><br/>

          Risposta:<br/>
          { "status": 200, "scoreAction": "inserted", "position": 4 }<br/><br/>

          // <strong>scoreAction</strong> indica se il punteggio è stato inserito o aggiornato.<br/>
          // <strong>position</strong> indica la posizione in classifica del punteggio appena aggiunto.
        </div>

        <h6 class="documentation-example-title">Esempio con Game Maker Studio</h6>
        <div class="code-block jsHigh">
          var points = 100; // Punti del giocatore<br/>
          var player = "Harry"; // Nome del giocatore<br/>
          var data = "game=ID&leaderboard_id=ID_LEADERBOARD&score=" + string(points) + "&player=" + base64_encode(player);<br/>
          var secret = "SECRET_DEL_GIOCO"; // Il secret si ottiene dopo aver creato un gioco<br/>
          var hash = "&hash=" + sha1_string_utf8(data + secret);<br/>
          http_post_string("<?= $baseApiPath ?>/add.php", data + hash);
        </div>

        <div class="panel-info">
          <p><i class="fas fa-info-circle" style="margin-right:8px"></i>Nota: se si vuole aggiornare la classifica subito dopo aver inviato il punteggio, è necessario farlo solo dopo che la richiesta è stata processata, perchè altrimenti il nuovo punteggio non sarà immediatamente visibile.</p>
        </div>
      </div>
    </div>

    <div class="accordion-container">
      <button class="accordion-header" style="display:block;width:100%;text-align:left;background:var(--bg-color-offset,#f1f1f1);border:none;padding:8px 16px;cursor:pointer">
        <span style="margin-right:16px">Lista punteggi</span>
        <i class="fas fa-chevron-down accordion-icon"></i>
      </button>
      <div class="accordion-content" style="display:none">
        <div class="code-block jsHigh">
          GET /list.php<br/><br/>

          Parametri (query):<br/>
          &nbsp;&nbsp;game {int} = ID del gioco<br/>
          &nbsp;&nbsp;leaderboard_id {int} = ID della leaderboard (obbligatorio)<br/>
          &nbsp;&nbsp;tags {string} = Tags per filtrare (opzionale)<br/>
          &nbsp;&nbsp;page {int} = Paginazione risultati (di default 0, cioè prima pagina di punteggi).<br/>
          &nbsp;&nbsp;limit {int} = Numero di punteggi da visualizzare (di default 10, max 1000 per pagina).<br/>
          &nbsp;&nbsp;order {string} = "ASC" o "DESC". Ordinamento punteggi (di default DESC).<br/>
          &nbsp;&nbsp;player {int|string} = ID o nome del giocatore (base64). Se specificato, verranno ritornati solo i punteggi di questo giocatore.<br/>
          &nbsp;&nbsp;startTime {string} = Se specificato, filtra i punteggi partendo da questa data (es. "2020-05-04" oppure "2020-05-04 22:20:20").<br/>
          &nbsp;&nbsp;endTime {string} = Se specificato, filtra i punteggi fino a questa data.<br/>
          &nbsp;&nbsp;includePlayer {string} = Nome del giocatore (base64). Se specificato, include nella risposta il punteggio migliore del giocatore, in base ai filtri order/startTime/endTime. Questo è utile quando si vuole conoscere il punteggio del giocatore anche quando non rientra tra i primi classificati.<br/>
          &nbsp;&nbsp;env {string} = Ambiente ("production" [default], "test", "all"). Filtra i punteggi per ambiente.<br/>
          &nbsp;&nbsp;hash {string} = Obbligatorio per classifiche protette. sha1("game=" + game_id + "&leaderboard_id=" + leaderboard_id + secret).<br/><br/>

          Esempio di risposta:<br/>
          {<br/>
          &nbsp;&nbsp;"status": 200,<br/>
          &nbsp;&nbsp;"scores": [<br/>
          &nbsp;&nbsp;&nbsp;&nbsp;{ "player_id": 130, "username": "Freank", "score": 2000, "created_at": "2020-05-03 08:58:12", "updated_at": "2020-05-03 08:58:12" },<br/>
          &nbsp;&nbsp;&nbsp;&nbsp;{ "player_id": 54, "username": "Jak", "score": 1200, "created_at": "2020-05-04 22:20:20", "updated_at": "2020-05-04 22:20:20" }<br/>
          &nbsp;&nbsp;],<br/>
          <br/>
          &nbsp;&nbsp;"playerScore": {<br/>
          &nbsp;&nbsp;&nbsp;&nbsp;"player_id": 75, "username": "Rolando", "score": 1000, "created_at": "2020-05-04 22:20:20", "updated_at": "2020-05-04 22:20:20", "position": 1 }<br/>
          &nbsp;&nbsp;}<br/>
          }<br/><br/>
          // Nota 1: se nell'invio è stato usato il 'sign', ogni punteggio avrà anche la chiave 'sign'.<br/>
          // Nota 2: playerScore sarà null se non è stato specificato il parametro 'includePlayer'.<br/>
        </div>

        <h6 class="documentation-example-title">Esempio con Game Maker Studio</h6>
        <div class="code-block jsHigh">
          // Da mettere nell'evento 'Create' di un oggetto.<br/>
          // Questo effettua la richiesta per prendere i punteggi<br/>
          scores = noone;<br/>
          getScores = http_get("<?= $baseApiPath ?>/list.php?game=ID");
        </div>
        <div class="code-block jsHigh">
          // Da mettere nell'evento 'Async HTTP' dello stesso oggetto.<br/>
          if (async_load[? "id"] == getScores && async_load[? "status"] == 0) {<br/>
          &nbsp;&nbsp;var result = json_decode(async_load[? "result"]);<br/>
          &nbsp;&nbsp;scores = result[? "scores"];<br/>
          }
        </div>
      </div>
    </div>
  </div>

  <div class="documentation-section">
    <h5 class="documentation-subtitle"><strong>Sicurezza dei punteggi inviati</strong></h5>
    <!-- <div class="accordion-container">
      <button class="accordion-header" style="display:block;width:100%;text-align:left;background:var(--bg-color-offset,#f1f1f1);border:none;padding:8px 16px;cursor:pointer">
        <span style="margin-right:16px">Secret e hash</span>
        <i class="fas fa-chevron-down accordion-icon"></i>
      </button>
      <div class="accordion-content"> -->
        <p class="documentation-text">Ogni gioco ha un proprio 'secret' che permette di firmare la richiesta di invio punti o visione classifica. <br/>Sul server viene verificato automaticamente che i dati trasmessi siano stati effettivamente generati con il proprio secret, rendendo vana la tecnica di sniffing del traffico HTTP del proprio gioco.</p>
      <!-- </div> -->
    </div>

    <!-- <div class="accordion-container">
      <button class="accordion-header" style="display:block;width:100%;text-align:left;background:var(--bg-color-offset,#f1f1f1);border:none;padding:8px 16px;cursor:pointer">
        <span style="margin-right:16px">Firma con chiave privata (opzionale)</span>
        <i class="fas fa-chevron-down accordion-icon"></i>
      </button>
      <div class="accordion-content" style="display:none">
        <p class="documentation-text">La firma con chiave privata è un meccanismo di sicurezza aggiuntiva che permette di verificare automaticamente che non sia stata effettuata una manipolazione dei punteggi, ad esempio se modificati a mano nel database. Il sistema di firma è lo stesso del secret con l'unica differenza che la chiave la conosce solo lo sviluppatore e non è mai trasmessa online.<br/>
        Come funziona la verifica? Si confronta la firma associata al punteggio (salvata sul db) con la firma calcolata in locale. Se sono diverse, il punteggio è stato modificato a mano.</p>
        <hr class="documentation-divider">
        <h6 class="documentation-example-title">Esempio con Game Maker Studio</h6>
        <div class="code-block jsHigh">
          /* Invio di un punteggio */<br/>
          var points = 100; // Punti del giocatore<br/>
          var player = "Harry"; // Nome del giocatore<br/>
          var data = "game=ID&score=" + string(points) + "&player=" + base64_encode(player);<br/>
          var secret = "SECRET_DEL_GIOCO"; // Il secret si ottiene dopo aver creato un gioco<br/>
          var private_key = "CHIAVE_PRIVATA";<br/>
          var hash = "&hash=" + sha1_string_utf8(data + secret);<br/>
          var signature = "&sign=" + sha1_string_utf8(data + private_key);<br/>
          http_post_string("<?= $baseApiPath ?>/add.php", data + hash + signature);
        </div>
        <div class="code-block jsHigh">
          /* Verifica della lista punteggi (evento Async HTTP) */<br/>
          var private_key = "CHIAVE_PRIVATA";<br/>
          var result = json_decode(async_load[? "result"]);<br/>
          scores = result[? "scores"];<br/><br/>

          for (var i=0; i&lt;ds_list_size(scores); i++) {<br/>
          &nbsp;&nbsp;var record = scores[| i];<br/>
          &nbsp;&nbsp;var data = "game=ID&score=" + string(record[? "score"]) + "&player=" + base64_encode(record[? "username"]);<br/><br/>
            
          &nbsp;&nbsp;// Confronto la firma associata al punteggio con la firma calcolata in locale, se sono diverse, il punteggio è stato modificato a mano<br/>
          &nbsp;&nbsp;if (record[? "sign"] != sha1_string_utf8(data + private_key)) {<br/>
          &nbsp;&nbsp;&nbsp;&nbsp;// Show error<br/>
          &nbsp;&nbsp;}<br/>
          }<br/>
        </div>
      </div>
    </div> -->

  <div class="documentation-section">
    <h5 class="documentation-subtitle"><strong>Gestione degli errori</strong></h5>
    <p class="documentation-text">Se una richiesta fallisce, la risposta sarà di questo tipo:</p>
    <div class="code-block jsHigh">
    {<br/>
    &nbsp;&nbsp;"message": "&lt;messaggio&gt;",<br/>
    &nbsp;&nbsp;"code": "&lt;ID errore&gt;",<br/>
    &nbsp;&nbsp;"status": &lt;http status code&gt;<br/>
    }
    </div>
  </div>

  <div class="documentation-section">
    <h5 class="documentation-subtitle"><strong>Risorse</strong></h5>
    <div class="accordion-container">
      <button class="accordion-header" style="display:block;width:100%;text-align:left;background:var(--bg-color-offset,#f1f1f1);border:none;padding:8px 16px;cursor:pointer">
        <span style="margin-right:16px"><i class="fas fa-download" style="margin-right:8px"></i>Scarica la libreria per Game Maker</span>
        <i class="fas fa-chevron-down accordion-icon"></i>
      </button>
      <div class="accordion-content">
        <p class="documentation-text">Include gli scripts pronti per inviare e leggere i punteggi. Per importarla, basta trascinare il file sull'editor</p>
        <?= ui_button('Scarica v0.9.3', 'primary', 'md', ['icon' => 'fa fa-download', 'href' => '/files/gms2_3-gmi_cloud-v0_9_3.yymps', 'attrs' => ['download' => ''], 'class' => 'mb-4']) ?>
        <p class="documentation-text"><strong>Anteprima:</strong></p>
        <div class="code-block jsHigh">
        gmi_scores_send({ player: "Harry", score: 5000 }); // Invia il punteggio 5000 del player Harry
        </div>
        <div class="code-block jsHigh">
        gmi_scores_get_list(); // Ottiene la lista dei punteggi
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://www.w3schools.com/lib/w3codecolor.js"></script>
<script>
  w3CodeColor();

  // Accordion toggle
  var accordions = document.getElementsByClassName("accordion-header");
  for (var i = 0; i < accordions.length; i++) {
    accordions[i].addEventListener("click", function() {
      this.classList.toggle("active");
      var content = this.nextElementSibling;
      var icon = this.querySelector('.accordion-icon');
      
      if (content.style.display !== "none") {
        content.style.maxHeight = null;
        content.style.display = "none";
        icon.classList.remove("fa-chevron-up");
        icon.classList.add("fa-chevron-down");
      } else {
        content.style.display = "block";
        content.style.maxHeight = content.scrollHeight + "px";
        icon.classList.remove("fa-chevron-down");
        icon.classList.add("fa-chevron-up");
      }
    });
  }
</script>
