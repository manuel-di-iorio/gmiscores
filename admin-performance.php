<?php
error_reporting(0);
ini_set('display_errors', '0');
ob_start();

// AJAX API endpoint — must run before any output
if (isset($_GET['action'])) {
  require_once("lib/db.php");
  require_once("models/QueryAnalyzer.php");

  $isAdmin = isset($user["admin"]) && (int)$user["admin"] === 1;
  if (!$isAdmin) {
    while (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
  }

  while (ob_get_level()) ob_end_clean();
  header('Content-Type: application/json; charset=utf-8');

  switch ($_GET['action']) {
    case 'analyze':
      $results = QueryAnalyzer::analyzeAllQueries();
      $json = json_encode($results);
      if ($json === false) {
        echo json_encode(['error' => 'JSON encode failed: ' . json_last_error_msg()]);
      } else {
        echo $json;
      }
      exit;

    case 'missing_indexes':
      $indexes = QueryAnalyzer::findMissingIndexes();
      echo json_encode($indexes);
      exit;

    case 'apply_index':
      $sql = $_POST['sql'] ?? '';
      if (empty($sql)) {
        echo json_encode(['success' => false, 'message' => 'SQL mancante']);
        exit;
      }
      if (preg_match('/^\s*CREATE\s+INDEX\s+/i', $sql) !== 1) {
        echo json_encode(['success' => false, 'message' => 'Solo CREATE INDEX è consentito']);
        exit;
      }
      $result = QueryAnalyzer::applyIndex($sql);
      echo json_encode($result);
      exit;

    case 'apply_all':
      $indexes = QueryAnalyzer::findMissingIndexes();
      $sqls = array_column($indexes, 'sql');
      $results = QueryAnalyzer::applyAllIndexes($sqls);
      echo json_encode($results);
      exit;

    default:
      echo json_encode(['error' => 'Unknown action']);
      exit;
  }
}

// Page render
require_once("lib/db.php");
require_once("lib/checkSession.php");

$isAdmin = isset($user["admin"]) && (int)$user["admin"] === 1;
if (!$isAdmin) {
  header("Location: /");
  exit;
}

$pageName = "Performance Dashboard";
$backUrl = "admin.php";
$view = "admin-performance";
require_once("includes/layout.php");
