<?php
$env = parse_ini_file(__DIR__ . '/../.env');

$config = [
  "appEnv" => $env['APP_ENV'] ?? 'production',
  "dbHost" => $env['DB_HOST'],
  "dbUsername" => $env['DB_USERNAME'],
  "dbPassword" => $env['DB_PASSWORD'],
  "dbDatabase" => $env['DB_DATABASE'],
  "discordLoginClientId" => $env['DISCORD_LOGIN_CLIENT_ID'],
  "discordLoginClientSecret" => $env['DISCORD_LOGIN_CLIENT_SECRET'],
  "host" => $env['HOST'],
  "analytics" => $env['ANALYTICS'] === 'true',
  "analyticsId" => $env['ANALYTICS_ID'],
  "platformTitle" => $env['PLATFORM_TITLE'],
  "platformDescription" => $env['PLATFORM_DESCRIPTION'],
  "logo" => $env['LOGO'],
  "logoWidth" => $env['LOGO_WIDTH'],
  "logoHeight" => $env['LOGO_HEIGHT'],
  "loginCallback" => $env['LOGIN_CALLBACK'],
  "playerLoginCallback" => $env['PLAYER_LOGIN_CALLBACK'] ?? '',
  "cookieKey" => $env['COOKIE_KEY'],
  "cookieDomain" => $env['COOKIE_DOMAIN'],
  "recaptchaKey" => $env['RECAPTCHA_KEY'],
  "recaptchaSecret" => $env['RECAPTCHA_SECRET'],
  "httpsRedirect" => $env['HTTPS_REDIRECT'],
  "maintenance" => $env['MAINTENANCE'] === 'true',
  "maintenanceMessage" => $env['MAINTENANCE_MESSAGE'],
];

function asset_version($path) {
  $full = __DIR__ . '/../' . $path;
  return file_exists($full) ? filemtime($full) : '1';
}
