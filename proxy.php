<?php

$path = $_GET['path'] ?? '';

if ($path === '' || strpos($path, 'api/') !== 0) {
  http_response_code(404);
  exit;
}

$file = __DIR__ . '/' . $path;

if (!file_exists($file)) {
  http_response_code(404);
  exit;
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include $file;
