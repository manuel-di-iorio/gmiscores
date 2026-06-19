<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("models/Team.php");
require_once("models/Score.php");
require_once("models/Leaderboard.php");

if (!isset($_GET["id"])) {
  header("Location: games.php");
  exit;
}

$gameId = (int)$_GET["id"];
$gameResult = Game::getByIdWithAccess($gameId, $user["id"]);
if (!$gameResult || !$gameResult->num_rows) {
  header("Location: games.php");
  exit;
}
$game = $gameResult->fetch_assoc();

$leaderboardId = isset($_GET["leaderboard_id"]) ? (int)$_GET["leaderboard_id"] : null;

// Verify leaderboard
if ($leaderboardId) {
  $lb = Leaderboard::getById($leaderboardId);
  if (!$lb || $lb['game_id'] != $gameId) {
    $leaderboardId = null;
  }
}

$envFilter = isset($_GET["env"]) ? $_GET["env"] : null;

// Handle AJAX export request
if (isset($_POST["action"]) && $_POST["action"] === "export") {
  header('Content-Type: application/json');

  $env = isset($_POST["env"]) ? $_POST["env"] : null;

  $result = Score::getAll($gameId, $user["id"], $env);

  $total = $result->num_rows;

  $rows = [];
  while ($record = $result->fetch_assoc()) {
    $rows[] = [
      base64_encode($record["username"]),
      $record["score"],
      isset($record["ip"]) ? aes_encrypt($record["ip"]) : "",
      $record["ip_country"],
      $record["created_at"],
      $record["sign"],
      $record["tags"],
      $record["leaderboard_id"],
      $record["data"],
      $record["env"]
    ];
  }

  echo json_encode([
    "success" => true,
    "total" => $total,
    "rows" => $rows
  ]);
  exit;
}

$pageName = __('scores_export_page_title', ['game' => htmlspecialchars($game["name"])]);
$backUrl = "game-scores.php?id=" . $gameId . ($leaderboardId ? "&leaderboard_id=" . $leaderboardId : "");
$view = "game-scores-export";
require_once("includes/layout.php");
