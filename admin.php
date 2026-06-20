<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("models/Score.php");
require_once("models/Player.php");
require_once("models/Leaderboard.php");
require_once("models/Ban.php");
require_once("assets/ui-kit/kit.php");

$isAdmin = isset($user["admin"]) && (int)$user["admin"] === 1;
if (!$isAdmin) {
  header("Location: /");
  exit;
}

$activeTab = $_GET["tab"] ?? "users";

// Load only the active tab's data
switch ($activeTab) {
  case 'users':
    $search = $_GET["search"] ?? null;
    $pendingOnly = isset($_GET["pending"]) && $_GET["pending"] === "1";
    $page = (int)($_GET["page"] ?? 0);
    $perPage = 20;
    $users = [];
    $result = User::listAll($search, $pendingOnly, $page, $perPage);
    while ($row = $result->fetch_assoc()) {
      $users[] = $row;
    }
    $totalUsers = User::countAll($search, $pendingOnly);
    $unapprovedCount = User::countUnapproved();
    break;

  case 'players':
    $playersSearch = $_GET["players_search"] ?? null;
    $playersPage = (int)($_GET["players_page"] ?? 0);
    $playersSortBy = $_GET["players_sort"] ?? null;
    $playersSortDir = $_GET["players_dir"] ?? 'DESC';
    $playersBannedOnly = isset($_GET["players_banned"]) && $_GET["players_banned"] === "1";
    $playersPerPage = 20;
    $players = [];
    $playersResult = Player::listAllWithScores($playersSearch, $playersPage, $playersPerPage, $playersSortBy, $playersSortDir, $playersBannedOnly);
    while ($row = $playersResult->fetch_assoc()) {
      $players[] = $row;
    }
    $totalPlayers = Player::countAllWithScores($playersSearch, $playersBannedOnly);
    break;

  case 'scores':
    $scoresSearch = $_GET["scores_search"] ?? null;
    $scoresPage = (int)($_GET["scores_page"] ?? 0);
    $scoresSortBy = $_GET["scores_sort"] ?? 'date';
    $scoresSortDir = $_GET["scores_dir"] ?? 'DESC';
    $scoresPerPage = 50;
    $scores = [];
    $scoresResult = Score::listAllRecent($scoresPage, $scoresPerPage, $scoresSearch, $scoresSortBy, $scoresSortDir);
    while ($row = $scoresResult->fetch_assoc()) {
      $scores[] = $row;
    }
    $totalScores = Score::countAllFiltered($scoresSearch);
    break;

  case 'analytics':
    $globalTotalScores = Score::count();
    $globalTotalGames = Game::count();
    $globalTotalPlayers = Player::count();
    $totalUsers = User::countAll(null, false);
    $globalActiveGames = Score::getActiveGames();
    $globalTopGame = Score::getGameWithMoreScores();
    $globalTopPlayer = Score::getPlayerWithMoreScores();
    $globalCountries = Score::getUniqueCountriesCount();

    $globalScoresOverTime = [];
    $result = Score::getScoresOverTime(30);
    while ($row = $result->fetch_assoc()) {
      $globalScoresOverTime[] = $row;
    }

    $globalScoresByGame = [];
    $result = Score::getScoresByGame();
    while ($row = $result->fetch_assoc()) {
      $globalScoresByGame[] = $row;
    }

    $globalCountriesList = [];
    $result = Score::getCountries();
    while ($row = $result->fetch_assoc()) {
      $globalCountriesList[] = $row;
    }
    break;

  case 'migrate':
    $migrationsDir = __DIR__ . '/migrations';
    $dbTableMigrations = 'migrations';

    exec_query("CREATE TABLE IF NOT EXISTS $dbTableMigrations (
      id INT AUTO_INCREMENT PRIMARY KEY,
      migration VARCHAR(255) NOT NULL UNIQUE,
      applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $applied = [];
    $result = exec_query("SELECT migration, applied_at FROM $dbTableMigrations ORDER BY migration");
    if ($result) {
      while ($row = $result->fetch_assoc()) {
        $applied[$row['migration']] = $row['applied_at'];
      }
    }

    $files = glob($migrationsDir . '/*.php');
    sort($files);

    $migrations = [];
    foreach ($files as $file) {
      $name = basename($file);
      $data = include $file;
      $migrations[] = [
        'file' => $file,
        'name' => $name,
        'description' => is_array($data) && isset($data['description']) ? $data['description'] : 'N/A',
        'sql' => is_array($data) && isset($data['sql']) ? (is_array($data['sql']) ? $data['sql'] : [$data['sql']]) : [],
        'is_applied' => isset($applied[$name]),
      ];
    }

    $run = isset($_POST['run']) && $_POST['run'] === '1';
    $migrateOutput = [];

    if ($run) {
      foreach ($migrations as $m) {
        if ($m['is_applied']) continue;

        $success = true;
        foreach ($m['sql'] as $sql) {
          try {
            exec_query($sql);
          } catch (Exception $e) {
            $migrateOutput[] = "ERROR {$m['name']}: " . $e->getMessage();
            $success = false;
            break;
          }
        }

        if ($success) {
          exec_query("INSERT INTO $dbTableMigrations (migration) VALUES (?)", ["s", $m['name']]);
          $migrateOutput[] = "OK {$m['name']}";
          $applied[$m['name']] = date('Y-m-d H:i:s');
        } else {
          $migrateOutput[] = "FAIL {$m['name']}";
        }
      }

      foreach ($migrations as &$m) {
        $m['is_applied'] = isset($applied[$m['name']]);
      }
      unset($m);
    }

    $pendingMigrateCount = 0;
    foreach ($migrations as $m) {
      if (!$m['is_applied']) $pendingMigrateCount++;
    }
    break;
}

require_once("lib/csrf.php");

// AJAX mode — output only the requested tab's HTML
if (isset($_GET['ajax'])) {
  header('Content-Type: text/html; charset=utf-8');
  require "pages/admin/admin-tab-render.php";
  exit;
}

$view = "admin";
$pageName = "Administration";
require_once("includes/layout.php");
