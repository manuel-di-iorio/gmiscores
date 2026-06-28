<?php
require_once("../../lib/db.php");
require_once("../../lib/insertScore.php");
require_once("../../lib/rateLimit.php");
require_once("../../lib/syncHandlers.php");
require_once("../../models/SyncOperation.php");
require_once("../../models/Game.php");
require_once("../../models/Player.php");
require_once("../../models/Score.php");
require_once("../../models/Ban.php");
require_once("../../models/Leaderboard.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
  api_reply_error("Request method not allowed", "MethodNotAllowed", 405);
}

if (!empty($config["maintenance"])) {
  api_reply_error("Service unavailable: maintenance in progress", "MaintenanceMode", 503);
}

check_rate_limit('sync_batch', 30, 60);

$rawBody = file_get_contents("php://input");
$body = json_decode($rawBody, true);

if (!is_array($body) || !isset($body["operations"]) || !is_array($body["operations"])) {
  api_reply_error("Invalid request body", "ValidationError", 400);
}

$operations = $body["operations"];

if (count($operations) > 20) {
  api_reply_error("Max 20 operations per request", "ValidationError", 400);
}

$results = [];

foreach ($operations as $op) {
  $opId = $op["op_id"] ?? '';
  $type = $op["type"] ?? '';
  $payload = $op["payload"] ?? [];

  if (empty($opId) || empty($type)) {
    $results[] = ["op_id" => $opId, "status" => "failed", "error" => "Missing op_id or type"];
    continue;
  }

  if (!isset($syncHandlers[$type])) {
    $results[] = ["op_id" => $opId, "status" => "failed", "error" => "UnknownOperationType"];
    continue;
  }

  $gameId = isset($payload["game"]) ? (int)$payload["game"] : 0;
  if ($gameId <= 0) {
    $results[] = ["op_id" => $opId, "status" => "failed", "error" => "Invalid game_id"];
    continue;
  }

  $existing = SyncOperation::findByOpId($gameId, $opId);
  if ($existing && $existing["status"] === "applied") {
    $cachedResult = $existing["result"] ? json_decode($existing["result"], true) : null;
    $results[] = array_merge(["op_id" => $opId, "status" => "duplicate"], $cachedResult ?: []);
    continue;
  }

  $handlerName = $syncHandlers[$type];
  $handlerResult = $handlerName($payload);

  $playerId = null;
  if (isset($payload["token"])) {
    try {
      $tokenData = json_decode(aes_decrypt($payload["token"], true), true);
      if (isset($tokenData["id"])) {
        $userResult = User::getById($tokenData["id"]);
        if ($userResult->num_rows) {
          $loggedUser = $userResult->fetch_assoc();
          $playerObj = Player::getByUserId((int)$loggedUser["id"]);
          if ($playerObj->num_rows) {
            $player = $playerObj->fetch_assoc();
            $playerId = (int)$player["player_id"];
          }
        }
      }
    } catch (Exception $e) {}
  }

  if ($handlerResult['ok']) {
    SyncOperation::record($gameId, $opId, $playerId, $type, 'applied', json_encode($handlerResult['data']));
    $results[] = array_merge(["op_id" => $opId, "status" => "applied"], $handlerResult['data']);
  } else {
    SyncOperation::record($gameId, $opId, $playerId, $type, 'failed', json_encode(["error" => $handlerResult['error']]));
    $results[] = ["op_id" => $opId, "status" => "failed", "error" => $handlerResult['error']];
  }
}

echo json_encode(["status" => 200, "results" => $results]);
