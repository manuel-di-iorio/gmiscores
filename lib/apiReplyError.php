<?php

/**
 * Exit with a JSON API error
 */
function api_reply_error(string $message, string $code, int $status) {
  global $db;

  // Log non-validation errors for API v1 endpoints
  if ($code !== "ValidationError" && isset($db)) {
    $uri = $_SERVER["REQUEST_URI"] ?? "";
    $uriPath = parse_url($uri, PHP_URL_PATH);
    if (strpos($uriPath, "/api/v1/") === 0) {
      $method = $_SERVER["REQUEST_METHOD"] ?? "N/A";
      $ip = $_SERVER["HTTP_CF_CONNECTING_IP"] ?? $_SERVER["REMOTE_ADDR"] ?? "N/A";

      $requestData = null;
      if ($method === "POST") {
        $sanitized = $_POST;
        unset($sanitized["hash"], $sanitized["token"], $sanitized["client_secret"]);
        $requestData = json_encode($sanitized);
      } elseif ($method === "GET") {
        $sanitized = $_GET;
        unset($sanitized["hash"], $sanitized["token"]);
        $requestData = json_encode($sanitized);
      }

      $stmt = $db->prepare("INSERT INTO api_errors (error_code, message, status, endpoint, method, ip, request_data) VALUES (?, ?, ?, ?, ?, ?, ?)");
      if ($stmt) {
        $stmt->bind_param("sssiss", $code, $message, $status, $uriPath, $method, $ip, $requestData);
        $stmt->execute();
        $stmt->close();
      }
    }
  }

  http_response_code($status);
  header('Content-Type: application/json');

  exit(json_encode([
    "message" => $message,
    "code" => $code,
    "status" => $status,
  ]));
}
