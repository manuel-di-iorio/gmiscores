<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("models/Score.php");

if (!isset($_GET["id"])) {
  header("Location: games.php");
  exit;
}

$gameId = (int)$_GET["id"];
$leaderboardId = isset($_GET["leaderboard_id"]) ? (int)$_GET["leaderboard_id"] : null;

if ($leaderboardId) {
  Score::clear($gameId, $user["id"], $leaderboardId);
} else {
  Score::clear($gameId, $user["id"]);
}

$redirect = "game-scores.php?id=$gameId";
if ($leaderboardId) {
  $redirect .= "&leaderboard_id=$leaderboardId";
}
header("Location: $redirect");