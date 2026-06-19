<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("lib/csrf.php");
require_once("models/Ban.php");

csrf_validate_request();

if (isset($_POST["id"])) {
  $banId = (int)$_POST["id"];
  $userId = $user["id"];

  $result = Ban::getByIdAndUser($banId, $userId);
  if (!$result->num_rows) {
    header("Location: games.php");
    exit;
  }

  Ban::remove($banId, $userId);
}

if (isset($_POST["game"])) {
  header("Location: game-bans.php?id=" . (int)$_POST["game"]);
} else {
  header("Location: games.php");
}
