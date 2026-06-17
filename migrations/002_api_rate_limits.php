<?php
return [
  'description' => 'Create api_rate_limits table for rate limiting API endpoints',
  'sql' => [
    "CREATE TABLE IF NOT EXISTS api_rate_limits (
      id INT AUTO_INCREMENT PRIMARY KEY,
      identifier VARCHAR(255) NOT NULL,
      endpoint VARCHAR(100) NOT NULL,
      requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      INDEX idx_cleanup (endpoint, requested_at),
      INDEX idx_lookup (identifier, endpoint, requested_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
  ],
];
