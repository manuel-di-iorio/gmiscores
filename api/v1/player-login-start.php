<?php
require_once("../../lib/db.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
  api_reply_error("Request method not allowed", "MethodNotAllowed", 405);
}

$sessionToken = bin2hex(random_bytes(32));

global $db;
$ins = $db->prepare("INSERT INTO player_login_sessions (session_token) VALUES (?)");
$ins->bind_param("s", $sessionToken);
$ins->execute();
$ins->close();

echo json_encode(["status" => 200, "session_token" => $sessionToken]);
