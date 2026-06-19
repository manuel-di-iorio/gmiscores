<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("models/Team.php");
require_once("models/Player.php");
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

// Handle AJAX parse request (validate CSV and show preview)
if (isset($_POST["action"]) && $_POST["action"] === "parse") {
  header('Content-Type: application/json');

  if (!isset($_FILES["file"])) {
    echo json_encode(["success" => false, "error" => __('scores_import_error_no_file')]);
    exit;
  }

  $file = $_FILES["file"];

  if ($file["error"]) {
    echo json_encode(["success" => false, "error" => __('scores_import_error_upload')]);
    exit;
  }

  if (($file["type"] !== "application/octet-stream" && $file["type"] !== "text/csv" && $file["type"] !== "application/vnd.ms-excel") || substr($file["name"], -4) !== ".csv") {
    echo json_encode(["success" => false, "error" => __('scores_import_error_invalid_csv')]);
    exit;
  }

  try {
    $csv = array_map('str_getcsv', file($file['tmp_name']));
    if (count($csv) < 2) {
      echo json_encode(["success" => false, "error" => __('scores_import_error_empty_csv')]);
      exit;
    }

    array_shift($csv); // Remove header

    $preview = [];
    $errors = [];
    $validCount = 0;
    $totalRows = count($csv);

    foreach ($csv as $idx => $line) {
      $rowNum = $idx + 2; // +2 because we removed header and 1-indexed
      $columnCount = count($line);

      if ($columnCount > 10) {
        $errors[] = ['row' => $rowNum, 'error' => __('scores_import_error_too_many_columns')];
        continue;
      }

      $playerName = base64_decode($line[0]);
      $score = $line[1];
      $ip = aes_decrypt($line[2]);

      if (empty($playerName)) {
        $errors[] = ['row' => $rowNum, 'error' => __('scores_import_error_empty_player')];
        continue;
      }

      if ($score === '' || !is_numeric($score)) {
        $errors[] = ['row' => $rowNum, 'error' => __('scores_import_error_invalid_score')];
        continue;
      }

      $validCount++;

      if (count($preview) < 10) {
        $preview[] = [
          'row' => $rowNum,
          'player' => $playerName,
          'score' => $score,
          'country' => $line[3] ?? '',
          'date' => $line[4] ?? '',
        ];
      }
    }

    // Save temp file for import
    $tmpFile = tempnam(sys_get_temp_dir(), 'gmi_import_');
    move_uploaded_file($file['tmp_name'], $tmpFile);

    echo json_encode([
      "success" => true,
      "total" => $totalRows,
      "valid" => $validCount,
      "errors_count" => count($errors),
      "preview" => $preview,
      "errors" => array_slice($errors, 0, 20),
      "tmp_file" => basename($tmpFile)
    ]);
  } catch (Exception $e) {
    echo json_encode(["success" => false, "error" => __('scores_import_error_parse')]);
  }
  exit;
}

// Handle AJAX import request (execute the import)
if (isset($_POST["action"]) && $_POST["action"] === "import") {
  header('Content-Type: application/json');

  $tmpFileBasename = isset($_POST["tmp_file"]) ? $_POST["tmp_file"] : '';
  $tmpFile = sys_get_temp_dir() . '/' . $tmpFileBasename;

  if (!file_exists($tmpFile)) {
    echo json_encode(["success" => false, "error" => __('scores_import_error_temp_missing')]);
    exit;
  }

  try {
    $csv = array_map('str_getcsv', file($tmpFile));
    array_shift($csv); // Remove header

    $db->begin_transaction();

    $imported = 0;
    $skipped = 0;
    $errors = [];
    $total = count($csv);

    foreach ($csv as $idx => $line) {
      $rowNum = $idx + 2;
      $columnCount = count($line);

      if ($columnCount > 10) {
        $skipped++;
        continue;
      }

      $playerName = base64_decode($line[0]);
      $score = $line[1];
      $ip = aes_decrypt($line[2]);
      $country = $line[3];
      $createdAt = $line[4];
      $sign = $columnCount > 5 && !empty($line[5]) ? $line[5] : NULL;
      $tags = $columnCount > 6 ? $line[6] : "default";
      $data = $columnCount > 8 ? $line[8] : NULL;
      $env = $columnCount > 9 && !empty($line[9]) ? $line[9] : 'production';

      if (empty($playerName) || $score === '' || !is_numeric($score)) {
        $skipped++;
        continue;
      }

      try {
        Player::create($playerName);
        $player = Player::getByName($playerName)->fetch_assoc();
        Score::create($gameId, $player["player_id"], $score, $ip, $country, $createdAt, $sign, $leaderboardId, $tags, $data, $env);
        $imported++;
      } catch (Exception $e) {
        $skipped++;
        $errors[] = ['row' => $rowNum, 'error' => $e->getMessage()];
      }
    }

    if (!$db->commit()) throw new Exception("TransactionCommitFailed");

    @unlink($tmpFile);

    echo json_encode([
      "success" => true,
      "total" => $total,
      "imported" => $imported,
      "skipped" => $skipped,
      "errors" => array_slice($errors, 0, 20)
    ]);
  } catch (Exception $e) {
    $db->rollback();
    @unlink($tmpFile);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
  }
  exit;
}

$pageName = __('scores_import_page_title', ['game' => htmlspecialchars($game["name"])]);
$backUrl = "game-scores.php?id=" . $gameId . ($leaderboardId ? "&leaderboard_id=" . $leaderboardId : "");
$view = "game-scores-import";
require_once("includes/layout.php");
