<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("lib/csrf.php");
require_once("models/Ban.php");

csrf_validate_request();

$gameId = null;

if (isset($_POST["player_id"]) && isset($_POST["game_id"])) {
  $playerId = (int)$_POST["player_id"];
  $gameId = (int)$_POST["game_id"];
  $userId = $user["id"];

  $gameResult = Game::getByIdWithAccess($gameId, $userId);
  if ($gameResult && $gameResult->num_rows) {
    Ban::removeByPlayerAndGame($playerId, $gameId);
  }
} elseif (isset($_POST["id"])) {
  $banId = (int)$_POST["id"];
  $userId = $user["id"];

  $result = Ban::getByIdAndUser($banId, $userId);
  if ($result && $result->num_rows) {
    Ban::remove($banId, $userId);
    $gameId = isset($_POST["game"]) ? (int)$_POST["game"] : null;
  }
}

if (isset($_POST["game"])) {
  $gameId = (int)$_POST["game"];
}

if ($gameId) {
  header("Location: game.php?id=" . $gameId . "&tab=players");
} else {
  header("Location: games.php");
}
