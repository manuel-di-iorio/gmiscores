<?php

$path = $_GET['path'] ?? '';
$path = str_replace('\\', '/', $path);

if ($path === '' || !preg_match('#^api/(v\d+)/(.+\.php)$/', $path)) {
  http_response_code(404);
  echo json_encode(["debug" => "regex_fail", "path" => $path]);
  exit;
}

$file = __DIR__ . '/' . $path;

if (!file_exists($file)) {
  http_response_code(404);
  echo json_encode(["debug" => "file_not_found", "path" => $file]);
  exit;
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include $file;
