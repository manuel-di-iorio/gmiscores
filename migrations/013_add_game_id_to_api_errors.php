<?php
return [
  'description' => 'Add game_id column to api_errors table',
  'sql' => [
    "ALTER TABLE api_errors ADD COLUMN game_id INT DEFAULT NULL AFTER ip",
    "CREATE INDEX idx_game_id ON api_errors (game_id)",
  ],
];
