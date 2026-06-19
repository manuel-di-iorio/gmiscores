<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("includes/table.php");

$selectedTeamId = isset($_COOKIE['selected_team_id']) && $_COOKIE['selected_team_id'] !== '' ? (int)$_COOKIE['selected_team_id'] : null;

$nameFilter = isset($_GET['name']) ? trim($_GET['name']) : null;

if ($selectedTeamId !== null) {
  require_once("models/Team.php");
  if (!Team::isMember($selectedTeamId, $user["id"])) {
    $selectedTeamId = null;
    setcookie("selected_team_id", "", ["expires" => time() - 3600, "path" => "/"]);
    $_COOKIE['selected_team_id'] = '';
  }
}

if ($selectedTeamId !== null) {
  $result = Game::listByTeam($selectedTeamId, $nameFilter);
  $pageName = __('games_title');
} else {
  $result = Game::listByUser($user["id"], $nameFilter);
  $pageName = __('games_title');
}

$games = [];
while ($row = $result->fetch_assoc()) {
  $games[] = $row;
}

$view = "games";
require_once("includes/layout.php");
