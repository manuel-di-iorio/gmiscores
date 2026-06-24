<div class="internal-page documentation-page">

  <!-- API Overview Card -->
  <div class="api-overview-card">
    <div class="api-overview-info">
      <div class="api-overview-item">
        <span class="api-overview-label">Base URL</span>
        <div class="api-overview-value">
          <code id="api-base-url"><?= htmlspecialchars($config["host"]) ?>/api/v1</code>
          <button class="api-overview-copy-btn" onclick="copyTextToClipboard('api-base-url', this)" data-tippy-content="Copia URL">
            <i class="far fa-copy"></i>
          </button>
        </div>
      </div>
      <div class="api-overview-item">
        <span class="api-overview-label">Request Format</span>
        <div class="api-overview-value">
          <code>application/x-www-form-urlencoded</code>
        </div>
      </div>
      <div class="api-overview-item">
        <span class="api-overview-label">Response Format</span>
        <div class="api-overview-value">
          <code>application/json</code>
        </div>
      </div>
    </div>
  </div>

  <?php
  // SECURITY TAB CONTENT
  ob_start();
  ?>
  <div class="security-card">
    <div class="security-icon-container">
      <i class="fas fa-shield-alt"></i>
    </div>
    <div>
      <p class="documentation-text" style="margin-bottom:0"><?= __('docs_security_text') ?></p>
    </div>
  </div>

  <div style="margin-top: 24px;">
    <h6 class="documentation-example-title" style="margin-bottom: 12px"><i class="fas fa-lock" style="margin-right: 8px"></i><?= __('docs_security_how_title') ?></h6>
    <p class="documentation-text"><?= __('docs_security_how_text') ?></p>

    <div class="terminal-mockup" style="margin-top: 16px;">
      <div class="terminal-header">
        <span class="terminal-title"><?= __('docs_security_example_title') ?></span>
      </div>
      <div class="terminal-body code-block-wrapper">
        <button class="copy-code-btn" onclick="copyBlockContent(this)" data-tippy-content="Copia codice">
          <i class="far fa-copy"></i>
        </button>
        <div class="code-block jsHigh">// <?= __('docs_security_example_comment') ?><br/>var data = "game=" + game_id + "&amp;leaderboard_id=" + lb_id + "&amp;score=" + score + "&amp;player=" + base64_encode(player);<br/>var secret = "SECRET_DEL_GIOCO";<br/>var hash = sha1_string_utf8(data + secret);<br/><br/>// <?= __('docs_security_example_send') ?><br/>http_post_string(ENDPOINT + "/add.php", data + "&amp;hash=" + hash);</div>
      </div>
    </div>

    <div style="margin-top: 20px;">
      <h6 class="documentation-example-title" style="margin-bottom: 12px"><i class="fas fa-check-circle" style="margin-right: 8px; color: #4ade80"></i><?= __('docs_security_protected_title') ?></h6>
      <ul class="documentation-text" style="margin-left: 20px; line-height: 1.8;">
        <li><?= __('docs_security_protected_1') ?></li>
        <li><?= __('docs_security_protected_2') ?></li>
        <li><?= __('docs_security_protected_3') ?></li>
        <li><?= __('docs_security_protected_4') ?></li>
      </ul>
    </div>
  </div>

  <div class="panel-info" style="margin-top: 24px; border-color: #f59e0b; display: block;">
    <p style="margin-bottom: 8px;"><i class="fas fa-exclamation-triangle mr-2" style="color: #f59e0b"></i><strong><?= __('docs_security_limitation_title') ?></strong></p>
    <p class="documentation-text" style="margin-bottom: 0;"><?= __('docs_security_limitation_text') ?></p>
  </div>
  <?php
  $securityContent = ob_get_clean();

  // ERRORS TAB CONTENT
  ob_start();
  ?>
  <div class="api-endpoint-grid">
    <div class="api-endpoint-left">
      <p class="documentation-text"><?= __('docs_errors_text') ?></p>
    </div>
    <div class="api-endpoint-right">
      <div class="terminal-mockup">
        <div class="terminal-header">
          <span class="terminal-title">Error Response JSON</span>
        </div>
        <div class="terminal-body code-block-wrapper">
          <button class="copy-code-btn" onclick="copyBlockContent(this)" data-tippy-content="Copia codice">
            <i class="far fa-copy"></i>
          </button>
          <div class="code-block jsHigh">{<br/>&nbsp;&nbsp;"message": "&lt;messaggio&gt;",<br/>&nbsp;&nbsp;"code": "&lt;ID errore&gt;",<br/>&nbsp;&nbsp;"status": &lt;http status code&gt;<br/>}</div>
        </div>
      </div>
    </div>
  </div>
  <?php
  $errorsContent = ob_get_clean();

  // RESOURCES TAB CONTENT
  ob_start();
  ?>
  <div class="download-card" style="margin-bottom:24px">
    <div class="download-icon">
      <i class="fas fa-file-download"></i>
    </div>
    <h3 class="font-bold text-lg" style="margin: 0; color: var(--text-color-headings);"><?= __('docs_resources_library') ?></h3>
    <p class="documentation-text" style="margin: 0; font-size: 0.9em;"><?= __('docs_resources_desc') ?></p>
    <?= ui_button(__('docs_resources_download'), 'primary', 'md', ['icon' => 'fa fa-download', 'href' => '/sdk/GameMaker/sdk.yymps', 'attrs' => ['download' => ''], 'class' => 'ripple-btn']) ?>
  </div>

  <div class="resources-grid">
    <div class="api-endpoint-right">
      <h6 class="documentation-example-title"><?= __('docs_sdk_quickstart_title') ?></h6>
      <div class="terminal-mockup">
        <div class="terminal-header">
          <span class="terminal-title">Create Event</span>
        </div>
        <div class="terminal-body code-block-wrapper">
          <button class="copy-code-btn" onclick="copyBlockContent(this)" data-tippy-content="Copia codice">
            <i class="far fa-copy"></i>
          </button>
          <div class="code-block jsHigh">gmi_init(GAME_ID, "GAME_SECRET");</div>
        </div>
      </div>

      <div class="terminal-mockup" style="margin-top: 16px;">
        <div class="terminal-header">
          <span class="terminal-title">Async HTTP Event</span>
        </div>
        <div class="terminal-body code-block-wrapper">
          <button class="copy-code-btn" onclick="copyBlockContent(this)" data-tippy-content="Copia codice">
            <i class="far fa-copy"></i>
          </button>
          <div class="code-block jsHigh">gmi_event_http(); // <?= __('docs_sdk_event_desc') ?></div>
        </div>
      </div>

      <h6 class="documentation-example-title" style="margin-top:24px"><?= __('docs_sdk_methods_title') ?></h6>

      <div class="terminal-mockup" style="margin-top: 12px;">
        <div class="terminal-header">
          <span class="terminal-title">gmi_login()</span>
        </div>
        <div class="terminal-body code-block-wrapper">
          <div class="code-block jsHigh">gmi_login(); // <?= __('docs_sdk_method_login') ?></div>
        </div>
      </div>

      <div class="terminal-mockup" style="margin-top: 12px;">
        <div class="terminal-header">
          <span class="terminal-title">gmi_logout()</span>
        </div>
        <div class="terminal-body code-block-wrapper">
          <div class="code-block jsHigh">gmi_logout(); // <?= __('docs_sdk_method_logout') ?></div>
        </div>
      </div>

      <div class="terminal-mockup" style="margin-top: 12px;">
        <div class="terminal-header">
          <span class="terminal-title">gmi_scores_send()</span>
        </div>
        <div class="terminal-body code-block-wrapper">
          <div class="code-block jsHigh">// <?= __('docs_sdk_scores_guest') ?><br/>gmi_scores_send({<br/>&nbsp;&nbsp;leaderboard_id: 30,<br/>&nbsp;&nbsp;player: "Harry",<br/>&nbsp;&nbsp;score: 5000,<br/>&nbsp;&nbsp;on_success: function(data) {<br/>&nbsp;&nbsp;&nbsp;&nbsp;// <?= __('docs_sdk_scores_send_desc') ?><br/>&nbsp;&nbsp;&nbsp;&nbsp;show_debug_message("Posizione: " + string(data.position));<br/>&nbsp;&nbsp;},<br/>&nbsp;&nbsp;on_error: function(err) {<br/>&nbsp;&nbsp;&nbsp;&nbsp;show_debug_message("Errore: " + string(err));<br/>&nbsp;&nbsp;}<br/>});<br/><br/>// <?= __('docs_sdk_scores_auth') ?><br/>// <?= __('docs_sdk_scores_auth_desc') ?><br/>gmi_scores_send({<br/>&nbsp;&nbsp;leaderboard_id: 30,<br/>&nbsp;&nbsp;score: 5000<br/>});</div>
        </div>
      </div>

      <div class="terminal-mockup" style="margin-top: 12px;">
        <div class="terminal-header">
          <span class="terminal-title">gmi_scores_get_list()</span>
        </div>
        <div class="terminal-body code-block-wrapper">
          <div class="code-block jsHigh">gmi_scores_get_list({<br/>&nbsp;&nbsp;leaderboard_id: 30,<br/>&nbsp;&nbsp;on_success: function(data) {<br/>&nbsp;&nbsp;&nbsp;&nbsp;// <?= __('docs_sdk_scores_list_desc') ?><br/>&nbsp;&nbsp;&nbsp;&nbsp;var scores = data.scores;<br/>&nbsp;&nbsp;&nbsp;&nbsp;var playerScore = data.playerScore;<br/>&nbsp;&nbsp;&nbsp;&nbsp;for (var i = 0; i < array_length(scores); i++) {<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;show_debug_message(scores[i].username + ": " + string(scores[i].score));<br/>&nbsp;&nbsp;&nbsp;&nbsp;}<br/>&nbsp;&nbsp;},<br/>&nbsp;&nbsp;on_error: function(err) {<br/>&nbsp;&nbsp;&nbsp;&nbsp;show_debug_message("Errore: " + string(err));<br/>&nbsp;&nbsp;}<br/>});</div>
        </div>
      </div>

      <p class="documentation-text" style="margin-top: 20px;"><?= __('docs_sdk_token_persist_desc') ?></p>

      <hr style="margin: 32px 0 24px; border: none; border-top: 1px solid var(--text-color-secondary); opacity: 0.3;">

      <h6 class="documentation-example-title" style="margin-top:0"><?= __('docs_sdk_globals_title') ?></h6>

      <div class="terminal-mockup" style="margin-top: 12px;">
        <div class="terminal-header">
          <span class="terminal-title">global.GMI_PLAYER_LOGGED</span>
        </div>
        <div class="terminal-body code-block-wrapper">
          <div class="code-block jsHigh">// <?= __('docs_sdk_global_logged_desc') ?><br/>if (global.GMI_PLAYER_LOGGED) {<br/>&nbsp;&nbsp;show_debug_message("Loggato come " + global.GMI_PLAYER_USERNAME);<br/>}</div>
        </div>
      </div>

      <div class="terminal-mockup" style="margin-top: 12px;">
        <div class="terminal-header">
          <span class="terminal-title">global.GMI_PLAYER_USERNAME</span>
        </div>
        <div class="terminal-body code-block-wrapper">
          <div class="code-block jsHigh">// <?= __('docs_sdk_global_username_desc') ?><br/>var _name = global.GMI_PLAYER_USERNAME;</div>
        </div>
      </div>

      <div class="terminal-mockup" style="margin-top: 12px;">
        <div class="terminal-header">
          <span class="terminal-title">global.GMI_PLAYER_ID</span>
        </div>
        <div class="terminal-body code-block-wrapper">
          <div class="code-block jsHigh">// <?= __('docs_sdk_global_userid_desc') ?><br/>var _id = global.GMI_PLAYER_ID;</div>
        </div>
      </div>
    </div>
  </div>
  <?php
  $resourcesContent = ob_get_clean();

  // ENDPOINTS TAB CONTENT
  ob_start();
  ?>
  <div class="panel-info" style="margin-bottom: 24px;">
    <p><i class="fas fa-info-circle mr-2"></i><?= __('docs_api_advanced_banner') ?></p>
  </div>

  <p class="documentation-text font-semibold mb-4"><?= __('docs_subtitle') ?></p>

  <!-- POST /add.php Accordion -->
  <div class="accordion-container">
    <button class="accordion-header">
      <div class="accordion-header-left">
        <span class="method-badge method-badge--post">POST</span>
        <span class="font-mono font-semibold">/add.php</span>
      </div>
      <i class="fas fa-chevron-down accordion-icon"></i>
    </button>
    <div class="accordion-content" style="display:none">
      
      <div class="api-endpoint-grid">
        <div class="api-endpoint-left">
          <div class="api-params-title"><?= __('docs_params_title') ?></div>
          
          <div class="api-params-list">
            <!-- game -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">game</span>
                <span class="api-param-type">int</span>
                <span class="api-param-badge api-param-badge--required">required</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_game_id') ?>
              </div>
            </div>
            
            <!-- leaderboard_id -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">leaderboard_id</span>
                <span class="api-param-type">int</span>
                <span class="api-param-badge api-param-badge--required">required</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_lb_id') ?>
              </div>
            </div>
            
            <!-- score -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">score</span>
                <span class="api-param-type">float</span>
                <span class="api-param-badge api-param-badge--required">required</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_score') ?>
              </div>
            </div>
            
            <!-- player -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">player</span>
                <span class="api-param-type">string</span>
                <span class="api-param-badge api-param-badge--required">required</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_player') ?>
              </div>
            </div>
            
            <!-- hash -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">hash</span>
                <span class="api-param-type">string</span>
                <span class="api-param-badge api-param-badge--required">required</span>
              </div>
              <div class="api-param-desc">
                <code class="inline-code">sha1("game=" + game_id + "&amp;leaderboard_id=" + leaderboard_id + "&amp;score=" + score + "&amp;player=" + player + secret)</code>
              </div>
            </div>
            
            <!-- tags -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">tags</span>
                <span class="api-param-type">string</span>
                <span class="api-param-badge api-param-badge--optional">optional</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_tags') ?>
              </div>
            </div>

            <!-- insertMode -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">insertMode</span>
                <span class="api-param-type">string</span>
                <span class="api-param-badge api-param-badge--optional">optional</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_insert_mode') ?>
              </div>
            </div>
            
            <!-- data -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">data</span>
                <span class="api-param-type">string</span>
                <span class="api-param-badge api-param-badge--optional">optional</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_data') ?>
              </div>
            </div>
            
            <!-- env -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">env</span>
                <span class="api-param-type">string</span>
                <span class="api-param-badge api-param-badge--optional">optional</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_env') ?>
              </div>
            </div>

            <!-- token -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">token</span>
                <span class="api-param-type">string</span>
                <span class="api-param-badge api-param-badge--optional">optional</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_token_desc') ?>
              </div>
            </div>
          </div>

          <div class="panel-info">
            <p><i class="fas fa-info-circle mr-2"></i><?= __('docs_note_add') ?></p>
          </div>
        </div>

        <div class="api-endpoint-right">
          <h6 class="documentation-example-title"><?= __('docs_response') ?></h6>

          <div style="font-size:0.82em; color:var(--text-color-secondary); line-height:1.5; margin: -8px 0 16px; padding: 0 4px;">
            // <?= __('docs_comment_score_action') ?><br/>
            // <?= __('docs_comment_position') ?>
          </div>

          <h6 class="documentation-example-title"><?= __('docs_example_gms') ?></h6>
          <div class="terminal-mockup">
            <div class="terminal-header">
              <span class="terminal-title">GameMaker Integration</span>
            </div>
            <div class="terminal-body code-block-wrapper">
              <button class="copy-code-btn" onclick="copyBlockContent(this)" data-tippy-content="Copia codice">
                <i class="far fa-copy"></i>
              </button>
              <div class="code-block jsHigh">var points = 100; // <?= __('docs_code_points') ?><br/>var player = "Harry"; // <?= __('docs_code_player') ?><br/>var data = "game=ID&amp;leaderboard_id=ID_LEADERBOARD&amp;score=" + string(points) + "&amp;player=" + base64_encode(player);<br/>var secret = "SECRET_DEL_GIOCO"; // <?= __('docs_code_secret') ?><br/>var hash = "&amp;hash=" + sha1_string_utf8(data + secret);<br/>http_post_string("<?= $baseApiPath ?>/add.php", data + hash);</div>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </div>

  <!-- GET /list.php Accordion -->
  <div class="accordion-container">
    <button class="accordion-header">
      <div class="accordion-header-left">
        <span class="method-badge method-badge--get">GET</span>
        <span class="font-mono font-semibold">/list.php</span>
      </div>
      <i class="fas fa-chevron-down accordion-icon"></i>
    </button>
    <div class="accordion-content" style="display:none">
      
      <div class="api-endpoint-grid">
        <div class="api-endpoint-left">
          <div class="api-params-title"><?= __('docs_params_query') ?></div>
          
          <div class="api-params-list">
            <!-- game -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">game</span>
                <span class="api-param-type">int</span>
                <span class="api-param-badge api-param-badge--required">required</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_list_game_id') ?>
              </div>
            </div>
            
            <!-- leaderboard_id -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">leaderboard_id</span>
                <span class="api-param-type">int</span>
                <span class="api-param-badge api-param-badge--required">required</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_list_lb_id') ?>
              </div>
            </div>

            <!-- hash -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">hash</span>
                <span class="api-param-type">string</span>
                <span class="api-param-badge api-param-badge--optional">optional</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_list_hash') ?>
              </div>
            </div>
            
            <!-- tags -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">tags</span>
                <span class="api-param-type">string</span>
                <span class="api-param-badge api-param-badge--optional">optional</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_list_tags') ?>
              </div>
            </div>
            
            <!-- page -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">page</span>
                <span class="api-param-type">int</span>
                <span class="api-param-badge api-param-badge--optional">optional</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_list_page') ?>
              </div>
            </div>
            
            <!-- limit -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">limit</span>
                <span class="api-param-type">int</span>
                <span class="api-param-badge api-param-badge--optional">optional</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_list_limit') ?>
              </div>
            </div>
            
            <!-- order -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">order</span>
                <span class="api-param-type">string</span>
                <span class="api-param-badge api-param-badge--optional">optional</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_list_order') ?>
              </div>
            </div>
            
            <!-- player -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">player</span>
                <span class="api-param-type">int | string</span>
                <span class="api-param-badge api-param-badge--optional">optional</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_list_player') ?>
              </div>
            </div>
            
            <!-- startTime -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">startTime</span>
                <span class="api-param-type">string</span>
                <span class="api-param-badge api-param-badge--optional">optional</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_list_start') ?>
              </div>
            </div>
            
            <!-- endTime -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">endTime</span>
                <span class="api-param-type">string</span>
                <span class="api-param-badge api-param-badge--optional">optional</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_list_end') ?>
              </div>
            </div>
            
            <!-- includePlayer -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">includePlayer</span>
                <span class="api-param-type">string</span>
                <span class="api-param-badge api-param-badge--optional">optional</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_list_include_player') ?>
              </div>
            </div>
            
            <!-- env -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">env</span>
                <span class="api-param-type">string</span>
                <span class="api-param-badge api-param-badge--optional">optional</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_list_env') ?>
              </div>
            </div>

            <!-- token -->
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">token</span>
                <span class="api-param-type">string</span>
                <span class="api-param-badge api-param-badge--optional">optional</span>
              </div>
              <div class="api-param-desc">
                <?= __('docs_param_token_list_desc') ?>
              </div>
            </div>
          </div>
        </div>
        
        <div class="api-endpoint-right">
          <h6 class="documentation-example-title"><?= __('docs_response_example') ?></h6>
          <div class="terminal-mockup">
            <div class="terminal-header">
              <span class="terminal-title">Response payload</span>
            </div>
            <div class="terminal-body code-block-wrapper">
              <button class="copy-code-btn" onclick="copyBlockContent(this)" data-tippy-content="Copia codice">
                <i class="far fa-copy"></i>
              </button>
              <div class="code-block jsHigh">{<br/>&nbsp;&nbsp;"status": 200,<br/>&nbsp;&nbsp;"scores": [<br/>&nbsp;&nbsp;&nbsp;&nbsp;{ "player_id": 130, "username": "Freank", "score": 2000, "created_at": "2020-05-03 08:58:12" },<br/>&nbsp;&nbsp;&nbsp;&nbsp;{ "player_id": 54, "username": "Jak", "score": 1200, "created_at": "2020-05-04 22:20:20" }<br/>&nbsp;&nbsp;],<br/>&nbsp;&nbsp;"playerScore": {<br/>&nbsp;&nbsp;&nbsp;&nbsp;"player_id": 75,<br/>&nbsp;&nbsp;&nbsp;&nbsp;"username": "Rolando",<br/>&nbsp;&nbsp;&nbsp;&nbsp;"score": 1000,<br/>&nbsp;&nbsp;&nbsp;&nbsp;"position": 1<br/>&nbsp;&nbsp;}<br/>}</div>
            </div>
          </div>

          <div style="font-size:0.82em; color:var(--text-color-secondary); line-height:1.5; margin: -8px 0 16px; padding: 0 4px;">
            // <?= __('docs_note1') ?><br/>
            // <?= __('docs_note2') ?>
          </div>

          <h6 class="documentation-example-title"><?= __('docs_example_gms2') ?></h6>
          <div class="terminal-mockup">
            <div class="terminal-header">
              <span class="terminal-title">Create Event</span>
            </div>
            <div class="terminal-body code-block-wrapper">
              <button class="copy-code-btn" onclick="copyBlockContent(this)" data-tippy-content="Copia codice">
                <i class="far fa-copy"></i>
              </button>
              <div class="code-block jsHigh">// <?= __('docs_code_create_comment') ?><br/>// <?= __('docs_code_request_comment') ?><br/>scores = noone;<br/>getScores = http_get("<?= $baseApiPath ?>/list.php?game=ID");</div>
            </div>
          </div>

          <div class="terminal-mockup" style="margin-top: 16px;">
            <div class="terminal-header">
              <span class="terminal-title">Async HTTP Event</span>
            </div>
            <div class="terminal-body code-block-wrapper">
              <button class="copy-code-btn" onclick="copyBlockContent(this)" data-tippy-content="Copia codice">
                <i class="far fa-copy"></i>
              </button>
              <div class="code-block jsHigh">// <?= __('docs_code_async_comment') ?><br/>if (async_load[? "id"] == getScores &amp;&amp; async_load[? "status"] == 0) {<br/>&nbsp;&nbsp;var result = json_decode(async_load[? "result"]);<br/>&nbsp;&nbsp;scores = result[? "scores"];<br/>}</div>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </div>

  <!-- Autenticazione Player -->
  <div style="margin-top:40px">
    <h6 class="documentation-example-title" style="font-size:1.1em; margin-bottom:8px"><i class="fas fa-key" style="margin-right:8px"></i><?= __('docs_auth_flow_title') ?></h6>
    <p class="documentation-text mb-4"><?= __('docs_auth_intro') ?></p>
  </div>

  <!-- POST /player-auth/login-start.php -->
  <div class="accordion-container">
    <button class="accordion-header">
      <div class="accordion-header-left">
        <span class="method-badge method-badge--post">POST</span>
        <span class="font-mono font-semibold">/player-auth/login-start.php</span>
      </div>
      <i class="fas fa-chevron-down accordion-icon"></i>
    </button>
    <div class="accordion-content" style="display:none">
      <div class="api-endpoint-grid">
        <div class="api-endpoint-left">
          <p class="documentation-text"><strong><?= __('docs_auth_step1_title') ?></strong></p>
          <p class="documentation-text"><?= __('docs_auth_step1_desc') ?></p>
          <div class="panel-info" style="margin-top:16px">
            <p><i class="fas fa-info-circle mr-2"></i><?= __('docs_auth_no_body') ?></p>
          </div>
        </div>
        <div class="api-endpoint-right">
          <h6 class="documentation-example-title"><?= __('docs_response_example') ?></h6>
          <div class="terminal-mockup">
            <div class="terminal-header">
              <span class="terminal-title">Response</span>
            </div>
            <div class="terminal-body code-block-wrapper">
              <button class="copy-code-btn" onclick="copyBlockContent(this)" data-tippy-content="Copia codice">
                <i class="far fa-copy"></i>
              </button>
              <div class="code-block jsHigh">{<br/>&nbsp;&nbsp;"status": 200,<br/>&nbsp;&nbsp;"session_token": "a1b2c3d4... (64 caratteri hex)"<br/>}</div>
            </div>
          </div>
          <h6 class="documentation-example-title" style="margin-top:16px">cURL</h6>
          <div class="terminal-mockup">
            <div class="terminal-header">
              <span class="terminal-title">Shell</span>
            </div>
            <div class="terminal-body code-block-wrapper">
              <button class="copy-code-btn" onclick="copyBlockContent(this)" data-tippy-content="Copia codice">
                <i class="far fa-copy"></i>
              </button>
              <div class="code-block jsHigh">curl -X POST "<?= $config["host"] ?>/player-auth/login-start.php"</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- GET /player-auth/discord/login.php -->
  <div class="accordion-container">
    <button class="accordion-header">
      <div class="accordion-header-left">
        <span class="method-badge method-badge--get">GET</span>
        <span class="font-mono font-semibold">/player-auth/discord/login.php?session={token}</span>
      </div>
      <i class="fas fa-chevron-down accordion-icon"></i>
    </button>
    <div class="accordion-content" style="display:none">
      <div class="api-endpoint-grid">
        <div class="api-endpoint-left">
          <p class="documentation-text"><strong><?= __('docs_auth_step2_title') ?></strong></p>
          <p class="documentation-text"><?= __('docs_auth_step2_desc') ?></p>
          <div class="api-params-title" style="margin-top:16px"><?= __('docs_params_query') ?></div>
          <div class="api-params-list">
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">session</span>
                <span class="api-param-type">string</span>
                <span class="api-param-badge api-param-badge--required">required</span>
              </div>
              <div class="api-param-desc"><?= __('docs_auth_session_from_prev') ?></div>
            </div>
          </div>
          <div class="panel-info" style="margin-top:16px">
            <p><i class="fas fa-info-circle mr-2"></i><?= __('docs_auth_redirect_info') ?></p>
          </div>
        </div>
        <div class="api-endpoint-right">
          <h6 class="documentation-example-title">Redirect URL</h6>
          <div class="terminal-mockup">
            <div class="terminal-header">
              <span class="terminal-title">Browser redirect</span>
            </div>
            <div class="terminal-body code-block-wrapper">
              <button class="copy-code-btn" onclick="copyBlockContent(this)" data-tippy-content="Copia codice">
                <i class="far fa-copy"></i>
              </button>
              <div class="code-block jsHigh"><?= $config["host"] ?>/player-auth/discord/login.php?session=a1b2c3d4...<br/><br/>// <?= __('docs_auth_browser_redirect') ?><br/>// <?= __('docs_auth_after_login') ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- GET /player-auth/check-session.php -->
  <div class="accordion-container">
    <button class="accordion-header">
      <div class="accordion-header-left">
        <span class="method-badge method-badge--get">GET</span>
        <span class="font-mono font-semibold">/player-auth/check-session.php?session={token}</span>
      </div>
      <i class="fas fa-chevron-down accordion-icon"></i>
    </button>
    <div class="accordion-content" style="display:none">
      <div class="api-endpoint-grid">
        <div class="api-endpoint-left">
          <p class="documentation-text"><strong><?= __('docs_auth_step3_title') ?></strong></p>
          <p class="documentation-text"><?= __('docs_auth_step3_desc') ?></p>
          <div class="api-params-title" style="margin-top:16px"><?= __('docs_params_query') ?></div>
          <div class="api-params-list">
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">session</span>
                <span class="api-param-type">string</span>
                <span class="api-param-badge api-param-badge--required">required</span>
              </div>
              <div class="api-param-desc"><?= __('docs_auth_session_from_1') ?></div>
            </div>
          </div>
          <div class="panel-info" style="margin-top:16px">
            <p><i class="fas fa-info-circle mr-2"></i><?= __('docs_auth_session_expire') ?></p>
          </div>
        </div>
        <div class="api-endpoint-right">
          <h6 class="documentation-example-title"><?= __('docs_auth_waiting_login') ?></h6>
          <div class="terminal-mockup">
            <div class="terminal-header">
              <span class="terminal-title">Response (not yet logged)</span>
            </div>
            <div class="terminal-body code-block-wrapper">
              <button class="copy-code-btn" onclick="copyBlockContent(this)" data-tippy-content="Copia codice">
                <i class="far fa-copy"></i>
              </button>
              <div class="code-block jsHigh">{<br/>&nbsp;&nbsp;"status": 200,<br/>&nbsp;&nbsp;"logged": false<br/>}</div>
            </div>
          </div>

          <h6 class="documentation-example-title" style="margin-top:16px"><?= __('docs_auth_login_done') ?></h6>
          <div class="terminal-mockup">
            <div class="terminal-header">
              <span class="terminal-title">Response (logged in)</span>
            </div>
            <div class="terminal-body code-block-wrapper">
              <button class="copy-code-btn" onclick="copyBlockContent(this)" data-tippy-content="Copia codice">
                <i class="far fa-copy"></i>
              </button>
              <div class="code-block jsHigh">{<br/>&nbsp;&nbsp;"status": 200,<br/>&nbsp;&nbsp;"logged": true,<br/>&nbsp;&nbsp;"token": "eyJ0eXAiOiJKV1Qi...",<br/>&nbsp;&nbsp;"username": "PlayerName",<br/>&nbsp;&nbsp;"user_id": 42<br/>}</div>
            </div>
          </div>

          <h6 class="documentation-example-title" style="margin-top:16px">cURL</h6>
          <div class="terminal-mockup">
            <div class="terminal-header">
              <span class="terminal-title">Shell</span>
            </div>
            <div class="terminal-body code-block-wrapper">
              <button class="copy-code-btn" onclick="copyBlockContent(this)" data-tippy-content="Copia codice">
                <i class="far fa-copy"></i>
              </button>
              <div class="code-block jsHigh">curl "<?= $config["host"] ?>/player-auth/check-session.php?session=a1b2c3d4..."</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- GET /player-auth/check-token.php -->
  <div class="accordion-container">
    <button class="accordion-header">
      <div class="accordion-header-left">
        <span class="method-badge method-badge--get">GET</span>
        <span class="font-mono font-semibold">/player-auth/check-token.php?token={token}&amp;game={game}</span>
      </div>
      <i class="fas fa-chevron-down accordion-icon"></i>
    </button>
    <div class="accordion-content" style="display:none">
      <div class="api-endpoint-grid">
        <div class="api-endpoint-left">
          <p class="documentation-text"><strong><?= __('docs_auth_step4_title') ?></strong></p>
          <p class="documentation-text"><?= __('docs_auth_step4_desc') ?></p>
          <div class="api-params-title" style="margin-top:16px"><?= __('docs_params_query') ?></div>
          <div class="api-params-list">
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">token</span>
                <span class="api-param-type">string</span>
                <span class="api-param-badge api-param-badge--required">required</span>
              </div>
              <div class="api-param-desc"><?= __('docs_auth_saved_token') ?></div>
            </div>
            <div class="api-param-row">
              <div class="api-param-header">
                <span class="api-param-name">game</span>
                <span class="api-param-type">int</span>
                <span class="api-param-badge api-param-badge--required">required</span>
              </div>
              <div class="api-param-desc"><?= __('docs_auth_game_id_desc') ?></div>
            </div>
          </div>
          <div class="panel-info" style="margin-top:16px">
            <p><i class="fas fa-info-circle mr-2"></i><?= __('docs_auth_token_persist_desc') ?></p>
          </div>
        </div>
        <div class="api-endpoint-right">
          <h6 class="documentation-example-title"><?= __('docs_auth_token_valid') ?></h6>
          <div class="terminal-mockup">
            <div class="terminal-header">
              <span class="terminal-title">Response (valid)</span>
            </div>
            <div class="terminal-body code-block-wrapper">
              <button class="copy-code-btn" onclick="copyBlockContent(this)" data-tippy-content="Copia codice">
                <i class="far fa-copy"></i>
              </button>
              <div class="code-block jsHigh">{<br/>&nbsp;&nbsp;"status": 200,<br/>&nbsp;&nbsp;"valid": true,<br/>&nbsp;&nbsp;"approved": true,<br/>&nbsp;&nbsp;"token": "eyJ0eXAiOiJKV1Qi... <?= __('docs_auth_token_updated') ?>",<br/>&nbsp;&nbsp;"username": "PlayerName",<br/>&nbsp;&nbsp;"user_id": 42<br/>}</div>
            </div>
          </div>

          <h6 class="documentation-example-title" style="margin-top:16px"><?= __('docs_auth_token_valid_ban') ?></h6>
          <div class="terminal-mockup">
            <div class="terminal-header">
              <span class="terminal-title">Response (with game param)</span>
            </div>
            <div class="terminal-body code-block-wrapper">
              <button class="copy-code-btn" onclick="copyBlockContent(this)" data-tippy-content="Copia codice">
                <i class="far fa-copy"></i>
              </button>
              <div class="code-block jsHigh">{<br/>&nbsp;&nbsp;"status": 200,<br/>&nbsp;&nbsp;"valid": true,<br/>&nbsp;&nbsp;"approved": true,<br/>&nbsp;&nbsp;"is_banned": false,<br/>&nbsp;&nbsp;"token": "...",<br/>&nbsp;&nbsp;"username": "PlayerName",<br/>&nbsp;&nbsp;"user_id": 42<br/>}</div>
            </div>
          </div>

          <h6 class="documentation-example-title" style="margin-top:16px"><?= __('docs_auth_token_invalid') ?></h6>
          <div class="terminal-mockup">
            <div class="terminal-header">
              <span class="terminal-title">Response (invalid)</span>
            </div>
            <div class="terminal-body code-block-wrapper">
              <button class="copy-code-btn" onclick="copyBlockContent(this)" data-tippy-content="Copia codice">
                <i class="far fa-copy"></i>
              </button>
              <div class="code-block jsHigh">{<br/>&nbsp;&nbsp;"status": 200,<br/>&nbsp;&nbsp;"valid": false<br/>}</div>
            </div>
          </div>

          <h6 class="documentation-example-title" style="margin-top:16px">cURL</h6>
          <div class="terminal-mockup">
            <div class="terminal-header">
              <span class="terminal-title">Shell</span>
            </div>
            <div class="terminal-body code-block-wrapper">
              <button class="copy-code-btn" onclick="copyBlockContent(this)" data-tippy-content="Copia codice">
                <i class="far fa-copy"></i>
              </button>
              <div class="code-block jsHigh">curl "<?= $config["host"] ?>/player-auth/check-token.php?token=eyJ0eXAiOiJKV1Qi..."&amp;game=1<br/><br/>// <?= __('docs_code_secret') ?>:<br/>curl "<?= $config["host"] ?>/player-auth/check-token.php?token=...&amp;game=1"</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="panel-info" style="margin-top:16px">
    <p><i class="fas fa-info-circle mr-2"></i><?= __('docs_auth_note_player') ?></p>
  </div>

  <?php
  $endpointsContent = ob_get_clean();

  // RENDER TABS
  echo ui_tabs([
    ["id" => "resources", "label" => "SDK", "icon" => "fas fa-download", "content" => '
      <div class="documentation-section">
        ' . $resourcesContent . '
      </div>
    '],
    ["id" => "security", "label" => __('docs_security_title'), "icon" => "fas fa-lock", "content" => '
      <div class="documentation-section">
        ' . $securityContent . '
      </div>
    '],
    ["id" => "endpoints", "label" => __('docs_subtitle'), "icon" => "fas fa-code", "content" => '
      <div class="documentation-section">
        ' . $endpointsContent . '
      </div>
    '],
    ["id" => "errors", "label" => __('docs_errors_title'), "icon" => "fas fa-exclamation-triangle", "content" => '
      <div class="documentation-section">
        ' . $errorsContent . '
      </div>
    '],
  ]);
  ?>
</div>

<script src="https://www.w3schools.com/lib/w3codecolor.js"></script>
<script>
  w3CodeColor();

  // Accordion Toggle
  var accordions = document.getElementsByClassName("accordion-header");
  for (var i = 0; i < accordions.length; i++) {
    accordions[i].addEventListener("click", function() {
      this.classList.toggle("active");
      var content = this.nextElementSibling;
      var icon = this.querySelector('.accordion-icon');

      if (content.style.display !== "none") {
        content.style.display = "none";
        icon.classList.remove("fa-chevron-up");
        icon.classList.add("fa-chevron-down");
      } else {
        content.style.display = "block";
        icon.classList.remove("fa-chevron-down");
        icon.classList.add("fa-chevron-up");
      }
    });
  }

  // Copy URL Helper
  function copyTextToClipboard(elementId, btn) {
    var text = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(text).then(function() {
      var icon = btn.querySelector('i');
      icon.className = 'fas fa-check';
      btn.style.color = '#4ade80';
      if (btn._tippy) {
        btn._tippy.setContent('Copiato!');
        btn._tippy.show();
      }
      setTimeout(function() {
        icon.className = 'far fa-copy';
        btn.style.color = '';
        if (btn._tippy) btn._tippy.setContent('Copia URL');
      }, 2000);
    }).catch(function(err) {
      console.error('Could not copy text: ', err);
    });
  }

  // Copy Code Block Helper
  function copyBlockContent(btn) {
    var codeBlock = btn.nextElementSibling;
    var text = codeBlock.innerText || codeBlock.textContent;
    navigator.clipboard.writeText(text).then(function() {
      btn.classList.add('copied');
      var icon = btn.querySelector('i');
      icon.className = 'fas fa-check';
      var origHTML = btn.innerHTML;
      btn.innerHTML = '<i class="fas fa-check"></i> Copiato!';
      if (btn._tippy) {
        btn._tippy.setContent('Copiato!');
        btn._tippy.show();
      }
      setTimeout(function() {
        btn.classList.remove('copied');
        icon.className = 'far fa-copy';
        btn.innerHTML = '<i class="far fa-copy"></i>';
        if (btn._tippy) btn._tippy.setContent('Copia codice');
      }, 2000);
    }).catch(function(err) {
      console.error('Could not copy code block: ', err);
    });
  }
</script>
