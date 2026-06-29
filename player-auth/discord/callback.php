<?php
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../models/User.php");

session_start();

// Validate OAuth state
if (!isset($_GET["code"]) || !isset($_GET["state"]) || empty($_SESSION["oauth_state"]) || !hash_equals($_SESSION["oauth_state"], $_GET["state"])) {
  header("Location: /");
  exit;
}

unset($_SESSION["oauth_state"]);

$code = $_GET["code"];
$sessionToken = $_SESSION["player_login_session"] ?? '';
unset($_SESSION["player_login_session"]);

// Get tokens
$response = file_get_contents('https://discordapp.com/api/v6/oauth2/token', false, stream_context_create([
  'http' => [
    'method' => 'POST',
    'header' => "Content-type: application/x-www-form-urlencoded",
    'content' => http_build_query([
      'client_id' => $config['discordLoginClientId'],
      'client_secret' => $config['discordLoginClientSecret'],
      'grant_type' => 'authorization_code',
      'code' => $code,
      'redirect_uri' => $config["playerLoginCallback"],
      'scope' => 'identify'
    ])
  ]
]));

if ($response === FALSE) {
  header("Location: /player-auth/discord/login.php?error=GetTokensRequestError");
  exit;
}

$tokens = json_decode($response, true);

// Get user data
$response = file_get_contents('https://discordapp.com/api/v6/users/@me', false, stream_context_create([
  'http' => [
    'method' => 'GET',
    'header' => "Content-type: application/json\r\nAuthorization: Bearer " . $tokens["access_token"]
  ]
]));

if ($response === FALSE) {
  header("Location: /player-auth/discord/login.php?error=GetUserDataRequestError");
  exit;
}

$discordUser = json_decode($response, true);
$discordUserId = $discordUser["id"];

// Upsert user
User::upsert($discordUserId, $discordUser["username"], $discordUser["avatar"]);
$user = User::getByDiscordUserId($discordUserId)->fetch_assoc();

// Link session to user
if (!empty($sessionToken) && strlen($sessionToken) === 64) {
  global $db;
  $stmt = $db->prepare("UPDATE player_login_sessions SET user_id = ? WHERE session_token = ?");
  $stmt->bind_param("is", $user["id"], $sessionToken);
  $stmt->execute();
  $stmt->close();
}

// Also set cookie for web users
$encryptedUser = aes_encrypt(json_encode(["id" => $user["id"]]), true);
setcookie("user", $encryptedUser, time()+60*60*24*365, "/", "", false, true);

session_regenerate_id(true);

header("Location: /player-auth/discord/login.php?session=" . urlencode($sessionToken) . "&done=1");
exit;
