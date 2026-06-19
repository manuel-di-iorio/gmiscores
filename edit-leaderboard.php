<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("models/Game.php");
require_once("models/Leaderboard.php");

if (!isset($_GET['leaderboard_id']) || !is_numeric($_GET['leaderboard_id'])) {
    header("Location: games.php?error=" . urlencode("ID classifica non valido."));
    exit;
}

$lb_id = (int)$_GET['leaderboard_id'];
$lb = Leaderboard::getById($lb_id);
if (!$lb) {
    header("Location: games.php?error=" . urlencode("Classifica non trovata."));
    exit;
}

require_once("models/Team.php");
$gameResult = Game::getByIdWithAccess($lb['game_id'], $user['id']);
if (!$gameResult || !$gameResult->num_rows) {
    header("Location: games.php?error=" . urlencode("Non autorizzato."));
    exit;
}
$game = $gameResult->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    if (empty($name)) {
        $error = "Il nome è obbligatorio.";
    } else {
        $isPrivate = isset($_POST['is_private']) && $_POST['is_private'] === '1';
        Leaderboard::update($lb_id, $name, $description ?: null, $lb['game_id'], $isPrivate);
        header("Location: leaderboards.php?game_id=" . $lb['game_id']);
        exit;
    }
}

$view = "edit-leaderboard";
$pageName = __('edit_lb_page_title', ['game' => htmlspecialchars($game['name'])]);
$backUrl = "leaderboards.php?game_id=" . $lb['game_id'];
require_once("includes/layout.php");
?>