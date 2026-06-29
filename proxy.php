<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

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

include $file;
