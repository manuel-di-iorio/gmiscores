<?php
require_once("../lib/db.php");
require_once("../lib/csrf.php");

header('Content-Type: application/json');

if (!isset($user) || empty($user)) {
  http_response_code(401);
  echo json_encode(['error' => 'Not authenticated']);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'Method not allowed']);
  exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid request body']);
  exit;
}

$csrfToken = $input['csrf_token'] ?? null;
if (!csrf_validate($csrfToken)) {
  http_response_code(403);
  echo json_encode(['error' => 'CSRF token invalid']);
  exit;
}

$action = $input['action'] ?? '';
$userId = $user['id'];

if ($action === 'skip') {
  exec_query("UPDATE users SET tutorial_skipped = 1 WHERE id = ?", ["i", $userId]);
  echo json_encode(['ok' => true]);
  exit;
}

if ($action === 'progress') {
  $step = $input['step'] ?? '';
  if (empty($step)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing step parameter']);
    exit;
  }

  $validSteps = ['welcome', 'create-game', 'add-game-name', 'add-game-auth', 'game-id', 'client-secret', 'leaderboard-tab', 'players-tab', 'analytics', 'api-submit', 'complete', '__complete__'];
  if (!in_array($step, $validSteps)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid step']);
    exit;
  }

  $column = 'tutorial_progress';
  $value = $step;

  if ($step === '__complete__') {
    exec_query("UPDATE users SET tutorial_progress = '__complete__' WHERE id = ?", ["i", $userId]);
  } else {
    exec_query("UPDATE users SET tutorial_progress = ? WHERE id = ?", ["si", $value, $userId]);
  }

  echo json_encode(['ok' => true, 'step' => $step]);
  exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unknown action']);
