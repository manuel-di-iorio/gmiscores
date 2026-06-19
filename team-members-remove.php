<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("models/Team.php");

if (!isset($_GET["id"]) || !isset($_GET["user_id"])) {
  header("Location: teams.php");
  exit;
}

$teamId = (int)$_GET["id"];
$targetUserId = (int)$_GET["user_id"];
$userId = $user["id"];

if (!Team::isAdmin($teamId, $userId)) {
  header("Location: teams.php");
  exit;
}

Team::removeMember($teamId, $userId, $targetUserId);
header("Location: team.php?id=$teamId&tab=members");
