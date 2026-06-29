<?php

// Enforce HTTPS by redirecting if necessary
if ($config["httpsRedirect"] === "true") {
  $isHttps = false;

  if (isset($_SERVER["HTTP_CF_VISITOR"])) {
    // Cloudflare: check CF_VISITOR header
    $isHttps = strstr($_SERVER["HTTP_CF_VISITOR"], 'https') !== false;
  } elseif (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") {
    // Standard Apache/Nginx
    $isHttps = true;
  } elseif (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] == 443) {
    // Fallback: check port
    $isHttps = true;
  }

  if (!$isHttps) {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit;
  }
}
