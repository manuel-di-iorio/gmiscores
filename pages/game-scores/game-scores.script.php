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
      alert('Errore: ' + (data.error || 'Operazione fallita'));
    }
  })
  .catch(function() {
    alert('Errore di rete durante l\'eliminazione');
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


// --------------

// When the user clicks anywhere outside of the modal, close it
const modalDeleteScoreDiv = document.getElementById('modal-delete-score');
const modalClearScoresDiv = document.getElementById('modal-clear-scores');
const modalBanPlayerDiv = document.getElementById('modal-ban-player');
const modalViewScoreData = document.getElementById('modal-view-score-data');

window.onclick = (event) => {
  switch (event.target) {
    case modalDeleteScoreDiv: closeModal('modal-delete-score', onDeleteScoreModalClose); break;
    case modalClearScoresDiv: closeModal('modal-clear-scores'); break;
    case modalBanPlayerDiv: closeModal('modal-ban-player', onBanPlayerModalClose); break;
    case modalViewScoreData: closeModal('modal-view-score-data'); break;
  }
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
  btn.innerHTML = '<i class="fas fa-spinner fa-spin w3-margin-right"></i> Invio...';
});

function resetInsertScoreForm() {
  document.getElementById("form-add-score").reset();
}
</script>
