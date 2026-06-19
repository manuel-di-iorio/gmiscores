<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("lib/csrf.php");
require_once("models/Score.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: games.php");
  exit;
}

csrf_validate_request();

$input = json_decode(file_get_contents('php://input'), true);
$scoreIds = isset($input['score_ids']) ? $input['score_ids'] : [];
$gameId = isset($input['game_id']) ? (int)$input['game_id'] : 0;
$leaderboardId = isset($input['leaderboard_id']) ? (int)$input['leaderboard_id'] : 0;

if (empty($scoreIds) || !$gameId) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => 'Parametri mancanti']);
  exit;
}

Score::deleteBatch($scoreIds, $user['id']);

echo json_encode(['success' => true]);
