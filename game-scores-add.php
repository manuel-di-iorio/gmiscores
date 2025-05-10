<?php
require_once("lib/db.php");
require_once("lib/insertScore.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("models/Score.php");
require_once("models/Player.php");

// Input validation
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
  api_reply_error("Request method not allowed", "MethodNotAllowed", 405);
}

if (!isset($_GET["id"])) {
  header("Location: games.php");
  exit;
}

if (!isset($_GET["id"]) || !isset($_POST["score"]) || !isset($_POST["player"]) || !isset($_POST["insertMode"])) {
  api_reply_error("Missing parameters", "ValidationError", 400);
}

$userId = $user["id"];
$gameId = (int)$_GET["id"];
$score = (float)$_POST["score"];
$sign = isset($_POST["sign"]) ? $_POST["sign"] : NULL;
$leaderboardId = isset($_POST["leaderboard"]) ? (string)$_POST["leaderboard"] : "default";
$insertMode = $_POST["insertMode"];
$playerName = $_POST["player"];
$data = isset($_POST["data"]) ? $_POST["data"] : NULL;
$minScore = isset($_POST["minScore"]) ? (float)$_POST["minScore"] : NULL;
$maxScore = isset($_POST["maxScore"]) ? (float)$_POST["maxScore"] : NULL;

if (empty($sign)) $sign = NULL;
if (empty($data)) $data = NULL;

// Check that the user owns the game
$result = Game::getByIdAndUser($gameId, $userId);
if (!$result->num_rows) {
  header("Location: games.php");
  exit;
}

// Score format validation
if (!is_numeric($score)) {
  api_reply_error("Invalid parameter 'score'", "ValidationError", 400);
}

// Insert mode validation
if (!is_null($insertMode) && $insertMode !== "all" && $insertMode !== "higher" && $insertMode !== "lower") {
  api_reply_error("Invalid parameter 'insertMode'", "ValidationError", 400);
}

// Create the player if not exists
Player::create($playerName);
$player = Player::getByName($playerName)->fetch_assoc();
$playerId = $player["player_id"];

// Add the score
$ip = NULL;
$country = NULL;

/* Insert the score */
[
  "scoreId" => $scoreId,
  "scoreAction" => $scoreAction,
  "position" => $position,
] = insert_score([ 
  "insertMode" => $insertMode,
  "playerName" => $playerName,
  "gameId" => $gameId,
  "score" => $score,
  "ip" => $ip,
  "country" => $country,
  "sign" => $sign,
  "leaderboardId" => $leaderboardId,
  "data" => $data,
  "minScore" => $minScore,
  "maxScore" => $maxScore
]);

header("Location: game-scores.php?id=$gameId");
