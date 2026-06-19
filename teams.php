<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Team.php");

$userId = $user["id"];
$teams = [];
$result = Team::listByUser($userId);
while ($row = $result->fetch_assoc()) {
  $teams[] = $row;
}

$view = "teams";
$pageName = __('teams_title');
require_once("includes/layout.php");
