<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("models/Score.php");
require_once("models/Team.php");
require_once("includes/table.php");

if (isset($_GET["error"])) {
  $errorMessages = [
    "GetTokensRequestError" => __("error_get_tokens"),
    "GetUserDataRequestError" => __("error_get_user_data"),
  ];
  $errorKey = $_GET["error"];
  $loginError = $errorMessages[$errorKey] ?? __("error_generic");
  unset($_GET["error"]);
} else {
  $loginError = null;
}

$userId = $user["id"];
$selectedTeamId = isset($_COOKIE['selected_team_id']) && $_COOKIE['selected_team_id'] !== '' ? (int)$_COOKIE['selected_team_id'] : null;

if ($selectedTeamId !== null && !Team::isMember($selectedTeamId, $userId)) {
  $selectedTeamId = null;
}

if ($selectedTeamId !== null) {
  $totalScores = Score::countByTeam($selectedTeamId);
  $totalPlayers = Score::getUniquePlayersByTeam($selectedTeamId);
  $totalGames = Game::countByTeamId($selectedTeamId);
  $scoresToday = Score::countByTeamToday($selectedTeamId);

  $scoresOverTime = [];
  $result = Score::getScoresPerDayByTeam($selectedTeamId, 30);
  while ($row = $result->fetch_assoc()) {
    $scoresOverTime[] = $row;
  }

  $scoresByGame = [];
  $result = Score::getScoresByGameByTeam($selectedTeamId);
  while ($row = $result->fetch_assoc()) {
    $scoresByGame[] = $row;
  }

  $countries = [];
  $result = Score::getCountriesByTeam($selectedTeamId);
  while ($row = $result->fetch_assoc()) {
    $countries[] = $row;
  }
} else {
  $totalScores = Score::countByUser($userId);
  $totalPlayers = Score::getUniquePlayersByUser($userId);
  $totalGames = Game::countByUser($userId);
  $scoresToday = Score::countByUserToday($userId);

  $scoresOverTime = [];
  $result = Score::getScoresPerDayByUser($userId, 30);
  while ($row = $result->fetch_assoc()) {
    $scoresOverTime[] = $row;
  }

  $scoresByGame = [];
  $result = Score::getScoresByGameByUser($userId);
  while ($row = $result->fetch_assoc()) {
    $scoresByGame[] = $row;
  }

  $countries = [];
  $result = Score::getCountriesByUser($userId);
  while ($row = $result->fetch_assoc()) {
    $countries[] = $row;
  }
}

$view = "home";
$pageName = "Dashboard";
require_once("includes/layout.php");
