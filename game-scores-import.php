<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("models/Game.php");
require_once("models/Player.php");
require_once("models/Score.php");
require_once("models/Leaderboard.php");

if (!isset($_GET["id"]) || !isset($_FILES["file"])) {
  header("Location: games.php");
  exit;
}
$gameId = (int)$_GET["id"];
$leaderboardId = isset($_GET["leaderboard_id"]) ? (int)$_GET["leaderboard_id"] : null;
$file = $_FILES["file"];
$userId = $user["id"];

$result = Game::getByIdAndUser($gameId, $userId);
if (!$result->num_rows) {
  header("Location: games.php");
  exit;
}

// Verify leaderboard
if ($leaderboardId) {
  $lb = Leaderboard::getById($leaderboardId);
  if (!$lb || $lb['game_id'] != $gameId) {
    $leaderboardId = null;
  }
}

if (!$file["error"]) {
  if (($file["type"] !== "application/octet-stream" && $file["type"] !== "text/csv" && $file["type"] !== "application/vnd.ms-excel") || substr($file["name"], -4) !== ".csv") {
    header("Location: game-scores.php?id=" . $gameId . "&leaderboard_id=" . $leaderboardId . "&error=InvalidCSV");
    exit;
  }

  try {
    $csv = array_map('str_getcsv', file($file['tmp_name']));
    array_shift($csv);

    $db->begin_transaction();

    foreach ($csv as $line) {
      $columnCount = count($line);
      if ($columnCount > 9) throw new Exception("InvalidCSV");
      $playerName = base64_decode($line[0]);
      $score = $line[1];
      $ip = aes_decrypt($line[2]);
      $country = $line[3];
      $createdAt = $line[4];
      $sign = $columnCount > 5 && !empty($line[5]) ? $line[5] : NULL;
      $tags = $columnCount > 6 ? $line[6] : "default";
      $data = $columnCount > 8 ? $line[8] : NULL;
      $env = $columnCount > 9 && !empty($line[9]) ? $line[9] : 'production';

      Player::create($playerName);
      $player = Player::getByName($playerName)->fetch_assoc();

      Score::create($gameId, $player["player_id"], $score, $ip, $country, $createdAt, $sign, $leaderboardId, $tags, $data, $env);
    }

    if (!$db->commit()) throw new Exception("TransactionCommitFailed");
  } catch (Exception $e) {
    $db->rollback();
    header("Location: game-scores.php?id=" . $gameId . "&leaderboard_id=" . $leaderboardId . "&error=InvalidCSV");
    exit;
  }
}

$redirect = "game-scores.php?id=" . $gameId;
if ($leaderboardId) {
  $redirect .= "&leaderboard_id=" . $leaderboardId;
}
header("Location: $redirect");