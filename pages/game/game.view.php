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

.game-stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.game-stat-card {
  background: var(--bg-color-card, #fff);
  border: 1px solid var(--border-color, #e5e7eb);
  border-radius: 12px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  transition: box-shadow 0.2s, border-color 0.2s;
}

.game-stat-card:hover {
  border-color: var(--glass-border-hover, rgba(99,102,241,0.3));
  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.game-stat-card__icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2em;
  flex-shrink: 0;
}

.game-stat-card__icon--primary { background: rgba(99,102,241,0.1); color: #6366f1; }
.game-stat-card__icon--success { background: rgba(16,185,129,0.1); color: #10b981; }
.game-stat-card__icon--info { background: rgba(59,130,246,0.1); color: #3b82f6; }
.game-stat-card__icon--purple { background: rgba(168,85,247,0.1); color: #a855f7; }

.game-stat-card__value {
  font-size: 1.5em;
  font-weight: 800;
  color: var(--text-color-headings, #333);
  line-height: 1.2;
  font-variant-numeric: tabular-nums;
}

.game-stat-card__label {
  font-size: 0.82em;
  color: var(--text-color-secondary, #6b7280);
}

.chart-container {
  position: relative;
  width: 100%;
  max-height: 300px;
}

.chart-container canvas {
  max-height: 300px;
}

.chart-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-top: 20px;
}

.chart-grid > .ui-card {
  display: flex;
  flex-direction: column;
  height: 360px;
}

.chart-grid > .ui-card > .ui-card__body {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.chart-grid > .ui-card .chart-container {
  flex: 1;
  min-height: 200px;
  max-height: none;
}

.chart-grid > .ui-card .chart-container canvas {
  max-height: none;
}

.chart-grid .ui-card + .ui-card {
  margin-top: 0;
}

@media (max-width: 768px) {
  .chart-grid {
    grid-template-columns: 1fr;
  }
  .game-stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  }
}
</style>

<?php
// Configuration tab content
$configContent = '
  <div class="internal-actions internal-actions--right" style="margin-bottom:20px">
    ' . ui_button(__('game_tab_leaderboards'), 'primary', 'md', ['icon' => 'fas fa-trophy', 'href' => 'leaderboards.php?game_id=' . $gameId]) . '
    ' . ui_button(__('game_tab_bans'), 'primary', 'md', ['icon' => 'fas fa-user-times', 'href' => 'game-bans.php?id=' . $gameId]) . '
    ' . ui_button(__('game_tab_delete'), 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => "openModal('modal-delete-game', onDeleteGameModalOpen, { gameId: $gameId, gameName: '" . escapeChars($game['name']) . "' })"]]) . '
  </div>

  <div style="display:flex;gap:20px;flex-wrap:wrap">
    <div style="flex:1;min-width:300px">
      <div class="internal-card">
        <div class="internal-card__title"><i class="fas fa-cog"></i> ' . __('game_details_title') . '</div>
        <label class="form-label">' . __('game_details_id') . '</label>
        <div class="input-group">
          <input id="input-gameid" class="ui-input" value="' . $gameId . '" disabled style="background:var(--bg-color-sidebar,#f0f0f0)!important">
        </div>

        <label class="form-label" style="margin-top:16px">' . __('game_details_secret') . '</label>
        <div style="color:var(--text-muted,#666);font-size:0.875em;margin-bottom:12px">' . __('game_details_secret_help') . '</div>
        <div class="input-group">
          <input id="input-secret" type="password" class="ui-input" value="' . $game["client_secret"] . '" disabled>
          <i class="input-regenerate-secret-btn fas fa-sync" onclick="openModal(\'modal-regenerate-secret\')" data-tippy-content="' . __('game_details_secret_regenerate_tooltip') . '"></i>
          <i class="input-secret-eye-btn fas fa-eye" onclick="toggleSecretVisibility(this)" data-tippy-content="' . __('game_details_secret_toggle_tooltip') . '"></i>
        </div>
      </div>
    </div>

    <div style="flex:1;min-width:300px">
      <div class="internal-card">
        <div class="internal-card__title"><i class="fas fa-edit"></i> ' . __('game_rename_title') . '</div>
        <form method="POST" action="/game-rename.php?id=' . $gameId . '">
          <div class="input-group">
            <input id="input-game-name" name="name" type="text" class="ui-input" value="' . htmlspecialchars($game["name"]) . '" required>
          </div>
          ' . ui_button(__('game_rename_button'), 'primary', 'md', ['icon' => 'fa fa-edit', 'type' => 'submit', 'class' => 'mt-2']) . '
        </form>
      </div>
    </div>
  </div>

  <h3 class="section-header" style="margin-top:2rem"><i class="fab fa-steam-symbol" style="margin-right:16px"></i>' . __('game_integration_title') . '</h3>

  <div class="internal-card">
    <div class="code-block-header">' . __('game_code_send_header') . '</div>
    <div class="code-block jsHigh">
  var points = 100; // ' . __('game_code_player_points') . '<br/>
  var player = "Harry"; // ' . __('game_code_player_name') . '<br/>
  var data = "game=' . $gameId . '&leaderboard_id=ID_CLASSIFICA&score=" + string(points) + "&player=" + base64_encode(player);<br/>
  var secret = "SECRET_DEL_GIOCO"; // ' . __('game_code_secret_comment') . '<br/>
  var hash = "&hash=" + sha1_string_utf8(data + secret);<br/>
  http_post_string("' . $baseApiPath . '/add.php", data + hash);
    </div>
  </div>

  <div class="internal-card">
    <div class="code-block-header">' . __('game_code_list_header') . '</div>
    <div class="code-block jsHigh">
  // ' . __('game_code_list_create_comment') . '<br/>
  // ' . __('game_code_list_request_comment') . '<br/>
  scores = noone;<br/>
  getScores = http_get("' . $baseApiPath . '/list.php?game=' . $gameId . '&leaderboard_id=ID_CLASSIFICA");
    </div>
  </div>

  <div class="internal-card">
    <div class="code-block-header">' . __('game_code_list_async_header') . '</div>
    <div class="code-block jsHigh">
  // ' . __('game_code_list_async_comment') . '<br/>
  if (async_load[? "id"] == getScores && async_load[? "status"] == 0) {<br/>
  &nbsp;&nbsp;var result = json_decode(async_load[? "result"]);<br/>
  &nbsp;&nbsp;scores = result[? "scores"];<br/>
  }
    </div>
  </div>

  <div class="internal-card">
    <div class="code-block-header">' . __('game_code_draw_comment') . '</div>
    <div class="code-block jsHigh">
  draw_text(20, 20, "' . __('game_code_draw_header') . '");<br/><br/>
  if (scores != noone) {<br/>
  &nbsp;&nbsp;for (var i=0; i&lt;ds_list_size(scores); i++) {<br/>
  &nbsp;&nbsp;&nbsp;&nbsp;var player = scores[| i];<br/>
  &nbsp;&nbsp;&nbsp;&nbsp;draw_text(20, 50+i*20, player[? "username"] + " - " + string(player[? "score"]));<br/>
  &nbsp;&nbsp;}<br/>
  }
    </div>
  </div>

  <h3 class="section-header" style="margin-top:2rem"><i class="fas fa-book" style="margin-right:16px"></i>' . __('game_api_docs_title') . '</h3>
  <p style="margin-bottom:16px">' . __('game_api_docs_desc') . '</p>
  ' . ui_button(__('game_api_docs_button'), 'primary', 'md', ['icon' => 'fa fa-arrow-circle-right', 'href' => 'documentation.php']) . '
';

$activeTab = $_GET["tab"] ?? "config";

$analyticsContent = '';
if ($activeTab === 'analytics') {
  ob_start();
  require "pages/game/game-tab-render.php";
  $analyticsContent = ob_get_clean();
}

echo ui_tabs([
  ["id" => "config", "label" => __('game_tab_config'), "icon" => "fas fa-cog", "content" => $configContent],
  ["id" => "analytics", "label" => __('game_tab_analytics'), "icon" => "fas fa-chart-pie", "content" => $activeTab === 'analytics' ? $analyticsContent : ui_skeleton('chart', 2), "url" => $activeTab !== 'analytics' ? "/game.php?id=$gameId&tab=analytics&ajax=1" : null],
], ["active" => $activeTab]);
?>

<?= ui_modal('modal-regenerate-secret', [
  'title' => __('game_modal_secret_title'),
  'content' => '<p>' . __('game_modal_secret_body') . '</p>
    <div style="background:#fff8e1;border-left:4px solid #ffc107;padding:16px;border-radius:8px;margin-top:16px">
      <p><i class="fas fa-exclamation-triangle" style="margin-right:8px"></i><strong>' . __('game_modal_secret_warning_label') . '</strong> ' . __('game_modal_secret_warning') . '</p>
      <p>' . __('game_modal_secret_irreversible') . '</p>
    </div>',
  'footer' =>
    ui_button(__('game_modal_secret_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-regenerate-secret')"]]) .
    ui_button(__('game_modal_secret_confirm'), 'danger', 'md', ['icon' => 'fas fa-sync', 'attrs' => ['onclick' => 'regenerateSecret()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>

<?= ui_modal('modal-delete-game', [
  'title' => __('game_modal_delete_title'),
  'content' => '<p>' . __('game_modal_delete_body') . ' <strong><span id="modal-game-name"></span></strong> ?</p><p>' . __('game_modal_delete_irreversible') . '</p>',
  'footer' =>
    ui_button(__('game_modal_delete_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-delete-game', onDeleteGameModalClose)"]]) .
    ui_button(__('game_modal_delete_confirm'), 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'deleteGame()'], 'class' => 'ui-destructive']),
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
