<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("models/Game.php");
require_once("models/Leaderboard.php");

if (!isset($_GET['game_id']) || !is_numeric($_GET['game_id'])) {
    header("Location: games.php?error=" . urlencode("ID gioco non valido."));
    exit;
}

$game_id = (int)$_GET['game_id'];
$game = Game::getByIdAndUser($game_id, $user['id']);
if (!$game) {
    header("Location: games.php?error=" . urlencode("Gioco non trovato."));
    exit;
}
$game = $game->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    if (empty($name)) {
        $error = "Il nome è obbligatorio.";
    } else {
        $isPrivate = isset($_POST['is_private']) && $_POST['is_private'] === '1';
        Leaderboard::create($game_id, $name, $description ?: null, $user['id'], $isPrivate);
        header("Location: leaderboards.php?game_id=$game_id");
        exit;
    }
}

$view = "add-leaderboard";
$pageName = __('add_lb_page_title', ['game' => htmlspecialchars($game['name'])]);
$backUrl = "leaderboards.php?game_id=$game_id";
require_once("includes/layout.php");
?>