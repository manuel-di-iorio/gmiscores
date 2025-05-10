<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");

// Get the games
$result = Game::listByUser($user["id"]);

$games = [];
while ($row = $result->fetch_assoc()) {
  $games[] = $row;
}

// Render the layout
$view = "games";
$pageName = "I tuoi giochi";
require_once("includes/layout.php");
