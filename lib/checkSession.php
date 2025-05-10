<?php

// Check if the user is logged in
if (!isset($user)) {
  // Store the go path
  $url = urlencode('http' . (isset($_SERVER['HTTPS']) ? 's' : '') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

  // Redirect to the login page
  header("Location: login.php?go=$url");
  exit;
}
