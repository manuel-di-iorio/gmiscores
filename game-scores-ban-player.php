<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("models/Game.php");
require_once("models/Score.php");
require_once("models/Ban.php");

if (!isset($_GET["id"]) || !isset($_GET["game"])) {
  header("Location: games.php");
  exit;
}

$scoreId = (int)$_GET["id"];
$gameId = (int)$_GET["game"];
$leaderboardId = isset($_GET["leaderboard_id"]) ? (int)$_GET["leaderboard_id"] : null;

$result = Game::getByIdAndUser($gameId, $user["id"]);
if (!$result->num_rows) {
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
  header("Location: $redirect&error=" . urlencode($e->getMessage()));
  exit;
}

$redirect = "game-scores.php?id=" . $gameId;
if ($leaderboardId) {
  $redirect .= "&leaderboard_id=" . $leaderboardId;
}
header("Location: $redirect");