<?php
return [
  'description' => 'Add game_id column to players table',
  'sql' => [
    "ALTER TABLE players ADD COLUMN game_id INT DEFAULT NULL AFTER user_id",
    "ALTER TABLE players ADD INDEX idx_players_game_id (game_id)",
  ],
];
