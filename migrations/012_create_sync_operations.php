<?php
return [
  'description' => 'Create sync_operations table for offline sync',
  'sql' => [
    "CREATE TABLE sync_operations (
      id INT AUTO_INCREMENT PRIMARY KEY,
      operation_id VARCHAR(36) NOT NULL,
      game_id INT NOT NULL,
      player_id INT DEFAULT NULL,
      type VARCHAR(64) NOT NULL,
      status ENUM('applied','failed') NOT NULL,
      result TEXT DEFAULT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      UNIQUE KEY uniq_game_op (game_id, operation_id),
      INDEX idx_game_player (game_id, player_id),
      INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
  ],
];
