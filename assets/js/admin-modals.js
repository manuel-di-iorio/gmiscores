let adminToggleUrl = '';
let adminUserToggleBody = null;
let adminPlayerBanBody = null;
let adminScoreDeleteUrl = '';
let adminScoreBanUrl = '';
let adminScoreDeleteBody = null;
let adminScoreBanBody = null;

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
function onAdminScoreDeleteClose() { adminScoreDeleteUrl = ''; }
function onAdminScoreBanClose() { adminScoreBanUrl = ''; }

function adminUserToggleConfirm() {
  if (adminToggleUrl) location.href = adminToggleUrl;
}

function adminPlayerBanConfirm() {
  if (adminToggleUrl) location.href = adminToggleUrl;
}

function adminScoreDeleteConfirm() {
  if (adminScoreDeleteUrl) location.href = adminScoreDeleteUrl;
}

function adminScoreBanConfirm() {
  if (adminScoreBanUrl) location.href = adminScoreBanUrl;
}

document.addEventListener('DOMContentLoaded', function() {
  adminUserToggleBody = document.getElementById('modal-admin-user-toggle__body');
  adminPlayerBanBody = document.getElementById('modal-admin-player-ban__body');
  adminScoreDeleteBody = document.getElementById('modal-admin-score-delete__body');
  adminScoreBanBody = document.getElementById('modal-admin-score-ban__body');
});

document.addEventListener('click', function (e) {
  var scoreDelete = e.target.closest('[data-admin-score-delete]');
  if (scoreDelete) {
    e.preventDefault();
    adminScoreDeleteUrl = scoreDelete.getAttribute('href');
    if (adminScoreDeleteBody) {
      adminScoreDeleteBody.textContent = scores_modal_delete_body + ' ' + (scoreDelete.dataset.player || '') + '?';
    }
    openModal('modal-admin-score-delete', onAdminScoreDeleteClose, {});
    return;
  }

  var scoreBan = e.target.closest('[data-admin-score-ban]');
  if (scoreBan) {
    e.preventDefault();
    adminScoreBanUrl = scoreBan.getAttribute('href');
    if (adminScoreBanBody) {
      adminScoreBanBody.textContent = scores_modal_ban_body1 + ' ' + (scoreBan.dataset.player || '') + ' (' + (scoreBan.dataset.game || '') + ')?';
    }
    openModal('modal-admin-score-ban', onAdminScoreBanClose, {});
    return;
  }

  var toggle = e.target.closest('.ui-toggle');
  if (!toggle) return;
  e.preventDefault();
  adminToggleUrl = toggle.getAttribute('href');

  var tableHeader = toggle.closest('.ui-table').querySelector('.ui-table-header');
  if (tableHeader && tableHeader.textContent.indexOf(admin_col_banned) !== -1) {
    var row = toggle.closest('tr');
    var cells = row.querySelectorAll('.ui-table-cell');
    var playerName = cells.length > 1 ? cells[1].textContent.trim() : '';
    var gameName = cells.length > 3 ? cells[3].textContent.trim() : '';
    var isBanning = toggle.style.color === 'rgb(156, 163, 175)' || toggle.getAttribute('title') === admin_ban;
    var actionLabel = isBanning ? admin_ban : admin_unban;
    if (adminPlayerBanBody) {
      adminPlayerBanBody.textContent = admin_confirm_player_ban_body
        .replace('__ACTION__', actionLabel)
        .replace('__PLAYER__', playerName)
        .replace('__GAME__', gameName);
    }
    openModal('modal-admin-player-ban', onAdminPlayerBanClose, {});
  } else {
    var row = toggle.closest('tr');
    var cells = row.querySelectorAll('.ui-table-cell');
    var userName = cells.length > 1 ? cells[1].textContent.trim() : '';
    var isEnabling = toggle.style.color === 'rgb(156, 163, 175)';
    var actionLabel = isEnabling ? admin_enable : admin_disable;
    if (adminUserToggleBody) {
      adminUserToggleBody.textContent = admin_confirm_user_toggle_body
        .replace('__ACTION__', actionLabel)
        .replace('__USER__', userName);
    }
    openModal('modal-admin-user-toggle', onAdminUserToggleClose, {});
  }
});
