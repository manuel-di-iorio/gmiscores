<?php
return [
  'description' => 'Create api_errors table for API v1 error logging',
  'sql' => [
    "CREATE TABLE api_errors (
      id INT AUTO_INCREMENT PRIMARY KEY,
      error_code VARCHAR(64) NOT NULL,
      message VARCHAR(512) NOT NULL,
      status INT NOT NULL,
      endpoint VARCHAR(255) NOT NULL,
      method VARCHAR(10) NOT NULL,
      ip VARCHAR(45) DEFAULT NULL,
      request_data TEXT DEFAULT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      INDEX idx_error_code (error_code),
      INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
  ],
];
