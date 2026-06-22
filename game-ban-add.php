<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("lib/csrf.php");
require_once("models/Game.php");
require_once("models/Team.php");
require_once("models/Player.php");
require_once("models/Score.php");
require_once("models/Ban.php");

csrf_validate_request();

if (!isset($_POST["player_id"]) || !isset($_POST["game_id"])) {
  header("Location: games.php");
  exit;
}

$playerId = (int)$_POST["player_id"];
$gameId = (int)$_POST["game_id"];

$result = Game::getByIdWithAccess($gameId, $user["id"]);
if (!$result || !$result->num_rows) {
  header("Location: games.php");
  exit;
}

$existingBan = Ban::getByPlayerAndGame($playerId, $gameId);
if ($existingBan->num_rows) {
  header("Location: game.php?id=$gameId&tab=players");
  exit;
}

$playerResult = Player::getByIdWithScores($playerId);
if (!$playerResult) {
  header("Location: game.php?id=$gameId&tab=players");
  exit;
}

$playerName = $playerResult["username"];

try {
  $db->begin_transaction();

  Score::deleteByPlayerAndGame($playerId, $gameId);

  Ban::add($playerId, $playerName, null, $gameId);

  if (!$db->commit()) throw new Exception("TransactionCommitFailed");
} catch (Exception $e) {
  $db->rollback();
  header("Location: game.php?id=$gameId&tab=players");
  exit;
}

header("Location: game.php?id=$gameId&tab=players");
