<div class="internal-page documentation-page">

  <div class="documentation-section">
    <p class="documentation-text">Base URL: <code class="inline-code shadow-sm"><?= $config["host"] ?>/api/v1</code></p>
    <p class="documentation-text">Request content-type: <code class="inline-code shadow-sm">application/x-www-form-urlencoded</code></p>
    <p class="documentation-text">Response content-type: <code class="inline-code shadow-sm">application/json</code></p>
  </div>

  <div class="documentation-section">
    <h5 class="documentation-subtitle"><?= __('docs_subtitle') ?></h5>

    <div class="accordion-container">
      <button class="accordion-header">
        <span><?= __('docs_accordion_add') ?></span>
        <i class="fas fa-chevron-down accordion-icon text-gray-400"></i>
      </button>
      <div class="accordion-content" style="display:none">
        <div class="code-block jsHigh">
          POST /add.php<br/><br/>

          <?= __('docs_params_title') ?><br/>
          &nbsp;&nbsp;game {int} = <?= __('docs_param_game_id') ?><br/>
          &nbsp;&nbsp;leaderboard_id {int} = <?= __('docs_param_lb_id') ?><br/>
          &nbsp;&nbsp;tags {string} = <?= __('docs_param_tags') ?><br/>
          &nbsp;&nbsp;score {float} = <?= __('docs_param_score') ?><br/>
          &nbsp;&nbsp;player {string} = <?= __('docs_param_player') ?><br/>
          &nbsp;&nbsp;hash {string} = sha1("game=" + game_id + "&leaderboard_id=" + leaderboard_id + "&score=" + score + "&player=" + player + secret)<br/>
          &nbsp;&nbsp;insertMode {string} = <?= __('docs_param_insert_mode') ?><br/>
          &nbsp;&nbsp;data {string} = <?= __('docs_param_data') ?><br/>
          &nbsp;&nbsp;env {string} = <?= __('docs_param_env') ?><br/><br/>

          <?= __('docs_response') ?><br/>
          { "status": 200, "scoreAction": "inserted", "position": 4 }<br/><br/>

          // <?= __('docs_comment_score_action') ?><br/>
          // <?= __('docs_comment_position') ?>
        </div>

        <h6 class="documentation-example-title"><?= __('docs_example_gms') ?></h6>
        <div class="code-block jsHigh">
          var points = 100; // <?= __('docs_code_points') ?><br/>
          var player = "Harry"; // <?= __('docs_code_player') ?><br/>
          var data = "game=ID&leaderboard_id=ID_LEADERBOARD&score=" + string(points) + "&player=" + base64_encode(player);<br/>
          var secret = "SECRET_DEL_GIOCO"; // <?= __('docs_code_secret') ?><br/>
          var hash = "&hash=" + sha1_string_utf8(data + secret);<br/>
          http_post_string("<?= $baseApiPath ?>/add.php", data + hash);
        </div>

        <div class="panel-info">
          <p><i class="fas fa-info-circle mr-2"></i><?= __('docs_note_add') ?></p>
        </div>
      </div>
    </div>

    <div class="accordion-container">
      <button class="accordion-header">
        <span><?= __('docs_accordion_list') ?></span>
        <i class="fas fa-chevron-down accordion-icon text-gray-400"></i>
      </button>
      <div class="accordion-content" style="display:none">
        <div class="code-block jsHigh">
          GET /list.php<br/><br/>

          <?= __('docs_params_query') ?><br/>
          &nbsp;&nbsp;game {int} = <?= __('docs_param_list_game_id') ?><br/>
          &nbsp;&nbsp;leaderboard_id {int} = <?= __('docs_param_list_lb_id') ?><br/>
          &nbsp;&nbsp;tags {string} = <?= __('docs_param_list_tags') ?><br/>
          &nbsp;&nbsp;page {int} = <?= __('docs_param_list_page') ?><br/>
          &nbsp;&nbsp;limit {int} = <?= __('docs_param_list_limit') ?><br/>
          &nbsp;&nbsp;order {string} = <?= __('docs_param_list_order') ?><br/>
          &nbsp;&nbsp;player {int|string} = <?= __('docs_param_list_player') ?><br/>
          &nbsp;&nbsp;startTime {string} = <?= __('docs_param_list_start') ?><br/>
          &nbsp;&nbsp;endTime {string} = <?= __('docs_param_list_end') ?><br/>
          &nbsp;&nbsp;includePlayer {string} = <?= __('docs_param_list_include_player') ?><br/>
          &nbsp;&nbsp;env {string} = <?= __('docs_param_list_env') ?><br/>
          &nbsp;&nbsp;hash {string} = <?= __('docs_param_list_hash') ?><br/><br/>

          <?= __('docs_response_example') ?><br/>
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
          // <?= __('docs_note1') ?><br/>
          // <?= __('docs_note2') ?><br/>
        </div>

        <h6 class="documentation-example-title"><?= __('docs_example_gms2') ?></h6>
        <div class="code-block jsHigh">
          // <?= __('docs_code_create_comment') ?><br/>
          // <?= __('docs_code_request_comment') ?><br/>
          scores = noone;<br/>
          getScores = http_get("<?= $baseApiPath ?>/list.php?game=ID");
        </div>
        <div class="code-block jsHigh">
          // <?= __('docs_code_async_comment') ?><br/>
          if (async_load[? "id"] == getScores && async_load[? "status"] == 0) {<br/>
          &nbsp;&nbsp;var result = json_decode(async_load[? "result"]);<br/>
          &nbsp;&nbsp;scores = result[? "scores"];<br/>
          }
        </div>
      </div>
    </div>
  </div>

  <div class="documentation-section">
    <h5 class="documentation-subtitle"><?= __('docs_security_title') ?></h5>
    <!-- <div class="accordion-container">
      <button class="accordion-header" style="display:block;width:100%;text-align:left;background:var(--bg-color-offset,#f1f1f1);border:none;padding:8px 16px;cursor:pointer">
        <span style="margin-right:16px">Secret e hash</span>
        <i class="fas fa-chevron-down accordion-icon"></i>
      </button>
      <div class="accordion-content"> -->
        <p class="documentation-text"><?= __('docs_security_text') ?></p>
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
    <h5 class="documentation-subtitle"><?= __('docs_errors_title') ?></h5>
    <p class="documentation-text"><?= __('docs_errors_text') ?></p>
    <div class="code-block jsHigh">
    {<br/>
    &nbsp;&nbsp;"message": "&lt;messaggio&gt;",<br/>
    &nbsp;&nbsp;"code": "&lt;ID errore&gt;",<br/>
    &nbsp;&nbsp;"status": &lt;http status code&gt;<br/>
    }
    </div>
  </div>

  <div class="documentation-section">
    <h5 class="documentation-subtitle"><?= __('docs_resources_title') ?></h5>
    <div class="accordion-container">
      <button class="accordion-header">
        <span><i class="fas fa-download mr-2"></i><?= __('docs_resources_library') ?></span>
        <i class="fas fa-chevron-down accordion-icon text-gray-400"></i>
      </button>
      <div class="accordion-content">
        <p class="documentation-text"><?= __('docs_resources_desc') ?></p>
        <?= ui_button(__('docs_resources_download'), 'primary', 'md', ['icon' => 'fa fa-download', 'href' => '/sdk/GameMaker/sdk.yymps', 'attrs' => ['download' => ''], 'class' => 'mb-4']) ?>
        <p class="documentation-text"><strong><?= __('docs_resources_preview') ?></strong></p>
        <div class="code-block jsHigh">
        gmi_scores_send({ leaderboard_id: 30, player: "Harry", score: 5000 }); // <?= __('docs_resources_preview_send') ?>
        </div>
        <div class="code-block jsHigh">
        gmi_scores_get_list({ leaderboard_id: 30 }); // <?= __('docs_resources_preview_list') ?>
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
