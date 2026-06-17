<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("models/Score.php");
require_once("models/Leaderboard.php");
require_once("includes/table.php");

if (!isset($_GET["id"])) {
  header("Location: games.php");
  exit;
}

$gameId = (int)$_GET["id"];
$result = Game::getByIdAndUser($gameId, $user["id"]);
if (!$result->num_rows) {
  header("Location: games.php");
  exit;
}
$game = $result->fetch_assoc();

// Require leaderboard_id
if (!isset($_GET["leaderboard_id"]) || !is_numeric($_GET["leaderboard_id"])) {
  header("Location: leaderboards.php?game_id=$gameId");
  exit;
}

$leaderboardId = (int)$_GET["leaderboard_id"];

// Verify leaderboard exists and belongs to this game
$lb = Leaderboard::getById($leaderboardId);
if (!$lb || $lb['game_id'] != $gameId) {
  header("Location: leaderboards.php?game_id=$gameId");
  exit;
}

$page = isset($_GET["page"]) ? (int)$_GET["page"] : 0;

$filters = [
  'player' => isset($_GET['player']) ? trim($_GET['player']) : null,
  'score_min' => isset($_GET['score_min']) ? $_GET['score_min'] : null,
  'score_max' => isset($_GET['score_max']) ? $_GET['score_max'] : null,
  'ip_country' => isset($_GET['ip_country']) ? trim($_GET['ip_country']) : null,
  'tags' => isset($_GET['tags']) ? trim($_GET['tags']) : null,
  'date_from' => isset($_GET['date_from']) ? $_GET['date_from'] : null,
  'date_to' => isset($_GET['date_to']) ? $_GET['date_to'] : null,
  'leaderboard_id' => $leaderboardId,
  'env' => isset($_GET['env']) ? $_GET['env'] : 'production',
];

$scoresCount = Score::countByGame($gameId, $filters)->fetch_assoc()["count"];
$pagesCount = max(1, ceil($scoresCount / 100));
$paginationArrowPrevLink = $page == 0 ? "javascript:;" : "game-scores.php?id=" . $game["game_id"] . "&leaderboard_id=" . $leaderboardId . "&page=" . ($page-1);
$paginationArrowNextLink = $page >= $pagesCount-1 ? "javascript:;" : "game-scores.php?id=" . $game["game_id"] . "&leaderboard_id=" . $leaderboardId . "&page=" . ($page+1);

$sort = isset($_GET["sort"]) ? $_GET["sort"] : "updated_at";

if (isset($_GET["dir"])) {
  $sortOrder = strtolower($_GET["dir"]) === 'desc' ? 'DESC' : 'ASC';
} else {
  $sortOrder = isset($_GET["sortOrder"]) ? ($_GET["sortOrder"] === "1" ? "DESC" : "ASC") : "DESC";
}

$result = Score::listByGame($gameId, $page, $sort, $sortOrder, $filters);
$scores = [];
while ($row = $result->fetch_assoc()) {
  $row["_updated_at_pretty"] = date("H:i:s - d/m/Y", strtotime($row["updated_at"]));
  $scores[] = $row;
}

$view = "game-scores";
$pageName = __('scores_page_title', ['leaderboard' => htmlspecialchars($lb['name']), 'game' => htmlspecialchars($game["name"])]);
$backUrl = "leaderboards.php?game_id=$gameId";
require_once("includes/layout.php");