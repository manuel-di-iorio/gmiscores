<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Ban.php");

if (isset($_GET["id"])) {
  $banId = (int)$_GET["id"];
  $userId = $user["id"];

  // Check that the user owns the game
  $result = Ban::getByIdAndUser($banId, $userId);
  if (!$result->num_rows) {
    header("Location: games.php");
    exit;
  }

  // Remove the ban
  Ban::remove($banId);
}

if (isset($_GET["game"])) {
  header("Location: game-bans.php?id=" . (int)$_GET["game"]);
} else {
  header("Location: games.php");
}
