<?php

$path = $_GET['path'] ?? '';
$method = $_GET['_method'] ?? 'GET';

if ($path === '' || strpos($path, 'api/') !== 0) {
  http_response_code(404);
  exit;
}

$file = __DIR__ . '/' . $path;

if (!file_exists($file)) {
  http_response_code(404);
  exit;
}

$_SERVER['REQUEST_METHOD'] = strtoupper($method);

if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
  $rawBody = file_get_contents('php://input');
  parse_str($rawBody, $_POST);
}

chdir(dirname($file));
include $file;
