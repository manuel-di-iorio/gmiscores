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

header("Content-Type: application/json");
echo json_encode([
  "debug_method" => $_SERVER["REQUEST_METHOD"],
  "debug_orig_method" => $_SERVER["HTTP_X_ORIGINAL_METHOD"] ?? "not_set",
  "debug_get" => $_GET,
]);
exit;
