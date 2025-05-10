<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Score.php");

if (isset($_GET["id"])) {
  Score::delete((int)$_GET["id"], $user["id"]);
}

if (isset($_GET["game"])) {
  header("Location: game-scores.php?id=" . (int)$_GET["game"]);
} else {
  header("Location: games.php");
}
