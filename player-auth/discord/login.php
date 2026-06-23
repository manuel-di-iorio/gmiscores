<?php
session_start();
require_once(__DIR__ . "/../../lib/db.php");

$session = isset($_GET["session"]) ? preg_replace('/[^a-f0-9]/i', '', $_GET["session"]) : '';
if (empty($session) || strlen($session) !== 64) {
  header("Location: /");
  exit;
}

// Already logged in via cookie — link session directly
if (isset($user)) {
  global $db;
  $stmt = $db->prepare("UPDATE player_login_sessions SET user_id = ? WHERE session_token = ?");
  $stmt->bind_param("is", $user["id"], $session);
  $stmt->execute();
  $stmt->close();
  header("Location: /player-auth/discord/login.php?session=" . urlencode($session) . "&done=1");
  exit;
}

$_SESSION["player_login_session"] = $session;

$oauthState = bin2hex(random_bytes(32));
$_SESSION["oauth_state"] = $oauthState;

$loginRedirectUrl = "https://discordapp.com/api/oauth2/authorize?client_id=" . $config["discordLoginClientId"] . "&response_type=code&scope=identify&redirect_uri=" . urlencode($config["playerLoginCallback"]) . "&state=" . $oauthState;

$view = "player-login-sdk";
$pageName = "Player Login";
require_once(__DIR__ . "/../../includes/layout-minimal.php");
