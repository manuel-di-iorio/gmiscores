<?php
require_once("../../lib/db.php");
require_once("../../lib/insertScore.php");
require_once("../../lib/rateLimit.php");
require_once("../../models/Game.php");
require_once("../../models/Player.php");
require_once("../../models/Score.php");
require_once("../../models/Ban.php");
require_once("../../models/Leaderboard.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

try {
  if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    api_reply_error("Request method not allowed", "MethodNotAllowed", 405);
  }

  if (!empty($config["maintenance"])) {
    api_reply_error("Service unavailable: maintenance in progress", "MaintenanceMode", 503);
  }

  check_rate_limit('add_score', 10, 60);

  $result = process_score_submission($_POST);

  if (!$result['ok']) {
    api_reply_error($result['error'], $result['code'], $result['status']);
  }

  echo json_encode([
    "status" => 200,
    "score" => $result['data']["score"],
    "scoreAction" => $result['data']["scoreAction"],
    "position" => $result['data']["position"]
  ]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    "status" => 500,
    "message" => $e->getMessage(),
    "file" => $e->getFile(),
    "line" => $e->getLine()
  ]);
}
