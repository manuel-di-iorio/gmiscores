<?php
require_once("lib/db.php");
session_start();

$view = "login";
$pageName = "Login";
$loginRedirectUrl = "https://discordapp.com/api/oauth2/authorize?client_id=" . $config["discordLoginClientId"] . "&response_type=code&scope=identify&redirect_uri=" . urlencode($config["loginCallback"]);

if (isset($_GET["go"])) { 
  $_SESSION["loginGo"] = $_GET["go"];
} else {
  unset($_SESSION["loginGo"]);
}

require_once("includes/layout.php");
