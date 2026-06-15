<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");

$baseApiPath = $config["host"] . "/api/v1";

// Get the game
if (!isset($_GET["id"])) {
  header("Location: games.php");
  exit;
}

$gameResult = Game::getByIdAndUser((int)$_GET["id"], $user["id"]);
if (!$gameResult->num_rows) {
  header("Location: games.php");
  exit;
}
$game = $gameResult->fetch_assoc();

$pageName = $game["name"];
$view = "game";
require_once("includes/layout.php");
