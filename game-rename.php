<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("lib/csrf.php");
require_once("models/Game.php");

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
  api_reply_error("Request method not allowed", "MethodNotAllowed", 405);
}

csrf_validate_request();

if (!isset($_GET["id"]) || !isset($_POST["name"])) {
  api_reply_error("Missing parameters", "ValidationError", 400);
}

$gameId = (int)$_GET["id"];
$name = trim($_POST["name"]);

if (strlen($name) == 0) {
  api_reply_error("Name cannot be empty", "ValidationError", 400);
}

Game::renameWithAccess($gameId, $user["id"], $name);

header('Content-Type: application/json');
echo json_encode(["status" => 200, "message" => "Updated successfully"]);
