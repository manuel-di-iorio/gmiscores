<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Score.php");
require_once("models/Ban.php");

$isAdmin = isset($user["admin"]) && (int)$user["admin"] === 1;
if (!$isAdmin) {
  header("Location: /");
  exit;
}

$scoreId = (int)($_GET["id"] ?? 0);
$page = max(0, (int)($_GET["scores_page"] ?? 0));
$search = $_GET["scores_search"] ?? null;
$sortBy = $_GET["scores_sort"] ?? null;
$sortDir = $_GET["scores_dir"] ?? null;
$redirect = "admin.php?tab=scores&scores_page=" . $page;
if (!empty($search)) $redirect .= "&scores_search=" . urlencode($search);
if (!empty($sortBy)) $redirect .= "&scores_sort=" . urlencode($sortBy);
if (!empty($sortDir)) $redirect .= "&scores_dir=" . urlencode($sortDir);

if ($scoreId <= 0) {
  header("Location: $redirect");
  exit;
}

$score = Score::getByIdForAdmin($scoreId);
if (!$score) {
  header("Location: $redirect");
  exit;
}

try {
  $db->begin_transaction();

  Score::deleteByPlayerAndGame((int)$score["player_id"], (int)$score["game_id"]);
  Ban::add((int)$score["player_id"], $score["username"], $score["ip"], (int)$score["game_id"]);

  if (!$db->commit()) throw new Exception("TransactionCommitFailed");
} catch (Exception $e) {
  $db->rollback();
  header("Location: $redirect&error=" . urlencode($e->getMessage()));
  exit;
}

header("Location: $redirect");
exit;
