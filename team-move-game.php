<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("models/Team.php");
require_once("models/Game.php");

if (!isset($_GET["id"])) {
  header("Location: teams.php");
  exit;
}

$gameId = (int)$_GET["id"];
$userId = $user["id"];

if ($_SERVER['REQUEST_METHOD'] === "POST") {
  $targetTeamId = isset($_POST["target_team_id"]) ? (int)$_POST["target_team_id"] : null;
  if ($targetTeamId === 0) $targetTeamId = null;

  Game::moveToTeamWithAccess($gameId, $userId, $targetTeamId);

  if ($targetTeamId !== null) {
    header("Location: team.php?id=$targetTeamId&tab=games");
  } else {
    header("Location: games.php");
  }
  exit;
}

$gameResult = Game::getByIdWithAccess($gameId, $userId);
if (!$gameResult || !$gameResult->num_rows) {
  header("Location: games.php");
  exit;
}
$game = $gameResult->fetch_assoc();
$currentTeamId = $game["team_id"];

$userTeams = [];
$teamsResult = Team::listByUser($userId);
while ($row = $teamsResult->fetch_assoc()) {
  if ($row["team_id"] != $currentTeamId) {
    $userTeams[] = $row;
  }
}

$pageName = __('team_games_move_title') . ' - ' . $game["name"];
$view = "team-move-game";
$backUrl = $currentTeamId ? "team.php?id=$currentTeamId&tab=games" : "games.php";
require_once("includes/layout.php");
