<?php

// Enforce HTTPS by redirecting if necessary
if ($config["httpsRedirect"] === "true") {
  if (!strstr($_SERVER["HTTP_CF_VISITOR"], 'https')) {
      header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
      exit;
  }
}
