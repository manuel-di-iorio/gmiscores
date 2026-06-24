<?php
require_once("../lib/db.php");
require_once("../models/User.php");
require_once("../models/Player.php");
require_once("../models/Ban.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== "GET") {
  api_reply_error("Request method not allowed", "MethodNotAllowed", 405);
}

if (!isset($_GET["token"]) || empty($_GET["token"])) {
  api_reply_error("Missing token parameter", "ValidationError", 400);
}

if (!isset($_GET["game"]) || empty($_GET["game"])) {
  api_reply_error("Missing game parameter", "ValidationError", 400);
}

$token = $_GET["token"];
$gameId = (int)$_GET["game"];

try {
  $tokenData = json_decode(aes_decrypt($token, true), true);
  if (!isset($tokenData["id"])) {
    echo json_encode(["status" => 200, "valid" => false]);
    exit;
  }

  $userResult = User::getById((string)$tokenData["id"]);
  if ($userResult->num_rows) {
    $user = $userResult->fetch_assoc();
    $encryptedToken = aes_encrypt(json_encode(["id" => (int)$user["id"]]), true);
    $approved = (bool)$user["approved"];

    $playerResult = Player::getByUserId((int)$user["id"]);
    $isBanned = false;
    if ($playerResult->num_rows) {
      $player = $playerResult->fetch_assoc();
      $banResult = Ban::getByPlayerAndGame((int)$player["player_id"], $gameId);
      $isBanned = $banResult->num_rows > 0;
    }

    echo json_encode([
      "status" => 200,
      "valid" => true,
      "approved" => $approved,
      "is_banned" => $isBanned,
      "token" => $encryptedToken,
      "username" => $user["username"],
      "user_id" => (int)$user["id"],
    ]);
  } else {
    echo json_encode(["status" => 200, "valid" => false]);
  }
} catch (Exception $e) {
  echo json_encode(["status" => 200, "valid" => false]);
}
