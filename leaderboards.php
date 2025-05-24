<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("models/Leaderboard.php");

if (!isset($_GET['game_id']) || !is_numeric($_GET['game_id'])) {
    header("Location: games.php?error=" . urlencode("ID gioco non valido."));
    exit;
}

$game_id = (int)$_GET['game_id'];
$game = Game::getByIdAndUser($game_id, $user['id']);

if (!$game) {
    header("Location: games.php?error=" . urlencode("Gioco non trovato o non autorizzato."));
    exit;
}
$game = $game->fetch_assoc();


$leaderboards = Leaderboard::listByGame($game_id);

$view = "leaderboards";
$pageName = "Classifiche di " . htmlspecialchars($game['name']);
require_once("includes/layout.php");
?>
