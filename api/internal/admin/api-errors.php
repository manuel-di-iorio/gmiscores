<?php
require_once("../../../lib/db.php");
require_once("../../../lib/csrf.php");

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== "GET") {
  api_reply_error("Request method not allowed", "MethodNotAllowed", 405);
}

csrf_validate_request();

if (!isset($user) || !isset($user["admin"]) || (int)$user["admin"] !== 1) {
  api_reply_error("Not authenticated", "AuthenticationError", 401);
}

global $db;

$page = isset($_GET["page"]) ? max(0, (int)$_GET["page"]) : 0;
$perPage = 50;

$offset = $page * $perPage;

$countResult = $db->query("SELECT COUNT(*) AS cnt FROM api_errors");
$total = $countResult->fetch_assoc()["cnt"];

$result = $db->query("SELECT id, error_code, message, status, endpoint, method, ip, game_id, request_data, created_at FROM api_errors ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");

$errors = [];
while ($row = $result->fetch_assoc()) {
  $errors[] = $row;
}

echo json_encode([
  "status" => 200,
  "errors" => $errors,
  "total" => (int)$total,
  "page" => $page,
  "perPage" => $perPage,
]);
