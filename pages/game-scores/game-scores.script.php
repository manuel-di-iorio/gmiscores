<script>
/* MODALS */
let modalSelectedScore;

/* BATCH SELECTION */
document.addEventListener('change', function(e) {
  if (e.target.name === 'selected_ids[]') {
    updateDeleteSelectedButton();
  }
});

function updateDeleteSelectedButton() {
  var checked = document.querySelectorAll('input[name="selected_ids[]"]:checked');
  var btn = document.getElementById('btn-delete-selected-wrapper');
  if (checked.length > 0) {
    btn.style.display = 'inline';
  } else {
    btn.style.display = 'none';
  }
}

function getSelectedScoreIds() {
  var checked = document.querySelectorAll('input[name="selected_ids[]"]:checked');
  var ids = [];
  for (var i = 0; i < checked.length; i++) {
    ids.push(parseInt(checked[i].value));
  }
  return ids;
}

function deleteSelectedScores() {
  var ids = getSelectedScoreIds();
  if (ids.length === 0) return;

  fetch('game-scores-delete-batch.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      score_ids: ids,
      game_id: <?= $game["game_id"]; ?>,
      leaderboard_id: <?= $leaderboardId; ?>
    })
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success) {
      location.reload();
    } else {
      alert(<?= json_encode(__('scores_script_error_prefix')) ?> + (data.error || <?= json_encode(__('scores_script_error_fallback')) ?>));
    }
  })
  .catch(function() {
    alert(<?= json_encode(__('scores_script_network_error')) ?>);
  });
}

function onDeleteSelectedScoresModalOpen() {
  var ids = getSelectedScoreIds();
  document.getElementById('modal-delete-selected-scores__count').textContent = ids.length;
}

// MODAL: Delete score
const modalDivScorePlayerName = document.getElementById('modal-delete-score__player-name');

function onDeleteScoreModalOpen({ scoreId, playerName }) {
  modalSelectedScore = scoreId;
  modalDivScorePlayerName.innerHTML = playerName;
}

function onDeleteScoreModalClose() {
  modalDivScorePlayerName.innerHTML = "";
}

function deleteScore() {
  location.href = "game-scores-delete.php?id=" + modalSelectedScore + "&game=<?= $game["game_id"]; ?>&leaderboard_id=<?= $leaderboardId; ?>";
}

// MODAL: Clear scores
function clearScores() {
  location.href = "game-scores-clear.php?id=<?= $game["game_id"]; ?>&leaderboard_id=<?= $leaderboardId; ?>";
}

// MODAL: Ban Player
const modalDivBanPlayerName = document.getElementById('modal-ban-player__player-name');

function onBanPlayerModalOpen({ scoreId, playerName }) {
  modalSelectedScore = scoreId;
  modalDivBanPlayerName.innerHTML = playerName;
}

function onBanPlayerModalClose() {
  modalDivBanPlayerName.innerHTML = "";
}

function banPlayer() {
  location.href = "game-scores-ban-player.php?id=" + modalSelectedScore + "&game=<?= $game["game_id"]; ?>&leaderboard_id=<?= $leaderboardId; ?>";
}

// MODAL: Ban Player
const modalDivViewScoreData_scoreId = document.getElementById('modal-view-score-data__score-id');
const modalDivViewScoreData_playerName = document.getElementById('modal-view-score-data__player-name');
const modalDivViewScoreData_data = document.getElementById('modal-view-score-data__data');

function onViewScoreDataModalOpen({ scoreId, playerName, data }) {
  // modalSelectedScore = scoreId;
  modalDivViewScoreData_scoreId.innerHTML = scoreId;
  modalDivViewScoreData_playerName.innerHTML = playerName;
  modalDivViewScoreData_data.value = atob(data);
}


/* IMPORT FILE UPLOAD */
function importPickFile() {
  document.getElementById("btn-import-pick-file").click();
}

function importUploadOnChange(elem) {
  document.getElementById("form-import").submit();
  document.getElementById("btn-import-pick-file").value = "";
}


/* Add score */
document.getElementById("form-add-score").addEventListener("submit", function() {
  var btn = this.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:8px"></i> ' + <?= json_encode(__('scores_script_sending')) ?>;
});

function resetInsertScoreForm() {
  document.getElementById("form-add-score").reset();
}
</script>
