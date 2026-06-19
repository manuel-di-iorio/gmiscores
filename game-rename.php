<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("models/Game.php");

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
  api_reply_error("Request method not allowed", "MethodNotAllowed", 405);
}

if (!isset($_GET["id"]) || !isset($_POST["name"])) {
  header("Location: games.php");
  exit;
}

$gameId = (int)$_GET["id"];
$name = trim($_POST["name"]);

if (strlen($name) == 0) {
  header("Location: game.php?id=" . $gameId);
  exit;
}

Game::renameWithAccess($gameId, $user["id"], $name);

header("Location: game.php?id=" . $gameId);
