<?php
require_once("lib/db.php");
session_start();

$view = "login";
$pageName = __("nav_login");
$pageDesc = __("login_title");

// Generate CSRF state for OAuth
$oauthState = bin2hex(random_bytes(32));
$_SESSION["oauth_state"] = $oauthState;

$loginRedirectUrl = "https://discord.com/api/oauth2/authorize?client_id=" . $config["discordLoginClientId"] . "&response_type=code&scope=identify&redirect_uri=" . urlencode($config["loginCallback"]) . "&state=" . $oauthState;

if (isset($_GET["go"])) {
  $go = $_GET["go"];
  // Only allow internal relative paths (no protocol-relative URLs, no external domains)
  if (strlen($go) > 0 && $go[0] === "/" && (strlen($go) < 2 || $go[1] !== "/")) {
    $_SESSION["loginGo"] = str_replace(["\r", "\n"], "", $go);
  } else {
    unset($_SESSION["loginGo"]);
  }
} else {
  unset($_SESSION["loginGo"]);
}

require_once("includes/layout.php");
