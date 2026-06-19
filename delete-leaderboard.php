<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("lib/csrf.php");
require_once("models/Game.php");
require_once("models/Leaderboard.php");

csrf_validate_request();

if (!isset($_POST['leaderboard_id']) || !is_numeric($_POST['leaderboard_id']) || !isset($_POST['game_id']) || !is_numeric($_POST['game_id'])) {
    header("Location: games.php");
    exit;
}

$leaderboardId = (int)$_POST['leaderboard_id'];
$gameId = (int)$_POST['game_id'];

require_once("models/Team.php");
$gameResult = Game::getByIdWithAccess($gameId, $user['id']);
if (!$gameResult || !$gameResult->num_rows) {
    header("Location: games.php");
    exit;
}

$lb = Leaderboard::getById($leaderboardId);
if (!$lb || $lb['game_id'] != $gameId) {
    header("Location: leaderboards.php?game_id=$gameId&error=" . urlencode("Classifica non trovata."));
    exit;
}

$allLbs = Leaderboard::listByGame($gameId);
if (count($allLbs) <= 1) {
    header("Location: leaderboards.php?game_id=$gameId&error=" . urlencode("Non puoi eliminare l'unica classifica del gioco."));
    exit;
}

Leaderboard::delete($leaderboardId, $gameId);

header("Location: leaderboards.php?game_id=$gameId");
exit;
