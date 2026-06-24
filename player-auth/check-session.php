<?php
require_once("../lib/db.php");
require_once("../models/User.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== "GET") {
  api_reply_error("Request method not allowed", "MethodNotAllowed", 405);
}

if (!isset($_GET["session"]) || empty($_GET["session"])) {
  api_reply_error("Missing session parameter", "ValidationError", 400);
}

$sessionToken = preg_replace('/[^a-f0-9]/i', '', $_GET["session"]);
if (strlen($sessionToken) !== 64) {
  api_reply_error("Invalid session token", "ValidationError", 400);
}

global $db;

// Check if session already completed
$result = exec_query("SELECT user_id FROM player_login_sessions WHERE session_token = ?", ["s", $sessionToken]);

if ($result->num_rows) {
  $row = $result->fetch_assoc();
  if ($row["user_id"]) {
    // Login completed - return token + username
    $encryptedToken = aes_encrypt(json_encode(["id" => (int)$row["user_id"]]), true);
    $userResult = User::getById((string)$row["user_id"]);
    $username = "";
    if ($userResult->num_rows) {
      $username = $userResult->fetch_assoc()["username"];
    }
    // Clean up
    exec_query("DELETE FROM player_login_sessions WHERE session_token = ?", ["s", $sessionToken]);
    echo json_encode(["status" => 200, "logged" => true, "token" => $encryptedToken, "username" => $username, "user_id" => (int)$row["user_id"]]);
    exit;
  }
}

// Not yet completed
echo json_encode(["status" => 200, "logged" => false]);
