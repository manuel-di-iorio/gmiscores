<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Team.php");
require_once("models/Game.php");
require_once("assets/ui-kit/kit.php");
require_once("includes/table-filters.php");
require_once("includes/table.php");

if (!isset($_GET["id"])) {
  header("Location: teams.php");
  exit;
}

$teamId = (int)$_GET["id"];
$userId = $user["id"];

if (!Team::isMember($teamId, $userId)) {
  header("Location: teams.php");
  exit;
}

$team = Team::getById($teamId);
if (!$team) {
  header("Location: teams.php");
  exit;
}

$isTeamAdmin = Team::isAdmin($teamId, $userId);
$activeTab = $_GET["tab"] ?? "config";

switch ($activeTab) {
  case 'members':
    $members = [];
    $membersResult = Team::getMembers($teamId);
    while ($row = $membersResult->fetch_assoc()) {
      $members[] = $row;
    }
    break;
  case 'games':
    $nameFilter = isset($_GET['name']) ? trim($_GET['name']) : null;
    $games = [];
    $gamesResult = Game::listByTeam($teamId, $nameFilter);
    while ($row = $gamesResult->fetch_assoc()) {
      $games[] = $row;
    }
    break;
}

require_once("lib/csrf.php");

if (isset($_GET['ajax'])) {
  header('Content-Type: text/html; charset=utf-8');
  ob_start();
  require "pages/team/team-tab-render.php";
  echo ob_get_clean();
  exit;
}

$pageName = $team["name"];
$view = "team";
$backUrl = "teams.php";
require_once("includes/layout.php");
