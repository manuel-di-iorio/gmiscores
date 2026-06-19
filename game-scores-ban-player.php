<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("lib/csrf.php");
require_once("models/Game.php");
require_once("models/Team.php");
require_once("models/Score.php");
require_once("models/Ban.php");

csrf_validate_request();

if (!isset($_POST["id"]) || !isset($_POST["game"])) {
  header("Location: games.php");
  exit;
}

$scoreId = (int)$_POST["id"];
$gameId = (int)$_POST["game"];
$leaderboardId = isset($_POST["leaderboard_id"]) ? (int)$_POST["leaderboard_id"] : null;

$result = Game::getByIdWithAccess($gameId, $user["id"]);
if (!$result || !$result->num_rows) {
  header("Location: games.php");
  exit;
}

$result = Score::getById($scoreId);
if (!$result->num_rows) {
  header("Location: games.php");
  exit;
}
$score = $result->fetch_assoc();
$playerId = $score["player_id"];

try {
  $db->begin_transaction();

  Score::deleteByPlayerAndGame($playerId, $gameId);

  Ban::add($playerId, $score["username"], $score["ip"], $gameId);

  if (!$db->commit()) throw new Exception("TransactionCommitFailed");
} catch (Exception $e) {
  $db->rollback();
  $redirect = "game-scores.php?id=" . $gameId;
  if ($leaderboardId) {
    $redirect .= "&leaderboard_id=" . $leaderboardId;
  }
  header("Location: $redirect&error=" . urlencode("An error occurred."));
  exit;
}

$redirect = "game-scores.php?id=" . $gameId;
if ($leaderboardId) {
  $redirect .= "&leaderboard_id=" . $leaderboardId;
}
header("Location: $redirect");
