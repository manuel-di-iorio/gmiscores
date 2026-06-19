<?php
require_once("lib/config.php");

// Back URL validation
$go = $_GET["go"] ?? "";
$theme = $_GET["theme"] ?? "";

if (!in_array($theme, ["light", "dark"]) || strlen($go) < 1 || $go[0] !== "/" || (strlen($go) > 1 && $go[1] === "/")) {
  header("Location: /");
  exit;
}

// Sanitize go: strip CRLF characters, reject anything with newlines
$go = str_replace(["\r", "\n"], "", $go);

// Set the theme cookie
setcookie("theme", $theme, [
  "expires" => time()+60*60*24*365, 
  "path" => "/",
  "secure" => $config["httpsRedirect"] === "true",
  "httponly" => true,
  "sameSite" => "Lax"
]);

// Redirect back to the client
header("Location: " . $go);
exit;
