<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("models/Leaderboard.php");

if (!isset($_GET['leaderboard_id']) || !is_numeric($_GET['leaderboard_id']) || !isset($_GET['game_id']) || !is_numeric($_GET['game_id'])) {
    header("Location: games.php");
    exit;
}

$leaderboardId = (int)$_GET['leaderboard_id'];
$gameId = (int)$_GET['game_id'];

// Verify ownership
$game = Game::getByIdAndUser($gameId, $user['id']);
if (!$game) {
    header("Location: games.php");
    exit;
}

$lb = Leaderboard::getById($leaderboardId);
if (!$lb || $lb['game_id'] != $gameId) {
    header("Location: leaderboards.php?game_id=$gameId&error=" . urlencode("Classifica non trovata."));
    exit;
}

Leaderboard::delete($leaderboardId);

header("Location: leaderboards.php?game_id=$gameId");
exit;
?>