<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/csrf.php");

$isAdmin = isset($user["admin"]) && (int)$user["admin"] === 1;
if (!$isAdmin) {
  header("Location: /");
  exit;
}

csrf_validate_request();

if (!isset($_POST["id"])) {
  header("Location: admin.php");
  exit;
}

User::toggleApproved((int)$_POST["id"]);

$params = ["tab" => "users"];
if (!empty($_POST["search"])) {
  $params["search"] = $_POST["search"];
}
if (!empty($_POST["pending"])) {
  $params["pending"] = $_POST["pending"];
}
if (!empty($_POST["page"])) {
  $params["page"] = (int)$_POST["page"];
}

$query = $params ? "?" . http_build_query($params) : "";
header("Location: admin.php" . $query);
exit;
