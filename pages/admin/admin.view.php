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
}

.migrate-output .ok { color: #a6e3a1; }
.migrate-output .error { color: #f38ba8; }
.migrate-output .fail { color: #fab387; }
</style>

<?php
// Render the active tab's content
ob_start();
$activeTab = $_GET["tab"] ?? "users";
require "pages/admin/admin-tab-render.php";
$activeContent = ob_get_clean();

// Build tab array with lazy loading for inactive tabs
$tabs = [];
$tabIds = ['users', 'players', 'analytics', 'migrate'];
$tabLabels = [
  'users' => __('admin_tab_users'),
  'players' => __('admin_tab_players'),
  'analytics' => __('admin_tab_analytics'),
  'migrate' => __('admin_tab_migrate'),
];
$tabIcons = [
  'users' => 'fas fa-users',
  'players' => 'fas fa-user-friends',
  'analytics' => 'fas fa-chart-pie',
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
    $skeletonType = $id === 'analytics' ? 'chart' : 'table-row';
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
    ui_button(__('admin_confirm_confirm'), 'primary', 'md', ['icon' => 'fas fa-check', 'attrs' => ['onclick' => 'adminUserToggleConfirm()']]),
]);

echo ui_modal('modal-admin-player-ban', [
  'title' => __('admin_confirm_title'),
  'content' => '<p id="modal-admin-player-ban__body"></p>',
  'footer' =>
    ui_button(__('admin_confirm_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-admin-player-ban', onAdminPlayerBanClose)"]]) .
    ui_button(__('admin_confirm_confirm'), 'danger', 'md', ['icon' => 'fas fa-ban', 'attrs' => ['onclick' => 'adminPlayerBanConfirm()']]),
]);
?>

<script>
let adminToggleUrl = '';
const adminUserToggleBody = document.getElementById('modal-admin-user-toggle__body');
const adminPlayerBanBody = document.getElementById('modal-admin-player-ban__body');

function openModal(id, onOpen, data) {
  var overlay = document.getElementById(id);
  if (!overlay) return;
  overlay.style.display = 'block';
  if (typeof onOpen === 'function') onOpen(data);
}

function closeModal(id, onClose) {
  var overlay = document.getElementById(id);
  if (!overlay) return;
  overlay.style.display = 'none';
  if (typeof onClose === 'function') onClose();
}

function onAdminUserToggleClose() { adminToggleUrl = ''; }
function onAdminPlayerBanClose() { adminToggleUrl = ''; }

function adminUserToggleConfirm() {
  if (adminToggleUrl) location.href = adminToggleUrl;
}

function adminPlayerBanConfirm() {
  if (adminToggleUrl) location.href = adminToggleUrl;
}

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.ui-table').forEach(function (table) {
    table.addEventListener('click', function (e) {
      var toggle = e.target.closest('.ui-toggle');
      if (!toggle) return;
      e.preventDefault();
      adminToggleUrl = toggle.getAttribute('href');

      var tableHeader = toggle.closest('.ui-table').querySelector('.ui-table-header');
      if (tableHeader && tableHeader.textContent.indexOf('<?= __('admin_col_banned') ?>') !== -1) {
        var row = toggle.closest('tr');
        var cells = row.querySelectorAll('.ui-table-cell');
        var playerName = cells.length > 1 ? cells[1].textContent.trim() : '';
        var gameName = cells.length > 3 ? cells[3].textContent.trim() : '';
        var isBanning = toggle.style.color === 'rgb(156, 163, 175)' || toggle.getAttribute('title') === '<?= __('admin_ban') ?>';
        var actionLabel = isBanning ? '<?= __('admin_ban') ?>' : '<?= __('admin_unban') ?>';
        adminPlayerBanBody.textContent = '<?= __('admin_confirm_player_ban_body', ['action' => '__ACTION__', 'player' => '__PLAYER__', 'game' => '__GAME__']) ?>'
          .replace('__ACTION__', actionLabel)
          .replace('__PLAYER__', playerName)
          .replace('__GAME__', gameName);
        openModal('modal-admin-player-ban', onAdminPlayerBanClose, {});
      } else {
        var row = toggle.closest('tr');
        var cells = row.querySelectorAll('.ui-table-cell');
        var userName = cells.length > 1 ? cells[1].textContent.trim() : '';
        var isEnabling = toggle.style.color === 'rgb(156, 163, 175)';
        var actionLabel = isEnabling ? '<?= __('admin_enable') ?>' : '<?= __('admin_disable') ?>';
        adminUserToggleBody.textContent = '<?= __('admin_confirm_user_toggle_body', ['action' => '__ACTION__', 'user' => '__USER__']) ?>'
          .replace('__ACTION__', actionLabel)
          .replace('__USER__', userName);
        openModal('modal-admin-user-toggle', onAdminUserToggleClose, {});
      }
    });
  });
});
</script>