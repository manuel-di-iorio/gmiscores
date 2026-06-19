<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("models/Team.php");

if ($_SERVER['REQUEST_METHOD'] === "POST") {
  $teamName = isset($_POST["name"]) ? trim($_POST["name"]) : "";
  if (empty($teamName)) {
    header("Location: teams.php");
    exit;
  }

  $teamId = Team::create($user["id"], $teamName);
  header("Location: team.php?id=$teamId");
  exit;
}

$view = "add-team";
$pageName = __('teams_create_title');
$backUrl = "teams.php";
require_once("includes/layout.php");
