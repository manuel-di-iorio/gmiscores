<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("lib/csrf.php");
require_once("models/Game.php");

csrf_validate_request();

if (!isset($_POST["id"])) {
  header("Location: games.php");
  exit;
}

$gameId = (int)$_POST["id"];
$secret = bin2hex(random_bytes(16));
Game::regenerateSecretWithAccess($gameId, $user["id"], $secret);

header("Location: game.php?id=" . $gameId);
