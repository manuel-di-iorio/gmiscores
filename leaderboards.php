<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("models/Leaderboard.php");
require_once("includes/table.php");

if (!isset($_GET['game_id']) || !is_numeric($_GET['game_id'])) {
    header("Location: games.php?error=" . urlencode("ID gioco non valido."));
    exit;
}

$game_id = (int)$_GET['game_id'];
require_once("models/Team.php");
$gameResult = Game::getByIdWithAccess($game_id, $user['id']);

if (!$gameResult || !$gameResult->num_rows) {
    header("Location: games.php?error=" . urlencode("Gioco non trovato o non autorizzato."));
    exit;
}
$game = $gameResult->fetch_assoc();

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $lb = Leaderboard::getById($deleteId);
    if ($lb && $lb['game_id'] == $game_id) {
        Leaderboard::delete($deleteId);
    }
    header("Location: leaderboards.php?game_id=$game_id");
    exit;
}

$filters = [
    'name' => isset($_GET['name']) ? trim($_GET['name']) : null,
    'score_min' => isset($_GET['score_min']) ? $_GET['score_min'] : null,
    'score_max' => isset($_GET['score_max']) ? $_GET['score_max'] : null,
];
$leaderboards = Leaderboard::listByGame($game_id, $filters);

// Format date for display
foreach ($leaderboards as &$row) {
    $row["_created_at_pretty"] = date("H:i:s - d/m/Y", strtotime($row["created_at"]));
}
unset($row);

$view = "leaderboards";
$pageName = __('leaderboards_page_title', ['game' => htmlspecialchars($game['name'])]);
require_once("includes/layout.php");
?>