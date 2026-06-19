<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("lib/csrf.php");
require_once("models/Game.php");

csrf_validate_request();

if (isset($_POST["id"])) {
  Game::deleteWithAccess((int)$_POST["id"], $user["id"]);
}

header("Location: games.php");
