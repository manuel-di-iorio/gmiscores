<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Score.php");

if (isset($_GET["id"])) {
  Score::delete((int)$_GET["id"], $user["id"]);
}

$leaderboardId = isset($_GET["leaderboard_id"]) ? (int)$_GET["leaderboard_id"] : 0;

if (isset($_GET["game"])) {
  $redirect = "game-scores.php?id=" . (int)$_GET["game"];
  if ($leaderboardId > 0) {
    $redirect .= "&leaderboard_id=" . $leaderboardId;
  }
  header("Location: $redirect");
} else {
  header("Location: games.php");
}