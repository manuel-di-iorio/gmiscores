<?php
$version = "4"; // Release version (used to refresh assets cache)

// Load the environment configuration
$config = [
  "dbHost" => "localhost",
  "dbUsername" => "root",
  "dbDatabase" => "my_gmiscores",
  "dbPassword" => "",
  "discordLoginClientId" => "<discordLoginClientId>",
  "discordLoginClientSecret" => "<discordLoginClientSecret>",
  "host" => "http://localhost",
  "analytics" => false,
  "analyticsId" => "",
  "platformTitle" => "Classifica online by GameMaker Italia",
  "platformDescription" => "Classifica online per i tuoi giochi hostata su GMI",
  "logo" => "https://gmiscores.altervista.org/assets/images/logoSmallWhite.png",
  "logoWidth" => 179,
  "logoHeight" => 184,
  "loginCallback" => "http://localhost/api/v1/gmi-login.php",
  "cookieKey" => "<cookieKey",
  "cookieDomain" => "",
  "recaptchaKey" => "<recaptchaKey>",
  "recaptchaSecret" => "<recaptchaSecret>",
  "httpsRedirect" => "false"
];
