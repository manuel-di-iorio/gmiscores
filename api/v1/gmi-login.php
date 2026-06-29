<?php
require_once("../../lib/db.php");
require_once("../../lib/http.php");
session_start();

// Validate OAuth state parameter (CSRF protection)
if (!isset($_GET["code"]) || !isset($_GET["state"]) || empty($_SESSION["oauth_state"]) || !hash_equals($_SESSION["oauth_state"], $_GET["state"])) {
  header("Location: /");
  exit;
}

unset($_SESSION["oauth_state"]);

$code = $_GET["code"];

$redirectUri = isset($_SESSION["loginGo"]) ? $_SESSION["loginGo"] : "/home.php";

// Get the tokens
$response = httpPost('https://discord.com/api/v10/oauth2/token', http_build_query([
  'client_id' => $config['discordLoginClientId'],
  'client_secret' => $config['discordLoginClientSecret'],
  'grant_type' => 'authorization_code',
  'code' => $code,
  'redirect_uri' => $config["loginCallback"],
  'scope' => 'identify'
]));
if ($response === FALSE) {
  header("Location: /home.php?error=GetTokensRequestError");
  exit;
}
$tokens = json_decode($response, true);

// Get the user data
$response = httpGet('https://discord.com/api/v10/users/@me', $tokens["access_token"]);
if ($response === FALSE) {
  header("Location: /home.php?error=GetUserDataRequestError");
  exit;
}
$discordUser = json_decode($response, true);
$discordUserId = $discordUser["id"];

// Get the user avatar URL
if (isset($discordUser["avatar"])) {
  $discordUser["_avatarUrl"] = "https://cdn.discordapp.com/avatars/" . $discordUser["id"] . "/" . 
                              $discordUser["avatar"] . ".png";
}

// Store the user in the database
User::upsert($discordUserId, $discordUser["username"], $discordUser["avatar"]);
$user = User::getByDiscordUserId($discordUserId)->fetch_assoc();

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

// Store the user in the cookie
$encryptedUser = aes_encrypt(json_encode([ "id" => $user["id"] ]), true);

setcookie("user", $encryptedUser, time()+60*60*24*365, "/", "", false, true);

// Redirect back
header("Location: $redirectUri");
exit;
