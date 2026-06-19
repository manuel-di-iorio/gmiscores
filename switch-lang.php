<?php
require_once("lib/config.php");
require_once("lib/getLang.php");

$go = $_GET["go"] ?? "";
$lang = $_GET["lang"] ?? "";

if (!in_array($lang, $availableLangs) || strlen($go) < 1 || $go[0] !== "/" || (strlen($go) > 1 && $go[1] === "/")) {
  header("Location: /");
  exit;
}

// Sanitize go: strip CRLF characters
$go = str_replace(["\r", "\n"], "", $go);

setcookie("lang", $lang, [
  "expires" => time() + 60 * 60 * 24 * 365,
  "path" => "/",
  "secure" => $config["httpsRedirect"] === "true",
  "httponly" => true,
  "sameSite" => "Lax"
]);

header("Location: " . $go);
exit;
