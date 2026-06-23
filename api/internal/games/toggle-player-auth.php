<?php
require_once("../../../lib/db.php");
require_once("../../../lib/csrf.php");
require_once("../../../models/Game.php");

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
  api_reply_error("Request method not allowed", "MethodNotAllowed", 405);
}

csrf_validate_request();

if (!isset($_POST["id"]) || !isset($_POST["enabled"])) {
  api_reply_error("Missing parameters", "ValidationError", 400);
}

$gameId = (int)$_POST["id"];
$enabled = $_POST["enabled"] === "1";
$name = isset($_POST["name"]) ? trim($_POST["name"]) : null;

if (!isset($user)) {
  api_reply_error("Not authenticated", "AuthenticationError", 401);
}

global $db, $dbTableGames;

if ($name !== null && strlen($name) > 0) {
  $sql = "UPDATE $dbTableGames SET require_player_auth = ?, name = ? WHERE game_id = ? AND user_id = ?";
  exec_query($sql, ["isii", $enabled ? 1 : 0, $name, $gameId, $user["id"]]);
} else {
  $sql = "UPDATE $dbTableGames SET require_player_auth = ? WHERE game_id = ? AND user_id = ?";
  exec_query($sql, ["iii", $enabled ? 1 : 0, $gameId, $user["id"]]);
}

echo json_encode(["status" => 200, "message" => "Updated successfully"]);
