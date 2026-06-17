<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("models/Score.php");
require_once("models/Leaderboard.php");

$baseApiPath = $config["host"] . "/api/v1";

if (!isset($_GET["id"])) {
  header("Location: games.php");
  exit;
}

$gameResult = Game::getByIdAndUser((int)$_GET["id"], $user["id"]);
if (!$gameResult->num_rows) {
  header("Location: games.php");
  exit;
}
$game = $gameResult->fetch_assoc();
$gameId = $game["game_id"];

// Analytics data
$gameScoresResult = Score::countByGame($gameId, ['env' => 'production']);
$gameTotalScores = $gameScoresResult->fetch_assoc()["count"] ?? 0;
$gameUniquePlayers = Score::getUniquePlayersByGame($gameId);
$gameLeaderboardCount = Game::getLeaderboardCountByGame($gameId);

$gameScoresOverTime = [];
$result = Score::getScoresOverTimeByGame($gameId, 30);
while ($row = $result->fetch_assoc()) {
  $gameScoresOverTime[] = $row;
}

$gameCountries = [];
$result = Score::getCountriesByGame($gameId);
while ($row = $result->fetch_assoc()) {
  $gameCountries[] = $row;
}
$gameCountryCount = count($gameCountries);

$gameScoresByLb = [];
$result = Score::getScoresByLeaderboardByGame($gameId);
while ($row = $result->fetch_assoc()) {
  $gameScoresByLb[] = $row;
}

$pageName = $game["name"];
$view = "game";
require_once("includes/layout.php");
