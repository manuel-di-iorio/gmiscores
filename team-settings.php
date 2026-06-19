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
  $teamName = isset($_POST["name"]) ? trim($_POST["name"]) : "";
  if (!empty($teamName)) {
    Team::updateName($teamId, $userId, $teamName);
  }
  header("Location: team.php?id=$teamId&tab=config");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === "DELETE") {
  csrf_validate_request();
  Team::delete($teamId, $userId);
  header("Location: teams.php");
  exit;
}

header("Location: teams.php");
