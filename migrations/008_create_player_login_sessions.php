<?php
return [
  'description' => 'Create player_login_sessions table for SDK Discord login',
  'sql' => [
    "CREATE TABLE player_login_sessions (
      id INT AUTO_INCREMENT PRIMARY KEY,
      session_token VARCHAR(64) NOT NULL UNIQUE,
      user_id INT DEFAULT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      INDEX idx_session_token (session_token)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
  ],
];
