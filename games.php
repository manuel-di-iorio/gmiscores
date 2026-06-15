<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("includes/table.php");

// Get the games
$nameFilter = isset($_GET['name']) ? trim($_GET['name']) : null;
$result = Game::listByUser($user["id"], $nameFilter);

$games = [];
while ($row = $result->fetch_assoc()) {
  $games[] = $row;
}

// Render the layout
$view = "games";
$pageName = "I tuoi giochi";
require_once("includes/layout.php");
