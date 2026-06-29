<?php

$path = $_GET['path'] ?? '';
if ($path === '' || !preg_match('#^api/#', $path)) {
  http_response_code(404);
  exit;
}

$target = 'https://gmicloud.altervista.org/' . $path;
if ($_SERVER['QUERY_STRING']) {
  $target .= '?' . $_SERVER['QUERY_STRING'];
}

$method = $_SERVER['REQUEST_METHOD'];
$headers = getallheaders();

$ch = curl_init($target);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST  => $method,
  CURLOPT_TIMEOUT        => 10,
  CURLOPT_FOLLOWLOCATION => false,
  CURLOPT_HTTPHEADER     => array_map(fn($k, $v) => "$k: $v", array_keys($headers), array_values($headers)),
]);

if ($method !== 'GET' && $method !== 'HEAD') {
  curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
}

$response = curl_exec($ch);
$status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err      = curl_error($ch);
curl_close($ch);

if ($response === false) {
  http_response_code(502);
  echo json_encode(['message' => 'Proxy error: ' . $err, 'code' => 'PROXY_ERROR', 'status' => 502]);
  exit;
}

http_response_code($status);
echo $response;
