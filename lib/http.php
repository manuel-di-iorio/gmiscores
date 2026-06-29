<?php

function httpPost($url, $body, $headers = []) {
  $ctx = stream_context_create([
    'http' => [
      'method' => 'POST',
      'header' => "Content-Type: application/x-www-form-urlencoded\r\n" . implode("\r\n", $headers),
      'content' => $body,
      'timeout' => 10,
    ],
  ]);
  return @file_get_contents($url, false, $ctx);
}

function httpGet($url, $bearerToken = null, $headers = []) {
  $httpHeaders = "Content-Type: application/json\r\n";
  if ($bearerToken) {
    $httpHeaders .= "Authorization: Bearer $bearerToken\r\n";
  }
  foreach ($headers as $h) {
    $httpHeaders .= "$h\r\n";
  }
  $ctx = stream_context_create([
    'http' => [
      'method' => 'GET',
      'header' => $httpHeaders,
      'timeout' => 10,
    ],
  ]);
  return @file_get_contents($url, false, $ctx);
}
