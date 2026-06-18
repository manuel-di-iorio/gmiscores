<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Score.php");

$isAdmin = isset($user["admin"]) && (int)$user["admin"] === 1;
if (!$isAdmin) {
  header("Location: /");
  exit;
}

$scoreId = (int)($_GET["id"] ?? 0);
if ($scoreId > 0) {
  Score::deleteAsAdmin($scoreId);
}

$page = max(0, (int)($_GET["scores_page"] ?? 0));
$search = $_GET["scores_search"] ?? null;
$sortBy = $_GET["scores_sort"] ?? null;
$sortDir = $_GET["scores_dir"] ?? null;
$redirect = "admin.php?tab=scores&scores_page=" . $page;
if (!empty($search)) $redirect .= "&scores_search=" . urlencode($search);
if (!empty($sortBy)) $redirect .= "&scores_sort=" . urlencode($sortBy);
if (!empty($sortDir)) $redirect .= "&scores_dir=" . urlencode($sortDir);
header("Location: $redirect");
exit;
