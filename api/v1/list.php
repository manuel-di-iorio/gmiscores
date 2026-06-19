<?php
require_once("../../lib/db.php");
require_once("../../lib/rateLimit.php");
require_once("../../models/Score.php");
require_once("../../models/Game.php");
require_once("../../models/Leaderboard.php");

header("Access-Control-Allow-Origin: *");

if ($_SERVER['REQUEST_METHOD'] !== "GET") {
  api_reply_error("Request method not allowed", "MethodNotAllowed", 405);
}

if (!isset($_GET["game"])) {
  api_reply_error("Missing parameters", "ValidationError", 400);
}

check_rate_limit('get_scores', 60, 60);

$gameId = (int)$_GET["game"];
$tags = isset($_GET["tags"]) && $_GET["tags"] !== '' ? (string)$_GET["tags"] : NULL;
$page = isset($_GET["page"]) ? max(0, (int)$_GET["page"]) : 0;
$limit = isset($_GET["limit"]) ? max(0, min(1000, (int)$_GET["limit"])) : 10;
$order = isset($_GET["order"]) && strtoupper($_GET["order"]) === "ASC" ? "ASC" : "DESC";
$startTime = isset($_GET["startTime"]) ? $_GET["startTime"] : NULL;
$endTime = isset($_GET["endTime"]) ? $_GET["endTime"] : NULL;
$playerIdOrName = isset($_GET["player"]) ? $_GET["player"] : NULL;
$includePlayer = isset($_GET["includePlayer"]) ? $_GET["includePlayer"] : NULL;
$env = isset($_GET["env"]) ? $_GET["env"] : "production";

// leaderboard_id: INT (new client) or tag string (old client)
$leaderboardId = NULL;

if (isset($_GET["leaderboard_id"])) {
  if (is_numeric($_GET["leaderboard_id"])) {
    // New client: leaderboard_id as INT
    $leaderboardId = (int)$_GET["leaderboard_id"];
    $lb = Leaderboard::getById($leaderboardId);
    if (!$lb || $lb['game_id'] != $gameId) {
      api_reply_error("Invalid leaderboard_id", "ValidationError", 400);
    }
  } else {
    // Old client: leaderboard_id as tag string
    $tags = (string)$_GET["leaderboard_id"];
  }
}

if (!$leaderboardId) {
  $allLbs = Leaderboard::listByGame($gameId);
  if (empty($allLbs)) {
    api_reply_error("No leaderboard found for this game", "NotFoundError", 404);
  }
  $leaderboardId = $allLbs[0]['leaderboard_id'];
}

// Check if leaderboard is private
$lb = Leaderboard::getById($leaderboardId);
if ($lb && $lb['is_private']) {
  $clientHash = isset($_GET["hash"]) ? $_GET["hash"] : '';
  $secretResult = Game::getClientSecretById($gameId);
  if (!$secretResult->num_rows) {
    api_reply_error("Game not found", "NotFoundError", 404);
  }
  $clientSecret = $secretResult->fetch_assoc()["client_secret"];
  $salt = "game=$gameId&leaderboard_id=$leaderboardId";
  $serverHash = sha1($salt . $clientSecret);
  if (!hash_equals($clientHash, $serverHash)) {
    api_reply_error("Access denied: private leaderboard", "AuthorizationError", 403);
  }
}

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
    api_reply_error("Parameter 'endTime' is not a valid date", "ValidationError", 400);
  }
}

$result = Game::getById($gameId);
if (!$result->num_rows) {
  api_reply_error("Game #$gameId does not exists", "NotFoundError", 404);
}

$envFilter = in_array($env, ['test', 'production']) ? $env : ($env === 'all' ? null : 'production');

$result = Score::listSortedByGameId($gameId, $leaderboardId, $page, $limit, $order, $playerIdOrName, $startTime, $endTime, $envFilter);
$scores = [];
while ($row = $result->fetch_assoc()) {
  $scores[] = $row;
}

$resp = [ "status" => 200, "scores" => $scores, "playerScore" => NULL ];

if (!is_null($includePlayer)) {
  $result = Score::listSortedByGameId($gameId, $leaderboardId, 0, 1, $order, $includePlayer, $startTime, $endTime, $envFilter);
  if ($result->num_rows) {
    $resp["playerScore"] = $result->fetch_assoc();
  }
  
  if (!is_null($resp["playerScore"])) {
    $resp["playerScore"]["position"] = (int)Score::getRankByScoreId($resp["playerScore"]["score_id"], $gameId);
  }
}

header('Content-Type: application/json');
echo json_encode($resp);
