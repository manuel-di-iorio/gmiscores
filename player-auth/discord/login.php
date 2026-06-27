<?php
session_start();
require_once(__DIR__ . "/../../lib/db.php");

$session = isset($_GET["session"]) ? preg_replace('/[^a-f0-9]/i', '', $_GET["session"]) : '';
if (empty($session) || strlen($session) !== 64) {
  header("Location: /");
  exit;
}

// Cleanup old sessions (older than 10 minutes)
$cleanup = $db->prepare("DELETE FROM player_login_sessions WHERE created_at < DATE_SUB(NOW(), INTERVAL 10 MINUTE)");
$cleanup->execute();
$cleanup->close();

// Ensure the session row exists in the database
global $db;
$check = $db->prepare("SELECT id FROM player_login_sessions WHERE session_token = ?");
$check->bind_param("s", $session);
$check->execute();
$check->store_result();
if ($check->num_rows === 0) {
  $ins = $db->prepare("INSERT INTO player_login_sessions (session_token) VALUES (?)");
  $ins->bind_param("s", $session);
  $ins->execute();
  $ins->close();
}
$check->close();

// Already logged in via cookie - link session directly
if (isset($user)) {
  if (!isset($_GET["done"])) {
    $stmt = $db->prepare("UPDATE player_login_sessions SET user_id = ? WHERE session_token = ?");
    $stmt->bind_param("is", $user["id"], $session);
    $stmt->execute();
    $stmt->close();
    header("Location: /player-auth/discord/login.php?session=" . urlencode($session) . "&done=1");
    exit;
  }
  // Session already linked and done=1 present - render the success page
  $view = "player-login-sdk";
  $pageName = "Player Login";
  require_once(__DIR__ . "/../../includes/layout-minimal.php");
  exit;
}

$_SESSION["player_login_session"] = $session;

$oauthState = bin2hex(random_bytes(32));
$_SESSION["oauth_state"] = $oauthState;

// Redirect directly to Discord OAuth
$loginRedirectUrl = "https://discordapp.com/api/oauth2/authorize?client_id=" . $config["discordLoginClientId"] . "&response_type=code&scope=identify&redirect_uri=" . urlencode($config["playerLoginCallback"]) . "&state=" . $oauthState;

header("Location: " . $loginRedirectUrl);
exit;
