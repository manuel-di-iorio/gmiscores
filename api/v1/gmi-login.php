<?php
require_once("../../lib/db.php");
session_start();

if (!isset($_GET["code"])) {
  header("Location: /");
}
$code = $_GET["code"];

$redirectUri = isset($_SESSION["loginGo"]) ? $_SESSION["loginGo"] : "/";

// Get the tokens
$response = file_get_contents('https://discordapp.com/api/v6/oauth2/token', false, stream_context_create([
  'http' => [
      'method' => 'POST',
      'header'  => "Content-type: application/x-www-form-urlencoded",
      'content' => http_build_query([
          'client_id' => $config['discordLoginClientId'], //'706202409848012830',
          'client_secret' => $config['discordLoginClientSecret'], // 'xG1ESfyY1jirFwawuWkIczYTUrNbGzGV',
          'grant_type' => 'authorization_code',
          'code' => $code,
          'redirect_uri' => $config["loginCallback"],
          'scope' => 'identify'
      ])
  ]
]));
if ($response === FALSE) {
  header("Location: /?error=GetTokensRequestError");
  exit;
}
$tokens = json_decode($response, true);

// Get the user data
$response = file_get_contents('https://discordapp.com/api/v6/users/@me', false, stream_context_create([
  'http' => [
      'method' => 'GET',
      'header'  => "Content-type: application/json\r\n" . 
      "Authorization: Bearer " . $tokens["access_token"] . "\r\n"
  ]
]));
if ($response === FALSE) {
  header("Location: $redirectUri?error=GetUserDataRequestError");
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

// Store the user in the cookie
$encryptedUser = aes_encrypt(json_encode([ "id" => $user["id"] ]), true);

setcookie("user", $encryptedUser, [
  "expires" => time()+60*60*24*365,
  "domain" => $config["cookieDomain"],
  "path" => "/", 
  "secure" => $config["httpsRedirect"] === "true",
  "httponly" => true,
  "sameSite" => "Lax"
]);

// Redirect back
header("Location: $redirectUri");
