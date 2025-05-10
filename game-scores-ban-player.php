<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("models/Score.php");
require_once("models/Ban.php");

if (!isset($_GET["id"]) || !isset($_GET["game"])) {
  header("Location: games.php");
  exit;
}

$scoreId = (int)$_GET["id"];
$gameId = (int)$_GET["game"];

// Check that the game is owned from the logged in user
$result = Game::getByIdAndUser($gameId, $user["id"]);
if (!$result->num_rows) {
  header("Location: games.php");
  exit;
}

// Get the score data (ip, player id/username)
$result = Score::getById($scoreId);
if (!$result->num_rows) {
  header("Location: games.php");
  exit;
}
$score = $result->fetch_assoc();
$playerId = $score["player_id"];

// Execute the ban
try {
  $db->begin_transaction();

  // Remove all player scores on this game
  Score::deleteByPlayerAndGame($playerId, $gameId);

  // Add a ban entry
  Ban::add($playerId, $score["username"], $score["ip"], $gameId);

  if (!$db->commit()) throw new Exception("TransactionCommitFailed");
} catch (Exception $e) {
  $db->rollback();
  header("Location: game-scores.php?id=" . $gameId . "&error=" . urlencode($e->getMessage()));
  exit;
}

header("Location: game-scores.php?id=" . (int)$_GET["game"]);
