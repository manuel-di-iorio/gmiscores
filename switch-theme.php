<?php
require_once("lib/config.php");

// Back URL validation
$go = $_GET["go"] ?? "";
if (!isset($_GET["theme"]) || strlen($go) < 1 || $go[0] !== "/" || (strlen($go) > 1 && $go[1] === "/")) {
  header("Location: /");
  exit;
}

// Set the theme cookie
setcookie("theme", $_GET["theme"], [
  "expires" => time()+60*60*24*365, 
  "path" => "/",
  "secure" => $config["httpsRedirect"] === "true",
  "httponly" => true,
  "sameSite" => "Lax"
]);

// Redirect back to the client
header("Location: " . $_GET["go"]);
