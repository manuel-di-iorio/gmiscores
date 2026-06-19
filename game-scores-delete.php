<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("lib/csrf.php");
require_once("models/Score.php");

csrf_validate_request();

if (isset($_POST["id"])) {
  Score::delete((int)$_POST["id"], $user["id"]);
}

$leaderboardId = isset($_POST["leaderboard_id"]) ? (int)$_POST["leaderboard_id"] : 0;

if (isset($_POST["game"])) {
  $redirect = "game-scores.php?id=" . (int)$_POST["game"];
  if ($leaderboardId > 0) {
    $redirect .= "&leaderboard_id=" . $leaderboardId;
  }
  header("Location: $redirect");
} else {
  header("Location: games.php");
}
