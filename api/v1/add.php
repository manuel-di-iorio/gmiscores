<?php
require_once("../../lib/db.php");
require_once("../../lib/insertScore.php");
require_once("../../models/Game.php");
require_once("../../models/Player.php");
require_once("../../models/Score.php");
require_once("../../models/Ban.php");
header("Access-Control-Allow-Origin: *");

// Input validation
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
  api_reply_error("Request method not allowed", "MethodNotAllowed", 405);
}

if (!isset($_POST["game"]) || !isset($_POST["score"]) || !isset($_POST["player"]) || !isset($_POST["hash"])) {
  api_reply_error("Missing parameters", "ValidationError", 400);
}

$gameId = (int)$_POST["game"];
$score = (string)$_POST["score"];
$playerNameEncoded = $_POST["player"];
$playerName = base64_decode($playerNameEncoded);
$clientHash = $_POST["hash"];
$sign = isset($_POST["sign"]) ? $_POST["sign"] : NULL;
$leaderboardId = isset($_POST["leaderboard"]) ? (string)$_POST["leaderboard"] : "default";
$insertMode = isset($_POST["insertMode"]) ? $_POST["insertMode"] : "higher";
$data = isset($_POST["data"]) ? (string)$_POST["data"] : NULL;
$minScore = isset($_POST["minScore"]) ? (float)$_POST["minScore"] : NULL;
$maxScore = isset($_POST["maxScore"]) ? (float)$_POST["maxScore"] : NULL;

// Retro-compatibility condition
if ($leaderboardId === "0") $leaderboardId = "default";

// Insert mode validation
if (!is_null($insertMode) && $insertMode !== "all" && $insertMode !== "higher" && $insertMode !== "lower" 
 && $insertMode !== "sum" && $insertMode !== "multiply" && $insertMode !== "divide" && $insertMode !== "replace") {
  api_reply_error("Invalid parameter 'insertMode'", "ValidationError", 400);
}

// Score format validation
if (!is_numeric($score)) {
  api_reply_error("Invalid parameter 'score'", "ValidationError", 400);
}

// Get the client secret
$result = Game::getClientSecretById($gameId);
if (!$result->num_rows) {
  api_reply_error("Game #$gameId does not exists", "NotFoundError", 404);
}
$clientSecret = $result->fetch_assoc()["client_secret"];

// Hash validation
$salt = "game=$gameId";
if (isset($_POST["leaderboard"])) $salt .= "&leaderboard=$leaderboardId";
$salt .= "&score=$score&player=$playerNameEncoded&hash=$clientHash";
$saltRaw = preg_replace("/&hash=([a-z0-9]+)+/i", "", $salt);
$serverHash = sha1($saltRaw . $clientSecret);

if (!hash_equals($clientHash, $serverHash)) {
  api_reply_error("Invalid hash provided", "InvalidHashError", 401);
}

$score = floatval($score);

// Get the IP and country name
$ip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : "N/A";
$country = isset($_SERVER["HTTP_CF_IPCOUNTRY"]) ?
           Locale::getDisplayRegion('-' . $_SERVER["HTTP_CF_IPCOUNTRY"], 'it') : "N/A";

// Check if the player has been banned for this game
$result = Ban::isBanned($gameId, $playerName, $ip);
if ($result->num_rows) {
  api_reply_error("Not authorized to send scores on this game", "AuthorizationError", 403);
}

// Insert the score
[
  "scoreId" => $scoreId,
  "score" => $score,
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

header('Content-Type: application/json');

echo json_encode([ 
  "status" => 200, 
  "score" => $score,
  "scoreAction" => $scoreAction,
  "position" => intval($position) 
]);
