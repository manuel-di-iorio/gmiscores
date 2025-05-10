<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("models/Score.php");

// Get the game data
if (!isset($_GET["id"])) {
  header("Location: games.php");
}

$gameId = (int)$_GET["id"];
$result = Game::getByIdAndUser($gameId, $user["id"]);
if (!$result->num_rows) {
  header("Location: games.php");
}
$game = $result->fetch_assoc();

// Get the pages
$page = isset($_GET["page"]) ? (int)$_GET["page"] : 0;
$scoresCount = Score::countByGame($gameId)->fetch_assoc()["count"];
$pagesCount = max(1, ceil($scoresCount / 100));
$paginationArrowPrevLink = $page == 0 ? "javascript:;" : "game-scores.php?id=" . $game["game_id"] . "&page=" . ($page-1);
$paginationArrowNextLink = $page >= $pagesCount-1 ? "javascript:;" : "game-scores.php?id=" . $game["game_id"] . "&page=" . ($page+1);

$sort = isset($_GET["sort"]) ? $_GET["sort"] : "updated_at";
$sortOrder = isset($_GET["sortOrder"]) ? ($_GET["sortOrder"] === "1" ? "DESC" : "ASC") : "DESC";

// Get the scores
$result = Score::listByGame($gameId, $page, $sort, $sortOrder);
$scores = [];
while ($row = $result->fetch_assoc()) {
  $row["_updated_at_pretty"] = date("H:i:s - d/m/Y", strtotime($row["updated_at"]));
  $scores[] = $row;
}

// Render the layout
$view = "game-scores";
$pageName = "Punteggi di " . htmlspecialchars($game["name"]);
require_once("includes/layout.php");
