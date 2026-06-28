<?php

/**
 * Check rate limit using a sliding window in MySQL.
 *
 * Limits are per identifier (IP) per endpoint.
 * Cleanup expired entries on each call (no cron needed).
 *
 * @param string $endpoint  Endpoint name (e.g. 'add_score', 'get_scores')
 * @param int    $maxRequests  Max requests allowed in the window
 * @param int    $windowSeconds  Time window in seconds
 *
 * Sends 429 + JSON error if exceeded.
 */
function check_rate_limit(string $endpoint, int $maxRequests = 10, int $windowSeconds = 60): void {
  $table = 'api_rate_limits';
  $identifier = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
  $cutoff = date('Y-m-d H:i:s', time() - $windowSeconds);

  // Record this request first
  exec_query(
    "INSERT INTO $table (identifier, endpoint, requested_at) VALUES (?, ?, NOW())",
    ["ss", $identifier, $endpoint]
  );

  // Count requests in the current window
  $countResult = exec_query(
    "SELECT COUNT(*) AS cnt FROM $table WHERE identifier = ? AND endpoint = ? AND requested_at >= ?",
    ["sss", $identifier, $endpoint, $cutoff]
  );
  $row = $countResult->fetch_assoc();
  $count = $row ? (int)$row['cnt'] : 0;
  
  error_log("[GMI RATE] endpoint=$endpoint count=$count max=$maxRequests id=$identifier");

  // If exceeded, delete the record we just inserted and reject
  if ($count > $maxRequests) {
    exec_query(
      "DELETE FROM $table WHERE identifier = ? AND endpoint = ? ORDER BY id DESC LIMIT 1",
      ["ss", $identifier, $endpoint]
    );
    $retryAfter = $windowSeconds;
    header('X-RateLimit-Limit: ' . $maxRequests);
    header('X-RateLimit-Remaining: 0');
    header('X-RateLimit-Reset: ' . (time() + $retryAfter));
    header('Retry-After: ' . $retryAfter);
    api_reply_error('Rate limit exceeded. Try again in ' . $retryAfter . ' seconds.', 'RateLimitExceeded', 429);
  }

  // Send rate limit headers
  header('X-RateLimit-Limit: ' . $maxRequests);
  header('X-RateLimit-Remaining: ' . ($maxRequests - $count));
  header('X-RateLimit-Reset: ' . (time() + $windowSeconds));
}
