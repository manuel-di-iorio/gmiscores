<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Score.php");

if (!isset($_GET["id"])) {
  header("Location: games.php");
  exit;
}

$gameId = (int)$_GET["id"];
Score::clear($gameId, $user["id"]);
header("Location: game-scores.php?id=$gameId");
