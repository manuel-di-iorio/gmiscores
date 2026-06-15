<?php
require_once("lib/db.php");
require_once("lib/insertScore.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("models/Game.php");
require_once("models/Score.php");
require_once("models/Player.php");
require_once("models/Leaderboard.php");

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
  api_reply_error("Request method not allowed", "MethodNotAllowed", 405);
}

if (!isset($_GET["id"])) {
  header("Location: games.php");
  exit;
}

if (!isset($_GET["id"]) || !isset($_POST["score"]) || !isset($_POST["player"]) || !isset($_POST["insertMode"]) || !isset($_POST["leaderboard_id"])) {
  api_reply_error("Missing parameters", "ValidationError", 400);
}

$userId = $user["id"];
$gameId = (int)$_GET["id"];
$leaderboardId = (int)$_POST["leaderboard_id"];
$score = (float)$_POST["score"];
$sign = isset($_POST["sign"]) ? $_POST["sign"] : NULL;
$tags = isset($_POST["tags"]) ? (string)$_POST["tags"] : "default";
$insertMode = $_POST["insertMode"];
$playerName = $_POST["player"];
$data = isset($_POST["data"]) ? $_POST["data"] : NULL;
$minScore = isset($_POST["minScore"]) ? (float)$_POST["minScore"] : NULL;
$maxScore = isset($_POST["maxScore"]) ? (float)$_POST["maxScore"] : NULL;

if (empty($sign)) $sign = NULL;
if (empty($data)) $data = NULL;

$result = Game::getByIdAndUser($gameId, $userId);
if (!$result->num_rows) {
  header("Location: games.php");
  exit;
}

// Verify leaderboard exists and belongs to this game
$lb = Leaderboard::getById($leaderboardId);
if (!$lb || $lb['game_id'] != $gameId) {
  header("Location: game-scores.php?id=$gameId&leaderboard_id=$leaderboardId&error=" . urlencode("Leaderboard non valida."));
  exit;
}

if (!is_numeric($score)) {
  api_reply_error("Invalid parameter 'score'", "ValidationError", 400);
}

if (!is_null($insertMode) && $insertMode !== "all" && $insertMode !== "higher" && $insertMode !== "lower") {
  api_reply_error("Invalid parameter 'insertMode'", "ValidationError", 400);
}

Player::create($playerName);
$player = Player::getByName($playerName)->fetch_assoc();
$playerId = $player["player_id"];

$ip = NULL;
$country = NULL;

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
  "tags" => $tags,
  "data" => $data,
  "minScore" => $minScore,
  "maxScore" => $maxScore
]);

header("Location: game-scores.php?id=$gameId&leaderboard_id=$leaderboardId");