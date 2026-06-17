<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("models/Score.php");
require_once("includes/table.php");

$userId = $user["id"];

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

$view = "home";
$pageName = "Dashboard";
require_once("includes/layout.php");
