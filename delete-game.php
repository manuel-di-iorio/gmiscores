<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");

if (isset($_GET["id"])) {
  Game::delete((int)$_GET["id"], $user["id"]);
}

header("Location: games.php");
