<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");

$isAdmin = isset($user["admin"]) && (int)$user["admin"] === 1;
if (!$isAdmin) {
  header("Location: /");
  exit;
}

if (!isset($_GET["id"])) {
  header("Location: admin.php");
  exit;
}

User::toggleApproved((int)$_GET["id"]);

$params = ["tab" => "users"];
if (!empty($_GET["search"])) {
  $params["search"] = $_GET["search"];
}
if (!empty($_GET["pending"])) {
  $params["pending"] = $_GET["pending"];
}
if (!empty($_GET["page"])) {
  $params["page"] = (int)$_GET["page"];
}

$query = $params ? "?" . http_build_query($params) : "";
header("Location: admin.php" . $query);
exit;
