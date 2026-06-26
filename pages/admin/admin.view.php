<style>
.admin-stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.admin-stat-card {
  background: var(--bg-color-card, #fff);
  border: 1px solid var(--border-color, #e5e7eb);
  border-radius: 12px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  transition: box-shadow 0.2s, border-color 0.2s;
}

.admin-stat-card:hover {
  border-color: var(--glass-border-hover, rgba(99,102,241,0.3));
  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.admin-stat-card__icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2em;
  flex-shrink: 0;
}

.admin-stat-card__icon--primary { background: rgba(99,102,241,0.1); color: #6366f1; }
.admin-stat-card__icon--success { background: rgba(16,185,129,0.1); color: #10b981; }
.admin-stat-card__icon--info { background: rgba(59,130,246,0.1); color: #3b82f6; }
.admin-stat-card__icon--purple { background: rgba(168,85,247,0.1); color: #a855f7; }
.admin-stat-card__icon--warning { background: rgba(245,158,11,0.1); color: #f59e0b; }
.admin-stat-card__icon--pink { background: rgba(236,72,153,0.1); color: #ec4899; }

.admin-stat-card__value {
  font-size: 1.5em;
  font-weight: 800;
  color: var(--text-color-headings, #333);
  line-height: 1.2;
  font-variant-numeric: tabular-nums;
}

.admin-stat-card__label {
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

  .chart-grid .chart-container {
    flex: 1;
    min-height: 200px;
    max-height: none;
  }

  .chart-grid .chart-container canvas {
    max-height: none;
  }

@media (max-width: 768px) {
  .chart-grid {
    grid-template-columns: 1fr;
  }
  .admin-stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  }
}

.search-form {
  display: flex;
  gap: 8px;
  margin-bottom: 16px;
  align-items: center;
  flex-wrap: wrap;
}

.search-form .ui-input { height: 38px; }
.search-form .ui-btn { height: 38px; }

@media (max-width: 1400px) {
  .search-form form { flex-wrap: wrap; }
  .search-form form .ui-input { max-width: 180px; }
  .search-form form .ui-btn { min-width: 80px; }
  .pending-filter-btn { min-width: 80px; }
}

.pending-filter-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  height: 38px;
  padding: 0 14px;
  border-radius: 8px;
  font-size: 0.85em;
  font-weight: 600;
  text-decoration: none;
  transition: background 0.2s, color 0.2s;
  white-space: nowrap;
  box-sizing: border-box;
}

.pending-filter-btn--active {
  background: rgba(245,158,11,0.15);
  color: #f59e0b;
  border: 1px solid rgba(245,158,11,0.3);
}

.pending-filter-btn--inactive {
  background: var(--bg-color-offset, #f3f4f6);
  color: var(--text-color-secondary, #6b7280);
  border: 1px solid var(--border-color, #e5e7eb);
}

.pending-filter-btn--inactive:hover {
  background: var(--bg-color-card, #fff);
  color: var(--text-color, #374151);
}

.migrate-output {
  background: #1e1e2e;
  color: #cdd6f4;
  font-family: 'Consolas', 'Courier New', monospace;
  font-size: 0.85em;
  padding: 16px;
  border-radius: 8px;
  max-height: 400px;
  overflow-y: auto;
  white-space: pre-wrap;
  word-break: break-all;
  margin-top: 16px;
  margin-bottom: 16px;
}

.migrate-output .ok { color: #a6e3a1; }
.migrate-output .error { color: #f38ba8; }
.migrate-output .fail { color: #fab387; }

.admin-score-action {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  color: var(--table-action-icon-color, #555);
  text-decoration: none;
  transition: background 0.15s, color 0.15s, transform 0.15s;
}

.admin-score-action:hover {
  background: var(--table-action-icon-hover-bg, rgba(99,102,241,0.08));
  color: var(--primary-color, #6366f1);
  transform: translateY(-1px);
}

.admin-score-action--danger:hover {
  color: #dc2626;
  background: rgba(220,38,38,0.08);
}
</style>

<?php
// Render the active tab's content
ob_start();
$activeTab = $_GET["tab"] ?? "users";
require "pages/admin/admin-tab-render.php";
$activeContent = ob_get_clean();

// Build tab array with lazy loading for inactive tabs
$tabs = [];
$tabIds = ['users', 'players', 'scores', 'analytics', 'api-errors', 'migrate'];
$tabLabels = [
  'users' => 'Users',
  'players' => 'Players',
  'scores' => 'Scores',
  'analytics' => 'Analytics',
  'api-errors' => 'API Errors',
  'migrate' => 'Migrations',
];
$tabIcons = [
  'users' => 'fas fa-users',
  'players' => 'fas fa-user-friends',
  'scores' => 'fas fa-star',
  'analytics' => 'fas fa-chart-pie',
  'api-errors' => 'fas fa-exclamation-triangle',
  'migrate' => 'fas fa-database',
];

foreach ($tabIds as $id) {
  $tab = [
    'id' => $id,
    'label' => $tabLabels[$id],
    'icon' => $tabIcons[$id],
  ];

  if ($id === $activeTab) {
    $tab['content'] = $activeContent;
  } else {
    $tab['url'] = '/admin.php?tab=' . $id . '&ajax=1';
    $skeletonType = ($id === 'analytics' || $id === 'api-errors') ? 'chart' : 'table-row';
    $tab['content'] = ui_skeleton($skeletonType, $skeletonType === 'chart' ? 2 : 8);
  }

  $tabs[] = $tab;
}

echo ui_tabs($tabs, ["active" => $activeTab]);
?>

<?php
echo ui_modal('modal-admin-user-toggle', [
  'title' => __('admin_confirm_title'),
  'content' => '<p id="modal-admin-user-toggle__body"></p>',
  'footer' =>
    ui_button(__('admin_confirm_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-admin-user-toggle', onAdminUserToggleClose)"]]) .
    ui_button(__('admin_confirm_confirm'), 'primary', 'md', ['icon' => 'fas fa-check', 'attrs' => ['onclick' => 'adminUserToggleConfirm()'], 'class' => 'ui-destructive']),
]);

echo ui_modal('modal-admin-player-ban', [
  'title' => __('admin_confirm_title'),
  'content' => '<p id="modal-admin-player-ban__body"></p>',
  'footer' =>
    ui_button(__('admin_confirm_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-admin-player-ban', onAdminPlayerBanClose)"]]) .
    ui_button(__('admin_confirm_confirm'), 'danger', 'md', ['icon' => 'fas fa-ban', 'attrs' => ['onclick' => 'adminPlayerBanConfirm()'], 'class' => 'ui-destructive']),
]);

echo ui_modal('modal-admin-score-delete', [
  'title' => __('admin_confirm_deletion_title'),
  'content' => '<p id="modal-admin-score-delete__body"></p><p>' . __('scores_modal_delete_irreversible') . '</p>',
  'footer' =>
    ui_button(__('admin_confirm_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-admin-score-delete', onAdminScoreDeleteClose)"]]) .
    ui_button(__('scores_modal_delete_confirm'), 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'adminScoreDeleteConfirm()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]);

echo ui_modal('modal-admin-score-ban', [
  'title' => __('admin_confirm_ban_title'),
  'content' => '<p id="modal-admin-score-ban__body"></p><p>' . __('admin_ban_warning') . '</p><p>' . __('admin_ban_note') . '</p>',
  'footer' =>
    ui_button(__('admin_confirm_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-admin-score-ban', onAdminScoreBanClose)"]]) .
    ui_button(__('scores_modal_ban_confirm'), 'danger', 'md', ['icon' => 'fas fa-ban', 'attrs' => ['onclick' => 'adminScoreBanConfirm()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]);

echo ui_modal('modal-sync-indexes', [
  'title' => __('admin_sync_title'),
  'content' => '<p>' . __('admin_sync_desc') . '</p>',
  'footer' =>
    ui_button(__('admin_confirm_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-sync-indexes')"]]) .
    ui_button(__('admin_sync_run'), 'primary', 'md', ['icon' => 'fas fa-sync', 'attrs' => ['onclick' => 'syncIndexesConfirm()']]),
  'footer_right' => true,
]);
?>

<script>
const _t = <?= json_encode([
  'scores_modal_delete_body' => __('scores_modal_delete_body'),
  'scores_modal_ban_body1' => __('scores_modal_ban_body1'),
  'admin_col_banned' => __('admin_col_banned'),
  'admin_ban' => __('admin_ban'),
  'admin_unban' => __('admin_unban'),
  'admin_ban_infinitive' => __('admin_ban_infinitive'),
  'admin_unban_infinitive' => __('admin_unban_infinitive'),
  'admin_enable_infinitive' => __('admin_enable_infinitive'),
  'admin_disable_infinitive' => __('admin_disable_infinitive'),
  'admin_confirm_player_ban_body' => __('admin_confirm_player_ban_body'),
  'admin_confirm_user_toggle_body' => __('admin_confirm_user_toggle_body'),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

let adminToggleUrl = '';
let adminToggleBody = '';
const adminUserToggleBody = document.getElementById('modal-admin-user-toggle__body');
const adminPlayerBanBody = document.getElementById('modal-admin-player-ban__body');
let adminScoreDeleteUrl = '';
let adminScoreDeleteBody = '';
let adminScoreBanUrl = '';
let adminScoreBanBody = '';
const adminScoreDeleteBodyEl = document.getElementById('modal-admin-score-delete__body');
const adminScoreBanBodyEl = document.getElementById('modal-admin-score-ban__body');

function openModal(id, onOpen, data) {
  var overlay = document.getElementById(id);
  if (!overlay) return;
  overlay.style.display = 'block';
  overlay.removeAttribute('data-armed');
  var btn = overlay.querySelector('.ui-destructive');
  if (btn) {
    btn.innerHTML = btn.getAttribute('data-original-html') || btn.innerHTML;
    btn.classList.remove('is-armed');
  }
  if (typeof onOpen === 'function') onOpen(data);
}

function closeModal(id, onClose) {
  var overlay = document.getElementById(id);
  if (!overlay) return;
  overlay.style.display = 'none';
  if (typeof onClose === 'function') onClose();
}

function onAdminUserToggleClose() { adminToggleUrl = ''; adminToggleBody = ''; }
function onAdminPlayerBanClose() { adminToggleUrl = ''; adminToggleBody = ''; }
function onAdminScoreDeleteClose() { adminScoreDeleteUrl = ''; adminScoreDeleteBody = ''; }
function onAdminScoreBanClose() { adminScoreBanUrl = ''; adminScoreBanBody = ''; }

function postAndReload(url, body) {
  fetch(url, { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: body })
    .then(function() { location.reload(); });
}

function adminUserToggleConfirm() {
  if (adminToggleUrl) postAndReload(adminToggleUrl, adminToggleBody);
}

function adminPlayerBanConfirm() {
  if (adminToggleUrl) postAndReload(adminToggleUrl, adminToggleBody);
}

function adminScoreDeleteConfirm() {
  if (adminScoreDeleteUrl) postAndReload(adminScoreDeleteUrl, adminScoreDeleteBody);
}

function adminScoreBanConfirm() {
  if (adminScoreBanUrl) postAndReload(adminScoreBanUrl, adminScoreBanBody);
}

function syncIndexesConfirm() {
  closeModal('modal-sync-indexes');
  var output = document.getElementById('sync-indexes-output');
  if (!output) return;
  output.style.display = 'block';
  output.innerHTML = '<div class="ok">Running...</div>';

  fetch('/sync-indexes.php', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      var html = '';
      if (data.errors && data.errors.length) {
        data.errors.forEach(function(e) { html += '<div class="error">ERROR ' + e + '</div>'; });
      }
      if (data.created && data.created.length) {
        data.created.forEach(function(c) { html += '<div class="ok">CREATED ' + c + '</div>'; });
      }
      if (data.skipped && data.skipped.length) {
        data.skipped.forEach(function(s) { html += '<div style="color:#a6adc8">SKIP ' + s + '</div>'; });
      }
      if ((!data.created || !data.created.length) && (!data.errors || !data.errors.length)) {
        html = '<div class="ok">All indexes already exist.</div>';
      }
      output.innerHTML = html;
    })
    .catch(function(err) {
      output.innerHTML = '<div class="error">ERROR ' + err.message + '</div>';
    });
}

document.addEventListener('click', function (e) {
  var scoreDelete = e.target.closest('[data-admin-score-delete]');
  if (scoreDelete) {
    e.preventDefault();
    adminScoreDeleteUrl = scoreDelete.getAttribute('data-post-url');
    adminScoreDeleteBody = scoreDelete.getAttribute('data-post-body');
    adminScoreDeleteBodyEl.textContent = _t.scores_modal_delete_body + ' ' + (scoreDelete.dataset.player || '') + '?';
    openModal('modal-admin-score-delete');
    return;
  }

  var scoreBan = e.target.closest('[data-admin-score-ban]');
  if (scoreBan) {
    e.preventDefault();
    adminScoreBanUrl = scoreBan.getAttribute('data-post-url');
    adminScoreBanBody = scoreBan.getAttribute('data-post-body');
    adminScoreBanBodyEl.textContent = _t.scores_modal_ban_body1 + ' ' + (scoreBan.dataset.player || '') + ' (' + (scoreBan.dataset.game || '') + ')?';
    openModal('modal-admin-score-ban');
    return;
  }

  var toggle = e.target.closest('.ui-toggle');
  if (!toggle) return;
  e.preventDefault();
  adminToggleUrl = toggle.getAttribute('data-post-url');
  adminToggleBody = toggle.getAttribute('data-post-body');

  var tableHeader = toggle.closest('.ui-table').querySelector('.ui-table-header');
  if (tableHeader && tableHeader.textContent.indexOf(_t.admin_col_banned) !== -1) {
    var row = toggle.closest('tr');
    var cells = row.querySelectorAll('.ui-table-cell');
    var playerName = cells.length > 1 ? cells[1].textContent.trim() : '';
    var gameName = cells.length > 3 ? cells[3].textContent.trim() : '';
    var isBanning = toggle.style.color === 'rgb(156, 163, 175)' || toggle.getAttribute('title') === _t.admin_ban;
    var actionLabel = isBanning ? _t.admin_ban_infinitive : _t.admin_unban_infinitive;
    adminPlayerBanBody.textContent = _t.admin_confirm_player_ban_body
      .replace('{action}', actionLabel)
      .replace('{player}', playerName)
      .replace('{game}', gameName);
    openModal('modal-admin-player-ban');
  } else {
    var row = toggle.closest('tr');
    var cells = row.querySelectorAll('.ui-table-cell');
    var userName = cells.length > 1 ? cells[1].textContent.trim() : '';
    var isEnabling = toggle.style.color === 'rgb(156, 163, 175)';
    var actionLabel = isEnabling ? _t.admin_enable_infinitive : _t.admin_disable_infinitive;
    adminUserToggleBody.textContent = _t.admin_confirm_user_toggle_body
      .replace('{action}', actionLabel)
      .replace('{user}', userName);
    openModal('modal-admin-user-toggle');
  }
});
</script>
