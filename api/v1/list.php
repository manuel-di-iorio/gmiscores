<?php
require_once("../../lib/db.php");
require_once("../../models/Score.php");
require_once("../../models/Game.php");
header("Access-Control-Allow-Origin: *");

// Input validation
if ($_SERVER['REQUEST_METHOD'] !== "GET") {
  api_reply_error("Request method not allowed", "MethodNotAllowed", 405);
}

if (!isset($_GET["game"])) {
  api_reply_error("Missing parameters", "ValidationError", 400);
}

$gameId = (int)$_GET["game"];
$leaderboardId = isset($_GET["leaderboard"]) ? (string)$_GET["leaderboard"] : "default";
$page = isset($_GET["page"]) ? max(0, (int)$_GET["page"]) : 0;
$limit = isset($_GET["limit"]) ? max(0, min(1000, (int)$_GET["limit"])) : 10;
$order = isset($_GET["order"]) && strtoupper($_GET["order"]) === "ASC" ? "ASC" : "DESC";
$startTime = isset($_GET["startTime"]) ? $_GET["startTime"] : NULL;
$endTime = isset($_GET["endTime"]) ? $_GET["endTime"] : NULL;
$playerIdOrName = isset($_GET["player"]) ? $_GET["player"] : NULL;
$includePlayer = isset($_GET["includePlayer"]) ? $_GET["includePlayer"] : NULL;

// Retro-compatibility condition
if ($leaderboardId === "0") $leaderboardId = "default";

// Time validation
if (!is_null($startTime)) {
  try {
    new DateTime($startTime);
  } catch (Exception $e) {
    api_reply_error("Parameter 'startTime' is not a valid date", "ValidationError", 400);
  }
}

if (!is_null($endTime)) {
  try {
    new DateTime($endTime);
  } catch (Exception $e) {
    print_r($e->getMessage());
    api_reply_error("Parameter 'endTime' is not a valid date", "ValidationError", 400);
  }
}

// Check if the game exists
$result = Game::getById($gameId);
if (!$result->num_rows) {
  api_reply_error("Game #$gameId does not exists", "NotFoundError", 404);
}

// Get the scores
$result = Score::listSortedByGameId($gameId, $leaderboardId, $page, $limit, $order, $playerIdOrName, $startTime, $endTime);
$scores = [];
while ($row = $result->fetch_assoc()) {
  $scores[] = $row;
}

$resp = [ "status" => 200, "scores" => $scores, "playerScore" => NULL ];

// If specified, include the player best score
if (!is_null($includePlayer)) {
  $result = Score::listSortedByGameId($gameId, $leaderboardId, 0, 1, $order, $includePlayer, $startTime, $endTime);
  if ($result->num_rows) {
    $resp["playerScore"] = $result->fetch_assoc();
  }
  
  // Get the score position 
  if (!is_null($resp["playerScore"])) {
    $resp["playerScore"]["position"] = (int)Score::getRankByScoreId($resp["playerScore"]["score_id"], $gameId);
  }
}

header('Content-Type: application/json');
echo json_encode($resp);
