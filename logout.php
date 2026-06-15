<?php
require_once("lib/config.php");
session_start();
session_destroy();
setcookie("user", "", [
  "expires" => time() - 3600, 
  "domain" => $config["cookieDomain"],
  "path" => "/", 
  "secure" => $config["httpsRedirect"] === "true",
  "httponly" => true,
  "sameSite" => "Lax"
]);
header("Location: /");
