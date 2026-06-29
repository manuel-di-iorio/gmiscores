<?php

$path = $_GET['path'] ?? '';
error_log("proxy.php called: path=" . $path . " method=" . $_SERVER["REQUEST_METHOD"]);

if ($path === '' || !preg_match('#^api/(v\d+)/(.+\.php)$/', $path)) {
  http_response_code(404);
  echo json_encode(["debug" => "regex_fail", "path" => $path]);
  exit;
}

$file = __DIR__ . '/' . $path;
error_log("proxy.php file=" . $file . " exists=" . (file_exists($file) ? 'yes' : 'no'));

if (!file_exists($file)) {
  http_response_code(404);
  echo json_encode(["debug" => "file_not_found", "path" => $file]);
  exit;
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include $file;
