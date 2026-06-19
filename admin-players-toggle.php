<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/csrf.php");
require_once("models/Player.php");
require_once("models/Ban.php");

$isAdmin = isset($user["admin"]) && (int)$user["admin"] === 1;
if (!$isAdmin) {
  header("Location: /");
  exit;
}

csrf_validate_request();

$playerId = (int)($_POST["id"] ?? 0);
if (!$playerId) {
  header("Location: admin.php");
  exit;
}

$player = Player::getByIdWithScores($playerId);
if (!$player || !$player["top_game_id"]) {
  header("Location: admin.php?tab=players");
  exit;
}

$topGameId = (int)$player["top_game_id"];

$existingBan = Ban::getByPlayerAndGame($playerId, $topGameId);
if ($existingBan && $existingBan->num_rows > 0) {
  Ban::removeByPlayerAndGame($playerId, $topGameId);
} else {
  Ban::add($playerId, $player["username"], null, $topGameId);
}

$params = ["tab" => "players"];
if (!empty($_POST["players_search"])) {
  $params["players_search"] = $_POST["players_search"];
}
if (!empty($_POST["players_page"])) {
  $params["players_page"] = (int)$_POST["players_page"];
}
if (!empty($_POST["players_sort"])) {
  $params["players_sort"] = $_POST["players_sort"];
}
if (!empty($_POST["players_dir"])) {
  $params["players_dir"] = $_POST["players_dir"];
}
if (!empty($_POST["players_banned"])) {
  $params["players_banned"] = $_POST["players_banned"];
}

$query = $params ? "?" . http_build_query($params) : "";
header("Location: admin.php" . $query);
exit;
