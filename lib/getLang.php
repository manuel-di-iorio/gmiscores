<?php

$availableLangs = ['en', 'it', 'es', 'fr', 'de'];
$defaultLang = 'en';

if (isset($_COOKIE["lang"]) && in_array($_COOKIE["lang"], $availableLangs)) {
  $currentLang = $_COOKIE["lang"];
} elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
  $currentLang = $defaultLang;
  $browserLangs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
  foreach ($browserLangs as $browserLang) {
    $code = substr(trim($browserLang), 0, 2);
    if (in_array($code, $availableLangs)) {
      $currentLang = $code;
      break;
    }
  }
} else {
  $currentLang = $defaultLang;
}

$langData = json_decode(file_get_contents(__DIR__ . "/../locales/$currentLang.json"), true);
if (!$langData) {
  $langData = json_decode(file_get_contents(__DIR__ . "/../locales/$defaultLang.json"), true);
}

function __($key, $params = []) {
  global $langData;
  $text = $langData[$key] ?? $key;
  if ($params) {
    foreach ($params as $k => $v) {
      $text = str_replace('{' . $k . '}', $v, $text);
    }
  }
  return $text;
}
