<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("models/Ban.php");

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

// Get the ban list
$result = Ban::list($gameId);
$records = [];
while ($record = $result->fetch_assoc()) {
  $record["_created_at_pretty"] = date("H:i:s - d/m/Y", strtotime($record["created_at"]));
  $records[] = $record;
}

// Render the layout
$view = "game-bans";
$pageName = "Giocatori bannati su " . htmlspecialchars($game["name"]);
require_once("includes/layout.php");
