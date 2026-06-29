<?php

$path = $_GET['path'] ?? '';
$originalMethod = $_GET['orig_method'] ?? 'GET';

if ($path === '' || strpos($path, 'api/') !== 0) {
  http_response_code(404);
  exit;
}

$file = __DIR__ . '/' . $path;

if (!file_exists($file)) {
  http_response_code(404);
  exit;
}

$_SERVER['REQUEST_METHOD'] = $originalMethod;

chdir(dirname($file));
include $file;
