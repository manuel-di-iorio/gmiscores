<?php

$path = $_GET['path'] ?? '';

echo json_encode([
  "raw" => $path,
  "bytes" => array_map('ord', str_split($path)),
]);

$path = str_replace('\\', '/', $path);

if ($path === '' || !preg_match('#^api/(v\d+)/(.+\.php)$/', $path)) {
  http_response_code(404);
  echo json_encode(["debug" => "regex_fail", "path" => $path, "raw_path" => $_GET['path'] ?? 'empty']);
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
