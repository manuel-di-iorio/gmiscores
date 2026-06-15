<?php
require_once("../../lib/db.php");
require_once("../../lib/insertScore.php");
require_once("../../models/Game.php");
require_once("../../models/Player.php");
require_once("../../models/Score.php");
require_once("../../models/Ban.php");
require_once("../../models/Leaderboard.php");
header("Access-Control-Allow-Origin: *");

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
  api_reply_error("Request method not allowed", "MethodNotAllowed", 405);
}

if (!isset($_POST["game"]) || !isset($_POST["score"]) || !isset($_POST["player"]) || !isset($_POST["hash"]) || !isset($_POST["leaderboard_id"])) {
  api_reply_error("Missing parameters", "ValidationError", 400);
}

$gameId = (int)$_POST["game"];
$score = (string)$_POST["score"];
$playerNameEncoded = $_POST["player"];
$playerName = base64_decode($playerNameEncoded);
$clientHash = $_POST["hash"];
$sign = isset($_POST["sign"]) ? $_POST["sign"] : NULL;
$leaderboardId = (int)$_POST["leaderboard_id"];
$tags = isset($_POST["tags"]) ? (string)$_POST["tags"] : "default";
$insertMode = isset($_POST["insertMode"]) ? $_POST["insertMode"] : "higher";
$data = isset($_POST["data"]) ? (string)$_POST["data"] : NULL;
$minScore = isset($_POST["minScore"]) ? (float)$_POST["minScore"] : NULL;
$maxScore = isset($_POST["maxScore"]) ? (float)$_POST["maxScore"] : NULL;

if ($tags === "0") $tags = "default";

if (!is_null($insertMode) && $insertMode !== "all" && $insertMode !== "higher" && $insertMode !== "lower" 
 && $insertMode !== "sum" && $insertMode !== "multiply" && $insertMode !== "divide" && $insertMode !== "replace") {
  api_reply_error("Invalid parameter 'insertMode'", "ValidationError", 400);
}

if (!is_numeric($score)) {
  api_reply_error("Invalid parameter 'score'", "ValidationError", 400);
}

// Verify leaderboard exists and belongs to this game
$lb = Leaderboard::getById($leaderboardId);
if (!$lb || $lb['game_id'] != $gameId) {
  api_reply_error("Invalid leaderboard_id", "ValidationError", 400);
}

$result = Game::getClientSecretById($gameId);
if (!$result->num_rows) {
  api_reply_error("Game #$gameId does not exists", "NotFoundError", 404);
}
$clientSecret = $result->fetch_assoc()["client_secret"];

// Hash validation
$salt = "game=$gameId";
$salt .= "&leaderboard_id=$leaderboardId";
if (isset($_POST["tags"])) $salt .= "&tags=$tags";
$salt .= "&score=$score&player=$playerNameEncoded&hash=$clientHash";
$saltRaw = preg_replace("/&hash=([a-z0-9]+)+/i", "", $salt);
$serverHash = sha1($saltRaw . $clientSecret);

if (!hash_equals($clientHash, $serverHash)) {
  api_reply_error("Invalid hash provided", "InvalidHashError", 401);
}

$score = floatval($score);

$ip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : "N/A";
$country = isset($_SERVER["HTTP_CF_IPCOUNTRY"]) ?
           Locale::getDisplayRegion('-' . $_SERVER["HTTP_CF_IPCOUNTRY"], 'it') : "N/A";

$result = Ban::isBanned($gameId, $playerName, $ip);
if ($result->num_rows) {
  api_reply_error("Not authorized to send scores on this game", "AuthorizationError", 403);
}

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
  "tags" => $tags,
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