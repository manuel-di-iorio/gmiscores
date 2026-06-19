<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("lib/csrf.php");
require_once("models/Score.php");

csrf_validate_request();

if (!isset($_POST["id"])) {
  header("Location: games.php");
  exit;
}

$gameId = (int)$_POST["id"];
$leaderboardId = isset($_POST["leaderboard_id"]) ? (int)$_POST["leaderboard_id"] : null;

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
