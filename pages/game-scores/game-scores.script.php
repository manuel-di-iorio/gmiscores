<script>
/* MODALS */
let modalSelectedScore;

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
  location.href = "game-scores-delete.php?id=" + modalSelectedScore + "&game=<?= $game["game_id"]; ?>";
}

// MODAL: Clear scores
function clearScores() {
  location.href = "game-scores-clear.php?id=<?= $game["game_id"]; ?>";
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
  location.href = "game-scores-ban-player.php?id=" + modalSelectedScore + "&game=<?= $game["game_id"]; ?>";
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
function resetInsertScoreForm() {
  document.getElementById("form-add-score").reset();
}
</script>
