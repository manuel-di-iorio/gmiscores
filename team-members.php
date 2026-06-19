<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("lib/csrf.php");
require_once("models/Team.php");

if (!isset($_GET["id"])) {
  header("Location: teams.php");
  exit;
}

$teamId = (int)$_GET["id"];
$userId = $user["id"];

if (!Team::isAdmin($teamId, $userId)) {
  header("Location: teams.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
  csrf_validate_request();
  $discordId = isset($_POST["discord_id"]) ? trim($_POST["discord_id"]) : "";
  $role = isset($_POST["role"]) ? $_POST["role"] : "member";

  if (!empty($discordId)) {
    if ($role !== "admin") $role = "member";
    Team::addMember($teamId, $userId, $discordId, $role);
  }
  header("Location: team.php?id=$teamId&tab=members");
  exit;
}

header("Location: team.php?id=$teamId&tab=members");
