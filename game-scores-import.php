<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("models/Player.php");
require_once("models/Score.php");

if (!isset($_GET["id"]) || !isset($_FILES["file"])) {
  header("Location: games.php");
  exit;
}
$gameId = (int)$_GET["id"];
$file = $_FILES["file"];
$userId = $user["id"];

// Check that the user owns the game
$result = Game::getByIdAndUser($gameId, $userId);
if (!$result->num_rows) {
  header("Location: games.php");
  exit;
}

// Read the file
if (!$file["error"]) {
  // Check if the file is a CSV
  if (($file["type"] !== "application/octet-stream" && $file["type"] !== "text/csv" && $file["type"] !== "application/vnd.ms-excel") || substr($file["name"], -4) !== ".csv") {
    header("Location: game-scores.php?id=" . $gameId . "&error=InvalidCSV");
    exit;
  }

  // Parse the CSV into an array
  try {
    $csv = array_map('str_getcsv', file($file['tmp_name']));
    array_shift($csv);

    $db->begin_transaction();

    foreach ($csv as $line) {
      $columnCount = count($line);
      if ($columnCount > 8) throw new Exception("InvalidCSV");
      $playerName = base64_decode($line[0]);
      $score = $line[1];
      $ip = aes_decrypt($line[2]);
      $country = $line[3];
      $createdAt = $line[4];
      $sign = $columnCount > 5 && !empty($line[5]) ? $line[5] : NULL;
      $leaderboardId = $columnCount > 6 ? $line[6] : "default";
      $data = $columnCount > 7 ? $line[7] : NULL;

      // Create the player if not exists
      Player::create($playerName);
      $player = Player::getByName($playerName)->fetch_assoc();

      /* Insert the score */
      Score::create($gameId, $player["player_id"], $score, $ip, $country, $createdAt, $sign, $leaderboardId, $data);
    }

    if (!$db->commit()) throw new Exception("TransactionCommitFailed");
  } catch (Exception $e) {
    $db->rollback();
    header("Location: game-scores.php?id=" . $gameId . "&error=InvalidCSV");
    exit;
  }
}

// Redirect back
header("Location: game-scores.php?id=" . $gameId);
