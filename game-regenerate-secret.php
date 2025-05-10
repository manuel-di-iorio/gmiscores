<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");

if (!isset($_GET["id"])) {
  header("Location: games.php");
  exit;
}

$gameId = (int)$_GET["id"];
$secret = bin2hex(random_bytes(16));
Game::regenerateSecret($gameId, $user["id"], $secret);

header("Location: game.php?id=" . $gameId);
