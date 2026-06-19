<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("models/Team.php");
require_once("models/Ban.php");

// Get the game data
if (!isset($_GET["id"])) {
  header("Location: games.php");
}
$gameId = (int)$_GET["id"];
$result = Game::getByIdWithAccess($gameId, $user["id"]);
if (!$result || !$result->num_rows) {
  header("Location: games.php");
}
$game = $result->fetch_assoc();

// Get the ban list (optional filter by player name)
$playerFilter = isset($_GET['player']) ? trim($_GET['player']) : null;
$result = Ban::list($gameId, $playerFilter);
$records = [];
while ($record = $result->fetch_assoc()) {
  $record["_created_at_pretty"] = date("H:i:s - d/m/Y", strtotime($record["created_at"]));
  $records[] = $record;
}

// Render the layout
$view = "game-bans";
$pageName = __('bans_page_title', ['game' => htmlspecialchars($game["name"])]);
require_once("includes/layout.php");
