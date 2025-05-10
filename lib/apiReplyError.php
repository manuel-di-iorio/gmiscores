<?php

/**
 * Exit with a JSON API error
 */
function api_reply_error(string $message, string $code, int $status) {
  http_response_code($status);
  header('Content-Type: application/json');

  exit(json_encode([
    "message" => $message,
    "code" => $code,
    "status" => $status,
  ]));
}
