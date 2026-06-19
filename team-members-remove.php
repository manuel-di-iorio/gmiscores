<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("lib/csrf.php");
require_once("models/Team.php");

csrf_validate_request();

if (!isset($_POST["id"]) || !isset($_POST["user_id"])) {
  header("Location: teams.php");
  exit;
}

$teamId = (int)$_POST["id"];
$targetUserId = (int)$_POST["user_id"];
$userId = $user["id"];

if (!Team::isAdmin($teamId, $userId)) {
  header("Location: teams.php");
  exit;
}

Team::removeMember($teamId, $userId, $targetUserId);
header("Location: team.php?id=$teamId&tab=members");
